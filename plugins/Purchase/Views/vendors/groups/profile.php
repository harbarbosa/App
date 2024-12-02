<h4 class="customer-profile-group-heading"><?php echo _l('vendor_add_edit_profile'); ?></h4>

<div class="row">
   <?php echo form_hidden('userid',( isset($client) ? $client->userid : '') ); ?>
   <?php echo form_open(uri_string(),array('class'=>'vendor-form general-form','autocomplete'=>'off')); ?>
   <div class="additional"></div>

   <ul id="profile-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
  
      <li>
         <a role="presentation" class="active" href="javascript:;" data-bs-target="#contact_info"><?php echo _l( 'vendor_detail'); ?></a>
      </li>

   
      <li>
         <a role="presentation" class="" href="javascript:;" data-bs-target="#billing_and_shipping"><?php echo _l( 'billing_and_shipping'); ?></a>
      </li>
   </ul>
   <div class="col-md-12 mt-2">
      <div class="tab-content">
         <div role="tabpanel" class="tab-pane fade active show" id="contact_info">
            <div class="row">
              
               <div class="col-md-6">
                  <?php $vendor_code = ( isset($client) ? $client->vendor_code : '');
                   echo render_input1('vendor_code','vendor_code',$vendor_code,'text'); ?>
                  <?php $value=( isset($client) ? $client->company : ''); ?>
                  <?php $attrs = (isset($client) ? array() : array('autofocus'=>true)); ?>
                  <?php echo render_input1( 'company', 'client_company',$value,'text',$attrs); ?>
                  <div id="company_exists_info" class="hide"></div>
                  <?php 
                     $value=( isset($client) ? $client->vat : '');
                     echo render_input1( 'vat', 'client_vat_number',$value);
                      ?>
                  <?php $value=( isset($client) ? $client->phonenumber : ''); ?>
                  <?php echo render_input1( 'phonenumber', 'client_phonenumber',$value); ?>
                  <?php if((isset($client) && empty($client->website)) || !isset($client)){
                     $value=( isset($client) ? $client->website : '');
                     echo render_input1( 'website', 'client_website',$value);
                     } else { ?>
                  <div class="form-group">
                     <label for="website"><?php echo _l('client_website'); ?></label>
                     <div class="input-group">
                        <input type="text" name="website" id="website" value="<?php echo html_entity_decode($client->website); ?>" class="form-control">
                        <div class="input-group-addon">
                           <span><a href="<?php echo maybe_add_http($client->website); ?>" target="_blank" tabindex="-1"><i class="fa fa-globe"></i></a></span>
                        </div>
                     </div>
                  </div>
                  <?php } ?>

                  <div class="form-group">
                    
                     <label  for="category"><?php echo _l('vendor_category'); ?></label>
                     <select  name="category[]" id="category" class="select2 validate-hidden" data-live-search="true" multiple data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                         <?php foreach ($vendor_categories as $vc) {?>
                           <option value="<?php echo html_entity_decode($vc->id); ?>" <?php if(isset($client) && in_array($vc->id, explode(',',$client->category))){ echo 'selected'; } ?>><?php echo html_entity_decode($vc->category_name); ?></option>
                           <?php }?>
                     </select>
                  </div>

                  <?php if(!isset($client)){ ?>
                  <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('customer_currency_change_notice'); ?>"></i>
                  <?php }
                     $s_attrs = array();
                     $selected = '';
                     
                     foreach($currency_dropdown as $currency){
                        if(isset($client)){
                          if($currency['id'] == $client->default_currency){
                            $selected = $currency['id'];
                         }
                      }
                     }
                            // Do not remove the currency field from the customer profile!
                     echo render_select1('default_currency',$currency_dropdown,array('id','text'),'invoice_add_edit_currency',$selected,$s_attrs); ?>
                 
               </div>
               <div class="col-md-6">
                  <?php $value=( isset($client) ? $client->address : ''); ?>
                  <?php echo render_textarea1( 'address', 'client_address',$value); ?>
                  <?php $value=( isset($client) ? $client->city : ''); ?>
                  <?php echo render_input1( 'city', 'client_city',$value); ?>
                  <?php $value=( isset($client) ? $client->state : ''); ?>
                  <?php echo render_input1( 'state', 'client_state',$value); ?>
                  <?php $value=( isset($client) ? $client->zip : ''); ?>
                  <?php echo render_input1( 'zip', 'client_postal_code',$value); ?>
                   <?php $value=( isset($client) ? $client->country : ''); ?>
                  <?php echo render_input1( 'country', 'country',$value); ?>
                  <?php $bank_detail=( isset($client) ? $client->bank_detail : ''); ?>
                  <?php echo render_textarea1( 'bank_detail', 'bank_detail',$bank_detail); ?>
                  <?php $payment_terms=( isset($client) ? $client->payment_terms : ''); ?>
                  <?php echo render_textarea1( 'payment_terms', 'payment_terms',$payment_terms); ?>
               </div>
            </div>
         </div>
  
         <div role="tabpanel" class="tab-pane fade" id="billing_and_shipping">
            <div class="row">
               <div class="col-md-12">
                  <div class="row">
                     <div class="col-md-6">
                        <h4 class="no-mtop"><?php echo _l('billing_address'); ?> <a href="#" class="pull-right billing-same-as-customer"><small class="font-medium-xs"><?php echo _l('customer_billing_same_as_profile'); ?></small></a></h4>
                        <hr />
                        <?php $value=( isset($client) ? $client->billing_street : ''); ?>
                        <?php echo render_textarea1( 'billing_street', 'billing_street',$value); ?>
                        <?php $value=( isset($client) ? $client->billing_city : ''); ?>
                        <?php echo render_input1( 'billing_city', 'billing_city',$value); ?>
                        <?php $value=( isset($client) ? $client->billing_state : ''); ?>
                        <?php echo render_input1( 'billing_state', 'billing_state',$value); ?>
                        <?php $value=( isset($client) ? $client->billing_zip : ''); ?>
                        <?php echo render_input1( 'billing_zip', 'billing_zip',$value); ?>
                        <?php $value=( isset($client) ? $client->billing_country : ''); ?>
                        <?php echo render_input1( 'billing_country', 'billing_country',$value); ?>
                     </div>
                     <div class="col-md-6">
                        <h4 class="no-mtop">
                           <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('customer_shipping_address_notice'); ?>"></i>
                           <?php echo _l('shipping_address'); ?> <a href="#" class="pull-right customer-copy-billing-address"><small class="font-medium-xs"><?php echo _l('customer_billing_copy'); ?></small></a>
                        </h4>
                        <hr />
                        <?php $value=( isset($client) ? $client->shipping_street : ''); ?>
                        <?php echo render_textarea1( 'shipping_street', 'shipping_street',$value); ?>
                        <?php $value=( isset($client) ? $client->shipping_city : ''); ?>
                        <?php echo render_input1( 'shipping_city', 'shipping_city',$value); ?>
                        <?php $value=( isset($client) ? $client->shipping_state : ''); ?>
                        <?php echo render_input1( 'shipping_state', 'shipping_state',$value); ?>
                        <?php $value=( isset($client) ? $client->shipping_zip : ''); ?>
                        <?php echo render_input1( 'shipping_zip', 'shipping_zip',$value); ?>
                        <?php $value=( isset($client) ? $client->shipping_country : ''); ?>
                        <?php echo render_input1( 'shipping_country', 'shipping_country',$value); ?>
                     </div>
                    
                  </div>
               </div>
            </div>
         </div>

         

      </div>
   </div>
   <?php echo form_close(); ?>
</div>

  