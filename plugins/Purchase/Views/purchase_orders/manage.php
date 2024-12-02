<div id="page-content" class="page-wrapper clearfix">
  <div class="card clearfix">
  		<div class="page-title clearfix">
	        <h4 class="no-margin font-bold"><i class="fa fa-shopping-basket" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>

          <?php if($user_type == 'staff'){ ?>
  	        <div class="title-button-group">
  	            <a href="<?php echo get_uri('purchase/pur_order'); ?>"class="btn btn-default">
  	              <i data-feather='plus-circle' class='icon-16'></i>  <?php echo _l('add_pur_order'); ?>
  	            </a>
  	        </div>
          <?php } ?>
      	</div>

      	<div class="row ml15 mr15 mt10">
      		<div class="col-md-3">
              <label for="from_date"><?php echo app_lang('from_date'); ?></label>
              <?php echo render_date_input1('from_date','','',array('placeholder' => _l('from_date') )); ?>
          </div>
          <div class="col-md-3">
            <label for="to_date"><?php echo app_lang('to_date'); ?></label>
              <?php echo render_date_input1('to_date','','',array('placeholder' => _l('to_date') )); ?>
          </div>

		   <div class="col-md-3">
		   	<label for="pur_request"><?php echo app_lang('pur_request'); ?></label>
		     <select name="pur_request[]" id="pur_request" class="select2 validate-hidden " multiple data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('purchase_request'); ?>">
		       <?php foreach($pur_request as $s) { ?>
		        <option value="<?php echo html_entity_decode($s['id']); ?>" ><?php echo html_entity_decode($s['pur_rq_code'].' - '.$s['pur_rq_name']); ?></option>
		      <?php } ?>
		     </select>
		   </div>

       <?php if($user_type == 'staff'){ ?>
  		   <div class="col-md-3">
  		   	<label for="vendor"><?php echo app_lang('vendors'); ?></label>
  		     <select name="vendor[]" id="vendor" class="select2 validate-hidden pull-right mright10" multiple data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('vendor'); ?>">
  		       <?php foreach($vendors as $s) { ?>
  		        <option value="<?php echo html_entity_decode($s['userid']); ?>" ><?php echo html_entity_decode($s['company']); ?></option>
  		      <?php } ?>
  		     </select>
  		   </div>
       <?php } ?>
		</div>

        <!-- if estimateid found in url -->

         <?php $table_data = array(
           _l('purchase_order'),
           _l('vendor'),
           _l('order_date'),
           _l('type'),
           _l('project'),
           _l('department'),
           _l('po_description'),
           _l('po_value'),
           _l('tax_value'),
           _l('po_value_included_tax'),
           _l('approval_status'),
           _l('delivery_date'),
           _l('delivery_status'),
           _l('payment_status'),
           _l('options'),
           );
    
       render_datatable1($table_data,'table_pur_order'); ?>
	  
	</div>
</div>

<?php require('plugins/Purchase/assets/js/purchase_orders/purchase_order_manage_js.php'); ?>

