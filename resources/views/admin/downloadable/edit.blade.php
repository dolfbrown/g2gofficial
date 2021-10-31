@extends('admin.layouts.master')

@section('content')
    {!! Form::model($product, ['method' => 'POST', 'route' => ['admin.catalog.downloadable.update', $product->id], 'files' => true, 'id' => 'form-ajax-upload', 'data-toggle' => 'validator']) !!}

	    @include('admin.downloadable._form')

    {!! Form::close() !!}
@endsection

@section('page-script')
    @include('plugins.dropzone-upload')
    @include('plugins.dynamic-inputs')
    @include('plugins.variants')
@endsection