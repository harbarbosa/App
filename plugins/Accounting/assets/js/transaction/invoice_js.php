<script type="text/javascript">
var site_url = $('input[name="site_url"]').val();
   
function init_invoices_table() {
  "use strict";
  
  $('#sales-invoices-table').appTable({
    source: site_url + 'accounting/sales_invoice_table',
            multiSelect: [
              {
                name: "status",
                text: "<?php echo app_lang('status'); ?>",
                options: [
                    {text: '<?php echo app_lang("acc_converted") ?>', value: "converted"},
                    {text: '<?php echo app_lang("has_not_been_converted") ?>', value: "has_not_been_converted"},
                ]
              }
            ],
           
            rangeDatepicker: [{startDate: {name: "from_date", value: ""}, endDate: {name: "to_date", value: ""}, showClearButton: true}],

            columns: [
              {title: "<?php echo app_lang("invoice") ?>"},
              {title: "<?php echo app_lang("date") ?>"},
              {title: "<?php echo app_lang("amount") ?>"},
              {title: "<?php echo app_lang("client") ?>"},
              {title: "<?php echo app_lang("acc_invoice_status") ?>"},
              {title: "<?php echo app_lang("status") ?>"},
              {title: '<?php echo app_lang("acc_convert") ?>', "class": "text-center option w100"}
            ],
            printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6])
  });

}
    $(document).ready(function () {
        init_invoices_table();
    });
</script>