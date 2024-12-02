<?php

namespace App\Models;

class Estimate_items_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'estimate_items';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $estimate_items_table = $this->db->prefixTable('estimate_items');
        $estimates_table = $this->db->prefixTable('estimates');
        $clients_table = $this->db->prefixTable('clients');
        $estimate_etapa_table = $this->db->prefixTable('etapa');
        
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $estimate_items_table.id=$id";
        }
        $estimate_id = $this->_get_clean_value($options, "estimate_id");
        if ($estimate_id) {
            $where .= " AND $estimate_items_table.estimate_id=$estimate_id";
        }
    
        $sql = "SELECT 
                    $estimate_items_table.*, 
                    (SELECT $clients_table.currency_symbol FROM $clients_table WHERE $clients_table.id=$estimates_table.client_id LIMIT 1) AS currency_symbol, 
                    $estimates_table.created_by,
                    $estimate_etapa_table.nome_etapa AS nome_etapa -- Substitua 'nome' pelo campo que deseja da tabela 'etapa'
                FROM 
                    $estimate_items_table
                LEFT JOIN 
                    $estimates_table ON $estimates_table.id=$estimate_items_table.estimate_id
                LEFT JOIN 
                    $estimate_etapa_table ON $estimate_etapa_table.id_etapa = $estimate_items_table.etapa_item -- Join com a tabela etapa
                WHERE 
                    $estimate_items_table.deleted=0 $where
                ORDER BY 
                    $estimate_items_table.sort ASC";
    
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
