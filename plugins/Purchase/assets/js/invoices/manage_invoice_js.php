<script> 
    $(document).ready(function () {
    "use strict"; 
      $(".select2").select2();
      setDatePicker("#from_date");
      setDatePicker("#to_date");
    });

    
    (function($) {
    	"use strict"; 
    	var table_invoice = $('.table-table_pur_invoices');
    	var Params = {
    		"from_date": 'input[name="from_date"]',
            "to_date": 'input[name="to_date"]',
            "pur_orders": "[name='pur_orders[]']",
            "vendor": "[name='vendor_ft[]']"
        };

    	initDataTable(table_invoice, "<?php echo  get_uri('purchase/table_pur_invoices'); ?>",[0], [0], Params);
    	$.each(Params, function(i, obj) {
            $('select' + obj).on('change', function() {  
                table_invoice.DataTable().ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
            });
        });

        $('input[name="from_date"]').on('change', function() {
            table_invoice.DataTable().ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
        });
        $('input[name="to_date"]').on('change', function() {
            table_invoice.DataTable().ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
        });
    })(jQuery);
</script>   