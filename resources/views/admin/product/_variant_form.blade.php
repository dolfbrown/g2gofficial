<div class="row">
  <div class="col-md-8 nopadding-right">
    <div class="form-group">
      {!! Form::label('title', trans('app.form.title'), ['class' => 'with-help']) !!}
      <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.product_title') }}"></i>
      {!! Form::text('title', isset($variant) ? $variant->title : $product->title, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.title')]) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>
  <div class="col-md-4 nopadding-left">
    <div class="form-group">
      {!! Form::label('available_from', trans('app.form.available_from'), ['class' => 'with-help']) !!}
      <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.available_from') }}"></i>
      <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        {!! Form::text('available_from', isset($variant) ? $variant->available_from : $product->available_from, ['class' => 'datetimepicker form-control', 'placeholder' => trans('app.placeholder.available_from')]) !!}
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-8 nopadding-right">
    <div class="form-group">
      {!! Form::label('sku', trans('app.form.sku').'*', ['class' => 'with-help']) !!}
      <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.sku') }}"></i>
      {!! Form::text('sku', isset($variant) ? $variant->sku : $product->sku . '-' . ($product->variants->count() + 1), ['class' => 'form-control', 'placeholder' => trans('app.placeholder.sku'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>

  <div class="col-md-4 nopadding-left">
    <div class="form-group">
      {!! Form::label('condition', trans('app.form.condition').'*', ['class' => 'with-help']) !!}
      <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.seller_product_condition') }}"></i>
      {!! Form::select('condition', ['New' => trans('app.new'), 'Used' => trans('app.used'), 'Refurbished' => trans('app.refurbished')], isset($variant) ? $variant->condition : $product->condition, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.select'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>
</div>

<fieldset>
  <legend> {{ trans('app.image') }} </legend>

  @if(isset($variant) && $variant->image)
    <img src="{{ get_storage_file_url($variant->image->path, 'small') }}" alt="{{ trans('app.variant_image') }}">
    <label>
      <span style="margin-left: 10px;">
        {!! Form::checkbox('delete_image', 1, null, ['class' => 'icheck']) !!} {{ trans('app.form.delete_image') }}
      </span>
    </label>
  @endif

  <div class="row">
    <div class="col-md-9 nopadding-right">
       <input id="uploadFile" placeholder="{{ trans('app.variant_image') }}" class="form-control" disabled="disabled" style="height: 28px;" />
      </div>
      <div class="col-md-3 nopadding-left">
        <div class="fileUpload btn btn-primary btn-block btn-flat">
            <span>{{ trans('app.form.upload') }} </span>
            <input type="file" name="image" id="uploadBtn" class="upload" />
        </div>
      </div>
  </div>
</fieldset>

<div class="spacer30"></div>

<fieldset>
  <legend>{{ trans('app.attributes') }}</legend>

  @php $i = 1; @endphp
  <div class="row">
    @foreach ($attributes as $attribute)
      @if($productAttributeIds->contains($attribute->id))

        <div class="col-md-6 nopadding-{{ ($i%2) ? "right" : "left"}}">
          <div class="form-group">
            {!! Form::label($attribute->name, $attribute->name . '*', ['class' => 'with-help']) !!}

            {!! Form::select('attributes[' . $attribute->id . ']', $attribute->attributeValues->pluck('value', 'id'), Null, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.attribute_values'), 'required']) !!}

            {{-- <select class="form-control select2-normal" id="{{ $attribute->id }}" name="attributes[{{ $attribute->id }}]" placeholder="{{ trans('app.placeholder.attribute_values') }}" required="required">

              <option value="">{{ trans('app.placeholder.select') }}</option>

              @foreach($attribute->attributeValues as $attributeValue)
                <option value="{{ $attributeValue->id }}" {{ (old('attributes') != Null) && in_array($attributeValue->id, old('attributes')) ? "selected" : "" }}>
                  {{ $attributeValue->value }}
                </option>
              @endforeach
            </select> --}}
            <div class="help-block with-errors"></div>
          </div>
        </div>

        @php $i++; @endphp
      @endif
    @endforeach
  </div>
</fieldset>

<fieldset>
  <legend>{{ trans('app.inventory_rules') }}</legend>

  <div class="row">
    <div class="col-md-6 nopadding-right">
      <div class="form-group">
        {!! Form::label('price', trans('app.form.price').'*', ['class' => 'with-help']) !!}
        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.price') }}"></i>
        <div class="input-group">
          <span class="input-group-addon">{{ config('system_settings.currency_symbol') ?: '$' }}</span>
          <input name="price" value="{{ isset($variant) ? $variant->price : $product->price }}" type="number" step="any" placeholder="{{ trans('app.placeholder.price') }}" class="form-control" required="required">
        </div>
        <div class="help-block with-errors"></div>
      </div>
    </div>
    <div class="col-md-6 nopadding-left">
      <div class="form-group">
        {!! Form::label('offer_price', trans('app.form.offer_price'), ['class' => 'with-help']) !!}
        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.offer_price') }}"></i>
        <div class="input-group">
          <span class="input-group-addon">{{ config('system_settings.currency_symbol') ?: '$' }}</span>
          {!! Form::number('offer_price', isset($variant) ? $variant->offer_price : $product->offer_price, ['class' => 'form-control', 'step' => 'any', 'placeholder' => trans('app.placeholder.offer_price')]) !!}
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4 nopadding-right">
      <div class="form-group">
        {!! Form::label('stock_quantity', trans('app.form.stock_quantity').'*', ['class' => 'with-help']) !!}
        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.stock_quantity') }}"></i>
        {!! Form::number('stock_quantity', isset($variant) ? $variant->stock_quantity : $product->stock_quantity, ['min' => 0, 'class' => 'form-control', 'placeholder' => trans('app.placeholder.stock_quantity'), 'required']) !!}
        <div class="help-block with-errors"></div>
      </div>
    </div>

    <div class="col-md-4 nopadding">
      <div class="form-group">
        {!! Form::label('min_order_quantity', trans('app.form.min_order_quantity'), ['class' => 'with-help']) !!}
        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.min_order_quantity') }}"></i>
        {!! Form::number('min_order_quantity', isset($variant) ? $variant->min_order_quantity : $product->min_order_quantity, ['min' => 1, 'class' => 'form-control', 'placeholder' => trans('app.placeholder.min_order_quantity')]) !!}
      </div>
    </div>

    <div class="col-md-4 nopadding-left">
      <div class="form-group">
        {!! Form::label('shipping_weight', trans('app.form.shipping_weight'), ['class' => 'with-help']) !!}
        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.shipping_weight') }}"></i>
        <div class="input-group">
          {!! Form::number('shipping_weight', isset($variant) ? $variant->shipping_weight : $product->shipping_weight, ['class' => 'form-control', 'step' => 'any', 'min' => 0, 'placeholder' => trans('app.placeholder.shipping_weight')]) !!}
          <span class="input-group-addon">{{ config('system_settings.weight_unit', 'gm') }}</span>
        </div>
        <div class="help-block with-errors"></div>
      </div>
    </div>
  </div>
</fieldset>