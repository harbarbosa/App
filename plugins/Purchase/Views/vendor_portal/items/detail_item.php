
<div class="row">
	<div class="col-md-12">
		<div class="panel_s">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
						<h4>
	                      <?php echo html_entity_decode($item->description); ?>
	                   	</h4>
	                   	<hr class="hr-panel-heading" /> 
					</div>
					
					<div class="col-md-7 panel-padding">
			          <table class="table border table-striped table-margintop">
			              <tbody>

			              	  <tr class="project-overview">
			                    <td class="bold" width="30%"><?php echo _l('title'); ?></td>
			                    <td><?php echo html_entity_decode($item->title) ; ?></td>
			                 </tr>	
			                  <tr class="project-overview">
			                    <td class="bold" width="30%"><?php echo _l('commodity_code'); ?></td>
			                    <td><?php echo html_entity_decode($item->commodity_code) ; ?></td>
			                 </tr>
			                 <tr class="project-overview">
			                    <td class="bold"><?php echo _l('commodity_name'); ?></td>
			                    <td><?php echo html_entity_decode($item->description) ; ?></td>
			                 </tr>
			                 <tr class="project-overview">
			                    <td class="bold"><?php echo _l('commodity_group'); ?></td>
			                    <td><?php echo get_group_name_pur(html_entity_decode($item->group_id)) != null ? get_group_name_pur(html_entity_decode($item->group_id))->title : '' ; ?></td>
			                 </tr>
			                 <tr class="project-overview">
			                    <td class="bold"><?php echo _l('commodity_barcode'); ?></td>
			                    <td><?php echo html_entity_decode($item->commodity_barcode) ; ?></td>
			                 </tr>
			                 <tr class="project-overview">
			                    <td class="bold"><?php echo _l('sku_code'); ?></td>
			                    <td><?php echo html_entity_decode($item->sku_code) ; ?></td>
			                 </tr>
			                 <tr class="project-overview">
			                    <td class="bold"><?php echo _l('sku_name'); ?></td>
			                    <td><?php echo html_entity_decode($item->sku_name) ; ?></td>
			                 </tr>
			                 <tr class="project-overview">
			                    <td class="bold"><?php echo _l('tax_1'); ?></td>
			                    <td><?php echo html_entity_decode($item->tax) != '' && pur_get_tax_rate($item->tax) != null ? pur_get_tax_rate($item->tax)->title : '';  ?></td>
			                 </tr> 
			                 <tr class="project-overview">
			                    <td class="bold"><?php echo _l('tax_2'); ?></td>
			                    <td><?php echo html_entity_decode($item->tax2) != '' && pur_get_tax_rate($item->tax2) != null ? pur_get_tax_rate($item->tax2)->title : '';  ?></td>
			                 </tr> 
			                 <tr class="project-overview">
			                    <td class="bold"><?php echo _l('rate'); ?></td>
			                    <td><?php echo to_currency($item->rate, '')  ?></td>
			                 </tr> 
			                 <tr class="project-overview">
			                    <td class="bold"><?php echo _l('description'); ?></td>
			                    <td><?php echo html_entity_decode($item->long_description)  ?></td>
			                 </tr>	

			                </tbody>
			          </table>
			      	</div>
			      	<div class="col-md-5">
			            

			        <div class="container-fluid">

						<?php
						if ($commodity_file) {
							$files = $commodity_file;
							if (count($files)) {
								?>
								<div class="col-md-12 mt15">
									<?php
									if ($files) {
										$total_files = count($files);
										echo view("Purchase\Views\includes\\timeline_preview", array("files" => $files));
									}
									?>
								</div>
								<?php
							}
						}
						?>
					</div>


			        </div>

				</div>
			</div>
		</div>
	</div>
</div>
