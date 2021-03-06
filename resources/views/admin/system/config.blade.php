@extends('admin.layouts.master')

@php
	$can_update = Gate::allows('update', $system) ?: Null;
@endphp

@section('content')
	<div class="box">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs nav-justified">
				<li class="active"><a href="#basic_settings_tab" data-toggle="tab">
					<i class="fa fa-cubes hidden-sm"></i>
					{{ trans('app.basic_settings') }}
				</a></li>
				<li><a href="#formats_tab" data-toggle="tab">
					<i class="fa fa-cog hidden-sm"></i>
					{{ trans('app.config_formats') }}
				</a></li>
				<li><a href="#support_tab" data-toggle="tab">
					<i class="fa fa-phone hidden-sm"></i>
					{{ trans('app.support') }}
				</a></li>
				<li><a href="#reports_tab" data-toggle="tab">
					<i class="fa fa-line-chart hidden-sm"></i>
					{{ trans('app.reports') }}
				</a></li>
				<li><a href="#notifications_tab" data-toggle="tab">
					<i class="fa fa-bell-o hidden-sm"></i>
					{{ trans('app.notifications') }}
				</a></li>
			</ul>
			<div class="tab-content">
			    <div class="tab-pane active" id="basic_settings_tab">
			    	<div class="row">
				        {!! Form::model($system, ['method' => 'PUT', 'route' => ['admin.setting.system.update'], 'files' => true, 'id' => 'form2', 'class' => 'form-horizontal ajax-form', 'data-toggle' => 'validator']) !!}
					    	<div class="col-sm-6">
					    		<fieldset>
					    			<legend>{{ trans('app.units') }}</legend>
									<div class="form-group">
								        {!! Form::label('weight_unit', '*' . trans('app.weight_unit'). ':', ['class' => 'with-help col-sm-5 control-label']) !!}
									  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.system_weight_unit') }}"></i>
									  	<div class="col-sm-6 nopadding-left">
									  		@if($can_update)
											    {!! Form::select('weight_unit', ['g' => 'Gram(g)', 'kg' => 'Kilogram(kg)', 'lb' => 'Pound(lb)', 'oz' => 'Ounce(oz)'], $system->weight_unit, ['class' => 'form-control select2-normal', 'required']) !!}
										      	<div class="help-block with-errors"></div>
											@else
												<span>{{ $system->weight_unit }}</span>
											@endif
									  	</div>
									</div>

									<div class="form-group">
								        {!! Form::label('length_unit', '*' . trans('app.length_unit'). ':', ['class' => 'with-help col-sm-5 control-label']) !!}
									  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.system_length_unit') }}"></i>
									  	<div class="col-sm-6 nopadding-left">
									  		@if($can_update)
											    {!! Form::select('length_unit', ['meter' => 'Meter(M)', 'cm' => 'Centemeter(cm)', 'in' => 'Inch(in)'], $system->length_unit, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.select'), 'required']) !!}
										      	<div class="help-block with-errors"></div>
											@else
												<span>{{ $system->length_unit }}</span>
											@endif
									  	</div>
									</div>

									<div class="form-group">
								        {!! Form::label('valume_unit', '*' . trans('app.valume_unit'). ':', ['class' => 'with-help col-sm-5 control-label']) !!}
									  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.system_valume_unit') }}"></i>
									  	<div class="col-sm-6 nopadding-left">
									  		@if($can_update)
											    {!! Form::select('valume_unit', ['liter' => 'Liter(L)', 'gal' => 'gallon(gal)'], $system->valume_unit, ['class' => 'form-control select2-normal', 'required']) !!}
										      	<div class="help-block with-errors"></div>
											@else
												<span>{{ $system->valume_unit }}</span>
											@endif
									  	</div>
									</div>
					    		</fieldset>

					    		<fieldset>
					    			<legend>{{ trans('app.inventories') }}</legend>
									<div class="form-group">
								        {!! Form::label('alert_quantity', trans('app.alert_quantity'). ':', ['class' => 'with-help col-sm-4 control-label']) !!}
									  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.config_alert_quantity') }}"></i>
									  	<div class="col-sm-2 nopadding-left">
									  		@if($can_update)
									        	{!! Form::number('alert_quantity', get_formated_decimal($system->alert_quantity), ['class' => 'form-control', 'placeholder' => trans('app.placeholder.alert_quantity')]) !!}
											@else
												<span>{{ get_formated_decimal($system->alert_quantity) }}</span>
											@endif
									  	</div>
									</div>

					    		</fieldset>

					    		<fieldset>
					    			<legend>{{ trans('app.config_customer_section') }}</legend>
							    	<div class="row">
								    	<div class="col-sm-7 text-right">
											<div class="form-group">
										        {!! Form::label('ask_customer_for_email_subscription', trans('app.ask_customer_for_email_subscription'). ':', ['class' => 'with-help control-label']) !!}
											  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.ask_customer_for_email_subscription') }}"></i>
											</div>
										</div>
								    	<div class="col-sm-4">
									  		@if($can_update)
											  	<div class="handle horizontal text-center">
													<a href="{{ route('admin.setting.system.notification.toggle', 'ask_customer_for_email_subscription') }}" type="button" class="btn btn-md btn-secondary btn-toggle {{ $system->ask_customer_for_email_subscription == 1 ? 'active' : '' }}" data-toggle="button" aria-pressed="{{ $system->ask_customer_for_email_subscription == 1 ? 'true' : 'false' }}" autocomplete="off">
														<div class="btn-handle"></div>
													</a>
											  	</div>
											@else
												<span>{{ $system->ask_customer_for_email_subscription == 1 ? trans('app.on') : trans('app.off') }}</span>
											@endif
										</div>
								  	</div>
								    <!-- /.row -->

							    	<div class="row">
								    	<div class="col-sm-7 text-right">
											<div class="form-group">
										        {!! Form::label('allow_guest_checkout', trans('app.allow_guest_checkout'). ':', ['class' => 'with-help control-label']) !!}
											  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.allow_guest_checkout') }}"></i>
											</div>
										</div>
								    	<div class="col-sm-4">
									  		@if($can_update)
											  	<div class="handle horizontal text-center">
													<a href="{{ route('admin.setting.system.notification.toggle', 'allow_guest_checkout') }}" type="button" class="btn btn-md btn-secondary btn-toggle {{ $system->allow_guest_checkout == 1 ? 'active' : '' }}" data-toggle="button" aria-pressed="{{ $system->allow_guest_checkout == 1 ? 'true' : 'false' }}" autocomplete="off">
														<div class="btn-handle"></div>
													</a>
											  	</div>
											@else
												<span>{{ $system->allow_guest_checkout == 1 ? trans('app.on') : trans('app.off') }}</span>
											@endif
										</div>
								  	</div> <!-- /.row -->
								</fieldset>
					    	</div>

					    	<div class="col-sm-6">
					    		<fieldset>
					    			<legend><i class="fa fa-laptop hidden-sm"></i> {{ trans('app.orders') }}</legend>
									<div class="form-group">
										{!! Form::label('order_number_prefix', trans('app.order_number_prefix') . ':', ['class' => 'with-help col-sm-4 control-label']) !!}
								        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.order_number_prefix_suffix') }}"></i>
									  	<div class="col-sm-2 nopadding-left">
									  		@if($can_update)
									  			{!! Form::text('order_number_prefix', $system->order_number_prefix, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.order_number_prefix')]) !!}
											@else
												<span>{{ $system->order_number_prefix }}</span>
											@endif
									  	</div>

										{!! Form::label('order_number_suffix', trans('app.and') . ' ' . trans('app.suffix') . ':', ['class' => 'with-help col-sm-3 control-label']) !!}
									  	<div class="col-sm-2 nopadding-left">
									  		@if($can_update)
									  			{!! Form::text('order_number_suffix', $system->order_number_suffix, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.order_number_suffix')]) !!}
											@else
												<span>{{ $system->order_number_suffix }}</span>
											@endif
									  	</div>
									</div>

									<div class="form-group">
								        {!! Form::label('order_handling_cost', trans('app.order_handling_cost'). ':', ['class' => 'with-help col-sm-4 control-label']) !!}
									  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.config_order_handling_cost') }}"></i>
									  	<div class="col-sm-7 nopadding-left">
									  		@if($can_update)
											    <div class="input-group">
										        	{!! Form::number('order_handling_cost', get_formated_decimal($system->order_handling_cost), ['class' => 'form-control', 'placeholder' => trans('app.placeholder.order_handling_cost')]) !!}
											        <span class="input-group-addon">{{ config('system_settings.currency_symbol') ?: '$' }}</span>
												</div>
											@else
												<span>{{ get_formated_decimal($system->order_handling_cost) }}</span>
											@endif
									  	</div>
									</div>
								</fieldset>

					    		<fieldset>
					    			<legend>{{ trans('app.after_fulfilled') }}</legend>
							    	<div class="row">
								    	<div class="col-sm-6 text-right">
											<div class="form-group">
										        {!! Form::label('auto_archive_order', trans('app.auto_archive_order'). ':', ['class' => 'with-help control-label']) !!}
											  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.config_auto_archive_order') }}"></i>
											</div>
										</div>
								    	<div class="col-sm-6">
									  		@if($can_update)
											  	<div class="handle horizontal">
													<a href="{{ route('admin.setting.system.notification.toggle', 'auto_archive_order') }}" type="button" class="btn btn-md btn-secondary btn-toggle {{ $system->auto_archive_order == 1 ? 'active' : '' }}" data-toggle="button" aria-pressed="{{ $system->auto_archive_order == 1 ? 'true' : 'false' }}" autocomplete="off">
														<div class="btn-handle"></div>
													</a>
											  	</div>
											@else
												<span>{{ $system->auto_archive_order == 1 ? trans('app.on') : trans('app.off') }}</span>
											@endif
										</div>
								  	</div> <!-- /.row -->
								</fieldset>

					    		<fieldset>
					    			<legend><i class="fa fa-laptop hidden-sm"></i> {{ trans('app.views') }}</legend>
									<div class="form-group">
								        {!! Form::label('pagination', trans('app.pagination'). ':', ['class' => 'with-help col-sm-6 control-label']) !!}
									  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.system_pagination') }}"></i>
									  	<div class="col-sm-5 nopadding-left">
									  		@if($can_update)
									    	    {!! Form::number('pagination', $system->pagination, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.pagination')]) !!}
										      	<div class="help-block with-errors"></div>
											@else
												<span>{{ $system->pagination }}</span>
											@endif
									  	</div>
									</div>

									<div class="form-group">
								        {!! Form::label('max_img_size_limit_kb', trans('app.max_img_size_limit_kb'). ':', ['class' => 'with-help col-sm-6 control-label']) !!}
									  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.config_max_img_size_limit_kb') }}"></i>
									  	<div class="col-sm-5 nopadding-left">
									  		@if($can_update)
									    	    {!! Form::number('max_img_size_limit_kb', $system->max_img_size_limit_kb, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.max_img_size_limit_kb')]) !!}
										      	<div class="help-block with-errors"></div>
											@else
												<span>{{ $system->max_img_size_limit_kb }}</span>
											@endif
									  	</div>
									</div>

									<div class="form-group">
								        {!! Form::label('max_number_of_inventory_imgs', trans('app.max_number_of_inventory_imgs'). ':', ['class' => 'with-help col-sm-6 control-label']) !!}
									  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.config_max_number_of_inventory_imgs') }}"></i>
									  	<div class="col-sm-5 nopadding-left">
									  		@if($can_update)
									    	    {!! Form::number('max_number_of_inventory_imgs', $system->max_number_of_inventory_imgs, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.max_number_of_inventory_imgs')]) !!}
										      	<div class="help-block with-errors"></div>
											@else
												<span>{{ $system->max_number_of_inventory_imgs }}</span>
											@endif
									  	</div>
									</div>
					    		</fieldset>
					    	</div>

					  		@if($can_update)
								<div class="col-sm-12">
									<p class="help-block">* {{ trans('app.form.required_fields') }}</p>
						            {!! Form::submit(trans('app.update'), ['class' => 'btn btn-lg btn-flat btn-new col-sm-offset-2']) !!}
						        </div>
					  		@endif
				        {!! Form::close() !!}
			    	</div>
			    </div>
			  	<!-- /.tab-pane -->

			    <div class="tab-pane" id="formats_tab">
			    	<div class="row">
				        {!! Form::model($system, ['method' => 'PUT', 'route' => ['admin.setting.system.update'], 'files' => true, 'id' => 'form2', 'class' => 'form-horizontal ajax-form', 'data-toggle' => 'validator']) !!}
					    	<div class="col-sm-6">
					    		{{-- <fieldset>
					    			<legend>{{ trans('app.config_date_and_time') }}</legend>
									<div class="form-group">
								        {!! Form::label('date_format', '*' . trans('app.date_format'). ':', ['class' => 'with-help col-sm-7 control-label']) !!}
									  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.system_date_format') }}"></i>
									  	<div class="col-sm-4 nopadding-left">
									  		@if($can_update)
											    {!! Form::select('date_format', ['YYYY-MM-DD' => 'YYYY-MM-DD', 'DD-MM-YYYY' => 'DD-MM-YYYY', 'MM-DD-YYYY' => 'MM-DD-YYYY'], $system->date_format, ['class' => 'form-control select2-normal', 'required']) !!}
										      	<div class="help-block with-errors"></div>
											@else
												<span>{{ $system->date_format }}</span>
											@endif
									  	</div>
									</div>

									<div class="form-group">
								        {!! Form::label('date_separator', '*' . trans('app.date_separator'). ':', ['class' => 'with-help col-sm-7 control-label']) !!}
									  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.config_date_separator') }}"></i>
									  	<div class="col-sm-4 nopadding-left">
									  		@if($can_update)
											    {!! Form::select('date_separator', ['.' => '.', '-' => '-', '/' => '/'], $system->date_separator, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.select'), 'required']) !!}
										      	<div class="help-block with-errors"></div>
											@else
												<span>{{ $system->date_separator }}</span>
											@endif
									  	</div>
									</div>

									<div class="form-group">
								        {!! Form::label('time_format', '*' . trans('app.time_format'). ':', ['class' => 'with-help col-sm-7 control-label']) !!}
									  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.system_time_format') }}"></i>
									  	<div class="col-sm-4 nopadding-left">
									  		@if($can_update)
											    {!! Form::select('time_format', ['12h' => '12h', '24h' => '24h'], $system->time_format, ['class' => 'form-control select2-normal', 'required']) !!}
										      	<div class="help-block with-errors"></div>
											@else
												<span>{{ $system->time_format }}</span>
											@endif
									  	</div>
									</div>

									<div class="form-group">
								        {!! Form::label('time_separator', '*' . trans('app.time_separator'). ':', ['class' => 'with-help col-sm-7 control-label']) !!}
									  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.config_time_separator') }}"></i>
									  	<div class="col-sm-4 nopadding-left">
									  		@if($can_update)
											    {!! Form::select('time_separator', ['.' => '.', ':' => ':'], $system->time_separator, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.select'), 'required']) !!}
										      	<div class="help-block with-errors"></div>
											@else
												<span>{{ $system->time_separator }}</span>
											@endif
									  	</div>
									</div>
					    		</fieldset> --}}

					    		<fieldset>
					    			<legend>{{ trans('app.config_currency') }}</legend>
									<div class="form-group">
								        {!! Form::label('decimals', '*' . trans('app.decimals'). ':', ['class' => 'with-help col-sm-7 control-label']) !!}
									  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.config_decimals') }}"></i>
									  	<div class="col-sm-4 nopadding-left">
									  		@if($can_update)
											    {!! Form::select('decimals', ['2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'], $system->decimals, ['class' => 'form-control select2-normal', 'required']) !!}
										      	<div class="help-block with-errors"></div>
											@else
												<span>{{ $system->decimals }}</span>
											@endif
									  	</div>
									</div>

							    	<div class="row">
								    	<div class="col-sm-7 text-right">
											<div class="form-group">
										        {!! Form::label('show_currency_symbol', trans('app.show_currency_symbol'). ':', ['class' => 'with-help control-label']) !!}
											  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.config_show_currency_symbol') }}"></i>
											</div>
										</div>
								    	<div class="col-sm-4">
									  		@if($can_update)
											  	<div class="handle horizontal text-center">
													<a href="{{ route('admin.setting.system.notification.toggle', 'show_currency_symbol') }}" type="button" class="btn btn-md btn-secondary btn-toggle {{ $system->show_currency_symbol == 1 ? 'active' : '' }}" data-toggle="button" aria-pressed="{{ $system->show_currency_symbol == 1 ? 'true' : 'false' }}" autocomplete="off">
														<div class="btn-handle"></div>
													</a>
											  	</div>
											@else
												<span>{{ $system->show_currency_symbol == 1 ? trans('app.on') : trans('app.off') }}</span>
											@endif
										</div>
								  	</div>
								    <!-- /.row -->
							    	<div class="row">
								    	<div class="col-sm-7 text-right">
											<div class="form-group">
										        {!! Form::label('show_space_after_symbol', trans('app.show_space_after_symbol'). ':', ['class' => 'with-help control-label']) !!}
											  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.config_show_space_after_symbol') }}"></i>
											</div>
										</div>
								    	<div class="col-sm-4">
									  		@if($can_update)
											  	<div class="handle horizontal text-center">
													<a href="{{ route('admin.setting.system.notification.toggle', 'show_space_after_symbol') }}" type="button" class="btn btn-md btn-secondary btn-toggle {{ $system->show_space_after_symbol == 1 ? 'active' : '' }}" data-toggle="button" aria-pressed="{{ $system->show_space_after_symbol == 1 ? 'true' : 'false' }}" autocomplete="off">
														<div class="btn-handle"></div>
													</a>
											  	</div>
											@else
												<span>{{ $system->show_space_after_symbol == 1 ? trans('app.on') : trans('app.off') }}</span>
											@endif
										</div>
								  	</div>
								    <!-- /.row -->
								</fieldset>
					    	</div>

					    	<div class="col-sm-6">
					    		<fieldset>
					    			<legend>{{ trans('app.config_promotions') }}</legend>
									<div class="form-group">
								        {!! Form::label('coupon_code_size', '*' . trans('app.coupon_code_size'). ':', ['class' => 'with-help col-sm-7 control-label']) !!}
									  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.config_coupon_code_size') }}"></i>
									  	<div class="col-sm-4 nopadding-left">
									  		@if($can_update)
									    	    {!! Form::number('coupon_code_size', $system->coupon_code_size, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.coupon_code_size'), 'required']) !!}
										      	<div class="help-block with-errors"></div>
											@else
												<span>{{ $system->coupon_code_size }}</span>
											@endif
									  	</div>
									</div>

									{{-- <div class="form-group">
								        {!! Form::label('gift_card_pin_size', '*' . trans('app.config_gift_card_pin_size'). ':', ['class' => 'with-help col-sm-7 control-label']) !!}
									  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.config_gift_card_pin_size') }}"></i>
									  	<div class="col-sm-4 nopadding-left">
									  		@if($can_update)
									    	    {!! Form::number('gift_card_pin_size', $system->gift_card_pin_size, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.gift_card_pin_size'), 'required']) !!}
										      	<div class="help-block with-errors"></div>
											@else
												<span>{{ $system->gift_card_pin_size }}</span>
											@endif
									  	</div>
									</div>

									<div class="form-group">
								        {!! Form::label('gift_card_serial_number_size', '*' . trans('app.gift_card_serial_number_size'). ':', ['class' => 'with-help col-sm-7 control-label']) !!}
									  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.config_gift_card_serial_number_size') }}"></i>
									  	<div class="col-sm-4 nopadding-left">
									  		@if($can_update)
									    	    {!! Form::number('gift_card_serial_number_size', $system->gift_card_serial_number_size, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.gift_card_serial_number_size'), 'required']) !!}
										      	<div class="help-block with-errors"></div>
											@else
												<span>{{ $system->gift_card_serial_number_size }}</span>
											@endif
									  	</div>
									</div>--}}
								</fieldset>


					    		<fieldset>
					    			<legend>{{ trans('app.address') }}</legend>
									<div class="form-group">
								        {!! Form::label('address_default_country', trans('app.config_address_default_country'). ':', ['class' => 'with-help col-sm-5 control-label']) !!}
									  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.config_address_default_country') }}"></i>
									  	<div class="col-sm-6 nopadding-left">
									  		@if($can_update)
										    	{!! Form::select('address_default_country', $countries , $system->address_default_country, ['id' => 'country_id', 'class' => 'form-control select2', 'placeholder' => trans('app.placeholder.country')]) !!}
											@else
												<span>{{ get_value_from($system->address_default_country, 'countries', 'name') }}</span>
											@endif
									  	</div>
									</div>

									<div class="form-group">
								        {!! Form::label('address_default_state', trans('app.config_address_default_state'). ':', ['class' => 'with-help col-sm-5 control-label']) !!}
									  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.config_address_default_state') }}"></i>
									  	<div class="col-sm-6 nopadding-left">
									  		@if($can_update)
										    	{!! Form::select('address_default_state', $states , $system->address_default_state, ['id' => 'state_id', 'class' => 'form-control select2-tag', 'placeholder' => trans('app.placeholder.state')]) !!}
											@else
												<span>{{ get_value_from($system->address_default_state, 'states', 'name') }}</span>
											@endif
									  	</div>
									</div>

							    	<div class="row">
								    	<div class="col-sm-7 text-right">
											<div class="form-group">
										        {!! Form::label('show_address_title', trans('app.show_address_title'). ':', ['class' => 'with-help control-label']) !!}
											  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.config_show_address_title') }}"></i>
											</div>
										</div>
								    	<div class="col-sm-4">
									  		@if($can_update)
											  	<div class="handle horizontal text-center">
													<a href="{{ route('admin.setting.system.notification.toggle', 'show_address_title') }}" type="button" class="btn btn-md btn-secondary btn-toggle {{ $system->show_address_title == 1 ? 'active' : '' }}" data-toggle="button" aria-pressed="{{ $system->show_address_title == 1 ? 'true' : 'false' }}" autocomplete="off">
														<div class="btn-handle"></div>
													</a>
											  	</div>
											@else
												<span>{{ $system->show_address_title == 1 ? trans('app.on') : trans('app.off') }}</span>
											@endif
										</div>
								  	</div>
								    <!-- /.row -->
							    	<div class="row">
								    	<div class="col-sm-7 text-right">
											<div class="form-group">
										        {!! Form::label('address_show_country', trans('app.address_show_country'). ':', ['class' => 'with-help control-label']) !!}
											  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.config_address_show_country') }}"></i>
											</div>
										</div>
								    	<div class="col-sm-4">
									  		@if($can_update)
											  	<div class="handle horizontal text-center">
													<a href="{{ route('admin.setting.system.notification.toggle', 'address_show_country') }}" type="button" class="btn btn-md btn-secondary btn-toggle {{ $system->address_show_country == 1 ? 'active' : '' }}" data-toggle="button" aria-pressed="{{ $system->address_show_country == 1 ? 'true' : 'false' }}" autocomplete="off">
														<div class="btn-handle"></div>
													</a>
											  	</div>
											@else
												<span>{{ $system->address_show_country == 1 ? trans('app.on') : trans('app.off') }}</span>
											@endif
										</div>
								  	</div>
								    <!-- /.row -->
							    	<div class="row">
								    	<div class="col-sm-7 text-right">
											<div class="form-group">
										        {!! Form::label('address_show_map', trans('app.address_show_map'). ':', ['class' => 'with-help control-label']) !!}
											  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.config_address_show_map') }}"></i>
											</div>
										</div>
								    	<div class="col-sm-4">
									  		@if($can_update)
											  	<div class="handle horizontal text-center">
													<a href="{{ route('admin.setting.system.notification.toggle', 'address_show_map') }}" type="button" class="btn btn-md btn-secondary btn-toggle {{ $system->address_show_map == 1 ? 'active' : '' }}" data-toggle="button" aria-pressed="{{ $system->address_show_map == 1 ? 'true' : 'false' }}" autocomplete="off">
														<div class="btn-handle"></div>
													</a>
											  	</div>
											@else
												<span>{{ $system->address_show_map == 1 ? trans('app.on') : trans('app.off') }}</span>
											@endif
										</div>
								  	</div>
								    <!-- /.row -->
					    		</fieldset>
					    	</div>

					    	<div class="col-sm-12">
						  		@if($can_update)
									<p class="help-block">* {{ trans('app.form.required_fields') }}</p>
									<div class="col-md-offset-3">
							            {!! Form::submit(trans('app.update'), ['class' => 'btn btn-lg btn-flat btn-new']) !!}
							        </div>
						  		@endif
							</div>
				        {!! Form::close() !!}
			    	</div>
			    </div>
			    <!-- /.tab-pane -->

			    <div class="tab-pane" id="support_tab">
			        {!! Form::model($system, ['method' => 'PUT', 'route' => ['admin.setting.system.update'], 'files' => true, 'id' => 'form2', 'class' => 'form-horizontal ajax-form', 'data-toggle' => 'validator']) !!}
				    	<div class="row">
					    	<div class="col-sm-12">
								<div class="form-group">
							        {!! Form::label('support_phone', trans('app.support_phone'). ':', ['class' => 'with-help col-sm-3 control-label']) !!}
								  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.support_phone') }}"></i>
								  	<div class="col-sm-6 nopadding-left">
								  		@if($can_update)
										    <div class="input-group">
										        <span class="input-group-addon"><i class="fa fa-phone"></i></span>
									    	    {!! Form::number('support_phone', $system->support_phone, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.support_phone')]) !!}
									    	</div>
										@else
											<span>{{ $system->support_phone }}</span>
										@endif
								  	</div>
								</div>

								<div class="form-group">
							        {!! Form::label('support_phone_toll_free', trans('app.support_phone_toll_free'). ':', ['class' => 'with-help col-sm-3 control-label']) !!}
								  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.support_phone_toll_free') }}"></i>
								  	<div class="col-sm-6 nopadding-left">
								  		@if($can_update)
										    <div class="input-group">
										        <span class="input-group-addon"><i class="fa fa-phone-square"></i></span>
									    	    {!! Form::number('support_phone_toll_free', $system->support_phone_toll_free, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.support_phone_toll_free')]) !!}
									    	</div>
										@else
											<span>{{ $system->support_phone_toll_free }}</span>
										@endif
								  	</div>
								</div>

								<div class="form-group">
							        {!! Form::label('support_email', '*' . trans('app.support_email'). ':', ['class' => 'with-help col-sm-3 control-label']) !!}
								  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.support_email') }}"></i>
								  	<div class="col-sm-6 nopadding-left">
								  		@if($can_update)
										    <div class="input-group">
										        <span class="input-group-addon"><i class="fa fa-envelope-o"></i></span>
												{!! Form::email('support_email', $system->support_email, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.support_email'), 'required']) !!}
									    	</div>
									      	<div class="help-block with-errors"></div>
										@else
											<span>{{ $system->support_email }}</span>
										@endif
								  	</div>
								</div>

								<div class="form-group">
							        {!! Form::label('default_sender_email_address', '*' . trans('app.default_sender_email_address'). ':', ['class' => 'with-help col-sm-3 control-label']) !!}
								  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.default_sender_email_address') }}"></i>
								  	<div class="col-sm-6 nopadding-left">
								  		@if($can_update)
										    <div class="input-group">
										        <span class="input-group-addon"><i class="fa fa-at"></i></span>
												{!! Form::email('default_sender_email_address', $system->default_sender_email_address, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.default_sender_email_address'), 'required']) !!}
									    	</div>
									      	<div class="help-block with-errors"></div>
										@else
											<span>{{ $system->default_sender_email_address }}</span>
										@endif
								  	</div>
								</div>

								<div class="form-group">
							        {!! Form::label('default_email_sender_name', '*' . trans('app.default_email_sender_name'). ':', ['class' => 'with-help col-sm-3 control-label']) !!}
								  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.default_email_sender_name') }}"></i>
								  	<div class="col-sm-6 nopadding-left">
								  		@if($can_update)
										    <div class="input-group">
										        <span class="input-group-addon"><i class="fa fa-user"></i></span>
												{!! Form::text('default_email_sender_name', $system->default_email_sender_name, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.default_email_sender_name'), 'required']) !!}
									    	</div>
									      	<div class="help-block with-errors"></div>
										@else
											<span>{{ $system->default_email_sender_name }}</span>
										@endif
								  	</div>
								</div>
							</div>
						</div>

				    	<div class="row">
				    		<fieldset>
								<div class="col-sm-12">
					    			<legend class="col-sm-9">{{ trans('app.social_links') }}</legend>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
								        {!! Form::label('google_plus_link', trans('app.google_plus_link'). ':', ['class' => 'with-help col-sm-3 control-label']) !!}
									  	<div class="col-sm-6 nopadding-left">
									  		@if($can_update)
											    <div class="input-group">
											        <span class="input-group-addon"><i class="fa fa-google-plus-official"></i></span>
													{!! Form::text('google_plus_link', $system->google_plus_link, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.google_plus_link')]) !!}
										    	</div>
											@else
												<span>{{ $system->google_plus_link }}</span>
											@endif
									  	</div>
									</div>
									<div class="form-group">
								        {!! Form::label('facebook_link', trans('app.facebook_link'). ':', ['class' => 'with-help col-sm-3 control-label']) !!}
									  	<div class="col-sm-6 nopadding-left">
									  		@if($can_update)
											    <div class="input-group">
											        <span class="input-group-addon"><i class="fa fa-facebook"></i></span>
													{!! Form::text('facebook_link', $system->facebook_link, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.facebook_link')]) !!}
										    	</div>
											@else
												<span>{{ $system->facebook_link }}</span>
											@endif
									  	</div>
									</div>
   									<div class="form-group">
								        {!! Form::label('twitter_link', trans('app.twitter_link'). ':', ['class' => 'with-help col-sm-3 control-label']) !!}
									  	<div class="col-sm-6 nopadding-left">
									  		@if($can_update)
											    <div class="input-group">
											        <span class="input-group-addon"><i class="fa fa-twitter"></i></span>
													{!! Form::text('twitter_link', $system->twitter_link, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.twitter_link')]) !!}
										    	</div>
											@else
												<span>{{ $system->twitter_link }}</span>
											@endif
									  	</div>
									</div>
									<div class="form-group">
								        {!! Form::label('pinterest_link', trans('app.pinterest_link'). ':', ['class' => 'with-help col-sm-3 control-label']) !!}
									  	<div class="col-sm-6 nopadding-left">
									  		@if($can_update)
											    <div class="input-group">
											        <span class="input-group-addon"><i class="fa fa-pinterest"></i></span>
													{!! Form::text('pinterest_link', $system->pinterest_link, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.pinterest_link')]) !!}
										    	</div>
											@else
												<span>{{ $system->pinterest_link }}</span>
											@endif
									  	</div>
									</div>
									<div class="form-group">
								        {!! Form::label('instagram_link', trans('app.instagram_link'). ':', ['class' => 'with-help col-sm-3 control-label']) !!}
									  	<div class="col-sm-6 nopadding-left">
									  		@if($can_update)
											    <div class="input-group">
											        <span class="input-group-addon"><i class="fa fa-instagram"></i></span>
													{!! Form::text('instagram_link', $system->instagram_link, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.instagram_link')]) !!}
										    	</div>
											@else
												<span>{{ $system->instagram_link }}</span>
											@endif
									  	</div>
									</div>
									<div class="form-group">
								        {!! Form::label('youtube_link', trans('app.youtube_link'). ':', ['class' => 'with-help col-sm-3 control-label']) !!}
									  	<div class="col-sm-6 nopadding-left">
									  		@if($can_update)
											    <div class="input-group">
											        <span class="input-group-addon"><i class="fa fa-youtube"></i></span>
													{!! Form::text('youtube_link', $system->youtube_link, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.youtube_link')]) !!}
										    	</div>
											@else
												<span>{{ $system->youtube_link }}</span>
											@endif
									  	</div>
									</div>
						    	</div>
				    		</fieldset>
				    	</div>

					    <div class="row">
					    	<div class="col-sm-12">
								<p class="help-block">* {{ trans('app.form.required_fields') }}</p>
						  		@if($can_update)
									<div class="col-md-offset-3">
							            {!! Form::submit(trans('app.update'), ['class' => 'btn btn-lg btn-flat btn-new']) !!}
							        </div>
						  		@endif
					    	</div>
				    	</div>
			        {!! Form::close() !!}
			    </div>
			  	<!-- /.tab-pane -->

			    <div class="tab-pane" id="reports_tab">
			    	<div class="row">
				    	<div class="col-sm-6">
				    		<fieldset>
				    			<legend>{{ trans('app.visitors') }}</legend>
						    	<div class="row">
							    	<div class="col-sm-8 text-right">
										<div class="form-group">
									        {!! Form::label('google_analytic_report', trans('app.google_analytic_report'). ':', ['class' => 'with-help control-label']) !!}
										  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.google_analytic_report') }}"></i>
										</div>
									</div>
							    	<div class="col-sm-4">
								  		@if($can_update)
										  	<div class="handle horizontal">
												<a href="{{ route('admin.setting.system.notification.toggle', 'google_analytic_report') }}" type="button" class="btn btn-md btn-secondary btn-toggle {{ $system->google_analytic_report == 1 ? 'active' : '' }}" data-toggle="button" aria-pressed="{{ $system->google_analytic_report == 1 ? 'true' : 'false' }}" autocomplete="off">
													<div class="btn-handle"></div>
												</a>
										  	</div>
										@else
											<span>{{ $system->google_analytic_report == 1 ? trans('app.on') : trans('app.off') }}</span>
										@endif
									</div>
							  	</div>
							    <!-- /.row -->
							</fieldset>
					  	</div>
					    <!-- /.col-sm-6 -->

					  	<div class="col-sm-6">

					  	</div>
					    <!-- /.col-sm-6 -->
			    	</div>
				    <!-- /.row -->
			    </div>
			    <!-- /.tab-pane -->

			    <div class="tab-pane" id="notifications_tab">
			    	<div class="row">
				    	<div class="col-sm-6">
				    		<fieldset>
				    			<legend>{{ trans('app.order') }}</legend>
						    	<div class="row">
							    	<div class="col-sm-8 text-right">
										<div class="form-group">
									        {!! Form::label('notify_new_order', trans('app.notify_new_order'). ':', ['class' => 'with-help control-label']) !!}
										  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.notify_new_order') }}"></i>
										</div>
									</div>
							    	<div class="col-sm-4">
								  		@if($can_update)
										  	<div class="handle horizontal">
												<a href="{{ route('admin.setting.system.notification.toggle', 'notify_new_order') }}" type="button" class="btn btn-md btn-secondary btn-toggle {{ $system->notify_new_order == 1 ? 'active' : '' }}" data-toggle="button" aria-pressed="{{ $system->notify_new_order == 1 ? 'true' : 'false' }}" autocomplete="off">
													<div class="btn-handle"></div>
												</a>
										  	</div>
										@else
											<span>{{ $system->notify_new_order == 1 ? trans('app.on') : trans('app.off') }}</span>
										@endif
									</div>
							  	</div> <!-- /.row -->

						    	<div class="row">
							    	<div class="col-sm-8 text-right">
										<div class="form-group">
									        {!! Form::label('notify_alert_quantity', trans('app.notify_alert_quantity'). ':', ['class' => 'with-help control-label']) !!}
										  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.notify_alert_quantity') }}"></i>
										</div>
									</div>
							    	<div class="col-sm-4">
								  		@if($can_update)
										  	<div class="handle horizontal">
												<a href="{{ route('admin.setting.system.notification.toggle', 'notify_alert_quantity') }}" type="button" class="btn btn-md btn-secondary btn-toggle {{ $system->notify_alert_quantity == 1 ? 'active' : '' }}" data-toggle="button" aria-pressed="{{ $system->notify_alert_quantity == 1 ? 'true' : 'false' }}" autocomplete="off">
													<div class="btn-handle"></div>
												</a>
										  	</div>
										@else
											<span>{{ $system->notify_alert_quantity == 1 ? trans('app.on') : trans('app.off') }}</span>
										@endif
									</div>
							  	</div> <!-- /.row -->

						    	<div class="row">
							    	<div class="col-sm-8 text-right">
										<div class="form-group">
									        {!! Form::label('notify_inventory_out', trans('app.notify_inventory_out'). ':', ['class' => 'with-help control-label']) !!}
										  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.notify_inventory_out') }}"></i>
										</div>
									</div>
							    	<div class="col-sm-4">
								  		@if($can_update)
										  	<div class="handle horizontal">
												<a href="{{ route('admin.setting.system.notification.toggle', 'notify_inventory_out') }}" type="button" class="btn btn-md btn-secondary btn-toggle {{ $system->notify_inventory_out == 1 ? 'active' : '' }}" data-toggle="button" aria-pressed="{{ $system->notify_inventory_out == 1 ? 'true' : 'false' }}" autocomplete="off">
													<div class="btn-handle"></div>
												</a>
										  	</div>
										@else
											<span>{{ $system->notify_inventory_out == 1 ? trans('app.on') : trans('app.off') }}</span>
										@endif
									</div>
							  	</div> <!-- /.row -->

						    	<div class="row">
							    	<div class="col-sm-8 text-right">
										<div class="form-group">
									        {!! Form::label('notify_abandoned_checkout', trans('app.notify_abandoned_checkout'). ':', ['class' => 'with-help control-label']) !!}
										  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.notify_abandoned_checkout') }}"></i>
										</div>
									</div>
							    	<div class="col-sm-4">
								  		@if($can_update)
										  	<div class="handle horizontal">
												<a href="{{ route('admin.setting.system.notification.toggle', 'notify_abandoned_checkout') }}" type="button" class="btn btn-md btn-secondary btn-toggle {{ $system->notify_abandoned_checkout == 1 ? 'active' : '' }}" data-toggle="button" aria-pressed="{{ $system->notify_abandoned_checkout == 1 ? 'true' : 'false' }}" autocomplete="off">
													<div class="btn-handle"></div>
												</a>
										  	</div>
										@else
											<span>{{ $system->notify_abandoned_checkout == 1 ? trans('app.on') : trans('app.off') }}</span>
										@endif
									</div>
							  	</div> <!-- /.row -->

							</fieldset>
					  	</div>
					    <!-- /.col-sm-6 -->

					  	<div class="col-sm-6">

				    		<fieldset>
				    			<legend>{{ trans('app.support') }}</legend>
						    	<div class="row">
							    	<div class="col-sm-8 text-right">
										<div class="form-group">
									        {!! Form::label('notify_new_message', trans('app.notify_new_message'). ':', ['class' => 'with-help control-label']) !!}
										  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.notify_new_message') }}"></i>
										</div>
									</div>
							    	<div class="col-sm-4">
								  		@if($can_update)
										  	<div class="handle horizontal">
												<a href="{{ route('admin.setting.system.notification.toggle', 'notify_new_message') }}" type="button" class="btn btn-md btn-secondary btn-toggle {{ $system->notify_new_message == 1 ? 'active' : '' }}" data-toggle="button" aria-pressed="{{ $system->notify_new_message == 1 ? 'true' : 'false' }}" autocomplete="off">
													<div class="btn-handle"></div>
												</a>
										  	</div>
										@else
											<span>{{ $system->notify_new_message == 1 ? trans('app.on') : trans('app.off') }}</span>
										@endif
									</div>
							  	</div> <!-- /.row -->

						    	<div class="row">
							    	<div class="col-sm-8 text-right">
										<div class="form-group">
									        {!! Form::label('notify_new_disput', trans('app.notify_new_disput'). ':', ['class' => 'with-help control-label']) !!}
										  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.notify_new_disput') }}"></i>
										</div>
									</div>
							    	<div class="col-sm-4">
								  		@if($can_update)
										  	<div class="handle horizontal">
												<a href="{{ route('admin.setting.system.notification.toggle', 'notify_new_disput') }}" type="button" class="btn btn-md btn-secondary btn-toggle {{ $system->notify_new_disput == 1 ? 'active' : '' }}" data-toggle="button" aria-pressed="{{ $system->notify_new_disput == 1 ? 'true' : 'false' }}" autocomplete="off">
													<div class="btn-handle"></div>
												</a>
										  	</div>
										@else
											<span>{{ $system->notify_new_disput == 1 ? trans('app.on') : trans('app.off') }}</span>
										@endif
									</div>
							  	</div> <!-- /.row -->

						    	<div class="row">
							    	<div class="col-sm-8 text-right">
										<div class="form-group">
									        {!! Form::label('notify_when_dispute_appealed', trans('app.notify_when_dispute_appealed'). ':', ['class' => 'with-help control-label']) !!}
										  	<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.notify_when_dispute_appealed') }}"></i>
										</div>
									</div>
							    	<div class="col-sm-4">
								  		@if($can_update)
										  	<div class="handle horizontal">
												<a href="{{ route('admin.setting.system.notification.toggle', 'notify_when_dispute_appealed') }}" type="button" class="btn btn-md btn-secondary btn-toggle {{ $system->notify_when_dispute_appealed == 1 ? 'active' : '' }}" data-toggle="button" aria-pressed="{{ $system->notify_when_dispute_appealed == 1 ? 'true' : 'false' }}" autocomplete="off">
													<div class="btn-handle"></div>
												</a>
										  	</div>
										@else
											<span>{{ $system->notify_when_dispute_appealed == 1 ? trans('app.on') : trans('app.off') }}</span>
										@endif
									</div>
							  	</div> <!-- /.row -->
							</fieldset>
					  	</div>
					    <!-- /.col-sm-6 -->
			    	</div>
				    <!-- /.row -->
			    </div>
			    <!-- /.tab-pane -->
			</div>
			<!-- /.tab-content -->
		</div>
	</div> <!-- /.box -->
@endsection