<div id="page-content" class="page-wrapper clearfix">
	<div class="col-md-12">
		<h3 id="greeting" class="no-mtop"></h3>
		
			<div class="card clearfix">
				<div class="row ml15 mr15 mt15">
					<h3 class="text-success projects-summary-heading no-mtop mbot15"><?php echo _l('summary'); ?></h3>
					<div class="row mb15">
						<?php $where = array('vendor'=>get_vendor_user_id()); ?>
	
						<div class="col-md-3 list-status projects-status">
							<a href="#" class="">
								<h3 class="bold"><?php echo total_rows(db_prefix().'pur_orders',$where); ?></h3>
								<span class="text-danger">
									<?php echo _l('purchase_order'); ?>
							</a>
						</div>
						<div class="col-md-3 list-status projects-status">
							<a href="#" class="">
								<h3 class="bold"><?php echo total_rows(db_prefix().'pur_estimates',$where); ?></h3>
								<span class="text-warning">
									<?php echo _l('quotations'); ?>
							</a>
						</div>
						<div class="col-md-3 list-status projects-status">
							<a href="#" class="">
								<h3 class="bold"><?php echo total_rows(db_prefix().'pur_invoices',$where); ?></h3>
								<span class="text-success">
									<?php echo _l('invoices'); ?>
							</a>
						</div>
					</div>
				</div>
			</div>
	
		<div class="card clearfix">
			<div class="row ml15 mr15 mt15">
				<table class="table dt-table" >
		            <thead>
		               <tr>
		                  <th ><?php echo _l('purchase_order'); ?></th>
		                  <th ><?php echo _l('po_value'); ?></th>
		                  <th ><?php echo _l('tax_value'); ?></th>
		                  <th ><?php echo _l('po_value_included_tax'); ?></th>
		                  <th ><?php echo _l('order_date'); ?></th>
		                 
		            </thead>
		            <tbody>
		            	<?php foreach($pur_order as $p){ ?>
		            		<tr>
		            			<td><a href="<?php echo get_uri('purchase/view_pur_order/'.$p['id']); ?>"><?php echo html_entity_decode($p['pur_order_number'].' - '.$p['pur_order_name']); ?></a></td>
		            			<td><?php echo html_entity_decode(to_currency($p['subtotal'],$p['currency'])); ?></td>
		            			<td><?php echo html_entity_decode(to_currency($p['total_tax'],$p['currency'])); ?></td>
		            			<td><?php echo html_entity_decode(to_currency($p['total'],$p['currency'])); ?></td>
		            			<td><span class="label label-primary"><?php echo html_entity_decode(_d($p['order_date'])); ?></span></td>
		            		</tr>
		            	<?php } ?>
		            </tbody>
		         </table>
			</div>
		</div>
	</div>
</div>