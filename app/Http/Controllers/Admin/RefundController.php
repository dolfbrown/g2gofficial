<?php

namespace App\Http\Controllers\Admin;

use App\Order;
use App\Refund;
use App\Common\Authorizable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\Refund\RefundDeclined;
use App\Events\Refund\RefundApproved;
use App\Events\Refund\RefundInitiated;
use App\Http\Requests\Validations\InitiateRefundRequest;

class RefundController extends Controller
{
    use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->model_name = trans('app.model.refund');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $refunds = Refund::open()->with('order')->get();

        $closed = Refund::closed()->with('order')->get();

        return view('admin.refund.index', compact('refunds', 'closed'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $order
     * @return \Illuminate\Http\Response
     */
    public function showRefundForm($order = Null)
    {
        if($order)
            $order = Order::findOrFail($order);;

        return view('admin.refund._initiate', compact('order'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function initiate(InitiateRefundRequest $request)
    {
        $refund = Refund::create($request->all());

        event(new RefundInitiated($refund, $request->filled('notify_customer')));

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Display the specified resource.
     *
     * @param  Refund $refund
     * @return \Illuminate\Http\Response
     */
    public function response(Refund $refund)
    {
        return view('admin.refund._response', compact('refund'));
    }

    public function approve(Request $request, Refund $refund)
    {
        if( ! $refund->update(['status' => Refund::STATUS_APPROVED]) )
            return back()->with('error', trans('messages.failed'));

        event(new RefundApproved($refund, $request->filled('notify_customer')));

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    public function decline(Request $request, Refund $refund)
    {
        if( ! $refund->update(['status' => Refund::STATUS_DECLINED]) )
            return back()->with('error', trans('messages.failed'));

        event(new RefundDeclined($refund, $request->filled('notify_customer')));

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }
}