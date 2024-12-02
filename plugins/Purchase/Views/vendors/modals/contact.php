
            <?php echo form_open(admin_url('purchase/form_contact/'.$customer_id.'/'.$contactid),array('id'=>'contact-form', 'class'=> 'general-form', 'autocomplete'=>'off')); ?>
      
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        
                        <?php if(isset($contact)){ ?>
                        <div class="alert alert-warning hide" role="alert" id="contact_proposal_warning">
                            <?php echo _l('proposal_warning_email_change',array(_l('contact_lowercase'),_l('contact_lowercase'),_l('contact_lowercase'))); ?>
                            <hr />
                            <a href="#" id="contact_update_proposals_emails" data-original-email="" onclick="update_all_proposal_emails_linked_to_contact(<?php echo html_entity_decode($contact->id); ?>); return false;"><?php echo _l('update_proposal_email_yes'); ?></a>
                            <br />
                            <a href="#" onclick="close_modal_manually('#contact'); return false;"><?php echo _l('update_proposal_email_no'); ?></a>
                        </div>
                        <?php } ?>
                        <!-- // For email exist check -->
                        <?php echo form_hidden('contactid',$contactid); ?>
                        <?php $value=( isset($contact) ? $contact->first_name : ''); ?>
                        <label for="first_name"><span class="text-danger">* </span><?php echo _l('client_firstname'); ?></label>
                        <?php echo render_input1( 'first_name', '',$value, 'text',['required' => true]); ?>  
                        <?php $value=( isset($contact) ? $contact->last_name : ''); ?>
                         <label for="last_name"><span class="text-danger">* </span><?php echo _l('client_lastname'); ?></label>
                        <?php echo render_input1( 'last_name', '',$value, 'text',['required' => true]); ?>                         
                        <?php $value=( isset($contact) ? $contact->job_title : ''); ?>
                        <?php echo render_input1( 'job_title', 'contact_position',$value); ?>
                        <?php $value=( isset($contact) ? $contact->email : ''); ?>
                        <label for="email"><span class="text-danger">* </span><?php echo _l('client_email'); ?></label>
                        <?php echo render_input1( 'email', '',$value, 'email', ['required' => true]); ?>
                        <?php $value=( isset($contact) ? $contact->phone : ''); ?>
                        <?php echo render_input1( 'phone', 'client_phonenumber',$value,'text',array('autocomplete'=>'off')); ?>
 
                    <?php $rel_id=( isset($contact) ? $contact->id : false); ?>
           
                    <!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
                    <input  type="text" class="fake-autofill-field" name="fakeusernameremembered" value='' tabindex="-1" />
                    <input  type="password" class="fake-autofill-field" name="fakepasswordremembered" value='' tabindex="-1"/>

                    <div class="client_password_set_wrapper ">
                            <label for="password" class="control-label">
                                <?php if(!isset($contact)){ ?>
                                <span class="text-danger">* </span>
                                <?php } ?>
                                <?php echo _l( 'client_password'); ?>
                            </label>
                            <div class="input-group">

                                <input type="password" class="form-control password" name="password" <?php if(!isset($contact)){ echo 'required="true"'; } ?> value="" autocomplete="false">
                                <span class="input-group-addon">
                                    <a href="#password" class="show_password" onclick="showPassword('password'); return false;"><i data-feather="eye" class="icon-16"></i></a>
                                </span>
                                <span class="input-group-addon">
                                    <a href="#" class="generate_password" onclick="generatePassword(this);return false;"><i  data-feather="refresh-ccw" class="icon-16"></i></a>
                                </span>
                            </div>
          
                    </div>
                <hr />
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="is_primary_contact" id="contact_primary" <?php if((!isset($contact) && total_rows(db_prefix().'users',array('is_primary_contact'=>1,'vendor_id'=>$customer_id)) == 0) || (isset($contact) && $contact->is_primary_contact == 1)){echo 'checked';}; ?> <?php if((isset($contact) && total_rows(db_prefix().'users',array('is_primary_contact'=>1,'vendor_id'=>$customer_id)) == 1 && $contact->is_primary_contact == 1)){echo 'readonly';} ?>>
                    <label for="contact_primary">
                        <?php echo _l( 'contact_primary'); ?>
                    </label>
                </div>
               
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div>
    <?php echo form_close(); ?>

<?php require FCPATH. PLUGIN_URL_PATH .'Purchase/assets/js/vendors/contact_modal_js.php';?>