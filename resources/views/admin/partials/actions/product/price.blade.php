@if($product->hasOffer())
	@php
		$offer_price_help =
			trans('help.offer_starting_time') . ': ' .
			$product->offer_start->diffForHumans() . ' ' . trans('app.and') . ' ' .
			trans('help.offer_ending_time') . ': ' .
			$product->offer_end->diffForHumans();
	@endphp

	<small class="text-muted">{{ get_formated_currency($product->price) }}</small>
	<small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ $offer_price_help }}"><sup><i class="fa fa-question"></i></sup></small><br/>
@endif
{{ get_formated_currency($product->currnt_price()) }}
