<?php echo form_open(get_uri('purchase/add_invoice_payment/'.$pur_invoice->id),array('id'=>'purinvoice-add_payment-form', 'class' => 'general-form')); ?>


    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
             <div id="additional"></div>
             <lablel for="amount"><span class="text-danger">* </span><?php echo _l('amount'); ?></lablel>
             <?php echo render_input1('amount', '',purinvoice_left_to_pay($pur_invoice->id),'number',array('required' => true ,'step' => 'any', 'max' => purinvoice_left_to_pay($pur_invoice->id))); ?>
                <?php echo render_date_input1('payment_date','payment_edit_date', date('Y-m-d')); ?>
                <?php echo render_select1('paymentmode',$payment_modes,array('id','title'),'payment_mode'); ?>
                
                <?php echo render_input1('transactionid','payment_transaction_id'); ?>
                <?php echo render_textarea1('note','note','',array('rows'=>7)); ?>

            </div>
        </div>
    </div>
        <div class="modal-footer">
           <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
            <button type="submit" class="btn btn-info text-white"><?php echo _l('submit'); ?></button>
        </div>

    <?php echo form_close(); ?>

<?php require FCPATH. PLUGIN_URL_PATH . "Purchase/assets/js/invoices/payment_modal_js.php";  ?>