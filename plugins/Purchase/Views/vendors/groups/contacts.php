<?php if(isset($client)){ ?>
<div class="page-title clearfix">
    <h4 class="no-margin font-bold"><?php echo is_empty_vendor_company($client->userid) ? _l('pur_contact') : _l('pur_contacts'); ?></h4>
<?php if(has_permission('purchase_vendors','','create') ){
   $disable_new_contacts = false;
   if(is_empty_vendor_company($client->userid) && total_rows(db_prefix().'contacts',array('userid'=>$client->userid)) == 1){
      $disable_new_contacts = true;
   }
   ?>
    <div class="title-button-group">
        <?php echo modal_anchor(get_uri("purchase/vendor_contact_modal_form/".$client->userid), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('new_contact'), array("class" => "btn btn-default", "disabled" => $disable_new_contacts, "title" => app_lang('new_contact'))); ?>
    </div>

<?php } ?>
</div>
<?php
   $table_data = array(_l('pur_list_full_name'));

  $table_data = array_merge($table_data, array(_l('pur_email'),_l('contact_position'),_l('pur_phonenumber'), _l('options')));

   echo render_datatable1($table_data,'vendor_contacts'); ?>
<?php } ?>
<div id="contact_data"></div>
<div id="consent_data"></div>
