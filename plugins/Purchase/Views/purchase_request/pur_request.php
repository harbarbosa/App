<div id="page-content" class="page-wrapper clearfix">
  <?php echo form_open(uri_string(),array('id'=>'add_edit_pur_request-form','class'=>'general-form ')); ?>
   <div class="card clearfix">
     <div class="page-title clearfix">
        <h4 class="no-margin font-bold"><?php if(isset($pur_request)){ echo html_entity_decode($pur_request->pur_rq_code); }else{ echo html_entity_decode($title). ' '._l('purchase_request') ; } ?></h4>
    </div>
    <div class="modal-body clearfix">

                          <?php 

                  if(isset($pur_request)){
                           echo form_hidden('isedit');
                        }?>
                <div class=" accounting-template">


                  
                      <div class="row">
                        <div class="col-md-6">
                         <?php
                            $prefix = get_setting('pur_request_prefix');
                            $next_number = get_setting('next_purchase_request_number');
                            $number = (isset($pur_request) ? $pur_request->number : $next_number);
                            echo form_hidden('number',$number); ?> 
                             
                        <?php $pur_rq_code = ( isset($pur_request) ? $pur_request->pur_rq_code : $prefix.'-'.str_pad($next_number,5,'0',STR_PAD_LEFT).'-'.date('Y'));
                        echo render_input('pur_rq_code','pur_rq_code',$pur_rq_code ,'text',array('readonly' => '')); ?>
                      </div>
                      <div class="col-md-6">
                        <?php $pur_rq_name = ( isset($pur_request) ? $pur_request->pur_rq_name : $pur_rq_code);
                        echo render_input('pur_rq_name','pur_rq_name', $pur_rq_name); ?>
                      </div>
                    </div>
                    <?php 
                      $project_id = '';
                      /*if($this->input->get('project')){
                        $project_id = $this->input->get('project'); 
                      }*/
                    ?>
                     <div class="row ">
                        <div class="col-md-3 form-group">
                        <label for="department"><?php echo _l('department'); ?></label>
                          <select name="department" id="department" class="select2 validate-hidden" onchange="department_change(this); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                            <option value="">-</option>
                            <?php foreach($departments as $s) { ?>
                              <option value="<?php echo html_entity_decode($s['id']); ?>" <?php if(isset($pur_request) && $s['id'] == $pur_request->department){ echo 'selected'; } ?>><?php echo html_entity_decode($s['title']); ?></option>
                              <?php } ?>
                          </select>
                     
                      </div>

                        
                        <div class="col-md-3 form-group">
                          <label for="sale_estimate"><?php echo _l('sale_estimate'); ?></label>
                            <select name="sale_estimate" id="sale_estimate" onchange="coppy_sale_estimate(); return false;" class="select2 validate-hidden" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                              <option value="">-</option>
                              <?php foreach($salse_estimates as $s) { ?>
                                <option value="<?php echo html_entity_decode($s['id']); ?>" <?php if(isset($pur_request) && $s['id'] == $pur_request->sale_estimate){ echo 'selected'; } ?>><?php echo get_estimate_id($s['id']); ?></option>
                                <?php } ?>
                            </select>
                     
                        </div>

                          <div class="col-md-3 form-group">
                            <label for="type"><?php echo _l('type'); ?></label>
                              <select name="type" id="type" class="select2 validate-hidden" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                                <option value="">-</option>
                                <option value="capex" <?php if(isset($pur_request) && $pur_request->type == 'capex'){ echo 'selected';} ?>><?php echo _l('capex'); ?></option>
                                <option value="opex" <?php if(isset($pur_request) && $pur_request->type == 'opex'){ echo 'selected';} ?>><?php echo _l('opex'); ?></option>
                              </select>
                             
                          </div>

                          <div class="col-md-3 ">
                           <?php
                              $currency_attr = array();

                              $selected = (isset($pur_request) && $pur_request->currency != '') ? $pur_request->currency : '';
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
                      
                  </div>

                  <div class="row">
                    <div class="col-md-3 form-group">
                        <label for="project"><?php echo _l('project'); ?></label>
                          <select name="project" id="project" class="select2 validate-hidden" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                            <option value="">-</option>
                            <?php foreach($projects as $s) { ?>
                              <option value="<?php echo html_entity_decode($s['id']); ?>" <?php if(isset($pur_request) && $s['id'] == $pur_request->project){ echo 'selected'; }else if(!isset($pur_request) && $s['id'] == $project_id ){ echo 'selected';  } ?>><?php echo html_entity_decode($s['title']); ?></option>
                              <?php } ?>
                          </select>
                       
                      </div>


                    <div class="col-md-3 form-group ">
                      <label for="sale_invoice"><?php echo _l('sale_invoice'); ?></label>
                        <select name="sale_invoice" onchange="coppy_sale_invoice(); return false;" id="sale_invoice" class="select2 validate-hidden"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value="">-</option>
                          <?php foreach($invoices as $inv) { ?>
                            <option value="<?php echo html_entity_decode($inv['id']); ?>" <?php if(isset($pur_request) && $inv['id'] == $pur_request->sale_invoice){ echo 'selected'; } ?>><?php echo get_invoice_id($inv['id']); ?></option>
                            <?php } ?>
                        </select>
                        
                    </div>
                 

                    <div class="col-md-3 form-group">
                      <label for="requester"><?php echo _l('requester'); ?></label>
                        <select name="requester" id="requester" class="select2 validate-hidden" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value="">-</option>
                          <?php foreach($staffs as $s) { ?>
                            <option value="<?php echo html_entity_decode($s['id']); ?>" <?php if(isset($pur_request) && $s['id'] == $pur_request->requester){ echo 'selected'; }elseif($s['id'] == get_staff_user_id()){ echo 'selected'; } ?>><?php echo html_entity_decode($s['text'] ); ?></option>
                            <?php } ?>
                        </select>
                      
                    </div>
                    
                    <div class="col-md-3 form-group">
                      <label for="send_to_vendors"><?php echo _l('pur_send_to_vendors'); ?></label>
                      <select name="send_to_vendors[]" id="send_to_vendors" class="select2 validate-hidden" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                          <?php
                            if(isset($pur_request)) {
                              $vendors_arr = explode(',', $pur_request->send_to_vendors);
                            }
                          ?>

                          <?php foreach($vendors as $s) { ?>
                          <option value="<?php echo html_entity_decode($s['userid']); ?>" <?php if(isset($pur_request) && in_array($s['userid'], $vendors_arr)){ echo 'selected';  } ?> ><?php echo html_entity_decode($s['company']); ?></option>
                            <?php } ?>
                      </select>  
                    </div>
                  </div>
                    <div class="col-md-12">
                      <?php $rq_description = ( isset($pur_request) ? $pur_request->rq_description : '');
                      echo render_textarea1('rq_description','rq_description',$rq_description); ?>
                    </div>
                  </div>
                 
                    
      </div>
      
   </div>

   <div class="row mt3">
      <div class="col-md-12">
      <div class="card clearfix">
         <div class="modal-body clearfix">
            <div class="mtop10 invoice-item">
              
              
                <div class="row">
                  <div class="col-md-4">
                    <?php echo view('Purchase\Views\item_include\main_item_select'); ?>
                  </div>

                  <?php
                        $pur_request_currency = $base_currency;
                        if(isset($pur_request) && $pur_request->currency != ''){
                          $pur_request_currency = $pur_request->currency;
                        } 

                        $from_currency = (isset($pur_request) && $pur_request->from_currency != null) ? $pur_request->from_currency : $base_currency;
                        echo form_hidden('from_currency', $from_currency);

                      ?>
                  <div class="col-md-8 <?php if($pur_request_currency == $base_currency){ echo 'hide'; } ?>" id="currency_rate_div">
                    <div class="row">
                      <div class="col-md-10 text-right">
                        
                        <p class="mtop10"><?php echo _l('currency_rate'); ?><span id="convert_str"><?php echo ' ('.$base_currency.' => '.$pur_request_currency.'): ';  ?></span></p>
                      </div>
                      <div class="col-md-2 pull-right">
                        <?php $currency_rate = 1;
                          if(isset($pur_request) && $pur_request->currency != ''){
                            $currency_rate = $pur_request->currency_rate;
                          }
                        echo render_input1('currency_rate', '', $currency_rate, 'number', [], [], '', 'text-right'); 
                        ?>
                      </div>
                    </div>
                  </div>

                </div>

                <div class="table-responsive s_table ">
                  <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                    <thead>
                      <tr>
                        <th></th>
                        <th width="25%" align="left" class="th-item"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('debit_note_table_item_heading'); ?></th>
                        <th width="10%" align="right" class="text-right"><?php echo _l('unit_price'); ?><span class="th_currency"><?php echo '('.$pur_request_currency.')'; ?></span></th>
                        <th width="10%" align="right" class="qty text-right"><?php echo _l('purchase_quantity'); ?></th>
                        <th width="10%" align="right" class="text-right"><?php echo _l('subtotal'); ?><span class="th_currency"><?php echo '('.$pur_request_currency.')'; ?></span></th>
                        <th width="15%" align="right" ><?php echo _l('debit_note_table_tax_heading'); ?></th>
                        <th width="10%" align="right" class="text-right"><?php echo _l('tax_value'); ?><span class="th_currency"><?php echo '('.$pur_request_currency.')'; ?></span></th>
                        <th width="10%" align="right" class="text-right"><?php echo _l('debit_note_total'); ?><span class="th_currency"><?php echo '('.$pur_request_currency.')'; ?></span></th>
                        <th align="right"  class="text-right"><i data-feather='settings' class='icon-16'></i></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php echo html_entity_decode($purchase_request_row_template); ?>
                    </tbody>
                  </table>
                </div>

              
            <div class="row">
              <div class="col-md-6 ">
              </div>
              <div class="col-md-6 ">
                 <table class="table text-right mbot0">
                   <tbody>
                      <tr id="subtotal">
                         <td class="td_style"><span class="bold"><?php echo _l('subtotal'); ?></span>
                         </td>
                         <td width="65%" id="total_td">
                          
                           <div class="input-group" id="discount-total">

                                  <input type="text" readonly="true"  class="form-control text-right" name="subtotal" value="<?php if(isset($pur_request)){ echo html_entity_decode($pur_request->subtotal); } ?>">

                                 <div class="input-group-addon">
                                    <div class="dropdown">
                                       
                                       <span class="discount-type-selected currency_span" id="subtotal_currency">
                                        <?php 
                                          if(!isset($pur_request)){
                                            if($base_currency != get_setting('default_currency')){
                                              echo html_entity_decode($base_currency); 
                                            }else{
                                              echo get_setting('currency_symbol');
                                            }
                                          }else{
                                            if($pur_request->currency != ''){
                                              if($pur_request->currency != get_setting('default_currency')){
                                                echo html_entity_decode($pur_request->currency); 
                                              }else{
                                                echo get_setting('currency_symbol');
                                              }

                                            }else{
                                              if($base_currency != get_setting('default_currency')){
                                                echo html_entity_decode($base_currency); 
                                              }else{
                                                echo get_setting('currency_symbol');
                                              }
                                            }
                                          }
                                        ?>
                                       </span>
                                       
                                       
                                    </div>
                                 </div>

                              </div>
                         </td>
                      </tr>

                      <tr id="total">
                         <td class="td_style"><span class="bold"><?php echo _l('total'); ?></span>
                         </td>
                         <td width="65%" id="total_td">
                           <div class="input-group" id="total">
                                 <input type="text" readonly="true" class="form-control text-right" name="total_mn" value="<?php if(isset($pur_request)){ echo html_entity_decode($pur_request->total); } ?>">
                                 <div class="input-group-addon">
                                    <div class="dropdown">
                                       
                                       <span class="discount-type-selected currency_span">
                                        <?php 
                                          if(!isset($pur_request)){
                                            if($base_currency != get_setting('default_currency')){
                                              echo html_entity_decode($base_currency); 
                                            }else{
                                              echo get_setting('currency_symbol');
                                            }
                                          }else{
                                            if($pur_request->currency != ''){
                                              if($pur_request->currency != get_setting('default_currency')){
                                                echo html_entity_decode($pur_request->currency); 
                                              }else{
                                                echo get_setting('currency_symbol');
                                              }

                                            }else{
                                              if($base_currency != get_setting('default_currency')){
                                                echo html_entity_decode($base_currency); 
                                              }else{
                                                echo get_setting('currency_symbol');
                                              }
                                            }
                                          }
                                        ?>
                                       </span>
                                    </div>
                                 </div>

                              </div>
                         </td>
                      </tr>
                    </tbody>
                  </table>


              </div>
            </div>

            <div id="removed-items"></div>
            </div>

            </div>

              <div class="clearfix"></div>

            <div class="btn-bottom-toolbar text-right">  
              <button type="button" class="btn-tr save_pr btn btn-info mr-5 text-white">
            <?php echo _l('submit'); ?>
            </button>

            </div>
             <div class="btn-bottom-pusher"></div>

            
       
           </div>
      </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?php require('plugins/Purchase/assets/js/purchase_request/pur_request_js.php'); ?>
