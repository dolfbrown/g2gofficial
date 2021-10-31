<script type="text/javascript">
"use strict";
;(function($, window, document) {
    var unitPrice = {{ $item->currnt_price() }};
    var variants = '{!! $item->variants !!}';
    // console.log(variants);
    var itemWrapper = $("#single-product-wrapper");

    var buyNowBaseUrl = $("#buy-now-btn").data('href');
    buyNowBaseUrl = buyNowBaseUrl.substr(0, buyNowBaseUrl.lastIndexOf('/') + 1);

    var addToCartBaseUrl = itemWrapper.find('.sc-add-to-cart').attr('href');
    addToCartBaseUrl = addToCartBaseUrl.substr(0, addToCartBaseUrl.lastIndexOf('/') + 1);

    $(document).ready(function(){
        $('select.color-options').simplecolorpicker();

        // Move to the detail section if hash given
        $(function () {
            var tabs = ['#desc_tab', '#reviews_tab'];
            if(tabs.indexOf(window.location.hash) != -1)
                $('html,body').animate({scrollTop:$("#item-desc-section").offset().top}, 500);
        });
        $('.product-info-rating-count').on('click', function(e) {
            $('html,body').animate({scrollTop:$("#item-desc-section").offset().top}, 500);
            $('ul.nav a[href="' + this.hash + '"]').tab('show');
        });

        //radioSelect
        $(function () {
            $('.radioSelect').each(function (selectIndex, selectElement) {
                var select = $(selectElement);
                var container = $("<div class='radioSelectContainer' />");
                select.parent().append(container);
                container.append(select);

                select.find('option').each(function (optionIndex, optionElement) {
                    var label = $("<label />");
                    container.append(label);

                    var selectedOption = optionElement.hasAttribute('selected') ? "selected" : "";
                    // console.log(selectedOption);
                    $("<span data-value='"+ $(this).val() +"' class='"+ selectedOption +"'>" + $(this).text() + "</span>").appendTo(label);
                });

                // Handles unchecking when clicking on an already checked radio
                container.find("label > span").mousedown(
                    function (e) {
                        var selectedSpan = $(this);

                        // Ignore if already selected
                        if(selectedSpan.hasClass('selected')) return;

                        // Apply class
                        container.find("label > span").removeClass('selected');
                        selectedSpan.addClass('selected');

                        // Reset and update the seleceted value
                        $('option:selected', 'select[id="'+select.attr('id')+'"]').removeAttr('selected');
                        $("select[id='"+select.attr('id')+"']")
                            .find("option[value='"+selectedSpan.data('value')+"']")
                            .attr("selected", true).change();
                    }
                );
            });
        });

    });

    // Social share btns
    var popupSize = {
        width: 780, height: 550
    };
    $(document).on('click', '.social-share-btn', function (e) {
        event.preventDefault();
        var verticalPos = Math.floor(($(window).width() - popupSize.width) / 2),
            horisontalPos = Math.floor(($(window).height() - popupSize.height) / 2);

        var popup = window.open($(this).prop('href'), 'social',
            'width=' + popupSize.width + ',height=' + popupSize.height +
            ',left=' + verticalPos + ',top=' + horisontalPos +
            ',location=0,menubar=0,toolbar=0,status=0,scrollbars=1,resizable=1');

        if (popup) {
            popup.focus();
            e.preventDefault();
        }
    });

    // Variation updates
    $('.product-attribute-selector').on('change', function(){
        apply_busy_filter('body');
        $('#loading').show();

        var attrs = [];
        $('.product-attribute-selector').each(function(){
            var val = $(this).val();
            if(val) {
                attrs.push(Number(val));
            }
        });

        var filtered = filterItems(attrs);
        // console.log(filtered);

        if(filtered == undefined) {
            $("#variant-id").val('Null');
            itemWrapper.find('.sc-add-to-cart').attr("disabled", "disabled");
            remove_busy_filter('body');
            $('#loading').hide();
            return;
        }

        $("#variant-id").val(filtered.id);

        setSalePrice(filtered);         // Set sale price

        setImg(filtered);               // Set image price

        remove_busy_filter('body');
        $('#loading').hide();
    });

    //////////////////////////
    /// Attribute Changes ///
    //////////////////////////
    function filterItems(options)
    {
        // if (!options || $.isEmptyObject(variants))   return NaN;
        options = JSON.stringify(options.sort());

        return jQuery.parseJSON(variants).find(function (item) {
            // Get the attr sets of the item
            var attrs = item.attribute_values.map(a => a.id);

            // Return the exact match of options with items attr sets
            return JSON.stringify(attrs.sort()) === options;
        });
    }

    function setSalePrice(item)
    {
        if(
            (item.offer_price > 0) && (item.offer_price < item.price) &&
            (Date.parse(item.offer_start) < Date.now()) && (Date.parse(item.offer_end) > Date.now())
        ) {
            unitPrice = Number(item.offer_price);       // Update the unit price for calculation
            var off = ( (Number(item.price) - Number(item.offer_price)) * 100 ) / Number(item.price);
            itemWrapper.find('.old-price').show().html(getFormatedPrice(item.price));
            itemWrapper.find('.product-info-price-new').html(getFormatedPrice(item.offer_price));
            itemWrapper.find('.percent-off').show().html(getFormatedValue(off,0) + '{{trans('theme.percnt_off')}}');
        }
        else {
            unitPrice = Number(item.price);       // Update the unit price for calculation
            itemWrapper.find('.old-price, .percent-off').hide().text('');
            itemWrapper.find('.product-info-price-new').html(getFormatedPrice(item.price));
        }
    }

    function setImg(item)
    {
        if(item.image_id){

            var images = {!! $item->images->toJson() !!};

            var img = images.filter(function (img) {
                return img.id == item.image_id;
            });

            // console.log(img[0]);
            if (typeof img[0] !== 'undefined') {
                var path = getFromPHPHelper('get_storage_file_url', [img[0].path, 'full']);
                $('#jqzoom').removeData('jqzoom'); //Reset the jqzoom
                $('#jqzoom .product-img').attr('src', path);
                $('#jqzoom').attr('href', path);

                path = path.replace(/\?.*/,''); // Remove the size attr from the path url

                $('ul.jqzoom-thumbs').find( 'img' ).each(function() {
                    var src = $(this).attr("src").replace(/\?.*/,'');
                    var node = $(this).parent('a');

                    if(path == src) {
                        node.addClass('zoomThumbActive');
                    }
                    else {
                        node.removeClass('zoomThumbActive');
                    }
                });

                //binding
                $("#jqzoom").jqzoom();
            }
        }
    }

}(window.jQuery, window, document));
</script>