@extends('admin.layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-8 nopadding-right">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('app.new_variant') }}</h3>
                </div> <!-- /.box-header -->
                <div class="box-body">
                    {!! Form::open(['route' => ['admin.catalog.product.saveVariant', $product], 'files' => true, 'id' => 'form', 'data-toggle' => 'validator']) !!}

                        @include('admin.product._variant_form')

                        <p class="help-block">* {{ trans('app.form.required_fields') }}</p>

                        <div class="box-tools pull-right">
                          {!! Form::submit( isset($product) ? trans('app.form.update') : trans('app.form.save'), ['class' => 'btn btn-flat btn-lg btn-primary']) !!}
                        </div>

                    {!! Form::close() !!}
                </div> <!-- /.box-body -->
            </div> <!-- /.box -->
        </div><!-- /.col-md-8 -->

        <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('app.product') }}</h3>
                    <div class="box-tools pull-right">
                        <a href="{{ route('admin.catalog.product.edit', $product) }}" class="btn btn-default">
                            <i class="fa fa-angle-double-left"></i> {{ trans('app.back_to_product') }}
                        </a>
                    </div>
                </div> <!-- /.box-header -->
                <div class="box-body">

                    @include('admin.partials._product_widget', ['product' => $product])

                    <fieldset>
                        <legend>{{ trans('app.variants') }}</legend>

                        <table class="table table-default">
                            @foreach($product->variants as $variant)
                                <tr>
                                    <td>
                                        @if($variant->image)
                                            <img src="{{ get_storage_file_url(optional($variant->image)->path, 'mini') }}" class="img-md" alt="{{ $variant->title }}">
                                        @else
                                            <img src="{{ url("images/placeholders/no_img.png") }}" class="img-md" alt="{{ $variant->title }}">
                                        @endif
                                    </td>

                                    <td>
                                        @foreach($variant->attributeValues as $attrVal)

                                            {{ $attrVal->value }}

                                            @unless($loop->last)
                                              <span class="text-primary"> &#8226; </span>
                                            @endunless

                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </fieldset>
                </div> <!-- /.box-body -->
            </div> <!-- /.box -->
        </div><!-- /.col-md-4 -->

    </div><!-- /.row -->
@endsection