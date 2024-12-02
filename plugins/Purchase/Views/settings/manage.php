<?php purchase_load_css(array("assets/css/purchase_style.css")); ?>
<div id="page-content" class="page-wrapper clearfix">
  <div class="row">
    <div class="col-sm-3 col-lg-2">
      <ul class="list-group help-catagory" >
        <?php
          foreach ($tab as $hook_tab) {
              ?>
              <a role="presentation" class="list-group-item <?php if($group == $hook_tab){ echo 'active'; } ?>" href="<?php echo_uri('purchase/settings?group='.$hook_tab); ?>" data-bs-target="#<?php echo html_entity_decode($hook_tab); ?>"><?php echo app_lang($hook_tab); ?></a>
       <?php
          }
         ?>
      </ul>
    </div>
  <div class="col-sm-9 col-lg-10">    	
    <div class="card clearfix">
	    <div class="tab-content ml15 mr15">
          <div role="tabpanel" class="tab-pane fade <?php if($group == 'purchase_order_settings'){ echo 'active show'; } ?>" id="purchase_order_settings">
            <?php echo form_open_multipart('purchase/pur_order_setting',array('id'=>'pur_order_setting-form', 'class' => 'general-form')); ?>
            <div class="page-title clearfix">
              <h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('purchase_order_settings'); ?></h4>
              
            </div>
              <div class="container-fluid mt-3">
                <div class="row">
                  <div class="form-group col-md-6">
                    <label for="pur_order_prefix"><?php echo app_lang('pur_order_prefix'); ?></label>
                    <?php
                        echo form_input(array(
                            "id" => "pur_order_prefix",
                            "name" => "pur_order_prefix",
                            "value" => get_setting('pur_order_prefix'),
                            "class" => "form-control recurring_element",
                            "placeholder" => app_lang('pur_order_prefix'),
                            "autocomplete" => "off",
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="pur_request_prefix"><?php echo app_lang('pur_request_prefix'); ?></label>
                    <?php
                        echo form_input(array(
                            "id" => "pur_request_prefix",
                            "name" => "pur_request_prefix",
                            "value" => get_setting('pur_request_prefix'),
                            "class" => "form-control recurring_element",
                            "placeholder" => app_lang('pur_request_prefix'),
                            "autocomplete" => "off",
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="next_purchase_order_number"><?php echo app_lang('next_purchase_order_number'); ?></label>
                    <?php
                        echo form_input(array(
                            "id" => "next_purchase_order_number",
                            "name" => "next_purchase_order_number",
                            "value" => get_setting('next_purchase_order_number'),
                            "class" => "form-control recurring_element",
                            "placeholder" => app_lang('next_purchase_order_number'),
                            "type" => 'number',
                            "autocomplete" => "off",
       
                        ));
                        ?>
                  </div>


                  <div class="form-group col-md-6">
                    <label for="next_purchase_request_number"><?php echo app_lang('next_purchase_request_number'); ?></label>
                    <?php
                        echo form_input(array(
                            "id" => "next_purchase_request_number",
                            "name" => "next_purchase_request_number",
                            "value" => get_setting('next_purchase_request_number'),
                            "class" => "form-control recurring_element",
                            "placeholder" => app_lang('next_purchase_request_number'),
                            "type" => 'number',
                            "autocomplete" => "off",
 
                        ));
                        ?>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="pur_inv_prefix"><?php echo app_lang('pur_inv_prefix'); ?></label>
                    <?php
                        echo form_input(array(
                            "id" => "pur_inv_prefix",
                            "name" => "pur_inv_prefix",
                            "value" => get_setting('pur_inv_prefix'),
                            "class" => "form-control recurring_element",
                            "placeholder" => app_lang('pur_inv_prefix'),
                            "autocomplete" => "off",
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="pur_next_inv_number"><?php echo app_lang('pur_next_inv_number'); ?></label>
                    <?php
                        echo form_input(array(
                            "id" => "pur_next_inv_number",
                            "name" => "pur_next_inv_number",
                            "value" => get_setting('pur_next_inv_number'),
                            "class" => "form-control recurring_element",
                            "placeholder" => app_lang('pur_next_inv_number'),
                            "autocomplete" => "off",
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                  </div>

                

                  <div class="col-md-12">
                    <hr>
                  </div>

                  <div class="form-group col-md-6">
                      <label for="terms_and_conditions"><?php echo app_lang('terms_and_conditions'); ?></label>
                      <?php
                      echo form_textarea(array(
                          "id" => "pur_terms_and_conditions",
                          "name" => "pur_terms_and_conditions",
                          "value" => get_setting('pur_terms_and_conditions'),
                          "placeholder" => app_lang('terms_and_conditions'),
                          "class" => "form-control"
                      ));
                      ?>
                                
                  </div>

                  <div class="form-group col-md-6">
                    <label for="vendor_note"><?php echo app_lang('vendor_note'); ?></label>
                      <?php
                      echo form_textarea(array(
                          "id" => "vendor_note",
                          "name" => "vendor_note",
                          "value" => get_setting('vendor_note'),
                          "placeholder" => app_lang('vendor_note'),
                          "class" => "form-control"
                      ));
                      ?>
                                
                  </div>


                </div>
                <button type="submit" class="btn btn-primary pull-right mb-3"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
              </div>

            <?php echo form_close(); ?>
          </div>

          <div role="tabpanel" class="tab-pane fade <?php if($group == 'purchase_options'){ echo 'active show'; } ?>" id="purchase_options">
            <div class="page-title clearfix">
              <h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('purchase_options'); ?></h4>
              
            </div>
            <div class="container-fluid mt-3">
                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group">
                          <div class="checkbox checkbox-primary">
                            <input onchange="purchase_order_setting(this); return false" type="checkbox" id="purchase_order_setting" name="purchase_setting[purchase_order_setting]" <?php if(get_setting('purchase_order_setting') == 1 ){ echo 'checked';} ?> value="purchase_order_setting" class="form-check-input">
                            <label for="purchase_order_setting"><?php echo app_lang('create_purchase_order_non_create_purchase_request_quotation'); ?>
                            </label>
                          </div>
                        </div>

                        <div class="form-group">
                          <div class="checkbox checkbox-primary">
                            <input onchange="item_by_vendor(this); return false" type="checkbox" id="item_by_vendor" name="purchase_setting[item_by_vendor]" <?php if(get_setting('item_by_vendor') == 1 ){ echo 'checked';} ?> value="item_by_vendor" class="form-check-input">
                            <label for="item_by_vendor"><?php echo app_lang('load_item_by_vendor'); ?>

                            </label>
                          </div>
                        </div>

                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <div class="checkbox checkbox-primary">
                          <input onchange="show_tax_column(this); return false" type="checkbox" id="show_purchase_tax_column" name="purchase_setting[show_purchase_tax_column]" <?php if(get_setting('show_purchase_tax_column') == 1 ){ echo 'checked';} ?> value="show_purchase_tax_column" class="form-check-input">
                          <label for="show_purchase_tax_column"><?php echo app_lang('show_purchase_tax_column'); ?>

                          </label>
                        </div>
                      </div>

                        <div class="form-group">
                          <div class="checkbox checkbox-primary">
                            <input onchange="po_only_prefix_and_number(this); return false" type="checkbox" id="po_only_prefix_and_number" name="purchase_setting[po_only_prefix_and_number]" <?php if(get_setting('po_only_prefix_and_number') == 1 ){ echo 'checked';} ?> value="po_only_prefix_and_number" class="form-check-input">
                            <label for="po_only_prefix_and_number"><?php echo app_lang('po_only_prefix_and_number'); ?>

                            </label>
                          </div>
                        </div>

                        
                    </div>
                </div>
            </div>
          </div>

          <div role="tabpanel" class="tab-pane fade <?php if($group == 'units'){ echo 'active show'; } ?>" id="units">

            <div class="page-title clearfix">
              <h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('units'); ?></h4>
              <div class="title-button-group">
                <?php echo modal_anchor(get_uri("purchase/modal_unit_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('pur_add_unit'), array("class" => "btn btn-default mb1", "title" => app_lang('pur_add_unit'))); ?>
              </div>
            </div>
              <div class="table-responsive">
                  <table id="units-table" class="display" cellspacing="0" width="100%">   
                  </table>
              </div>
            
          </div>

          <div role="tabpanel" class="tab-pane fade <?php if($group == 'approval'){ echo 'active show'; } ?>" id="approval">

            <div class="page-title clearfix">
              <h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('approval'); ?></h4>
              <div class="title-button-group">
                <?php echo modal_anchor(get_uri("purchase/modal_approval_setting_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('pur_add_approval_setting'), array("class" => "btn btn-default mb1 pull-right", "title" => app_lang('pur_add_approval_setting'))); ?>
              </div>
            </div>

              <div class="table-responsive">
                <table id="approval_setting-table" class="display" cellspacing="0" width="100%">            
                </table>
              </div>
            
          </div>

          <div role="tabpanel" class="tab-pane fade <?php if($group == 'commodity_group'){ echo 'active show'; } ?>" id="commodity_group">
            <div class="page-title clearfix">
              <h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('commodity_group'); ?></h4>
              <div class="title-button-group">
                 <?php echo modal_anchor(get_uri("purchase/modal_commodity_group_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('pur_add_commodity_group'), array("class" => "btn btn-default mb1", "title" => app_lang('pur_add_commodity_group'))); ?>
              </div>
            </div>
              <div class="table-responsive">
                  <table id="commodity_groups-table" class="display" cellspacing="0" width="100%">   
                  </table>
              </div>
           
          </div>

          <div role="tabpanel" class="tab-pane fade <?php if($group == 'sub_group'){ echo 'active show'; } ?>" id="sub_group">

            <div class="page-title clearfix">
              <h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('sub_group'); ?></h4>
              <div class="title-button-group">
                 <?php echo modal_anchor(get_uri("purchase/modal_sub_group_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('pur_add_sub_group'), array("class" => "btn btn-default mb1", "title" => app_lang('pur_add_sub_group'))); ?>
              </div>
            </div>

              <div class="table-responsive">
                  <table id="sub_groups-table" class="display" cellspacing="0" width="100%">   
                  </table>
              </div>
            
          </div>

          <div role="tabpanel" class="tab-pane fade <?php if($group == 'vendor_category'){ echo 'active show'; } ?>" id="vendor_category">
            <div class="container-fluid mt-3">

            </div>
            <div class="page-title clearfix">
              <h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('vendor_category'); ?></h4>
              <div class="title-button-group">
                 <?php echo modal_anchor(get_uri("purchase/modal_vendor_category_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('pur_add_vendor_category'), array("class" => "btn btn-default mb1", "title" => app_lang('pur_add_vendor_category'))); ?>
              </div>
            </div>
              <div class="table-responsive">
                  <table id="vendor_category-table" class="display" cellspacing="0" width="100%">   
                  </table>
              </div>

            
          </div>

      </div>

    </div>
  </div>
</div>
</div>
<?php if($group == 'units'){ 
  require FCPATH. PLUGIN_URL_PATH . "Purchase/assets/js/settings/unit_js.php";
 }else if($group == 'commodity_group'){
  require FCPATH. PLUGIN_URL_PATH . "Purchase/assets/js/settings/commodity_group_js.php";
 }else if($group == 'sub_group'){
  require FCPATH. PLUGIN_URL_PATH . "Purchase/assets/js/settings/sub_group_js.php";
 }else if($group == 'vendor_category'){ 
  require FCPATH. PLUGIN_URL_PATH . "Purchase/assets/js/settings/vendor_category_js.php";
 }else if($group == 'purchase_options'){
  require FCPATH. PLUGIN_URL_PATH . "Purchase/assets/js/settings/purchase_option_js.php";
 }else if($group == 'approval'){
  require FCPATH. PLUGIN_URL_PATH . "Purchase/assets/js/settings/approval_js.php";  
 }
?>