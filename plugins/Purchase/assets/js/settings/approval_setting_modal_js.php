<script>
	$(document).ready(function () {
		"use strict";
		$(".select2").select2();
	});

	var lastAddedItemKey = "<?php echo html_entity_decode($key_number); ?>";

	/*Validate Form*/	
	$("#approval_setting-form").appForm({
		ajaxSubmit: false,
		onSuccess: function (result) {
			if (window.refreshAfterUpdate) {
				window.refreshAfterUpdate = false;
				location.reload();
			} else {
				$("#approval_setting-table").appTable({newData: result.data, dataId: result.id});
			}
		}
	});


	function wh_add_item_to_table(data, itemid) {
		"use strict";
		data = typeof (data) == 'undefined' || data == 'undefined' ? wh_get_item_preview_values() : data;

		if ( data.staff == "" || data.action == "" || data.staff == 'undefined' || data.action == 'undefined') {
			return;
		}

		var table_row = '';
		var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.invoice-items-table tbody .item').length + 1;
		lastAddedItemKey = item_key;
		$("body").append('<div class="dt-loader"></div>');
		wh_get_item_row_template('newitems[' + item_key + ']',data.approver, data.staff, data.action, itemid).done(function(output){
			table_row += output;
			
			$('.invoice-items-table.items').append(table_row);

			wh_clear_item_preview_values('.row');

			$('.select2').select2('destroy');
			$('.select2').select2();

			return true;
		});
		return false;
	}

	function wh_get_item_row_template(name, approver, staff, action, item_key)  {
		"use strict";

		jQuery.ajaxSetup({
			async: false
		});

		var d = $.post("<?php  echo get_uri('purchase/get_approval_setting_row_template'); ?>", {
			name: name,
			approver : approver,
			staff : staff,
			action : action,
			item_key : item_key
		});
		jQuery.ajaxSetup({
			async: true
		});
		return d;
	}

	function wh_clear_item_preview_values(parent) {
		"use strict";

		$('.main select[name="approver"]').val('');
		$('.main select[name="action"]').val('');
		$('.main select[name="staff"]').val('');
	}

	function wh_get_item_preview_values() {
		"use strict";

		var response = {};
		response.approver = $('.main select[name="approver"]').val();
		response.staff = $('.main select[name="staff"]').val();
		response.action = $('.main select[name="action"]').val();
		return response;
	}

	function wh_delete_item(row, itemid,parent) {
		"use strict";

		setTimeout(function () {
			$(row).parent().parent().parent('div.row').remove();
		}, 50);

		if (itemid && $('input[name="isedit"]').length > 0) {
			$(parent+' #removed-items').append(hidden_input('removed_items[]', itemid));
		}

	}

</script>