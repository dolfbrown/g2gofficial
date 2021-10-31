@extends('admin.layouts.master')

@section('page-style')
  @include('plugins.ionic')
@endsection

@section('content')
  @if(! Auth::user()->isVerified())
    <div class="alert alert-info alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <strong><i class="icon fa fa-info-circle"></i>{{ trans('app.notice') }}</strong>
      {{ trans('messages.email_verification_notice') }}
        <a href="{{ route('verify') }}">{{ trans('app.resend_varification_link') }}</a>
    </div>
  @endif

  @if(Request::session()->has('new_version'))
    <div class="callout callout-info">
        {!! Form::open(['route' => 'admin.version.update', 'class' => 'form-inline']) !!}
            <strong><i class="icon fa fa-info-circle"></i> {{ trans('app.notice') }}</strong>
            {{ trans('messages.new_version_released') }}
            {!! Form::submit(trans('app.update_now'), ['class' => 'btn btn-flat btn-new btn-xs pull-right confirm']) !!}
        {!! Form::close() !!}
    </div>
  @endif

  <!-- Info boxes -->
  <div class="row">
      <div class="col-md-3 col-sm-6 col-xs-12 nopadding-right">

        <div class="info-box">
          <span class="info-box-icon bg-green">
            <i class="icon ion-md-wallet"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text">{{ trans('app.todays_sale') }}</span>
              <span class="info-box-number">
                {{ get_formated_currency($todays_sale_amount) }}
                <a href="{{ route('admin.order.order.index') }}" class="pull-right small" data-toggle="tooltip" data-placement="left" title="{{ trans('app.detail') }}" >
                  <i class="icon ion-md-send"></i>
                </a>
            </span>

            @php
              $difference = $todays_sale_amount - $yesterdays_sale_amount;
              $todays_sale_percents = $todays_sale_amount == 0 ? 0 : round(($difference / $todays_sale_amount) * 100);
            @endphp
            <div class="progress">
              <div class="progress-bar progress-bar-success" style="width: {{$todays_sale_percents}}%"></div>
            </div>
            <span class="progress-description text-muted">
              @if($todays_sale_amount == 0)
                <i class="icon ion-md-hourglass"></i>
                {{ trans('messages.no_sale', ['date' => trans('app.today')]) }}
              @else
                <i class="icon ion-md-arrow-{{ $difference < 0 ? 'down' : 'up'}}"></i>
                {{ trans('messages.todays_sale_percents', ['percent' => $todays_sale_percents, 'state' => $difference < 0 ? trans('app.down') : trans('app.up')]) }}
              @endif
            </span>
          </div> <!-- /.info-box-content -->
        </div> <!-- /.info-box -->
      </div> <!-- /.col -->

      <div class="col-md-3 col-sm-6 col-xs-12 nopadding-right nopadding-left">
        <div class="info-box">
          <span class="info-box-icon bg-aqua">
            <i class="icon ion-md-cart"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text">{{ trans('app.last_sale') }}</span>
            <span class="info-box-number">
              {{ get_formated_currency($last_sale ? $last_sale->total : 0) }}
              @if($last_sale)
                <a href="{{ route('admin.order.order.show', $last_sale->id) }}" class="pull-right small" data-toggle="tooltip" data-placement="left" title="{{ trans('app.detail') }}" >
                  <i class="icon ion-md-send"></i>
                </a>
              @endif
            </span>
            <div class="progress" style="background: transparent;"></div>
            <span class="progress-description text-muted">
              @if($last_sale)
                <i class="icon ion-md-time"></i> {{ $last_sale->created_at->diffForHumans() }}
              @else
                <i class="icon ion-md-hourglass"></i> {{ trans('messages.no_sale', ['date' => trans('app.yet')]) }}
              @endif
            </span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->

      <!-- fix for small devices only -->
      <div class="clearfix visible-sm-block"></div>

      <div class="col-md-3 col-sm-6 col-xs-12 nopadding-right nopadding-left">
        <div class="info-box">
          <span class="info-box-icon bg-yellow"><i class="icon ion-md-people"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">{{ trans('app.customers') }}</span>
              <span class="info-box-number">
                  {{ $customer_count }}
                  <a href="{{ route('admin.admin.customer.index') }}" class="pull-right small" data-toggle="tooltip" data-placement="left" title="{{ trans('app.detail') }}" >
                    <i class="icon ion-md-send"></i>
                  </a>
              </span>
              <div class="progress" style="background: transparent;"></div>
              <span class="progress-description text-muted">
                  <i class="icon ion-md-add"></i>
                  {{ trans('app.new_in_30_days', ['new' => $new_customer_last_30_days, 'model' => trans('app.customers')]) }}
              </span>
          </div> <!-- /.info-box-content -->
        </div> <!-- /.info-box -->
      </div> <!-- /.col -->

      <div class="col-md-3 col-sm-6 col-xs-12 nopadding-left">

        <div class="info-box">
          <span class="info-box-icon bg-red">
            <i class="icon ion-md-heart"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text">{{ trans('app.visitors_today') }}</span>
              <span class="info-box-number">
                {{ $todays_visitor_count }}
                <a href="{{ route('admin.report.visitors') }}" class="pull-right small" data-toggle="tooltip" data-placement="left" title="{{ trans('app.detail') }}" >
                  <i class="icon ion-md-send"></i>
                </a>
              </span>

              @php
                $last_months = $last_60days_visitor_count - $last_30days_visitor_count;
                $difference = $last_30days_visitor_count - $last_months;
                $last_30_days_percents = $last_months == 0 ? 100 : round(($difference / $last_months) * 100);
              @endphp
              <div class="progress">
                <div class="progress-bar progress-bar-info" style="width: {{$last_30_days_percents}}%"></div>
              </div>
              <span class="progress-description text-muted">
                <i class="icon ion-md-arrow-{{ $difference > 0 ? 'up' : 'down'}}"></i>
                {{ trans('messages.last_30_days_percents', ['percent' => $last_30_days_percents, 'state' => $difference > 0 ? trans('app.increase') : trans('app.decrease')]) }}
              </span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
  </div> <!-- /.row -->

  <div class="row">
      <div class="col-md-3 col-sm-6 col-xs-12 nopadding-right">
        <div class="info-box">
          <span class="info-box-icon bg-yellow">
            <i class="icon ion-md-cube"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text">{{ trans('app.unfulfilled_orders') }}</span>
            <span class="info-box-number">
              {{ $unfulfilled_order_count }}
              <a href="{{ url('admin/order/order?tab=unfulfilled') }}" class="pull-right small" data-toggle="tooltip" data-placement="left" title="{{ trans('app.detail') }}" >
                <i class="icon ion-md-send"></i>
              </a>
            </span>
              @php
                $unfulfilled_percents = $todays_order_count == 0 ?
                    ($unfulfilled_order_count * 100) : round(($unfulfilled_order_count / $todays_order_count) * 100);
              @endphp
              <div class="progress">
                <div class="progress-bar progress-bar-warning" style="width: {{$unfulfilled_percents}}%"></div>
              </div>
              <span class="progress-description text-muted">
                {{ trans('messages.unfulfilled_percents', ['percent' => $unfulfilled_percents]) }}
              </span>
          </div> <!-- /.info-box-content -->
        </div> <!-- /.info-box -->
      </div> <!-- /.col -->

      <div class="col-md-3 col-sm-6 col-xs-12 nopadding-right nopadding-left">
        <div class="info-box bg-yellow">
          <span class="info-box-icon"><i class="icon ion-md-megaphone"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">{{ trans('app.open_disputes') }}</span>
            <span class="info-box-number">
                {{ $dispute_count }}
                <a href="{{ route('admin.support.dispute.index') }}" class="pull-right" data-toggle="tooltip" data-placement="left" title="{{ trans('app.take_action') }}" >
                  <i class="icon ion-md-paper-plane"></i>
                </a>
            </span>

            @php
              $last_months = $last_60days_dispute_count - $last_30days_dispute_count;
              $difference = $last_30days_dispute_count - $last_months;
              $last_30_days_percents = $last_months == 0 ? 100 : round(($difference / $last_months) * 100);
            @endphp
            <div class="progress">
              <div class="progress-bar" style="width: {{$last_30_days_percents}}%"></div>
            </div>
            <span class="progress-description">
                <i class="icon ion-md-arrow-{{ $difference > 0 ? 'up' : 'down'}}"></i>
                {{ trans('messages.last_30_days_percents', ['percent' => $last_30_days_percents, 'state' => $difference > 0 ? trans('app.increase') : trans('app.decrease')]) }}
            </span>
          </div> <!-- /.info-box-content -->
        </div>
      </div> <!-- /.col -->

      <!-- fix for small devices only -->
      <div class="clearfix visible-sm-block"></div>

      <div class="col-md-3 col-sm-6 col-xs-12 nopadding-right nopadding-left">
        <div class="info-box bg-aqua">
          <span class="info-box-icon"><i class="icon ion-md-nuclear"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">{{ trans('app.refund_requests') }}</span>
            <span class="info-box-number">
                {{ $refund_request_count }}
                <a href="{{ route('admin.support.refund.index') }}" class="pull-right" data-toggle="tooltip" data-placement="left" title="{{ trans('app.take_action') }}" >
                  <i class="icon ion-md-paper-plane"></i>
                </a>
            </span>

            @php
              $last_months = $last_60days_refund_request_count - $last_30days_refund_request_count;
              $difference = $last_30days_refund_request_count - $last_months;
              $last_30_days_percents = $last_months == 0 ? 100 : round(($difference / $last_months) * 100);
            @endphp
            <div class="progress">
              <div class="progress-bar" style="width: {{$last_30_days_percents}}%"></div>
            </div>
            <span class="progress-description">
              <i class="icon ion-md-arrow-{{ $difference > 0 ? 'up' : 'down'}}"></i>
              {{ trans('messages.last_30_days_percents', ['percent' => $last_30_days_percents, 'state' => $difference > 0 ? trans('app.increase') : trans('app.decrease')]) }}
            </span>
          </div> <!-- /.info-box-content -->
        </div>
      </div>
      <!-- /.col -->

      <div class="col-md-3 col-sm-6 col-xs-12 nopadding-left">
        <div class="info-box">
          <span class="info-box-icon bg-red">
            <i class="icon ion-md-notifications-outline"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text">{{ trans('app.stock_outs') }}</span>
            <span class="info-box-number">
              {{ $stock_out_count }}
              <a href="{{ url('admin/stock/inventory?tab=out_of_stock') }}" class="pull-right small" data-toggle="tooltip" data-placement="left" title="{{ trans('app.detail') }}" >
                <i class="icon ion-md-send"></i>
              </a>
            </span>

            @php
              $stock_out_percents = $stock_count == 0 ?
                  ($stock_out_count * 100) : round(($stock_out_count / $stock_count) * 100);
            @endphp
            <div class="progress">
              <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
            </div>
            <span class="progress-description text-muted">
            {{ trans('messages.stock_out_percents', ['percent' => $stock_out_percents, 'total' => $stock_count]) }}
            </span>
          </div> <!-- /.info-box-content -->
        </div> <!-- /.info-box -->
      </div> <!-- /.col -->
  </div> <!-- /.row -->
  <!-- End Info boxes -->

  <div class="row">
    <div class="col-md-8 col-sm-7 col-xs-12">
      <div class="box">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs nav-justified">
            <li class="active"><a href="#orders_tab" data-toggle="tab">
              <i class="fa fa-shopping-cart hidden-sm"></i>
              {{ trans('app.latest_orders') }}
            </a></li>
            <li><a href="#latest_product_tab" data-toggle="tab">
              <i class="fa fa-cubes hidden-sm"></i>
              {{ trans('app.recently_added_products') }}
            </a></li>
            <li><a href="#low_stock_tab" data-toggle="tab">
              <i class="fa fa-cube hidden-sm"></i>
              {{ trans('app.low_stock_items') }}
            </a></li>
          </ul>
          <!-- /.nav .nav-tabs -->

          <div class="tab-content">
            <div class="tab-pane active" id="orders_tab">
              <div class="box-body nopadding">
                <div class="table-responsive">
                  <table class="table no-margin table-condensed">
                    <thead>
                      <tr>
                        <th>{{ trans('app.order_number') }}</th>
                        <th>{{ trans('app.order_date') }}</th>
                        <th>{{ trans('app.customer') }}</th>
                        <th>{{ trans('app.grand_total') }}</th>
                        <th>{{ trans('app.payment') }}</th>
                        <th>{{ trans('app.status') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($latest_orders as $order)
                        <tr>
                          <td>
                            @can('view', $order)
                              <a href="{{ route('admin.order.order.show', $order->id) }}">
                                {{ $order->order_number }}
                              </a>
                            @else
                              {{ $order->order_number }}
                            @endcan
                            @if($order->disputed)
                              <span class="label label-danger indent5">{{ trans('app.statuses.disputed') }}</span>
                            @endif
                          </td>
                              <td>{{ $order->created_at->diffForHumans() }}</td>
                          <td>{{ optional($order->customer)->name }}</td>
                          <td>{{ get_formated_currency($order->grand_total )}}</td>
                          <td>{!! $order->paymentStatusName() !!}</td>
                          <td>
                            <span class="label label-outline" style="background-color: {{ optional($order->status)->label_color }}">
                              {{ $order->status ? strToupper(optional($order->status)->name) : trans('app.statuses.new') }}
                            </span>
                          </td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="6">{{ trans('app.no_data_found') }}</td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div> <!-- /.table-responsive -->
              </div> <!-- /.box-body -->
              <div class="box-footer clearfix">
                @can('create', App\Order::class)
                  <a href="javascript:void(0)" data-link="{{ route('admin.order.order.searchCutomer') }}" class="ajax-modal-btn btn btn-new btn-flat pull-left">
                    <i class="icon ion-md-cart"></i> {{ trans('app.add_order') }}
                  </a>
                @endcan
                @can('index', App\Order::class)
                  <a href="{{ route('admin.order.order.index') }}" class="btn btn-default btn-flat pull-right">
                    <i class="icon ion-md-gift"></i> {{ trans('app.all_orders') }}
                  </a>
                @endcan
              </div> <!-- /.box-footer -->
            </div> <!-- /.tab-pane -->

            <div class="tab-pane" id="latest_product_tab">
              <div class="box-body nopadding">
                <div class="table-responsive">
                  <table class="table no-margin table-condensed">
                      <thead>
                        <tr>
                          <th>{{ trans('app.image') }}</th>
                          <th>{{ trans('app.sku') }}</th>
                          <th width="50%">{{ trans('app.title') }}</th>
                          <th>{{ trans('app.price') }} <small>( {{ trans('app.excl_tax') }} )</small> </th>
                          <th>{{ trans('app.quantity') }}</th>
                          <th width="20px">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($latest_products as $product)
                            <tr>
                              <td>
                                <img src="{{ get_product_img_src($product, 'tiny') }}" class="img-circle img-sm" alt="{{ trans('app.featured_image') }}">
                              </td>
                              <td>{{ $product->sku }}</td>
                              <td>
                                <p>
                                  <a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.show', $product->id) }}"  class="ajax-modal-btn">
                                    {{ $product->title }}
                                  </a>
                                  @unless($product->active)
                                    <span class="label label-default indent10">{{ trans('app.inactive') }}</span>
                                  @endunless
                                </p>
                              </td>
                              <td>
                                @if(($product->offer_price > 0) && ($product->offer_end > \Carbon\Carbon::now()))
                                  <?php $offer_price_help =
                                      trans('help.offer_starting_time') . ': ' .
                                      $product->offer_start->diffForHumans() . ' and ' .
                                      trans('help.offer_ending_time') . ': ' .
                                      $product->offer_end->diffForHumans(); ?>

                                  <small class="text-muted">{{ $product->price }}</small><br/>
                                  {{ get_formated_currency($product->offer_price) }}

                                  <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ $offer_price_help }}"><sup><i class="fa fa-question"></i></sup></small>
                                @else
                                  {{ get_formated_currency($product->price) }}
                                @endif
                              </td>
                              <td class="text-center">{{ ($product->stock_quantity > 0) ? $product->stock_quantity : trans('app.out_of_stock') }}</td>
                              <td>
                                @can('update', $product)
                                  <a href="{{ route('admin.catalog.product.edit', $product->id) }}"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}" class="fa fa-edit"></i></a>
                                @endcan
                              </td>
                            </tr>
                        @empty
                          <tr>
                            <td colspan="3">{{ trans('app.no_data_found') }}</td>
                          </tr>
                        @endforelse
                      </tbody>
                  </table>
                </div> <!-- /.table-responsive -->
              </div> <!-- /.box-body -->
              <div class="box-footer clearfix">
                @can('index', App\Product::class)
                  <a href="{{ route('admin.catalog.product.index') }}" class="btn btn-default btn-flat pull-right">
                    <i class="icon ion-md-cube"></i> {{ trans('app.products') }}
                  </a>
                @endcan
              </div>
            </div> <!-- /.tab-pane -->

            <div class="tab-pane" id="low_stock_tab">
              <div class="box-body nopadding">
                <div class="table-responsive">
                  <table class="table no-margin table-condensed">
                    <thead>
                      <tr>
                        <th>{{ trans('app.image') }}</th>
                        <th>{{ trans('app.sku') }}</th>
                        <th width="50%">{{ trans('app.title') }}</th>
                        <th>{{ trans('app.quantity') }}</th>
                        <th width="20px">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($low_qtt_stocks as $item)
                        <tr>
                          <td>
                            <img src="{{ get_product_img_src($item, 'tiny') }}" class="img-sm" alt="{{ trans('app.image') }}">
                          </td>
                          <td>{{ $item->sku }}</td>
                          <td>
                                <p>
                                  <a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.show', $item->id) }}"  class="ajax-modal-btn">
                                    {{ $item->title }}
                                  </a>
                                  @unless($item->active)
                                    <span class="label label-default indent10">{{ trans('app.inactive') }}</span>
                                  @endunless
                                </p>
                          </td>
                          <td class="qtt-{{$item->id}}">{{ ($item->stock_quantity > 0) ? $item->stock_quantity : trans('app.out_of_stock') }}</td>
                          <td class="row-options">
                            @can('update', $item)
                              <a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.editQtt', $item->id) }}" class="ajax-modal-btn"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.update') }}" class="icon ion-md-add-circle"></i></a>
                            @endcan
                          </td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="6">{{ trans('app.no_data_found') }}</td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div> <!-- /.table-responsive -->
                </div> <!-- /.box-body -->
                <div class="box-footer clearfix">
                  @can('index', App\Product::class)
                    <a href="{{ route('admin.catalog.product.index') }}" class="btn btn-default btn-flat pull-right">
                      <i class="icon ion-md-cube"></i> {{ trans('app.products') }}
                    </a>
                  @endcan
                </div> <!-- /.box-footer -->
            </div> <!-- /.tab-pane -->
          </div> <!-- /.tab-content -->
        </div> <!-- /.nav-tabs-custom -->
      </div> <!-- /.box -->

      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-users"></i> {{ trans('app.visitors_graph') }}</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
          </div>
        </div> <!-- /.box-header -->
        <div class="box-body">
          <div>{!! $visitorsChart->container() !!}</div>
        </div> <!-- /.box-body -->
      </div> <!-- /.box -->
    </div>  <!-- /.col-*-* -->

    <div class="col-md-4 col-sm-5 col-xs-12 nopadding-left">
      @if($appealed_dispute_count > 0)
        <div class="info-box bg-red">
          <span class="info-box-icon"><i class="icon ion-md-megaphone"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">{{ trans('app.appealed_disputes') }}</span>
            <span class="info-box-number">
                {{ $appealed_dispute_count }}
                <a href="{{ route('admin.support.dispute.index') }}" class="pull-right" data-toggle="tooltip" data-placement="left" title="{{ trans('app.take_action') }}" >
                  <i class="icon ion-md-paper-plane"></i>
                </a>
            </span>

            @php
              $last_months = $last_60days_appealed_dispute_count - $last_30days_appealed_dispute_count;
              $difference = $last_30days_appealed_dispute_count - $last_months;
              $last_30_days_appealed_percents = $last_months == 0 ? 100 : round(($difference / $last_months) * 100);
            @endphp
            <div class="progress">
              <div class="progress-bar" style="width: {{$last_30_days_appealed_percents}}%"></div>
            </div>

            <span class="progress-description">
                <i class="icon ion-md-arrow-{{ $difference > 0 ? 'up' : 'down'}}"></i>
                {{ trans('messages.last_30_days_percents', ['percent' => $last_30_days_appealed_percents, 'state' => $difference > 0 ? trans('app.increase') : trans('app.decrease')]) }}
            </span>
          </div>
          <!-- /.info-box-content -->
        </div>
      @endif

        <div class="box box-solid">
          <div class="box-header with-border">
              <h3 class="box-title text-warning">
                <i class="icon ion-md-clock"></i> {{ trans('app.latest_days', ['days' => config('charts.latest_sales.days', 15)]) }}
              </h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
          </div> <!-- /.box-header -->
          <div class="box-body">
            <p class="text-muted"><span class="lead"> {{ trans('app.total') }}: {{ get_formated_currency($latest_sale_total) }} </span><span class="pull-right">{{ $latest_order_count . ' ' . trans('app.orders') }}</span></p>
          <div>{!! $salesChart->container() !!}</div>

          <table class="table table-default">
            <thead>
              <tr>
                <td><span class="info-box-text">{{ trans('app.breakdown') }}:</span></td>
                <td>&nbsp;</td>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>{{ trans('app.orders') }}</td>
                <td class="pull-right">{{ get_formated_currency($latest_sale_total) }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.refunds') }}</td>
                <td class="pull-right">-{{ get_formated_currency($latest_refund_total) }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.total') }}</td>
                <td class="pull-right">{{ get_formated_currency($latest_sale_total - $latest_refund_total) }}</td>
              </tr>
            </tbody>
          </table>
        </div> <!-- /.box-body -->
      </div><!-- /.box -->

      <div class="box">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs nav-justified">
            <li class="active"><a href="#top_customer_tab" data-toggle="tab">
              <i class="icon ion-md-people hidden-sm"></i>
              {{ trans('app.top_customers') }}
            </a></li>
            <li><a href="#top_item_tab" data-toggle="tab">
              <i class="icon ion-md-rocket hidden-sm"></i>
              {{ trans('app.top_selling_items') }}
            </a></li>
          </ul>
          <!-- /.nav .nav-tabs -->

          <div class="tab-content nopadding">
            <div class="tab-pane active" id="top_customer_tab">
              <div class="box-body">
                <div class="table-responsive">
                  <table class="table no-margin table-condensed">
                      <thead>
                        <tr class="text-muted">
                          <th>{{ trans('app.name') }}</th>
                          <th><i class="icon ion-md-cart"></i></th>
                          <th>{{ trans('app.revenue') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($top_customers as $customer)
                          <tr>
                            <td>
                              @if($customer->image)
                                <img src="{{ get_storage_file_url(optional($customer->image)->path, 'tiny') }}" class="img-circle" alt="{{ trans('app.avatar') }}">
                              @else
                                <img src="{{ get_gravatar_url($customer->email, 'tiny') }}" class="img-circle" alt="{{ trans('app.avatar') }}">
                              @endif
                              <p class="indent5">
                                @can('view', $customer)
                                    <a href="javascript:void(0)" data-link="{{ route('admin.admin.customer.show', $customer->id) }}" class="ajax-modal-btn modal-btn">{{ $customer->getName() }}</a>
                                @else
                                  {{ $customer->getName() }}
                                @endcan
                              </p>
                            </td>
                            <td>
                              <span class="label label-outline">{{ $customer->orders_count }}</span>
                            </td>
                            <td>{{ get_formated_currency(round($customer->orders->sum('total'))) }}</td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="3">{{ trans('app.no_data_found') }}</td>
                          </tr>
                        @endforelse
                      </tbody>
                  </table>
                </div>
                <!-- /.table-responsive -->
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.tab-pane -->

            <div class="tab-pane" id="top_item_tab">
              <div class="box-body">
                <div class="table-responsive">
                  <table class="table no-margin table-condensed">
                      <thead>
                          <th width="60px">&nbsp;</th>
                          <th>{{ trans('app.product') }}</th>
                          <th width="8%">{{ trans('app.sold') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($top_listings as $item)
                          <tr>
                            <td>
                              <img src="{{ get_product_img_src($item, 'small') }}" class="img-md" alt="{{ trans('app.image') }}">
                            </td>
                            <td>
                            <h5 class="nopadding">
                              <small>{{ trans('app.sku') . ': ' }}</small>
                                @can('view', $item)
                                    <a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.show', $item->id) }}" class="ajax-modal-btn modal-btn">{{ $item->sku }}</a>
                                @else
                                  {{ $item->sku }}
                                @endcan
                                </h5>

                                <span class="text-muted">
                                  {{ $item->title }}
                                </span>
                            </td>
                            <td>{{ trans('app.sold_units', ['units' => $item->sold_qtt]) }}</td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="3">{{ trans('app.no_data_found') }}</td>
                          </tr>
                        @endforelse
                      </tbody>
                  </table>
                </div>
                <!-- /.table-responsive -->
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.tab-pane -->
          </div>
          <!-- /.tab-content -->
        </div>
        <!-- /.nav-tabs-custom -->
      </div> <!-- /.box -->
    </div> <!-- /.col-*-* -->
  </div>
@endsection

@section('page-script')
  @include('plugins.chart')
  <script src="https://code.highcharts.com/modules/exporting.js"></script>
  <script src="https://code.highcharts.com/modules/export-data.js"></script>

  {!! $salesChart->script() !!}
  {!! $visitorsChart->script() !!}
@endsection
