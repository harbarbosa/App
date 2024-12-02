<?php 
    $status_dropdown = [ 
      1 => ['id' => 'converted', 'text' => app_lang('acc_converted')],
      2 => ['id' => 'has_not_been_converted', 'text' => app_lang('has_not_been_converted')],
    ];
 ?>
<script type="text/javascript">
var site_url = $('input[name="site_url"]').val();

    function init_sales_table() {
      "use strict";
      $('#payment-table').appTable({
        source: site_url + 'accounting/sales_table',
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
                filterDropdown: [
                  {name: "payment_method_id", class: "w200", options: <?php echo $payment_method_dropdown; ?>},
                ],
                rangeDatepicker: [{startDate: {name: "from_date", value: ""}, endDate: {name: "to_date", value: ""}, showClearButton: true}],
                columns: [
                  {title: "<?php echo app_lang("date") ?>"},
                  {title: "<?php echo app_lang("amount") ?>"},
                  {title: "<?php echo app_lang("payment_method") ?>"},
                  {title: "<?php echo app_lang("invoice") ?>"},
                  {title: "<?php echo app_lang("status") ?>"},
                  {title: '<?php echo app_lang("acc_convert") ?>', "class": "text-center option w100"}
                ],
                printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
                xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6])
      });

    }
    
    $(document).ready(function () {
        init_sales_table();
    });
</script>