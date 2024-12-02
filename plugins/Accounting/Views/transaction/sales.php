<div class="card">
    <div class="clearfix">
        <ul id="transaction-sale-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs rounded classic mb20 scrollable-tabs" role="tablist">
            <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo app_lang('sales') ?></h4></li>
            <li>
                <a role="presentation" href="<?php echo get_uri('accounting/transaction?group=sales&tab=payment'); ?>" data-bs-target="#payment"><?php echo app_lang('payment'); ?> <span class="text-danger"><?php echo '('.$count_payment.')'; ?></span></a></li>
            <li><a role="presentation" href="<?php echo_uri("accounting/transaction_invoices_list"); ?>" data-bs-target="#transaction_invoices"><?php echo app_lang('invoice'); ?> <span class="text-danger"><?php echo '('.$count_invoice.')'; ?></span></a></li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade active" id="payment">
                <table id="payment-table" class="display table-sales" cellspacing="0" width="100%">            
                </table>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="transaction_invoices"></div>
        </div>
    </div>
</div>
<?php require 'plugins/Accounting/assets/js/transaction/payment_js.php'; ?>
