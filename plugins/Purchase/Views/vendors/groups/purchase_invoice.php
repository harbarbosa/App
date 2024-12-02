<div class="col-md-12" id="small-table">
	<div class="page-title clearfix">
    <h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo _l('purchase_invoices'); ?></h4>
    <div class="title-button-group">
        <a href="<?php echo get_uri('purchase/pur_invoice?vendor='.$client->userid); ?>" class="btn btn-default"><i data-feather='plus-circle' class='icon-16'></i>&nbsp;<?php echo app_lang('add_invoice'); ?></a>
    </div>
  </div>


        <?php  $table_data = array(
                _l('invoice_no'),
                _l('vendor'),
                _l('pur_order'),
                _l('invoice_date'),
                _l('invoice_amount'),
                _l('tax_value'),
                _l('total_included_tax'),
                _l('payment_request_status'),
                _l('payment_status'),
                _l('transaction_id'),
                _l('options'),
                );
            
            render_datatable1($table_data,'table_pur_invoices'); ?> 
</div>