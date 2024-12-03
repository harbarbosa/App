


<?php echo form_open(get_uri("projects/save_item"), array("id" => "project-item-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <input type="hidden" id="item_id" name="item_id" value="" />
        <input type="hidden" id="etapa_id" name="etapa_id" value="" />
        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
        <input type="hidden" name="add_new_item_to_library" value="" id="add_new_item_to_library" />
        <div class="form-group">
            <div class="row">
                <label for="project_item_title" class=" col-md-3"><?php echo app_lang('item'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "project_item_title",
                        "name" => "project_item_title",
                        "value" => $model_info->title,
                        "class" => "form-control validate-hidden",
                        "placeholder" => app_lang('select_or_create_new_item'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                    <a id="project_item_title_dropdwon_icon" tabindex="-1" href="javascript:void(0);" style="color: #B3B3B3;float: right; padding: 5px 7px; margin-top: -35px; font-size: 18px;"><span>×</span></a>
                </div>
            </div>
        </div>
        
       
                    <div class="form-group">
                <div class="row">
                    <label for="entregue_por" class="col-md-3">Entregue por:</label>
                    <div class="col-md-9" id="dropdown-apploader-section">
                        <select id="entregue_por" class="form-control" name="entregue_por">
                            <option value="">- Selecione um colaborador -</option>
                            <?php foreach ($project_members_dropdown as $member): ?>
                                <option value="<?php echo $member['id']; ?>" 
                                    <?php echo ($member['id'] == $model_info->user_entrega) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($member['text'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="retirado_por" class="col-md-3">Retirado por:</label>
                    <div class="col-md-9" id="dropdown-apploader-section">
                        <select id="retirado_por" class="form-control" name="retirado_por">
                            <option value="">- Selecione um colaborador -</option>
                            <?php foreach ($project_members_dropdown as $member): ?>
                                <option value="<?php echo $member['id']; ?>" 
                                    <?php echo ($member['id'] == $model_info->user_retirada) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($member['text'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>


      
            <div class="form-group">
            <div class="row">
                <label for="start_date" class=" col-md-3 col-sm-3"><?php echo app_lang('start_date'); ?></label>
                <div class="col-md-9 col-sm-9 form-group">
                    <?php
                    $in_time = is_date_exists($model_info->data_retirada) ? convert_date_utc_to_local($model_info->data_retirada) : "";

                   

                    echo form_input(array(
                        "id" => "data_entrega",
                        "name" => "data_entrega",
                        "value" => $in_time ? date("Y-m-d", strtotime($in_time)) : "",
                        "class" => "form-control",
                        "placeholder" => "Data Entrega",
                        "autocomplete" => "off",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
                
                </div>
            </div>
       

        <div class="form-group">
            <div class="row">
                <label for="project_item_description" class="col-md-3"><?php echo app_lang('description'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "project_item_description",
                        "name" => "project_item_description",
                        "value" => $model_info->description ? $model_info->description : "",
                        "class" => "form-control",
                        "placeholder" => app_lang('description'),
                        "data-rich-text-editor" => true
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="project_item_quantity" class=" col-md-3"><?php echo app_lang('quantity'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "project_item_quantity",
                        "name" => "project_item_quantity",
                        "value" => $model_info->quantity ? to_decimal_format($model_info->quantity) : "",
                        "class" => "form-control",
                        "placeholder" => app_lang('quantity'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="project_unit_type" class=" col-md-3"><?php echo app_lang('unit_type'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "project_unit_type",
                        "name" => "project_unit_type",
                        "value" => $model_info->unit_type,
                        "class" => "form-control",
                        "placeholder" => app_lang('unit_type') . ' (Ex: hours, pc, etc.)'
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group collapse">
            <div class="row">
                <label for="project_item_rate" class=" col-md-3"><?php echo app_lang('rate'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "project_item_rate",
                        "name" => "project_item_rate",
                        "value" => $model_info->rate ? 'R$ '.to_decimal_format($model_info->rate) : "",
                        "class" => "form-control money-mask ",
                        "type" => "hidden",
                        "placeholder" => app_lang('rate'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>

       

    </div>
</div>



<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        setDatePicker("#data_entrega, #end_date, #date");
        MaskMoney();
        
        $("#project-item-form").appForm({
            onSuccess: function (result) {
                $("#project-item-table").appTable({newData: result.data, dataId: result.id});
               
                window.location.reload(true);
                if (typeof updateInvoiceStatusBar == 'function') {
                    updateInvoiceStatusBar(result.project_id);
                }
            }
        });

        //show item suggestion dropdown when adding new item
        var isUpdate = "<?php echo $model_info->id; ?>";
        if (!isUpdate) {
            applySelect2OnItemTitle();
            applySelect2OnItemEtapa();
        }

        //re-initialize item suggestion dropdown on request
        $("#project_item_title_dropdwon_icon").click(function () {
            applySelect2OnItemTitle();
        })

        $("#project_item_etapa_dropdwon_icon").click(function () {
            applySelect2OnItemEtapa();
        })

    });

    function applySelect2OnItemTitle() {
        $("#project_item_title").select2({
            showSearchBox: true,
            ajax: {
                url: "<?php echo get_uri("estimates/get_estimate_item_suggestion"); ?>",
                dataType: 'json',
                quietMillis: 250,
                data: function (term, page) {
                    return {
                        q: term // search term
                    };
                },
                results: function (data, page) {
                    return {results: data};
                }
            }
        }).change(function (e) {
            if (e.val === "+") {
                //show simple textbox to input the new item
                $("#project_item_title").select2("destroy").val("").focus();
                $("#add_new_item_to_library").val(1); //set the flag to add new item in library
            } else if (e.val) {
                //get existing item info
                $("#add_new_item_to_library").val(""); //reset the flag to add new item in library
                $.ajax({
                    url: "<?php echo get_uri("estimates/get_estimate_item_info_suggestion"); ?>",
                    data: {item_id: e.val},
                    cache: false,
                    type: 'POST',
                    dataType: "json",
                    success: function (response) {

                        //auto fill the description, unit type and rate fields.
                        if (response && response.success) {
                            $("#item_id").val(response.item_info.id);
                            $("#project_item_title").val(response.item_info.title);
                            
                            $("#project_item_description").val(response.item_info.description);

                            $("#project_unit_type").val(response.item_info.unit_type);

                            $("#project_item_rate").val(response.item_info.rate);
                        }
                    }
                });
            }

        });
    }

    

        //intialized select2 dropdown for first time
        $("#entregue_por").select2({data: <?php echo json_encode($project_members_dropdown); ?>});
        



    function applySelect2OnItemEtapa() {
        $("#project_item_etapa").select2({
            showSearchBox: true,
            ajax: {
                url: "<?php echo get_uri("estimates/get_estimate_etapa_suggestion"); ?>",
                dataType: 'json',
                quietMillis: 250,
                data: function (term, page) {
                    var projectId = $("input[name='project_id']").val();
                    return {
                        q: term, // search term
                        project_id: projectId // passando project_id
                    };
                },
                results: function (data, page) {
                    return {results: data};
                }
            }
        }).change(function (e) {
            if (e.val === "+") {
                
            } else if (e.val) {
                //get existing item info
                $("#add_new_item_to_library").val(""); //reset the flag to add new item in library
                $.ajax({
                    url: "<?php echo get_uri("estimates/get_estimate_etapa_info_suggestion"); ?>",
                    data: {etapa_id: e.val},
                    cache: false,
                    type: 'POST',
                    dataType: "json",
                    success: function (response) {

                        //auto fill the description, unit type and rate fields.
                        if (response && response.success) {
                            $("#etapa_id").val(response.item_info.id_etapa);
                            $("#estimate_item_etapa").val(response.item_info.nome_etapa);
                            
                           
                        }
                    }
                });
            }

        });
    }

</script>
