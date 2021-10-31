@extends('admin.layouts.master')

@section('content')
	{!! Form::open(['route' => 'admin.catalog.downloadable.store', 'files' => true, 'id' => 'form-ajax-upload', 'data-toggle' => 'validator']) !!}

	    @include('admin.downloadable._form')

    {!! Form::close() !!}
@endsection


@section('page-script')
    @include('plugins.dropzone-upload')
    @include('plugins.dynamic-inputs')
    @include('plugins.variants')
@endsection