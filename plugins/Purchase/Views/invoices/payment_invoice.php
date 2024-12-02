
  <div id="page-content" class="page-wrapper clearfix">
    <div class="row">
      <div class="col-md-6">
        <div class="panel_s">
          <div class="panel-body">

          	   <?php if($payment_invoice->approval_status == 1){ ?>
                    <div class="ribbon info"><span class="fontz9" ><?php echo _l('pur_draft'); ?></span></div>
                <?php }elseif($payment_invoice->approval_status == 2){ ?>
                  <div class="ribbon success"><span><?php echo _l('purchase_approved'); ?></span></div>
                <?php }elseif($payment_invoice->approval_status == 3){ ?>  
                  <div class="ribbon danger"><span><?php echo _l('purchase_reject'); ?></span></div>
                <?php } ?>

          	<h4 class="pull-left "><?php echo _l('payment_for').' '; ?><a href="<?php echo get_uri('purchase/purchase_invoice/'. $payment_invoice->pur_invoice); ?>"><?php echo html_entity_decode($invoice->invoice_number); ?></a></h4>
					<div class="clearfix"></div>
				<hr class="hr-panel-heading" />
          	<div class="col-md-12">
          		
				<div class="row">
					<div class="col-md-6 col-sm-6">
						<address>
							<?php echo company_widget(); ?>
						</address>
					</div>
				
					</div>
					<div class="col-md-12 text-center">
						<h3 class="text-uppercase"><?php echo _l('payment_receipt'); ?></h3>
					</div>
					<div class="col-md-12 mt25">
						<div class="row">
							<div class="col-md-6">
								<p><?php echo _l('payment_date'); ?> <span class="pull-right bold"><?php echo _d($payment_invoice->date); ?></span></p>
								<hr />
								<p><?php echo _l('payment_view_mode'); ?>
								<span class="pull-right bold">
									
									<?php if(!empty($payment_invoice->paymentmode)){
										echo  get_payment_mode_by_id($payment_invoice->paymentmode);
									}
									?>
								</span></p>
								<?php if(!empty($payment_invoice->transactionid)) { ?>
									<hr />
									<p><?php echo _l('payment_transaction_id'); ?>: <span class="pull-right bold"><?php echo html_entity_decode($payment_invoice->transactionid); ?></span></p>
								<?php } ?>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-6">
								<div class="payment-preview-wrapper">
									<?php echo _l('payment_total_amount'); ?><br />
									<?php echo to_currency($payment_invoice->amount,$base_currency); ?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12 mt25">
					<h4><?php echo _l('payment_for_string'); ?></h4>
					<div class="table-responsive">
					<table class="table table-borderd table-hover">
						<thead>
							<tr>
								<th><?php echo _l('payment_table_invoice_number'); ?></th>
								<th><?php echo _l('payment_table_invoice_date'); ?></th>
								<th><?php echo _l('payment_table_invoice_amount_total'); ?></th>
								<th><?php echo _l('payment_table_payment_amount_total'); ?></th>
								<?php if($invoice->payment_status != 'paid') { ?>
										<th><span class="text-danger"><?php echo _l('invoice_amount_due'); ?></span></th>
									<?php } ?>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><?php echo get_pur_invoice_number($payment_invoice->pur_invoice); ?></td>
									<td><?php echo _d($invoice->invoice_date); ?></td>
									<td><?php echo to_currency($invoice->total, $base_currency); ?></td>
									<td><?php echo to_currency($payment_invoice->amount, $base_currency); ?></td>
									<?php if($invoice->payment_status != 'paid') { ?>
											<td class="text-danger">
												<?php echo to_currency(purinvoice_left_to_pay($invoice->id), $base_currency); ?>
											</td>
										<?php } ?>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
			</div>

			

        </div>
      </div>
    </div>

    <div class="col-md-6">
     <div class="panel_s">
      <div class="panel-body">
      	<h4 class="pull-left "><?php echo _l('pur_approval_infor'); ?></h4>
					<div class="clearfix"></div>
				<hr class="hr-panel-heading" />

      
        <?php if(count($list_approve_status) > 0){ ?>
          
         <div class="project-overview-right">
           <div class="row">
             <div class="col-md-12 project-overview-expenses-finance">
              <div class="row">
              <?php 
                $users_model = model("Models\Users_model");
                $enter_charge_code = 0;
              foreach ($list_approve_status as $value) {
                $value['staffid'] = explode(', ',$value['staffid']);
                if($value['action'] == 'sign'){
               ?>
               <div class="col-md-3 apr_div">
                   <p class="text-uppercase text-muted no-mtop bold">
                    <?php
                    $staff_name = '';
                    $st = _l('status_0');
                    $color = 'warning';
                    foreach ($value['staffid'] as $key => $val) {
                      if($staff_name != '')
                      {
                        $staff_name .= ' or ';
                      }

                      $options = array(
                          "id" => $val,
                          "user_type" => "staff",
                      );
                      $staff_name .= isset($users_model->get_details($options)->getRow()->first_name) ? $users_model->get_details($options)->getRow()->first_name : '';
                    }
                    echo html_entity_decode($staff_name); 
                    ?></p>
                   <?php if($value['approve'] == 2){ 
                    ?>
                    <img src="<?php echo base_url('plugins/Purchase/Uploads/payment_invoice/signature/'.$payment_invoice->id.'/signature_'.$value['id'].'.png'); ?>" class="img_style">
                     <br><br>
                   <p class="bold text-center text-success"><?php echo _l('signed').' '.format_to_date($value['date']); ?></p>
                   <?php } ?> 
                      
              </div>
              <?php }else{ ?>
              <div class="col-md-3 text-center apr_div">
                   <p class="text-uppercase text-muted no-mtop bold">
                    <?php
                    $staff_name = '';
                    foreach ($value['staffid'] as $key => $val) {
                      if($staff_name != '')
                      {
                        $staff_name .= ' or ';
                      }
                      $options = array(
                          "id" => $val,
                          "user_type" => "staff",
                      );

                      $staff_name .= isset($users_model->get_details($options)->getRow()->first_name) ? $users_model->get_details($options)->getRow()->first_name : '';
                    }
                    echo html_entity_decode($staff_name); 
                    ?></p>
                   <?php if($value['approve'] == 2){ 
                    ?>
                    <img src="<?php echo base_url('plugins/Purchase/Uploads/approval/approved.png'); ?>" class="img_style">
                   <?php }elseif($value['approve'] == 3){ ?>
                      <img src="<?php echo  base_url('plugins/Purchase/Uploads/approval/rejected.png'); ?>" class="img_style">
                  <?php } ?> 
                  <br><br>
                  <p><?php echo html_entity_decode($value['note']) ?></p>  
                  <p class="bold text-center text-<?php if($value['approve'] == 2){ echo 'success'; }elseif($value['approve'] == 3){ echo 'danger'; } ?>"><?php echo format_to_date($value['date']); ?></p> 
              </div>
              <?php }
              } ?>
             </div>
           </div>
          </div>
          </div>
        
        <?php } ?>
        
        <div class=" ml15 mr15 mb15">
            <?php if($check_appr && $check_appr != false){
            if($payment_invoice->approval_status == 1 && ($check_approve_status == false || $check_approve_status == 'reject')){ ?>
        <a data-toggle="tooltip" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_request_approve(<?php echo html_entity_decode($payment_invoice->id); ?>); return false;"><?php echo _l('send_request_approve_pur'); ?></a>
      <?php } }
        if(isset($check_approve_status['staffid'])){
            ?>
            <?php 
        if(in_array(get_staff_user_id(), $check_approve_status['staffid']) && !in_array(get_staff_user_id(), $get_staff_sign) && $payment_invoice->approval_status == 1){
         ?>
            <a href="#" class="btn btn-success  show_approve" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo app_lang('approve'); ?><span class="caret"></span></a>
                        <div class="modal fade" id="approve_modal" tabindex="-1" role="dialog">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h4 class="modal-title"><?php echo app_lang('approve'); ?></h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body general-form">
                                <?php echo render_textarea1('reason', 'reason'); ?>
                              </div>
                              <div class="modal-footer">

                                <a href="#" class="btn btn-success pull-left display-block  mr-4 button-margin-r-b" data-loading-text="<?php echo app_lang('wait_text'); ?>" onclick="approve_request(<?php echo html_entity_decode($payment_invoice->id); ?>); return false;"><span data-feather="upload" class="icon-16"></span>
                                  <?php echo app_lang('approve'); ?>
                                </a>

                                <a href="#" data-loading-text="<?php echo app_lang('wait_text'); ?>" onclick="deny_request(<?php echo html_entity_decode($payment_invoice->id); ?>); return false;" class="btn btn-warning text-white"><span data-feather="x" class="icon-16"></span><?php echo app_lang('deny'); ?>
                              </a>

                            </div>
                          </div>
                        </div>
                      </div>
          <?php }
            ?>
            
          <?php
           if(in_array(get_staff_user_id(), $check_approve_status['staffid']) && in_array(get_staff_user_id(), $get_staff_sign) && $payment_invoice->approval_status == 1){ ?>
            <button onclick="accept_action();" class="btn btn-success pull-left action-button"><?php echo _l('e_signature_sign'); ?></button>
          <?php }
            ?>
            <?php 
             }
            ?>
          </div>
         </div>
       </div>
     </div>

  </div>
</div>

<div class="modal fade" id="add_action" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         
        <div class="modal-body">
         <p class="bold" id="signatureLabel"><?php echo _l('signature'); ?></p>
            <div class="signature-pad--body border">
              <canvas id="signature" height="130" width="550"></canvas>
            </div>
            <input type="text" class="ip_style d-none" tabindex="-1" name="signature" id="signatureInput">
            <div class="dispay-block">
              <button type="button" class="btn btn-default btn-xs clear" tabindex="-1" onclick="signature_clear();"><?php echo _l('clear'); ?></button>
            
            </div>

          </div>
          <div class="modal-footer">

           <button onclick="sign_request(<?php echo html_entity_decode($payment_invoice->id); ?>);" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" class="btn btn-success"><?php echo _l('e_signature_sign'); ?></button>
          </div>

      </div><!-- /.modal-content -->
   </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php require FCPATH. PLUGIN_URL_PATH . "Purchase/assets/js/invoices/payment_invoice_js.php";  ?>
