<div class="col-md-12" id="small-table">
	<div class="page-title clearfix">
    <h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo _l('quotations'); ?></h4>
    <div class="title-button-group">
        <a href="<?php echo get_uri('purchase/estimate?vendor='.$client->userid); ?>" class="btn btn-default"><i data-feather='plus-circle' class='icon-16'></i>&nbsp;<?php echo app_lang('add_estimate'); ?></a>
    </div>
  </div>

        
        <?php $table_data = array(
       _l('estimate_dt_table_heading_number'),
       _l('estimate_dt_table_heading_amount'),
       _l('estimates_total_tax'),
       _l('vendor'),
       _l('pur_request'),
       _l('estimate_dt_table_heading_date'),
       _l('estimate_dt_table_heading_expirydate'),
       _l('approval_status'),
       _l('options'),
    );


    render_datatable1($table_data, 'pur_estimates'); ?>
</div>
