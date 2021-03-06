<div class="clearfix">
	<a href="{{ get_product_img_src($item, 'full') }}" id="jqzoom" data-rel="gal-1">
		<img class="product-img" data-name="product_image" src="{{ get_product_img_src($item, 'large') }}" alt="{{ $item->title }}" title="{{ $item->title }}" />
	</a>
</div>

<ul class="jqzoom-thumbs">
	@foreach($item->allImages as $img)
		<li>
			<a class="{{ $loop->first ? 'zoomThumbActive' : '' }}" href="javascript:void(0)" data-rel="{gallery:'gal-1', smallimage: '{{ get_storage_file_url($img->path, 'large') }}', largeimage: '{{ get_storage_file_url($img->path, 'full') }}'}">
				<img src="{{ get_storage_file_url($img->path, 'mini') }}" alt="Thumb" title="{{ $item->title }}" />
			</a>
		</li>
	@endforeach
</ul> <!-- /.jqzoom-thumbs -->