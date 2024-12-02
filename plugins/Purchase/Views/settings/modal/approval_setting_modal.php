<?php echo form_open(get_uri("purchase/approval_setting/".$id), array("id" => "approval_setting-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
	<div class="modal-body clearfix">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">

					<?php 
					$related = [];
					$related = [ 
						0 => ['id' => 'pur_request', 'name' => app_lang('pur_request')],
						1 => ['id' => 'pur_quotation', 'name' => app_lang('pur_quotation')],
						2 => ['id' => 'pur_order', 'name' => app_lang('pur_order')],
						3 => ['id' => 'payment_request', 'name' => app_lang('payment_request')],

					];

					$name_value = '';
					$related_value = '';
					if(isset($approval_setting)){
						$name_value = $approval_setting->name;
						$related_value = $approval_setting->related;
					}

					?>

					<label for="name"><span class="text-danger">* </span><?php echo app_lang('_subject'); ?></label>
					<?php echo render_input1('name','',$name_value,'text', [], [], '', '', true); ?>
					<label for="related"><span class="text-danger">* </span><?php echo app_lang('related_type'); ?></label>
					<?php echo render_select1('related',$related,array('id','name'),'', $related_value, [], [], '', '', false); ?>

					<div class=" ">
						<div class="invoice-items-table items table-main-invoice-edit has-calculations">
							<?php echo html_entity_decode($create_approval_setting_row_template); ?>
						</div>

						<div id="removed-items"></div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
		<button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('save'); ?></button>

	</div>
</div>
<?php echo form_close(); ?>
<?php require('plugins/Purchase/assets/js/settings/approval_setting_modal_js.php'); ?>
