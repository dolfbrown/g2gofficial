<div class="row">
  <div class="col-md-8">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">
          <i class="fa fa-cloud-download"></i>
          {{ trans('app.downloadable') }}
        </h3>
      </div> <!-- /.box-header -->
      <div class="box-body">
        <div class="row">
          <div class="col-md-9 nopadding-right">
            <div class="form-group">
              {!! Form::label('title', trans('app.form.title').'*', ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.product_title') }}"></i>
              {!! Form::text('title', null, ['class' => isset($product) ? 'form-control' : 'form-control makeSlug', 'placeholder' => trans('app.placeholder.title'), 'required']) !!}
              <div class="help-block with-errors"></div>
            </div>
          </div>
          <div class="col-md-3 nopadding-left">
            <div class="form-group">
              {!! Form::label('active', trans('app.form.status').'*', ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.product_active') }}"></i>
              {!! Form::select('active', ['1' => trans('app.active'), '0' => trans('app.inactive')], !isset($product) ? 1 : null, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.status'), 'required']) !!}
              <div class="help-block with-errors"></div>
            </div>
          </div>
        </div>

        <div class="row">
            <div class="col-md-6 nopadding-right">
              <div class="form-group">
                {!! Form::label('sku', trans('app.form.sku').'*', ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.sku') }}"></i>
                {!! Form::text('sku', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.sku'), 'required']) !!}
                <div class="help-block with-errors"></div>
              </div>
            </div>
            <div class="col-md-3 nopadding">
              <div class="form-group">
                {!! Form::label('stock_quantity', trans('app.form.stock_quantity').'*', ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.stock_quantity') }}"></i>
                {!! Form::number('stock_quantity', isset($product) ? null : 1, ['min' => 0, 'class' => 'form-control', 'placeholder' => trans('app.placeholder.stock_quantity'), 'required']) !!}
                <div class="help-block with-errors"></div>
              </div>
            </div>
            <div class="col-md-3 nopadding-left">
              <div class="form-group">
                {!! Form::label('min_order_quantity', trans('app.form.min_order_quantity'), ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.min_order_quantity') }}"></i>
                {!! Form::number('min_order_quantity', isset($product) ? null : 1, ['min' => 1, 'class' => 'form-control', 'placeholder' => trans('app.placeholder.min_order_quantity')]) !!}
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3 nopadding-right">
              <div class="form-group">
                {!! Form::label('price', trans('app.form.price').'*', ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.price') }}"></i>
                <div class="input-group">
                  <span class="input-group-addon">{{ config('system_settings.currency_symbol') ?: '$' }}</span>
                  <input name="price" value="{{ isset($product) ? $product->price : Null }}" type="number" step="any" placeholder="{{ trans('app.placeholder.price') }}" class="form-control" required="required">
                </div>
                <div class="help-block with-errors"></div>
              </div>
            </div>
            <div class="col-md-3 nopadding-left nopadding-right">
              <div class="form-group">
                {!! Form::label('offer_price', trans('app.form.offer_price'), ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.offer_price') }}"></i>
                <div class="input-group">
                  <span class="input-group-addon">{{ config('system_settings.currency_symbol') ?: '$' }}</span>
                  {!! Form::number('offer_price', null, ['class' => 'form-control', 'step' => 'any', 'placeholder' => trans('app.placeholder.offer_price')]) !!}
                </div>
              </div>
            </div>
            <div class="col-md-3 nopadding-left nopadding-right">
              <div class="form-group">
                {!! Form::label('offer_start', trans('app.form.offer_start'), ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.offer_start') }}"></i>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  {!! Form::text('offer_start', null, ['class' => 'form-control datetimepicker', 'placeholder' => trans('app.placeholder.offer_start')]) !!}
                </div>
                <div class="help-block with-errors"></div>
              </div>
            </div>
            <div class="col-md-3 nopadding-left">
              <div class="form-group">
                {!! Form::label('offer_end', trans('app.form.offer_end'), ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.offer_end') }}"></i>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  {!! Form::text('offer_end', null, ['class' => 'form-control datetimepicker', 'placeholder' => trans('app.placeholder.offer_end')]) !!}
                </div>
                <div class="help-block with-errors"></div>
              </div>
            </div>
          </div>

        <fieldset>
          <legend>{{ trans('app.form.key_features') }}
              <button id="AddMoreField" class="btn btn-xs btn-new" data-toggle="tooltip" data-title="{{ trans('help.add_input_field') }}"><i class="fa fa-plus"></i></button>
          </legend>
          <div id="DynamicInputsWrapper">
            @if(isset($product) && $product->key_features)
              @foreach(unserialize($product->key_features) as $key_feature)
                <div class="form-group">
                  <div class="input-group">
                    {!! Form::text('key_features[]', $key_feature, ['class' => 'form-control input-sm', 'placeholder' => trans('app.placeholder.key_feature')]) !!}
                    <span class="input-group-addon">
                      <i class="fa fa-times removeThisInputBox" data-toggle="tooltip" data-title="{{ trans('help.remove_input_field') }}"></i>
                    </span>
                  </div>
                </div>
              @endforeach
            @else
              <div class="form-group">
                <div class="input-group">
                  {!! Form::text('key_features[]', null, ['id' => 'field_1', 'class' => 'form-control input-sm', 'placeholder' => trans('app.placeholder.key_feature')]) !!}
                  <span class="input-group-addon">
                    <i class="fa fa-times removeThisInputBox" data-toggle="tooltip" data-title="{{ trans('help.remove_input_field') }}"></i>
                  </span>
                </div>
              </div>
            @endif
          </div>
        </fieldset>

        <div class="form-group">
          {!! Form::label('description', trans('app.form.description').'*', ['class' => 'with-help']) !!}
          <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.product_description') }}"></i>
          {!! Form::textarea('description', null, ['class' => 'form-control summernote', 'rows' => '4', 'placeholder' => trans('app.placeholder.description'), 'required']) !!}
          <div class="help-block with-errors">{!! $errors->first('description', ':message') !!}</div>
        </div>

        <fieldset>
          <legend>
            {{ trans('app.form.images') }}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.product_images') }}"></i>
          </legend>
          <div class="form-group">
            <div class="file-loading">
              <input id="dropzone-input" name="images[]" type="file" accept="image/*" multiple>
            </div>
          </div>
        </fieldset>

        <fieldset>
          <legend>
            {{ trans('app.downloadable') }}
            <i class="fa fa-question-circle small" data-toggle="tooltip" data-placement="top" title="{{ trans('help.downloadable_file') }}"></i>
          </legend>

          @if(isset($product))
            @foreach($product->attachments as $attachment )
                <h2 class="text-center">
                  <a href="{{ route('attachment.download', $attachment) }}" class="small" data-toggle="tooltip" data-title="{{ trans('app.download') }}">
                    <i class="fa fa-download"></i>
                  </a>

                  <span class="indent10">
                    {{ $attachment->name }}
                  </span>

                  <small class="indent10">
                    ({{ get_formated_file_size($attachment->size) }})
                    {{ $attachment->updated_at->diffForHumans() }}
                  </small>
                </h2>

                @if(!$loop->last)
                  <br/>
                @endif
            @endforeach
          @endif

          <div class="row">
            <div class="col-md-9 nopadding-right">
               <input id="uploadFile" placeholder="{{ trans('app.downloadable') }}" class="form-control" disabled="disabled" style="height: 28px;" />
              </div>
              <div class="col-md-3 nopadding-left">
                <div class="fileUpload btn btn-primary btn-block btn-flat">
                    <span>{{ trans('app.form.upload') }} </span>
                    <input type="file" name="file" id="uploadBtn" class="upload" />
                </div>
              </div>
          </div>
        </fieldset>

        <div class="cleaflex spacer20"></div>

        <div class="form-group">
          {!! Form::label('linked_items[]', trans('app.form.linked_items'), ['class' => 'with-help']) !!}
          <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.inventory_linked_items') }}"></i>
          {!! Form::select('linked_items[]', $products , isset($product) ? unserialize($product->linked_items) : Null, ['class' => 'form-control select2-normal', 'multiple' => 'multiple']) !!}
          <div class="help-block with-errors"></div>
        </div>

        @if(isset($product) && $product->variants->count())
            <fieldset>
              <legend>
                  {{ trans('app.variants') }}
                  {{-- <a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.addVariant', $product) }}" class="ajax-modal-btn btn btn-xs btn-new" data-toggle="tooltip" data-title="{{ trans('app.add_variants') }}"><i class="fa fa-plus"></i></a> --}}
              </legend>

              @include('admin.product._variants')

            </fieldset>
        @else

          <div class="cleaflex spacer20"></div>

          <a href="javascript:void(0)" class="btn btn-default pull-right" data-toggle="collapse" data-target="#myAttributes"><i class="fa fa-plus"></i> {{ trans('app.add_variants')}}</a>

          <div id="myAttributes" class="collapse">
            <div>{!! trans('help.choose_attributes') !!}</div>
            <div class="spacer20"></div>

            @foreach ($attributes as $attribute)
              <div class="form-group">
                {!! Form::label($attribute->name, $attribute->name) !!}

                <select class="form-control select2-set_attribute" id="{{ $attribute->id }}" name="{{ $attribute->id }}[]" multiple='multiple' placeholder="{{ trans('app.placeholder.attribute_values') }}">

                  <option value="">{{ trans('app.placeholder.select') }}</option>

                  @foreach($attribute->attributeValues as $attributeValue)
                    <option value="{{ $attributeValue->id }}">
                      {{ $attributeValue->value }}
                    </option>
                  @endforeach
                </select>
              </div>
            @endforeach

            <button class="btn btn-default" id="setCombinations">{{ trans('app.set_variants') }}</button>
            <div class="spacer20"></div>
          </div>

          <div id="combinationsPlaceholder"></div>
        @endif

        <div class="cleaflex spacer30"></div>

        <p class="help-block">* {{ trans('app.form.required_fields') }}</p>

        <div class="box-tools pull-right">
          {!! Form::submit(trans('app.form.save'), ['class' => 'btn btn-flat btn-lg btn-primary']) !!}
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4 nopadding-left">
    <div class="box">
      <div class="box-header with-border">
          <h3 class="box-title">{{ trans('app.organization') }}</h3>
      </div> <!-- /.box-header -->
      <div class="box-body">
          <div class="form-group">
            {!! Form::label('available_from', trans('app.form.available_from'), ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.available_from') }}"></i>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
              {!! Form::text('available_from', null, ['class' => 'datetimepicker form-control', 'placeholder' => trans('app.placeholder.available_from')]) !!}
            </div>
          </div>

          <div class="form-group">
            {!! Form::label('category_list[]', trans('app.form.categories').'*') !!}
            {!! Form::select('category_list[]', $categories , Null, ['class' => 'form-control select2-normal', 'multiple' => 'multiple', 'required']) !!}
            <div class="help-block with-errors"></div>
          </div>

        <fieldset>
          <legend>{{ trans('app.branding') }}</legend>
          <div class="form-group">
              {!! Form::label('origin_country', trans('app.form.origin'), ['class' => 'with-help']) !!}
              {!! Form::select('origin_country', $countries , null, ['class' => 'form-control select2', 'placeholder' => trans('app.placeholder.origin')]) !!}
              <div class="help-block with-errors"></div>
          </div>

          <div class="form-group">
            {!! Form::label('brand', trans('app.form.brand'), ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.brand') }}"></i>
            {!! Form::text('brand', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.brand')]) !!}
          </div>

          <div class="form-group">
            {!! Form::label('model_number', trans('app.form.model_number'), ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.model_number') }}"></i>
            {!! Form::text('model_number', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.model_number')]) !!}
          </div>

          <div class="form-group">
            {!! Form::label('manufacturer_id', trans('app.form.manufacturer'), ['class' => 'with-help']) !!}
            {!! Form::select('manufacturer_id', $manufacturers , null, ['class' => 'form-control select2', 'placeholder' => trans('app.placeholder.manufacturer')]) !!}
            <div class="help-block with-errors"></div>
          </div>
        </fieldset>

        <fieldset>
          <legend>{{ trans('app.reporting') }}</legend>
          <div class="form-group">
            {!! Form::label('purchase_price', trans('app.form.purchase_price'), ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.purchase_price') }}"></i>
            <div class="input-group">
              <span class="input-group-addon">{{ config('system_settings.currency_symbol') ?: '$' }}</span>
              {!! Form::number('purchase_price', null, ['class' => 'form-control', 'step' => 'any', 'placeholder' => trans('app.placeholder.purchase_price')]) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('supplier_id', trans('app.form.supplier'), ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.select_supplier') }}"></i>
            {!! Form::select('supplier_id', $suppliers, null, ['class' => 'form-control select2', 'placeholder' => trans('app.placeholder.select')]) !!}
          </div>
        </fieldset>

        <fieldset>
          <legend>{{ trans('app.seo') }}</legend>
          <div class="form-group">
            {!! Form::label('slug', trans('app.form.slug').'*', ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.slug') }}"></i>
            {!! Form::text('slug', null, ['class' => 'form-control slug', 'placeholder' => 'SEO Friendly URL', 'required']) !!}
            <div class="help-block with-errors"></div>
          </div>

          <div class="form-group">
            {!! Form::label('tag_list[]', trans('app.form.tags'), ['class' => 'with-help']) !!}
            {!! Form::select('tag_list[]', $tags, null, ['class' => 'form-control select2-tag', 'multiple' => 'multiple']) !!}
          </div>

          <div class="form-group">
            {!! Form::label('meta_title', trans('app.form.meta_title'), ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.meta_title') }}"></i>
            {!! Form::text('meta_title', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.meta_title')]) !!}
          </div>

          <div class="form-group">
            {!! Form::label('meta_description', trans('app.form.meta_description'), ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.meta_description') }}"></i>
            {!! Form::text('meta_description', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.meta_description')]) !!}
          </div>
        </fieldset>
      </div>
    </div>
  </div>
</div>