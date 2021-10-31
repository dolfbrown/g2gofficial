<?php

namespace App\Http\Controllers\Admin;

use App\Coupon;
use Illuminate\Http\Request;
use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateCouponRequest;
use App\Http\Requests\Validations\UpdateCouponRequest;

class CouponController extends Controller
{
    use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->model_name = trans('app.model.coupon');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coupons = Coupon::withCount(['customers', 'shippingZones'])->get();

        $trashes = Coupon::onlyTrashed()->get();

        return view('admin.coupon.index', compact('coupons', 'trashes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.coupon._create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCouponRequest $request)
    {
        $coupon = Coupon::create($request->all());

        if ($request->input('customer_list'))
            $this->syncCustomers($coupon, $request->input('customer_list'));

        if ($request->input('zone_list'))
            $this->syncZones($coupon, $request->input('zone_list'));

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Display the specified resource.
     *
     * @param  Coupon $coupon
     * @return \Illuminate\Http\Response
     */
    public function show(Coupon $coupon)
    {
        return view('admin.coupon._show', compact('coupon'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Coupon $coupon
     * @return \Illuminate\Http\Response
     */
    public function edit(Coupon $coupon)
    {
        $customer_list = $this->customer_list($coupon);

        return view('admin.coupon._edit', compact('coupon', 'customer_list'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        $coupon->update($request->all());

        if ($request->input('customer_list'))
            $this->syncCustomers($coupon, $request->input('customer_list'));

        if ($request->input('zone_list'))
            $this->syncZones($coupon, $request->input('zone_list'));

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Coupon $coupon
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, Coupon $coupon)
    {
        if($coupon->delete())
            return back()->with('success', trans('messages.trashed', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Restore the specified resource from soft delete.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, $id)
    {
        $coupon = Coupon::onlyTrashed()->findOrFail($id);

        if($coupon->restore())
            return back()->with('success', trans('messages.restored', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $coupon = Coupon::onlyTrashed()->findOrFail($id);

        if($coupon->forceDelete())
            return back()->with('success',  trans('messages.deleted', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    private function customer_list($coupon)
    {
        $customers = $coupon->customers;

        $results = [];
        foreach ($customers as $customer)
            $results[$customer->id] = get_formated_cutomer_str($customer);

        return $results;
    }

    private function syncCustomers($coupon, array $ids)
    {
        $coupon->customers()->sync($ids);
    }

    private function syncZones($coupon, array $ids)
    {
        $coupon->shippingZones()->sync($ids);
    }
}