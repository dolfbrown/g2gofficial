@extends('admin.layouts.master')

@section('content')
    <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">{{ trans('app.config') }}</h3>
        </div> <!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">

                    <p class="alert alert-info"><i class="fa fa-info-circle"></i> {!! trans('help.modify_fb_config_file') !!}</p>

                    @unless( config('app.demo') == true )
                    {!! Form::open(['route' => 'admin.setting.saveFBMessengerConfigFile', 'data-toggle' => 'validator']) !!}

                        @if( config('app.demo') == true )
                            <div class="alert alert-warning">
                                {{ trans('messages.demo_restriction') }}
                            </div>
                        @else
                            <div class="form-group">
                                {!! Form::label('file_content', trans('app.file_content')) !!}
                                <textarea class="form-control" name="file_content" rows="17" style="background-color: #2b303b; color: #c0c5ce" placeholder="{{ trans('app.pest_fb_config_content_here') }}">{{ $file_content }}</textarea>
                            </div>
                        @endif

                        {!! Form::submit(trans('app.form.save'), ['class' => 'btn btn-flat btn-new btn-lg pull-right']) !!}
                    {!! Form::close() !!}
                    @endunless
                    <div class="spacer50"></div>
                </div>
            </div>
        </div> <!-- /.box-body -->
    </div> <!-- /.box -->
@endsection