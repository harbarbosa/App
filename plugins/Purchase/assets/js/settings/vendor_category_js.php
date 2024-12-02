<script type="text/javascript">
loadUnitsTable = function (selector) {
var optionVisibility = false;

$(selector).appTable({
source: '<?php echo_uri("purchase/list_vendor_category_data") ?>',

        order: [[0, "desc"]],
        
        columns: [
        {title: "#ID", "class": "w10p"},
        {title: "<?php echo app_lang("pur_name") ?>", "class": ""},
        {title: "<?php echo app_lang("pur_description") ?>", "class": "w15p"},
        {title: "<?php echo app_lang("pur_options") ?>", "class": "w10p"},
        ],

});
};
$(document).ready(function () {
"use strict";
loadUnitsTable("#vendor_category-table");
});
</script>