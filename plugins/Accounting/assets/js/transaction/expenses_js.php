<script type="text/javascript">
  $(document).ready(function () {
    var site_url = $('input[name="site_url"]').val();

    (function($) {
    	"use strict";

        init_expenses_table();
    })(jQuery);

    function init_expenses_table() {
    "use strict";
      
      $('#expenses-table').appTable({
        source: site_url + 'accounting/expenses_table',
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
                  {name: "category_id", class: "w200", options: <?php echo $categories_dropdown; ?>},
                  {name: "user_id", class: "w200", options: <?php echo $members_dropdown; ?>}
                ],
                rangeDatepicker: [{startDate: {name: "start_date", value: ""}, endDate: {name: "end_date", value: ""}, showClearButton: true}],
                columns: [
                  {title: '<?php echo app_lang("date") ?>', "iDataSort": 0},
                  {title: '<?php echo app_lang("title") ?>'},
                  {title: '<?php echo app_lang("category") ?>'},
                  {title: '<?php echo app_lang("amount") ?>', "class": "text-right"},
                  {title: '<?php echo app_lang("status") ?>', "class": "text-right"},
                  {title: '<?php echo app_lang("acc_convert") ?>', "class": "text-center option w100"}
                ],
                printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
                xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6])
      });
    }
  });
</script>
