<?php

namespace App\Http\Controllers\Storefront;

use Auth;
use App\Order;
use App\Feedback;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\OrderDetailRequest;
use App\Http\Requests\Validations\ProductFeedbackCreateRequest;

class FeedbackController extends Controller
{
    /**
     * Show feedback form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Order   $order
     *
     * @return \Illuminate\Http\Response
     */
    public function feedback_form(OrderDetailRequest $request, Order $order)
    {
        return view('feedback_form', compact('order'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Order   $order
     * @return \Illuminate\Http\Response
     */
    public function save_product_feedbacks(ProductFeedbackCreateRequest $request, Order $order)
    {
        $inputs = $request->input('items');
        $customer_id = Auth::guard('customer')->user()->id; //Set customer_id

        foreach ($order->products as $product) {
            $feedback_data = $inputs[$product->id];
            $feedback_data['customer_id'] = $customer_id;

            $feedback = $product->feedbacks()->create($feedback_data);

            // Update feedback_id in order_items table
            \DB::table('order_items')->where('order_id', $product->pivot->order_id)
            ->where('product_id', $product->id)->update(['feedback_id' => $feedback->id]);
        }

        return back()->with('success', trans('theme.notify.your_feedback_saved'));
    }
}