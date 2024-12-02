<div id="page-content" class="page-wrapper clearfix">
	<div class="card clearfix">
		<div class="page-title clearfix">
			<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo ($title); ?></h4>
			<div class="title-button-group">
				<?php echo modal_anchor(get_uri("purchase/item_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_item'), array("class" => "btn btn-default", "title" => app_lang('add_item'))); ?>

					
			</div>
		</div>

		<div class="modal bulk_actions" id="table_commodity_list_bulk_actions" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="checkbox checkbox-danger">
							<div class="row">


									<div class="col-md-4">
										<div class="form-group">
											<input type="checkbox" class="form-check-input" name="mass_delete" id="mass_delete">
											<label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
										</div>
									</div>



							</div>

							<!-- TODO -->
							<div class="row d-none">
								
									<div class="col-md-4">
										<div class="form-group">
											<input type="checkbox" class="form-check-input" name="clone_items" id="clone_items">
											<label for="clone_items"><?php echo _l('clone_this_items'); ?></label>
										</div>
									</div>
							
							</div>

							
								<div class="row">
									<div class="col-md-5">
										<div class="form-group">

											<input type="checkbox" class="form-check-input" name="change_item_selling_price" id="change_item_selling_price" >
											<label for="change_item_selling_price"><?php echo _l('change_item_selling_price'); ?></label>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">

											<div class="input-group" id="discount-total">
												<input type="number" class="form-control text-right" min="0" max="100" name="selling_price" value="">
												<div class="input-group-addon">
													<div class="dropdown">
														<span class="discount-type-selected">
															&nbsp;%
														</span>
													</div>
												</div>
											</div>
										</div>

									</div>
								</div>

								<div class="row">
									<div class="col-md-5">
										<div class="form-group">

											<input type="checkbox" class="form-check-input" name="change_item_purchase_price" id="change_item_purchase_price">
											<label for="change_item_purchase_price"><?php echo _l('change_item_purchase_price'); ?></label>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">

											<div class="input-group" id="discount-total">
												<input type="number" class="form-control text-right" min="0" max="100" name="b_purchase_price" value="">
												<div class="input-group-addon">
													<div class="dropdown">
														<span class="discount-type-selected">
															&nbsp;%
														</span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
					

						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-bs-dismiss="modal"><i data-feather="x" class="icon-16"></i> <?php echo app_lang('close'); ?></button>

					
							<a href="#" class="btn btn-primary text-white" onclick="warehouse_delete_bulk_action(this); return false;"><span data-feather="check-circle" class="icon-16"></span><?php echo _l('confirm'); ?></a>

			
					</div>
				</div>

			</div>

		</div>

		<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_commodity_list" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>					

		<div class="table-responsive">
			<?php 
				$table_data = array(
					'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_commodity_list" class="form-check-input"><label></label></div>',
					_l('_images'),
					_l('commodity_code'),
					_l('commodity_name'),

					_l('group_name'),

					_l('unit_name'),
					_l('rate'),
					_l('purchase_price'),
					_l('tax_1').'(%)',
					_l('tax_2').'(%)',
                    "<i data-feather='menu' class='icon-16'></i>",                        
				);

				render_datatable1($table_data,'table_commodity_list',
					array('customizable-table'),
					array(
						'proposal_sm' => 'proposal_sm',
						'id'=>'table-table_commodity_list',
						'data-last-order-identifier'=>'table_commodity_list',
					)); ?>
		</div>
	</div>
</div>

<?php require('plugins/Purchase/assets/js/items/commodity_list_js.php'); ?>
