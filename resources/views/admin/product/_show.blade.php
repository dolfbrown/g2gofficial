<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-body" style="padding: 0px;">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 5px; right: 10px; z-index: 9;">×</button>

            <div class="col-md-3 nopadding" style="margin-top: 10px;">
				<img src="{{ get_product_img_src($product, 'medium') }}" class="thumbnail" width="100%" alt="{{ $product->title }}">
			</div>
            <div class="col-md-9 nopadding">
				<table class="table no-border">
					<tr>
						<th class="text-right">{{ trans('app.title') }}:</th>
						<td style="width: 75%;"><span class="lead">{{ $product->title }}</span></td>
					</tr>

	                <tr>
	                	<th class="text-right">{{ trans('app.status') }}: </th>
	                	<td style="width: 75%;">{{ $product->active ? trans('app.active') : trans('app.inactive') }}</td>
	                </tr>
					<tr>
						<th class="text-right">{{ trans('app.created_at') }}:</th>
						<td style="width: 75%;">{{ $product->created_at->toFormattedDateString() }}</td>
					</tr>
					<tr>
						<th class="text-right">{{ trans('app.updated_at') }}:</th>
						<td style="width: 75%;">{{ $product->updated_at->toDayDateTimeString() }}</td>
					</tr>
				</table>
			</div>
			<div class="clearfix"></div>
			<!-- Custom Tabs -->
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs nav-justified">
				  <li class="active"><a href="#basic_info_tab" data-toggle="tab">
					{{ trans('app.basic_info') }}
				  </a></li>
				  <li><a href="#listing_tab" data-toggle="tab">
					{{ trans('app.listing') }}
				  </a></li>
				  <li><a href="#description_tab" data-toggle="tab">
					{{ trans('app.description') }}
				  </a></li>
				  <li><a href="#seo_tab" data-toggle="tab">
					{{ trans('app.seo') }}
				  </a></li>
				</ul>
				<div class="tab-content">
				    <div class="tab-pane active" id="basic_info_tab">
				        <table class="table">
			                <tr>
			                	<th>{{ trans('app.categories') }}: </th>
			                	<td>
						          	@foreach($product->categories as $category)
							          	<span class="label label-outline">{{ $category->name }}</span>
							        @endforeach
				                </td>
				            </tr>

				            @if($product->gtin_type && $product->gtin )
				                <tr>
				                	<th>{{ $product->gtin_type }}: </th>
				                	<td>{{ $product->gtin }}</td>
				                </tr>
				            @endif

				            @if($product->brand)
				                <tr>
				                	<th>{{ trans('app.brand') }}: </th>
				                	<td>{{ $product->brand }}</td>
				                </tr>
				            @endif

				            @if($product->model_number)
				                <tr>
				                	<th>{{ trans('app.model_number') }}: </th>
				                	<td>{{ $product->model_number }}</td>
				                </tr>
				            @endif

							<tr>
								<th>{{ trans('app.requires_shipping') }}:</th>
								<td>{{ $product->requires_shipping ? trans('app.yes') : trans('app.no') }}</td>
							</tr>

							<tr>
								<th>{{ trans('app.downloadable') }}:</th>
								<td>{{ $product->downloadable ? trans('app.yes') : trans('app.no') }}</td>
							</tr>

				            @if($product->manufacturer && $product->manufacturer->name)
				                <tr>
				                	<th>{{ trans('app.manufacturer') }}: </th>
				                	<td>{{ $product->manufacturer->name }}</td>
				                </tr>
				            @endif

				            @if($product->origin)
				                <tr>
				                	<th>{{ trans('app.origin') }}: </th>
				                	<td>{{ $product->origin->name }}</td>
				                </tr>
				            @endif

				            @if($product->mpn)
				                <tr>
				                	<th>{{ trans('app.mpn') }}: </th>
				                	<td>{{ $product->mpn }}</td>
				                </tr>
				            @endif
				        </table>
				    </div> <!-- /.tab-pane -->

				    <div class="tab-pane" id="listing_tab">
				        <table class="table">
				            @if($product->sku)
								<tr>
									<th class="text-right">{{ trans('app.sku') }}:</th>
									<td style="width: 75%;">{{ $product->sku }}</td>
								</tr>
							@endif

				        	<tr>
								<th class="text-right">{{ trans('app.available_from') }}:</th>
								<td style="width: 75%;">{{ $product->available_from->toFormattedDateString() }}</td>
				        	</tr>

							<tr>
								<th class="text-right">{{ trans('app.price') }}:</th>
								<td style="width: 75%;"> {{ get_formated_currency($product->price) }} </td>
							</tr>

				            @if($product->offer_price && $product->offer_price > 0)
								<tr>
									<th class="text-right">{{ trans('app.offer_price') }}:</th>
									<td style="width: 75%;">{{ get_formated_currency($product->offer_price) }}</td>
								</tr>
					        @else
								<tr>
									<th>{{ trans('app.no_offer_available') }}</th>
								</tr>
							@endif
				            @if($product->offer_start)
								<tr>
									<th class="text-right">{{ trans('app.offer_start') }}:</th>
									<td style="width: 75%;">
										{{ $product->offer_start->toDayDateTimeString() .' - '. $product->offer_start->diffForHumans() }}
									</td>
								</tr>
							@endif
				            @if($product->offer_end)
								<tr>
									<th class="text-right">{{ trans('app.offer_end') }}:</th>
									<td style="width: 75%;">{{ $product->offer_end->toDayDateTimeString() .' - '. $product->offer_end->diffForHumans() }}</td>
								</tr>
							@endif

							<tr>
								<th class="text-right">{{ trans('app.stock_quantity') }}:</th>
								<td style="width: 75%;"> {{ $product->stock_quantity }} </td>
							</tr>

							<tr>
								<th class="text-right">{{ trans('app.min_order_quantity') }}:</th>
								<td style="width: 75%;">{{ $product->min_order_quantity }}</td>
							</tr>

					    	{{-- @php
					    		$attributes = $product->attributes->toArray();
					    		$attributeValues = $product->attributeValues->toArray();
					    	@endphp

				            @if(count($attributes) > 0)
								@foreach($attributes as $k => $attribute )
									<tr>
										<th class="text-right">{{ $attribute['name'] }}:</th>
										<td style="width: 75%;">{{ $attributeValues[$k]['value'] ?? trans('help.not_available') }}</td>
									</tr>
								@endforeach
							@endif --}}

							<tr>
								<th class="text-right">{{ trans('app.condition') }}:</th>
								<td style="width: 75%;">{!! $product->condition !!}</td>
							</tr>

				            @if($product->condition_note)
								<tr>
									<th class="text-right">{{ trans('app.condition_note') }}:</th>
									<td style="width: 75%;"> {{ $product->condition_note }} </td>
								</tr>
							@endif

							@if($product->requires_shipping)
								<tr>
									<th class="text-right">{{ trans('app.shipping_weight') }}:</th>
									<td style="width: 75%;">{{ get_formated_weight($product->shipping_weight) }}</td>
								</tr>
								<tr>
									<th class="text-right">{{ trans('app.packagings') }}:</th>
									<td style="width: 75%;">
										@forelse($product->packagings as $packaging)
											<label class="label label-outline">{{ $packaging->name }}</label>
										@empty
											<span>{{ trans('app.packaging_not_available') }}</span>
										@endforelse
									</td>
								</tr>
							@endif

				            @if($product->puchase_price)
								<tr>
									<th class="text-right">{{ trans('app.puchase_price') }}:</th>
									<td style="width: 75%;"> {{ get_formated_currency($product->puchase_price) }} </td>
								</tr>
							@endif

				            @if($product->damaged_quantity)
								<tr>
									<th class="text-right">{{ trans('app.damaged_quantity') }}:</th>
									<td style="width: 75%;"> {{ $product->damaged_quantity }} </td>
								</tr>
							@endif

				            @if($product->supplier)
								<tr>
									<th class="text-right">{{ trans('app.supplier') }}:</th>
									<td style="width: 75%;"> {{ $product->supplier->name }} </td>
								</tr>
							@endif

				            @if($product->warehouse)
								<tr>
									<th class="text-right">{{ trans('app.warehouse') }}:</th>
									<td style="width: 75%;"> {{ $product->warehouse->name }} </td>
								</tr>
							@endif
				        </table>
				    </div> <!-- /.tab-pane -->

				    <div class="tab-pane" id="description_tab">
					  <div class="box-body">
				        @if($product->description)
				            {!! htmlspecialchars_decode($product->description) !!}
				        @else
				            <p>{{ trans('app.description_not_available') }} </p>
				        @endif
					  </div>
				    </div> <!-- /.tab-pane -->

				    <div class="tab-pane" id="seo_tab">
				        <table class="table">
				            @if($product->slug)
				                <tr>
				                	<th>{{ trans('app.slug') }}: </th>
				                	<td>{{ $product->slug }}</td>
				                </tr>
				            @endif
				            @if($product->meta_title)
				                <tr>
				                	<th>{{ trans('app.meta_title') }}: </th>
				                	<td>{{ $product->meta_title }}</td>
				                </tr>
				            @endif
				            @if($product->meta_description)
				                <tr>
				                	<th>{{ trans('app.meta_description') }}: </th>
				                	<td>{{ $product->meta_description }}</td>
				                </tr>
				            @endif
				            @if($product->meta_keywords)
				                <tr>
				                	<th>{{ trans('app.meta_keywords') }}: </th>
				                	<td>{{ $product->meta_keywords }}</td>
				                </tr>
				            @endif
				            @if($product->tags)
				                <tr>
				                	<th>{{ trans('app.tags') }}: </th>
				                	<td>
							          	@foreach($product->tags as $tag)
								          	<span class="label label-outline">{{ $tag->name }}</span>
								        @endforeach
				                	</td>
				                </tr>
				            @endif
				        </table>
				    </div>
				  <!-- /.tab-pane -->
				</div>
				<!-- /.tab-content -->
			</div>
        </div>
    </div> <!-- / .modal-content -->
</div> <!-- / .modal-dialog -->