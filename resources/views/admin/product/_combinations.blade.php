  <table class="table table-default" id="variantsTable">
    <thead>
      <tr>
        <th>{{ trans('app.sl_number') }}</th>
        <th>{{ trans('app.form.variants') }}
          <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('help.variants') }}"><sup><i class="fa fa-question"></i></sup></small>
        </th>
        <th>{{ trans('app.form.image') }}
          <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('help.variant_image') }}"><sup><i class="fa fa-question"></i></sup></small>
        </th>
        <th>{{ trans('app.form.sku') }}
          <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('help.sku') }}"><sup><i class="fa fa-question"></i></sup></small>
        </th>
        <th>{{ trans('app.form.stock_quantity') }}
          <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('help.stock_quantity') }}"><sup><i class="fa fa-question"></i></sup></small>
        </th>
        <th>{{ trans('app.form.price') }}
          <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('help.price') }}"><sup><i class="fa fa-question"></i></sup></small>
        </th>
        <th><i class="fa fa-trash-o"></i></th>
      </tr>
    </thead>
    <tbody style="zoom: 0.80;">
      @foreach($combinations as $combination)
        <tr class="variant-row">
          <td><div class="form-group">{{ $loop->iteration }}</div></td>
          <td>
            <div class="form-group">
              @foreach($combination as $attrId => $attrValue)
                {{ Form::hidden('variants['. $loop->parent->index .']['. $attrId .']', key($attrValue)) }}

                {{ current($attrValue) }}

                @if($attrValue !== end($combination))
                  <span class="text-primary"> &#8226; </span>
                @endif
              @endforeach
            </div>
          </td>
          <td>
            <label class="img-btn-with-preview">
              {{ Form::file('variant_images['. $loop->index .']', ['class' => 'variant-img']) }}
              <img src="{{ url("images/placeholders/no_img.png") }}" class="img-md" alt="variant-{{$loop->iteration}}">
            </label>
          </td>
          <td>
            <div class="form-group">
              {!! Form::text('variant_skus['. $loop->index .']', null, ['class' => 'form-control variant-sku', 'placeholder' => trans('app.placeholder.sku'), 'required']) !!}
            </div>
          </td>
          <td>
            <div class="form-group">
              {!! Form::number('variant_quantities['. $loop->index .']', null, ['class' => 'form-control variant-qtt', 'placeholder' => trans('app.placeholder.stock_quantity'), 'required']) !!}
            </div>
          </td>
          <td>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">{{ config('system_settings.currency_symbol', '$') }}</span>
                {!! Form::number('variant_prices['. $loop->index .']', null, ['class' => 'form-control variant-price', 'step' => 'any', 'placeholder' => trans('app.placeholder.price'), 'required']) !!}
              </div>
            </div>
          </td>
          <td>
            <div class="form-group text-muted">
              <i class="fa fa-close deleteThisRow" data-toggle="tooltip" data-placement="left" title="{{ trans('help.delete_this_combination') }}"></i>
            </div>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>