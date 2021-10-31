<div class="admin-user-widget">
    <span class="admin-user-widget-img">
        <img src="{{ get_catalog_featured_img_src($product, 'small') }}" class="thumbnail" alt="{{ trans('app.image') }}">
    </span>

    <div class="admin-user-widget-content">

        <span class="admin-user-widget-title">
            {{ $product->title }}
        </span>
        <span class="admin-user-widget-text text-muted">
            {{ $product->gtin_type.': '.$product->gtin }}
        </span>
        <span class="admin-user-widget-text text-muted">
            {{ trans('app.model_number').': '.$product->model_number }}
        </span>
        <span class="admin-user-widget-text text-muted">
            {{ trans('app.manufacturer').': '.optional($product->manufacturer)->name }}
        </span>

    </div> <!-- /.admin-user-widget-content -->
</div>