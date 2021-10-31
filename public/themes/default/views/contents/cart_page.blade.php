<section>
  <div class="container">
    @if($carts->count() > 0)
      @php
        if( Auth::guard('customer')->check() ) {
          $customer = Auth::guard('customer')->user();
          $shipping_address = $customer->shippingAddress ? $customer->shippingAddress : $customer->primaryAddress;
          $shipping_country_id = $shipping_address ? $shipping_address->country_id : config('system_settings.address_default_country');
          $shipping_state_id = $shipping_address ? $shipping_address->state_id : config('system_settings.address_default_state');
        }
        else {
          $geoip = geoip( request()->ip() );
          $shipping_country_id = get_id_of_model('countries', 'iso_3166_2', $geoip->iso_code);
          $shipping_state_id = $geoip->state;
        }

        $country_dropdown = '';
        foreach($countries as $country_id => $country_name){
          $country_dropdown .= '<option value="' . $country_id . '" ';
          $country_dropdown .= $country_id == $shipping_country_id ? 'selected' : '';
          $country_dropdown .= '>' . $country_name . '</option>';
        }
      @endphp

      @foreach($carts as $cart)
        @php
          $cart_total = 0;

          $is_downloadable = $cart->is_downloadable();

          $shipping_zone = get_shipping_zone_of($shipping_country_id, $shipping_state_id);

          $shipping_options = isset($shipping_zone->id) ? getShippingRates($shipping_zone->id) : 'NaN';

          $default_packaging = $cart->shippingPackage ?? getPlatformDefaultPackaging();
        @endphp

        <div class="row shopping-cart-table-wrap space30 {{$expressId == $cart->id ? 'selected' : ''}}" id="cartId{{$cart->id}}" data-cart="{{$cart->id}}" data-downlodable="{{$is_downloadable}}">
          {!! Form::model($cart, ['method' => 'PUT', 'route' => ['cart.update', $cart->id], 'id' => 'formId'.$cart->id]) !!}
            {{ Form::hidden('cart_id', $cart->id, ['id' => 'cart-id'.$cart->id]) }}
            {{ Form::hidden('zone_id', isset($shipping_zone->id) ? $shipping_zone->id : Null, ['id' => 'zone-id'.$cart->id]) }}
            {{ Form::hidden('tax_id', isset($shipping_zone->id) ? $shipping_zone->tax_id : Null, ['id' => 'tax-id'.$cart->id]) }}
            {{ Form::hidden('taxrate', $cart->taxrate, ['id' => 'cart-taxrate'.$cart->id]) }}
            {{ Form::hidden('packaging_id', $cart->packaging_id, ['id' => 'packaging-id'.$cart->id]) }}
            {{ Form::hidden('shipping_rate_id', $cart->shipping_rate_id, ['id' => 'shipping-rate-id'.$cart->id]) }}
            {{-- {{ Form::hidden('shipping_rate_id', Null, ['id' => 'shipping-rate-id'.$cart->id]) }} --}}
            {{ Form::hidden('discount_id', $cart->coupon_id, ['id' => 'discount-id'.$cart->id]) }}
            {{ Form::hidden('handling_cost', config('system_settings.order_handling_cost'), ['id' => 'handling-cost'.$cart->id]) }}
            <div class="col-md-9 nopadding">

                <div class="notice notice-warning notice-sm hidden" id="shipping-notice{{$cart->id}}">
                  <strong>{{ trans('theme.warning') }}</strong> @lang('theme.notify.seller_doesnt_ship')
                </div>

                <div class="space20"></div>

                <div class="shopping-cart-header-section">
                  <span>@lang('theme.cart_details')</span>

                  @unless($is_downloadable)
                    <span class="pull-right">
                        @lang('theme.ship_to'):
                        <select name="ship_to" class="selectBoxIt ship_to" id="shipTo{{$cart->id}}" data-cart="{{$cart->id}}">
                          {!! $country_dropdown !!}
                        </select>
                    </span>
                  @endunless
                </div>

                <table class="table table shopping-cart-item-table" id="table{{$cart->id}}">
                    <thead>
                      <tr>
                          <th width="65px">{{ trans('theme.image') }}</th>
                          @if($is_downloadable)
                            <th>{{ trans('theme.description') }}</th>
                            <th>{{ trans('theme.price') }}</th>
                          @else
                            <th width="52%">{{ trans('theme.description') }}</th>
                            <th>{{ trans('theme.price') }}</th>
                            <th>{{ trans('theme.quantity') }}</th>
                            <th>{{ trans('theme.total') }}</th>
                          @endif
                          <th></th>
                      </tr>
                    </thead>

                    <tbody>
                      @foreach($cart->products as $item)
                        @php
                          $unit_price = $item->pivot->variant_id ? $item->pivot->unit_price : $item->currnt_price();
                          $item_total = $unit_price * $item->pivot->quantity;
                          $cart_total += $item_total;
                          $uniqeItemId = $item->pivot->id;
                        @endphp
                        <tr class="cart-item-tr">
                          <td>
                            <input type="hidden" class="freeShipping{{$cart->id}}" value="{{$item->free_shipping}}">
                            <input type="hidden" id="unitWeight{{$uniqeItemId}}" value="{{$item->shipping_weight}}">
                            {{ Form::hidden('shipping_weight['.$uniqeItemId.']', ($item->shipping_weight * $item->pivot->quantity), ['id' => 'itemWeight'.$uniqeItemId, 'class' => 'itemWeight'.$cart->id]) }}

                            @if($item->pivot->variant_id)
                              <img src="{{ get_variant_img_src($item->pivot->variant_id, 'small') }}" alt="{{ $item->slug }}" title="{{ $item->slug }}" />
                            @else
                              <img src="{{ get_product_img_src($item, 'small') }}" alt="{{ $item->slug }}" title="{{ $item->slug }}" />
                            @endif
                          </td>
                          <td>
                            <div class="shopping-cart-item-title">
                              <a href="{{ route('show.product', $item->slug) }}" class="product-info-title">{{ $item->pivot->item_description }}</a>
                            </div>
                          </td>
                          <td class="shopping-cart-item-price">
                            <span>{{ get_formated_currency_symbol() }}
                              <span id="item-price{{$cart->id}}-{{$uniqeItemId}}" data-value="{{$unit_price}}">{{ number_format($unit_price, 2, '.', '') }}</span>
                            </span>
                          </td>

                          @unless($is_downloadable)
                            <td>
                              <div class="product-info-qty-item">
                                <button class="product-info-qty product-info-qty-minus">-</button>
                                <input name="quantity[{{$uniqeItemId}}]" id="itemQtt{{$uniqeItemId}}" class="product-info-qty product-info-qty-input" data-cart="{{$cart->id}}" data-item="{{$uniqeItemId}}" data-min="{{$item->min_order_quantity}}" data-max="{{$item->stock_quantity}}" type="text" value="{{$item->pivot->quantity}}" {{ $item->downloadable ? 'disabled' : '' }}>
                                <button class="product-info-qty product-info-qty-plus">+</button>
                              </div>
                            </td>
                            <td>
                              <span>{{ get_formated_currency_symbol() }}
                                <span id="item-total{{$cart->id}}-{{$uniqeItemId}}" class="item-total{{$cart->id}}">{{ number_format($item_total, 2, '.', '') }}</span>
                              </span>
                            </td>
                          @endunless

                          <td>
                            <a class="cart-item-remove" href="javascript:void(0)" data-cart="{{$cart->id}}" data-item="{{$uniqeItemId}}" data-toggle="tooltip" title="@lang('theme.remove_item')">&times;</a>
                          </td>
                        </tr> <!-- /.order-body -->
                      @endforeach
                    </tbody>
                </table>

                <div class="space20"></div>

                <div class="row">
                  <div class="col-md-12">
                          <div class="input-group full-width">
                            <span class="input-group-addon flat">
                              <i class="fa fa-ticket"></i>
                            </span>
                            <input name="coupon" value="{{ $cart->coupon ? $cart->coupon->code : Null }}" id="coupon{{$cart->id}}" class="form-control flat" type="text" placeholder="@lang('theme.placeholder.have_coupon')">
                            <span class="input-group-btn">
                              <button class="btn btn-default flat apply_seller_coupon" type="button" data-cart="{{$cart->id}}">@lang('theme.button.apply_coupon')</button>
                            </span>
                          </div><!-- /.input-group -->
                  </div><!-- /.col-md- -->
                </div><!-- /.row -->

                <div class="space30"></div>

            </div><!-- /.col-md-9 -->

            <div class="col-md-3 space20">
                <div class="side-widget space50" id="cart-summary{{$cart->id}}">
                    <h3 class="side-widget-title"><span>{{ trans('theme.cart_summary') }}</span></h3>
                    <ul class="shopping-cart-summary">
                        <li>
                          <span>{{ trans('theme.subtotal') }}</span>
                          <span>{{ get_formated_currency_symbol() }}
                            <span id="summary-total{{$cart->id}}">{{ number_format($cart_total, 2, '.', '') }}</span>
                          </span>
                        </li>
                        @unless($is_downloadable)
                          <li>
                            <span>
                              <a class="dynamic-shipping-rates" data-toggle="popover" data-cart="{{$cart->id}}" data-options="{{ $shipping_options }}" id="shipping-options{{$cart->id}}" title= "{{ trans('theme.shipping') }}">
                                <u>{{ trans('theme.shipping') }}</u>
                              </a>
                              <em id="summary-shipping-name{{$cart->id}}" class="small text-muted"></em>
                            </span>
                            <span>{{ get_formated_currency_symbol() }}
                              <span id="summary-shipping{{$cart->id}}">{{ number_format(0, 2, '.', '') }}</span>
                            </span>
                          </li>
                          @unless(empty(json_decode($packagings)))
                            <li>
                              <span>
                                <a class="packaging-options" data-toggle="popover" data-cart="{{$cart->id}}" data-options="{{$packagings}}" title="{{ trans('theme.packaging') }}">
                                  <u>{{ trans('theme.packaging') }}</u>
                                </a>
                                <em class="small text-muted" id="summary-packaging-name{{$cart->id}}">
                                  {{ $default_packaging ? $default_packaging->name : '' }}
                                </em>
                              </span>
                              <span>{{ get_formated_currency_symbol() }}
                                <span id="summary-packaging{{$cart->id}}">
                                  {{ number_format($default_packaging ? $default_packaging->cost : 0, 2, '.', '') }}
                                </span>
                              </span>
                            </li>
                          @endunless
                        @endunless
                        <li id="discount-section-li{{$cart->id}}" style="display: {{$cart->coupon ? 'block' : 'none'}};">
                          <span>{{ trans('theme.discount') }}
                            <em id="summary-discount-name{{$cart->id}}" class="small text-muted">{{$cart->coupon ? $cart->coupon->name : ''}}</em>
                          </span>
                          <span>-{{ get_formated_currency_symbol() }}
                            <span id="summary-discount{{$cart->id}}">{{$cart->coupon ? number_format($cart->discount, 2, '.', '') : number_format(0, 2, '.', '') }}</span>
                          </span>
                        </li>
                        <li id="tax-section-li{{$cart->id}}" style="display: none;">
                          <span>{{ trans('theme.taxes') }}</span>
                          <span>{{ get_formated_currency_symbol() }}
                            <span id="summary-taxes{{$cart->id}}">{{ number_format(0, 2, '.', '') }}</span>
                          </span>
                        </li>
                        <li>
                          <span>{{ trans('theme.total') }}</span>
                          <span>{{ get_formated_currency_symbol() }}
                            <span id="summary-grand-total{{$cart->id}}">{{ number_format(0, 2, '.', '') }}</span>
                          </span>
                        </li>
                    </ul>
                </div>

                <button class="btn btn-primary btn-block flat pull-right" id="checkout-btn{{$cart->id}}" type="submit"><i class="fa fa-shopping-cart"></i> {{ trans('theme.button.checkout') }}</button>
            </div> <!-- /.col-md-3 -->
          {!! Form::close() !!}
        </div> <!-- /.row -->
      @endforeach

      <a class="btn btn-black flat" href="{{ url('/') }}">{{ trans('theme.button.continue_shopping') }}</a>
    @else
      <div class="clearfix space50"></div>
      <p class="lead text-center space50">
        @lang('theme.empty_cart')
        <a href="{{ url('/') }}" class="btn btn-primary btn-sm flat">@lang('theme.button.shop_now')</a>
      </p>
    @endif
  </div> <!-- /.container -->
</section>