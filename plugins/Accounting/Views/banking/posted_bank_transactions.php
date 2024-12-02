<div class="row">
  
<div class="col-md-12">
  <a href="#" id="update_bank_transactions" class="btn btn-info pull-right text-white mleft5" disabled><i data-feather="upload-cloud" class="icon-16"></i> <?php echo _l('update_bank_transactions'); ?></a>
  <a href="<?php echo admin_url('accounting/plaid_bank_new_transactions'); ?>" id="set_up_your_bank_account" class="btn btn-info pull-right text-white mleft5"><i data-feather="tool" class="icon-16"></i> <?php echo _l('set_up_your_bank_account'); ?></a>
  <a href="<?php echo admin_url('accounting/import_xlsx_posted_bank_transactions'); ?>" class="btn btn-success mr-4 button-margin-r-b pull-right " title="<?php echo _l('import_excel') ?> ">
    <i data-feather="upload" class="icon-16"></i> <?php echo _l('import_excel'); ?>
  </a>
</div>
</div>
  <div class="mbot25 text-center"><h4><?php echo _l('posted_transactions_from_your_bank_account'); ?></h4></div>
  <table class="table table-banking">
  </table>
  
<?php require 'plugins/Accounting/assets/js/banking/posted_bank_transactions_js.php';?>
