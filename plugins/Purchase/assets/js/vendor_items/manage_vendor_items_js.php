<script>
$(document).ready(function () {
	"use strict";
	$(".select2").select2();
});

var fnServerParams;

(function($) {
	"use strict";

	fnServerParams = {
		"vendor_filter": '[name="vendor_filter"]',
    	"items_filter": '[name="item_select"]',
    	"group_items_filter": '[name="group_items_filter"]'
	}

	init_ajax_search('items','#item_select.ajax-search',undefined,"<?php echo get_uri('purchase/pur_commodity_code_search'); ?>" );

	init_vendor_items_table();

	$('select[name="vendor_filter"]').on('change', function() {
		init_vendor_items_table();

	});
	$('select[name="item_select"]').on('change', function() {
		init_vendor_items_table();
	});
	$('select[name="group_items_filter"]').on('change', function() {
		init_vendor_items_table();
	});

	 $("body").on('change', '#mass_select_all', function() {
        var to, rows, checked;
        to = $(this).data('to-table');
        rows = $('.table-' + to).find('tbody tr');
        checked = $(this).prop('checked');
        $.each(rows, function() {
            $($(this).find('td').eq(0)).find('input').prop('checked', checked);
        });
    });

})(jQuery);


function init_vendor_items_table() {
  "use strict";
 if ($.fn.DataTable.isDataTable('.table-vendor-items')) {
   $('.table-vendor-items').DataTable().destroy();
 }

 initDataTable('.table-vendor-items', "<?php echo get_uri('purchase/vendor_items_table'); ?>", [0], [0], fnServerParams, [1, 'desc']);

}

function staff_bulk_actions(){
	"use strict";
	$('#table_vendors_items_list_bulk_actions').modal('show');
}

function purchase_delete_bulk_action(event) {
	"use strict";

	if (confirm_delete()) {
		var mass_delete = $('#mass_delete').prop('checked');

		if(mass_delete == true){
			var ids = [];
			var data = {};

			data.mass_delete = true;
			data.rel_type = 'vendor_items';

			var rows = $('.table-vendor-items').find('tbody tr');
			$.each(rows, function() {
				var checkbox = $($(this).find('td').eq(0)).find('input');
				if (checkbox.prop('checked') === true) {
					ids.push(checkbox.val());
				}
			});

			data.ids = ids;
			$(event).addClass('disabled');
			setTimeout(function() {
				$.post( "<?php echo get_uri('purchase/warehouse_delete_bulk_action'); ?>", data).done(function() {
					window.location.reload();
				}).fail(function(data) {
					$('#table_vendors_items_list_bulk_actions').modal('hide');
					appAlert.error( data.responseText);
				});
			}, 200);
		}else{
			window.location.reload();
		}

	}
}

// Will give alert to confirm delete
function confirm_delete() {
	"use strict";
    var message = 'Are you sure you want to perform this action?';

    // Clients area
    if (typeof(app) != 'undefined') {
        message = "<?php echo app_lang('confirm_action_prompt'); ?>";
    }

    var r = confirm(message);
    if (r == false) { return false; }
    return true;
}
</script>