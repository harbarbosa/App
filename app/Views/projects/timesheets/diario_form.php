<?php echo form_open(get_uri("projects/save_diario"), array("id" => "task-form", "class" => "general-form", "role" => "form")); ?>

<div id="tasks-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $add_type == "multiple" ? "" : $model_info->id; ?>" />
            
            
            

            <?php if (!$project_id) { ?>
            <div class="form-group">
                <div class="row">
                    <label for="project_id" class=" col-md-3"><?php echo app_lang('project'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_dropdown("project_id", $projects_dropdown, array(), "class='select2' id='project_id'");
                        ?>
                    </div>
                </div>
            </div>
        <?php } else {?>
            
            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
           
            <?php
        }
            $contexts_dropdown = array();

          ?>

            <?php if ($is_clone) { ?>
                <input type="hidden" name="is_clone" value="1" />
            <?php } ?>

           
         
            
            <?php if ($show_assign_to_dropdown) { ?>
                

                <div class="form-group">
                    <div class="row">
                        <label for="collaborators" class=" col-md-3"><?php echo app_lang('collaborators'); ?></label>
                        <div class="col-md-9" id="dropdown-apploader-section">
                            <?php
                            echo form_input(array(
                                "id" => "collaborators",
                                "name" => "collaborators",
                                "value" => $model_info->user_id,
                                "class" => "form-control",
                                "placeholder" => app_lang('collaborators')
                            ));
                            ?>
                        </div>
                    </div>
                </div>

            <?php } ?>


            <?php
            $related_to_dropdowns = array();
            if ($show_contexts_dropdown) {
                ?>
                

            <?php } else { ?>
                <input type="hidden" name="context" id="task-context" value="<?php echo $selected_context; ?>" />
            <?php } ?>

            <?php
            //when opening from global task creation link, there might be only one context perimission
            //and don't have any context_id selected. So, have to show the context dropdown
            if (!$show_contexts_dropdown) {
                $context_id_key = $selected_context . "_id";
                if (!${$context_id_key}) {
                    $show_contexts_dropdown = true;
                }
            }

          
            ?>    

<?php if ((get_setting("users_can_input_only_total_hours_instead_of_period") && (!$diario_model->id || $diario_model->hours)) || (!get_setting("users_can_input_only_total_hours_instead_of_period") && $diario_model->hours)) { ?>
            <div class="row">
                <label for="date" class=" col-md-3 col-sm-3"><?php echo app_lang('date'); ?></label>
                <div class="col-md-4 col-sm-4 form-group">
                    <?php
                    $in_time = is_date_exists($diario_model->start_time) ? convert_date_utc_to_local($diario_model->start_time) : "";

                    echo form_input(array(
                        "id" => "date",
                        "name" => "date",
                        "value" => $in_time ? date("Y-m-d", strtotime($in_time)) : "",
                        "class" => "form-control",
                        "placeholder" => app_lang('date'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
                <label for="hours" class=" col-md-2 col-sm-2"><?php echo app_lang('hours'); ?></label>
                <div class=" col-md-3 col-sm-3 form-group">
                    <?php
                    echo form_input(array(
                        "id" => "hours",
                        "name" => "hours",
                        "value" => $diario_model->hours ? convert_hours_to_humanize_data($diario_model->hours) : "",
                        "class" => "form-control",
                        "placeholder" => app_lang('timesheet_hour_input_help_message'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>

        <?php } else { ?>

            <div class="row">
                <label for="start_date" class=" col-md-3 col-sm-3"><?php echo app_lang('start_date'); ?></label>
                <div class="col-md-4 col-sm-4 form-group">
                    <?php
                    $in_time = is_date_exists($diario_model->start_time) ? convert_date_utc_to_local($diario_model->start_time) : "";

                    if ($time_format_24_hours) {
                        $in_time_value = $in_time ? date("H:i", strtotime($in_time)) : "";
                    } else {
                        $in_time_value = $in_time ? convert_time_to_12hours_format(date("H:i:s", strtotime($in_time))) : "";
                    }

                    echo form_input(array(
                        "id" => "start_date",
                        "name" => "start_date",
                        "value" => $in_time ? date("Y-m-d", strtotime($in_time)) : "",
                        "class" => "form-control",
                        "placeholder" => app_lang('start_date'),
                        "autocomplete" => "off",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
                <label for="start_time" class=" col-md-2 col-sm-2"><?php echo app_lang('start_time'); ?></label>
                <div class=" col-md-3 col-sm-3  form-group">
                    <?php
                    echo form_input(array(
                        "id" => "start_time",
                        "name" => "start_time",
                        "value" => $in_time_value,
                        "class" => "form-control",
                        "placeholder" => app_lang('start_time'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>

            <div class="row">
                <label for="end_date" class=" col-md-3 col-sm-3"><?php echo app_lang('end_date'); ?></label>
                <div class=" col-md-4 col-sm-4 form-group">
                    <?php
                    $out_time = is_date_exists($diario_model->end_time) ? convert_date_utc_to_local($diario_model->end_time) : "";

                    if ($time_format_24_hours) {
                        $out_time_value = $in_time ? date("H:i", strtotime($out_time)) : "";
                    } else {
                        $out_time_value = $in_time ? convert_time_to_12hours_format(date("H:i:s", strtotime($out_time))) : "";
                    }
                    echo form_input(array(
                        "id" => "end_date",
                        "name" => "end_date",
                        "value" => $out_time ? date("Y-m-d", strtotime($out_time)) : "",
                        "class" => "form-control",
                        "placeholder" => app_lang('end_date'),
                        "autocomplete" => "off",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                        "data-rule-greaterThanOrEqual" => "#start_date",
                        "data-msg-greaterThanOrEqual" => app_lang("end_date_must_be_equal_or_greater_than_start_date")
                    ));
                    ?>
                </div>
                <label for="end_time" class=" col-md-2 col-sm-2"><?php echo app_lang('end_time'); ?></label>
                <div class=" col-md-3 col-sm-3 form-group">
                    <?php
                    echo form_input(array(
                        "id" => "end_time",
                        "name" => "end_time",
                        "value" => $out_time_value,
                        "class" => "form-control",
                        "placeholder" => app_lang('end_time'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        <?php } ?>

        <div class="form-group">
                <div class="row">
                    <label for="atividade_realizada" class=" col-md-3">Atividades Realizadas</label>
                    <div class=" col-md-9">
                        <?php
                        echo form_textarea(array(
                            "id" => "atividade_realizada",
                            "name" => "atividade_realizada",
                            "value" => $add_type == "multiple" ? "" : process_images_from_content($model_info->atividade_realizada, false),
                            "class" => "form-control",
                            "placeholder" => "Atividades Realizadas",
                            "data-rich-text-editor" => true
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="observacoes" class=" col-md-3">Observações </label>
                    <div class=" col-md-9">
                        <?php
                        echo form_textarea(array(
                            "id" => "observacoes",
                            "name" => "observacoes",
                            "value" => $add_type == "multiple" ? "" : process_images_from_content($model_info->observacoes, false),
                            "class" => "form-control",
                            "placeholder" => "Observações",
                            "data-rich-text-editor" => true
                        ));
                        ?>
                    </div>
                </div>
            </div>

                <hr>
              

                <div class="form-group">
                   <h5> Condições Climaticas </h5>
            <div class="row">
           
                <div class="col-md-10">
                <label class="col-md-2" for="tempo_manha" >Manhã</label>
                
                    <?php
               
                  
                   
                 
                    echo form_radio(array(
                        "id" => "option_2",
                        "name" => "tempo_manha",
                        "value" => "claro",
                        "checked" => (empty($model_info->project_id) || ($model_info->tempo_manha === "claro")),
                        
                        "class" => "form-check-input",
                    ));
                    
                    echo form_label("Claro", "option_2", array("class" => "form-check-label col-md-2"));

                    echo form_radio(array(
                        "id" => "option_1",
                        "name" => "tempo_manha",
                        "value" => "nublado",
                       "checked" => ($model_info->tempo_manha === "nublado"),
                        "class" => "form-check-input ",
                    ));
                    
                    echo form_label("Nublado", "option_1", array("class" => "form-check-label col-md-2"));

                    echo form_radio(array(
                        "id" => "option_1",
                        "name" => "tempo_manha",
                        "value" => "chuvoso",
                       "checked" => ($model_info->tempo_manha == "chuvoso"),
                        "class" => "form-check-input ",
                    ));
                    
                    echo form_label("Cluvoso", "option_1", array("class" => "form-check-label col-md-2"));

                    echo form_radio(array(
                        "id" => "option_1",
                        "name" => "tempo_manha",
                        "value" => "n/a",
                        "checked" => ($model_info->tempo_manha == "n/a"),
                        "class" => "form-check-input ",
                    ));
                    
                    echo form_label("N/A", "option_1", array("class" => "form-check-label col-md-2"));
                    ?>

                    
                </div>

                
               
               

            </div>
           

            <div class="row">
                
                <div class="col-md-10">
                <label class="col-md-2" for="impressao_total" >Tarde</label>
                    <?php
                    $option1 = false; // Define como false por padrão
                    $option2 = false; // Define como false por padrão
                    
                   
                 
                    echo form_radio(array(
                        "id" => "option_2",
                        "name" => "tempo_tarde",
                        "value" => "claro",
                        "checked" => (empty($model_info->project_id) || ($model_info->tempo_tarde === "claro")),
                        
                        "class" => "form-check-input",
                    ));
                    
                    echo form_label("Claro", "option_2", array("class" => "form-check-label col-md-2"));

                    echo form_radio(array(
                        "id" => "option_1",
                        "name" => "tempo_tarde",
                        "value" => "nublado",
                        "checked" => ($model_info->tempo_tarde == "nublado"),
                        "class" => "form-check-input ",
                    ));
                    
                    echo form_label("Nublado", "option_1", array("class" => "form-check-label col-md-2"));

                    echo form_radio(array(
                        "id" => "option_1",
                        "name" => "tempo_tarde",
                        "value" => "chuvoso",
                        "checked" => ($model_info->tempo_tarde == "chuvoso"),
                        "class" => "form-check-input ",
                    ));
                    
                    echo form_label("Cluvoso", "option_1", array("class" => "form-check-label col-md-2"));

                    echo form_radio(array(
                        "id" => "option_1",
                        "name" => "tempo_tarde",
                        "value" => "n/a",
                        "checked" => ($model_info->tempo_tarde == "n/a"),
                        "class" => "form-check-input ",
                    ));
                    
                    echo form_label("N/A", "option_1", array("class" => "form-check-label col-md-2"));
                    ?>

                    
                </div>

                
               
               

            </div>

            <div class="row">
                
                <div class="col-md-10">
                <label class="col-md-2" for="impressao_total" >Noite</label>
                    <?php
                    $option1 = false; // Define como false por padrão
                    $option2 = false; // Define como false por padrão
                    
                   
                 
                    echo form_radio(array(
                        "id" => "option_2",
                        "name" => "tempo_noite",
                        "value" => "claro",
                        "checked" => ($model_info->tempo_noite == "claro"),
                        
                        "class" => "form-check-input",
                    ));
                    
                    echo form_label("Claro", "option_2", array("class" => "form-check-label col-md-2"));

                    echo form_radio(array(
                        "id" => "option_1",
                        "name" => "tempo_noite",
                        "value" => "nublado",
                        "checked" => ($model_info->tempo_noite == "nublado"),
                        "class" => "form-check-input ",
                    ));
                    
                    echo form_label("Nublado", "option_1", array("class" => "form-check-label col-md-2"));

                    echo form_radio(array(
                        "id" => "option_1",
                        "name" => "tempo_noite",
                        "value" => "chuvoso",
                        "checked" => ($model_info->tempo_noite == "chuvoso"),
                        "class" => "form-check-input ",
                    ));
                    
                    echo form_label("Cluvoso", "option_1", array("class" => "form-check-label col-md-2"));

                    echo form_radio(array(
                        "id" => "option_1",
                        "name" => "tempo_noite",
                        "value" => "n/a",
                        "checked" => (empty($model_info->project_id) || ($model_info->tempo_noite === "n/a")),
                        "class" => "form-check-input ",
                    ));
                    
                    echo form_label("N/A", "option_1", array("class" => "form-check-label col-md-2"));
                    ?>

                    
                </div>

                
               
               

            </div>

           

            <hr>
        </div>

     

            <?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => "col-md-3", "field_column" => " col-md-9")); ?> 

            <?php echo view("includes/dropzone_preview"); ?>

            <?php if ($is_clone) { ?>
                <?php if ($has_checklist) { ?>
                    <div class="form-group">
                        <label for="copy_checklist" class=" col-md-12">
                            <?php
                            echo form_checkbox("copy_checklist", "1", true, "id='copy_checklist' class='float-start mr15 form-check-input'");
                            ?>    
                            <?php echo app_lang('copy_checklist'); ?>
                        </label>
                    </div>
                <?php } ?>

                <?php if ($has_sub_task) { ?>
                    <div class="form-group">
                        <label for="copy_sub_tasks" class=" col-md-12">
                            <?php
                            echo form_checkbox("copy_sub_tasks", "1", false, "id='copy_sub_tasks' class='float-start mr15 form-check-input'");
                            ?>    
                            <?php echo app_lang('copy_sub_tasks'); ?>
                        </label>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>

    <div class="modal-footer">
        <div id="link-of-new-view" class="hide">
            <?php
            echo modal_anchor(get_uri("tasks/view"), "", array("data-modal-lg" => "1"));
            ?>
        </div>

        <?php
        if (!$model_info->id || $add_type == "multiple") {
            echo view("includes/upload_button");
        }
        ?>

        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>

        <?php if ($add_type == "multiple") { ?>
            <button id="save-and-add-button" type="button" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save_and_add_more'); ?></button>
        <?php } else { ?>
            
            <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
<?php } ?>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $("#collaborators").on("change", function () {
                    var project_id =  $("#project_id").val();
                  
                    $("#project_id").val(project_id); // Define o valor em outro input
                   
                });
    $(document).ready(function () {

        $("#project_id").select2().on("change", function () {
            var projectId = $(this).val();
            $("#projectid").val(projectId);
            });

                
            

       

        setDatePicker("#start_date, #end_date, #date");
        setTimePicker("#start_time, #end_time");

        $('[data-bs-toggle="tooltip"]').tooltip();

        //send data to show the task after save
        window.showAddNewModal = false;

        $("#save-and-show-button, #save-and-add-button").click(function () {
            window.showAddNewModal = true;
            $(this).trigger("submit");
        });

        var taskShowText = "<?php echo app_lang('task_info') ?>",
                multipleTaskAddText = "<?php echo app_lang('add_multiple_tasks') ?>",
                addType = "<?php echo $add_type; ?>";

        window.taskForm = $("#task-form").appForm({
            closeModalOnSuccess: false,
            onSuccess: function (result) {
                window.location.reload(true);
                $("#task-table").appTable({newData: result.data, dataId: result.id});
                $("#reload-kanban-button:visible").trigger("click");

                $("#save_and_show_value").append(result.save_and_show_link);

                if (window.showAddNewModal) {
                    var $taskViewLink = $("#link-of-new-view").find("a");

                    if (addType === "multiple") {
                        //add multiple tasks
                        $taskViewLink.attr("data-action-url", "<?php echo get_uri("tasks/modal_form"); ?>");
                        $taskViewLink.attr("data-title", multipleTaskAddText);
                        $taskViewLink.attr("data-post-last_id", result.id);
                        $taskViewLink.attr("data-post-project_id", "<?php echo $project_id; ?>");
                        $taskViewLink.attr("data-post-add_type", "multiple");
                    } else {
                        //save and show
                        $taskViewLink.attr("data-action-url", "<?php echo get_uri("tasks/view"); ?>");
                        $taskViewLink.attr("data-title", taskShowText + " #" + result.id);
                        $taskViewLink.attr("data-post-id", result.id);
                    }

                    $taskViewLink.trigger("click");
                } else {
                    window.taskForm.closeModal();

                    if (window.refreshAfterAddTask) {
                        window.refreshAfterAddTask = false;
                        location.reload();
                    }
                }

                window.reloadKanban = true;

                if (typeof window.reloadGantt === "function") {
                    window.reloadGantt(true);
                }
            },
            onAjaxSuccess: function (result) {
                if (!result.success && result.next_recurring_date_error) {
                    $("#next_recurring_date").val(result.next_recurring_date_value);
                    $("#next_recurring_date_container").removeClass("hide");

                    $("#task-form").data("validator").showErrors({
                        "next_recurring_date": result.next_recurring_date_error
                    });
                }
            }
        });

       

        $("#task-form .select2").select2();
        setTimeout(function () {
            $("#title").focus();
        }, 200);

        setDatePicker("#start_date");

        setDatePicker("#deadline", {
            endDate: "<?php echo $project_deadline; ?>"
        });

        setTimePicker("#start_time, #end_time");

        $('[data-bs-toggle="tooltip"]').tooltip();

        //show/hide recurring fields
        $("#recurring").click(function () {
            if ($(this).is(":checked")) {
                $("#recurring_fields").removeClass("hide");
            } else {
                $("#recurring_fields").addClass("hide");
            }
        });

        setDatePicker("#next_recurring_date", {
            startDate: moment().add(1, 'days').format("YYYY-MM-DD") //set min date = tomorrow
        });


    });
</script>


<?php
echo view("tasks/get_dropdowns_script", array(
    "related_to_dropdowns" => $related_to_dropdowns,
    "milestones_dropdown" => $milestones_dropdown,
    "assign_to_dropdown" => $assign_to_dropdown,
    "collaborators_dropdown" => $collaborators_dropdown,
    "statuses_dropdown" => $statuses_dropdown,
    "label_suggestions" => $label_suggestions,
    "priorities_dropdown" => $priorities_dropdown
));
