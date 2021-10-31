<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      {!! Form::label('name', trans('app.form.topic_name').'*') !!}
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.name'), 'required']) !!}
    </div>
  </div>
</div>
<p class="help-block">* {{ trans('app.form.required_fields') }}</p>