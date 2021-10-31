<?php

namespace App\Http\Controllers\Storefront;

use DB;
use Auth;
use App\Cart;
use App\Order;
use App\Product;
use App\Customer;
use Paypalpayment;
use CybersourcePayments;
use Instamojo\Instamojo;
use Illuminate\Http\Request;
use App\Events\Order\OrderPaid;
use App\Events\Order\OrderCreated;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\AuthorizeNetException;
use App\Http\Requests\Validations\OrderDetailRequest;
use App\Http\Requests\Validations\CheckoutCartRequest;
use App\Http\Requests\Validations\DownloadPurchasedFileRequest;
use App\Http\Requests\Validations\ConfirmGoodsReceivedRequest;
use App\Notifications\Auth\SendVerificationEmail as EmailVerificationNotification;

use net\authorize\api\contract\v1 as AuthorizeNetAPI;
use net\authorize\api\controller as AuthorizeNetController;

class OrderController extends Controller
{
    /**
     * Checkout the specified cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(CheckoutCartRequest $request, Cart $cart)
    {
        $cart = crosscheckAndUpdateOldCartInfo($request, $cart);

        if ($request->email && $request->has('create-account') && $request->password) {
            $customer = $this->createNewCustomer($request);
            $request->merge(['customer_id' => $customer->id]); //Set customer_id
        }

        // Get shipping address
        if(is_numeric($request->ship_to)) {
            $address = \App\Address::find($request->ship_to)->toHtml('<br/>', False);
        }
        else {
            $address = get_address_str_from_request_data($request);
        }

        // Push shipping address into the request
        $request->merge(['shipping_address' => $address]);

        // Start transaction!
        DB::beginTransaction();
        try {
            // Create the order
            // $order = $this->saveOrderFromCart($request, $cart);
            $order = saveOrderFromCart($request, $cart);

            // Process payment with credit card
            if (
                'saved_card' == $request->payment_method ||
                \App\PaymentMethod::TYPE_CREDIT_CARD == optional($order->paymentMethod)->type ||
                \App\PaymentMethod::TYPE_OTHERS == optional($order->paymentMethod)->type
            ) {
                switch (optional($order->paymentMethod)->code) {
                    case 'stripe':
                        // Charge using Stripe
                        $this->chargeWithStripe($request, $order);
                        break;

                    case 'instamojo':
                        DB::commit();           // Everything is fine. Now commit the transaction Don't change it
                        // Charge using Instamojo
                        $this->chargeWithInstamojo($request, $order, $cart);
                        break;

                    case 'authorize-net':
                        // Charge using authorize.net
                        $this->chargeWithAuthorizeNet($request, $order);
                        break;

                    case 'cybersource':
                        DB::commit();         // Everything is fine. Now commit the transaction Don't change it
                        // Charge using cybersource
                        $this->chargeWithCyberSource($request, $order);
                        break;

                    case 'paystack':
                        DB::commit();           // Everything is fine. Now commit the transaction Don't change it
                        // Charge using paystack
                        $this->chargeWithPaystack($request, $order, $cart);
                        break;
                }

                // Order has been paided
                $this->markOrderAsPaid($order);
            }
        } catch(\Exception $e){
            \Log::error($e);        // Log the error

            DB::rollback();         // rollback the transaction and log the error

            // Set error messages:
            if (
                $e instanceOf \Yabacon\Paystack\Exception\ApiException ||
                $e instanceOf \Incevio\Cybersource\CybersourceSDK\ApiException ||
                $e instanceOf AuthorizeNetException
            ) {
                \Log::error('Payment failed:: ');
                \Log::info($e->getMessage());

                if($e instanceOf \Stripe\Error\Base) {
                    \Log::info('ResponseBody:: ' . $e->getJsonBody());
                }
                // elseif($e instanceOf AuthorizeNetException) {
                //     \Log::info('ResponseBody:: ' . $e->message);
                // }
                // else {
                //     \Log::info('ResponseBody:: ' . json_encode($e->getResponseBody()));
                // }

                $error = trans('theme.notify.invalid_request');
            }
            else {
                $error = trans('theme.notify.order_creation_failed');
            }

            return redirect()->back()->with('error', $error)->withInput();
        }

        DB::commit();           // Everything is fine. Now commit the transaction

        $cart->forceDelete();   // Delete the cart

        // Process payment with PayPal
        if ('paypal-express' == optional($order->paymentMethod)->code) {
            try {
                $payment = $this->chargeWithPayPal($request, $order);
            } catch (\Exception $e) {
                \Log::info('PayPal ERROR:');
                \Log::error($e);

                return redirect()->route("payment.failed", $order->id)->withInput();
            }

            return redirect()->to($payment->getData()->approval_url);
        }


        event(new OrderCreated($order));   // Trigger the Event

        return redirect()->route('order.success', $order)->with('success', trans('theme.notify.order_placed'));
    }


    private function chargeWithCyberSource($request, Order $order)
    {
        // Get the configs
        $config = config('services.cybersource');

        // If the cybersource is not cofigured
        if( ! ($config['merchant'] && $config['key'] && $config['secret']) ) {
            return redirect()->back()->with('error', trans('theme.notify.payment_method_config_error'))->withInput();
        }

        // Set vendor's cybersource config
        config()->set('cybersource_config.auth', $config['auth']);
        config()->set('cybersource_config.mode', 'cyberSource.environment.' . $config['sandbox']);
        config()->set('cybersource_config.merchantID', $config['merchant']);
        config()->set('cybersource_config.apiKeyID', $config['key']);
        config()->set('cybersource_config.secretKey', $config['secret']);

        // Get customer
        $customer = Auth::guard('customer')->check() ? Auth::guard('customer')->user() : Null;

        $address = Null;
        $order_email = $request->email ?? $order->email;

        if ($customer) {
            $address = $customer->billingAddress ?? $customer->address();
            $order_email = $customer->email;
        }

        $country_id = $address ? $address->country_id : $request->country_id;
        $state_id = $address && $address->state ? $address->state_id : $request->state_id;

        $name = explode(' ', $request->cardholder_name);
        $fname = $name[0];
        $lname = count($name) > 1 ? end($name) : $fname;

        $billtoArr = [
            "firstName"          => $fname,
            "lastName"           => $lname,
            "address1"           => $address ? $address->address_line_1 : $request->address_line_1,
            "address2"           => $address ? $address->address_line_2 : $request->address_line_2,
            "postalCode"         => $address ? $address->zip_code : $request->zip_code,
            "locality"           => $address ? $address->city : $request->city,
            "country"            => get_value_from($country_id, 'countries', 'iso_code'),
            "administrativeArea" => $state_id ? get_value_from($state_id, 'states', 'iso_code') : '',
            "phoneNumber"        => $address ? $address->phone : $request->phone,
            "email"              => $order_email,
        ];

        $amountDetailsArr = [
            "totalAmount" => get_formated_decimal($order->grand_total, false, 2),
            "currency"    => get_currency_code()
        ];

        $paymentCardInfo = [
            "number"          => $request->cnumber,
            "securityCode"    => $request->ccode,
            "expirationMonth" => $request->card_expiry_month,
            "expirationYear"  => $request->card_expiry_year,
        ];

        $cliRefInfoArr = [
            "code" => get_platform_title() . " " . trans('app.order') . " " . $order->order_number,
        ];

        try {
            $response = CybersourcePayments::processPayment($cliRefInfoArr, $amountDetailsArr, $billtoArr, $paymentCardInfo, false);

            if($response[0]['status'] == 'AUTHORIZED') {
                return $response[0]['id'];
            }

            throw new \Incevio\Cybersource\CybersourceSDK\ApiException($response[0]['errorInformation']);
        }
        catch(Cybersource\ApiException $e)
        {
            \Log::error('ResponseBody:: ' . json_encode($e->getResponseBody()));

            throw new \Incevio\Cybersource\CybersourceSDK\ApiException($e->getMessage());
        }
    }

    /**
     * Charge using Stripe
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Order  $order
     *
     * @return [type]
     */
    private function chargeWithStripe($request, Order $order)
    {
        // If the stripe is not cofigured
        if(! (config('services.stripe.key') && config('services.stripe.secret'))) {
            return redirect()->back()->with('success', trans('theme.notify.payment_method_config_error'))->withInput();
        }

        // Get customer
        if(Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        if ('saved_card' == $request->payment_method) {  // Charge old card
            // Create stripe token
            $token = \Stripe\Token::create([
              "customer" => $customer->stripe_id,
            ]);

            $stripeToken = $token->id;
        }
        else if ($request->has('cc_token')){    // This is a new card with stripe token

            if ($request->has('remember_the_card')) {  // Create Stripe Customer for future use
                $stripeCustomer = \Stripe\Customer::create([
                    'name' => $request->cardholder_name ?? $customer->name,
                    'email' => $customer->email,
                    'source' => $request->cc_token,
                    'address' => $address->toStripeAddress(),
                ]);

                // Save cart info for future use
                $customer->stripe_id = $stripeCustomer->id;
                if (count($stripeCustomer->sources->data) > 0) {
                    $customer->card_brand = $stripeCustomer->sources->data[0]->brand;
                    $customer->card_holder_name = $stripeCustomer->sources->data[0]->name;
                    $customer->card_last_four = $stripeCustomer->sources->data[0]->last4;
                }
                $customer->save();

                // Create stripe token
                $token = \Stripe\Token::create([
                  "customer" => $customer->stripe_id,
                ]);

                $stripeToken = $token->id;
            }
            else {      // Just charge the new card (Don't save)
                $stripeToken = $request->cc_token;
            }
        }

        // Get calculated application fee for the order
        // $application_fee = getPlatformFeeForOrder($order);

        return \Stripe\Charge::create([
            'amount' => get_cent_from_doller($order->grand_total),
            'currency' => get_currency_code(),
            'description' => trans('app.purchase_from', ['marketplace' => get_platform_title()]),
            'source' => $stripeToken,
            'metadata' => [
                'order_number' => $order->order_number,
                'shipping_address' => strip_tags($order->shipping_address),
                'buyer_note' => $order->buyer_note
            ],
        ]);
    }

    /**
     * [chargeWithInstamojo description]
     *
     * @param  [type] $request [description]
     * @param  Order  $order   [description]
     * @param  Cart   $cart    [description]
     *
     * @return [type]          [description]
     */
    private function chargeWithInstamojo($request, Order $order, Cart $cart)
    {
        // If the stripe is not cofigured
        if(! (config('services.instamojo.key') && config('services.instamojo.token'))) {
            return redirect()->back()->with('success', trans('theme.notify.payment_method_config_error'))->withInput();
        }

        $instamojoApi = new Instamojo(
                                    config('services.instamojo.key'),
                                    config('services.instamojo.token'),
                                    config('services.instamojo.sandbox', true) ? 'https://test.instamojo.com/api/1.1/' : Null
                                );

        try {
            $response = $instamojoApi->paymentRequestCreate([
                            "purpose" => trans('theme.order_id') . ': ' . $order->order_number,
                            "amount" => number_format($order->grand_total, 2),
                            "buyer_name" => Auth::guard('customer')->check() ?
                                            Auth::guard('customer')->user()->getName() : $request->address_title,
                            "send_email" => true,
                            "email" =>  Auth::guard('customer')->check() ?
                                        Auth::guard('customer')->user()->email : $request->email,
                            "phone" => Auth::guard('customer')->check() ? '' : $request->phone,
                            "redirect_url" => route('instamojo.redirect', ['order' => $order, 'cart' => $cart])
                        ]);

            // $response = $instamojoApi->paymentRequestStatus($response['id']);
            // print_r($response);
        }
        catch (Exception $e) {
            return $e->getMessage();
        }

        // redirect to page so User can pay
        header('Location: ' . $response['longurl']);
        exit();
    }

    /**
     * [instamojoRedirect description]
     *
     * @param  Request $request [description]
     * @param  [type]  $order   [description]
     * @param  [type]  $cart    [description]
     *
     * @return [type]           [description]
     */
    public function instamojoSuccess(Request $request, $order, $cart)
    {
        if ( $request->payment_status != 'Credit' || ! $request->has('payment_request_id') ||  ! $request->has('payment_id') ) {
            return redirect()->route("payment.failed", $order);
        }

        if( !$order instanceOf Order ) {
            $order = Order::find($order);
        }

        // Delete the cart
        Cart::find($cart)->forceDelete();   // Delete the cart

        // Order has been paided
        $this->markOrderAsPaid($order);


        event(new OrderCreated($order));   // Trigger the Event

        return redirect()->route('order.success', $order)->with('success', trans('theme.notify.order_placed'));
    }

    /**
     * [chargeWithAuthorizeNet description]
     *
     * @param  [type] $request [description]
     * @param  Order  $order   [description]
     *
     * @return [type]          [description]
     */
    private function chargeWithAuthorizeNet($request, Order $order)
    {
        if( ! (config('services.authorizenet.id') && config('services.authorizenet.key')) )
            return redirect()->back()->with('success', trans('theme.notify.payment_method_config_error'))->withInput();

        // Common setup for API credentials
        $merchantAuthentication = new AuthorizeNetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(config('services.authorizenet.id'));
        $merchantAuthentication->setTransactionKey(config('services.authorizenet.key'));
        $refId = 'ref'.time();

        // Create the payment data for a credit card
        $creditCard = new AuthorizeNetAPI\CreditCardType();
        $creditCard->setCardNumber($request->cnumber);
        // $creditCard->setExpirationDate( "2038-12");
        $expiry = $request->card_expiry_year . '-' . $request->card_expiry_month;
        $creditCard->setExpirationDate($expiry);
        $paymentOne = new AuthorizeNetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        // Create a transaction
        $transactionRequestType = new AuthorizeNetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount(get_formated_decimal($order->grand_total));
        $transactionRequestType->setPayment($paymentOne);
        $ApiRequest = new AuthorizeNetAPI\CreateTransactionRequest();
        $ApiRequest->setMerchantAuthentication($merchantAuthentication);
        $ApiRequest->setRefId($refId);
        $ApiRequest->setTransactionRequest($transactionRequestType);
        $controller = new AuthorizeNetController\CreateTransactionController($ApiRequest);
        $response = $controller->executeWithApiResponse(
            config('services.authorizenet.sandbox', true) ?
            \net\authorize\api\constants\ANetEnvironment::SANDBOX :
            \net\authorize\api\constants\ANetEnvironment::PRODUCTION
        );

        if ($response != null) {
            $tresponse = $response->getTransactionResponse();
            if (($tresponse != null) && ($tresponse->getResponseCode() == "1")) { // Approved
                \Log::info("Charge Credit Card AUTH CODE : " . $tresponse->getAuthCode() . "\n");
                \Log::info("Charge Credit Card TRANS ID  : " . $tresponse->getTransId() . "\n");

                return TRUE;
            }
            else {
                $errMsg = $tresponse == null ? trans('theme.notify.invalid_request') : $tresponse->getErrors()[0]->getErrorText();
                throw new AuthorizeNetException($errMsg);

                return FALSE;
            }
        }

        \Log::error("AuthorizeNetException:: Charge Credit Card Null response returned");

        throw new AuthorizeNetException(trans('theme.notify.payment_failed'));

        return FALSE;
    }

    /**
     * [chargeWithPaystack description]
     *
     * @param  [type] $request [description]
     * @param  Order  $order   [description]
     * @param  Cart   $cart    [description]
     *
     * @return [type]          [description]
     */
    private function chargeWithPaystack($request, Order $order, Cart $cart)
    {
        // Get the vendor configs
        $paystackSecret = config('services.paystack.secret');

        // If the stripe is not cofigured
        if( ! $paystackSecret ) {
            return redirect()->back()->with('success', trans('theme.notify.payment_method_config_error'))->withInput();
        }

        $paystack = new \Yabacon\Paystack($paystackSecret);
        $tranx = $paystack->transaction->initialize([
            'email' => $request->email,
            'amount' => (int) ($order->grand_total * 100),
            'quantity' => $order->quantity,
            'orderID' => $order->id,
            'callback_url' => route('paystack.success', ['order' => $order, 'cart' => $cart]),
            // 'reference' => $order->order_number,
            'metadata'=>json_encode([
                'order_number' => $order->order_number,
                'custom_fields'=> [
                    [
                        'display_name'=> "Order Number",
                        'variable_name'=> "order_number",
                        'value'=> $order->order_number
                    ],[
                        'display_name'=> "Shipping Address",
                        'variable_name'=> "shipping_address",
                        'value'=> $order->order_number
                    ]
                ]
            ])
        ]);

        if(!$tranx->status)
            throw new \Yabacon\Paystack\Exception\ApiException;

        // store transaction reference so we can query in case user never comes back
        // perhaps due to network issue
        // save_last_transaction_reference($tranx->data->reference);

        // redirect to page so User can pay
        header('Location: ' . $tranx->data->authorization_url);
        exit();
    }

    /**
     * [paystackPaymentSuccess description]
     *
     * @param  Request $request [description]
     * @param  [type]  $order   [description]
     * @param  [type]  $cart    [description]
     *
     * @return [type]           [description]
     */
    public function paystackPaymentSuccess(Request $request, $order, $cart)
    {
        if ( ! $request->has('trxref') ||  ! $request->has('reference') )
            return redirect()->route("payment.failed", $order);

        if( !$order instanceOf Order )
            $order = Order::find($order);

        // Delete the cart
        Cart::find($cart)->forceDelete();   // Delete the cart

        // Order has been paided
        $this->markOrderAsPaid($order);


        event(new OrderCreated($order));   // Trigger the Event

        return redirect()->route('order.success', $order)->with('success', trans('theme.notify.order_placed'));
    }

    /**
     * Charge using Stripe
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Order  $order
     *
     * @return [type]
     */
    private function chargeWithPayPal($request, Order $order)
    {
        if( ! (config('paypal_payment.account.client_id') && config('paypal_payment.account.client_secret')) ) {
            return redirect()->back()->with('success', trans('theme.notify.payment_method_config_error'))->withInput();
        }

        // ### Payer
        $payer = Paypalpayment::payer();
        $payer->setPaymentMethod("paypal");

        $allItems = [];
        foreach ($order->products as $item) {
            $tempItem = Paypalpayment::item();
            $tempItem->setName($item->title)->setDescription($item->pivot->item_description)
            ->setCurrency( get_currency_code() )->setQuantity($item->pivot->quantity)
            ->setTax($order->taxrate)->setPrice($item->pivot->unit_price);

            $allItems[] = $tempItem;
        }

        $itemList = Paypalpayment::itemList();
        $itemList->setItems($allItems);
        // ->setShippingAddress($shippingAddress);

        $details = Paypalpayment::details();
        $details->setShipping( $order->get_shipping_cost() )->setTax($order->taxes)
        ->setGiftWrap($order->packaging)->setShippingDiscount($order->discount)
        ->setSubtotal($order->calculate_total_for_paypal()); //total of items prices

        //Payment Amount
        $amount = Paypalpayment::amount();
        $amount->setCurrency( get_currency_code() )
        ->setTotal( $order->grand_total_for_paypal() )
        ->setDetails($details);

        // ### Transaction
        // A transaction defines the contract of a payment - what is the payment for and who
        // is fulfilling it. Transaction is created with a `Payee` and `Amount` types
        $transaction = Paypalpayment::transaction();
        $transaction->setAmount($amount)
        ->setItemList($itemList)
        ->setDescription( trans('app.purchase_from', ['marketplace' => get_platform_title()]) )
        ->setInvoiceNumber($order->order_number);

        // ### Payment
        // A Payment Resource; create one using the above types and intent as 'sale'
        $redirectUrls = Paypalpayment::redirectUrls();
        $redirectUrls->setReturnUrl(route("payment.success", $order->id))
        ->setCancelUrl(route("payment.failed", $order->id));

        $payment = Paypalpayment::payment();

        $payment->setIntent("sale")->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions([$transaction]);

        try {
            // ### Create Payment
            // Create a payment by posting to the APIService using a valid ApiContext The return object contains the status;
            $payment->create(Paypalpayment::apiContext());
        } catch (\PPConnectionException $ex) {
            return response()->json(["error" => $ex->getMessage()], 400);
        }

        return response()->json([$payment->toArray(), 'approval_url' => $payment->getApprovalLink()], 200);
    }

    /**
     * Payment done successfully. Sync inventory and trigger event
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Order  $order
     *
     * @return \Illuminate\Http\Response
     */
    public function paymentSuccess(Request $request, $order)
    {
        if ( ! $request->has('token') ||  ! $request->has('paymentId') || ! $request->has('PayerID') ) {
            return redirect()->route("payment.failed", $order);
        }

        if( !$order instanceOf Order ) {
            $order = Order::find($order);
        }

        // Order has been paided
        $this->markOrderAsPaid($order);

        event(new OrderCreated($order));   // Trigger the Event

        return redirect()->route('order.success', $order)->with('success', trans('theme.notify.order_placed'));
    }

    /**
     * Payment failed. revert the order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Order  $order
     *
     * @return \Illuminate\Http\Response
     */
    public function paymentFailed(Request $request, $order)
    {
        // $cart = $this->revertOrder($order);
        $cart = revertOrderAndMoveToCart($order);

        return redirect()->route('cart.checkout', $cart)->with('error', trans('theme.notify.payment_failed'))->withInput();
    }

    /**
     * Order placed successfully.
     *
     * @param  App\Order   $order
     *
     * @return \Illuminate\Http\Response
     */
    public function orderPlaced($order)
    {
        if( !$order instanceOf Order ) {
            $order = Order::find($order);
        }

        return view('order_complete', compact('order'));
    }

    /**
     * Display order detail page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Order   $order
     *
     * @return \Illuminate\Http\Response
     */
    public function detail(OrderDetailRequest $request, Order $order)
    {
        $order->load(['products.image','conversation.replies.attachments']);

        $is_downloadable = $order->is_downloadable();

        return view('order_detail', compact('order', 'is_downloadable'));
    }

    /**
     * Buyer confirmed goods received
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Order   $order
     *
     * @return \Illuminate\Http\Response
     */
    public function goods_received(ConfirmGoodsReceivedRequest $request, Order $order)
    {
        $order->goods_received();

        return redirect()->route('order.feedback', $order)->with('success', trans('theme.notify.order_updated'));
    }

    /**
     * Track order shippping.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Order   $order
     *
     * @return \Illuminate\Http\Response
     */
    public function track(Request $request, Order $order)
    {
        return view('order_tracking', compact('order'));
    }

    /**
     * Create a new Customer
     *
     * @param  Request $request
     *
     * @return App\Customer
     */
    private function createNewCustomer($request)
    {
        $customer = Customer::create([
            'name' => $request->address_title,
            'email' => $request->email,
            'password' => $request->password,
            'accepts_marketing' => $request->subscribe,
            'verification_token' => str_random(40),
            'active' => 1,
        ]);

        // Sent email address verification notich to customer
        $customer->notify(new EmailVerificationNotification($customer));

        $customer->addresses()->create($request->all()); //Save address

        if ( Auth::guard('web')->check() ) {
            Auth::logout();
        }

        Auth::guard('customer')->login($customer); //Login the customer

        return $customer;
    }

    /**
     * MarkOrderAsPaid
     */
    private function markOrderAsPaid($order)
    {
        if( !$order instanceOf Order )
            $order = Order::find($order);

        $order->payment_status = Order::PAYMENT_STATUS_PAID;

        $order->save();

        event(new OrderPaid($order));

        return $order;
    }

    /**
     * Download digital files.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Order  $order
     * @param  Product  $product
     * @return \Illuminate\Http\Response
     */
    public function download(DownloadPurchasedFileRequest $request, Order $order, Product $product)
    {
        if (Storage::exists($product->attachment->path)) {
            return Storage::download($product->attachment->path, $product->attachment->name);
        }

        return back()->with('error', trans('messages.file_not_exist'));
    }

    private function logErrors($error, $feedback)
    {
        \Log::error($error);

        // Set error messages:
        // $error = new \Illuminate\Support\MessageBag();
        // $error->add('errors', $feedback);

        return $error;
    }

}
