<div id="page-content" class="page-wrapper clearfix">
	<div class="content">
		<div class="row">
			<?php
			echo form_open(uri_string(),array('id'=>'pur_estimate-form','class'=>' general-form'));
			if(isset($estimate)){
				echo form_hidden('isedit');
			}
			?>
			<div class="col-md-12">
				<div class="panel_s accounting-template estimate">
				   <div class="card clearfix">
				      <div class="page-title clearfix">
					        <h4 class="no-margin font-bold"><?php if(isset($estimate)){ echo format_pur_estimate_number($estimate->id); }else{ echo html_entity_decode($title) ; } ?></h4>
					    </div>
				      <div class="row ml15 mr15 mt1">
				         <div class="col-md-6 pleft0">
				         	<div class="row">
				            <?php $additional_discount = 0; ?>
				            <input type="hidden" name="additional_discount" value="<?php echo html_entity_decode($additional_discount); ?>">
				            <div class="col-md-6 form-group">
				              <label for="vendor"><?php echo _l('vendor'); ?></label>
				              <select name="vendor" id="vendor" class="select2 validate-hidden" <?php if($user_type == 'vendor'){ echo 'disabled="true"'; } ?> required="true" onchange="estimate_by_vendor(this); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >

				                  <?php foreach($vendors as $s) { ?>
				                  <option value="<?php echo html_entity_decode($s['userid']); ?>" <?php if(isset($estimate) && $estimate->vendor == $s['userid']){ echo 'selected'; } ?>><?php echo html_entity_decode($s['company']); ?></option>
				                    <?php } ?>
				              </select>
				     		  
				     		  <?php if($user_type == 'vendor'){ echo form_hidden('vendor', $vendor_id); } ?>
				            </div>
				            <div class="col-md-6 form-group">
				              <label for="pur_request"><?php echo _l('pur_request'); ?></label>
				              <select name="pur_request" id="pur_request" onchange="coppy_pur_request(); return false;" class="select2 validate-hidden"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
				                <option value="">-</option>
				                  <?php foreach($pur_request as $s) { ?>
				                  <option value="<?php echo html_entity_decode($s['id']); ?>" <?php if(isset($estimate) && $estimate->pur_request != '' && $estimate->pur_request->id == $s['id']){ echo 'selected'; } ?> ><?php echo html_entity_decode($s['pur_rq_code'].' - '.$s['pur_rq_name']); ?></option>
				                    <?php } ?>
				              </select>
				             </div>

				            <?php
				               $next_estimate_number = max_number_estimates()+1;
				               $format = get_setting('estimate_number_format');

				                if(isset($estimate)){
				                  $format = $estimate->number_format;
				                }

				               	$prefix = get_setting('pur_estimate_prefix');

				               
				                 $__number = $next_estimate_number;
				                 if(isset($estimate)){
				                   $__number = $estimate->number;
				                   $prefix = '<span id="prefix">' . $estimate->prefix . '</span>';
				                 }
				               

				               $_estimate_number = str_pad($__number, 5, '0', STR_PAD_LEFT);
				               $isedit = isset($estimate) ? 'true' : 'false';
				               $data_original_number = isset($estimate) ? $estimate->number : 'false';
				               ?>
				            <div class="col-md-6">
				              <div class="form-group">
				                 <label for="number"><?php echo _l('estimate_add_edit_number'); ?></label>
				                 <div class="input-group">
				                    <span class="input-group-addon">
				                    <?php if(isset($estimate)){ ?>
				                    <a href="#" onclick="return false;" data-toggle="popover" data-container='._transaction_form' data-html="true" data-content="<label class='control-label'><?php echo _l('settings_sales_estimate_prefix'); ?></label><div class='input-group'><input name='s_prefix' type='text' class='form-control' value='<?php echo html_entity_decode($estimate->prefix); ?>'></div><button type='button' onclick='save_sales_number_settings(this); return false;' data-url='<?php echo admin_url('estimates/update_number_settings/'.$estimate->id); ?>' class='btn btn-info btn-block mtop15'><?php echo _l('submit'); ?></button>"><i class="fa fa-cog"></i></a>
				                     <?php }
				                      echo html_entity_decode($prefix);
				                    ?>
				                   </span>
				                    <input type="text" name="number" class="form-control" value="<?php echo html_entity_decode($_estimate_number); ?>" data-isedit="<?php echo html_entity_decode($isedit); ?>" data-original-number="<?php echo html_entity_decode($data_original_number); ?>">
				                   
				                 </div>
				              </div>
				            </div>
				            <div class="col-md-6">
				                         <?php
				                        $selected = '';
				                        foreach($staffs as $member){
				                         if(isset($estimate)){
				                           if($estimate->buyer == $member['id']) {
				                             $selected = $member['id'];
				                           }
				                         }elseif($member['id'] == get_staff_user_id1()){
				                          $selected = $member['id'];
				                         }
				                        }
				                        echo render_select1('buyer',$staffs,array('id','text'),'buyer',$selected);
				                        ?>
				            </div>
				            
				            <div class="clearfix mbot15"></div>
				            <?php $rel_id = (isset($estimate) ? $estimate->id : false); ?>
				            </div>
				         </div>
				         <div class="col-md-6">
				            
				              
				               <div class="row">
				                  <div class="col-md-12">
				                     <?php
			                              $currency_attr = array();

			                              $selected = (isset($estimate) && $estimate->currency != '') ? $estimate->currency : '';
			                              if($selected == ''){
			                                foreach($currencies as $currency){
			                                  if( $currency['text'] == get_setting('default_currency')){
			                                    $selected = $currency['id'];
			                                  }
			                                }
			                              }
			                              ?>
			                           <?php echo render_select1('currency', $currencies, array('id','text'), 'invoice_add_edit_currency', $selected, $currency_attr,  [], '', '', false); ?>
				                  </div>
				                  <div class="col-md-6">
				                  <?php $value = (isset($estimate) ? _d($estimate->date) : _d(date('Y-m-d'))); ?>
				                  <?php echo render_date_input1('date','estimate_add_edit_date',$value); ?>
				               </div>
				               <div class="col-md-6">
				                  <?php
				                  $value = '';
				                  if(isset($estimate)){
				                    $value = format_to_date($estimate->expirydate);
				                  }else{
				                  	$value = format_to_date(date('Y-m-d'));
				                  } 
				                  echo render_date_input1('expirydate','expiry_date',$value); ?>
				               </div>
				                 
				               </div>
				            
				         </div>

				               
				           
				      </div>
				   </div>

				   <div class="card clearfix mtop10 invoice-item">
				  <div class="row ml15 mr15 mt10">
				    <div class="col-md-4">
				      <?php echo view('Purchase\Views\item_include\main_item_select'); ?>
				    </div>
				    <?php
				          $estimate_currency = $base_currency;
				          if(isset($estimate) && $estimate->currency != '' && $estimate->currency != 0){
				            $estimate_currency = $estimate->currency;
				          } 

				          $from_currency = (isset($estimate) && $estimate->from_currency != null) ? $estimate->from_currency : $base_currency;
				          echo form_hidden('from_currency', $from_currency);

				        ?>
				    <div class="col-md-8 <?php if($estimate_currency == $base_currency){ echo 'hide'; } ?>" id="currency_rate_div">
				    	<div class="row">
					      <div class="col-md-10 text-right">
					        
					        <p class="mtop10"><?php echo _l('currency_rate'); ?><span id="convert_str"><?php echo ' ('.$base_currency.' => '.$estimate_currency.'): ';  ?></span></p>
					      </div>
					      <div class="col-md-2 pull-right">
					        <?php $currency_rate = 1;
					          if(isset($estimate) && $estimate->currency != 0){
					            $currency_rate = $estimate->currency_rate;
					          }
					        echo render_input1('currency_rate', '', $currency_rate, 'number', [], [], '', 'text-right'); 
					        ?>
					      </div>
					  </div>
				    </div>
				  </div>

				  <div class="row ml15 mr15">
				   <div class="col-md-12">
				    <div class="table-responsive s_table ">
				        <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
				          <thead>
				            <tr>
				              <th></th>
				              <th width="20%" align="left" class="th-item"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('invoice_table_item_heading'); ?></th>
				              <th width="10%" align="right" class="text-right"><?php echo _l('unit_price'); ?><span class="th_currency"><?php echo '('.$estimate_currency.')'; ?></span></th>
				              <th width="10%" align="right" class="text-right" class="qty"><?php echo _l('quantity'); ?></th>
				              <th width="10%" align="right" class="text-right"><?php echo _l('subtotal_before_tax'); ?><span class="th_currency"><?php echo '('.$estimate_currency.')'; ?></span></th>
				              <th width="12%" align="right" class="text-right"><?php echo _l('invoice_table_tax_heading'); ?></th>
				              <th width="10%" align="right" class="text-right"><?php echo _l('tax_value'); ?><span class="th_currency"><?php echo '('.$estimate_currency.')'; ?></span></th>
				              <th width="10%" align="right" class="text-right"><?php echo _l('pur_subtotal_after_tax'); ?><span class="th_currency"><?php echo '('.$estimate_currency.')'; ?></span></th>
				              <th width="7%" align="right" class="text-right"><?php echo _l('discount').'(%)'; ?></th>
				              <th width="10%" align="right" class="text-right"><?php echo _l('discount(money)'); ?><span class="th_currency"><?php echo '('.$estimate_currency.')'; ?></span></th>
				              <th width="10%" align="right" class="text-right"><?php echo _l('total'); ?><span class="th_currency"><?php echo '('.$estimate_currency.')'; ?></span></th>
				              <th align="right"  class="text-right"><i data-feather='settings' class='icon-16'></i></th>
				            </tr>
				          </thead>
				          <tbody>
				            <?php echo html_entity_decode($pur_quotation_row_template); ?>
				          </tbody>
				        </table>
				      </div>
				      	<div class="row">
				      		<div class="col-md-4"></div>
						     <div class="col-md-8 col-md-offset-4">
						      <table class="table text-right">
						        <tbody>
						          <tr id="subtotal">
						            <td><span class="bold"><?php echo _l('subtotal'); ?> :</span>
						              <?php echo form_hidden('total_mn', ''); ?>
						            </td>
						            <td class="wh-subtotal">
						            </td>
						          </tr>
						          <tr id="total_discount">
						            <td><span class="bold"><?php echo _l('total_discount'); ?> :</span>
						              <?php echo form_hidden('dc_total', ''); ?>
						            </td>
						            <td class="wh-total_discount">
						            </td>
						          </tr>

						          <tr>
						            <td>
						             <div class="row">
						              <div class="col-md-9">
						               <span class="bold"><?php echo _l('pur_shipping_fee'); ?></span>
						             </div>
						             <div class="col-md-3">
						              <input type="number" onchange="pur_calculate_total()" data-toggle="tooltip" value="<?php if(isset($estimate)){ echo html_entity_decode($estimate->shipping_fee); }else{ echo '0';} ?>" class="form-control pull-left text-right" name="shipping_fee">
						            </div>
						          </div>
						          </td>
						          <td class="shiping_fee">
						          </td>
						          </tr>
						          
						          <tr id="totalmoney">
						            <td><span class="bold"><?php echo _l('grand_total'); ?> :</span>
						              <?php echo form_hidden('grand_total', ''); ?>
						            </td>
						            <td class="wh-total">
						            </td>
						          </tr>
						        </tbody>
						      </table>
						    </div>
						</div>
				    <div id="removed-items"></div>
				    </div>
				    </div>
				   </div>
				   <div class="row">
				      <div class="col-md-12 mtop15">
				         <div class="card clearfix bottom-transaction">
				         	<div class="row ml15 mr15 mt10">
					            <?php $value = (isset($estimate) ? $estimate->vendornote : get_setting('vendor_note')); ?>
					            <?php echo render_textarea1('vendornote','estimate_add_edit_vendor_note',$value,array(),array(),'mtop15'); ?>
					            <?php $value = (isset($estimate) ? $estimate->terms : get_setting('pur_terms_and_conditions')); ?>
					            <?php echo render_textarea1('terms','terms_and_conditions',$value,array(),array(),'mtop15'); ?>
					        </div>
				            <div class="btn-bottom-toolbar text-right">
				              
				              <button type="submit" class="save_detail btn btn-info mr-5  text-white">
				              <?php echo _l('submit'); ?>
				              </button>
				            </div>
				         </div>
				           <div class="btn-bottom-pusher"></div>
				      </div>
				   </div>
				</div>

			</div>
			<?php echo form_close(); ?>
			
		</div>
	</div>
</div>
</div>

<?php require('plugins/Purchase/assets/js/quotations/estimate_js.php'); ?>