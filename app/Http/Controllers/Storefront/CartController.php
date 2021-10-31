<?php

namespace App\Http\Controllers\Storefront;

use Auth;
use Carbon\Carbon;
use App\Cart;
use App\Order;
use App\Coupon;
use App\Product;
use App\Packaging;
use App\ShippingRate;
use App\PaymentMethod;
use App\ProductVariant;
use App\Helpers\ListHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\DirectCheckoutRequest;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $expressId = Null)
    {
        $carts = Cart::whereNull('customer_id')->where('ip_address', $request->ip());

        if( Auth::guard('customer')->check() ) {
            $carts = $carts->orWhere('customer_id', Auth::guard('customer')->user()->id);
        }

        $carts = $carts->get();

        // Load related models
        $carts->load(['products.image', 'shippingPackage']);

        $countries = ListHelper::countries(); // Country list for shop_to dropdown

        $packagings = getPackagings(); // Get packaging list

        return view('cart', compact('carts','countries','packagings','expressId'));
    }

    /**
     * Validate coupon.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addToCart(Request $request, $slug)
    {
        $item = Product::where('slug', $slug)->firstOrFail();

        if(! $item) {
            return response()->json(trans('theme.item_not_available'), 404);
        }

        $customer_id = Auth::guard('customer')->check() ? Auth::guard('customer')->user()->id : Null;

        if($customer_id){
            $old_cart = Cart::where(function($query) use ($customer_id){
                $query->where('customer_id', $customer_id)->orWhere(function($q){
                    $q->whereNull('customer_id')->where('ip_address', request()->ip());
                });
            })->first();
        }
        else{
            $old_cart = Cart::whereNull('customer_id')->where('ip_address', $request->ip())->first();
        }

        $variantId = $request->variantId;

        // check it variant is selected when item has variants
        if(! $variantId && $item->hasVariants()) {
            return response()->json(['href' => route('show.product', $item->slug)], 307);
        }

        if( $variantId ) {
            $variant = ProductVariant::find($variantId);
            $variantId = $variant ? $variantId : Null;
        }

        // Check if the item is alrealy in the cart
        if($old_cart) {
            $item_in_cart = DB::table('cart_items')->where('cart_id', $old_cart->id)
                            ->where('product_id', $item->id)
                            ->where('variant_id', $variantId)->first();

            if( $item_in_cart ) { // Item alrealy in cart
                return response()->json(['cart_id' => $item_in_cart->cart_id], 409);
            }
        }

        $qtt = $request->quantity ?? $item->min_order_quantity;
        // $shipping_rate_id = $old_cart ? $old_cart->shipping_rate_id : $request->shippingRateId;
        $unit_price = $variantId ? $variant->current_price() : $item->currnt_price();
        $shipping_weight = $variantId && $variant->shipping_weight ? $variant->shipping_weight : $item->shipping_weight;

        // Instantiate new cart if old cart not found for the customer
        $cart = $old_cart ?? new Cart;
        $cart->customer_id = $customer_id;
        $cart->ip_address = $request->ip();
        $cart->item_count = $old_cart ? ($old_cart->item_count + 1) : 1;
        $cart->quantity = $old_cart ? ($old_cart->quantity + $qtt) : $qtt;

        if($request->shipTo) {
            $cart->ship_to = $request->shipTo;
        }

        //Reset if the old cart exist, bcoz shipping rate will change after adding new item
        // $cart->shipping_zone_id = $old_cart ? Null : $request->shippingZoneId;
        // $cart->shipping_rate_id = $old_cart ? Null : $request->shippingRateId == 'Null' ? Null : $request->shippingRateId;
        $cart->shipping_zone_id = $request->shippingZoneId;
        $cart->shipping_rate_id = $request->shippingRateId == 'Null' ? Null : $request->shippingRateId;

        $cart->handling = config('system_settings.order_handling_cost');
        $cart->total = $old_cart ? ($old_cart->total + ($qtt * $unit_price)) : $unit_price;
        // $cart->packaging_id = $old_cart ? $old_cart->packaging_id : 1;

        // All items need to have shipping_weight to calculate shipping
        // If any one the item missing shipping_weight set null to cart shipping_weight
        if( $shipping_weight == Null || ($old_cart && $old_cart->shipping_weight == Null) ) {
            $cart->shipping_weight = Null;
        }
        else {
            $cart->shipping_weight = $old_cart ? ($old_cart->shipping_weight + $shipping_weight) : $shipping_weight;
        }

        // Taxes
        if ($cart->shippingZone) {
            $taxrate = $cart->shippingZone->tax_id ? getTaxRate($cart->shippingZone->tax_id) : Null;
            $taxes = $taxrate ? ($cart->total * $taxrate)/100 : Null;

            $cart->taxrate = $taxrate;
            $cart->taxes = $taxrate;
        }

        $cart->save();

        $description = $item->title . ' - ' . $item->condition;

        if( $variantId ) {
            $title = $variant->title ?? $item->title;
            $condition = $variant->condition ?? $item->condition;
            $attributes = implode(' | ', $variant->attributeValues->pluck('value')->toArray());
            $description = $title . ' - ' . $attributes . ' - ' . $condition;
        }

        // Prepare and Save the item into pivot table
        $pivot_data = [
            'cart_id' => $cart->id,
            'product_id' => $item->id,
            'variant_id' => $variantId,
            'item_description'=> $description,
            'quantity' => $qtt,
            'unit_price' => $unit_price,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];

        DB::table('cart_items')->insert($pivot_data);

        return response()->json($cart->toArray(), 200);
    }

    /**
     * Update the cart and redirected to checkout page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cart    $cart
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cart $cart)
    {
        if(! crosscheckCartOwnership($request, $cart)) {
            return redirect()->route('cart.index')
            ->with('warning', trans('theme.notify.please_login_to_checkout'));
        }

        $cart = crosscheckAndUpdateOldCartInfo($request, $cart);

        return redirect()->route('cart.checkout', $cart);
    }

    /**
     * Checkout the specified cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkout(Request $request, Cart $cart)
    {
        if( !crosscheckCartOwnership($request, $cart) ) {
            return redirect()->route('cart.index')
            ->with('warning', trans('theme.notify.please_login_to_checkout'));
        }

        $cart = crosscheckAndUpdateOldCartInfo($request, $cart);

        $customer = Auth::guard('customer')->check() ? Auth::guard('customer')->user() : Null;
        $countries = ListHelper::countries(); // Country list for shop_to dropdown
        $payment_methods = PaymentMethod::enabled()->get();
        $is_downloadable = $cart->is_downloadable();

        return view('checkout', compact('cart', 'is_downloadable', 'customer', 'countries', 'payment_methods'));
    }

    /**
     * Direct checkout with the item/cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  str $slug
     *
     * @return \Illuminate\Http\Response
     */
    public function directCheckout(DirectCheckoutRequest $request, $slug)
    {
        $cart = $this->addToCart($request, $slug);

        if ( 200 == $cart->status() ) {
            return redirect()->route('cart.index', $cart->getdata()->id);
        }
        elseif ($cart->getdata()->cart_id) {
            return redirect()->route('cart.index', $cart->getdata()->cart_id);
        }

        return redirect()->back()->with('warning', trans('theme.notify.failed'));
    }

    /**
     * validate coupon.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function remove(Request $request)
    {
        $cart = Cart::findOrFail($request->cart);

        $result = DB::table('cart_items')->where([
            ['cart_id', $request->cart],
            ['id', $request->item],
        ])->delete();

        if($result) {
            if( ! $cart->products()->count() ) {
                $cart->forceDelete();
            }

            return response('Item removed', 200);
        }

        return response('Item remove failed!', 404);
    }

    /**
     * validate coupon.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function validateCoupon(Request $request)
    {
        // $request->all();
        $coupon = Coupon::active()->where('code', $request->coupon)
        ->withCount(['orders','customerOrders'])->first();

        if( ! $coupon ) {
            return response('Coupon not found', 404);
        }

        if( ! $coupon->isLive() || ! $coupon->isValidCustomer() ) {
            return response('Coupon not valid', 403);
        }

        if( ! $coupon->isValidZone($request->zone) ) {
            return response('Coupon not valid for shipping area', 443);
        }

        if( ! $coupon->hasQtt() ) {
            return response('Coupon qtt limit exit', 444);
        }

        return response()->json($coupon->toArray());
    }
}
