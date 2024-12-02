<script>
$('.select2').select2();
(function($) {

    "use strict";
    var Params = {
        "pur_request": "[name='pur_request[]']",
        "vendor": "[name='vendor[]']",
    };
    var table_estimates = $('.table-pur_estimates');
    initDataTable(table_estimates, "<?php echo get_uri('purchase/table_estimates'); ?>",[0], [0], Params);


     $.each(Params, function(i, obj) {
        $('select' + obj).on('change', function() {  
            table_estimates.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        });
    });
})(jQuery);





</script>