<section>
	<div class="container">
		<div class="row sc-product-item" id="single-product-wrapper" data-slug="{{$item->slug}}">
		  	<div class="col-md-5 col-sm-6">
		  		@include('layouts.jqzoom', ['item' => $item])
		  	</div><!-- /.col-md-5 col-sm-6 -->

		  	<div class="col-md-7 col-sm-6">
		  		<div class="row">
				  	<div class="col-md-7 col-sm-6 nopadding">
				      	<div class="product-single">
							<div class="product-info">
								@if($item->manufacturer->slug)
								  	<a href="{{ route('show.brand', $item->manufacturer->slug) }}" class="product-info-seller-name">
								  		{{ $item->manufacturer->name }}
								  	</a>
								@endif

								<h5 class="product-info-title space10" data-name="product_name">
									{{ $item->title }}
								</h5>

								@include('layouts.ratings', ['ratings' => $item->feedbacks->avg('rating'), 'count' => $item->feedbacks_count])

								@include('layouts.pricing', ['item' => $item])

							  	<div class="row">
							    	<div class="col-sm-12">
										<a href="{{ route('wishlist.add', $item) }}" class="btn btn-link">
											<i class="fa fa-heart-o"></i>
											@lang('theme.button.add_to_wishlist')
										</a>
							    	</div>
							  	</div>
							</div><!-- /.product-info -->

							@include('layouts.share_btns')

			              	<div class="space30"></div>

			              	@if($attributes->count())
					          	<div class="product-info-options space10">
					              	<div class="select-box-wrapper">
					              		@foreach($attributes as $attribute)
						                  	<div class="row product-attribute">
											  	<div class="col-sm-3 col-xs-4">
						    	              		<span class="info-label" id="attr-{{str_slug($attribute->name)}}" >{{ $attribute->name }}:</span>
												</div>
											  	<div class="col-sm-9 col-xs-8 nopadding-left">
								                    <select class="product-attribute-selector {{$attribute->css_classes}}" id="attribute-{{$attribute->id}}" required="required">
									              		@foreach($attribute->attributeValues as $option)
							                          		<option value="{{ $option->id }}" data-color="{{ $option->color ?? $option->value }}">{{ $option->value }}</option>
									              		@endforeach
							                      	</select>
													<div class="help-block with-errors"></div>
								              	</div><!-- /.col-sm-9 .col-xs-6 -->
							              	</div><!-- /.row -->
					              		@endforeach
					              	</div><!-- /.row .select-box-wrapper -->
					          	</div><!-- /.product-option -->
				          	@endif

				          	<div class="sep space30"></div>

				            {{ Form::hidden('variant_id', Null, ['id' => 'variant-id']) }}

 				          	<a href="javascript:void(0)" data-href="{{ route('direct.checkout', $item->slug) }}" class="btn btn-lg btn-warning flat" id="buy-now-btn"><i class="fa fa-rocket"></i> @lang('theme.button.buy_now')</a>

				          	<a href="{{ route('cart.addItem', $item->slug) }}" class="btn btn-primary btn-lg flat sc-add-to-cart">
				          		<i class="fa fa-shopping-bag"></i> @lang('theme.button.add_to_cart')
				          	</a>

				      	</div><!-- /.product-single -->
			  		</div>

				  	<div class="col-md-5 col-sm-6 nopadding-right">
						<div>
					        <div class="section-title">
					          <h4>{!! trans('theme.section_headings.key_features') !!}</h4>
					        </div>
							<ul class="key_feature_list">
								@foreach(unserialize($item->key_features) as $key_feature)
									<li>{!! $key_feature !!}</li>
								@endforeach
							</ul>
						</div>

						<div class="clearfix space20"></div>
			  		</div>
		  		</div><!-- /.row -->
		      	<div class="space20"></div>
		  	</div><!-- /.col-md-7 col-sm-6 -->
		</div><!-- /.row -->
	</div><!-- /.container -->
</section>

<div class="clearfix space20"></div>

<section id="item-desc-section">
    <div class="container">
      	<div class="row">
      		@if($linked_items->count())
		        <div class="col-md-3 bg-light nopadding-right nopadding-left">
			        <div class="section-title">
			          <p class="">@lang('theme.section_headings.bought_together'): </p>
			        </div>
					<ul class="sidebar-product-list">
					    @foreach($linked_items as $linkedItem)
					        <li class="sc-product-item" data-slug="{{$linkedItem->slug}}">
					            <div class="product-widget">
					                <div class="product-img-wrap">
					                    <img class="product-img" src="{{ get_product_img_src($linkedItem, 'small') }}" alt="{{ $linkedItem->title }}" title="{{ $linkedItem->title }}" />
					                </div>
					                <div class="product-info space10">
					                    @include('layouts.ratings', ['ratings' => $linkedItem->feedbacks->avg('rating')])

					                    <a href="{{ route('show.product', $linkedItem->slug) }}" class="product-info-title" data-name="product_name">{{ $linkedItem->title }}</a>

					                    @include('layouts.pricing', ['item' => $linkedItem])
					                </div>
					                <div class="btn-group pull-right">
				                        <a class="btn btn-default btn-xs flat itemQuickView" href="{{ route('quickView.product', $linkedItem->slug) }}">
				                            <i class="fa fa-external-link" data-toggle="tooltip" title="@lang('theme.button.quick_view')"></i> <span>@lang('theme.button.quick_view')</span>
				                        </a>

							          	<a href="{{ route('cart.addItem', $linkedItem->slug) }}" class="btn btn-primary btn-xs flat sc-add-to-cart pull-right">
							          		<i class="fa fa-shopping-bag"></i> @lang('theme.button.add_to_cart')
							          	</a>
					                </div>
					            </div>
					        </li>
					    @endforeach
					</ul>
		        </div><!-- /.col-md-2 -->
	        @endif

	        <div class="col-md-{{$linked_items->count() ? '9' : '12'}}" id="product_desc_section">
          		<div role="tabpanel">
	              	<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active">
							<a href="#desc_tab" aria-controls="desc_tab" role="tab" data-toggle="tab" aria-expanded="true">@lang('theme.product_desc')</a>
						</li>
						<li role="presentation">
							<a href="#reviews_tab" aria-controls="reviews_tab" role="tab" data-toggle="tab" aria-expanded="false">@lang('theme.customer_reviews')</a>
						</li>
					</ul><!-- /.nav-tab -->

              		<div class="tab-content">
                  		<div role="tabpanel" class="tab-pane fade active in" id="desc_tab">
							{!! $item->description !!}
		                </div>
		              	<div role="tabpanel" class="tab-pane fade" id="reviews_tab">
                      		<div class="reviews-tab">
	                      		@forelse($item->feedbacks->sortByDesc('created_at') as $feedback)
									<p>
										<b>{{ $feedback->customer->getName() }}</b>

										<span class="pull-right small">
											<b class="text-success">@lang('theme.verified_purchase')</b>
											<span class="text-muted"> | {{ $feedback->created_at->diffForHumans() }}</span>
										</span>
									</p>

									<p>{{ $feedback->comment }}</p>

			                        @include('layouts.ratings', ['ratings' => $feedback->rating])

			                        @unless($loop->last)
										<div class="sep"></div>
			                        @endunless
	                          	@empty
	                          		<div class="space20"></div>
	                          		<p class="lead text-center text-muted">@lang('theme.no_reviews')</p>
	                          	@endforelse
	                      	</div>
    	              	</div>
	              	</div><!-- /.tab-content -->
          		</div><!-- /.tabpanel -->
        	</div><!-- /.col-md-9 -->
      	</div><!-- /.row -->
    </div><!-- /.container -->
</section>

<div class="clearfix space20"></div>