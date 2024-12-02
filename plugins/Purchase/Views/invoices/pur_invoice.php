<div id="page-content" class="page-wrapper clearfix">
  <div class="content">
    <div class="row">

    <?php echo form_open_multipart(admin_url('purchase/pur_invoice_form'),array('id'=>'pur_invoice-form','class'=>' general-form')); ?>
    	<?php
    		if(isset($pur_invoice)){
		        echo form_hidden('isedit');
		    }
    	 ?>
      <div class="col-md-12">
        <div class="panel_s accounting-template estimate">
          <div class="card clearfix">

          	<div class="page-title clearfix">
              <h4 class="no-margin font-bold"><?php echo html_entity_decode($title); ?> <?php if(isset($pur_invoice)){ echo ' '.html_entity_decode($pur_invoice->invoice_number); } ?></h4>
          	</div>

            <div class="row ml5 mr5 mt10">
            	<?php $additional_discount = 0; ?>
                  <input type="hidden" name="additional_discount" value="<?php echo html_entity_decode($additional_discount); ?>">
            	<div class="col-md-6">
            		<?php echo form_hidden('id', (isset($pur_invoice) ? $pur_invoice->id : '') ); ?>

            		<div class="row">
		            	<div class="col-md-6 pad_left_0">
		            		<label for="invoice_number"><span class="text-danger">* </span><?php echo _l('invoice_number'); ?></label>
			            	<?php
		                    $prefix = get_setting('pur_inv_prefix');
		                    $next_number = get_setting('pur_next_inv_number');
		                    $number = (isset($pur_invoice) ? $pur_invoice->number : $next_number);
		                    echo form_hidden('number',$number); ?> 
		                           
		                    <?php $invoice_number = ( isset($pur_invoice) ? $pur_invoice->invoice_number : $prefix.str_pad($next_number,5,'0',STR_PAD_LEFT));
		                    echo render_input1('invoice_number','',$invoice_number ,'text',array('readonly' => '', 'required' => 'true')); ?>
		                </div>

		                <div class="col-md-6 pad_right_0 form-group">
		                	<label for="vendor"><span class="text-danger">* </span><?php echo _l('pur_vendor'); ?></label>
		                    <select name="vendor" id="vendor" class="select2 validate-hidden" <?php if($user_type == 'vendor'){ echo 'disabled="true"'; } ?> onchange="pur_vendor_change(this); return false;" required="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">

		                        <?php foreach($vendors as $ven){ ?>
		                        	<option value="<?php echo html_entity_decode($ven['userid']); ?>" <?php if(isset($pur_invoice) && $pur_invoice->vendor == $ven['userid']){ echo 'selected'; } ?>><?php echo html_entity_decode($ven['vendor_code'].' '.$ven['company']); ?></option>
		                        <?php } ?>
		                    </select>
		                   <?php if($user_type == 'vendor'){ echo form_hidden('vendor', $vendor_id); } ?>
		                </div>
		              
		                <div class="col-md-6 form-group pad_right_0">
		                	<label for="pur_order"><?php echo _l('pur_order'); ?></label>
		                    <select name="pur_order" id="pur_order" class="select2 validate-hidden" onchange="pur_order_change(this); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
		                        <option value="">-</option>
		                        <?php foreach($pur_orders as $ct){ ?>
		                        	<option value="<?php echo html_entity_decode($ct['id']); ?>" <?php if(isset($pur_invoice) && $pur_invoice->pur_order == $ct['id']){ echo 'selected'; } ?>><?php echo html_entity_decode($ct['pur_order_number']); ?></option>
		                        <?php } ?>
		                    </select>
		                </div>

		                <div class="col-md-6 pad_left_0">
		                	<label for="invoice_date"><span class="text-danger">* </span><?php echo _l('invoice_date'); ?></label>
		                	<?php $invoice_date = ( isset($pur_invoice) ? _d($pur_invoice->invoice_date) : _d(date('Y-m-d')) );
		                	 echo render_date_input1('invoice_date','',$invoice_date,array( 'required' => 'true')); ?>
		                </div>

		                
		            </div>
	            </div>

	            <div class="col-md-6">
	            	<div class="row">
		            	<div class="col-md-6 pad_left_0">
	                      <?php
	                                $currency_attr = array();

	                                $selected = (isset($pur_invoice) && $pur_invoice->currency != '') ? $pur_invoice->currency : '';
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

		                <div class="col-md-6 pad_left_0">
		                	<?php $transactionid = ( isset($pur_invoice) ? $pur_invoice->transactionid : '');
		                	echo render_input1('transactionid','transaction_id',$transactionid); ?>
		                </div>
		                <div class="col-md-6 pad_right_0">
		                	<label for="invoice_date"><?php echo _l('pur_due_date'); ?></label>
		                	<?php $duedate = ( isset($pur_invoice) ? _d($pur_invoice->duedate) : _d(date('Y-m-d')) );
		                	 echo render_date_input1('duedate','',$duedate); ?>
		                </div>
		                <div class="col-md-6 pad_right_0">
		                	<?php $transaction_date = ( isset($pur_invoice) ? $pur_invoice->transaction_date : '');
		                	echo render_date_input1('transaction_date','transaction_date',$transaction_date); ?>
		                </div>
		            </div>
	            </div>

            </div>

          </div>



          <div class="card clearfix mt10 invoice-item">

		        <div class="row ml5 mr5 mt10">
		          <div class="col-md-4">
		            <?php echo view('Purchase\Views\item_include\main_item_select'); ?>
		          </div>

				          <?php
				        $base_currency = get_base_currency();

		                $po_currency = $base_currency;
		                if(isset($pur_invoice) && $pur_invoice->currency != 0){
		                  $po_currency = $pur_invoice->currency;
		                } 

		                $from_currency = (isset($pur_invoice) && $pur_invoice->from_currency != null) ? $pur_invoice->from_currency : $base_currency;
		                echo form_hidden('from_currency', $from_currency);

		              ?>
		          <div class="col-md-8 <?php if($po_currency == $base_currency){ echo 'hide'; } ?>" id="currency_rate_div">
		          	<div class="row">
			            <div class="col-md-10 text-right">
			              
			              <p class="mtop10"><?php echo _l('currency_rate'); ?><span id="convert_str"><?php echo ' ('.$base_currency.' => '.$po_currency.'): ';  ?></span></p>
			            </div>
			            <div class="col-md-2 pull-right">
			              <?php $currency_rate = 1;
			                if(isset($pur_invoice) && $pur_invoice->currency != 0){
			                  $currency_rate = $pur_invoice->currency_rate;
			                }
			              echo render_input1('currency_rate', '', $currency_rate, 'number', ['step' => 'any'], [], '', 'text-right'); 
			              ?>
			            </div>
			        </div>
		          </div>
		        </div> 
		        <div class="row ml5 mr5">
		          <div class="col-md-12">
		            <div class="table-responsive s_table ">
		              <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
		                <thead>
		                  <tr>
		                    <th></th>
		                    <th width="12%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('invoice_table_item_heading'); ?></th>
		                    <th width="15%" align="left"><?php echo _l('item_description'); ?></th>
		                    <th width="10%" align="right"><?php echo _l('unit_price'); ?><span class="th_currency"><?php echo '('.$po_currency.')'; ?></span></th>
		                    <th width="10%" align="right" class="qty"><?php echo _l('quantity'); ?></th>
		                    <th width="12%" align="right"><?php echo _l('invoice_table_tax_heading'); ?></th>
		                    <th width="10%" align="right"><?php echo _l('tax_value'); ?><span class="th_currency"><?php echo '('.$po_currency.')'; ?></span></th>
		                    <th width="10%" align="right"><?php echo _l('pur_subtotal_after_tax'); ?><span class="th_currency"><?php echo '('.$po_currency.')'; ?></span></th>
		                    <th width="7%" align="right"><?php echo _l('discount').'(%)'; ?></th>
		                    <th width="10%" align="right"><?php echo _l('discount(money)'); ?><span class="th_currency"><?php echo '('.$po_currency.')'; ?></span></th>
		                    <th width="10%" align="right"><?php echo _l('total'); ?><span class="th_currency"><?php echo '('.$po_currency.')'; ?></span></th>
		                    <th align="center"><i class="fa fa-cog"></i></th>
		                  </tr>
		                </thead>
		                <tbody>
		                  <?php echo html_entity_decode($pur_invoice_row_template); ?>
		                </tbody>
		              </table>
		            </div>
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
				              
				              <tr id="order_discount_percent">
				                <td>
				                  <div class="row">
				                    <div class="col-md-7">
				                      <span class="bold"><?php echo _l('pur_discount'); ?> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo _l('discount_percent_note'); ?>" ></i></span>
				                    </div>
				                    <div class="col-md-3">
				                      <?php $discount_total = isset($pur_invoice) ? $pur_invoice->discount_total : '';
				                      echo render_input('order_discount', '', $discount_total, 'number', ['onchange' => 'pur_calculate_total()', 'onblur' => 'pur_calculate_total()']); ?>
				                    </div>
				                     <div class="col-md-2">
				                        <select name="add_discount_type" id="add_discount_type" class="select2 validate-hidden" onchange="pur_calculate_total(); return false;" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
				                            <option value="percent">%</option>
				                            <option value="amount" selected><?php echo _l('amount'); ?></option>
				                        </select>
				                     </div>
				                  </div>
				                </td>
				                <td class="order_discount_value">

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
				                  <input type="number" onchange="pur_calculate_total()" data-toggle="tooltip" value="<?php if(isset($pur_invoice)){ echo html_entity_decode($pur_invoice->shipping_fee); }else{ echo '0';} ?>" class="form-control pull-left text-right" name="shipping_fee">
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

		        <div class="row">
		          <div class="col-md-12 mtop15">
		             <div class="panel-body bottom-transaction">
		             	<div class="col-md-12 pad_left_0 pad_right_0">
	                	<?php $adminnote = ( isset($pur_invoice) ? $pur_invoice->adminnote : '');
		                	echo render_textarea1('adminnote','adminnote',$adminnote, array('rows' => 7)) ?>
		                </div>

		                <div class="col-md-12 pad_left_0 pad_right_0">
		                	<?php $vendor_note = ( isset($pur_invoice) ? $pur_invoice->vendor_note : get_setting('vendor_note'));
		                	echo render_textarea1('vendor_note','vendor_note',$vendor_note, array('rows' => 7)) ?>
		                </div>
		                <div class="col-md-12 pad_left_0 pad_right_0">
		                	<?php $terms = ( isset($pur_invoice) ? $pur_invoice->terms : get_setting('pur_terms_and_conditions'));
		                	echo render_textarea1('terms','terms',$terms, array('rows' => 7)) ?>
		                </div>

		                <div class=" text-right">
                  
		                  <button type="button" class="text-white btn-tr save_detail_inv btn btn-info mleft10 ">
		                  <?php echo _l('submit'); ?>
		                  </button>
		                </div>

		             </div>
		               <div class="btn-bottom-pusher"></div>
		          </div>
		        </div>
      	</div>

      	<?php echo form_close(); ?>
  	</div>
  </div>
</div>
<?php require FCPATH. PLUGIN_URL_PATH . "Purchase/assets/js/invoices/pur_invoice_js.php";  ?>