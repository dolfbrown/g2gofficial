@extends('admin.layouts.master')

@section('content')
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">
				<i class="fa fa-credit-card hidden-sm"></i>
				{{ trans('app.payment_methods') }}
			</h3>
		</div> <!-- /.box-header -->
		<div class="box-body">
	    	<div class="jumbotron" style="padding: 20px; margin-bottom: 10px;">
	    		<p class="text-center">{{ trans('help.config_enable_payment_method') }}</p>
	    	</div>
			@foreach($payment_method_types as $type_id => $type)
				@php
					$payment_providers = $payment_methods->where('type', $type_id);
					$logo_path = sys_image_path('payment-method-types') . "{$type_id}.svg";
				@endphp
		    	<div class="row">
					<span class="spacer10"></span>
			    	<div class="col-sm-6">
			    		@if(File::exists($logo_path))
							<img src="{{ asset($logo_path) }}" width="100" height="25" alt="{{ $type }}">
							<span class="spacer10"></span>
						@else
				    		<p class="lead">{{ $type }}</p>
						@endif
			    		<p>{!! get_payment_method_type($type_id)['description'] !!}</p>
			    	</div>
			    	<div class="col-sm-6">
		    			@foreach($payment_providers as $payment_provider)
		    				@php
		    					$logo_path = sys_image_path('payment-methods') ."{$payment_provider->code}.png";
		    				@endphp
							<ul class="list-group">
								<li class="list-group-item">
						    		@if(File::exists($logo_path))
										<img src="{{ asset($logo_path) }}" class="open-img-md" alt="{{ $type }}">
									@else
										<p class="list-group-item-heading inline lead">
											{{ $payment_provider->name }}
										</p>
									@endif

								  	<div class="handle inline pull-right no-margin">
										<span class="spacer10"></span>
										<a href="{{ route('admin.setting.system.paymentMethod.toggle', $payment_provider->id) }}" type="button" class="btn btn-md btn-secondary btn-toggle {{ $payment_provider->enabled == 1 ? 'active' : '' }}" data-toggle="button" aria-pressed="{{ $payment_provider->enabled == 1 ? 'true' : 'false' }}" autocomplete="off">
											<div class="btn-handle"></div>
										</a>
								  	</div>

									<span class="spacer10"></span>

									<p class="list-group-item-text">
										{!! $payment_provider->description !!}
									</p>

						    		@if($payment_provider->instructions)
						    			<div class="spacer10"></div>
							          	<div class="alert alert-info small">
							            	<strong class="text-uppercase">
							            		<i class="fa fa-info-circle"></i> {{ trans('app.instructions') }} :
								            </strong>
								            <span>{!! $payment_provider->instructions !!}</span>
								        </div>
									@endif

						    		@unless($payment_provider->isConfigured())
						    			<div class="spacer10"></div>
							          	<div class="alert alert-warning">
						            		<i class="fa fa-warning"></i>
								            <span>{{ trans('app.payment_method_not_configured') }}</span>
								        </div>
									@endunless

									<span class="spacer15"></span>

									@if($payment_provider->help_doc_link)
										<a href="{{ $payment_provider->help_doc_link }}" class="btn btn-default" target="_blank"> {{ trans('app.documentation') }}</a>
										<span class="spacer15"></span>
									@endif

									@if(in_array($payment_provider->code, ['cod','wire']))
										<a href="javascript:void(0)" data-link="{{ route('admin.setting.manualPaymentMethod.edit', $payment_provider->code) }}" class="ajax-modal-btn btn btn-default"> {{ trans('app.update_instructions') }}</a>
										<span class="spacer15"></span>
									@endif

								</li>
				    		</ul>
		    			@endforeach
			    	</div>
			    </div>

			    @unless($loop->last)
				    <hr>
			    @endunless
		    @endforeach
		</div> <!-- /.box-body -->
	</div> <!-- /.box -->
@endsection