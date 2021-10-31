<?php

namespace App\Http\Requests\Validations;

use App\Customer;
use App\Http\Requests\Request;

class RefundRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->user() instanceof Customer) {
            return $this->route('order')->customer_id == $this->user()->id;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $order = $this->route('order');

        Request::merge([
            'order_id' => $order->id,
            'order_fulfilled' => $order->isFulfilled(),
        ]);

        // Update the order if goods received
        if($this->goods_received == 1){
            $order->order_status_id = 6; // Delivered Status. This id is freezed by system config
            $order->goods_received = 1;
            $order->save();
        }

        return [
           'goods_received' => 'required',
           'description' => 'required',
           'amount' => 'required|numeric|max:' . $order->grand_total,
        ];
    }
}
