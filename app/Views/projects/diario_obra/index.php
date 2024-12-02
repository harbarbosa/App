<div class="card">
    <div class="card-header title-tab">
        <h4 class="float-start"><?php echo app_lang('tasks'); ?></h4>
        <div class="title-button-group">
            <?php
        
            if ($can_create_tasks) {
               
                echo modal_anchor(get_uri("projects/diario_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_task'), array("class" => "btn btn-outline-light", "title" => app_lang('add_task'), "data-post-project_id" => $project_id));
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


            $("#task-table").appTable({
            source: '<?php echo_uri("projects/diario_list_data/".$project_id) ?>',
      
            order: [[3, "desc"]],
          
            rangeDatepicker: [{startDate: {name: "start_date", value: ""}, endDate: {name: "end_date", value: ""}, showClearButton: true, label: "<?php echo app_lang('date'); ?>", ranges: ['this_month', 'last_month', 'this_year', 'last_year', 'last_30_days', 'last_7_days']}],
            columns: [
                {title: "Id", order_by: "member_name"},
                
                {visible: false, searchable: false, order_by: "start_time"},
                {title: "<?php echo get_setting("users_can_input_only_total_hours_instead_of_period") ? app_lang("date") : app_lang('start_time') ?>", "iDataSort": 4, order_by: "start_time"},
                {visible: false, searchable: false, order_by: "end_time"},
                {title: "<?php echo app_lang('end_time') ?>", "iDataSort": 6, order_by: "end_time"},
                {title: "<?php echo app_lang('total') ?>", "class": "text-right"},
                
                {visible: false, title: "<?php echo app_lang('hours') ?>", "class": "text-right"}, //follow the decimal seperator setting. Only for print. 
                {title: 'Funcionarios', "class": "w200"}
<?php echo $custom_field_headers; ?>,
                {visible: optionVisibility, title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: combineCustomFieldsColumns([0, 3, 5, 7, 8, 10, 11], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 3, 5, 7, 8, 9, 11], '<?php echo $custom_field_headers; ?>'),
            summation: [{column: 8, dataType: 'time'}]
        });





    });
</script>

<?php echo view("tasks/task_table_common_script", array("project_id" => $project_id)); ?>
<?php echo view("tasks/update_task_read_comments_status_script"); ?>
<?php echo view("tasks/quick_filters_helper_js"); ?>
