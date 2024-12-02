<?php echo form_open(get_uri("accounting/convert"), array("id" => "convert-form", "class" => "general-form", "role" => "form")); ?>
<div id="invoices-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            
            <?php echo html_entity_decode($html); ?>
            <?php echo form_hidden('id', $id); ?>
            <?php echo form_hidden('type', $type); ?>
            <?php echo form_hidden('amount', $amount); ?>
            <hr>
            <?php if($type != 'invoice'){ ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="payment_account" class=""><?php echo app_lang('payment_account'); ?></label>
                            <?php
                            echo form_dropdown("payment_account", $accounts_dropdown, array($credit ? $credit : ''), "class='select2 validate-hidden' id='payment_account' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");

                            ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="deposit_to" class=""><?php echo app_lang('deposit_to'); ?></label>
                        <?php
                            echo form_dropdown("deposit_to", $accounts_dropdown, array($debit ? $debit : ''), "class='select2 validate-hidden' id='deposit_to' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                            ?>
                    </div>
                </div>
            </div>  
            <?php } ?>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div>
</div>
<?php echo form_close(); ?>
<?php require 'plugins/Accounting/assets/js/transaction/convert_form_js.php'; ?>

