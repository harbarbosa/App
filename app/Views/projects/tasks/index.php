<div class="card">
    <div class="card-header title-tab">
        <h4 class="float-start"><?php echo app_lang('tasks'); ?></h4>
        <div class="title-button-group">
            <?php
            if ($login_user->user_type == "staff" && $can_edit_tasks) {
                echo modal_anchor(get_uri("labels/modal_form"), "<i data-feather='tag' class='icon-16'></i> " . app_lang('manage_labels'), array("class" => "btn btn-outline-light", "title" => app_lang('manage_labels'), "data-post-type" => "task"));
                echo modal_anchor("", "<i data-feather='edit' class='icon-16'></i> " . app_lang('batch_update'), array("class" => "btn btn-info text-white hide batch-update-btn", "title" => app_lang('batch_update'), "data-post-project_id" => $project_id));
                echo js_anchor("<i data-feather='check-square' class='icon-16'></i> " . app_lang("batch_update"), array("class" => "btn btn-outline-light batch-active-btn"));
                echo js_anchor("<i data-feather='x-square' class='icon-16'></i> " . app_lang("cancel_selection"), array("class" => "hide btn btn-outline-light batch-cancel-btn"));
            }
            if ($can_create_tasks) {
                echo modal_anchor(get_uri("tasks/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_multiple_tasks'), array("class" => "btn btn-outline-light", "title" => app_lang('add_multiple_tasks'), "data-post-project_id" => $project_id, "data-post-add_type" => "multiple"));
                echo modal_anchor(get_uri("tasks/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_task'), array("class" => "btn btn-outline-light", "title" => app_lang('add_task'), "data-post-project_id" => $project_id));
            }
            ?>
        </div>
    </div>
    <div class="table-responsive">
        <table id="task-table" class="display" width="100%">            
        </table>
    </div>    
</div>

<?php
//prepare status dropdown list
//select the non completed tasks for team members by default
//show all tasks for client by default.
$statuses = array();
foreach ($task_statuses as $status) {
    $is_selected = false;
    if ($login_user->user_type == "staff") {
        if ($status->key_name != "done") {
            $is_selected = true;
        }
    }

    $statuses[] = array("text" => ($status->key_name ? app_lang($status->key_name) : $status->title), "value" => $status->id, "isChecked" => $is_selected);
}
?>

<script type="text/javascript">
    $(document).ready(function () {
        var userType = "<?php echo $login_user->user_type; ?>";
        var canEditOrDeleteTasks = "<?php echo ($can_edit_tasks || $can_delete_tasks); ?>";
        var optionVisibility = false;
        if (canEditOrDeleteTasks) {
            optionVisibility = true;
        }

        var milestoneVisibility = false;
        var showMilestoneInfo = "<?php echo $show_milestone_info; ?>";
        if (showMilestoneInfo) {
            milestoneVisibility = true;
        }

        var showResponsiveOption = true,
                idColumnClass = "w10p",
                titleColumnClass = "",
                optionColumnClass = "w100";
        if (isMobile()) {
            showResponsiveOption = false;
            milestoneVisibility = false;
            idColumnClass = "w20p";
            titleColumnClass = "w60p";
            optionColumnClass = "w20p";
        }


        var rowCallback = function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $('td:eq(0)', nRow).attr("style", "border-left:5px solid " + aData[0] + " !important;");
                //add activated sub task filter class
                setTimeout(function () {
                    var searchValue = $('#task-table').closest(".dataTables_wrapper").find("input[type=search]").val();
                    if (searchValue.substring(0, 1) === "#") {
                        $('#task-table').find("[main-task-id='" + searchValue + "']").removeClass("filter-sub-task-button").addClass("remove-filter-button sub-task-filter-active");
                    }
                }, 50);
            };




        if (userType === "client") {
            //don't show assignee and options to clients

            var filterDropdown = [];
            if (showMilestoneInfo) {
                filterDropdown.push({name: "milestone_id", class: "w150", options: <?php echo $milestone_dropdown; ?>});
            }
            
            filterDropdown.push({name: "assigned_to", class: "w150", options: <?php echo $assigned_to_dropdown; ?>});
            filterDropdown.push(<?php echo $custom_field_filters; ?>);
            $("#task-table").appTable({
                source: '<?php echo_uri("tasks/list_data/project/" . $project_id) ?>',
                serverSide: true,
                order: [[1, "desc"]],
                filterDropdown: filterDropdown,
                responsive: false, //hide responsive (+) icon
                multiSelect: [
                    {
                        name: "status_id",
                        text: "<?php echo app_lang('status'); ?>",
                        options: <?php echo json_encode($statuses); ?>,
                        saveSelection: true
                    }
                ],
                columns: [
                    {visible: false, searchable: false},
                    {title: "<?php echo app_lang('id') ?>", "class": idColumnClass, order_by: "id"},
                    {title: "<?php echo app_lang('title') ?>", "class": titleColumnClass, order_by: "title"},
                    {visible: false, searchable: false, order_by: "start_date"},
                    {title: "<?php echo app_lang('start_date') ?>", "iDataSort": 3, visible: showResponsiveOption, order_by: "start_date"},
                    {visible: false, searchable: false, order_by: "deadline"},
                    {title: "<?php echo app_lang('deadline') ?>", "iDataSort": 5, visible: showResponsiveOption, order_by: "deadline"},
                    {title: "<?php echo app_lang('milestone') ?>", visible: milestoneVisibility, order_by: "milestone"},
                    {visible: false, searchable: false},
                    {visible: false, searchable: false},
                    {visible: false, searchable: false},
                    {title: "<?php echo app_lang('status') ?>", visible: showResponsiveOption, order_by: "status"}
                    <?php echo $custom_field_headers; ?>,
                    {title: '<i data-feather="menu" class="icon-16"></i>', visible: optionVisibility, "class": "text-center option " + optionColumnClass}
                ],
                printColumns: combineCustomFieldsColumns([1, 2, 4, 6, 7, 12], '<?php echo $custom_field_headers; ?>'),
                xlsColumns: combineCustomFieldsColumns([1, 2, 4, 6, 7, 12], '<?php echo $custom_field_headers; ?>'),
                rowCallback: tasksTableRowCallback //load this function from the task_table_common_script.php 
            });
        } else {

            var filterDropdown = [
                {name: "quick_filter", class: "w200", showHtml: true, options: <?php echo view("tasks/quick_filters_dropdown"); ?>},
                {name: "milestone_id", class: "w200", options: <?php echo $milestone_dropdown; ?>},
                {name: "priority_id", class: "w200", options: <?php echo $priorities_dropdown; ?>},
                {name: "label_id", class: "w200", options: <?php echo $labels_dropdown; ?>}

            ];
            var showAssignedTasksOnly = "<?php echo $show_assigned_tasks_only; ?>";
            if (!showAssignedTasksOnly) {
                filterDropdown.push({name: "assigned_to", class: "w200", options: <?php echo $assigned_to_dropdown; ?>});
            }
            filterDropdown.push(<?php echo $custom_field_filters; ?>);
            $("#task-table").appTable({
                source: '<?php echo_uri("tasks/list_data/project/" . $project_id) ?>',
                serverSide: true,
                order: [[1, "desc"]],
                smartFilterIdentity: "project_tasks_list", //a to z and _ only. should be unique to avoid conflicts 
                contextMeta: {contextId: "<?php echo $project_id; ?>", dependencies: ["milestone_id"]}, //useful to seperate instance related filters. Ex. Milestones are different for each projects. 
                responsive: false, //hide responsive (+) icon
                filterDropdown: filterDropdown,
                singleDatepicker: [{name: "deadline", defaultText: "<?php echo app_lang('deadline') ?>", class: "w200",
                        options: [
                            {value: "expired", text: "<?php echo app_lang('expired') ?>"},
                            {value: moment().format("YYYY-MM-DD"), text: "<?php echo app_lang('today') ?>"},
                            {value: moment().add(1, 'days').format("YYYY-MM-DD"), text: "<?php echo app_lang('tomorrow') ?>"},
                            {value: moment().add(7, 'days').format("YYYY-MM-DD"), text: "<?php echo sprintf(app_lang('in_number_of_days'), 7); ?>"},
                            {value: moment().add(15, 'days').format("YYYY-MM-DD"), text: "<?php echo sprintf(app_lang('in_number_of_days'), 15); ?>"}
                        ]}],
                multiSelect: [
                    {
                        name: "status_id",
                        text: "<?php echo app_lang('status'); ?>",
                        options: <?php echo json_encode($statuses); ?>,
                        saveSelection: true,
                        class: "w200"
                    }
                ],
                columns: [
                    {visible: false, searchable: false},
                    {title: "<?php echo app_lang('id') ?>", "class": idColumnClass, order_by: "id"},
                    {title: "<?php echo app_lang('title') ?>", "class": titleColumnClass, order_by: "title"},
                    {visible: false, searchable: false, order_by: "start_date"},
                    {title: "<?php echo app_lang('start_date') ?>", "iDataSort": 3, visible: showResponsiveOption, order_by: "start_date"},
                    {visible: false, searchable: false, order_by: "deadline"},
                    {title: "<?php echo app_lang('deadline') ?>", "iDataSort": 5, visible: showResponsiveOption, order_by: "deadline"},
                    {title: "<?php echo app_lang("milestone") ?>", visible: showResponsiveOption, order_by: "milestone"},
                    {visible: false, searchable: false},
                    {title: "<?php echo app_lang('assigned_to') ?>", "class": "min-w150", visible: showResponsiveOption, order_by: "assigned_to"},
                    {title: "<?php echo app_lang('collaborators') ?>", visible: showResponsiveOption},
                    {title: "<?php echo app_lang('status') ?>", visible: showResponsiveOption, order_by: "status"}
                    <?php echo $custom_field_headers; ?>,
                    {title: '<i data-feather="menu" class="icon-16"></i>', visible: optionVisibility, "class": "text-center option " + optionColumnClass}
                ],
                printColumns: combineCustomFieldsColumns([1, 2, 4, 6, 7, 9, 10, 12], '<?php echo $custom_field_headers; ?>'),
                xlsColumns: combineCustomFieldsColumns([1, 2, 4, 6, 8, 9, 10], '<?php echo $custom_field_headers; ?>'),
                rowCallback: tasksTableRowCallback, //load this function from the task_table_common_script.php 
                onRelaodCallback: function () {
                    hideBatchTasksBtn();
                }
            });
        }
    });
</script>

<?php echo view("tasks/task_table_common_script", array("project_id" => $project_id)); ?>
<?php echo view("tasks/update_task_read_comments_status_script"); ?>
<?php echo view("tasks/quick_filters_helper_js"); ?>
