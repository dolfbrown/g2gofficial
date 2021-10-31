<section>
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="space20"></div>
                <div class="panel-group space20" id="accordion">
                    @foreach($faqTopics as $topic)
                        <div class="faqHeader">{{ $topic->name }}</div>
                        <div class="panel panel-default">
                            @foreach($topic->faqs as $faq)
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#faq-{{ $faq->id }}">{!! $faq->question !!}</a>
                                    </h4>
                                </div>
                                <div id="faq-{{ $faq->id }}" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        {!! $faq->answer !!}
                                    </div>
                                </div>
                                @endforeach
                        </div>
                        @unless($loop->last)
                            <br/>
                        @endunless
                    @endforeach
                </div>
            </div>
        </div>
        <div class="space50"></div>
    </div>
</section>