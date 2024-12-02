<div id="page-content" class="page-wrapper clearfix">
  <div class="card clearfix">
  		<div class="page-title clearfix">
	        <h4 class="no-margin font-bold"><i class="fa fa-shopping-basket" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
	        <div class="title-button-group">
	            <a href="<?php echo get_uri('purchase/estimate'); ?>"class="btn btn-default">
	              <i data-feather='plus-circle' class='icon-16'></i>  <?php echo _l('create_new_estimate'); ?>
	            </a>
	        </div>
      	</div>

      	<div class="row ml15 mr15 mt10">
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
</div>

<?php require('plugins/Purchase/assets/js/quotations/manage_quotations_js.php'); ?>

