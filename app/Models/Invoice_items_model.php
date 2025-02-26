<?php

namespace App\Models;

use CodeIgniter\Model;

class Invoice_items_model extends Crud_model {

    protected $table = null;
    private $_Invoices_model = null;

    function __construct() {
        $this->table = 'invoice_items';
        parent::__construct($this->table);

        $this->_Invoices_model = model("App\Models\Invoices_model");
    }

    function get_details($options = array()) {
        $invoice_items_table = $this->db->prefixTable('invoice_items');
        $invoices_table = $this->db->prefixTable('invoices');
        $clients_table = $this->db->prefixTable('clients');
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $invoice_items_table.id=$id";
        }
        $invoice_id = $this->_get_clean_value($options, "invoice_id");
        if ($invoice_id) {
            $where .= " AND $invoice_items_table.invoice_id=$invoice_id";
        }

        $sql = "SELECT $invoice_items_table.*, (SELECT $clients_table.currency_symbol FROM $clients_table WHERE $clients_table.id=$invoices_table.client_id limit 1) AS currency_symbol
        FROM $invoice_items_table
        LEFT JOIN $invoices_table ON $invoices_table.id=$invoice_items_table.invoice_id
        WHERE $invoice_items_table.deleted=0 $where
        ORDER BY $invoice_items_table.sort ASC";
        return $this->db->query($sql);
    }

    function get_item_suggestion($keyword = "", $user_type = "") {
        
        $items_table = $this->db->prefixTable('items');

        if ($keyword) {
            $keyword = $this->db->escapeLikeString($keyword);
        }
       

        $where = "";
        if ($user_type && $user_type === "client") {
            $where = " AND $items_table.show_in_client_portal=1";
        }

        $sql = "SELECT $items_table.id, $items_table.title
        FROM $items_table
        WHERE $items_table.deleted=0  
        AND $items_table.title LIKE '%$keyword%' ESCAPE '!' 
        $where
        ORDER BY $items_table.title ASC";  // Adicionando a ordenação
        return $this->db->query($sql)->getResult();
    }

    function get_etapa_suggestion($keyword = "", $estimate_id="", $user_type = "") {
        $etapa_table = $this->db->prefixTable('etapa');

        if ($keyword) {
            $keyword = $this->db->escapeLikeString($keyword);
        }

        $where = "";
        

        $sql = "SELECT $etapa_table.id_etapa, $etapa_table.nome_etapa
        FROM $etapa_table
        WHERE $etapa_table.id_projeto=$estimate_id AND $etapa_table.nome_etapa LIKE '%$keyword%' ESCAPE '!' $where
        ORDER BY $etapa_table.nome_etapa ASC";
        return $this->db->query($sql)->getResult();
    }


    function get_item_info_suggestion($options = array()) {

        $items_table = $this->db->prefixTable('items');

        $where = "";
        $item_name = get_array_value($options, "item_name");
        if ($item_name) {
            $item_name = $this->db->escapeLikeString($item_name);
            $where .= " AND $items_table.title LIKE '%$item_name%' ESCAPE '!'";
        }

        $item_id = $this->_get_clean_value($options, "item_id");
        if ($item_id) {
            $where .= " AND $items_table.id=$item_id";
        }

        $user_type = $this->_get_clean_value($options, "user_type");
        if ($user_type && $user_type === "client") {
            $where = " AND $items_table.show_in_client_portal=1";
        }

        $sql = "SELECT $items_table.*
        FROM $items_table
        WHERE $items_table.deleted=0 $where
        ORDER BY id DESC LIMIT 1
        ";

        $result = $this->db->query($sql);

        if ($result->resultID->num_rows) {
            return $result->getRow();
        }
    }

    function get_etapa_info_suggestion($options = array()) {

        $etapa_table = $this->db->prefixTable('etapa');

        $where = "";
        $etapa_name = get_array_value($options, "nome_etapa");
        if ($etapa_name) {
            $etapa_name = $this->db->escapeLikeString($etapa_name);
            $where .= " AND $items_table.title LIKE '%$etapa_name%' ESCAPE '!'";
        }

        $etapa_id = $this->_get_clean_value($options, "etapa_id");
        if ($etapa_id) {
            $where .= " $etapa_table.id_etapa='$etapa_id'";
        }

        $user_type = $this->_get_clean_value($options, "user_type");
        if ($user_type && $user_type === "client") {
            $where = " AND $etapa_table.show_in_client_portal=1";
        }

        $sql = "SELECT $etapa_table.*
        FROM $etapa_table WHERE $where ";

        $result = $this->db->query($sql);

        if ($result->resultID->num_rows) {
            return $result->getRow();
        }
    }

    function save_item_and_update_invoice($data, $id, $invoice_id) {
        $result = $this->ci_save($data, $id);

        $invoices_model = model("App\Models\Invoices_model");
        $invoices_model->update_invoice_total_meta($invoice_id);

        return $result;
    }

    function delete_item_and_update_invoice($id, $undo = false) {
        $item_info = $this->get_one($id);

        $result = $this->delete($id, $undo);

        $invoices_model = model("App\Models\Invoices_model");
        $invoices_model->update_invoice_total_meta($item_info->invoice_id);

        return $result;
    }
}
