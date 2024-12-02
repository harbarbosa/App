<?php echo form_open(get_uri("purchase/unit_save"), array("id" => "unit-form", "class" => "general-form", "role" => "form")); ?>
<div class="unit-modal">
	<div class="modal-body clearfix">
        <div class="container-fluid">

        	<div class="row">
        		<?php $unit_type_id = isset($unit) ? $unit->unit_type_id : '';
        		echo form_hidden('unit_type_id', $unit_type_id); ?>
        		<div class="col-md-6 form-group">
        			<label for="unit_code"><span class="text-danger">* </span><?php echo app_lang('pur_unit_code'); ?></label>
                    <?php
                        echo form_input(array(
                            "id" => "unit_code",
                            "name" => "unit_code",
                            "value" => isset($unit) ? $unit->unit_code : '',
                            "class" => "form-control",
                            "placeholder" => app_lang('pur_unit_code'),
                            "autocomplete" => "off",
                            "required" => true,
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
        		</div>

        		<div class="col-md-6 form-group">
        			<label for="unit_name"><span class="text-danger">* </span><?php echo app_lang('pur_unit_name'); ?></label>
                    <?php
                        echo form_input(array(
                            "id" => "unit_name",
                            "name" => "unit_name",
                            "value" => isset($unit) ? $unit->unit_name : '',
                            "class" => "form-control",
                            "placeholder" => app_lang('pur_unit_name'),
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
        			<label for="unit_symbol"><?php echo app_lang('pur_unit_symbol'); ?></label>
                    <?php
                        echo form_input(array(
                            "id" => "unit_symbol",
                            "name" => "unit_symbol",
                            "value" => isset($unit) ? $unit->unit_symbol : '',
                            "class" => "form-control",
                            "placeholder" => app_lang('pur_unit_symbol'),
                            "autocomplete" => "off",
                        ));
                        ?>
        		</div>
        		<div class="col-md-4 form-group">
        			<label for="unit_symbol"><?php echo app_lang('order'); ?></label>
                    <?php
                        echo form_input(array(
                            "id" => "order",
                            "name" => "order",
                            "value" => isset($unit) ? $unit->order : '',
                            "class" => "form-control",
                            "placeholder" => app_lang('order'),
                            "autocomplete" => "off",
                        ));
                        ?>
        		</div>
        		<div class="col-md-2">
        			<div class="form-group float-right mt-5">
	                    <div class="checkbox checkbox-primary">
	                        <input type="checkbox" id="display" name="display" value="display" <?php if(isset($unit) && $unit->display == 1){ echo 'checked'; } ?>>
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
	                      "value" => isset($unit) ? $unit->note : '',
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