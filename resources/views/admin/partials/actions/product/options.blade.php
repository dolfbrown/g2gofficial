@can('view', $product)
	<a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.show', $product->id) }}" class="ajax-modal-btn"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.detail') }}" class="fa fa-expand"></i></a>&nbsp;
@endcan

@can('update', $product)
	@if($product->downloadable)
		<a href="{{ route('admin.catalog.downloadable.edit', $product->id) }}"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}" class="fa fa-edit"></i></a>&nbsp;
	@else
		<a href="{{ route('admin.catalog.product.edit', $product->id) }}"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}" class="fa fa-edit"></i></a>&nbsp;
	@endif
@endcan

@can('delete', $product)
	{!! Form::open(['route' => ['admin.catalog.product.trash', $product->id], 'method' => 'delete', 'class' => 'data-form']) !!}
		{!! Form::button('<i class="fa fa-trash-o"></i>', ['type' => 'submit', 'class' => 'confirm ajax-silent', 'title' => trans('app.trash'), 'data-toggle' => 'tooltip', 'data-placement' => 'top']) !!}
	{!! Form::close() !!}
@endcan
