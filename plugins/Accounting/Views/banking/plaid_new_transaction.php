
<div id="page-content" class="page-wrapper clearfix general-form">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
        <div class="card-body">


           <input type="hidden" name="csrf_token" value="<?php echo csrf_token();?>">

              <div class="row">
                <?php if(isset($account_data) && $account_data != NULL && $account_data[0]['plaid_status'] == 1) {?>

                  <div class="col-md-3">

                        <p>Account Name: <?php echo html_entity_decode($account_data[0]['account_name']); ?></p> 
                        <p>Status : Verified</p>


                  </div>

                <?php } 
                ?>

                <div class="col-md-3">
                    <?php echo render_select('bank_account',$bank_accounts,array('id','name'),'',(isset($_GET['id']) ? $_GET['id'] :""),array(),array()); ?>
                </div>

                

                <div class="col-md-6">

                    <?php if(isset($account_data) && $account_data != NULL && $account_data[0]['plaid_status'] == 0) {?>

                      <button type="button" class="btn btn-info btn-submit text-white" id="linkButton"><i data-feather="settings" class="icon-16"></i> <?php echo _l('verify_bank_account'); ?></button>

                    <?php } ?>

                    <?php if(isset($account_data) && $account_data != NULL && $account_data[0]['plaid_status'] == 1) {?>

                      <button type="button" id="delete_button" class="btn btn-warning text-white btn-submit" onclick="updatePlaidStatus()"><i data-feather="x-circle" class="icon-16"></i> <?php echo _l('delete_verification'); ?></button>

                    <?php } ?>

                </div>

            </div>

           <?php if(isset($account_data) && $account_data != NULL && $account_data[0]['plaid_status'] == 1) {?> 

            <div class="row">
                <div class="col-md-3">
                    <h5 class="heading">Last Refresh Date: </h5>
                </div>
                <div class="col-md-3">
                    <?php 
                        $value = '';
                        if(isset($refresh_data) && $refresh_data != NULL && $refresh_data[0]['refresh_date'] != NULL ){ 
                            $value = _d($refresh_data[0]['refresh_date']); 
                        }
                    ?>
                    <?php echo render_date_input('last_refresh_date', '', $value, array('disabled' => true)); ?>
                    
                </div>
            </div>

            <h4 style="">Import Transactions</h4>            
            <br>
            <div class="row">
                <div class="col-md-3">
                    <h5 class="heading">Date from which to import transactions:</h5>
                </div>
                <div class="col-md-3">
                    <?php $value = $last_updated != '' ? _d($last_updated) : ''; ?>
                    <?php echo render_date_input('from_date','',$value); ?>
                    
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-info btn-submit text-white" id="import_button" onclick="submitForm()"><i data-feather="download" class="icon-16"></i> <?php echo _l('import_new_transaction'); ?></button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                </div>
                <div class="col-md-6">
                    <h5 class="heading">Up to 500 transactions can be imported at a time. It may take a few minutes to grab them all from your bank.</h5>
                </div>
            </div>


            <br>

            <?php } ?>
            <?php if(isset($transactions) && isset($account_data) && $account_data != NULL && $account_data[0]['plaid_status'] == 1) { ?>        
                <table class="table table-banking">
                  <thead>
                    <th><?php echo _l('invoice_payments_table_date_heading'); ?></th>
                    <!-- <th><?php echo _l('check_#'); ?></th> -->
                    <th><?php echo _l('payee'); ?></th>
                    <th><?php echo _l('description'); ?></th>
                    <th><?php echo _l('withdrawals'); ?></th>
                    <th><?php echo _l('deposits'); ?></th>
                    <th><?php echo _l('imported_date'); ?></th>
                  </thead>
                  <tbody>
                    
                  </tbody>
                </table>

                <hr>

              <?php } ?>

    </div>

  </div>

</div>

 

<!-- box loading -->

<div id="box-loading"></div>

<?php require 'plugins/Accounting/assets/js/banking/plaid_new_transaction_js.php';?>

