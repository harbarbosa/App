<?php echo form_open_multipart(admin_url('purchase/share_request/'.$pur_request->id),array('id'=>'share_request-form')); ?>

      <div class="modal-body">
          <div class="row">

            <div class="col-md-12 form-group">
                  <label for="send_to_vendors"><?php echo _l('pur_send_to_vendors'); ?></label>
                  <select name="send_to_vendors[]" id="send_to_vendors" class="select2 validate-hidden" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >

                      <?php $vendors_arr = [];
                      if($pur_request->send_to_vendors != ''){
                        $vendors_arr = explode(',', $pur_request->send_to_vendors);
                      }
                      foreach($vendors as $s) { ?>
                      <option value="<?php echo html_entity_decode($s['userid']); ?>" <?php if(isset($pur_request) && in_array($s['userid'], $vendors_arr)){ echo 'selected';  } ?> ><?php echo html_entity_decode($s['company']); ?></option>
                        <?php } ?>

                  </select>  
                </div>    
 
          </div>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
          <button id="sm_btn" type="submit" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-info text-white"><?php echo _l('pur_share'); ?></button>
      </div>
<?php echo form_close(); ?>

<script type="text/javascript">
  
    $("#send_to_vendors").select2();
  
</script>