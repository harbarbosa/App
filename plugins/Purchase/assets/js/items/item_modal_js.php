<script>
	var sub_group_value ='';
	//variation
	var addMoreVendorsInputKey;

	$(document).ready(function () {
		var uploadUrl = "<?php echo get_uri("items/upload_file"); ?>";
		var validationUri = "<?php echo get_uri("items/validate_items_file"); ?>";
		var dropzone = attachDropzoneWithForm("#items-dropzone", uploadUrl, validationUri);

		$("#item-form").appForm({
			ajaxSubmit: false,
			onSuccess: function (result) {
				if (window.refreshAfterUpdate) {
					window.refreshAfterUpdate = false;
					location.reload();
				} else {
					$("#item-table").appTable({newData: result.data, dataId: result.id});
				}
			}
		});

		$("#item-form .select2").select2();
	});

	(function($) {
		"use strict";


		$('input[name="description"]' ).change(function() {
			if($( 'input[name="sku_name"]' ).val() == ''){
				$( 'input[name="sku_name"]' ).val($('input[name="description"]' ).val());
			}
		});


		$('input[name="purchase_price"]').keyup(function(){
			"use strict";
			var data={};
			data.purchase_price = $('input[name="purchase_price"]').val();
			data.profit_rate = $('input[name="profif_ratio"]').val();

			$.post("<?php  echo get_uri('purchase/caculator_sale_price') ?>", data).done(function(response) {
				response = JSON.parse(response);
				$('#item-form input[name="rate"]').val(response.sale_price);
			});

		});

		$('input[name="profif_ratio"]').keyup(function(){
			"use strict";
			var data={};
			data.purchase_price = $('input[name="purchase_price"]').val();
			data.profit_rate = $('input[name="profif_ratio"]').val();

			$.post("<?php  echo get_uri('purchase/caculator_sale_price') ?>", data).done(function(response) {
				response = JSON.parse(response);
				$('#item-form input[name="rate"]').val(response.sale_price);
			});

		});


		$('input[name="rate"]').keyup(function(){
			"use strict";
			var data={};
			data.sale_price = $('input[name="rate"]').val();
			data.profit_rate = $('input[name="profif_ratio"]').val();
			data.purchase_price = $('input[name="purchase_price"]').val();

			if($('input[name="profif_ratio"]').val() != 0 && $('input[name="purchase_price"]').val() != 0){
				$.post("<?php  echo get_uri('purchase/caculator_profit_rate') ?>", data).done(function(response) {
					response = JSON.parse(response);

					$('#item-form input[name="profif_ratio"]').val(response.profit_rate);

				});
			}else if($('input[name="profif_ratio"]').val() == 0){
				$('input[name="purchase_price"]').val($('input[name="rate"]').val());

			}else if($('input[name="profif_ratio"]').val() != 0){

				$.post("<?php  echo get_uri('Purchase/caculator_purchase_price') ?>", data).done(function(response) {
					response = JSON.parse(response);

					$('#item-form input[name="purchase_price"]').val(response.purchase_price);

				});
			}
		});

		/*update*/
		$('input[id="mass_delete"]').on('click', function() {
			"use strict";

			var mass_delete = $('input[id="mass_delete"]').is(":checked");


			if(mass_delete){

				$('input[id="change_item_selling_price"]').prop("checked", false);
				$('input[name="selling_price"]').val('');

				$('input[id="change_item_purchase_price"]').prop("checked", false);
				$('input[name="purchase_price"]').val('');
				$('input[id="clone_items"]').prop("checked", false);
			}

		});

		$('input[id="change_item_selling_price"]').on('click', function() {
			"use strict";

			var item_selling_price_checking = $('input[id="change_item_selling_price"]').is(":checked");


			if(item_selling_price_checking){
				$('input[id="mass_delete"]').prop("checked", false);

				$('input[id="change_item_purchase_price"]').prop("checked", false);
				$('input[name="purchase_price"]').val('');
				$('input[id="clone_items"]').prop("checked", false);
			}

		});

		$('input[id="change_item_purchase_price"]').on('click', function() {
			"use strict";

			var item_selling_purchase_checking = $('input[id="change_item_purchase_price"]').is(":checked");

			if(item_selling_purchase_checking){
				$('input[id="mass_delete"]').prop("checked", false);

				$('input[id="change_item_selling_price"]').prop("checked", false);
				$('input[name="selling_price"]').val('');
				$('input[id="clone_items"]').prop("checked", false);
			}

		});

		$('input[id="clone_items"]').on('click', function() {
			"use strict";

			var clone_items = $('input[id="clone_items"]').is(":checked");


			if(clone_items){

				$('input[id="change_item_selling_price"]').prop("checked", false);
				$('input[name="selling_price"]').val('');

				$('input[id="change_item_purchase_price"]').prop("checked", false);
				$('input[name="purchase_price"]').val('');

				$('input[id="mass_delete"]').prop("checked", false);
			}

		});


		$("body").on('click', '.remove_wh_approval', function() {
			$(this).parents('#item_approve').remove();
		});



})(jQuery); 





</script>