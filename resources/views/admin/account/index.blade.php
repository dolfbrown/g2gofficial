@extends('admin.layouts.master')

@section('content')
	<div class="box">
	    <div class="box-header with-border">
			<h3 class="box-title"><i class="fa fa-user"></i> {{ trans('app.profile') }}</h3>
	    </div>
	    <div class="box-body">
    		@include('admin.account._profile')
    		<span class="spacer20"></span>
		</div>
	</div> <!-- /.box -->
@endsection