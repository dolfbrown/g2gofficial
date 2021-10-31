@component('mail::panel')
{{ trans('messages.order_id') . ': ' . $refund_detail->order->order_number }}<br/>
{{ trans('messages.amount') . ': ' . get_formated_currency($refund_detail->amount) }}<br/>
{!! trans('messages.status') . ': ' . $refund_detail->statusName() !!}
@endcomponent
<br/>