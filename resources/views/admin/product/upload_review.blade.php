@extends('admin.layouts.master')

@section('content')
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('app.preview') }}</h3>
            <div class="box-tools pull-right">
                @can('create', App\Product::class)
                    <a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.bulk') }}" class="ajax-modal-btn btn btn-default btn-flat">{{ trans('app.bulk_import') }}</a>
                @endcan
            </div>
        </div> <!-- /.box-header -->

        <div class="box-body">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>{{ trans('app.image') }}</th>
                    <th width="20%">{{ trans('app.title') }}</th>
                    <th>{{ trans('app.condition') }}</th>
                    <th>{{ trans('app.quantity') }}</th>
                    <th>{{ trans('app.price') }}</th>
                    <th>{{ trans('app.variants') }}</th>
                    <th width="14%">{{ trans('app.key_features') }}</th>
                    <th width="22%">{{ trans('app.listing') }}</th>
                    <th>{{ trans('app.seo') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    @continue( ! $row['title'] )

                    @php
                        $slug = $row['slug'] ?: convertToSlugString($row['title'], $row['sku']);
                        $image_links = explode(',', $row['image_links']);
                    @endphp

                    <tr>
                        <td>
                            <img src="{{ count($image_links) ? $image_links[0] : get_placeholder_img('small') }}" class="img-sm">
                        </td>
                        <td>
                            {{ $row['title'] }}<br/>
                            <strong>{{ trans('app.description') }}: </strong> {{ substr($row['description'], 0, 100) }}
                        </td>



                        <td>
                            {{ $row['condition'] }}
                            @if($row['condition_note'])
                                <p class="small"> ({{ $row['condition_note'] }})</p>
                            @endif
                        </td>

                        <td>{{ $row['stock_quantity'] }}</td>

                        <td>
                            @if($row['offer_price'])
                                <dl>
                                    {{ get_formated_currency($row['offer_price']) }}
                                    <strike>{{ get_formated_currency($row['price']) }}</strike>
                                    <p class="small">({{ $row['offer_starts'] . ' - ' . $row['offer_ends']}})</p>
                                </dl>
                            @else
                                {{ get_formated_currency($row['price']) }}
                            @endif
                        </td>

                        <td>
                            @php
                                $variants = array_filter($row, function($key) {
                                    return strpos($key, 'option_name_') === 0;
                                }, ARRAY_FILTER_USE_KEY);
                            @endphp
                            <dl>
                                @foreach($variants as $index => $variant)
                                    @if($row['option_name_'.$loop->iteration] && $row['option_value_'.$loop->iteration])
                                        <dt>{{ $row['option_name_'.$loop->iteration] }}: </dt> <dd>{{ $row['option_value_'.$loop->iteration] }}</dd>
                                    @endif
                                @endforeach
                            </dl>
                        </td>
                        <td>
                            @php
                                $key_features = array_filter($row, function($key) {
                                    return strpos($key, 'key_feature_') === 0;
                                }, ARRAY_FILTER_USE_KEY);
                            @endphp

                            <ul>
                                @foreach($key_features as $key_feature)
                                    @if($key_feature)
                                        <li>{{ $key_feature }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </td>

                        <td>
                            <dl>
                                <dt>{{ trans('app.sku') }}: </dt> <dd>{{ $row['sku'] }}</dd>
                                @if($row['categories'])
                                    <dt>{{ trans('app.categories') }}: </dt> <dd>{{ $row['categories'] }}</dd>
                                @endif
                                <dt>{{ trans('app.gtin') }}: </dt> <dd>{{ $row['gtin_type'] . ' ' . $row['gtin'] }}</dd>
                                @if($row['mpn'])
                                    <dt>{{ trans('app.part_number') }}: </dt> <dd>{{ $row['mpn'] }}</dd>
                                @endif
                                @if($row['manufacturer'])
                                    <dt>{{ trans('app.manufacturer') }}: </dt> <dd>{{ $row['manufacturer'] }}</dd>
                                @endif
                                @if($row['brand'])
                                    <dt>{{ trans('app.brand') }}: </dt> <dd>{{ $row['brand'] }}</dd>
                                @endif

                                @if($row['model_number'])
                                    <dt>{{ trans('app.model_number') }}: </dt> <dd>{{ $row['model_number'] }}</dd>
                                @endif

                                @if($row['origin_country'])
                                    <dt>{{ trans('app.origin') }}: </dt> <dd>{{ $row['origin_country'] }}</dd>
                                @endif

                                <dt>{{ trans('app.has_variant') }}: </dt> <dd>@if(!empty($row['has_variant']))<i class="fa fa-{{ $row['has_variant'] == 'TRUE' ? 'check' : 'times' }} text-muted"></i>@endif</dd>

                                <dt>{{ trans('app.requires_shipping') }}: </dt> <dd><i class="fa fa-{{ $row['requires_shipping'] == 'TRUE' ? 'check' : 'times' }} text-muted"></i></dd>
                                <dt>{{ trans('app.active') }}: </dt> <dd><i class="fa fa-{{ $row['active'] == 'TRUE' ? 'check' : 'times' }} text-muted"></i></dd>
                            </dl>
                        </td>
                        <td>
                            <dl>
                                <dt>{{ trans('app.slug') }}: </dt> <dd>{{ $slug }}</dd>
                                @if($row['tags'])
                                    <dt>{{ trans('app.tags') }}: </dt> <dd>{{ $row['tags'] }}</dd>
                                @endif
                                @if($row['meta_title'])
                                    <dt>{{ trans('app.meta_title') }}: </dt> <dd>{{ $row['meta_title'] }}</dd>
                                @endif
                                @if($row['meta_description'])
                                    <dt>{{ trans('app.meta_description') }}: </dt> <dd>{{ $row['meta_description'] }}</dd>
                                @endif
                            </dl>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div> <!-- /.box-body -->

        <div class="box-footer">
            <a href="{{ route('admin.catalog.product.index') }}" class="btn btn-default btn-flat">{{ trans('app.cancel') }}</a>
            <div class="box-tools pull-right">
                {!! Form::open(['route' => 'admin.catalog.product.import', 'id' => 'form', 'class' => 'inline-form', 'data-toggle' => 'validator']) !!}
                @foreach($rows as $row)
                    @continue( ! $row['title'] )
                    {{ Form::hidden('data[]', serialize($row)) }}
                @endforeach
                {!! Form::button(trans('app.looks_good'), ['type' => 'submit', 'class' => 'confirm btn btn-new btn-flat']) !!}
                {!! Form::close() !!}
            </div>
        </div> <!-- /.box-footer -->
    </div> <!-- /.box -->
@endsection
