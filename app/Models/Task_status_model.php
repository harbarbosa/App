<?php

namespace App\Models;

class Task_status_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'tasks_status';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $task_status_table = 'tasks_status';

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $task_status_table.id=$id";
        }

        $hide_from_kanban = $this->_get_clean_value($options, "hide_from_kanban");
        if (!is_null($hide_from_kanban)) {
            $where .= " AND $task_status_table.hide_from_kanban=$hide_from_kanban";
        }

        $hide_from_non_project_related_tasks = $this->_get_clean_value($options, "hide_from_non_project_related_tasks");
        if (!is_null($hide_from_non_project_related_tasks)) {
            $where .= " AND $task_status_table.hide_from_non_project_related_tasks=$hide_from_non_project_related_tasks";
        }

        $exclude_status_ids = $this->_get_clean_value($options, "exclude_status_ids");
        if ($exclude_status_ids) {
            $where .= " AND $task_status_table.id NOT IN($exclude_status_ids)";
        }

        $sql = "SELECT $task_status_table.*
        FROM $task_status_table
        WHERE $task_status_table.deleted=0 $where
        ORDER BY $task_status_table.sort ASC";
        return $this->db->query($sql);
    }

    function get_max_sort_value() {
        $task_status_table = 'tasks_status';

        $sql = "SELECT MAX($task_status_table.sort) as sort
        FROM $task_status_table
        WHERE $task_status_table.deleted=0";
        $result = $this->db->query($sql);
        if ($result->resultID->num_rows) {
            return $result->getRow()->sort;
        } else {
            return 0;
        }
    }

}
