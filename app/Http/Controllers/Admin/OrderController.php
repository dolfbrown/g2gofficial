<?php

namespace App\Http\Controllers\Admin;

use App\Cart;
use App\Order;
use App\Carrier;
use App\Product;
use App\Customer;
use App\Packaging;
use App\Common\Authorizable;
use Illuminate\Http\Request;
use App\Events\Order\OrderPaid;
use App\Events\Order\OrderCreated;
use App\Events\Order\OrderUpdated;
use App\Events\Order\OrderFulfilled;
use App\Http\Controllers\Controller;
// use App\Events\Order\OrderPaymentFailed;
use App\Http\Requests\Validations\CreateOrderRequest;
use App\Http\Requests\Validations\FulfillOrderRequest;
use App\Http\Requests\Validations\CustomerSearchRequest;

class OrderController extends Controller
{
    use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->model_name = trans('app.model.order');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::with('customer', 'status')->get();

        $archives = Order::archived()->get();

        return view('admin.order.index', compact('orders', 'archives'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchCutomer()
    {
        return view('admin.order._search_customer');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data['customer'] = Customer::findOrFail($request->input('customer_id'));

        $data['cart_lists'] = Cart::where('customer_id', $request->input('customer_id'))
        ->where('deleted_at', Null)->with('products', 'customer')
        ->orderBy('created_at', 'desc')->get();

        if ($request->input('cart_id')) {
            $data['cart'] = Cart::find($request->input('cart_id'));
        }

        return view('admin.order.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateOrderRequest $request)
    {
        setAdditionalCartInfo($request); //Set some system information using helper function

        $order = Order::create($request->all());

        $this->syncInventory($order, $request->input('cart'));

        // DELETE THE SAVED CART AFTER THE ORDER
        if ($request->input('delete_the_cart')) {
            Cart::find($request->input('cart_id'))->forceDelete();
        }

        event(new OrderCreated($order));

        if ($order) {
            return redirect()->route('admin.order.order.index')->with('success', trans('messages.created', ['model' => $this->model_name]));
        }

        return redirect()->route('admin.order.order.index')->with('error', trans('messages.failed'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $order
     * @return \Illuminate\Http\Response
     */
    public function show($order)
    {
        $order = Order::withTrashed()->find($order);

        $this->authorize('view', $order); // Check permission

        $address = $order->customer->primaryAddress();

        return view('admin.order.show', compact('order', 'address'));
    }

    /**
     * Show the fulfillment form for the specified order.
     *
     * @param  Order $order
     * @return \Illuminate\Http\Response
     */
    public function fulfillment(Order $order)
    {
        $this->authorize('fulfill', $order); // Check permission

        return view('admin.order._fulfill', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Order $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        $this->authorize('fulfill', $order); // Check permission

        return view('admin.order._edit', compact('order'));
    }

    /**
     * Fulfill the order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Order $order
     * @return \Illuminate\Http\Response
     */
    public function fulfill(FulfillOrderRequest $request, Order $order)
    {
        $this->authorize('fulfill', $order); // Check permission

        if (! $order->update($request->all())) {
            return back()->with('error', trans('messages.failed'));
        }

        event(new OrderFulfilled($order, $request->filled('notify_customer')));

        // Need to check
        if (config('system_settings.auto_archive_order') && $order->isPaid()) {
            $order->delete();

            return redirect()->route('admin.order.order.index')->with('success', trans('messages.fulfilled_and_archived'));
        }

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * updateOrderStatus the order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Order $order
     * @return \Illuminate\Http\Response
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $this->authorize('fulfill', $order); // Check permission

        $order->order_status_id = $request->input('order_status_id');

        if (! $order->save()) {
            return back()->with('error', trans('messages.failed'));
        }

        event(new OrderUpdated($order, $request->filled('notify_customer')));

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Order  $order
     * @return \Illuminate\Http\Response
     */
    public function archive(Request $request, Order $order)
    {
        if ($order->delete()) {
            return redirect()->route('admin.order.order.index')->with('success', trans('messages.archived', ['model' => $this->model_name]));
        }

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Restore the specified resource from soft delete.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, $id)
    {
        $order = Order::onlyTrashed()->findOrFail($id);

        if ($order->restore()) {
            return back()->with('success', trans('messages.restored', ['model' => $this->model_name]));
        }

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Toggle Payment Status of the given order, Its uses the ajax middleware
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Order  $Order
     * @return \Illuminate\Http\Response
     */
    public function togglePaymentStatus(Request $request, Order $order)
    {
        $this->authorize('fulfill', $order); // Check permission

        $order->payment_status = ($order->payment_status == Order::PAYMENT_STATUS_PAID) ?
                                Order::PAYMENT_STATUS_UNPAID :
                                Order::PAYMENT_STATUS_PAID;

        if ( ! $order->save() ) {
            return back()->with('error', trans('messages.failed'));
        }

        if ($order->payment_status == Order::PAYMENT_STATUS_PAID) {
            event(new OrderPaid($order));
        }
        else {
            event(new OrderUpdated($order));
        }

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Sync up the inventory
     * @param  Order $order
     * @param  array $items
     * @return void
     */
    public function syncInventory(Order $order, array $items)
    {
        // echo "<pre>"; print_r($items); echo "</pre>"; exit();
        // Increase stock if any item removed from the order
        if ($order->products->count() > 0) {
            $newItems = array_column($items, 'id');

            foreach ($order->products as $product) {
                if ( ! in_array($product->id, $newItems) ) {
                    Product::find($product->id)->increment('stock_quantity', $product->pivot->quantity);
                }
            }
        }

        $temp = [];

        foreach ($items as $item) {
            $item = (object) $item;
            $id = $item->product_id;

            // Preparing data for the pivot table
            $temp[$id] = [
                'item_description' => $item->item_description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ];

            // adjust stock qtt based on tth order
            if ($order->products->contains($id)) {
                $old = $order->products()->where('id', $id)->first();
                $old_qtt = $old->pivot->quantity;

                if ($old_qtt > $item->quantity) {
                    Product::find($id)->increment('stock_quantity', $old_qtt - $item->quantity);
                }
                else if ($old_qtt < $item->quantity) {
                    Product::find($id)->decrement('stock_quantity', $item->quantity - $old_qtt);
                }
            }
            else {
                Product::find($id)->decrement('stock_quantity', $item->quantity);
            }
        }

        // Sync the pivot table
        if (! empty($temp)) {
            $order->products()->sync($temp);
        }

        return;
    }
}