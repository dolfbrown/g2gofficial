@can('view', $product)
	<a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.show', $product->id) }}" class="ajax-modal-btn">
		{{ $product->title }}
	</a>
@else
	{{ $product->title }}
@endcan

@unless($product->active)
    <span class="label label-default indent10">{{ trans('app.inactive') }}</span>
@endunless