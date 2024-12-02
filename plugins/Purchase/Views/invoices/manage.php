<div id="page-content" class="page-wrapper clearfix">
  <div class="card clearfix">
    <div class="page-title clearfix">
        <h4 class="no-margin font-bold"><i class="fa fa-shopping-basket" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
        <div class="title-button-group">
            <a href="<?php echo get_uri('purchase/pur_invoice'); ?>"class="btn btn-default">
              <i data-feather='plus-circle' class='icon-16'></i>  <?php echo _l('add_invoice'); ?>
            </a>
        </div>
      </div>
          <div class="row ml15 mr15 mt10 general-form">    
              <div class="col-md-2">
                  <?php echo render_date_input1('from_date','from_date','',array('placeholder' => _l('from_date') )); ?>
              </div>
              <div class="col-md-2">
                  <?php echo render_date_input1('to_date','to_date','',array('placeholder' => _l('to_date') )); ?>
              </div>

               <div class="col-md-2 form-group">
                <label for="pur_orders"><?php echo _l('purchase_order'); ?></label>
                 <select name="pur_orders[]" id="pur_orders" class="select2 validate-hidden" multiple="true"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('purchase_order'); ?>">
                   <?php foreach($pur_orders as $ct){ ?>
                    <option value="<?php echo html_entity_decode($ct['id']); ?>" ><?php echo html_entity_decode($ct['pur_order_number']); ?></option>
                   <?php } ?>
                </select>
               </div>
               <?php if($user_type == 'staff'){ ?>
                 <div class="col-md-2 form-group">
                    <?php echo render_select1('vendor_ft[]',$vendors,array('userid','company'),'vendor','',array('data-width'=>'100%','data-none-selected-text'=>_l('vendors'),'multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false); ?>
                </div>
              <?php } ?>
            </div>
            
            <?php 

            $table_data = array(
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
</div>

<?php require('plugins/Purchase/assets/js/invoices/manage_invoice_js.php'); ?>