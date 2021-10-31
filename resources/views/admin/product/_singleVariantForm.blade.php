<div class="modal-dialog modal-md">
    <div class="modal-content">
        {!! Form::open(['route' => ['admin.catalog.product.saveVariant', $product], 'files' => true, 'id' => 'form', 'data-toggle' => 'validator']) !!}

        <div class="modal-header">
        	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            {{ trans('app.add_variant') }}
        </div>
        <div class="modal-body">

            @foreach($product->variants as $variant)
                @if($loop->first)
                    @foreach($variant->attributes as $attr)
                        <div class="form-group">
                            {!! Form::label($attr->name, $attr->name . '*') !!}
                            {!! Form::text($attr->name, $variant->sku . $loop->iteration, ['class' => 'form-control', 'placeholder' => $attr->name, 'required']) !!}
                        </div>
                    @endforeach
                @endif
            @endforeach

            <div class="form-group">
                {!! Form::label('stock_quantity', trans('app.form.stock_quantity').'*') !!}
                {!! Form::number('stock_quantity', null, ['id' => 'stock_quantity', 'class' => 'form-control', 'step' => 'any', 'placeholder' => trans('app.placeholder.stock_quantity'), 'required']) !!}
                <div class="help-block with-errors"></div>
            </div>
        </div>
        <div class="modal-footer">
            {!! Form::submit(trans('app.update'), ['class' => 'btn btn-flat btn-new']) !!}
        </div>
        {!! Form::close() !!}
    </div> <!-- / .modal-content -->
</div> <!-- / .modal-dialog -->

<script type="text/javascript">
{{--     $('#stock_quantity').on('change', function(e){
        var qttNote = '.qtt-' + {{ Request::route('product') }};

        $(qttNote).text($(this).val());
    });
 --}}</script>