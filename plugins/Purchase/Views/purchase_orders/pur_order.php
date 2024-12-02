
<div id="page-content" class="page-wrapper clearfix">
  <div class="content">    <div class="row">
      <?php
      echo form_open(uri_string(),array('id'=>'pur_order-form','class'=>'_pur_order_form general-form', 'role'=>"form" ));
      if(isset($pur_order)){
        echo form_hidden('isedit');
      }
      ?>
      <div class="col-md-12">
        <div class="panel_s accounting-template estimate">
        <div class="card clearfix">
            
            <div class="page-title clearfix">
                  <h4 class="no-margin font-bold"><?php if(isset($estimate)){ echo format_pur_estimate_number($estimate->id); }else{ echo html_entity_decode($title) ; } ?></h4>
              </div>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="general_infor">
                <div class="row ml15 mr15 mt10">
                  <?php $additional_discount = 0; ?>
                  <input type="hidden" name="additional_discount" value="<?php echo html_entity_decode($additional_discount); ?>">

                   <div class="col-md-6">
                      <div class="row">
                        <div class="col-md-6 form-group">
                            <?php $prefix = get_setting('pur_order_prefix');
                                $next_number = get_setting('next_purchase_order_number');

                          $pur_order_number = (isset($pur_order) ? $pur_order->pur_order_number : $prefix.'-'.str_pad($next_number,5,'0',STR_PAD_LEFT).'-'.date('M-Y'));      
                          if(get_setting('po_only_prefix_and_number') == 1){
                             $pur_order_number = (isset($pur_order) ? $pur_order->pur_order_number : $prefix.'-'.str_pad($next_number,5,'0',STR_PAD_LEFT));
                          }      
                            
                          
                          $number = (isset($pur_order) ? $pur_order->number : $next_number);
                          echo form_hidden('number',$number); ?> 
                          
                          <label for="pur_order_number"><?php echo _l('pur_order_number'); ?></label>
                          
                              <input type="text" readonly class="form-control" name="pur_order_number" value="<?php echo html_entity_decode($pur_order_number); ?>">          
                        </div>

                        <div class="col-md-6">
                          <label for="pur_order_name"><span class="text-danger">* </span><?php echo _l('pur_order_description'); ?></label>
                          <?php $pur_order_name = (isset($pur_order) ? $pur_order->pur_order_name : $pur_order_number);
                          echo render_input1('pur_order_name','',$pur_order_name,'', ['required='=>true]); ?>
                
                        </div>
                        
                      </div>
                      
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="vendor"><span class="text-danger">* </span><?php echo _l('vendor'); ?></label>
                            <select name="vendor" id="vendor" class="select2" required="true" onchange="estimate_by_vendor(this); return false;" >
        
                                <?php foreach($vendors as $s) { ?>
                                <option value="<?php echo html_entity_decode($s['userid']); ?>" <?php if(isset($pur_order) && $pur_order->vendor == $s['userid']){ echo 'selected'; } ?>><?php echo html_entity_decode($s['company']); ?></option>
                                  <?php } ?>
                            </select>  
                          </div>
                        </div>

                        <div class="col-md-6 form-group">
                        <label for="pur_request"><?php echo _l('pur_request'); ?></label>
                        <select name="pur_request" id="pur_request" class="select2 validate-hidden" onchange="coppy_pur_request(); return false;"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                          <option value="">-</option>
                            <?php foreach($pur_request as $s) { ?>
                            <option value="<?php echo html_entity_decode($s['id']); ?>" <?php if(isset($pur_order) && $pur_order->pur_request != '' && $pur_order->pur_request == $s['id']){ echo 'selected'; } ?> ><?php echo html_entity_decode($s['pur_rq_code'].' - '.$s['pur_rq_name']); ?></option>
                              <?php } ?>
                        </select>
                       </div>
                        

                      </div>

                      <div class="row">
                        <?php if(get_setting('purchase_order_setting') == 0 ){ ?>
                          <div class="col-md-6 form-group">
                            <label for="estimate"><?php echo _l('estimates'); ?></label>
                            <select name="estimate" id="estimate" class="select2 validate-hidden" onchange="coppy_pur_estimate(); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                              <option value="">-</option>
                              <?php foreach($estimates as $s) { ?>
                            <option value="<?php echo html_entity_decode($s['id']); ?>" <?php if(isset($pur_order) && $pur_order->estimate != '' && $pur_order->estimate == $s['id']){ echo 'selected'; } ?> ><?php echo format_pur_estimate_number($s['id']); ?></option>
                              <?php } ?>
                            </select>
                            
                          </div>
                      <?php } ?>
                      <div class="col-md-<?php if(get_setting('purchase_order_setting') == 1 ){ echo '12' ;}else{ echo '6' ;} ;?> form-group">
                        <label for="department"><?php echo _l('department'); ?></label>
                          <select name="department" id="department" class="select2 validate-hidden" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                            <option value="">-</option>
                            <?php foreach($departments as $s) { ?>
                              <option value="<?php echo html_entity_decode($s['id']); ?>" <?php if(isset($pur_order) && $s['id'] == $pur_order->department){ echo 'selected'; } ?>><?php echo html_entity_decode($s['title']); ?></option>
                              <?php } ?>
                          </select>
                        </div>
                      </div>

                  
                      <div class="row">
                        <div class="col-md-6 form-group">
                          <label for="project"><?php echo _l('project'); ?></label>
                            <select name="project" id="project" class="select2 validate-hidden" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                              <option value="">-</option>
                              <?php foreach($projects as $s) { ?>
                                <option value="<?php echo html_entity_decode($s['id']); ?>" <?php if(isset($pur_order) && $s['id'] == $pur_order->project){ echo 'selected'; }else if(!isset($pur_order) && $s['id'] == $project_id){ echo 'selected'; } ?>><?php echo html_entity_decode($s['title']); ?></option>
                                <?php } ?>
                            </select>
                          </div>

                        <div class="col-md-6 form-group">
                          <label for="type"><?php echo _l('type'); ?></label>
                            <select name="type" id="type" class="select2 validate-hidden" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                              <option value=""></option>
                              <option value="capex" <?php if(isset($pur_order) && $pur_order->type == 'capex'){ echo 'selected';} ?>><?php echo _l('capex'); ?></option>
                              <option value="opex" <?php if(isset($pur_order) && $pur_order->type == 'opex'){ echo 'selected';} ?>><?php echo _l('opex'); ?></option>
                            </select>
                        </div>
                      </div>
                     
                   </div>
                   <div class="col-md-6">
                      <div class="row">
                        <div class="col-md-6 ">
                             <?php
                                $currency_attr = array();

                                $selected = (isset($pur_order) && $pur_order->currency != '') ? $pur_order->currency : '';
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
                            <?php $order_date = (isset($pur_order) ? _d($pur_order->order_date) : _d(date('Y-m-d')));
                            echo render_date_input('order_date','order_date',$order_date); ?>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-md-6 pright0">
                              <?php
                            $selected = '';
                            foreach($staff as $member){
                             if(isset($pur_order)){
                               if($pur_order->buyer == $member['id']) {
                                 $selected = $member['id'];
                               }
                             }elseif($member['id'] == get_staff_user_id1()){
                              $selected = $member['id'];
                             }
                            }
                            echo render_select1('buyer',$staff,array('id','text'),'person_in_charge',$selected);
                            ?>
                          </div>
                          
                          <div class="col-md-6 pright0">
                            <?php $delivery_date = (isset($pur_order) ? _d($pur_order->delivery_date) : '');
                             echo render_date_input('delivery_date','delivery_date',$delivery_date); ?>
                          </div>
                        </div>

                        <div class="row">

                          <?php $clients_ed = (isset($pur_order) ? explode(',',$pur_order->clients) : []); ?>
                          <div class="col-md-6 form-group">
                            <label for="clients"><?php echo _l('clients'); ?></label>
                            <select name="clients[]" id="clients" class="select2 validate-hidden" data-live-search="true" multiple data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >

                                <?php foreach($clients as $s) { ?>
                                <option value="<?php echo html_entity_decode($s['id']); ?>" <?php if(isset($pur_order) && in_array($s['id'], $clients_ed)){ echo 'selected'; } ?>><?php echo html_entity_decode($s['company_name']); ?></option>
                                  <?php } ?>
                            </select>
                          </div>

                          <div class="col-md-6 form-group pright0">
                          <label for="sale_invoice"><?php echo _l('sale_invoice'); ?></label>
                            <select name="sale_invoice" id="sale_invoice" class="select2 validate-hidden" onchange="coppy_sale_invoice(); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                              <option value="">-</option>
                              <?php foreach($invoices as $inv) { ?>
                                <option value="<?php echo html_entity_decode($inv['id']); ?>" <?php if(isset($pur_order) && $inv['id'] == $pur_order->sale_invoice){ echo 'selected'; } ?>><?php echo get_invoice_id($inv['id']); ?></option>
                                <?php } ?>
                            </select>
                          </div>

                          
                        </div>
                      
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
                $po_currency = $base_currency;
                if(isset($pur_order) && $pur_order->currency != ''){
                  $po_currency = $pur_order->currency;
                } 

                $from_currency = (isset($pur_order) && $pur_order->from_currency != null) ? $pur_order->from_currency : $base_currency;
                echo form_hidden('from_currency', $from_currency);

              ?>
          <div class="col-md-8 <?php if($po_currency == $base_currency){ echo 'hide'; } ?>" id="currency_rate_div">
            <div class="row">
                <div class="col-md-10 text-right">
                  
                  <p class="mtop10"><?php echo _l('currency_rate'); ?><span id="convert_str"><?php echo ' ('.$base_currency.' => '.$po_currency.'): ';  ?></span></p>
                </div>
                <div class="col-md-2 pull-right">
                  <?php $currency_rate = 1;
                    if(isset($pur_order) && $pur_order->currency != ''){
                      $currency_rate = $pur_order->currency_rate;
                    }
                  echo render_input('currency_rate', '', $currency_rate, 'number', ['step' => 'any'], [], '', 'text-right'); 
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
                    <th width="12%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('invoice_table_item_heading'); ?></th>
                    <th width="15%" align="left"><?php echo _l('item_description'); ?></th>
                    <th width="10%" align="right" class="text-right"><?php echo _l('unit_price'); ?><span class="th_currency"><?php echo '('.$po_currency.')'; ?></span></th>
                    <th width="10%" align="right" class="qty text-right"><?php echo _l('quantity'); ?></th>
                    <th width="10%" align="right" class="text-right"><?php echo _l('invoice_table_tax_heading'); ?></th>
                    <th width="10%" align="right" class="text-right"><?php echo _l('tax_value'); ?><span class="th_currency"><?php echo '('.$po_currency.')'; ?></span></th>
                    <th width="10%" align="right" class="text-right"><?php echo _l('pur_subtotal_after_tax'); ?><span class="th_currency"><?php echo '('.$po_currency.')'; ?></span></th>
                    <th width="7%" align="right" class="text-right"><?php echo _l('discount').'(%)'; ?></th>
                    <th width="10%" align="right" class="text-right"><?php echo _l('discount'); ?><span class="th_currency"><?php echo '('.$po_currency.')'; ?></span></th>
                    <th width="10%" align="right" class="text-right"><?php echo _l('total'); ?><span class="th_currency"><?php echo '('.$po_currency.')'; ?></span></th>
                    <th align="center"><i class="fa fa-cog"></i></th>
                  </tr>
                </thead>
                <tbody>
                  <?php echo html_entity_decode($pur_order_row_template); ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="row">
              <div class="col-md-4"></div>
             <div class="col-md-8">
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
                          <?php $discount_total = isset($pur_order) ? $pur_order->discount_total : '';
                          echo render_input1('order_discount', '', $discount_total, 'number', ['onchange' => 'pur_calculate_total()', 'onblur' => 'pur_calculate_total()']); ?>
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
                      <input type="number" onchange="pur_calculate_total()" data-toggle="tooltip" value="<?php if(isset($pur_order)){ echo html_entity_decode($pur_order->shipping_fee); }else{ echo '0';} ?>" class="form-control pull-left text-right" name="shipping_fee">
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
             <div class="card clearfix bottom-transaction">
                <div class="row ml15 mr15 mt10">
                  <?php $value = (isset($pur_order) ? $pur_order->vendornote : get_setting('vendor_note')); ?>
                  <?php echo render_textarea1('vendornote','estimate_add_edit_vendor_note',$value,array(),array(),'mtop15'); ?>
                  <?php $value = (isset($pur_order) ? $pur_order->terms :  get_setting('pur_terms_and_conditions')); ?>
                  <?php echo render_textarea1('terms','terms_and_conditions',$value,array(),array(),'mtop15'); ?>
                </div>

                <div class="col-md-12 text-right ml15 mr15 mb10">
                  
                  <button type="button" class="btn-tr btn po_submit btn-info mr-5 text-white ">
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


<?php require('plugins/Purchase/assets/js/purchase_orders/pur_order_js.php'); ?>
