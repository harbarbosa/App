<div id="page-content" class="page-wrapper clearfix">
	<div class="content">
		<div class="panel_s">
			<div class="panel-body">
				<?php 
				$base_currency = get_base_currency(); 
				$admin_currency = $base_currency;
				$vendor_currency = get_vendor_currency(get_vendor_user_id());
				if($vendor_currency != ''){
					$base_currency = $vendor_currency;
				}
				?>

				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><?php echo html_entity_decode($title); ?></h4>
			        <div class="title-button-group">
			            <a href="<?php echo site_url('purchase/add_update_vendor_items'); ?>" class="btn btn-default">
			              <i data-feather='plus-circle' class='icon-16'></i>  <?php echo _l('add_item'); ?>
			            </a>
			        </div>
		      	</div>
            
		            
               <ul data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
                  <li>
                     <a href="#internal_items" class="<?php if($tab == 'internal_items'){ echo 'active'; } ?>" data-bs-target="#internal_items" role="presentation">
                     <?php echo _l('private_items'); ?>
                     </a>
                  </li>

                  <li >
                     <a href="#external_items" class="<?php if($tab == 'external_items'){ echo 'active'; } ?>" data-bs-target="#external_items" role="presentation">
                     <?php echo _l('public_items'); ?>
                     </a>
                  </li>
                  
                  
                </ul>
		   
		        

		        <div class="tab-content">

             		<div role="tabpanel" class="tab-pane active" id="internal_items">
						<br><br>
						<table class="table dt-table" >
				            <thead>
				               <tr>
				               	  <th ><?php echo _l('pur_image'); ?></th>
				                  <th ><?php echo _l('pur_item'); ?></th>
				                  <th ><?php echo _l('unit'); ?></th>
				                  <th ><?php echo _l('pur_group'); ?></th>
				                  <th ><?php echo _l('pur_rate'); ?></th>
				                  <th ><?php echo _l('pur_tax'); ?></th>
				                  <th ><?php echo _l('options') ?></th>
				               </tr>
				            </thead>
				            <tbody>
				            	<?php foreach($items as $p){ ?>
				            	
				            		<tr>
				            			<td>
				            				<?php 
				            				$arr_images = vendor_item_images($p['id']);

				            				if(count($arr_images) > 0){

					            				if(file_exists(PURCHASE_MODULE_UPLOAD_FOLDER .'/vendor_items/' .$arr_images[0]['rel_id'] .'/'.$arr_images[0]['file_name'])){
								                    $_data = '<img class="images_w_table" src="' . base_url('plugins/Purchase/Uploads/vendor_items/' . $arr_images[0]['rel_id'] .'/'.$arr_images[0]['file_name']).'" alt="'.$arr_images[0]['file_name'] .'" >';
								                }else{
								                	$_data = '<img class="images_w_table" src="' . base_url('plugins/Purchase/Uploads/nul_image.jpg' ).'" alt="nul_image.jpg">';
								                }
								            }else{
				            				 	$_data = '<img class="images_w_table" src="' . base_url('plugins/Purchase/Uploads/nul_image.jpg' ).'" alt="nul_image.jpg">';
								            }

								            echo html_entity_decode($_data);

								            ?>
				            			</td>
				            			<td><a href="<?php echo site_url('purchase/detail_vendor_item/'.$p['id']); ?>"><?php echo html_entity_decode($p['commodity_code'].' - '.$p['description']); ?></a></td>
				            			<td><?php echo pur_get_unit_name($p['unit_id']); ?></td>
				            			<td>
				            			<?php 
				            				$group_name = '';
				            				$group = get_group_name_pur($p['group_id']);

				            				if($group){
				            					$group_name = $group->title;
				            				}

				            				echo html_entity_decode($group_name);
				            			 ?>
				            			</td>
				            			<td>
				            				<?php echo to_currency($p['rate'], $base_currency); ?>
				            			</td>
				            			<td>
				            				<?php
				            					$purchase_model = model('Purchase\Models\Purchase_model');
				            					if($p['tax'] != '' && $p['tax'] != null && $p['tax'] != 0){
				            						$tax_name = $purchase_model->get_tax_name($p['tax']);
				            						echo _l('tax_1').': '.$tax_name;
				            					}

				            					if($p['tax2'] != '' && $p['tax2'] != null && $p['tax2'] != 0){
				            						$tax_name2 = $purchase_model->get_tax_name($p['tax2']);
				            						echo ' | '._l('tax_2').': '.$tax_name2;
				            					}
				            				 ?>
				            			</td>
				            			<td>
				            				<?php 
				            					$view = '<li role="presentation"><a href="'.get_uri('purchase/detail_vendor_item/'. $p['id']).'" class="dropdown-item"><i data-feather="eye" class="icon-16"></i>&nbsp;&nbsp;'.app_lang('view').'</a></li>';

									            
									            $edit = '<li role="presentation"><a href="'.get_uri('purchase/add_update_vendor_items/'. $p['id']).'" class="dropdown-item"><i data-feather="edit" class="icon-16"></i>&nbsp;&nbsp;'.app_lang('edit').'</a></li>';
									            
									            $share = '';
									            if($p['share_status'] == 0){
									            	$share = '<li role="presentation"><a href="'.get_uri('purchase/share_item/'. $p['id']).'" class="dropdown-item"><i data-feather="share-2" class="icon-16"></i>&nbsp;&nbsp;'.app_lang('share_to_client').'</a></li>';
									            }


									            $delete = '<li role="presentation">' . modal_anchor(get_uri("purchase/delete_vendor_item_modal"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $p['id'], "class" => "dropdown-item")) . '</li>';

									 

									            $_data = '
									            <span class="dropdown inline-block">
									            <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
									            <i data-feather="tool" class="icon-16"></i>
									            </button>
									            <ul class="dropdown-menu dropdown-menu-end" role="menu">' .$view . $edit .$share . $delete. '</ul>
									            </span>';

									            echo html_entity_decode($_data);
				            				?>

				            				
				            			</td>
				            		</tr>
				            		
				            	<?php   } ?>
				            </tbody>
				         </table>
				     </div>

				    <div role="tabpanel" class="tab-pane" id="external_items">
				     	<table class="table dt-table" >
				            <thead>
				               <tr>
			
				                  <th width="50%"><?php echo _l('pur_item'); ?></th>
				                  <th ><?php echo _l('unit'); ?></th>
				                  <th ><?php echo _l('pur_group'); ?></th>
				                  <th ><?php echo _l('pur_rate'); ?></th>
				                  <th ><?php echo _l('pur_tax'); ?></th>
				               </tr>
				            </thead>
				            <tbody>
				            	<?php 
				            	foreach($external_items as $p){ ?>
				            		<?php $_item = get_item_hp($p['items']); ?>
				            		<?php if($_item){ ?>

					            		<tr>

					            			<td><?php echo html_entity_decode($_item->commodity_code.' - '.$_item->description); ?></td>
					            			<td><?php echo pur_get_unit_name($_item->unit_id); ?></td>
					            			<td>
					            			<?php 
					            				$group_name = '';
					            				$group = get_group_name_pur($_item->category_id);

					            				if($group){
					            					$group_name = $group->title;
					            				}

					            				echo html_entity_decode($group_name);
					            			 ?>
					            			</td>
					            			<td>
					            				<?php echo to_currency($_item->purchase_price, $admin_currency); ?>
					            			</td>
					            			<td>
					            				<?php
					            					$purchase_model = model('Purchase\Models\Purchase_model');
					            					if($_item->tax != '' && $_item->tax != null && $_item->tax != 0){
					            						$tax_name = $purchase_model->get_tax_name($_item->tax);
					            						echo _l('tax_1').': '.$tax_name;
					            					}

					            					if($_item->tax2 != '' && $_item->tax2 != null && $_item->tax2 != 0){
					            						$tax_name2 = $purchase_model->get_tax_name($_item->tax2);
					            						echo ' | '._l('tax_2').': '.$tax_name2;
					            					}
					            				 ?>
					            			</td>
					            			
					            		</tr>
					            	<?php } ?>
				            		
				            	<?php   } ?>
				            </tbody>
				         </table>
				    </div>
				 </div>
			</div>
		</div>
	</div>
</div>
