<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content flat">
        <a class="close" data-dismiss="modal" aria-hidden="true">&times;</a>
        <div class="row sc-product-item" data-slug="{{$item->slug}}">
            <div class="col-md-5 col-sm-6">
                @include('layouts.jqzoom', ['item' => $item])
            </div>
            <div class="col-md-7 col-sm-6">
                <div class="product-single">
                    @include('layouts.product_info', ['item' => $item])

                    <div class="sep"></div>

                    <div class="row product-attribute">
                        <div class="col-sm-12">
                            <div class="section-title space10">
                              {!! trans('theme.section_headings.key_features') !!}
                            </div>
                            <ul class="key_feature_list space20">
                                @foreach(unserialize($item->key_features) as $key_feature)
                                    <li>{{ $key_feature }}</li>
                                @endforeach
                            </ul>

                            <a href="{{ route('show.product', $item->slug) }}" class="btn btn-default btn-sm flat">
                                @lang('theme.button.view_product_details')
                            </a>
                        </div><!-- /.col-sm-12 -->
                    </div><!-- /.row -->

                    <div class="sep"></div>

                    <a href="{{ route('cart.addItem', $item->slug) }}" class="btn btn-primary flat sc-add-to-cart" data-dismiss="modal">
                        <i class="fa fa-shopping-bag"></i> @lang('theme.button.add_to_cart')
                    </a>

                    <a href="{{ route('direct.checkout', $item->slug) }}" class="btn btn-warning flat">
                        <i class="fa fa-rocket"></i> @lang('theme.button.buy_now')
                    </a>
                </div><!-- /.product-single -->

                <div class="space50"></div>
            </div>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->