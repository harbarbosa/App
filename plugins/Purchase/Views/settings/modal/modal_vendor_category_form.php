<?php echo form_open(get_uri("purchase/vendor_category_save"), array("id" => "vendor_category-form", "class" => "general-form", "role" => "form")); ?>
<div class="vendor_category-modal">
	<div class="modal-body clearfix">
        <div class="container-fluid">

        	<div class="row">
        		<?php $id = isset($vendor_category) ? $vendor_category->id : '';
        		echo form_hidden('id', $id); ?>

        		<div class="col-md-12 form-group">
        			<label for="unit_name"><span class="text-danger">* </span><?php echo app_lang('pur_vendor_category_name'); ?></label>
                    <?php
                        echo form_input(array(
                            "id" => "category_name",
                            "name" => "category_name",
                            "value" => isset($vendor_category) ? $vendor_category->category_name : '',
                            "class" => "form-control",
                            "placeholder" => app_lang('pur_vendor_category_name'),
                            "autocomplete" => "off",
                            "required" => true,
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
        		</div>
        	</div>

        	

        	<div class="row">
        		<div class="form-group col-md-12">
                    <label for="description"><?php echo app_lang('pur_description'); ?></label>
	                <?php
	                  echo form_textarea(array(
	                      "id" => "description",
	                      "name" => "description",
	                      "value" => isset($vendor_category) ? $vendor_category->description : '',
	                      "placeholder" => app_lang('pur_description'),
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