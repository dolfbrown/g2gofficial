<?php
namespace App\Http\Controllers\Admin;

use Auth;
use App\Cart;
use Illuminate\Http\Request;
use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateCartRequest;
use App\Http\Requests\Validations\UpdateCartRequest;

class CartController extends Controller
{
    use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->model_name = trans('app.model.cart');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cart_lists = Cart::whereHas('customer')->get();

        $trashes = Cart::onlyTrashed()->get();

        return view('admin.cart.index', compact('cart_lists', 'trashes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCartRequest $request)
    {
        setAdditionalCartInfo($request); //Set some system information using helper function

        $cart = Cart::create($request->all());

        $this->syncCartItems($cart, $request->input('cart'));

        if($cart)
            return back()->with('success', trans('messages.created', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Display the specified resource.
     *
     * @param  Cart $cart
     * @return \Illuminate\Http\Response
     */
    public function show(Cart $cart)
    {
        return view('admin.cart._show', compact('cart'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Cart $cart
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCartRequest $request, Cart $cart)
    {
        setAdditionalCartInfo($request); //Set some system information using helper function

        $cart->update($request->all());

        $this->syncCartItems($cart, $request->input('cart'));

        if($cart)
            return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Cart $cart
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, Cart $cart)
    {
        if($cart->delete())
            return back()->with('success', trans('messages.trashed', ['model' => $this->model_name]));

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
        $cart = Cart::onlyTrashed()->findOrFail($id);

        if($cart->restore())
            return back()->with('success', trans('messages.restored', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $cart = Cart::onlyTrashed()->findOrFail($id);

        if($cart->forceDelete())
            return back()->with('success',  trans('messages.deleted', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Sync up the list of items for the cart
     * @param  Cart $cart
     * @param  array $items
     * @return void
     */
    private function syncCartItems(Cart $cart, array $items)
    {
        $temp = [];

        foreach ($items as $item)
        {
            $item = (object) $item;
            $temp[$item->product_id] = [
                'item_description' => $item->item_description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ];
        }

        if (!empty($temp))
            $cart->products()->sync($temp);

        return true;
    }
}