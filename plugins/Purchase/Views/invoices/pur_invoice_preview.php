<div id="page-content" class="page-wrapper clearfix">
  <div class="card clearfix">
 		<div class="page-title clearfix">
          <h4 class="no-margin font-bold"><?php echo html_entity_decode($pur_invoice->invoice_number); ?></h4>
      	</div>


    	<?php 
    		$base_currency = get_base_currency();
    		if($pur_invoice->currency != ''){
    			$base_currency = $pur_invoice->currency;
    		}

    		if($base_currency == get_setting('default_currency')){
    			$base_currency = get_setting('currency_symbol');
    		}
    	 ?>
    	<?php echo form_hidden('invoice_id', $pur_invoice->id) ?>

       	<ul data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
          <li>
             <a href="#tab_pur_invoice" class="<?php if($tab == 'tab_pur_invoice'){ echo 'active'; } ?>" data-bs-target="#tab_pur_invoice" role="presentation">
             <?php echo _l('pur_invoice'); ?>
             </a>
          </li>
          <li>
             <a href="#payment_record" class="<?php if($tab == 'payment_record'){ echo 'active'; } ?>" data-bs-target="#payment_record" role="presentation">
             <?php echo _l('payment_record'); ?>
             </a>
          </li>
          <li>
             <a href="#attachment" class="<?php if($tab == 'attachment'){ echo 'active'; } ?>" data-bs-target="#attachment" role="presentation">
             <?php echo _l('attachment'); ?>
             </a>
          </li>       
       </ul>
    
     

     	<div class="row ml5 mr5 mt15">
     		<div class="col-md-3">
     			<?php $class = '';
     			if($pur_invoice->payment_status == 'unpaid'){
     				$class = 'danger';
     			}elseif($pur_invoice->payment_status == 'paid'){
     				$class = 'success';
     			}elseif($pur_invoice->payment_status == 'partially_paid'){
     				$class = 'warning';
     			} ?>
     			<span class="label label-<?php echo html_entity_decode($class); ?> mtop5 s-status invoice-status-3"><?php echo _l($pur_invoice->payment_status); ?></span>
     		</div>
     		<div class="col-md-9 _buttons">
     			<div class="visible-xs">
                  <div class="mtop10"></div>
               	</div>
               	<div class="pull-right">
               		<a href="<?php echo admin_url('purchase/pur_invoice/'.$pur_invoice->id); ?>" data-toggle="tooltip" title="<?php echo _l('edit_invoice'); ?>" class="btn btn-default btn-with-tooltip mright5" data-placement="bottom"><i data-feather='edit' class='icon-16'></i></a>
               		
               		<?php if($user_type == 'staff'){ ?>
	               	   <?php if(purinvoice_left_to_pay($pur_invoice->id) > 0){ ?>

		               	<?php echo modal_anchor(get_uri("purchase/add_payment_modal/". $pur_invoice->id), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('payment'), array("title" => app_lang('payment'), "data-post-id" => $pur_invoice->id, "class" => "btn btn-default")); ?>
		               <?php } ?>
		           <?php } ?>
               	</div>

     		</div>
     	</div>

     	<div class="clearfix"></div>
 		<hr class="hr-panel-heading" />

     	<div class="tab-content">
     		<div role="tabpanel" class="tab-pane ptop10 <?php if($tab == 'tab_pur_invoice'){ echo 'active'; } ?>" id="tab_pur_invoice">
     			<div class="row ml5 mr5">
	     			<div class="col-md-6 pad_left_0">
	     				<div class="col-md-6 pad_left_0 border-right">
	     					<p><?php echo _l('invoice_number').':'; ?><span class="pull-right bold"><?php echo html_entity_decode($pur_invoice->invoice_number); ?></span></p>
	     				</div>
	     				<div class="col-md-6 pad_right_0">
	     					<p><?php echo _l('invoice_date').':'; ?><span class="pull-right bold"><?php echo _d($pur_invoice->invoice_date); ?></span></p>
	     				</div>
	     				<div class="col-md-12 pad_left_0 pad_right_0">
	     					<hr class="mtop5 mbot5">
	     				</div>

	     				<div class="col-md-6  pad_right_0 ">
	     					<p><?php echo _l('pur_due_date').':'; ?><span class="pull-right bold"><?php echo _d($pur_invoice->duedate); ?></span></p>
	     				</div>
	     				<div class="col-md-6 pad_left_0  border-right">
	     					<p><?php echo _l('purchase_order').':'; ?><span class="pull-right bold"><a href="<?php echo get_uri('purchase/view_pur_order/'.$pur_invoice->pur_order); ?>" ><?php echo get_pur_order_subject($pur_invoice->pur_order); ?></a></span></p>
	     					
	     				</div>
	     				<div class="col-md-12 pad_left_0 pad_right_0">
	     					<hr class="mtop5 mbot5">
	     				</div>
	     				
	     				<div class="col-md-6 pad_right_0">
	     					<p><?php echo _l('invoice_amount').':'; ?><span class="pull-right bold"><?php echo to_currency($pur_invoice->total,$base_currency); ?></span></p>
	     				</div>

	     			</div>

	     			<div class="col-md-6">

	     				<div class="col-md-6 pad_left_0 border-right">
	     					<p><?php echo _l('transaction_id').':'; ?><span class="pull-right bold"><?php echo html_entity_decode($pur_invoice->transactionid); ?></span></p>
	     				</div>
	     				<div class="col-md-6 pad_right_0">
	     					<p><?php echo _l('transaction_date').':'; ?><span class="pull-right bold"><?php echo _d($pur_invoice->transaction_date); ?></span></p>
	     				</div>
	     				<div class="col-md-12 pad_left_0 pad_right_0">
	     					<hr class="mtop5 mbot5">
	     				</div>
	     				<div class="col-md-6 pad_left_0 border-right">
	     					<p><?php echo _l('add_from').':'; ?><span class="pull-right bold"><?php echo get_staff_full_name($pur_invoice->add_from); ?></span></p>
	     				</div>
	     				<div class="col-md-6 pad_right_0">
	     					<p><?php echo _l('date_add').':'; ?><span class="pull-right bold"><?php echo _d($pur_invoice->date_add); ?></span></p>
	     				</div>
	     				<div class="col-md-12 pad_left_0 pad_right_0">
	     					<hr class="mtop5 mbot5">
	     				</div>

	     			</div>

	     			<div class="col-md-12 pad_left_0 pad_right_0">
	         			<div class="table-responsive">
	                       <table class="table items items-preview estimate-items-preview" data-type="estimate">
	                          <thead>
	                             <tr>
	      
	                                <th class="description" width="50%" align="left"><?php echo _l('items'); ?></th>
	                                <th align="right" class="text-right"><?php echo _l('purchase_quantity'); ?></th>
	                                <th align="right" class="text-right"><?php echo _l('purchase_unit_price'); ?></th>
	                                <th align="right" class="text-right"><?php echo _l('into_money'); ?></th>
	                                <?php if(get_setting('show_purchase_tax_column') == 1){ ?>
	                                <th align="right" class="text-right"><?php echo _l('tax'); ?></th>
	                                <?php } ?>
	                                <th align="right" class="text-right"><?php echo _l('sub_total'); ?></th>
	                                <th align="right" class="text-right"><?php echo _l('discount(%)'); ?></th>
	                                <th align="right" class="text-right"><?php echo _l('discount(money)'); ?></th>
	                                <th align="right" class="text-right"><?php echo _l('total'); ?></th>
	                             </tr>
	                          </thead>
	                          <tbody class="ui-sortable">

	                             <?php $item_discount = 0;
	                             if(count($invoice_detail) > 0){
	                                $count = 1;
	                                $t_mn = 0;
	                                
	                             foreach($invoice_detail as $es) { ?>
	                             <tr nobr="true" class="sortable">

	                                <td class="description" align="left;"><span><strong><?php 
	                                $item = get_item_hp($es['item_code']); 
	                                if(isset($item) && isset($item->commodity_code) && isset($item->title)){
	                                   echo html_entity_decode($item->commodity_code.' - '.$item->title);
	                                }else{
	                                   echo html_entity_decode($es['item_name']);
	                                }
	                                ?></strong><?php if($es['description'] != ''){ ?><br><span><?php echo html_entity_decode($es['description']); ?></span><?php } ?></td>
	                                <td align="right"  width="12%"><?php echo html_entity_decode($es['quantity']); ?></td>
	                                <td align="right"><?php echo to_currency($es['unit_price'],$base_currency); ?></td>
	                                <td align="right"><?php echo to_currency($es['into_money'],$base_currency); ?></td>
	                                <?php if(get_setting('show_purchase_tax_column') == 1){ ?>
	                                <td align="right"><?php echo to_currency(($es['total'] - $es['into_money']),$base_currency); ?></td>
	                                <?php } ?>
	                                <td class="amount" align="right"><?php echo to_currency($es['total'],$base_currency); ?></td>
	                                <td class="amount" width="12%" align="right"><?php echo ($es['discount_percent'].'%'); ?></td>
	                                <td class="amount" align="right"><?php echo to_currency($es['discount_money'],$base_currency); ?></td>
	                                <td class="amount" align="right"><?php echo to_currency($es['total_money'],$base_currency); ?></td>
	                             </tr>
	                          <?php 
	                          $t_mn += $es['total_money'];
	                          $item_discount += $es['discount_money'];
	                          $count++; } } ?>
	                          </tbody>
	                       </table>
	                    </div>
	                </div>

	                <div class="row">
	                	<div class="col-md-7"></div>
		                <div class="col-md-5 col-md-offset-7 pad_left_0 pad_right_0">
		                    <table class="table text-right">
		                       <tbody>
		                          <tr id="inv_subtotal">
		                             <td><span class="bold"><?php echo _l('subtotal'); ?></span>
		                             </td>
		                             <td class="inv_subtotal">
		                                <?php echo to_currency($pur_invoice->subtotal,$base_currency); ?>
		                             </td>
		                          </tr>

		                          <?php if($tax_data['preview_html'] != ''){
		                            echo html_entity_decode($tax_data['preview_html']);
		                          } ?>


		                          <?php if(($pur_invoice->discount_total + $item_discount) > 0){ ?>
		                          
		                          <tr id="inv_discount_total">
		                             <td><span class="bold"><?php echo _l('discount_total(money)'); ?></span>
		                             </td>
		                             <td class="inv_discount_total">
		                                <?php echo '-'.to_currency(($pur_invoice->discount_total + $item_discount), $base_currency); ?>
		                             </td>
		                          </tr>
		                          <?php } ?>

		                          <?php if($pur_invoice->shipping_fee  > 0){ ?>
		                          
		                          <tr id="inv_discount_total">
		                             <td><span class="bold"><?php echo _l('pur_shipping_fee'); ?></span>
		                             </td>
		                             <td class="inv_discount_total">
		                                <?php echo to_currency($pur_invoice->shipping_fee, $base_currency); ?>
		                             </td>
		                          </tr>
		                          <?php } ?>


		                          <tr id="inv_total">
		                             <td><span class="bold"><?php echo _l('total'); ?></span>
		                             </td>
		                             <td class="inv_total bold">
		                                <?php echo to_currency($pur_invoice->total, $base_currency); ?>
		                             </td>
		                          </tr>
		                       </tbody>
		                    </table>
		                </div>
		            </div>

	     			<div class="col-md-12 pad_left_0 pad_right_0 ">
	 					<p><span class="bold"><?php echo _l('adminnote').': '; ?></span><span><?php echo html_entity_decode($pur_invoice->adminnote); ?></span></p>
	 				</div>
	 				<div class="col-md-12 pad_left_0 pad_right_0">
	 					<hr class="mtop5 mbot5">
	 				</div>
	     			<div class="col-md-12 pad_left_0 pad_right_0 ">
	 					<p><span class="bold"><?php echo _l('vendor_note').': '; ?></span><span><?php echo html_entity_decode($pur_invoice->vendor_note); ?></span></p>
	 				</div>
	 				<div class="col-md-12 pad_left_0 pad_right_0">
	 					<hr class="mtop5 mbot5">
	 				</div>
	 				<div class="col-md-12 pad_left_0 pad_right_0 ">
	 					<p><span class="bold"><?php echo _l('terms').': '; ?></span><span><?php echo html_entity_decode($pur_invoice->terms); ?></span></p>
	 				</div>
 				</div>
     		</div>


          	<div role="tabpanel" class="tab-pane <?php if($tab == 'payment_record'){ echo 'active'; } ?>" id="payment_record">
          		<div class="row ml5 mr5">
	               <div class="col-md-6 pad_left_0" >
	               <h4 class="font-medium mbot15 bold text-success"><?php echo _l('payment_for_pur_invoice').' '.$pur_invoice->invoice_number; ?></h4>
	               </div>
	               
	               <div class="clearfix"></div>

	               <?php if(count($payment) > 0){ ?>
	               	<div class="row ml5 mr5">
		               <table class="table dt-table table-striped">
		                   <thead>
		                     <th><?php echo _l('payments_table_amount_heading'); ?></th>
		                      <th><?php echo _l('payments_table_mode_heading'); ?></th>
		                      <th><?php echo _l('payment_transaction_id'); ?></th>
		                      <th><?php echo _l('payments_table_date_heading'); ?></th>
		                      <th><?php echo _l('approval_status'); ?></th>
		                      <?php if($user_type == 'staff'){ ?>
		                      	<th><?php echo _l('options'); ?></th>
		                      <?php } ?>
		                   </thead>
		                  <tbody>
		                     <?php foreach($payment as $pay) { ?>
		                        <tr>
		                           <td><?php echo to_currency($pay['amount'],$base_currency); ?></td>
		                           <td><?php echo get_payment_mode_by_id($pay['paymentmode']); ?></td>
		                           <td><?php echo html_entity_decode($pay['transactionid']); ?></td>
		                           <td><?php echo _d($pay['date']); ?></td>
		                           <td><?php echo get_status_approve($pay['approval_status']); ?></td>
		                           <?php if($user_type == 'staff'){ ?>
		                           <td>
		       

		                        	<?php
		                        		$view = '<li role="presentation"><a href="'.get_uri('purchase/payment_invoice/'. $pay['id']).'" class="dropdown-item"><i data-feather="eye" class="icon-16"></i>&nbsp;&nbsp;'.app_lang('view').'</a></li>';
							            
							            $delete = '<li role="presentation">' . modal_anchor(get_uri("purchase/delete_payment_pur_invoice_modal"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $pay['id'], "class" => "dropdown-item")) . '</li>';


							            $_data = '
							            <span class="dropdown inline-block">
							            <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
							            <i data-feather="tool" class="icon-16"></i>
							            </button>
							            <ul class="dropdown-menu dropdown-menu-end" role="menu">' .$view .  $delete. '</ul>
							            </span>';

							            echo html_entity_decode($_data);
		                        	 ?>
		                           </td>
		                           <?php } ?>
		                        </tr>
		                     <?php } ?>
		                  </tbody>
		               </table>
		           </div>
		           <?php }else{ ?>
		           	<p class="bold text-muted"><?php echo app_lang('no_payment_has_been_recorded_for_this_invoice'); ?></p>
		           <?php } ?>
	           </div>
            </div>

          	<div role="tabpanel" class="tab-pane <?php if($tab == 'attachment'){ echo 'active'; } ?>" id="attachment">
          		<?php echo form_open_multipart(get_uri('purchase/purchase_invoice_attachment/'.$pur_invoice->id),array('id'=>'partograph-attachments-upload')); ?>
            
          		<div class="row ml15 mr15">
                  <?php echo render_input('file','file','','file'); ?>
               </div>
             
                <div class="modal-footer bor_top_0" >
                   <button id="obgy_btn2" type="submit" class="btn btn-info text-white"><?php echo _l('submit'); ?></button>
               </div>
          
                <?php echo form_close(); ?>
               
               <div class="col-md-12" id="purinv_pv_file">
                                    <?php
                                        $file_html = '';
                                        if(count($pur_invoice_attachments) > 0){
                                            $file_html .= '<hr />';
                                            foreach ($pur_invoice_attachments as $f) {
                                                $href_url = base_url('plugins/Purchase/Uploads/pur_invoice/'.$f['rel_id'].'/'.$f['file_name']).'" download';
                                                                if(!empty($f['external'])){
                                                                  $href_url = $f['external_link'];
                                                                }
                                               $file_html .= '<div class="mb15 ml15 mr15 row" data-attachment-id="'. $f['id'].'">
                                              <div class="col-md-8 d-flex">
                                                   '.modal_anchor(get_uri("purchase/file_pur_invoice/".$f['id']."/".$f['rel_id']), "<i data-feather='eye' class='icon-16'></i>", array("class" => "btn btn-success text-white mr5", "title" => $f['file_name'], "data-post-id" => $f['id'])).'

                                                    <div class="d-block">
                                                     <div class="pull-left"><i class="'. get_mime_class($f['filetype']).'"></i></div>
                                                     <a href=" '. $href_url.'" target="_blank" download>'.$f['file_name'].'</a>
                                                     <br />
                                                    <small class="text-muted">'.$f['filetype'].'</small>
                                                    </div>
                                                   
                                              </div>
                                              <div class="col-md-4 text-right">';
                                                if($f['staffid'] == get_staff_user_id() || is_admin()){
                                                $file_html .= '<a href="#" class="text-danger" onclick="delete_purinv_attachment('. $f['id'].'); return false;"><i data-feather="x" class="icon-16"></i></a>';
                                                } 
                                               $file_html .= '</div></div>';
                                            }
                                            $file_html .= '<hr />';
                                            echo html_entity_decode($file_html);
                                        }
                                     ?>
                                  </div>

               <div id="purinv_file_data"></div>
          	</div>

     	</div>

  </div>
</div>

<div class="modal fade" id="payment_record_pur" tabindex="-1" role="dialog">
    <div class="modal-dialog dialog_30" >
        <?php echo form_open(get_uri('purchase/add_invoice_payment/'.$pur_invoice->id),array('id'=>'purinvoice-add_payment-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_payment'); ?></span>
                    <span class="add-title"><?php echo _l('new_payment'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                     <div id="additional"></div>
                     <?php echo render_input1('amount','amount',purinvoice_left_to_pay($pur_invoice->id),'number',array('max' => purinvoice_left_to_pay($pur_invoice->id))); ?>
                        <?php echo render_date_input1('date','payment_edit_date'); ?>
                        <?php echo render_select1('paymentmode',$payment_modes,array('id','name'),'payment_mode'); ?>
                        
                        <?php echo render_input1('transactionid','payment_transaction_id'); ?>
                        <?php echo render_textarea1('note','note','',array('rows'=>7)); ?>

                    </div>
                </div>
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

<?php require FCPATH. PLUGIN_URL_PATH . "Purchase/assets/js/invoices/pur_invoice_preview_js.php";  ?>