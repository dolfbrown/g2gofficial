@extends('layouts.main')

@section('content')
    <!-- HEADER SECTION -->
    @include('headers.product_page', ['product' => $item])

    <!-- CONTENT SECTION -->
    @if($item->downloadable)
        @include('contents.downloadable_product_page')
    @else
        @include('contents.product_page')
    @endif

    <!-- RELATED ITEMS -->
    <section id="related-items">
        <div class="container">
          <div class="row">
              <div class="col-md-12 nopadding">
                <div class="section-title">
                  <h4>{!! trans('theme.section_headings.related_items') !!}</h4>
                </div>

                @include('sliders.carousel_with_feedback', ['products' => $related])

              </div><!-- /.col-md-12 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </section>

    <div class="clearfix space20"></div>

    <!-- BROWSING ITEMS -->
    @include('sliders.browsing_items')

    <!-- bottom Banner -->
    @include('banners.bottom')
@endsection

@section('scripts')
    @if($item->downloadable)
        @include('scripts.downloadable_product_page')
    @else
        @include('scripts.product_page')
    @endif
@endsection