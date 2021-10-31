@if(Gate::allows('update', $product))
	<a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.editQtt', $product->id) }}" class="ajax-modal-btn qtt-{{$product->id}}" data-toggle="tooltip" data-placement="top" title="{{ trans('app.update') }}">
		{{ ($product->stock_quantity > 0) ? $product->stock_quantity : trans('app.out_of_stock') }}
	</a>
@else
	{{ ($product->stock_quantity > 0) ? $product->stock_quantity : trans('app.out_of_stock') }}
@endif