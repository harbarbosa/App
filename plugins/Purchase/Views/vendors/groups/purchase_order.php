<div class="col-md-12" id="small-table">
	<div class="page-title clearfix">
    <h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo _l('purchase_orders'); ?></h4>
    <div class="title-button-group">
        <a href="<?php echo get_uri('purchase/pur_order?vendor='.$client->userid); ?>" class="btn btn-default"><i data-feather='plus-circle' class='icon-16'></i>&nbsp;<?php echo app_lang('add_purchase_order'); ?></a>
    </div>
  </div>


        <?php $table_data = array(
        _l('purchase_order'),
        _l('total'),
        _l('estimates_total_tax'),
        _l('vendor'),
        _l('order_date'),
        _l('payment_status'),
        _l('status'),
        );

        render_datatable1($table_data,'table_pur_order'); ?>
</div>
