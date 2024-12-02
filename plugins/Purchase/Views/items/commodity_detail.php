
<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($commodity_item->title); ?></h4>
					<div class="title-button-group">
					</div>
				</div>
				<div class="card-body">
					<div class="row col-md-12">

						<h4 class="h4-color"><?php echo _l('general_infor'); ?></h4>
						<hr class="hr-color">



						<div class="col-md-7 panel-padding">
							<table class="table border table-striped table-margintop">
								<tbody>

									<tr class="project-overview">
										<td class="bold" width="30%"><?php echo _l('title'); ?></td>
										<td><?php echo html_entity_decode($commodity_item->title) ; ?></td>
									</tr>

									<tr class="project-overview">
										<td class="bold" width="30%"><?php echo _l('commodity_code'); ?></td>
										<td><?php echo html_entity_decode($commodity_item->commodity_code) ; ?></td>
									</tr>
									<tr class="project-overview">
										<td class="bold"><?php echo _l('commodity_name'); ?></td>
										<td><?php echo html_entity_decode($commodity_item->description) ; ?></td>
									</tr>
									
									<tr class="project-overview">
										<td class="bold"><?php echo _l('commodity_barcode'); ?></td>
										<td><?php echo html_entity_decode($commodity_item->commodity_barcode) ; ?></td>
									</tr>
									<tr class="project-overview">
										<td class="bold"><?php echo _l('sku_code'); ?></td>
										<td><?php echo html_entity_decode($commodity_item->sku_code) ; ?></td>
									</tr>
									<tr class="project-overview">
										<td class="bold"><?php echo _l('sku_name'); ?></td>
										<td><?php echo html_entity_decode($commodity_item->sku_name) ; ?></td>
									</tr>
									<tr class="project-overview">
										<td class="bold"><?php echo _l('tax_1'); ?></td>
										<td><?php echo html_entity_decode($commodity_item->tax) != '' && get_tax_rate($commodity_item->tax) != null ? get_tax_rate($commodity_item->tax)->title : '';  ?></td>
									</tr> 
									<tr class="project-overview">
										<td class="bold"><?php echo _l('tax_2'); ?></td>
										<td><?php echo html_entity_decode($commodity_item->tax2) != '' && get_tax_rate($commodity_item->tax2) != null ? get_tax_rate($commodity_item->tax2)->title : '';  ?></td>
									</tr> 
								</tbody>
							</table>
							<table class="table border table-striped table-margintop" >
								<tbody>
					

									<tr class="project-overview">
										<td class="bold"><?php echo _l('rate'); ?></td>
										<td><?php echo to_currency((float)$commodity_item->rate) ; ?></td>
									</tr>

									<tr class="project-overview">
										<td class="bold"><?php echo _l('_profit_rate_p'); ?></td>
										<td><?php echo html_entity_decode($commodity_item->profif_ratio) ; ?></td>
									</tr>
								</tbody>
							</table>
							<table class="table table-striped table-margintop">
								<tbody>

									<tr class="project-overview">
										<td class="bold"><?php echo _l('unit_id'); ?></td>
										<td><?php echo  $commodity_item->unit_id != '' && get_unit_type($commodity_item->unit_id) != null ? get_unit_type($commodity_item->unit_id)->unit_name : ''; ?></td>
									</tr> 

									<tr class="project-overview">
										<td class="bold"><?php echo _l('purchase_price'); ?></td>
										<td><?php echo to_currency((float)$commodity_item->purchase_price) ; ?></td>
									</tr>

							
								</tbody>
							</table>
						</div>

						<div class="col-md-5">
							<div class="container-fluid">

								<?php
								if ($model_info->files) {
									$files = @unserialize($model_info->files);
									if (count($files)) {
										?>
										<div class="col-md-12 mt15">
											<?php
											if ($files) {
												$total_files = count($files);
												echo view("includes/timeline_preview", array("files" => $files));
											}
											?>
										</div>
										<?php
									}
								}
								?>
							</div>
						</div>

						<div class=" row ">
							<div class="col-md-12">
								<h4 class="h4-color"><?php echo _l('description'); ?></h4>
								<hr class="hr-color">
								<h6><?php echo html_entity_decode($commodity_item->long_description) ; ?></h6>

							</div>

						</div>

						<div class=" row ">
							<div class="col-md-12">
								<h4 class="h4-color"><?php echo _l('long_description'); ?></h4>
								<hr class="hr-color">
								<h6><?php echo html_entity_decode($commodity_item->long_descriptions) ; ?></h6>

							</div>
						</div>


						<br>
					</div>

				</div>

			</div>

			</div>
		</div>
	</div>




