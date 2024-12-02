<?php purchase_load_css(array("assets/css/purchase_style.css")); ?>
<div id="page-content" class="page-wrapper clearfix">
    <div class="card clearfix">
        <div class="page-title clearfix">
            <h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo ($title); ?></h4>

            <div class="title-button-group">
                <a href="<?php echo get_uri('purchase/vendor'); ?>" class="btn btn-default"><i data-feather='plus-circle' class='icon-16'></i>&nbsp;<?php echo app_lang('add_vendor'); ?></a>
            </div>

        </div>
        <div class="table-responsive">
            <table id="monthly-vendor-table" class="display" cellspacing="0" width="100%">   
            </table>
        </div>
           
    </div>
</div>

<script type="text/javascript">
    loadVendorsTable = function (selector) {

    var optionVisibility = false;

    $(selector).appTable({
    source: '<?php echo_uri("purchase/list_vendor_data") ?>',

            order: [[0, "desc"]],
            
            columns: [
            {title: "#ID", "class": "w10p"},
            {title: "<?php echo app_lang("company") ?>", "class": ""},
            {title: "<?php echo app_lang("primary_contact") ?>", "class": "w15p"},

            {title: "<?php echo app_lang("primary_email") ?>", "class": "w10p", "iDataSort": 3},

            {title: "<?php echo app_lang("phone") ?>", "class": "w10p", "iDataSort": 5},
            {title: "<?php echo app_lang("pur_date_created") ?>", "class": "w10p text-right"},
            {title: "<?php echo app_lang("pur_options") ?>", "class": "w10p text-right"}
            ],

    });
    };
    $(document).ready(function () {
    loadVendorsTable("#monthly-vendor-table", "monthly");
    });
</script>