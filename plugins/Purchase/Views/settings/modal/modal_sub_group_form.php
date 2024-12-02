<?php echo form_open(get_uri("purchase/sub_group_save"), array("id" => "sub_group-form", "class" => "general-form", "role" => "form")); ?>
<div class="sub_group-modal">
	<div class="modal-body clearfix">
        <div class="container-fluid">

        	<div class="row">
        		<?php $id = isset($sub_group) ? $sub_group->id : '';
        		echo form_hidden('id', $id); ?>
        		<div class="col-md-6 form-group">
        			<label for="unit_code"><span class="text-danger">* </span><?php echo app_lang('pur_sub_group_code'); ?></label>
                    <?php
                        echo form_input(array(
                            "id" => "sub_group_code",
                            "name" => "sub_group_code",
                            "value" => isset($sub_group) ? $sub_group->sub_group_code : '',
                            "class" => "form-control",
                            "placeholder" => app_lang('pur_sub_group_code'),
                            "autocomplete" => "off",
                            "required" => true,
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
        		</div>

        		<div class="col-md-6 form-group">
        			<label for="unit_name"><span class="text-danger">* </span><?php echo app_lang('pur_sub_group_name'); ?></label>
                    <?php
                        echo form_input(array(
                            "id" => "sub_group_name",
                            "name" => "sub_group_name",
                            "value" => isset($sub_group) ? $sub_group->sub_group_name : '',
                            "class" => "form-control",
                            "placeholder" => app_lang('pur_sub_group_name'),
                            "autocomplete" => "off",
                            "required" => true,
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
        		</div>
        	</div>

        	<div class="row">

                <div class="col-md-6 form-group">
                    <label for="group_id"><span class="text-danger">* </span><?php echo app_lang('commodity_group'); ?></label>
                    <?php $group_id = isset($sub_group) ? $sub_group->group_id : '';
                    echo render_select1('group_id',$commodity_groups,array('id','title'),'', $group_id, [], [], '', '', false); ?>
                </div>
        		
        		<div class="col-md-4 form-group">
        			<label for="unit_symbol"><?php echo app_lang('order'); ?></label>
                    <?php
                        echo form_input(array(
                            "id" => "order",
                            "name" => "order",
                            "value" => isset($sub_group) ? $sub_group->order : '',
                            "class" => "form-control",
                            "placeholder" => app_lang('order'),
                            "autocomplete" => "off",
                            "type" => 'number'
                        ));
                        ?>
        		</div>
        		<div class="col-md-2">
        			<div class="form-group float-right mt-5">
	                    <div class="checkbox checkbox-primary">
	                        <input type="checkbox" id="display" name="display" value="display" <?php if(isset($sub_group) && $sub_group->display == 1){ echo 'checked'; } ?>>
	                        <label for="display"><?php echo app_lang('pur_display'); ?>
	                        </label>
	                    </div>
                    </div>
        		</div>
        	</div>

        	<div class="row">
        		<div class="form-group col-md-12">
	                <?php
	                  echo form_textarea(array(
	                      "id" => "note",
	                      "name" => "note",
	                      "value" => isset($sub_group) ? $sub_group->note : '',
	                      "placeholder" => app_lang('note'),
	                      "class" => "form-control"
	                  ));
	                ?>
                            
              	</div>
        	</div>
        </div>

    </div>

	<div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div>
</div>
<?php echo form_close(); ?>
<?php require('plugins/Purchase/assets/js/settings/sub_group_modal_js.php'); ?>