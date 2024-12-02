<?php

namespace App\Models;

class Project_items_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'project_items';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $project_items_table = $this->db->prefixTable('project_items');
        $project_table = $this->db->prefixTable('projects');
        $clients_table = $this->db->prefixTable('clients');
        $users_table = $this->db->prefixTable('users');
       
        
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $project_items_table.id=$id";
        }
        $project_id = $this->_get_clean_value($options, "project_id");
        if ($project_id) {
            $where .= " AND $project_items_table.project_id=$project_id";
        }
    
        $sql = "SELECT 
                    $project_items_table.*, 
                    (SELECT $clients_table.currency_symbol FROM $clients_table WHERE $clients_table.id=$project_table.client_id LIMIT 1) AS currency_symbol, 
                    $project_table.created_by
                FROM 
                    $project_items_table
                LEFT JOIN 
                    $project_table ON $project_table.id=$project_items_table.project_id
                LEFT JOIN 
                    $users_table ON $users_table.id=$project_items_table.user_retirada
                

                WHERE 
                    $project_items_table.deleted=0 $where
                ORDER BY 
                    $project_items_table.sort ASC";
    
        return $this->db->query($sql);
    }
    

    function get_etapa($options = array()) {
        $estimate_etapa_table = $this->db->prefixTable('etapa');
        $estimates_table = $this->db->prefixTable('estimates');
        $clients_table = $this->db->prefixTable('clients');
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        
        $estimate_id = $this->_get_clean_value($options, "estimate_id");
        
       
        $sql = "SELECT $estimate_etapa_table.* FROM $estimate_etapa_table   
        WHERE $estimate_etapa_table.id_projeto=$estimate_id";
        
        return $this->db->query($sql);
    }

    function get_items($estimate_id) {
        
        $estimate_items_table = $this->db->prefixTable('estimate_items');
        $estimate_etapa_table = $this->db->prefixTable('etapa');   
       
        $sql = "SELECT $estimate_items_table.* FROM $estimate_items_table WHERE $estimate_items_table.estimate_id=$estimate_id";
        
        return $this->db->query($sql);
    }

    function delete_etapa($id) {
        
       
        $estimate_etapa_table = $this->db->prefixTable('etapa');   
        
        $sql = "DELETE FROM $estimate_etapa_table WHERE $estimate_etapa_table.id_etapa=$id";
        
        return $this->db->query($sql);
    }

    function delete_all_items($id) {
        
       
        $estimate_etapa_table = $this->db->prefixTable('orcamento_items');   
        
        $sql = "DELETE FROM $estimate_etapa_table WHERE $estimate_etapa_table.estimate_id=$id";

      
        
        return $this->db->query($sql);
    }

    



}
