<script type="text/javascript">
;(function($, window, document) {
	// var sorter = $('#sortable').rowSorter({
	$( document ).ready(function () {
		$("#setCombinations").on("click", function(e){
			e.preventDefault();

			var options = {};
			$(".select2-set_attribute").each(function(indx, attrs){
				var attrName = $(this).attr('id');
				var attrValues = $(attrs).val();

				if(attrValues.length){
					options[attrName] = attrValues;
				}
			});

		    var url = "{{ route('admin.catalog.product.getCombinations') }}";
		    $.ajax({
		        url: url, data: options, async: false,
		        success: function(variants){
					$('#myAttributes').collapse('hide');
				    $('#combinationsPlaceholder').html(variants);
		        }
		    });

		    var sku = $('input[name="sku"]').val();
		    var price = $('input[name="price"]').val();
		    var quantity = $('input[name="stock_quantity"]').val();

			$("tr.variant-row").each(function(indx, row){
				if(sku)
					$(this).find('.variant-sku').val(sku + '-' + (indx + 1));

				if(price)
					$(this).find('.variant-price').val(price);

				$(this).find('.variant-qtt').val(quantity);
			});

		});

	    // Preview image on select
	    $('body').on('change', '.variant-img', function() {
		    var img = $(this).next("img");
		    var reader = new FileReader();
		    reader.onload = function (e) {
		        img.attr("src", e.target.result); // get loaded data and render preview.
		    };

		    reader.readAsDataURL(this.files[0]);  // read the image file as a data URL.
		});

		$('body').on('click', '.deleteThisRow', function() {
			$(this).closest('tr').remove();
		});

	});
}(window.jQuery, window, document));
</script>