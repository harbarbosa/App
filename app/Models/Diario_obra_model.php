<?php

namespace App\Models;

class Diario_obra_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'project_diario';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $item_categories_table = $this->db->prefixTable('project_diario');
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $item_categories_table.id=$id";
        }

        $sql = "SELECT $item_categories_table.*
        FROM $item_categories_table
        WHERE $item_categories_table.deleted=0 $where";
        return $this->db->query($sql);
    }

}
