<?php

namespace Demo\Controllers;

use App\Controllers\Security_Controller;

class Composicao extends Security_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
        
        return $this->template->rander('Demo\Views\composicao\index');
    }
    
    function list_data() {
 
    
        $category_id = $this->request->getPost('category_id');
        $options = array("category_id" => $category_id);

        $list_data = $this->Items_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_item_row($data);
        }
        echo json_encode(array("data" => $result));
    }

}
