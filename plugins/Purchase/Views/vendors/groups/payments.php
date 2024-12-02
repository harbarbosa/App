<div class="col-md-12" id="small-table">
  <div class="page-title clearfix">
    <h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo _l('payments'); ?></h4>
    
  </div>
    
    <div class="table-responvie mt10">
      <table class="table dt-table">
         <thead>
          <th><?php echo _l('invoices'); ?></th>
           <th><?php echo _l('payments_table_amount_heading'); ?></th>
            <th><?php echo _l('payments_table_mode_heading'); ?></th>
            <th><?php echo _l('payment_transaction_id'); ?></th>
            <th><?php echo _l('payments_table_date_heading'); ?></th>
            <th><?php echo _l('options'); ?></th>
         </thead>
        <tbody>
           <?php foreach($payments as $p) { ?>
            <?php 
              $base_currency = get_base_currency(); 
              $inv_currency = get_invoice_currency_id($p['pur_invoice']);

              if($inv_currency != ''){
                $base_currency = $inv_currency;
              }
            ?>
            <tr>
            <td><a href="<?php echo admin_url('purchase/purchase_invoice/' . $p['pur_invoice']); ?>" ><?php echo get_pur_invoice_number($p['pur_invoice']); ?></a></td>
            <td><?php echo to_currency($p['amount'],$base_currency); ?></td>
            <td><?php echo get_payment_mode_by_id($p['paymentmode']); ?></td>
            <td><?php echo html_entity_decode($p['transactionid']); ?></td>
            <td><?php echo _d($p['date']); ?></td>
            <td>
             
                <a href="<?php echo admin_url('purchase/payment_invoice/'.$p['id']); ?>" target="_blank" class="btn btn-default btn-icon" data-toggle="tooltip" data-placement="top" title="<?php echo _l('view'); ?>" ><i data-feather='eye' class="icon-16"></i></a>
           
            </td>
           </tr>
           <?php } ?>
        </tbody>
     </table> 
   </div>
</div>
