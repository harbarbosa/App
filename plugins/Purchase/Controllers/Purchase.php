<?php

namespace Purchase\Controllers;

use App\Controllers\Security_Controller;
use App\Libraries\Pdf;
use stdClass;

/**
 * This class describes a purchase.
 */
class Purchase extends Security_Controller {
	protected $Purchase_model;

	/**
	 * Constructs a new instance.
	 */
    function __construct() {
        parent::__construct();
        $this->Purchase_model = new \Purchase\Models\Purchase_model();
        app_hooks()->do_action('app_hook_purchase_init');
    }

    	
    /**
     * { vendors }
     */
    public function vendors(){
    	
        $data['title']          = app_lang('vendors');

        return $this->template->rander('Purchase\Views\vendors\manage', $data);
    }

    /* list of vendors, prepared for datatable  */

    public function list_vendor_data() {
        $options = array(
        );

        $list_data = $this->Purchase_model->get_vendor_table($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_vendor_row($data);
        }

        echo json_encode(array("data" => $result));
    }

    /* prepare a row of vendor list table */

    private function _make_vendor_row($data) {
        $edit = '<li role="presentation"><a href="'.get_uri('purchase/vendor/'. $data->userid).'" class="dropdown-item"><i data-feather="eye" class="icon-16"></i>&nbsp;&nbsp;'.app_lang('view').'</a></li>';

                $delete = '<li role="presentation">' . modal_anchor(get_uri("purchase/delete_vendor_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $data->userid, "class" => "dropdown-item")) . '</li>';


                $_data = '
                <span class="dropdown inline-block">
                <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
                <i data-feather="tool" class="icon-16"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" role="menu">' . $edit . $delete. '</ul>
                </span>';

        $primary_contact_name =  $this->Purchase_model->get_primary_contact_name_of_vendor($data->userid);
        $primary_contact_email =  $this->Purchase_model->get_primary_contact_email_of_vendor($data->userid);

        $row_data = array(
        	$data->userid, 
        	$data->company,
        	$primary_contact_name,
        	$primary_contact_email,
        	$data->phonenumber,
        	$data->datecreated,
            $_data,
        );
      

        return $row_data;
    }

    /**
     * { settings }
     *
     * @return     view
     */
    public function settings(){
    	$data['title']          = app_lang('settings');

    	$data['group'] = $this->request->getGet('group');

    	$data['tab'][] = 'purchase_order_settings';
       	$data['tab'][] = 'purchase_options';
       	$data['tab'][] = 'units';
        $data['tab'][] = 'approval';
       	$data['tab'][] = 'commodity_group';
        $data['tab'][] = 'vendor_category';


        if($data['group'] == ''){
            $data['group'] = 'purchase_order_settings';
        }else if($data['group'] == 'units'){
            $data['unit_types'] = $this->Purchase_model->get_unit_type();
        }

    	return $this->template->rander('Purchase\Views\settings\manage', $data);
    }

    /**
     * { list unit data }
     */
    public function list_unit_data() {
        $options = array(
        );

        $list_data = $this->Purchase_model->get_unit_table($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_unit_row($data);
        }
        
        echo json_encode(array("data" => $result));
    }

    /* prepare a row of units list table */

    private function _make_unit_row($data) {
    	$code_row = '<a href="#" data-act="ajax-modal" data-title="'.app_lang('edit_unit').'" data-action-url="'.get_uri('purchase/modal_unit_form/'.$data->unit_type_id).'">'.$data->unit_code.'</a>';
    	$display_row = ($data->display == 1) ? app_lang('display') : app_lang('not_display');
    	$options = '';
    	$options .= '<a href="#" data-act="ajax-modal" data-title="'.app_lang('edit_unit').'" class="btn btn-primary icon-btn mr5" data-action-url="'.get_uri('purchase/modal_unit_form/'.$data->unit_type_id).'"><i data-feather="edit" class="icon-16"></i></a>';

    	$options .= '<a href="#" data-action-url="'.get_uri('purchase/delete_unit/'.$data->unit_type_id).'" data-id="'.$data->unit_type_id.'"  data-action="delete-confirmation" class="delete btn btn-danger icon-btn" ><i data-feather="trash" class="icon-16"></i></a>';

        $row_data = array(
        	$data->unit_type_id, 
        	$code_row,
        	$data->unit_name,
        	$data->unit_symbol,
        	$data->order,
        	$display_row,
        	$data->note,
        	$options
        );
      
        return $row_data;
    }

    /**
     * { delete unit }
     *
     * @param        $id     The identifier
     */
    public function delete_unit(){
        $id = $this->request->getPost('id');
    	$deleted = $this->Purchase_model->delete_unit($id);
    	if($deleted){
    		echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
    	}else{

    	   echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    /**
     * { modal unit form }
     */
    public function modal_unit_form($id = ''){
    	$view_data = [];

    	if($id != ''){
    		$view_data['unit'] = $this->Purchase_model->get_unit_type($id);
    	}

    	return $this->template->view('Purchase\Views\settings\modal\modal_unit_form', $view_data);
    }

    /**
     * { unit save }
     */
    public function unit_save(){
    	if($this->request->getPost()){
    		$data = $this->request->getPost();

    		if($data['unit_type_id'] == ''){
    			unset($data['unit_type_id']);
    			$unit_id = $this->Purchase_model->add_unit($data);

    			if($unit_id){
    				$this->session->setFlashdata("success_message", app_lang("added_unit_successfully"));
    			}
    		}else{
    			$success = $this->Purchase_model->update_unit($data);

    			if($success){
    				$this->session->setFlashdata("success_message", app_lang("updated_unit_successfully"));
    			}
    		}

    		app_redirect('purchase/settings?group=units');
    	}
    }

    /**
     * { function_description }
     */
    public function list_item_categories_data(){
        $options = array(
        );

        $list_data = $this->Purchase_model->get_item_categories_table($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_item_category_row($data);
        }
        
        echo json_encode(array("data" => $result));
    }

    /* prepare a row of units list table */

    private function _make_item_category_row($data) {
        $code_row = '<a href="#" data-act="ajax-modal" data-title="'.app_lang('edit_commodity_group').'" data-action-url="'.get_uri('purchase/modal_commodity_group_form/'.$data->id).'">'.$data->commodity_group_code.'</a>';
        $display_row = ($data->display == 1) ? app_lang('display') : app_lang('not_display');
        $options = '';
        $options .= '<a href="#" data-act="ajax-modal" data-title="'.app_lang('edit_commodity_group').'" class="btn btn-primary icon-btn mr5" data-action-url="'.get_uri('purchase/modal_commodity_group_form/'.$data->id).'"><i data-feather="edit" class="icon-16"></i></a>';

        $options .= '<a href="#" data-action-url="'.get_uri('purchase/delete_commodity_group').'" data-action="delete-confirmation" data-id="'.$data->id.'" class="btn btn-danger icon-btn delete" ><i data-feather="trash" class="icon-16"></i></a>';

        $row_data = array(
            $data->id, 
            $code_row,
            $data->title,
            $data->order,
            $display_row,
            $data->note,
            $options
        );
      
        return $row_data;
    }


    /**
     * { modal unit form }
     */
    public function modal_commodity_group_form($id = ''){
        $view_data = [];

        if($id != ''){
            $view_data['commodity_group'] = $this->Purchase_model->get_commodity_group($id);
        }

        return $this->template->view('Purchase\Views\settings\modal\modal_commodity_group_form', $view_data);
    }

    /**
     * { commodity group save }
     */
    public function commodity_group_save(){
        if($this->request->getPost()){
            $data = $this->request->getPost();

            if($data['id'] == ''){
                unset($data['id']);
                $id = $this->Purchase_model->add_commodity_group($data);

                if($id){
                    $this->session->setFlashdata("success_message", app_lang("added_commodity_group_successfully"));
                }
            }else{
                $success = $this->Purchase_model->update_commodity_group($data);

                if($success){
                    $this->session->setFlashdata("success_message", app_lang("updated_commodity_group_successfully"));
                }
            }

            app_redirect('purchase/settings?group=commodity_group');
        }
    }

    /**
     * { delete sub group }
     *
     * @param        $id     The identifier
     */
    public function delete_commodity_group(){

        $id = $this->request->getPost('id');
        $deleted = $this->Purchase_model->delete_commodity_group($id);
        if($deleted){
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        }else{

           echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }

    }
    
    /**
     * { list subgroup data }
     */
    public function list_subgroup_data(){
        $options = array(
        );

        $list_data = $this->Purchase_model->get_subgroup_table($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_subgroup_row($data);
        }
        
        echo json_encode(array("data" => $result));
    }

    /* prepare a row of units list table */

    private function _make_subgroup_row($data) {
        $code_row = '<a href="#" data-act="ajax-modal" data-title="'.app_lang('edit_sub_group').'" data-action-url="'.get_uri('purchase/modal_sub_group_form/'.$data->id).'">'.$data->sub_group_code.'</a>';
        $display_row = ($data->display == 1) ? app_lang('display') : app_lang('not_display');
        $options = '';
        $options .= '<a href="#" data-act="ajax-modal" data-title="'.app_lang('edit_sub_group').'" class="btn btn-primary icon-btn mr5" data-action-url="'.get_uri('purchase/modal_sub_group_form/'.$data->id).'"><i data-feather="edit" class="icon-16"></i></a>';

        $options .= '<a href="#" data-action-url="'.get_uri('purchase/delete_sub_group').'" data-action="delete-confirmation" data-id="'.$data->id.'" class="btn btn-danger icon-btn delete" ><i data-feather="trash" class="icon-16"></i></a>';

        $commodity_group_name = '';
        $commodity_group = $this->Purchase_model->get_commodity_group($data->group_id);
        if(isset($commodity_group->title)){
            $commodity_group_name = $commodity_group->title;
        }

        $row_data = array(
            $data->id, 
            $code_row,
            $data->sub_group_name,
            $commodity_group_name,
            $data->order,
            $display_row,
            $data->note,
            $options
        );
      
        return $row_data;
    }

    /**
     * { modal unit form }
     */
    public function modal_sub_group_form($id = ''){
        $view_data = [];

        $view_data['commodity_groups'] = $this->Purchase_model->get_commodity_group_add_commodity();

        if($id != ''){
            $view_data['sub_group'] = $this->Purchase_model->get_sub_group($id);
        }

        return $this->template->view('Purchase\Views\settings\modal\modal_sub_group_form', $view_data);
    }

    /**
     * { sub group save }
     */
    public function sub_group_save(){
        if($this->request->getPost()){
            $data = $this->request->getPost();

            if($data['id'] == ''){
                unset($data['id']);
                $id = $this->Purchase_model->add_sub_group($data);

                if($id){
                    $this->session->setFlashdata("success_message", app_lang("added_sub_group_successfully"));
                }
            }else{
                $success = $this->Purchase_model->update_sub_group($data);

                if($success){
                    $this->session->setFlashdata("success_message", app_lang("updated_sub_group_successfully"));
                }
            }

            app_redirect('purchase/settings?group=sub_group');
        }
    }

    /**
     * { delete sub group }
     *
     * @param        $id     The identifier
     */
    public function delete_sub_group(){
        $id = $this->request->getPost('id');

        $deleted = $this->Purchase_model->delete_sub_group($id);
        if($deleted){
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        }else{

           echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }

    }

    /**
     * { pur order setting }
     */
    public function pur_order_setting(){
        if($this->request->getPost()){
            $data = $this->request->getPost();

            $success = $this->Purchase_model->update_po_setting($data);
            if($success){
                $this->session->setFlashdata("success_message", app_lang("updated_setting_successfully"));
            }

            app_redirect('purchase/settings?group=purchase_order_settings');
        }
    }

    /**
     * { list vendor category data }
     */
    public function list_vendor_category_data(){
        $options = array(
        );

        $list_data = $this->Purchase_model->get_vendor_category_table($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_vendor_category_row($data);
        }
        
        echo json_encode(array("data" => $result));
    }

    /* prepare a row of units list table */

    private function _make_vendor_category_row($data) {

        $options = '';
        $options .= '<a href="#" data-act="ajax-modal" data-title="'.app_lang('edit_vendor_category').'" class="btn btn-primary icon-btn mr5" data-action-url="'.get_uri('purchase/modal_vendor_category_form/'.$data->id).'"><i data-feather="edit" class="icon-16"></i></a>';
        $options .= '<a href="#" data-action-url="'.get_uri('purchase/delete_vendor_category/'.$data->id).'" data-action="delete-confirmation" data-id="'.$data->id.'" class="btn btn-danger icon-btn delete" ><i data-feather="trash" class="icon-16"></i></a>';

        $row_data = array(
            $data->id, 
            $data->category_name,
            $data->description,
            $options
        );
      
        return $row_data;
    }

    /**
     * { modal vendor category form }
     *
     * @return       ( modal vendor category form )
     */
    public function modal_vendor_category_form($id = ''){
        $view_data = [];

        if($id != ''){
            $view_data['vendor_category'] = $this->Purchase_model->get_vendor_category($id);
        }

        return $this->template->view('Purchase\Views\settings\modal\modal_vendor_category_form', $view_data);
    }


    /**
     * { sub group save }
     */
    public function vendor_category_save(){
        if($this->request->getPost()){
            $data = $this->request->getPost();

            if($data['id'] == ''){
                unset($data['id']);
                $id = $this->Purchase_model->add_vendor_category($data);

                if($id){
                    $this->session->setFlashdata("success_message", app_lang("added_vendor_category_successfully"));
                }
            }else{
                $success = $this->Purchase_model->update_vendor_category($data);

                if($success){
                    $this->session->setFlashdata("success_message", app_lang("updated_vendor_category_successfully"));
                }
            }

            app_redirect('purchase/settings?group=vendor_category');
        }
    }

     /**
     * { delete sub group }
     *
     * @param        $id     The identifier
     */
    public function delete_vendor_category(){

        $id = $this->request->getPost();
        $deleted = $this->Purchase_model->delete_vendor_category($id);
        if($deleted){
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        }else{

           echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }

    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function purchase_order_setting(){
        $data = $this->request->getPost();
        if($data != 'null'){
            $value = $this->Purchase_model->update_purchase_setting($data);
            if($value){
                $success = true;
                $message = _l('updated_successfully');
            }else{
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function item_by_vendor(){
        $data = $this->request->getPost();
        if($data != 'null'){
            $value = $this->Purchase_model->update_purchase_setting($data);
            if($value){
                $success = true;
                $message = _l('updated_successfully');
            }else{
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function send_email_welcome_for_new_contact(){
        $data = $this->request->getPost();
        if($data != 'null'){
            $value = $this->Purchase_model->update_purchase_setting($data);
            if($value){
                $success = true;
                $message = _l('updated_successfully');
            }else{
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function show_tax_column(){
        $data = $this->request->getPost();
        if($data != 'null'){
            $value = $this->Purchase_model->update_purchase_setting($data);
            if($value){
                $success = true;
                $message = _l('updated_successfully');
            }else{
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function po_only_prefix_and_number(){
        $data = $this->request->getPost();
        if($data != 'null'){
            $value = $this->Purchase_model->update_purchase_setting($data);
            if($value){
                $success = true;
                $message = _l('updated_successfully');
            }else{
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function reset_purchase_order_number_every_month(){
        $data = $this->request->getPost();
        if($data != 'null'){
            $value = $this->Purchase_model->update_purchase_setting($data);
            if($value){
                $success = true;
                $message = _l('updated_successfully');
            }else{
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * commodity type modal form
     * @return [type] 
     */
    public function modal_approval_setting_form() {
        $this->access_only_team_members();

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));
        $data = [];
        $color_data = [];

        $id = $this->request->getPost('id');

        $options = array(
            "status" => "active",
            "user_type" => "staff",
        );
        $staffs = $this->Users_model->get_details($options)->getResultArray();
        $create_approval_setting_row_template = '';
        $create_approval_setting_row_template .= $this->Purchase_model->create_approval_setting_row_template($staffs);
        $data['key_number'] = 0;

        if($id && is_numeric($id)){
            $approval_setting = $this->Purchase_model->get_approval_setting($id);
            $data['approval_setting'] = $approval_setting;
            if($approval_setting && strlen($approval_setting->setting) > 0){
                $setting = json_decode($approval_setting->setting);
                $data['key_number'] = count($setting);
                $item_index = 1;
                foreach ($setting as $index => $value) {
                    $name = 'newitems['.$item_index.']';
                    $item_key  = $item_index;
                    $approver = $value->approver;
                    $staff = $value->staff;
                    $action = $value->action;
                    $create_approval_setting_row_template .= $this->Purchase_model->create_approval_setting_row_template($staffs, $name, $approver, $staff, $action, $item_key);
                    $item_index++;
                    
                }
            }

        }else{
            $id = '';
        }
        $data['id'] = $id;

        $data['create_approval_setting_row_template'] = $create_approval_setting_row_template;

        return $this->template->view('Purchase\Views\settings\modal\approval_setting_modal', $data);
    }

    /**
     * Gets the approval setting row template.
     */
    public function get_approval_setting_row_template()
    {
        $name = $this->request->getPost('name');
        $approver = $this->request->getPost('approver');
        $staff = $this->request->getPost('staff');
        $action = $this->request->getPost('action');
        $item_key = $this->request->getPost('item_key');
        $options = array(
            "status" => "active",
            "user_type" => "staff",
        );
        $staffs = $this->Users_model->get_details($options)->getResultArray();

        echo html_entity_decode($this->Purchase_model->create_approval_setting_row_template($staffs, $name, $approver, $staff, $action, $item_key ));
    }

    /**
     * list commodity type data
     * @return [type] 
     */
    public function list_approval_setting_data() {
        $this->access_only_team_members();

        $list_data = $this->Purchase_model->get_approval_setting();

        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_approval_setting_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /**
     * _make commodity type row
     * @param  [type] $data 
     * @return [type]       
     */
    private function _make_approval_setting_row($data) {

        return array(
            $data['id'],
            nl2br($data['name']),
            app_lang($data['related']),
            modal_anchor(get_uri("purchase/modal_approval_setting_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_approval_setting'), "data-post-id" => $data['id']))
            . '<a href="#" data-action-url="'.get_uri('purchase/delete_approval_setting').'" data-action="delete-confirmation" data-id="'.$data['id'].'" class="btn btn-danger icon-btn delete" ><i data-feather="trash" class="icon-16"></i></a>'
        );
    }

    /**
     * approval setting
     * @return redirect
     */
    public function approval_setting($id = '') {
        if ($this->request->getPost()) {
            $data = $this->request->getPost();

            if (!is_numeric($id)) {
                $message = '';
                $success = $this->Purchase_model->add_approval_setting($data);
                if ($success) {
                    $this->session->setFlashdata("success_message", app_lang("added_successfully"));
                }
                app_redirect("purchase/settings?group=approval");
            } else {
                $success = $this->Purchase_model->edit_approval_setting($id, $data);
                if ($success) {
                    $this->session->setFlashdata("success_message", app_lang("updated_successfully"));
                }
                app_redirect("purchase/settings?group=approval");
            }
        }
    }

    /**
     * table commodity list
     * @return [type] 
     */
    public function table_commodity_list() {
        $dataPost = $this->request->getPost();
        $this->Purchase_model->get_table_data(module_views_path('Purchase', 'items/table_commodity_list'), $dataPost);
    }

    /**
     * delete approval setting
     * @param  integer $id
     * @return redirect
     */
    public function delete_approval_setting() {
        
        $id = $this->request->getPost('id');

        $deleted = $this->Purchase_model->delete_approval_setting($id);
        if($deleted){
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        }else{

           echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }

      
    }

    /**
     * { items }
     */
    public function items(){
        $data['units'] = $this->Purchase_model->get_unit_add_item();
        $data['taxes'] = [];
        $data['commodity_groups'] = $this->Purchase_model->get_commodity_group_add_commodity();
        $data['sub_groups'] = $this->Purchase_model->get_sub_group();
        $data['title'] = app_lang('item_list');


        return $this->template->rander('Purchase\Views\items\item_list', $data);
    }


    /**
     * modal form
     * @return [type] 
     */
    public function item_modal_form() {
        $this->access_only_team_members();

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $data['model_info'] = $this->Items_model->get_one($this->request->getPost('id'));
        $data['categories_dropdown'] = $this->Item_categories_model->get_dropdown_list(array("title"));
        $data['units'] = $this->Purchase_model->get_unit_add_item();


        $data['commodity_groups'] = $this->Purchase_model->get_commodity_group_add_commodity();
        $tax_options = array(
            "deleted" => 0,
        );
        $data['taxes'] = $this->Taxes_model->get_details($tax_options)->getResultArray();


        $data['sub_groups'] = [];
        $data['item_tags'] = [];

        $data['title'] = _l('commodity_list');

        $data['ajaxItems'] = false;

        if(!$this->request->getPost('id')){


            /*for create*/
        
            $data['get_commodity_barcode'] = $this->Purchase_model->generate_commodity_barcode();
          
            $data['parent_item_hide'] = false;

        }else{
            $id = $this->request->getPost('id');
            $data['item'] = $this->Purchase_model->get_commodity($id);

            //check have child item
            $flag_is_parent = false;
            $data['parent_item_hide'] = false;

            $builder = db_connect('default');
            $builder = $builder->table('items');
            $builder->where('parent_id', $id);
            $array_child_value = $builder->get()->getResultArray();

            if(count($array_child_value) > 0){
                $flag_is_parent = true;
                $data['parent_item_hide'] = true;
            }

            if($data['item']){
                $parent_id = $data['item']->parent_id;
            }else{
                $parent_id = '';
            }
        }

        return $this->template->view('Purchase\Views\items\item_modal', $data);
    }

    /**
     * commodity list add edit
     * @param  integer $id
     * @return json
     */
    public function commodity_list_add_edit($id = '') {
        $data = $this->request->getPost();
        if ($data) {
             $id = $this->request->getPost('id');

            $target_path = get_setting("timeline_file_path");
            $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "item");
            $new_files = unserialize($files_data);

            if ($id) {
                $item_info = $this->Items_model->get_one($id);
                $timeline_file_path = get_setting("timeline_file_path");

                $new_files = update_saved_files($timeline_file_path, $item_info->files, $new_files);
            }
            $data["files"] = serialize($new_files);

            if (!$id) {

                $result = $this->Purchase_model->add_commodity_one_item($data);
                if ($result) {
                    $this->session->setFlashdata("success_message", app_lang("added_successfully"));
                } else {
                    $this->session->setFlashdata("error_message", app_lang("add_failed"));
                }
                app_redirect("purchase/items");

            } else {

                $id = $data['id'];
                if(isset($data['id'])){
                    unset($data['id']);
                }
                $result = $this->Purchase_model->update_commodity_one_item($data, $id);

                if ($result) {
                    $this->session->setFlashdata("success_message", app_lang("updated_successfully"));
                }
                app_redirect("purchase/items");
            }
        }

    }

    /**
     * delete modal form
     * @return [type] 
     */
    public function delete_modal_form() {
        $this->access_only_team_members();
  

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        if($this->request->getPost('id')){
            $data['function'] = 'delete_commodity';
            $data['id'] = $this->request->getPost('id');
            return $this->template->view('Purchase\Views\items\delete_modal_form', $data);
        }
    }

    /**
     * delete modal form
     * @return [type] 
     */
    public function delete_vendor_modal_form() {
        $this->access_only_team_members();


        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        if($this->request->getPost('id')){
            $data['function'] = 'delete_vendor';
            $data['id'] = $this->request->getPost('id');
            return $this->template->view('Purchase\Views\items\delete_modal_form', $data);
        }
    }

    /**
     * delete modal form
     * @return [type] 
     */
    public function delete_estimate_modal() {

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        if($this->request->getPost('id')){
            $data['function'] = 'delete_pur_estimate';
            $data['id'] = $this->request->getPost('id');
            return $this->template->view('Purchase\Views\items\delete_modal_form', $data);
        }
    }

    /**
     * { delete pur order modal }
     *
     */
    public function delete_pur_order_modal(){
       $this->access_only_team_members();


        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        if($this->request->getPost('id')){
            $data['function'] = 'delete_pur_order';
            $data['id'] = $this->request->getPost('id');
            return $this->template->view('Purchase\Views\items\delete_modal_form', $data);
        } 
    }


    /**
     * delete modal form
     * @return [type] 
     */
    public function delete_pur_request_modal() {

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        if($this->request->getPost('id')){
            $data['function'] = 'delete_pur_request';
            $data['id'] = $this->request->getPost('id');
            return $this->template->view('Purchase\Views\items\delete_modal_form', $data);
        }
    }

    /**
     * delete modal form
     * @return [type] 
     */
    public function delete_vendor_item_modal_form() {
        $this->access_only_team_members();

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        if($this->request->getPost('id')){
            $data['function'] = 'delete_vendor_items';
            $data['id'] = $this->request->getPost('id');
            return $this->template->view('Purchase\Views\items\delete_modal_form', $data);
        }
    }

    /**
     * delete commodity
     * @param  integer $id
     * @return redirect
     */
    public function delete_vendor() {
        $id = $this->request->getPost('id');
        
        if (!$id) {
            app_redirect('purchase/items');
        }

        $response = $this->Purchase_model->delete_vendor($id);
        if (is_array($response) && isset($response['referenced'])) {
            $this->session->setFlashdata("error_message", app_lang("is_referenced"));
        } elseif ($response == true) {
            $this->session->setFlashdata("success_message", app_lang("record_deleted"));
        } else {
            $this->session->setFlashdata("error_message", app_lang("problem_deleting"));
        }
        app_redirect('purchase/vendors');
    }

    /**
     * delete commodity
     * @param  integer $id
     * @return redirect
     */
    public function delete_pur_request() {
        $id = $this->request->getPost('id');
        
        if (!$id) {
            app_redirect('purchase/items');
        }

        $response = $this->Purchase_model->delete_pur_request($id);
        if (is_array($response) && isset($response['referenced'])) {
            $this->session->setFlashdata("error_message", app_lang("is_referenced"));
        } elseif ($response == true) {
            $this->session->setFlashdata("success_message", app_lang("record_deleted"));
        } else {
            $this->session->setFlashdata("error_message", app_lang("problem_deleting"));
        }
        app_redirect('purchase/purchase_request');
    }

    /**
     * delete commodity
     * @param  integer $id
     * @return redirect
     */
    public function delete_pur_estimate() {
        $id = $this->request->getPost('id');
        
        if (!$id) {
            app_redirect('purchase/quotations');
        }

        $response = $this->Purchase_model->delete_pur_estimate($id);
        if (is_array($response) && isset($response['referenced'])) {
            $this->session->setFlashdata("error_message", app_lang("is_referenced"));
        } elseif ($response == true) {
            $this->session->setFlashdata("success_message", app_lang("record_deleted"));
        } else {
            $this->session->setFlashdata("error_message", app_lang("problem_deleting"));
        }
        app_redirect('purchase/quotations');
    }


    /**
     * delete commodity
     * @param  integer $id
     * @return redirect
     */
    public function delete_pur_order() {
        $id = $this->request->getPost('id');
        
        if (!$id) {
            app_redirect('purchase/purchase_orders');
        }

        $response = $this->Purchase_model->delete_pur_order($id);
        if (is_array($response) && isset($response['referenced'])) {
            $this->session->setFlashdata("error_message", app_lang("is_referenced"));
        } elseif ($response == true) {
            $this->session->setFlashdata("success_message", app_lang("record_deleted"));
        } else {
            $this->session->setFlashdata("error_message", app_lang("problem_deleting"));
        }
        app_redirect('purchase/purchase_orders');
    }


    /**
     * delete commodity
     * @param  integer $id
     * @return redirect
     */
    public function delete_commodity() {
        $id = $this->request->getPost('id');
        
        if (!$id) {
            app_redirect('purchase/items');
        }

        $response = $this->Purchase_model->delete_commodity($id);
        if (is_array($response) && isset($response['referenced'])) {
            $this->session->setFlashdata("error_message", app_lang("is_referenced"));
        } elseif ($response == true) {
            $this->session->setFlashdata("success_message", app_lang("wh_deleted"));
        } else {
            $this->session->setFlashdata("error_message", app_lang("problem_deleting"));
        }
        app_redirect('purchase/items');
    }

    /**
     * warehouse delete bulk action
     * @return
     */
    public function purchase_delete_bulk_action()
    {
        $this->access_only_team_members();

        $total_deleted = 0;
        $total_updated = 0;
        $total_cloned = 0;
        if ($this->request->getPost()) {

            $ids                   = $this->request->getPost('ids');
            $rel_type                   = $this->request->getPost('rel_type');

            /*check permission*/
            switch ($rel_type) {
                case 'commodity_list':
                if (!has_permission('purchase', '', 'delete') && !$this->login_user->is_admin) {
                    app_redirect("forbidden");
                }
                break;

                case 'change_item_selling_price':
                if (!has_permission('purchase', '', 'edit') && !$this->login_user->is_admin) {
                    app_redirect("forbidden");
                }
                break;

                case 'change_item_purchase_price':
                if (!has_permission('purchase', '', 'edit') && !$this->login_user->is_admin) {
                    app_redirect("forbidden");
                }
                break;

                


                default:
                break;
            }

            /*delete data*/
            if ( $this->request->getPost('mass_delete') && $this->request->getPost('mass_delete') == 'true' ) {
                if (is_array($ids)) {
                    foreach ($ids as $id) {

                        switch ($rel_type) {
                            case 'commodity_list':
                            if ($this->Purchase_model->delete_commodity($id)) {
                                $total_deleted++;
                                break;
                            }else{
                                break;
                            }

                            case 'vendor_items':
                            if ($this->Purchase_model->delete_vendor_items($id)) {
                                $total_deleted++;
                                break;
                            }else{
                                break;
                            }
                                

                            default:

                            break;
                        }


                    }
                }

                /*return result*/
                switch ($rel_type) {
                    case 'commodity_list':
                    $this->session->setFlashdata("success_message", app_lang("total_commodity_list"). ": " .$total_deleted);

                    break;

                    default:
                    break;

                }


            }

            /*TODO*/
            // Clone items
            if ($this->request->getPost('clone_items') && $this->request->getPost('clone_items') == 'true') {
                if (is_array($ids)) {
                    foreach ($ids as $id) {

                            switch ($rel_type) {
                                case 'commodity_list':
                                    if ($this->Purchase_model->clone_item($id)) {
                                        $total_cloned++;
                                        break;
                                    }else{
                                        break;
                                    }
                                
                                default:
                                   
                                    break;
                            }
                        }
                    }
                /*return result*/
                switch ($rel_type) {
                    case 'commodity_list':
                        $this->session->setFlashdata("success_message", app_lang("total_commodity_list"). ": " .$total_cloned);

                        break;

                    default:
                        break;

                }
            }

            // update selling price, purchase price
            if ( ($this->request->getPost('change_item_selling_price') ) || ($this->request->getPost('change_item_purchase_price') )  )  {

                if (is_array($ids)) {
                    foreach ($ids as $id) {

                        switch ($rel_type) {
                            case 'change_item_selling_price':
                            if ($this->Purchase_model->commodity_udpate_profit_rate($id, $this->request->getPost('selling_price'), 'selling_percent' )) {
                                $total_updated++;
                                break;
                            }else{
                                break;
                            }

                            case 'change_item_purchase_price':
                            if ($this->Purchase_model->commodity_udpate_profit_rate($id, $this->request->getPost('purchase_price'), 'purchase_percent' )) {
                                $total_updated++;
                                break;
                            }else{
                                break;
                            }
                            

                            default:

                            break;
                        }


                    }
                }

                /*return result*/
                switch ($rel_type) {
                    case 'change_item_selling_price':
                    $this->session->setFlashdata("success_message", app_lang("total_commodity_list"). ": " .$total_updated);
                    break;

                    case 'change_item_purchase_price':
                    $this->session->setFlashdata("success_message", app_lang("total_commodity_list"). ": " .$total_updated);
                    break;

                    default:
                    break;

                }

            }


        }

    }

    /**
     * { vendor }
     *
     * @param      string  $id     The identifier
     */
    public function vendor($id = ''){

        if ($this->request->getPost() ) {
            if ($id == '') {
                $data = $this->request->getPost();

                $save_and_add_contact = false;
                if (isset($data['save_and_add_contact'])) {
                    unset($data['save_and_add_contact']);
                    $save_and_add_contact = true;
                }
                $id = $this->Purchase_model->add_vendor($data);
                
                
                if ($id) {

                    $this->session->setFlashdata("success_message", app_lang("added_vendor_successfully"));
                    if ($save_and_add_contact == false) {
                        app_redirect('purchase/vendor/' . $id);
                    } else {
                        app_redirect('purchase/vendor/' . $id . '?group=contacts');
                    }
                }
            } else {
                
                $success = $this->Purchase_model->update_vendor($this->request->getPost(), $id);
                if ($success == true) {
                    $this->session->setFlashdata("success_message", app_lang("updated_vendor_successfully"));
                }
                app_redirect('purchase/vendor/' . $id);
            }
        }


        $group         = !$this->request->getGet('group') ? 'profile' : $this->request->getGet('group');
        $data['group_tab'] = $group;

        if ($group != 'contacts' && $contact_id = $this->request->getGet('contactid')) {
            app_redirect('purchase/vendor/' . $id . '?group=contacts&contactid=' . $contact_id);
        }

        if ($id == '') {
            $title = app_lang('add_new_vendor');
        } else {
            $client                = $this->Purchase_model->get_vendor($id);


            if (!$client) {
                show_404();
            }


            $data['group_tab'] = $this->request->getGet('group');


            $data['title']                 = app_lang('setting');
            $data['tab'][] = ['name' => 'profile', 'icon' => '<i class="fa fa-user-circle menu-icon"></i>'];
            $data['tab'][] = ['name' => 'contacts','icon' => '<i class="fa fa-users menu-icon"></i>'];

            if($this->login_user->user_type == 'staff'){
                $data['tab'][] = ['name' => 'quotations','icon' => '<i class="fa fa-file-powerpoint-o menu-icon"></i>'];
                $data['tab'][] = ['name' => 'purchase_order', 'icon' => '<i class="fa fa-cart-plus menu-icon"></i>'];
                $data['tab'][] = ['name' => 'purchase_invoice', 'icon' => '<i class="fa fa-clipboard menu-icon"></i>'];
                $data['tab'][] = ['name' => 'payments', 'icon' => '<i class="fa fa-usd menu-icon"></i>']; 
            }

            
            if($data['group_tab'] == ''){
                $data['group_tab'] = 'profile';
            }
            $data['tabs']['view'] = 'Purchase\Views\vendors\groups\\'.$data['group_tab'];
            // Fetch data based on groups
     

            $options = array();
            $data['staff'] = $this->Users_model->get_details($options)->getResultArray();

            $data['client'] = $client;
            $title          = $client->company;

            // Get all active staff members (used to add reminder)
            $data['members'] = $data['staff'];

        }

        $data['payments'] = $this->Purchase_model->get_payment_invoices_by_vendor($id);

        $data["currency_dropdown"] = $this->_get_currency_dropdown_select2_data();

        $data['vendor_categories'] = $this->Purchase_model->get_vendor_category();
        $data['title']     = $title;

        return $this->template->rander('Purchase\Views\vendors\vendor', $data);
    }


    /**
     * Determines if vendor code exists.
     */
    public function vendor_code_exists()
    {
        $builder = db_connect('default');
        $builder = $builder->table(get_db_prefix().'pur_vendor');
        if ($this->request->getPost()) {
            // First we need to check if the email is the same
            $id = $this->request->getPost('userid');
            if ($id != '') {
                $builder->where('userid', $id);
                $pur_vendor = $builder->get()->getRow();
                if ($pur_vendor->vendor_code == $this->request->getPost('vendor_code')) {
                    echo json_encode(true);
                    die();
                }
            }
            $builder->where('vendor_code', $this->request->getPost('vendor_code'));
            $total_rows = $builder->get()->getNumRows();
            if ($total_rows > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
            die();
        }
        
    }

    /**
     * { vendor items }
     */
    public function vendor_items(){

        $data['title'] = app_lang('vendor_items');
        $data['vendors'] = $this->Purchase_model->get_vendors();

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->Purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        

        $data['commodity_groups'] = $this->Purchase_model->get_commodity_group_add_commodity();
        return $this->template->rander('Purchase\Views\vendor_items\manage', $data);
    }

    /**
     * { vendor items table }
     */
    public function vendor_items_table(){
        $select = [
                db_prefix() . 'pur_vendor_items.id as vendor_items_id',
                db_prefix() . 'pur_vendor_items.items as items',
                db_prefix() . 'pur_vendor.company as company', 
                db_prefix() . 'pur_vendor_items.add_from as pur_vendor_items_addedfrom', 
               
            ];
            $where = [];
            

            if ($this->request->getPost('vendor_filter')) {
                $vendor_filter  = $this->request->getPost('vendor_filter');
                array_push($where, 'AND vendor IN ('. implode(',', $vendor_filter).')');
            }

            if ($this->request->getPost('group_items_filter')) {
                $group_items_filter  = $this->request->getPost('group_items_filter');
                array_push($where, 'AND group_items IN ('. implode(',', $group_items_filter).')');
            }

            if ($this->request->getPost('items_filter')) {
                $items_filter  = $this->request->getPost('items_filter');
                array_push($where, 'AND items = '.$items_filter);
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'pur_vendor_items';
            $join         = ['LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'pur_vendor_items.vendor',
                            'LEFT JOIN ' . db_prefix() . 'items ON ' . db_prefix() . 'items.id = ' . db_prefix() . 'pur_vendor_items.items'
                        ];

            $result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where,[db_prefix() . 'pur_vendor.userid as userid','datecreate','description','commodity_code', 'title'], '', [], $this->request->getPost() );

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total' => 0,
            ];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['vendor_items_id'] . '"><label></label></div>';

                $row[] = '<a href="'.get_uri('purchase/vendor/'.$aRow['userid']).'">'.$aRow['company'].'</a>';

                $row[] = '<a href="'.get_uri('purchase/items/'.$aRow['items']).'" >'.$aRow['commodity_code'].' - '.$aRow['title'].'</a>';

                $row[] = _d($aRow['datecreate']);

                $options = '<a href="#" data-action-url="'.get_uri('purchase/delete_vendor_items').'" data-action="delete-confirmation" data-id="'.$aRow['vendor_items_id'].'" class="btn btn-danger icon-btn delete"><i data-feather="trash" class="icon-16"></i></a>';

                $options = modal_anchor(get_uri("purchase/delete_vendor_item_modal_form"), "<i data-feather='trash' class='icon-16'></i> " , array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['vendor_items_id'], "class" => "btn btn-danger delete icon-btn"));

                $row[] =  $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
    }

     /**
     * delete vendor items
     * @param  integer $id
     * @return redirect
     */
    public function delete_vendor_items() {
        
        $id = $this->request->getPost('id');

        $deleted = $this->Purchase_model->delete_vendor_items($id);
        if($deleted){

            $this->session->setFlashdata('success_message', app_lang('record_deleted'));
        }else{

            $this->session->setFlashdata('error_message', app_lang('record_cannot_be_deleted'));
        }

        app_redirect('purchase/vendor_items');
    }

    /**
     * { new vendor items }
     */
    public function new_vendor_items(){

        if ($this->request->getPost()) {
            $data                = $this->request->getPost();
          
            $success = $this->Purchase_model->add_vendor_items($data);
            if ($success) {
                $this->session->setFlashdata("success_message", app_lang("added_successfully"));
            }
            app_redirect('purchase/vendor_items');
        }

        $data['title'] = _l('vendor_items');

        $data['vendors'] = $this->Purchase_model->get_vendors();
        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->Purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        $data['commodity_groups'] = $this->Purchase_model->get_commodity_group_add_commodity();
    
        return $this->template->rander('Purchase\Views\vendor_items\vendor_items', $data);
    }

    /**
     * { group item change }
     */
    public function group_it_change($group = ''){
        if($group != ''){
            $html = '';
            if (total_rows(db_prefix() . 'items', [ 'category_id' => $group ]) <= ajax_on_total_items()) {
                $list_items = $this->Purchase_model->get_item_by_group($group);
                if(count($list_items) > 0){
                    foreach($list_items as $item){
                        $html .= '<option value="'.$item['id'].'" selected>'.$item['commodity_code'].' - '.$item['title'].'</option>';
                    }
                }
            }

            echo json_encode([
                'html' => $html,
            ]);
        }else{

            $html = '';
            if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
                $items = $this->Purchase_model->get_item();
                if(count($items) > 0){
                    foreach($items as $it){
                        $html .= '<option value="'.$it['id'].'">'.$it['commodity_code'].' - '.$it['title'].'</option>';
                    }
                }
            }

            echo json_encode([
                'html' => $html,
            ]);
        }   

    }

    /**
     * get commodity
     * @param  boolean $id
     * @return array or object
     */
    public function get_item($id = false)
    {

        if (is_numeric($id)) {
        $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'items')->row();
        }
        if ($id == false) {
            return $this->db->query('select * from ' . db_prefix() . 'items where active = 1 AND id not in ( SELECT distinct parent_id from '.db_prefix().'items WHERE parent_id is not null AND parent_id != "0" ) order by id desc')->result_array();

        }

    }
    /**
     * caculator sale price
     * @return float 
     */
    public function caculator_sale_price()
    {
        $data = $this->request->getPost();
        $sale_price = 0;

        /*type : 0 purchase price, 1: sale price*/
        $profit_type = get_setting('profit_rate_by_purchase_price_sale');
        $the_fractional_part = get_setting('warehouse_the_fractional_part');
        $integer_part = get_setting('warehouse_integer_part');

        $profit_rate = $data['profit_rate'];
        $purchase_price = $data['purchase_price'];

        switch ($profit_type) {
            case '0':
                # Calculate the selling price based on the purchase price rate of profit
                # sale price = purchase price * ( 1 + profit rate)
            if( ($profit_rate =='') || ($profit_rate == '0')|| ($profit_rate == 'null') ){

                $sale_price = (float)$purchase_price;
            }else{
                $sale_price = (float)$purchase_price*(1+((float)$profit_rate/100));

            }
            break;

            case '1':
                # Calculate the selling price based on the selling price rate of profit
                # sale price = purchase price / ( 1 - profit rate)
            if( ($profit_rate =='') || ($profit_rate == '0')|| ($profit_rate == 'null') ){

                $sale_price = (float)$purchase_price;
            }else{
                $sale_price = (float)$purchase_price/(1-((float)$profit_rate/100));

            }
            break;
            
        }

        //round sale_price
        $sale_price = round($sale_price, (int)$the_fractional_part);

        if($integer_part != '0'){
            $integer_part = 0 - (int)($integer_part);
            $sale_price = round($sale_price, $integer_part);
        }

        echo json_encode([
            'sale_price' => $sale_price,
        ]);
        die;

    }

    /**
     * caculator purchase price
     * @return json 
     */
    public function caculator_profit_rate()
    {
        $data = $this->request->getPost();
        $profit_rate = 0;

        /*type : 0 purchase price, 1: sale price*/
        $profit_type = get_setting('profit_rate_by_purchase_price_sale');
        $the_fractional_part = get_setting('warehouse_the_fractional_part');
        $integer_part = get_setting('warehouse_integer_part');

        $purchase_price = $data['purchase_price'];
        $sale_price = $data['sale_price'];


        switch ($profit_type) {
            case '0':
                # Calculate the selling price based on the purchase price rate of profit
                # sale price = purchase price * ( 1 + profit rate)

            if( ($purchase_price =='') || ($purchase_price == '0')|| ($purchase_price == 'null') ){
                $profit_rate = 0;

            }else{
                $profit_rate = (((float)$sale_price/(float)$purchase_price)-1)*100;

            }
            break;

            case '1':
                # Calculate the selling price based on the selling price rate of profit
                # sale price = purchase price / ( 1 - profit rate)

            $profit_rate = (1-((float)$purchase_price/(float)$sale_price))*100;

            break;

        }


        echo json_encode([
            'profit_rate' => $profit_rate,
        ]);
        die;

    }

    /**
     * caculator purchase price
     * @return [type] 
     */
    public function caculator_purchase_price()
    {
        $data = $this->request->getPost();

        $purchase_price = $this->Purchase_model->caculator_purchase_price_model($data['profit_rate'], $data['sale_price']);

        echo json_encode([
            'purchase_price' => $purchase_price,
        ]);
        die;

    }

    /**
     * { purchase request }
     */
    public function purchase_request(){

        $data['title'] = _l('purchase_request');

        $data['user_type'] = $this->login_user->user_type;

        $data['vendors'] = $this->Purchase_model->get_vendors();
        $data['teams'] = $this->Team_model->get_details()->getResultArray();


        return $this->template->rander('Purchase\Views\purchase_request\manage', $data);
    }

    /**
     * { table pur request }
     */
    public function table_pur_request(){
        $dataPost = $this->request->getPost();
        $dataPost['user_type'] = $this->login_user->user_type;
        $this->Purchase_model->get_table_data(module_views_path('Purchase', 'purchase_request/table_pur_request'), $dataPost);
    }

    /**
     * { pur request }
     */
    public function pur_request($id = ''){

        if($id == ''){
            
            if($this->request->getPost()){
                $add_data = $this->request->getPost();
                $id = $this->Purchase_model->add_pur_request($add_data);
                if($id){
                    $this->session->setFlashdata("success_message", app_lang("added_successfully"));
                }
                app_redirect('purchase/purchase_request');
            }

            $data['title'] = _l('add_new');
        }else{
            if($this->request->getPost()){
                $edit_data = $this->request->getPost();
                $success = $this->Purchase_model->update_pur_request($edit_data,$id);
                if($success == true){

                    $this->session->setFlashdata("success_message", app_lang("updated_successfully"));
                }
                app_redirect('purchase/purchase_request');
            }

            $data['pur_request_detail'] = json_encode($this->Purchase_model->get_pur_request_detail($id));
            $data['pur_request'] = $this->Purchase_model->get_purchase_request($id);
            $data['taxes_data'] = $this->Purchase_model->get_html_tax_pur_request($id);
            $data['title'] = _l('edit');
        }

        $data['base_currency'] = get_setting('default_currency');

        $purchase_request_row_template = $this->Purchase_model->create_purchase_request_row_template();

        if($id != ''){
            $data['pur_request_detail'] = $this->Purchase_model->get_pur_request_detail($id);
            $currency_rate = 1;

            if($data['pur_request']->currency != '' && $data['pur_request']->currency_rate != null){
                $currency_rate = $data['pur_request']->currency_rate;
            }

            $to_currency = $data['base_currency'];
            if($data['pur_request']->currency != '' && $data['pur_request']->to_currency != null) {
                $to_currency = $data['pur_request']->to_currency;
            }

            if (count($data['pur_request_detail']) > 0) { 
                $index_request = 0;
                foreach ($data['pur_request_detail'] as $request_detail) {
                    $index_request++;
                    $unit_name = pur_get_unit_name($request_detail['unit_id']);
                    $taxname = '';
                    $item_text = $request_detail['item_text'];

                    if(strlen($item_text) == 0){
                        $item_text = pur_get_item_variatiom($request_detail['item_code']);
                    }

                    $purchase_request_row_template .= $this->Purchase_model->create_purchase_request_row_template('items[' . $index_request . ']', $request_detail['item_code'], $item_text, $request_detail['unit_price'], $request_detail['quantity'], $unit_name, $request_detail['into_money'], $request_detail['prd_id'], $request_detail['tax_value'], $request_detail['total'], $request_detail['tax_name'], $request_detail['tax_rate'], $request_detail['tax'], true, $currency_rate, $to_currency);
                }
            }
        }

        $data['currencies'] = $this->_get_currency_dropdown_select2_data();

        $data['vendors'] = $this->Purchase_model->get_vendors();
        $data['purchase_request_row_template'] = $purchase_request_row_template;
        $data['invoices'] = $this->Invoices_model->get_details()->getResultArray();
        $data['salse_estimates'] = $this->Estimates_model->get_details()->getResultArray();        
        $data['taxes'] = $this->Purchase_model->get_taxes();
        $data['projects'] = $this->Projects_model->get_details()->getResultArray();

        $users_model = model("App\Models\Users_model", false);
        $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff"))->getResult();
        $data['staffs'] = array();

        foreach ($team_members as $team_member) {
           $data['staffs'][] = array("id" => $team_member->id, "text" => $team_member->first_name . " " . $team_member->last_name);
        }

        $data['departments'] = $this->Team_model->get_details()->getResultArray();
        $data['units'] = $this->Purchase_model->get_units();

        // Old script  $data['items'] = $this->purchase_model->get_items();
        $data['ajaxItems'] = false;

        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->Purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        
       return $this->template->rander('Purchase\Views\purchase_request\pur_request', $data);
    }

    /**
     * Gets the item by identifier.
     *
     * @param          $id             The identifier
     * @param      bool|int  $get_warehouse  The get warehouse
     * @param      bool      $warehouse_id   The warehouse identifier
     */
    public function get_item_by_id($id, $currency_rate = 1)
    {
        
        $item                     = $this->Purchase_model->get_item_v2($id);
        $item->long_description   = nl2br($item->description);

        if($currency_rate != 1){
            $item->purchase_price = round(($item->purchase_price*$currency_rate), 2);
        }
        
        $html = '<option value=""></option>';
       
        echo json_encode($item);
        
    }

    /**
     * Gets the purchase request row template.
     */
    public function get_purchase_request_row_template(){
        $name = $this->request->getPost('name');
        $item_text = $this->request->getPost('item_text');
        $unit_price = $this->request->getPost('unit_price');
        $quantity = $this->request->getPost('quantity');
        $unit_name = $this->request->getPost('unit_name');
        $into_money = $this->request->getPost('into_money');
        $item_key = $this->request->getPost('item_key');
        $tax_value = $this->request->getPost('tax_value');
        $tax_name = $this->request->getPost('taxname');
        $total = $this->request->getPost('total');
        $item_code = $this->request->getPost('item_code');
        $currency_rate = $this->request->getPost('currency_rate');
        $to_currency = $this->request->getPost('to_currency');
        
        echo html_entity_decode($this->Purchase_model->create_purchase_request_row_template( $name, $item_code, $item_text, $unit_price, $quantity, $unit_name, $into_money, $item_key, $tax_value, $total, $tax_name, '', '', false, $currency_rate, $to_currency));
    }

    /**
     * Gets the currency rate.
     */
    public function get_currency_rate($pr_currency){
        $base_currency = get_base_currency();

        $currency_rate = 1;
        $convert_str = ' ('.$base_currency.' => '.$base_currency.')'; 
        $currency_name = '('.$base_currency.')';
        $currency_str = $base_currency;
        if($base_currency != $pr_currency){
            $currency_rate = pur_get_currency_rate($pr_currency);
            $convert_str = ' ('.$base_currency.' => '.$pr_currency.')'; 
            $currency_name = '('.$pr_currency.')';
            $currency_str = $pr_currency;
        }

        echo json_encode([
            'currency_rate' => $currency_rate,
            'convert_str' => $convert_str,
            'currency_name' => $currency_name,
            'currency_str' => $currency_str,
        ]);
    }

    /**
     * { coppy sale invoice }
     */
    public function coppy_sale_invoice($invoice_id){
        $invoice = $this->Invoices_model->get_details(['id' => $invoice_id])->getRow();
        $invoice_items = $this->Invoice_items_model->get_details(['invoice_id' => $invoice_id])->getResultArray();
        $invoice_sumary = $this->Invoices_model->get_invoice_total_summary($invoice_id);

        $base_currency = get_base_currency();

        $list_item = $this->Purchase_model->create_purchase_request_row_template();
        $currency_rate = 1;
        $to_currency = $invoice_sumary->currency;

        if($to_currency != $base_currency){
            $currency_rate = pur_get_currency_rate($to_currency);
        }

        if($invoice){
            if(count($invoice_items) > 0){
                $index_request = 0;
                foreach($invoice_items as $key => $item){
                    $index_request++;

                    $tax = '';
                    $tax_value = 0;
                    $tax_name = [];
                    $tax_name[0] = '';
                    $tax_rate = '';

                    if($invoice->tax_id != 0){
                        $tax .= $invoice->tax_id;
                        $tax_rate .= $invoice->tax_percentage;
                        $tax_name[] = $this->Purchase_model->get_tax_name($invoice->tax_id).'|'.$invoice->tax_percentage;
                        $tax_value += ($item['total']*$invoice->tax_percentage)/100;
                    }

                    if($invoice->tax_id2 != 0){
                        $tax .= '|'.$invoice->tax_id2;
                        $tax_rate .= '|'.$invoice->tax_percentage2;
                        $tax_name[] = $this->Purchase_model->get_tax_name($invoice->tax_id2).'|'.$invoice->tax_percentage2;
                        $tax_value += ($item['total']*$invoice->tax_percentage2)/100;
                    }

                    if($invoice->tax_id3 != 0){
                        $tax .= '|'.$invoice->tax_id3;
                        $tax_rate .= '|'.$invoice->tax_percentage3;
                        $tax_name[] = $this->Purchase_model->get_tax_name($invoice->tax_id3).'(TDS)|'.$invoice->tax_percentage3;
                        $tax_value += ($item['total']*$invoice->tax_percentage3)/100;
                    }

                    $item_code = get_item_id_by_des($item['title']);
                    $item_text = $item['title'];
                    $unit_price = $item['rate'];
                    $unit_name = $item['unit_type'];
                    $into_money = (float) ($item['rate'] * $item['quantity']);
                    $total = $tax_value + $into_money;

                    $list_item .= $this->Purchase_model->create_purchase_request_row_template('newitems[' . $index_request . ']', $item_code, $item_text, $unit_price, $item['quantity'], $unit_name, $into_money, $index_request, $tax_value, $total, $tax_name, $tax_rate, $tax, false, $currency_rate, $to_currency);
                }
            }
        }

        echo json_encode([
            'list_item' => $list_item,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
        ]);

    }

    /**
     * { coppy sale estimate }
     */
    public function coppy_sale_estimate($estimate_id){
        $estimate = $this->Estimates_model->get_details(['id' => $estimate_id])->getRow();
        $estimate_items = $this->Estimate_items_model->get_details(['estimate_id' => $estimate_id])->getResultArray();
        $estimate_sumary = $this->Estimates_model->get_estimate_total_summary($estimate_id);

        $base_currency = get_base_currency();

        $list_item = $this->Purchase_model->create_purchase_request_row_template();
        $currency_rate = 1;
        $to_currency = $estimate_sumary->currency;

        if($to_currency != $base_currency){
            $currency_rate = pur_get_currency_rate($to_currency);
        }

        if($estimate){
            if(count($estimate_items) > 0){
                $index_request = 0;
                foreach($estimate_items as $key => $item){
                    $index_request++;

                    $tax = '';
                    $tax_value = 0;
                    $tax_name = [];
                    $tax_name[0] = '';
                    $tax_rate = '';

                    if($estimate->tax_id != 0){
                        $tax .= $estimate->tax_id;
                        $tax_rate .= $estimate->tax_percentage;
                        $tax_name[] = $this->Purchase_model->get_tax_name($estimate->tax_id).'|'.$estimate->tax_percentage;
                        $tax_value += ($item['total']*$estimate->tax_percentage)/100;
                    }

                    if($estimate->tax_id2 != 0){
                        $tax .= '|'.$estimate->tax_id2;
                        $tax_rate .= '|'.$estimate->tax_percentage2;
                        $tax_name[] = $this->Purchase_model->get_tax_name($estimate->tax_id2).'|'.$estimate->tax_percentage2;
                        $tax_value += ($item['total']*$estimate->tax_percentage2)/100;
                    }


                    $item_code = get_item_id_by_des($item['title']);
                    $item_text = $item['title'];
                    $unit_price = $item['rate'];
                    $unit_name = $item['unit_type'];
                    $into_money = (float) ($item['rate'] * $item['quantity']);
                    $total = $tax_value + $into_money;

                    $list_item .= $this->Purchase_model->create_purchase_request_row_template('newitems[' . $index_request . ']', $item_code, $item_text, $unit_price, $item['quantity'], $unit_name, $into_money, $index_request, $tax_value, $total, $tax_name, $tax_rate, $tax, false, $currency_rate, $to_currency);
                }
            }
        }

        echo json_encode([
            'list_item' => $list_item,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
        ]);
    }

    /**
     * { view pur request }
     *
     * @param      <type>  $id     The identifier
     * @return view
     */
    public function view_pur_request($id){

        $session = \Config\Services::session();
        $send_mail_approve = $session->has("send_mail_approve");
        if (($send_mail_approve) && $session->get("send_mail_approve") != '') {

            $data['send_mail_approve'] = $session->get("send_mail_approve");
            $session->remove("send_mail_approve");
        }

        $data['user_type'] = $this->login_user->user_type;
        
        $data['pur_request_detail'] = $this->Purchase_model->get_pur_request_detail($id);
        $data['pur_request'] = $this->Purchase_model->get_purchase_request($id);
        if(!$data['pur_request']){
            show_404();
        }

        if($data['user_type'] == 'vendor'){
            $vendor_id = get_vendor_user_id();
            if($data['pur_request']->send_to_vendors != ''){
                $vendors_view_arr = explode(',', $data['pur_request']->send_to_vendors);
                if(!in_array($vendor_id, $vendors_view_arr));
            }else{
                show_404();
            }
        }


        $data['title'] = $data['pur_request']->pur_rq_code;

        $data['units'] = $this->Purchase_model->get_units();

        $data['items'] = $this->Purchase_model->get_items();

        $data['taxes_data'] = $this->Purchase_model->get_html_tax_pur_request($id);
        $data['base_currency'] = get_base_currency();
        $data['check_appr'] = $this->Purchase_model->get_approve_setting('pur_request');
        $data['get_staff_sign'] = $this->Purchase_model->get_staff_sign($id,'pur_request');
        $data['check_approve_status'] = $this->Purchase_model->check_approval_details($id,'pur_request');
        $data['list_approve_status'] = $this->Purchase_model->get_list_approval_details($id,'pur_request');
        $data['taxes'] = $this->Purchase_model->get_taxes();

        $data['tab'] = $this->request->getGet('tab');
        if($data['tab'] == ''){
            $data['tab'] == 'information';
        }

        $data['pur_request_attachments'] =  $this->Purchase_model->get_purchase_request_attachments($id);

        return $this->template->rander('Purchase\Views\purchase_request\view_pur_request', $data);

    }

    /**
     * { purchase request pdf }
     *
     * @param      <type>  $id     The identifier
     * @return pdf output
     */
    public function pur_request_pdf($id, $send = '')
    {
       

        $pur_request = $this->Purchase_model->get_purchase_request($id);

        

        $pdf = new Pdf();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCellPadding(1.5);
        $pdf->setImageScale(1.42);
        $pdf->AddPage();
        $pdf->SetFontSize(9);

        
        $html = $this->Purchase_model->get_pur_request_pdf_html($id);

        $type = 'D';

        if ($this->request->getGet('output_type')) {
            $type = $this->request->getGet('output_type');
        }

        if ($this->request->getGet('print')) {
            $type = 'I';
        }

        if ($type != "html") {
            $pdf->writeHTML($html, true, false, true, false, '');
        }

        if($send != ''){
            $type = 'send_email';
        }

        $pdf_file_name = $pur_request->pur_rq_code.'.pdf';

        if ($type === "D") {
            $pdf->Output($pdf_file_name, "D");
        } else if ($type === "send_email") {
            $temp_download_path = getcwd() . "/" . get_setting("temp_file_path") . $pdf_file_name;
            $pdf->Output($temp_download_path, "F");
            return $temp_download_path;
        } else if ($type === "I") {
            $pdf->SetTitle($pdf_file_name);
            $pdf->Output($pdf_file_name, "I");
            exit;
        } 

    }

    /**
     * Sends a request approve.
     * @return  json
     */
    public function send_request_approve(){
        $data = $this->request->getPost();
        $message = 'Send request approval fail';
        $success = $this->Purchase_model->send_request_approve($data);
        if ($success === true) {             
                $message = 'Send request approval success';
                $data_new = [];
                $data_new['send_mail_approve'] = $data;

                $session = \Config\Services::session();
                $session->set($data_new);
        }elseif($success === false){
            $message = _l('no_matching_process_found');
            $success = false;
            
        }else{
            $message = _l('could_not_find_approver_with');
            $success = false;
        }

        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]); 
        die;
    }

    /**
     * send mail
     * @param  integer $id
     * @return json
     */
    public function send_mail() {
        $data = $this->request->getGet();
        if ((isset($data)) && $data != '') {
            $this->Purchase_model->send_mail($data);

            $success = 'success';
            echo json_encode([
                'success' => $success,
            ]);
        }
    }

    /**
     * wh_create_notification
     * @param  array  $data 
     * @return [type]       
     */
    public function pur_create_notification($data = array()) {

        ini_set('max_execution_time', 300); //300 seconds 
        //validate notification request

        $event = '';
        $event = get_array_value($data, "event");

        $user_id = get_array_value($data, "user_id");
        $activity_log_id = get_array_value($data, "activity_log_id");

        $options = array(
            "project_id" => get_array_value($data, "project_id"),
            "task_id" => get_array_value($data, "task_id"),
            "project_comment_id" => get_array_value($data, "project_comment_id"),
            "ticket_id" => get_array_value($data, "ticket_id"),
            "ticket_comment_id" => get_array_value($data, "ticket_comment_id"),
            "project_file_id" => get_array_value($data, "project_file_id"),
            "leave_id" => get_array_value($data, "leave_id"),
            "post_id" => get_array_value($data, "post_id"),
            "to_user_id" => get_array_value($data, "to_user_id"),
            "activity_log_id" => get_array_value($data, "activity_log_id"),
            "client_id" => get_array_value($data, "client_id"),
            "invoice_payment_id" => get_array_value($data, "invoice_payment_id"),
            "invoice_id" => get_array_value($data, "invoice_id"),
            "estimate_id" => get_array_value($data, "estimate_id"),
            "order_id" => get_array_value($data, "order_id"),
            "estimate_request_id" => get_array_value($data, "estimate_request_id"),
            "actual_message_id" => get_array_value($data, "actual_message_id"),
            "parent_message_id" => get_array_value($data, "parent_message_id"),
            "event_id" => get_array_value($data, "event_id"),
            "announcement_id" => get_array_value($data, "announcement_id"),
            "exclude_ticket_creator" => get_array_value($data, "exclude_ticket_creator"),
            "notification_multiple_tasks" => get_array_value($data, "notification_multiple_tasks"),
            "contract_id" => get_array_value($data, "contract_id"),
            "lead_id" => get_array_value($data, "lead_id"),
            "proposal_id" => get_array_value($data, "proposal_id"),
            "estimate_comment_id" => get_array_value($data, "estimate_comment_id"),

            "inventory_goods_receiving_id" => get_array_value($data, "inventory_goods_receiving_id"),
            "inventory_goods_delivery_id" => get_array_value($data, "inventory_goods_delivery_id"),
            "packing_list_id" => get_array_value($data, "packing_list_id"),
            "internal_delivery_note_id" => get_array_value($data, "internal_delivery_note_id"),
            "loss_adjustment_is" => get_array_value($data, "loss_adjustment_is"),
            "receiving_exporting_return_order_id" => get_array_value($data, "receiving_exporting_return_order_id"),

            "pur_request_id" => get_array_value($data, "pur_request_id"),
            "pur_quotation_id" => get_array_value($data, "pur_quotation_id"),
            "pur_order_id" => get_array_value($data, "pur_order_id"),
            "pur_payment_id" => get_array_value($data, "pur_payment_id"),
        );

        //get data from plugin by persing 'plugin_'
        foreach ($data as $key => $value) {
            if (strpos($key, 'plugin_') !== false) {
                $options[$key] = $value;
            }
        }

        $this->Purchase_model->pur_create_notification($event, $user_id, $options, $data['to_user_id']);
    }

    /**
     * { approve request }
     * @return json
     */
    public function approve_request(){
        $data = $this->request->getpost();
        $data['staff_approve'] = get_staff_user_id1();
        $success = false; 
        $code = '';
        $signature = '';

        if(isset($data['signature'])){
            $signature = $data['signature'];
            unset($data['signature']);
        }
        $status_string = 'status_'.$data['approve'];
        $check_approve_status = $this->Purchase_model->check_approval_details($data['rel_id'],$data['rel_type']);
        
        if(isset($data['approve']) && in_array(get_staff_user_id1(), $check_approve_status['staffid'])){

            $success = $this->Purchase_model->update_approval_details($check_approve_status['id'], $data);

            $message = _l('approved_successfully');

            if ($success) {
                if($data['approve'] == 2){
                    $message = _l('approved_successfully');
                    $data_log = [];

                    if($signature != ''){
                        $data_log['note'] = "signed_request";
                    }else{
                        $data_log['note'] = "approve_request";
                    }
                    if($signature != ''){
                        switch ($data['rel_type']) {
                            case 'payment_request':
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER .'/payment_invoice/signature/' .$data['rel_id'];
                                break;
                            case 'pur_order':
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_order/signature/' .$data['rel_id'];
                                break;
                            case 'pur_request':
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_request/signature/' .$data['rel_id'];
                                break;
                            case 'pur_quotation':
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_estimate/signature/' .$data['rel_id'];
                                break;
                            case 'order_return':
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER .'/order_return/signature/' .$data['rel_id'];
                                break;
                            default:
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER;
                                break;
                        }
                        purchase_process_digital_signature_image($signature, $path, 'signature_'.$check_approve_status['id']);
                        $message = _l('sign_successfully');
                    }
                   


                    $check_approve_status = $this->Purchase_model->check_approval_details($data['rel_id'],$data['rel_type']);
                    if ($check_approve_status === true){
                        $this->Purchase_model->update_approve_request($data['rel_id'],$data['rel_type'], 2);
                    }
                }else{
                    $message = _l('rejected_successfully');
                    
                    $this->Purchase_model->update_approve_request($data['rel_id'],$data['rel_type'], '3');
                }
            }
        }

        $data_new = [];
        $data_new['send_mail_approve'] = $data;
        $session = \Config\Services::session();
        $session->set($data_new);
        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
        die();      
    }

    /**
     * { purchase request attachment }
     */
    public function purchase_request_attachment($id){
        handle_purchase_request_file($id);

        app_redirect('purchase/view_pur_request/'.$id.'?tab=attachment');
    }

    /**
     * { preview purchase order file }
     *
     * @param      <type>  $id      The identifier
     * @param      <type>  $rel_id  The relative identifier
     * @return  view
     */
    public function file_purrequest($id, $rel_id)
    {

        $data['file'] = $this->Purchase_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }

        return $this->template->view('Purchase\Views\purchase_request\_file', $data);
    }

    /**
     * { delete purchase order attachment }
     *
     * @param      <type>  $id     The identifier
     */
    public function delete_purrequest_attachment($id)
    {

        $file = $this->Purchase_model->get_file($id);
        if ($file->staffid == get_staff_user_id1() || is_admin()) {
            echo json_encode(['success' => $this->Purchase_model->delete_purrequest_attachment($id) ]);
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /**
     * { share_request_modal }
     *
     * @param        $pur_request  The pur request
     */
    public function share_request_modal($pur_request_id){
        $data['pur_request'] = $this->Purchase_model->get_purchase_request($pur_request_id);
        $data['vendors'] = $this->Purchase_model->get_vendors();

        return $this->template->view('Purchase\Views\purchase_request\share_request_modal', $data);
    }

    /**
     * { share request }
     *
     * @param        $pur_request_id  The pur request identifier
     */
    public function share_request($pur_request_id){
        if($this->request->getPost()){
            $data = $this->request->getPost();
            $success = $this->Purchase_model->send_to_vendors($pur_request_id, $data);
            if($success){
                $this->session->setFlashdata("success_message", app_lang("updated_successfully"));
            }
        }

        app_redirect('purchase/purchase_request');
    }

    /**
     * { quotations }
     *
     * @param      string  $id     The identifier
     * @return     view
     */
    public function quotations(){
        $data['user_type'] = $this->login_user->user_type;
        $data['pur_request'] = $this->Purchase_model->get_pur_request_by_status(2);
        if($data['user_type'] == 'vendor'){
            $vendor_id = get_vendor_user_id();
            $data['pur_request'] = $this->Purchase_model->get_purchase_request_by_vendor($vendor_id);
        }

        $data['vendors'] = $this->Purchase_model->get_vendors();
        $data['title']                 = _l('estimates');
        
        return $this->template->rander('Purchase\Views\quotations\manage', $data);
    
    }

    /**
     * { table pur request }
     */
    public function table_estimates(){
        $dataPost = $this->request->getPost();
        $dataPost['user_type'] = $this->login_user->user_type;
        $this->Purchase_model->get_table_data(module_views_path('Purchase', 'quotations/table_estimates'), $dataPost);
    }

    /**
     * { function_description }
     *
     * @param      string  $id     The identifier
     * @return     redirect
     */
    public function estimate($id = '')
    {
       
        $data['user_type'] = $this->login_user->user_type;

        if ($this->request->getPost()) {
            $estimate_data = $this->request->getPost();
            $estimate_data['terms'] = nl2br($estimate_data['terms']);
            if ($id == '') {
                
                $id = $this->Purchase_model->add_estimate($estimate_data);
                if ($id) {
                    $this->session->setFlashdata("success_message", app_lang("added_successfully"));
                    
                    app_redirect('purchase/quotations' );
                    
                }
            } else {
                if (!has_permission('purchase_quotations', '', 'edit')) {
                    access_denied('quotations');
                }
                $success = $this->Purchase_model->update_estimate($estimate_data, $id);
                if ($success) {
                    $this->session->setFlashdata("success_message", app_lang("updated_successfully"));
                }
                app_redirect('purchase/quotations' );
                
            }
        }
        if ($id == '') {
            $title = _l('create_new_estimate');
        } else {
            $estimate = $this->Purchase_model->get_estimate($id);

            if($data['user_type'] == 'vendor'){
                $vendor_id = get_vendor_user_id();
                if($estimate->vendor != $vendor_id){
                    show_404();
                }
            }

            $data['tax_data'] = $this->Purchase_model->get_html_tax_pur_estimate($id);
            
            $data['estimate'] = $estimate;
            $data['edit']     = true;
            $title            = _l('edit', _l('estimate_lowercase'));
        }
        if ($this->request->getGet('customer_id')) {
            $data['customer_id'] = $this->request->getGet('customer_id');
        }

        $data['base_currency'] = get_base_currency();

        $pur_quotation_row_template = $this->Purchase_model->create_quotation_row_template();

        if($id != ''){
            $data['estimate_detail'] = $this->Purchase_model->get_pur_estimate_detail($id);
            $currency_rate = 1;
            if($data['estimate']->currency != '' && $data['estimate']->currency_rate != null){
                $currency_rate = $data['estimate']->currency_rate;
            }

            $to_currency = $data['base_currency'];
            if($data['estimate']->currency != '' && $data['estimate']->to_currency != null) {
                $to_currency = $data['estimate']->to_currency;
            }


            if (count($data['estimate_detail']) > 0) { 
                $index_quote = 0;
                foreach ($data['estimate_detail'] as $quote_detail) { 
                    $index_quote++;
                    $unit_name = pur_get_unit_name($quote_detail['unit_id']);
                    $taxname = $quote_detail['tax_name'];
                    $item_name = $quote_detail['item_name'];

                    if(strlen($item_name) == 0){
                        $item_name = pur_get_item_variatiom($quote_detail['item_code']);
                    }

                    $pur_quotation_row_template .= $this->Purchase_model->create_quotation_row_template('items[' . $index_quote . ']',  $item_name, $quote_detail['quantity'], $unit_name, $quote_detail['unit_price'], $taxname, $quote_detail['item_code'], $quote_detail['unit_id'], $quote_detail['tax_rate'],  $quote_detail['total_money'], $quote_detail['discount_%'], $quote_detail['discount_money'], $quote_detail['total'], $quote_detail['into_money'], $quote_detail['tax'], $quote_detail['tax_value'], $quote_detail['id'], true, $currency_rate, $to_currency);
                }
            }

        }

        $data['pur_quotation_row_template'] = $pur_quotation_row_template;

        $data['taxes'] = $this->Purchase_model->get_taxes();
        
        $data['currencies'] = $this->_get_currency_dropdown_select2_data();

        $data['ajaxItems'] = false;
        if($data['user_type'] == 'staff'){
            if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
                $data['items'] = $this->Purchase_model->pur_get_grouped('can_be_purchased');
            } else {
                $data['items']     = [];
                $data['ajaxItems'] = true;
            }
        }else if($data['user_type'] == 'vendor'){
            if(total_rows(db_prefix().'pur_vendor_items', ['vendor' => get_vendor_user_id()]) <= ajax_on_total_items()){ 
                $data['items'] = $this->Purchase_model->pur_get_grouped('can_be_purchased', false, get_vendor_user_id());
            }else {
                $data['items']     = [];
                $data['ajaxItems'] = true;
            }
        }
        
        $data['vendor_id'] = $this->request->getGet('vendor');

        $users_model = model("App\Models\Users_model", false);
        $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff"))->getResult();
        $data['staffs'] = array();

        foreach ($team_members as $team_member) {
           $data['staffs'][] = array("id" => $team_member->id, "text" => $team_member->first_name . " " . $team_member->last_name);
        }
        $data['vendors'] = $this->Purchase_model->get_vendors();

        $data['pur_request'] = $this->Purchase_model->get_pur_request_by_status(2);
        if($data['user_type'] == 'vendor'){
            $vendor_id = get_vendor_user_id();
            $data['vendor_id'] = $vendor_id;
            $data['pur_request'] = $this->Purchase_model->get_purchase_request_by_vendor($vendor_id);
        }

        if($data['vendor_id'] == '' && count($data['vendors']) > 0){
            $data['vendor_id'] = $data['vendors'][0]['userid'];
        }

        $data['units'] = $this->Purchase_model->get_units();
       
        $data['title']             = $title;
        return $this->template->rander('Purchase\Views\quotations\estimate', $data);
    }

    /**
     * Gets the quotation row template.
     */
    public function get_quotation_row_template(){
        $name = $this->request->getPost('name');
        $item_name = $this->request->getPost('item_name');
        $quantity = $this->request->getPost('quantity');
        $unit_name = $this->request->getPost('unit_name');
        $unit_price = $this->request->getPost('unit_price');
        $taxname = $this->request->getPost('taxname');
        $item_code = $this->request->getPost('item_code');
        $unit_id = $this->request->getPost('unit_id');
        $tax_rate = $this->request->getPost('tax_rate');
        $discount = $this->request->getPost('discount');
        $item_key = $this->request->getPost('item_key');
        $currency_rate = $this->request->getPost('currency_rate');
        $to_currency = $this->request->getPost('to_currency');

        echo html_entity_decode($this->Purchase_model->create_quotation_row_template($name, $item_name, $quantity, $unit_name, $unit_price, $taxname, $item_code, $unit_id, $tax_rate, '', $discount, '', '', '', '', '', $item_key, false, $currency_rate, $to_currency));
    }

    /**
     * { estimate by vendor }
     *
     * @param      <type>  $vendor  The vendor
     * @return json
     */
    public function estimate_by_vendor($vendor){
        $estimate = $this->Purchase_model->estimate_by_vendor($vendor);
        $ven = $this->Purchase_model->get_vendor($vendor);

        $currency = get_base_currency();
        $currency_id = $currency;
        if($ven->default_currency != '' && $ven->default_currency != null ){
            $currency_id = $ven->default_currency;
        }
        
        $vendor_data = '';
        $html = '<option value=""></option>';
        $company = '';
        foreach($estimate as $es){
            $html .= '<option value="'.$es['id'].'">'.format_pur_estimate_number($es['id']).'</option>';
        }

        $option_html = '';

        if(total_rows(db_prefix().'pur_vendor_items', ['vendor' => $vendor]) <= ajax_on_total_items()){
            $items = $this->Purchase_model->get_items_by_vendor_variation($vendor);
            $option_html .= '<option value="">-</option>';
            foreach($items as $item){
                $option_html .= '<option value="'.$item['id'].'" >'.$item['label'].'</option>';
            }
        }


        if($ven){
            $vendor_data .= '<div class="col-md-6">';
            $vendor_data .= '<p class="bold p_style">'._l('vendor_detail').'</p>
                            <hr class="hr_style"/>';
            $vendor_data .= '<table class="table table-striped table-bordered"><tbody>';
            $vendor_data .= '<tr><td>'._l('company').'</td><td>'.$ven->company.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('client_vat_number').'</td><td>'.$ven->vat.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('client_phonenumber').'</td><td>'.$ven->phonenumber.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('website').'</td><td>'.$ven->website.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('vendor_category').'</td><td>'.get_vendor_category_html($ven->category).'</td></tr>';
            $vendor_data .= '<tr><td>'._l('client_address').'</td><td>'.$ven->address.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('client_city').'</td><td>'.$ven->city.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('client_state').'</td><td>'.$ven->state.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('client_postal_code').'</td><td>'.$ven->zip.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('clients_country').'</td><td>'.($ven->country).'</td></tr>';
            $vendor_data .= '<tr><td>'._l('bank_detail').'</td><td>'.$ven->bank_detail.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('payment_terms').'</td><td>'.$ven->payment_terms.'</td></tr>';
            $vendor_data .= '</tbody></table>';                    
            $vendor_data .= '</div>';

            $vendor_data .= '<div class="col-md-6">';
            $vendor_data .= '<p class="bold p_style">'._l('billing_address').'</p>
                            <hr class="hr_style"/>';
            $vendor_data .= '<table class="table table-striped table-bordered"><tbody>';
            $vendor_data .= '<tr><td>'._l('billing_street').'</td><td>'.$ven->billing_street.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('billing_city').'</td><td>'.$ven->billing_city.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('billing_state').'</td><td>'.$ven->billing_state.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('billing_zip').'</td><td>'.$ven->billing_zip.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('billing_country').'</td><td>'.($ven->billing_country).'</td></tr>';
            $vendor_data .= '</tbody></table>'; 
            $vendor_data .= '<p class="bold p_style">'._l('shipping_address').'</p>
                            <hr class="hr_style"/>';
            $vendor_data .= '<table class="table table-striped table-bordered"><tbody>';
            $vendor_data .= '<tr><td>'._l('shipping_street').'</td><td>'.$ven->shipping_street.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('shipping_city').'</td><td>'.$ven->shipping_city.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('shipping_state').'</td><td>'.$ven->shipping_state.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('shipping_zip').'</td><td>'.$ven->shipping_zip.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('shipping_country').'</td><td>'.($ven->shipping_country).'</td></tr>';
            $vendor_data .= '</tbody></table>';                  
            $vendor_data .= '</div>';

            if($ven->vendor_code != ''){
               $company = $ven->vendor_code; 
            }
            
        }

        echo json_encode([
            'result' => $html,
            'ven_html' => $vendor_data,
            'company' => $company,
            'option_html' => $option_html,
            'currency_id' => $currency_id
        ]);
    }

    /**
     * { validate estimate number }
     */
    public function validate_estimate_number()
    {
        $isedit          = $this->request->getPost('isedit');
        $number          = $this->request->getPost('number');
        $date            = $this->request->getPost('date');
        $original_number = $this->request->getPost('original_number');
        $number          = trim($number);
        $number          = ltrim($number, '0');

        if ($isedit == 'true') {
            if ($number == $original_number) {
                echo json_encode(true);
                die;
            }
        }

        if (total_rows(db_prefix().'pur_estimates', [
            'YEAR(date)' => date('Y', strtotime(to_sql_date($date))),
            'number' => $number,
        ]) > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    /**
     * { view quotation }
     */
    public function view_quotation($id){
        $estimate = $this->Purchase_model->get_estimate($id);

        if(!$estimate){
            show_404();
        }

        $estimate->date       = format_to_date($estimate->date);
        $estimate->expirydate = format_to_date($estimate->expirydate);

        $data['pur_estimate_attachments'] = $this->Purchase_model->get_purchase_estimate_attachments($id);
        $data['estimate_detail'] = $this->Purchase_model->get_pur_estimate_detail($id);
        $data['estimate']          = $estimate;

        $data['members']           = array();
        $users_model = model("App\Models\Users_model", false);
        $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff"))->getResult();
        foreach ($team_members as $team_member) {
           $data['members'][] = array("id" => $team_member->id, "text" => $team_member->first_name . " " . $team_member->last_name);
        }

        $data['user_type'] = $this->login_user->user_type;
        if($data['user_type'] == 'vendor'){
            $vendor_id = get_vendor_user_id();
            if($estimate->vendor != $vendor_id){
                show_404();
            }
        }

        $data['vendor_contacts'] = []; 

        $session = \Config\Services::session();
        $send_mail_approve = $session->has("send_mail_approve");
        if (($send_mail_approve) && $session->get("send_mail_approve") != '') {

            $data['send_mail_approve'] = $session->get("send_mail_approve");
            $session->remove("send_mail_approve");
        }

        $data['check_appr'] = $this->Purchase_model->get_approve_setting('pur_quotation');
        $data['get_staff_sign'] = $this->Purchase_model->get_staff_sign($id,'pur_quotation');
        $data['check_approve_status'] = $this->Purchase_model->check_approval_details($id,'pur_quotation');
        $data['list_approve_status'] = $this->Purchase_model->get_list_approval_details($id,'pur_quotation');
        $data['tax_data'] = $this->Purchase_model->get_html_tax_pur_estimate($id);

        $data['title'] = format_pur_estimate_number($id);

        $data['tab'] = $this->request->getGet('tab');
        if($data['tab'] == ''){
            $data['tab'] == 'tab_estimate';
        }
        
        return $this->template->rander('Purchase\Views\quotations\view_estimate', $data);
    }

    /**
     * Uploads a purchase estimate attachment.
     *
     * @param      string  $id  The purchase order
     * @return redirect
     */
    public function purchase_estimate_attachment($id){
        handle_purchase_estimate_file($id);

        app_redirect('purchase/view_quotation/'.$id);
    }

    /**
     * { preview purchase order file }
     *
     * @param      <type>  $id      The identifier
     * @param      <type>  $rel_id  The relative identifier
     * @return  view
     */
    public function file_pur_estimate($id, $rel_id)
    {

        $data['file'] = $this->Purchase_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }

        return $this->template->view('Purchase\Views\quotations\_file', $data);
    }

    /**
     * { purchase estimate pdf }
     */
    public function purestimate_pdf($id, $send = ''){
        $pur_estimate = $this->Purchase_model->get_estimate($id);

        if(!$pur_estimate){
            show_404();
        }

        $pdf = new Pdf();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCellPadding(1.5);
        $pdf->setImageScale(1.42);
        $pdf->AddPage();
        $pdf->SetFontSize(9);

        $user_type = $this->login_user->user_type;
        if($user_type == 'vendor'){
            $vendor_id = get_vendor_user_id();
            if($pur_estimate->vendor != $vendor_id){
                show_404();
            }
        }
        
        $html = $this->Purchase_model->get_purestimate_pdf_html($id);

        $type = 'D';

        if ($this->request->getGet('output_type')) {
            $type = $this->request->getGet('output_type');
        }

        if ($this->request->getGet('print')) {
            $type = 'I';
        }

        if ($type != "html") {
            $pdf->writeHTML($html, true, false, true, false, '');
        }

        if($send != ''){
            $type = 'send_email';
        }

        $pdf_file_name = format_pur_estimate_number($id).'.pdf';

        if ($type === "D") {
            $pdf->Output($pdf_file_name, "D");
        } else if ($type === "send_email") {
            $temp_download_path = getcwd() . "/" . get_setting("temp_file_path") . $pdf_file_name;
            $pdf->Output($temp_download_path, "F");
            return $temp_download_path;
        } else if ($type === "I") {
            $pdf->SetTitle($pdf_file_name);
            $pdf->Output($pdf_file_name, "I");
            exit;
        }
    }

    /**
     * { change status pur estimate }
     *
     * @param      <type>  $status  The status
     * @param      <type>  $id      The identifier
     * @return json
     */
    public function change_status_pur_estimate($status,$id){
        $change = $this->Purchase_model->change_status_pur_estimate($status,$id);
        if($change == true){
            
            $message = _l('change_status_pur_estimate').' '._l('successfully');
            echo json_encode([
                'result' => $message,
            ]);
        }else{
            $message = _l('change_status_pur_estimate').' '._l('fail');
            echo json_encode([
                'result' => $message,
            ]);
        }
    }

    /**
     * { coppy pur request }
     *
     * @param        $pur_request  The purchase request id
     * @return json
     */
    public function coppy_pur_request($pur_request){

        $pur_request_detail = $this->Purchase_model->get_pur_request_detail_in_estimate($pur_request);
        $purchase_request = $this->Purchase_model->get_purchase_request($pur_request);

        $base_currency = get_base_currency();

        $taxes = [];
        $tax_val = [];
        $tax_name = [];
        $subtotal = 0;
        $total = 0;
        $data_rs = [];
        $tax_html = '';
        
        if(count($pur_request_detail) > 0){
            foreach($pur_request_detail as $key => $item){
                $subtotal += $item['into_money'];
                $total += $item['total'];
            }
        }

        $list_item = $this->Purchase_model->create_quotation_row_template();

        $currency_rate = 1;
        $to_currency = $base_currency;
        if($purchase_request->currency != '' && $purchase_request->currency_rate != null){
            $currency_rate = $purchase_request->currency_rate;
            $to_currency = $purchase_request->currency;
        }

        if(count($pur_request_detail) > 0){
            $index_quote = 0;
            foreach($pur_request_detail as $key => $item){
                $index_quote++;
                $unit_name = pur_get_unit_name($item['unit_id']);
                $taxname = $item['tax_name'];
                $item_name = $item['item_text'];

                if(strlen($item_name) == 0){
                    $item_name = pur_get_item_variatiom($item['item_code']);
                }

                $list_item .= $this->Purchase_model->create_quotation_row_template('newitems[' . $index_quote . ']',  $item_name, $item['quantity'], $unit_name, $item['unit_price'], $taxname, $item['item_code'], $item['unit_id'], $item['tax_rate'],  $item['total'], '', '', $item['total'], $item['into_money'], $item['tax'], $item['tax_value'], $index_quote, true, $currency_rate, $to_currency);
            }
        }
        

        $taxes_data = $this->Purchase_model->get_html_tax_pur_request($pur_request);
        $tax_html = $taxes_data['html'];

        echo json_encode([
            'result' => $pur_request_detail,
            'subtotal' => round($subtotal,2),
            'total' => round($total, 2),
            'tax_html' => $tax_html,
            'taxes' => $taxes,
            'list_item' => $list_item,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
        ]);
    }

    /**
     * { delete purchase order attachment }
     *
     * @param        $id     The identifier
     */
    public function delete_estimate_attachment($id)
    {

        $file = $this->Purchase_model->get_file($id);
        if ($file->staffid == get_staff_user_id1() || is_admin()) {
            echo html_entity_decode($this->Purchase_model->delete_estimate_attachment($id));
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }


    /**
     * { purchase order }
     *
     * @param      string  $id     The identifier
     * @return view
     */
    public function purchase_orders(){

 
        $data['title'] = _l('purchase_order');

        $data['user_type'] = $this->login_user->user_type;

        $data['departments'] = $this->Team_model->get_details()->getResultArray();
        $data['projects'] = $this->Projects_model->get_details()->getResultArray();

        $data['currency'] = get_base_currency();

        $data['currencies'] = $this->_get_currency_dropdown_select2_data();
        $tax_options = array(
            "deleted" => 0,
        );
        $data['taxes'] = $this->Taxes_model->get_details($tax_options)->getResultArray();

        $data['vendors'] = $this->Purchase_model->get_vendors();
        $data['expense_categories'] = []; 

        $data['customers'] = $this->Clients_model->get_details()->getResultArray();

        $data['pur_request'] = $this->Purchase_model->get_pur_request_by_status(2);
        if($data['user_type'] == 'vendor'){
            $vendor_id = get_vendor_user_id();
            $data['pur_request'] = $this->Purchase_model->get_purchase_request_by_vendor($vendor_id);
        }

        
        return $this->template->rander("Purchase\Views\purchase_orders\manage", $data);
    }

    /**
     * view commodity detail
     * @param  [integer] $commodity_id
     * @return [type]
     */
    public function view_commodity_detail($commodity_id) {
        $commodity_item = pur_get_commodity_name($commodity_id);

        if (!$commodity_item) {
            blank_page('commodity item Not Found', 'danger');
        }

        //user for sub
        $data['units'] = $this->Purchase_model->get_unit_add_commodity();
        $data['commodity_groups'] = $this->Purchase_model->get_commodity_group_add_commodity();

        $tax_options = array(
            "deleted" => 0,
        );
        $data['taxes'] = $this->Taxes_model->get_details($tax_options)->getResultArray();

        $data['ajaxItems'] = false;
   
        $data['title'] = _l("item_detail");


        $data['commodity_item'] = $commodity_item;
        $data['commodity_file'] = [];

        $model_info = $this->Items_model->get_details(array("id" => $commodity_id, "login_user_id" => $this->login_user->id))->getRow();
        $data['model_info'] = $model_info;

        return $this->template->rander("Purchase\Views\items\commodity_detail", $data);
    }


    /**
     * { table pur request }
     */
    public function table_pur_order(){
        $dataPost = $this->request->getPost();
        $dataPost['user_type'] = $this->login_user->user_type;
        $this->Purchase_model->get_table_data(module_views_path('Purchase', 'purchase_orders/table_pur_order'), $dataPost);
    }

    /**
     * { purchase order form }
     *
     * @param      string  $id     The identifier
     * @return redirect, view
     */
    public function pur_order($id = ''){
        if ($this->request->getPost()) {
            $pur_order_data = $this->request->getPost();
            $pur_order_data['terms'] = nl2br($pur_order_data['terms']);
            if ($id == '') {
   
                $id = $this->Purchase_model->add_pur_order($pur_order_data);
                if ($id) {
                    $this->session->setFlashdata("success_message", app_lang("added_successfully"));
                    app_redirect('purchase/purchase_orders');
                    
                }
            } else {

                $success = $this->Purchase_model->update_pur_order($pur_order_data, $id);
                if ($success) {
                    $this->session->setFlashdata("success_message", app_lang("updated_successfully"));
                }
                app_redirect('purchase/purchase_orders');
                
            }
        }


        $data['base_currency'] = get_base_currency();

        $pur_order_row_template = $this->Purchase_model->create_purchase_order_row_template();

        if ($id == '') {
            $title = _l('create_new_pur_order');
        } else {
            $data['pur_order_detail'] = $this->Purchase_model->get_pur_order_detail($id);
            $data['pur_order'] = $this->Purchase_model->get_pur_order($id);

            $currency_rate = 1;
            if($data['pur_order']->currency != '' && $data['pur_order']->currency_rate != null){
                $currency_rate = $data['pur_order']->currency_rate;
            }

            $to_currency = $data['base_currency'];
            if($data['pur_order']->currency != '' && $data['pur_order']->to_currency != null) {
                $to_currency = $data['pur_order']->to_currency;
            }


            $data['tax_data'] = $this->Purchase_model->get_html_tax_pur_order($id);
            $title = _l('pur_order_detail');

            if (count($data['pur_order_detail']) > 0) { 
                $index_order = 0;
                foreach ($data['pur_order_detail'] as $order_detail) { 
                    $index_order++;
                    $unit_name = pur_get_unit_name($order_detail['unit_id']);
                    $taxname = $order_detail['tax_name'];
                    $item_name = $order_detail['item_name'];

                    if(strlen($item_name) == 0){
                        $item_name = pur_get_item_variatiom($order_detail['item_code']);
                    }

                    $pur_order_row_template .= $this->Purchase_model->create_purchase_order_row_template('items[' . $index_order . ']',  $item_name, $order_detail['description'], $order_detail['quantity'], $unit_name, $order_detail['unit_price'], $taxname, $order_detail['item_code'], $order_detail['unit_id'], $order_detail['tax_rate'],  $order_detail['total_money'], $order_detail['discount_%'], $order_detail['discount_money'], $order_detail['total'], $order_detail['into_money'], $order_detail['tax'], $order_detail['tax_value'], $order_detail['id'], true, $currency_rate, $to_currency);
                }
            }
        }
        $data['pur_order_row_template'] = $pur_order_row_template;

        
        $data['currencies'] = $this->_get_currency_dropdown_select2_data();

        $data['clients'] = $this->Clients_model->get_details()->getResultArray();

        $data['departments'] = $this->Team_model->get_details()->getResultArray();

        $data['invoices'] = $this->Invoices_model->get_details()->getResultArray();

        $data['pur_request'] = $this->Purchase_model->get_pur_request_by_status(2);

        $data['projects'] = $this->Projects_model->get_details()->getResultArray();
        $data['ven'] = $this->request->getGet('vendor');

        $tax_options = array(
            "deleted" => 0,
        );
        $data['taxes'] = $this->Taxes_model->get_details($tax_options)->getResultArray();

        $users_model = model("App\Models\Users_model", false);
        $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff"))->getResult();
        $data['staff'] = array();

        foreach ($team_members as $team_member) {
           $data['staff'][] = array("id" => $team_member->id, "text" => $team_member->first_name . " " . $team_member->last_name);
        }


        $data['vendors'] = $this->Purchase_model->get_vendors();
        $data['estimates'] = $this->Purchase_model->get_estimates_by_status(2);
        $data['units'] = $this->Purchase_model->get_units();

        $data['project_id'] = $this->request->getGet('project_id');

        $data['vendor_id'] = $this->request->getGet('vendor');

        if($data['vendor_id'] == '' && count($data['vendors']) > 0){
            $data['vendor_id'] = $data['vendors'][0]['userid'];
        }

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->Purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }

        $data['title'] = $title;

        return $this->template->rander("Purchase\Views\purchase_orders\pur_order", $data);
    }

    /**
     * Gets the purchase order row template.
     */
    public function get_purchase_order_row_template(){
        $name = $this->request->getPost('name');
        $item_name = $this->request->getPost('item_name');
        $item_description = $this->request->getPost('item_description');
        $quantity = $this->request->getPost('quantity');
        $unit_name = $this->request->getPost('unit_name');
        $unit_price = $this->request->getPost('unit_price');
        $taxname = $this->request->getPost('taxname');
        $item_code = $this->request->getPost('item_code');
        $unit_id = $this->request->getPost('unit_id');
        $tax_rate = $this->request->getPost('tax_rate');
        $discount = $this->request->getPost('discount');
        $item_key = $this->request->getPost('item_key');
        $currency_rate = $this->request->getPost('currency_rate');
        $to_currency = $this->request->getPost('to_currency');

        echo html_entity_decode($this->Purchase_model->create_purchase_order_row_template($name, $item_name, $item_description, $quantity, $unit_name, $unit_price, $taxname, $item_code, $unit_id, $tax_rate, '', $discount, '', '', '', '', '', $item_key, false, $currency_rate, $to_currency));
    }

    /**
     * { function_description }
     *
     * @param      <type>  $id     The identifier
     */
    public function view_pur_order($id){

        if (!$id) {
            die('No purchase order found');
        }

        $estimate = $this->Purchase_model->get_pur_order($id);
        if(!$estimate){
            show_404();
        }

        $data['user_type'] = $this->login_user->user_type;

        if($data['user_type'] == 'vendor'){
            $vendor_id = get_vendor_user_id();
            if($estimate->vendor != $vendor_id){
                show_404();
            }
        }

        $data['pur_order_attachments'] = $this->Purchase_model->get_purchase_order_attachments($id);
        $data['estimate_detail'] = $this->Purchase_model->get_pur_order_detail($id);
        $data['estimate']          = $estimate;


        $users_model = model("App\Models\Users_model", false);
        $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff"))->getResult();
        $data['members'] = array();
        foreach ($team_members as $team_member) {
           $data['members'][] = array("id" => $team_member->id, "text" => $team_member->first_name . " " . $team_member->last_name);
        }

        $data['vendor_contacts'] = [];

        $session = \Config\Services::session();
        $send_mail_approve = $session->has("send_mail_approve");
        if (($send_mail_approve) && $session->get("send_mail_approve") != '') {

            $data['send_mail_approve'] = $session->get("send_mail_approve");
            $session->remove("send_mail_approve");
        }

        $data['title'] = $estimate->pur_order_name;


        $data['check_appr'] = $this->Purchase_model->get_approve_setting('pur_order');
        $data['get_staff_sign'] = $this->Purchase_model->get_staff_sign($id,'pur_order');
        $data['check_approve_status'] = $this->Purchase_model->check_approval_details($id,'pur_order');
        $data['list_approve_status'] = $this->Purchase_model->get_list_approval_details($id,'pur_order');
        $data['tax_data'] = $this->Purchase_model->get_html_tax_pur_order($id);

        $data['tab'] = $this->request->getGet('tab');
        if($data['tab'] == ''){
            $data['tab'] = 'tab_estimate';
        }

        return $this->template->rander("Purchase\Views\purchase_orders\_view_pur_order", $data);
    }

    /**
     * Uploads a purchase order attachment.
     *
     * @param      string  $id  The purchase order
     * @return redirect
     */
    public function purchase_order_attachment($id){

        handle_purchase_order_file($id);

        app_redirect('purchase/view_pur_order/'.$id);
    }

    /**
     * { preview purchase order file }
     *
     * @param      <type>  $id      The identifier
     * @param      <type>  $rel_id  The relative identifier
     * @return  view
     */
    public function file_pur_order($id, $rel_id)
    {

        $data['file'] = $this->Purchase_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }

        return $this->template->view('Purchase\Views\purchase_orders\_file', $data);
    }

    /**
     * { delete purchase order attachment }
     *
     * @param      <type>  $id     The identifier
     */
    public function delete_purorder_attachment($id)
    {

        $file = $this->Purchase_model->get_file($id);
        if ($file->staffid == get_staff_user_id1() || is_admin()) {
            echo html_entity_decode($this->Purchase_model->delete_purorder_attachment($id));
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /**
     * { change status pur order }
     *
     * @param      <type>  $status  The status
     * @param      <type>  $id      The identifier
     * @return json
     */
    public function change_status_pur_order($status,$id){
        $change = $this->Purchase_model->change_status_pur_order($status,$id);
        if($change == true){
            
            $message = _l('change_status_pur_order').' '._l('successfully');
            echo json_encode([
                'result' => $message,
            ]);
        }else{
            $message = _l('change_status_pur_order').' '._l('fail');
            echo json_encode([
                'result' => $message,
            ]);
        }
    }

    /**
     * { update delivery status }
     *
     * @param      <type>  $pur_order  The pur order
     * @param      <type>  $status     The status
     */
    public function mark_pur_order_as( $status, $pur_order){
        

        $success = $this->Purchase_model->mark_pur_order_as($status, $pur_order);

        if($success){
            $this->session->setFlashdata("success_message", app_lang("updated_successfully"));

        }

        app_redirect('purchase/view_pur_order/' . $pur_order);
    }

    /**
     * { purchase estimate pdf }
     */
    public function purorder_pdf($id, $send = ''){
        $pur_order = $this->Purchase_model->get_pur_order($id);

        $pdf = new Pdf();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCellPadding(1.5);
        $pdf->setImageScale(1.42);
        $pdf->AddPage();
        $pdf->SetFontSize(9);

        
        $html = $this->Purchase_model->get_purorder_pdf_html($id);

        $type = 'D';

        if ($this->request->getGet('output_type')) {
            $type = $this->request->getGet('output_type');
        }

        if ($this->request->getGet('print')) {
            $type = 'I';
        }

        if ($type != "html") {
            $pdf->writeHTML($html, true, false, true, false, '');
        }

        if($send != ''){
            $type = 'send_email';
        }

        $pdf_file_name = $pur_order->pur_order_number.'.pdf';

        if ($type === "D") {
            $pdf->Output($pdf_file_name, "D");
        } else if ($type === "send_email") {
            $temp_download_path = getcwd() . "/" . get_setting("temp_file_path") . $pdf_file_name;
            $pdf->Output($temp_download_path, "F");
            return $temp_download_path;
        } else if ($type === "I") {
            $pdf->SetTitle($pdf_file_name);
            $pdf->Output($pdf_file_name, "I");
            exit;
        }
    }

    /**
     * { function_description }
     */
    public function convert_expense_modal_form($pur_order_id){

        $pur_order = $this->Purchase_model->get_pur_order($pur_order_id);

        $model_info = $this->Expenses_model->get_one('');

        $view_data['categories_dropdown'] = $this->Expense_categories_model->get_dropdown_list(array("title"));

        $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff"))->getResult();
        $members_dropdown = array();

        foreach ($team_members as $team_member) {
            $members_dropdown[$team_member->id] = $team_member->first_name . " " . $team_member->last_name;
        }

        $view_data['members_dropdown'] = array("0" => "-") + $members_dropdown;
        $view_data['clients_dropdown'] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id", array("is_lead" => 0));
        $view_data['projects_dropdown'] = array("0" => "-") + $this->Projects_model->get_dropdown_list(array("title"));
        $view_data['taxes_dropdown'] = array("" => "-") + $this->Taxes_model->get_dropdown_list(array("title"));
        $view_data['client_id'] = '';

        $view_data['is_clone'] = false;
        $model_info->amount = $pur_order->total;
        $model_info->pur_order_id = $pur_order_id;
        $view_data['model_info'] = $model_info;

         $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("expenses", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();
        return $this->template->view('Purchase\Views\purchase_orders\expense_modal_form', $view_data);
    }

    /**
     * Adds an expense.
     */
    public function add_expense()
    {   
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "expense_date" => "required",
            "category_id" => "required",
            "amount" => "required"
        ));

        $id = $this->request->getPost('id');

        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "expense");
        $new_files = unserialize($files_data);

        $recurring = $this->request->getPost('recurring') ? 1 : 0;
        $expense_date = $this->request->getPost('expense_date');
        $repeat_every = $this->request->getPost('repeat_every');
        $repeat_type = $this->request->getPost('repeat_type');
        $no_of_cycles = $this->request->getPost('no_of_cycles');

        $data = array(
            "expense_date" => $expense_date,
            "title" => $this->request->getPost('title'),
            "description" => $this->request->getPost('description'),
            "category_id" => $this->request->getPost('category_id'),
            "amount" => unformat_currency($this->request->getPost('amount')),
            "client_id" => $this->request->getPost('expense_client_id') ? $this->request->getPost('expense_client_id') : 0,
            "project_id" => $this->request->getPost('expense_project_id'),
            "user_id" => $this->request->getPost('expense_user_id'),
            "tax_id" => $this->request->getPost('tax_id') ? $this->request->getPost('tax_id') : 0,
            "tax_id2" => $this->request->getPost('tax_id2') ? $this->request->getPost('tax_id2') : 0,
            "recurring" => $recurring,
            "repeat_every" => $repeat_every ? $repeat_every : 0,
            "repeat_type" => $repeat_type ? $repeat_type : NULL,
            "no_of_cycles" => $no_of_cycles ? $no_of_cycles : 0,
        );

        $expense_info = $this->Expenses_model->get_one($id);

        //is editing? update the files if required
        if ($id) {
            $timeline_file_path = get_setting("timeline_file_path");
            $new_files = update_saved_files($timeline_file_path, $expense_info->files, $new_files);


        }

        $data["files"] = serialize($new_files);

        $is_clone = $this->request->getPost('is_clone');

        if ($is_clone && $id) {
            $id = "";
        }

        if ($recurring) {
            //set next recurring date for recurring expenses

            if ($id) {
                //update
                if ($this->request->getPost('next_recurring_date')) { //submitted any recurring date? set it.
                    $data['next_recurring_date'] = $this->request->getPost('next_recurring_date');
                } else {
                    //re-calculate the next recurring date, if any recurring fields has changed.
                    if ($expense_info->recurring != $data['recurring'] || $expense_info->repeat_every != $data['repeat_every'] || $expense_info->repeat_type != $data['repeat_type'] || $expense_info->expense_date != $data['expense_date']) {
                        $data['next_recurring_date'] = add_period_to_date($expense_date, $repeat_every, $repeat_type);
                    }
                }
            } else {
                //insert new
                $data['next_recurring_date'] = add_period_to_date($expense_date, $repeat_every, $repeat_type);
            }


            //recurring date must have to set a future date
            if (get_array_value($data, "next_recurring_date") && get_today_date() >= $data['next_recurring_date']) {
                echo json_encode(array("success" => false, 'message' => app_lang('past_recurring_date_error_message_title'), 'next_recurring_date_error' => app_lang('past_recurring_date_error_message'), "next_recurring_date_value" => $data['next_recurring_date']));
                return false;
            }
        }

        $save_id = $this->Expenses_model->ci_save($data, $id);

        if ($save_id) {

            $pur_order_id = $this->request->getPost('pur_order_id');

            $this->Purchase_model->mark_converted_purchase_order($pur_order_id, $save_id);

            save_custom_fields("expenses", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            echo json_encode(array("success" => true, 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }

        die;

    }


    /**
     * { coppy pur request }
     *
     * @param      <type>  $pur_request  The purchase request id
     * @return json
     */
    public function coppy_pur_request_for_po($pur_request, $vendor = ''){

        $pur_request_detail = $this->Purchase_model->get_pur_request_detail_in_po($pur_request);
        $purchase_request = $this->Purchase_model->get_purchase_request($pur_request);

        $base_currency = get_base_currency();
        $taxes = [];
        $tax_val = [];
        $tax_name = [];
        $subtotal = 0;
        $total = 0;
        $data_rs = [];
        $tax_html = '';
        $estimate_html = '';

        $estimate_html .= $this->Purchase_model->get_estimate_html_by_pr_vendor($pur_request, $vendor);
        
        if(count($pur_request_detail) > 0){
            foreach($pur_request_detail as $key => $item){
                $subtotal += $item['into_money'];
                $total += $item['total'];
            }
        }

        $list_item = $this->Purchase_model->create_purchase_order_row_template();

        $currency_rate = 1;
        $to_currency = $base_currency;
        if($purchase_request->currency != '' && $purchase_request->currency_rate != null){
            $currency_rate = $purchase_request->currency_rate;
            $to_currency = $purchase_request->currency;
        }


        if(count($pur_request_detail) > 0){
            $index_quote = 0;
            foreach($pur_request_detail as $key => $item){
                $index_quote++;
                $unit_name = pur_get_unit_name($item['unit_id']);
                $taxname = $item['tax_name'];
                $item_name = $item['item_text'];

                if(strlen($item_name) == 0){
                    $item_name = pur_get_item_variatiom($item['item_code']);
                }

                $list_item .= $this->Purchase_model->create_purchase_order_row_template('newitems[' . $index_quote . ']',  $item_name,'', $item['quantity'], $unit_name, $item['unit_price'], $taxname, $item['item_code'], $item['unit_id'], $item['tax_rate'],  $item['total'], '', '', $item['total'], $item['into_money'], $item['tax'], $item['tax_value'], $index_quote, true, $currency_rate, $to_currency);
            }
        }

        $taxes_data = $this->Purchase_model->get_html_tax_pur_request($pur_request);
        $tax_html = $taxes_data['html'];

        echo json_encode([
            'result' => $pur_request_detail,
            'subtotal' => to_currency(round($subtotal,2),''),
            'total' => to_currency(round($total, 2),''),
            'tax_html' => $tax_html,
            'taxes' => $taxes,
            'list_item' => $list_item,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
            'estimate_html' => $estimate_html,
        ]);
    }

     /**
     * { coppy pur estimate }
     *
     * @param        $pur_estimate  The purchase estimate id
     * @return  json
     */
    public function coppy_pur_estimate($pur_estimate_id){

        $pur_estimate_detail = $this->Purchase_model->get_pur_estimate_detail_in_order($pur_estimate_id);
        $pur_estimate = $this->Purchase_model->get_estimate($pur_estimate_id);

        $taxes = [];
        $tax_val = [];
        $tax_name = [];
        $subtotal = 0;
        $total = 0;
        $data_rs = [];
        $tax_html = '';
        
        if(count($pur_estimate_detail) > 0){
            foreach($pur_estimate_detail as $key => $item){
                $subtotal += $item['into_money'];
                $total += $item['total'];
            }
        }

        $base_currency = get_base_currency();
        $list_item = $this->Purchase_model->create_purchase_order_row_template();

        $currency_rate = 1;
        $to_currency = $base_currency;
        if($pur_estimate->currency != '' && $pur_estimate->currency_rate != null){
            $currency_rate = $pur_estimate->currency_rate;
            $to_currency = $pur_estimate->currency;
        }


        if(count($pur_estimate_detail) > 0){
            $index = 0;
            foreach($pur_estimate_detail as $key => $item){
                $index++;
                $unit_name = pur_get_unit_name($item['unit_id']);
                $taxname = $item['tax_name'];
                $item_name = $item['item_name'];
                if(strlen($item_name) == 0){
                    $item_name = pur_get_item_variatiom($item['item_code']);
                }

                $list_item .= $this->Purchase_model->create_purchase_order_row_template('newitems[' . $index . ']',  $item_name, '', $item['quantity'], $unit_name, $item['unit_price'], $taxname, $item['item_code'], $item['unit_id'], $item['tax_rate'],  $item['total_money'], $item['discount_%'], $item['discount_money'], $item['total'], $item['into_money'], $item['tax'], $item['tax_value'], $index, true, $currency_rate, $to_currency);
            }
        }

        $taxes_data = $this->Purchase_model->get_html_tax_pur_estimate($pur_estimate_id);
        $tax_html = $taxes_data['html'];

        echo json_encode([
            'result' => $pur_estimate_detail,
            'dc_percent' => $pur_estimate->discount_percent,
            'dc_total' => $pur_estimate->discount_total,
            'subtotal' => to_currency(round($subtotal,2),''),
            'total' => to_currency(round($total, 2),''),
            'tax_html' => $tax_html,
            'taxes' => $taxes,
            'list_item' => $list_item,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
            'shipping_fee' => $pur_estimate->shipping_fee
        ]);
    }

    /**
     * { coppy sale invoice }
     */
    public function coppy_sale_invoice_po($invoice_id){
        $invoice = $this->Invoices_model->get_details(['id' => $invoice_id])->getRow();
        $invoice_items = $this->Invoice_items_model->get_details(['invoice_id' => $invoice_id])->getResultArray();
        $invoice_sumary = $this->Invoices_model->get_invoice_total_summary($invoice_id);

        $base_currency = get_base_currency();

        $list_item = $this->Purchase_model->create_purchase_order_row_template();
        $currency_rate = 1;
        $to_currency = $invoice_sumary->currency;

        if($to_currency != $base_currency){
            $currency_rate = pur_get_currency_rate($to_currency);
        }

        if($invoice){
            if(count($invoice_items) > 0){
                $index_request = 0;
                foreach($invoice_items as $key => $item){
                    $index_request++;

                    $tax = '';
                    $tax_value = 0;
                    $tax_name = [];
                    $tax_name[0] = '';
                    $tax_rate = '';

                    if($invoice->tax_id != 0){
                        $tax .= $invoice->tax_id;
                        $tax_rate .= $invoice->tax_percentage;
                        $tax_name[] = $this->Purchase_model->get_tax_name($invoice->tax_id).'|'.$invoice->tax_percentage;
                        $tax_value += ($item['total']*$invoice->tax_percentage)/100;
                    }

                    if($invoice->tax_id2 != 0){
                        $tax .= '|'.$invoice->tax_id2;
                        $tax_rate .= '|'.$invoice->tax_percentage2;
                        $tax_name[] = $this->Purchase_model->get_tax_name($invoice->tax_id2).'|'.$invoice->tax_percentage2;
                        $tax_value += ($item['total']*$invoice->tax_percentage2)/100;
                    }

                    if($invoice->tax_id3 != 0){
                        $tax .= '|'.$invoice->tax_id3;
                        $tax_rate .= '|'.$invoice->tax_percentage3;
                        $tax_name[] = $this->Purchase_model->get_tax_name($invoice->tax_id3).'(TDS)|'.$invoice->tax_percentage3;
                        $tax_value += ($item['total']*$invoice->tax_percentage3)/100;
                    }

                    $item_code = get_item_id_by_des($item['title']);
                    $item_text = $item['title'];
                    $unit_price = $item['rate'];
                    $unit_name = $item['unit_type'];
                    $into_money = (float) ($item['rate'] * $item['quantity']);
                    $total = $tax_value + $into_money;


                    $list_item .= $this->Purchase_model->create_purchase_order_row_template('newitems[' . $index_request . ']', $item_text, $item['description'], $item['quantity'], $unit_name, $unit_price, $tax_name, $item_code, '', $tax_rate, $total, '', '', $total, $into_money, $tax, $tax_value, $index_request, false, $currency_rate, $to_currency);

                }
            }
        }

        echo json_encode([
            'list_item' => $list_item,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
        ]);

    }

    /**
     * { invoices }
     * @return view
     */
    public function invoices(){
        $data['title'] = _l('invoices');

        $data['user_type'] = $this->login_user->user_type;

        $data['pur_orders'] = $this->Purchase_model->get_list_pur_orders();
        if($data['user_type'] == 'vendor'){
            $vendor_id = get_vendor_user_id();
            $data['pur_orders'] = $this->Purchase_model->get_pur_order_by_vendor($vendor_id);
        }

        $data['vendors'] = $this->Purchase_model->get_vendors();

        return $this->template->rander("Purchase\Views\invoices\manage", $data);
    }

    /**
     * { table pur invoices }
     */
    public function table_pur_invoices(){
        $dataPost = $this->request->getPost();
        $dataPost['user_type'] = $this->login_user->user_type;
        $this->Purchase_model->get_table_data(module_views_path('Purchase', 'invoices/table_pur_invoices'), $dataPost);
    }

    /**
     * { purchase invoice }
     *
     * @param      string  $id     The identifier
     */
    public function pur_invoice($id = ''){
        $data['user_type'] = $this->login_user->user_type;

        if($id == ''){
            $data['title'] = _l('add_invoice');

        }else{
            $data['title'] = _l('edit_invoice');
            
        }


        $tax_options = array(
            "deleted" => 0,
        );
        $data['taxes'] = $this->Taxes_model->get_details($tax_options)->getResultArray();

        $data['currencies'] = $this->_get_currency_dropdown_select2_data();

        $data['vendors'] = $this->Purchase_model->get_vendors();
        $pur_invoice_row_template = $this->Purchase_model->create_purchase_invoice_row_template();

        $data['base_currency'] = get_base_currency();

        if($id != ''){
            $data['pur_orders'] = $this->Purchase_model->get_pur_order_approved();

            if($data['user_type'] == 'vendor'){
                $vendor_id = get_vendor_user_id();
                $data['pur_orders'] = $this->Purchase_model->get_pur_order_approved_by_vendor($vendor_id);
            }

            $data['pur_invoice'] = $this->Purchase_model->get_pur_invoice($id);

            if(!$data['pur_invoice']){
                show_404();
            }

            if($data['user_type'] == 'vendor'){
                $vendor_id = get_vendor_user_id();
                if($data['pur_invoice']->vendor != $vendor_id){
                    show_404();
                }
            }

            $data['pur_invoice_detail'] = $this->Purchase_model->get_pur_invoice_detail($id);

            $currency_rate = 1;
            if($data['pur_invoice']->currency != '' && $data['pur_invoice']->currency_rate != null){
                $currency_rate = $data['pur_invoice']->currency_rate;
            }

            $to_currency = $data['base_currency'];
            if($data['pur_invoice']->currency != '' && $data['pur_invoice']->to_currency != null) {
                $to_currency = $data['pur_invoice']->to_currency;
            }

            if (count($data['pur_invoice_detail']) > 0) { 
                $index_order = 0;
                foreach ($data['pur_invoice_detail'] as $inv_detail) { 
                    $index_order++;
                    $unit_name = pur_get_unit_name($inv_detail['unit_id']);
                    $taxname = $inv_detail['tax_name'];
                    $item_name = $inv_detail['item_name'];

                    if(strlen($item_name) == 0){
                        $item_name = pur_get_item_variatiom($inv_detail['item_code']);
                    }

                    $pur_invoice_row_template .= $this->Purchase_model->create_purchase_invoice_row_template('items[' . $index_order . ']',  $item_name, $inv_detail['description'], $inv_detail['quantity'], $unit_name, $inv_detail['unit_price'], $taxname, $inv_detail['item_code'], $inv_detail['unit_id'], $inv_detail['tax_rate'],  $inv_detail['total_money'], $inv_detail['discount_percent'], $inv_detail['discount_money'], $inv_detail['total'], $inv_detail['into_money'], $inv_detail['tax'], $inv_detail['tax_value'], $inv_detail['id'], true, $currency_rate, $to_currency);
                }
            }else{
                $item_name = $data['pur_invoice']->invoice_number;
                $description = $data['pur_invoice']->adminnote;
                $quantity = 1;
                $taxname = '';
                $tax_rate = 0;
                $tax = get_tax_rate_item($id);
                if($tax && !is_array($tax)){
                    $taxname = $tax->title;
                    $tax_rate = $tax->percentage;
                }

                $total = $data['pur_invoice']->subtotal + $data['pur_invoice']->tax;
                $index = 0;

                $pur_invoice_row_template .= $this->Purchase_model->create_purchase_invoice_row_template('newitems[' . $index . ']',  $item_name, $description, $quantity, '', $data['pur_invoice']->subtotal, $taxname, null, null, $tax_rate,  $data['pur_invoice']->total, 0, 0, $total, $data['pur_invoice']->subtotal , $data['pur_invoice']->tax_rate, $data['pur_invoice']->tax, '', true);
            }

        }else{
            $data['pur_orders'] = $this->Purchase_model->get_pur_order_approved_for_inv();
            if($data['user_type'] == 'vendor'){
                $vendor_id = get_vendor_user_id();
                $data['pur_orders'] = $this->Purchase_model->get_pur_order_approved_for_inv_by_vendor($vendor_id);
            }
        }

        $data['pur_invoice_row_template'] = $pur_invoice_row_template;
        $data['vendor_id'] = $this->request->getGet('vendor');
        if($data['user_type'] == 'vendor'){
            $vendor_id = get_vendor_user_id();
            $data['vendor_id'] = $vendor_id;
        }

        if($data['vendor_id'] == '' && count($data['vendors']) > 0){
            $data['vendor_id'] = $data['vendors'][0]['userid'];
        }

        $data['ajaxItems'] = false;
        if($data['user_type'] == 'staff'){
            if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
                $data['items'] = $this->Purchase_model->pur_get_grouped('can_be_purchased');
            } else {
                $data['items']     = [];
                $data['ajaxItems'] = true;
            }
        }else if($data['user_type'] == 'vendor'){
            if(total_rows(db_prefix().'pur_vendor_items', ['vendor' => get_vendor_user_id()]) <= ajax_on_total_items()){ 
                $data['items'] = $this->Purchase_model->pur_get_grouped('can_be_purchased', false, get_vendor_user_id());
            }else {
                $data['items']     = [];
                $data['ajaxItems'] = true;
            }
        }

        return $this->template->rander("Purchase\Views\invoices\pur_invoice", $data);
    }

    /**
     * { pur invoice form }
     * @return redirect
     */
    public function pur_invoice_form(){
        if($this->request->getPost()){
            $data = $this->request->getPost();
            if($data['id'] == ''){
                unset($data['id']);
                $mess = $this->Purchase_model->add_pur_invoice($data);
                if ($mess) {

                    $this->session->setFlashdata("success_message", app_lang("added_successfully"));
                }
                app_redirect('purchase/invoices');
            }else{
                $id = $data['id'];
                unset($data['id']);

                $success = $this->Purchase_model->update_pur_invoice($id, $data);
                if($success){
                    $this->session->setFlashdata("success_message", app_lang("updated_successfully"));
                }
                app_redirect('purchase/invoices');
            }
        }
    }

    /**
     * Gets the purchase order row template.
     */
    public function get_purchase_invoice_row_template(){
        $name = $this->request->getPost('name');
        $item_name = $this->request->getPost('item_name');
        $item_description = $this->request->getPost('item_description');
        $quantity = $this->request->getPost('quantity');
        $unit_name = $this->request->getPost('unit_name');
        $unit_price = $this->request->getPost('unit_price');
        $taxname = $this->request->getPost('taxname');
        $item_code = $this->request->getPost('item_code');
        $unit_id = $this->request->getPost('unit_id');
        $tax_rate = $this->request->getPost('tax_rate');
        $discount = $this->request->getPost('discount');
        $item_key = $this->request->getPost('item_key');
        $currency_rate = $this->request->getPost('currency_rate');
        $to_currency = $this->request->getPost('to_currency');

        echo html_entity_decode($this->Purchase_model->create_purchase_invoice_row_template($name, $item_name, $item_description, $quantity, $unit_name, $unit_price, $taxname, $item_code, $unit_id, $tax_rate, '', $discount, '', '', '', '', '', $item_key, false, $currency_rate, $to_currency ));
    }

    /**
     * { purchase invoice }
     *
     * @param       $id     The identifier
     */
    public function purchase_invoice($id){
        if (!$id) {
            app_redirect(admin_url('purchase/invoices'));
        }


        $data['pur_invoice'] = $this->Purchase_model->get_pur_invoice($id);

        if(!$data['pur_invoice']){
            show_404();
        }

        $data['user_type'] = $this->login_user->user_type;
        if($data['user_type'] == 'vendor'){
            $vendor_id = get_vendor_user_id();
            if($data['pur_invoice']->vendor != $vendor_id){
                show_404();
            }
        }

        $vendor_currency = get_vendor_currency($data['pur_invoice']->vendor);
        $data['vendor_currency'] = get_base_currency();
        if($vendor_currency != ''){
            $data['vendor_currency'] = $vendor_currency;
        }

        $data['invoice_detail'] = $this->Purchase_model->get_pur_invoice_detail($id);

        $data['tax_data'] = $this->Purchase_model->get_html_tax_pur_invoice($id);
        
        $data['title'] = $data['pur_invoice']->invoice_number;

        $data['payment_modes'] = $this->Payment_methods_model->get_available_online_payment_methods();

        $data['payment'] = $this->Purchase_model->get_payment_invoice($id);
        $data['pur_invoice_attachments'] = $this->Purchase_model->get_purchase_invoice_attachments($id);

        $data['tab'] = $this->request->getGet('tab');
        if($data['tab'] == ''){
            $data['tab'] = 'tab_pur_invoice';
        }

        return $this->template->rander("Purchase\Views\invoices\pur_invoice_preview", $data);
    }


    /**
     * { purchase invoice attachment }
     */
    public function purchase_invoice_attachment($id){
        handle_pur_invoice_file($id);
        app_redirect('purchase/purchase_invoice/'.$id);
    }

    /**
     * { preview purchase order file }
     *
     * @param      <type>  $id      The identifier
     * @param      <type>  $rel_id  The relative identifier
     * @return  view
     */
    public function file_pur_invoice($id, $rel_id)
    {

        $data['file'] = $this->Purchase_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }

        return $this->template->view('Purchase\Views\invoices\_file', $data);
    }

    /**
     * { delete purchase order attachment }
     *
     * @param      <type>  $id     The identifier
     */
    public function delete_purinv_attachment($id)
    {

        $file = $this->Purchase_model->get_file($id);
        if ($file->staffid == get_staff_user_id1() || is_admin()) {
            echo html_entity_decode($this->Purchase_model->delete_purinv_attachment($id));
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /**
     * Adds a payment modal.
     */
    public function add_payment_modal($invoice_id){
        $data['pur_invoice'] = $this->Purchase_model->get_pur_invoice($invoice_id);
        $data['payment_modes'] = $this->Payment_methods_model->get_details([])->getResultArray();
        return $this->template->view('Purchase\Views\invoices\payment_modal', $data);
    }

    /**
     * { vendors change }
     */
    public function pur_vendors_change($vendor){
        $currency_id = get_vendor_currency($vendor);
        if($currency_id == ''){
            $currency_id = get_base_currency();
        }

        $option_po = '<option value=""></option>';
        $option_ct = '<option value=""></option>';
       
        $pur_orders = $this->Purchase_model->get_pur_order_approved_for_inv_by_vendor($vendor);
        foreach($pur_orders as $po){
            $option_po .= '<option value="'.$po['id'].'">'.$po['pur_order_number'].'</option>';
        }
    
        $option_html = '';

        if(total_rows(db_prefix().'pur_vendor_items', ['vendor' => $vendor]) <= ajax_on_total_items()){
            $items = $this->Purchase_model->get_items_by_vendor_variation($vendor);
            $option_html .= '<option value=""></option>';
            foreach($items as $item){
                $option_html .= '<option value="'.$item['id'].'" >'.$item['label'].'</option>';
            }
        }

        echo json_encode([
            'type' => get_setting('create_invoice_by'),
            'html' => $option_ct,
            'po_html' => $option_po,
            'option_html' => $option_html,
            'currency_id' => $currency_id,
        ]);
    }

    /**
     * Adds a payment for invoice.
     *
     * @param      <type>  $pur_order  The purchase order id
     * @return  redirect
     */
    public function add_invoice_payment($invoice){
         if ($this->request->getPost()) {
            $data = $this->request->getPost();
            $message = '';
            $success = $this->Purchase_model->add_invoice_payment($data, $invoice);
            if ($success) {
                $this->session->setFlashdata("success_message", app_lang("added_successfully"));
            }
            
            app_redirect('purchase/purchase_invoice/'.$invoice);
            
        }
    }

    /**
     * { purchase order change }
     *
     * @param      <type>  $ct    
     */
    public function pur_order_change($ct){
        $pur_order = $this->Purchase_model->get_pur_order($ct);
        $pur_order_detail = $this->Purchase_model->get_pur_order_detail($ct);
        
        $list_item = $this->Purchase_model->create_purchase_order_row_template();
        $discount_percent = 0;

        $base_currency = get_base_currency();

        $currency_rate = 1;
        $to_currency = $base_currency;
        if($pur_order->currency != '' && $pur_order->currency_rate != null){
            $currency_rate = $pur_order->currency_rate;
            $to_currency = $pur_order->currency;
        }

        if(count($pur_order_detail) > 0){
            $index = 0;
            foreach($pur_order_detail as $key => $item){
                $index++;
                $unit_name = pur_get_unit_name($item['unit_id']);
                $taxname = $item['tax_name'];
                $item_name = $item['item_name'];
                if(strlen($item_name) == 0){
                    $item_name = pur_get_item_variatiom($item['item_code']);
                }

                $list_item .= $this->Purchase_model->create_purchase_invoice_row_template('newitems[' . $index . ']',  $item_name, '', $item['quantity'], $unit_name, $item['unit_price'], $taxname, $item['item_code'], $item['unit_id'], $item['tax_rate'],  $item['total_money'], $item['discount_%'], $item['discount_money'], $item['total'], $item['into_money'], $item['tax'], $item['tax_value'], $index, true, $currency_rate, $to_currency);
            }
        }

        if($pur_order){
            $discount_percent = $pur_order->discount_percent;
        }

        echo json_encode([
            'list_item' => $list_item,
            'discount_percent' => $discount_percent,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
            'shipping_fee' => $pur_order->shipping_fee,
            'order_discount' => $pur_order->discount_total,
        ]);
    }


    /**
     * { payment invoice }
     *
     * @param       $id     The identifier
     * @return view
     */
    public function payment_invoice($id){

        $session = \Config\Services::session();
        $send_mail_approve = $session->has("send_mail_approve");
        if (($send_mail_approve) && $session->get("send_mail_approve") != '') {

            $data['send_mail_approve'] = $session->get("send_mail_approve");
            $session->remove("send_mail_approve");
        }
        
        $data['check_appr'] = $this->Purchase_model->get_approve_setting('payment_request');
        $data['get_staff_sign'] = $this->Purchase_model->get_staff_sign($id,'payment_request');
        $data['check_approve_status'] = $this->Purchase_model->check_approval_details($id,'payment_request');
        $data['list_approve_status'] = $this->Purchase_model->get_list_approval_details($id,'payment_request');


        $data['payment_invoice'] = $this->Purchase_model->get_payment_pur_invoice($id);
        $data['title'] = _l('payment_for').' '.get_pur_invoice_number($data['payment_invoice']->pur_invoice);

        $data['invoice'] = $this->Purchase_model->get_pur_invoice($data['payment_invoice']->pur_invoice);

        $data['base_currency'] = get_base_currency();
        if($data['invoice']->currency != ''){
            $data['base_currency'] = $data['invoice']->currency;
        }

        return $this->template->rander("Purchase\Views\invoices\payment_invoice", $data);
    }

    /**
     * { delete pur invoice modal }
     */
    public function delete_pur_invoice_modal(){

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        if($this->request->getPost('id')){
            $data['function'] = 'delete_pur_invoice';
            $data['id'] = $this->request->getPost('id');
            return $this->template->view('Purchase\Views\items\delete_modal_form', $data);
        }
    }

    /**
     * { delete pur invoice }
     *
     * @param        $id     The identifier
     */
    public function delete_pur_invoice(){
        $id = $this->request->getPost('id');

        if (!$id) {
            app_redirect('purchase/invoices');
        }
        $response = $this->Purchase_model->delete_pur_invoice($id);
        if (is_array($response) && isset($response['referenced'])) {
            $this->session->setFlashdata("error_message", app_lang("is_referenced"));
        } elseif ($response == true) {
            $this->session->setFlashdata("success_message", app_lang("record_deleted"));
        } else {
            $this->session->setFlashdata("error_message", app_lang("problem_deleting"));
        }
        app_redirect('purchase/invoices');
    }

    /**
     * { delete pur invoice modal }
     */
    public function delete_payment_pur_invoice_modal(){

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        if($this->request->getPost('id')){
            $data['function'] = 'delete_payment_pur_invoice';
            $data['id'] = $this->request->getPost('id');
            return $this->template->view('Purchase\Views\items\delete_modal_form', $data);
        }
    }


    /**
     * { delete payment }
     *
     * @param       $id         The identifier
     * @param        $pur_order  The pur order
     * @return  redirect
     */
    public function delete_payment_pur_invoice()
    {
        $id = $this->request->getPost('id');

        $payment = $this->Purchase_model->get_payment_pur_invoice($id);
        if (!$id) {
            app_redirect('purchase/invoices');
        }

        $response = $this->Purchase_model->delete_payment_pur_invoice($id);
        if (is_array($response) && isset($response['referenced'])) {
            $this->session->setFlashdata("error_message", app_lang("is_referenced"));
        } elseif ($response == true) {
            $this->session->setFlashdata("success_message", app_lang("record_deleted"));
        } else {
            $this->session->setFlashdata("error_message", app_lang("problem_deleting"));
        }
        app_redirect('purchase/purchase_invoice/'.$payment->pur_invoice);
    }


    /**
     * { table pur request }
     */
    public function table_vendor_quotations($vendor_id){
        $dataPost = $this->request->getPost();
        $dataPost['vendor'] = [$vendor_id];
        $dataPost['user_type'] = [$this->login_user->user_type];
        $this->Purchase_model->get_table_data(module_views_path('Purchase', 'quotations/table_estimates'), $dataPost);
    }


    /**
     * { table pur request }
     */
    public function table_vendor_pur_order($vendor_id){
        $dataPost = $this->request->getPost();
        $dataPost['vendor_profile_id'] = [$vendor_id];
        $dataPost['user_type'] = [$this->login_user->user_type];
        $this->Purchase_model->get_table_data(module_views_path('Purchase', 'purchase_orders/table_pur_order'), $dataPost);
    }

    /**
     * { table pur request }
     */
    public function table_vendor_pur_invoices($vendor_id){
        $dataPost = $this->request->getPost();
        $dataPost['vendor'] = [$vendor_id];
        $dataPost['user_type'] = [$this->login_user->user_type];
        $this->Purchase_model->get_table_data(module_views_path('Purchase', 'invoices/table_pur_invoices'), $dataPost);
    }

    /**
     * { function_description }
     */
    public function vendor_contact_profile($contact_id = 0, $tab = ""){

        $view_data['user_info'] = $this->Users_model->get_one($contact_id);

        $view_data['vendor_info'] = $this->Purchase_model->get_vendor($view_data['user_info']->vendor_id);
        $view_data['tab'] = clean_data($tab);
        if ($view_data['user_info']->user_type === "vendor") {

            $view_data['show_cotact_info'] = true;
            $view_data['show_social_links'] = true;
            $view_data['social_link'] = $this->Social_links_model->get_one($contact_id);

            return $this->template->rander('Purchase\Views\vendor_portal\contacts_view', $view_data);
        } else {
            show_404();
        }
    }

    /**
     * { vendor portal items }
     */
    public function vendor_portal_items(){
        if($this->login_user->user_type == 'vendor'){
            $data['items'] = $this->Purchase_model->get_vendor_item(get_vendor_user_id());

            $data['external_items'] = $this->Purchase_model->get_item_by_vendor(get_vendor_user_id());

            $data['tab'] = $this->request->getGet('tab');
            $data['title'] = _l('items');

            return $this->template->rander('Purchase\Views\vendor_portal\items\items', $data);
        }else{
            show_404();
        }
    }

    /**
     * { vendor contacts }
     *
     * @param      <type>  $client_id  The client identifier
     */
    public function vendor_contacts($vendor_id)
    {
        $dataPost = $this->request->getPost();
        $dataPost['vendor'] = [$vendor_id];
        $this->Purchase_model->get_table_data(module_views_path('Purchase', 'vendors/table_contacts'), $dataPost);
    }

    /**
     * { function_description }
     */
    public function vendor_contact_modal_form($customer_id, $contact_id = ''){

        $data['customer_id'] = $customer_id;
        $data['contactid']   = $contact_id;
        
        if ($contact_id == '') {
            $title = _l('new_contact');
        } else {
            $data['contact'] = $this->Purchase_model->get_contact($contact_id);
            $title = $data['contact']->first_name . ' ' . $data['contact']->last_name;
        }

        $data['title']                = $title;
        return $this->template->view('Purchase\Views\vendors\modals\contact', $data);
    }

    /**
     * { form contact }
     *
     * @param      string  $customer_id  The customer identifier
     * @param      string  $contact_id   The contact identifier
     */
    public function form_contact($customer_id, $contact_id = ''){
        if ($this->request->getPost()) {
            $data             = $this->request->getPost();
            $data['password'] = $this->request->getPost('password');

            unset($data['contactid']);
            if ($contact_id == '') {

                $id      = $this->Purchase_model->add_contact($data, $customer_id);
                $message = '';
                $success = false;
                if ($id) {
                   
                    $success = true;
                    $this->session->setFlashdata("success_message", app_lang("added_successfully"));
                }
             }else{
  
                $original_contact = $this->Purchase_model->get_contact($contact_id);
                $success          = $this->Purchase_model->update_contact($data, $contact_id);
                $message          = '';
                $proposal_warning = false;
                $original_email   = '';
                $updated          = false;
                if (is_array($success)) {
                    if (isset($success['set_password_email_sent'])) {
                        $message = _l('set_password_email_sent_to_client');
                    } elseif (isset($success['set_password_email_sent_and_profile_updated'])) {
                        $updated = true;
                        $message = _l('set_password_email_sent_to_client_and_profile_updated');
                    }
                } else {
                    if ($success == true) {
                        $updated = true;
                        $message = _l('updated_successfully', _l('contact'));
                    }
                }
   
                if ($updated == true) {
                    $contact = $this->Purchase_model->get_contact($contact_id);

                    $this->session->setFlashdata("success_message", app_lang("updated_successfully"));
                }
            }
            
            app_redirect('purchase/vendor/'.$customer_id.'?group=contacts');
        }
    }


    /**
     * { delete pur invoice modal }
     */
    public function delete_contact_modal(){

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        if($this->request->getPost('id')){
            $data['function'] = 'delete_vendor_contact';
            $data['id'] = $this->request->getPost('id');
            return $this->template->view('Purchase\Views\items\delete_modal_form', $data);
        }
    }


    /**
     * { delete payment }
     *
     * @param       $id         The identifier
     * @param        $pur_order  The pur order
     * @return  redirect
     */
    public function delete_vendor_contact()
    {
        $id = $this->request->getPost('id');

        $contact = $this->Purchase_model->get_contact($id);
        if (!$id) {
            app_redirect('purchase/invoices');
        }

        $response = $this->Purchase_model->delete_vendor_contact($id);
        if (is_array($response) && isset($response['referenced'])) {
            $this->session->setFlashdata("error_message", app_lang("is_referenced"));
        } elseif ($response == true) {
            $this->session->setFlashdata("success_message", app_lang("record_deleted"));
        } else {
            $this->session->setFlashdata("error_message", app_lang("problem_deleting"));
        }
        app_redirect('purchase/vendor/'.$contact->vendor_id.'?group=contacts');
    }

    /**
     * Determines if contact email exists.
     */
    public function contact_email_exists()
    {
        
        if ($this->request->getPost()) {
            // First we need to check if the email is the same
            $builder = db_connect('default');

            $userid = $this->request->getPost('contact_id');
            $builder = $builder->table(db_prefix() . 'users');
            if ($userid != '') {
                $builder->where('id', $userid);
                $_current_email = $builder->get()->getRow();
                if ($_current_email->email == $this->request->getPost('email')) {
                    echo json_encode(true);
                    die();
                }
            }
          
            $total_rows = total_rows(db_prefix().'users', ['email' => $this->request->getPost('email')]);
            if ($total_rows > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
            die();
            
        }
        
    }

    /**
     * Adds update vendor items.
     *
     * @param      string  $id     The identifier
     *
     * @return       view
     */
    public function add_update_vendor_items($id = ''){
        if($this->login_user->user_type != 'vendor'){
            show_404();
        }

        $vendor_id = get_vendor_user_id();

        if($id == ''){
            $data['title'] = _l('pur_add_item');
        }else{
            $data['title'] = _l('pur_update_item');
            $data['item'] = $this->Purchase_model->get_item_of_vendor($id);
        }

        if($this->request->getPost()){
            $item_data = $this->request->getPost();
            if($id == ''){
                
                $item_id = $this->Purchase_model->add_vendor_item($item_data, $vendor_id);
                if($item_id){
                    handle_vendor_item_attachment($item_id);

                    $this->session->setFlashdata("success_message", app_lang("added_successfully"));
                    
                }
            }else{
                if($data['item']->vendor_id != $vendor_id){

                    set_alert('warning', _l('item_not_found'));

                    app_redirect('purchase/vendor_portal_items');
                }

                $success = $this->Purchase_model->update_vendor_item($item_data, $id);

                $handled = handle_vendor_item_attachment($id);
                if($success || $handled){

                    $this->session->setFlashdata("success_message", app_lang("updated_successfully"));
                }
            }

            app_redirect('purchase/vendor_portal_items');
        }

        $data['units'] = $this->Purchase_model->get_unit_add_item();
        $data['taxes'] = $this->Purchase_model->get_taxes();
        $data['commodity_groups'] = $this->Purchase_model->get_commodity_group_add_commodity();
        $data['sub_groups'] = $this->Purchase_model->get_sub_group();

        return $this->template->rander('Purchase\Views\vendor_portal\items\item', $data);
    }

    /**
     * { detail item }
     */
    public function detail_vendor_item($item_id){

        $vendor_id = get_vendor_user_id();

        $data['item'] = $this->Purchase_model->get_item_of_vendor($item_id);

        $data['commodity_file'] = $this->Purchase_model->get_vendor_item_file($item_id);

        if($data['item']->vendor_id != $vendor_id){
            $this->session->setFlashdata("error_message", app_lang("item_not_found"));
            app_redirect('purchase/vendor_portal_items');
        }

        $data['title'] = $data['item']->commodity_code;

        return $this->template->rander('Purchase\Views\vendor_portal\items\detail_item', $data);
    }

    /**
     * { share_item }
     */
    public function share_item($item_id){

        $vendor_id = get_vendor_user_id();

        $item = $this->Purchase_model->get_item_of_vendor($item_id);
        if($item->vendor_id != $vendor_id){
            $this->session->setFlashdata("error_message", app_lang("item_not_found"));
            app_redirect('purchase/vendor_portal_items');
        }

        $shared = $this->Purchase_model->share_vendor_item($item_id);
        if($shared){

            $this->session->setFlashdata("success_message", app_lang("shared_successfully"));
        }

        app_redirect('purchase/vendor_portal_items');
    }

    /**
     * delete modal form
     * @return [type] 
     */
    public function delete_vendor_item_modal() {


        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        if($this->request->getPost('id')){
            $data['function'] = 'delete_vendor_item';
            $data['id'] = $this->request->getPost('id');
            return $this->template->view('Purchase\Views\items\delete_modal_form', $data);
        }
    }

    /**
     * delete vendor items
     * @param  integer $id
     * @return redirect
     */
    public function delete_vendor_item() {
        
        $id = $this->request->getPost('id');
        $vendor_id = get_vendor_user_id();

        $deleted = $this->Purchase_model->delete_vendor_item($id, $vendor_id);
        if($deleted){

            $this->session->setFlashdata('success_message', app_lang('record_deleted'));
        }else{

            $this->session->setFlashdata('error_message', app_lang('record_cannot_be_deleted'));
        }

        app_redirect('purchase/vendor_portal_items');
    }

    /**
     * Sends an PO modal form.
     *
     * @param        $po_id  The PO identifier
     *
     * @return       view
     */
    public function send_po_modal_form($po_id) {

        if ($po_id) {

            $po_info = $this->Purchase_model->get_pur_order($po_id);
            $view_data['po_info'] = $po_info;

            $contacts_options = array("user_type" => "vendor", "vendor_id" => $po_info->vendor);
            $contacts = $this->Purchase_model->get_contact_details($contacts_options)->getResult();

            $primary_contact_info = "";
            $contacts_dropdown = array();
            foreach ($contacts as $contact) {
                if ($contact->is_primary_contact) {
                    $primary_contact_info = $contact;
                    $contacts_dropdown[$contact->id] = $contact->first_name . " " . $contact->last_name . " (" . app_lang("primary_contact") . ")";
                }
            }

            $cc_contacts_dropdown = array();

            foreach ($contacts as $contact) {
                if (!$contact->is_primary_contact) {
                    $contacts_dropdown[$contact->id] = $contact->first_name . " " . $contact->last_name;
                }

                $cc_contacts_dropdown[] = array("id" => $contact->id, "text" => $contact->first_name . " " . $contact->last_name);
            }

            $view_data['contacts_dropdown'] = $contacts_dropdown;
            $view_data['cc_contacts_dropdown'] = $cc_contacts_dropdown;

            $template_data = $this->get_send_po_template($po_id, 0, "", $po_info, $primary_contact_info);
            $view_data['message'] = get_array_value($template_data, "message");
            $view_data['subject'] = get_array_value($template_data, "subject");

            return $this->template->view('Purchase\Views\purchase_orders\send_po_modal_form', $view_data);
        } else {
            show_404();
        }
    }

    /**
     * Gets the send po template.
     *
     * @param      int     $po_id         The po identifier
     * @param      int     $contact_id    The contact identifier
     * @param      string  $return_type   The return type
     * @param      string  $po_info       The po information
     * @param      string  $contact_info  The contact information
     *
     * @return       The send po template.
     */
    public function get_send_po_template($po_id = 0, $contact_id = 0, $return_type = "", $po_info = "", $contact_info = "") {

        validate_numeric_value($po_id);
        validate_numeric_value($contact_id);

        if (!$po_info) {
            $options = array("id" => $po_id);
            $po_info = $this->Purchase_model->get_pur_order($po_id);
        }

        if (!$contact_info) {
            $contact_info = $this->Users_model->get_one($contact_id);
        }

        $email_template = $this->Email_templates_model->get_final_template("purchase_order_to_contact");

        $parser_data['PO_NUMBER'] = $po_info->pur_order_number;
        $parser_data['PO_NAME'] = $po_info->pur_order_name;
        $parser_data['PO_TAX_VALUE'] =  to_currency($po_info->total_tax, $po_info->currency);
        $parser_data['PO_SUBTOTAL'] =  to_currency($po_info->subtotal, $po_info->currency);
        $parser_data['PO_VALUE'] =  to_currency($po_info->total, $po_info->currency);
        $parser_data['PO_LINK'] = get_uri('purchase/view_pur_order/'.$po_id);
        $parser_data['ORDER_DATE'] = _d($po_info->order_date); 
        $parser_data['CONTACT_NAME'] =  $contact_info->first_name . " " . $contact_info->last_name;

        $message = $this->parser->setData($parser_data)->renderString($email_template->message);
        $message = htmlspecialchars_decode($message);
        $subject = $email_template->subject;

        if ($return_type == "json") {
            echo json_encode(array("success" => true, "message_view" => $message));
        } else {
            return array(
                "message" => $message,
                "subject" => $subject
            );
        }
    }

    /**
     * Sends an invoice.
     */
    public function send_po() {
        if (!$this->can_edit_invoices()) {
            app_redirect("forbidden");
        }

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $po_id = $this->request->getPost('id');

        $contact_id = $this->request->getPost('contact_id');

        $cc_array = array();
        $cc = $this->request->getPost('po_cc');

        if ($cc) {
            $cc = explode(',', $cc);

            foreach ($cc as $cc_value) {
                if (is_numeric($cc_value)) {
                    //selected a client contact
                    array_push($cc_array, $this->Users_model->get_one($cc_value)->email);
                } else {
                    //inputted an email address
                    array_push($cc_array, $cc_value);
                }
            }
        }

        $custom_bcc = $this->request->getPost('po_bcc');
        $subject = $this->request->getPost('subject');
        $message = decode_ajax_post_data($this->request->getPost('message'));

        $contact = $this->Users_model->get_one($contact_id);

    
        $attachement_url = $this->purorder_pdf($po_id, "send_email");

        $default_bcc = get_setting('send_bcc_to'); //get default settings
        $bcc_emails = "";

        if ($default_bcc && $custom_bcc) {
            $bcc_emails = $default_bcc . "," . $custom_bcc;
        } else if ($default_bcc) {
            $bcc_emails = $default_bcc;
        } else if ($custom_bcc) {
            $bcc_emails = $custom_bcc;
        }

        //add uploaded files
        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "purchase_order");
        $attachments = prepare_attachment_of_files(get_setting("timeline_file_path"), $files_data);

        //add invoice pdf
        array_unshift($attachments, array("file_path" => $attachement_url));

        if (send_app_mail($contact->email, $subject, $message, array("attachments" => $attachments, "cc" => $cc_array, "bcc" => $bcc_emails))) {
            
            // delete the temp invoice
            if (file_exists($attachement_url)) {
                unlink($attachement_url);
            }

            //delete attachments
            if ($files_data) {
                $files = unserialize($files_data);
                foreach ($files as $file) {
                    delete_app_files($target_path, array($file));
                }
            }

            echo json_encode(array('success' => true, 'message' => app_lang("po_sent_message"), "po_id" => $po_id));
        } else {
            echo json_encode(array('success' => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* upload a file */

    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for invoices */

    function validate_invoices_file() {
        return validate_post_file($this->request->getPost("file_name"));
    }


    /**
     * Sends an PQ modal form.
     *
     * @param        $pq_id  The PO identifier
     *
     * @return       view
     */
    public function send_pq_modal_form($pq_id) {

        if ($pq_id) {

            $pq_info = $this->Purchase_model->get_estimate($pq_id);
            $view_data['pq_info'] = $pq_info;

            $contacts_options = array("user_type" => "vendor", "vendor_id" => $pq_info->vendor);
            $contacts = $this->Purchase_model->get_contact_details($contacts_options)->getResult();

            $primary_contact_info = "";
            $contacts_dropdown = array();
            foreach ($contacts as $contact) {
                if ($contact->is_primary_contact) {
                    $primary_contact_info = $contact;
                    $contacts_dropdown[$contact->id] = $contact->first_name . " " . $contact->last_name . " (" . app_lang("primary_contact") . ")";
                }
            }

            $cc_contacts_dropdown = array();

            foreach ($contacts as $contact) {
                if (!$contact->is_primary_contact) {
                    $contacts_dropdown[$contact->id] = $contact->first_name . " " . $contact->last_name;
                }

                $cc_contacts_dropdown[] = array("id" => $contact->id, "text" => $contact->first_name . " " . $contact->last_name);
            }

            $view_data['contacts_dropdown'] = $contacts_dropdown;
            $view_data['cc_contacts_dropdown'] = $cc_contacts_dropdown;

            $template_data = $this->get_send_pq_template($pq_id, 0, "", $pq_info, $primary_contact_info);
            $view_data['message'] = get_array_value($template_data, "message");
            $view_data['subject'] = get_array_value($template_data, "subject");

            return $this->template->view('Purchase\Views\quotations\send_pq_modal_form', $view_data);
        } else {
            show_404();
        }
    }

    /**
     * Gets the send pq template.
     *
     * @param      int     $po_id         The po identifier
     * @param      int     $contact_id    The contact identifier
     * @param      string  $return_type   The return type
     * @param      string  $po_info       The po information
     * @param      string  $contact_info  The contact information
     *
     * @return       The send po template.
     */
    public function get_send_pq_template($pq_id = 0, $contact_id = 0, $return_type = "", $pq_info = "", $contact_info = "") {

        validate_numeric_value($pq_id);
        validate_numeric_value($contact_id);

        if (!$pq_info) {
            $options = array("id" => $pq_id);
            $pq_info = $this->Purchase_model->get_estimate($pq_id);
        }

        if (!$contact_info) {
            $contact_info = $this->Users_model->get_one($contact_id);
        }

        $email_template = $this->Email_templates_model->get_final_template("purchase_quotation_to_contact");

        $parser_data['PQ_NUMBER'] = format_pur_estimate_number($pq_id);
        $parser_data['PQ_TAX_VALUE'] =  to_currency($pq_info->total_tax, $pq_info->currency);
        $parser_data['PQ_SUBTOTAL'] =  to_currency($pq_info->subtotal, $pq_info->currency);
        $parser_data['PQ_VALUE'] =  to_currency($pq_info->total, $pq_info->currency);
        $parser_data['PQ_LINK'] = get_uri('purchase/view_quotation/'.$pq_id);
        $parser_data['DATE'] = _d($pq_info->date); 
        $parser_data['EXPIRY_DATE'] = _d($pq_info->expirydate);
        $parser_data['CONTACT_NAME'] =  $contact_info->first_name . " " . $contact_info->last_name;

        $message = $this->parser->setData($parser_data)->renderString($email_template->message);
        $message = htmlspecialchars_decode($message);
        $subject = $email_template->subject;

        if ($return_type == "json") {
            echo json_encode(array("success" => true, "message_view" => $message));
        } else {
            return array(
                "message" => $message,
                "subject" => $subject
            );
        }
    }

    /**
     * Sends an invoice.
     */
    public function send_pur_quotation() {

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $pq_id = $this->request->getPost('id');

        $contact_id = $this->request->getPost('contact_id');

        $cc_array = array();
        $cc = $this->request->getPost('pq_cc');

        if ($cc) {
            $cc = explode(',', $cc);

            foreach ($cc as $cc_value) {
                if (is_numeric($cc_value)) {
                    //selected a client contact
                    array_push($cc_array, $this->Users_model->get_one($cc_value)->email);
                } else {
                    //inputted an email address
                    array_push($cc_array, $cc_value);
                }
            }
        }

        $custom_bcc = $this->request->getPost('pq_bcc');
        $subject = $this->request->getPost('subject');
        $message = decode_ajax_post_data($this->request->getPost('message'));

        $contact = $this->Users_model->get_one($contact_id);

    
        $attachement_url = $this->purestimate_pdf($pq_id, "send_email");

        $default_bcc = get_setting('send_bcc_to'); //get default settings
        $bcc_emails = "";

        if ($default_bcc && $custom_bcc) {
            $bcc_emails = $default_bcc . "," . $custom_bcc;
        } else if ($default_bcc) {
            $bcc_emails = $default_bcc;
        } else if ($custom_bcc) {
            $bcc_emails = $custom_bcc;
        }

        //add uploaded files
        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "purchase_quotation");
        $attachments = prepare_attachment_of_files(get_setting("timeline_file_path"), $files_data);

        //add invoice pdf
        array_unshift($attachments, array("file_path" => $attachement_url));

        if (send_app_mail($contact->email, $subject, $message, array("attachments" => $attachments, "cc" => $cc_array, "bcc" => $bcc_emails))) {
            
            // delete the temp invoice
            if (file_exists($attachement_url)) {
                unlink($attachement_url);
            }

            //delete attachments
            if ($files_data) {
                $files = unserialize($files_data);
                foreach ($files as $file) {
                    delete_app_files($target_path, array($file));
                }
            }

            echo json_encode(array('success' => true, 'message' => app_lang("pq_sent_message"), "pq_id" => $pq_id));
        } else {
            echo json_encode(array('success' => false, 'message' => app_lang('error_occurred')));
        }
    }

    /**
     * Sends an PQ modal form.
     *
     * @param        $pq_id  The PO identifier
     *
     * @return       view
     */
    public function send_pr_modal_form($pr_id) {

        if ($pr_id) {

            $pr_info = $this->Purchase_model->get_purchase_request($pr_id);
            $view_data['pr_info'] = $pr_info;

            $contacts_options = array("user_type" => "vendor");
            $contacts = $this->Purchase_model->get_contact_details($contacts_options)->getResult();

            $primary_contact_info = "";
            $contacts_dropdown = array();
            foreach ($contacts as $contact) {
                if ($contact->is_primary_contact) {
                    $primary_contact_info = $contact;
                    $contacts_dropdown[$contact->id] = $contact->first_name . " " . $contact->last_name . " (" . app_lang("primary_contact") . ") - ". get_vendor_company_name($contact->vendor_id);
                }
            }

            $cc_contacts_dropdown = array();

            foreach ($contacts as $contact) {
                if (!$contact->is_primary_contact) {
                    $contacts_dropdown[$contact->id] = $contact->first_name . " " . $contact->last_name;
                }

                $cc_contacts_dropdown[] = array("id" => $contact->id, "text" => $contact->first_name . " " . $contact->last_name. " - ". get_vendor_company_name($contact->vendor_id));
            }

            $view_data['contacts_dropdown'] = $contacts_dropdown;
            $view_data['cc_contacts_dropdown'] = $cc_contacts_dropdown;

            $template_data = $this->get_send_pr_template($pr_id, 0, "", $pr_info, $primary_contact_info);
            $view_data['message'] = get_array_value($template_data, "message");
            $view_data['subject'] = get_array_value($template_data, "subject");

            return $this->template->view('Purchase\Views\purchase_request\send_pr_modal_form', $view_data);
        } else {
            show_404();
        }
    }


    /**
     * Gets the send pr template.
     *
     * @param      int     $pr_id         The pr identifier
     * @param      int     $contact_id    The contact identifier
     * @param      string  $return_type   The return type
     * @param      string  $pr_info       The pr information
     * @param      string  $contact_info  The contact information
     *
     * @return       The send po template.
     */
    public function get_send_pr_template($pr_id = 0, $contact_id = 0, $return_type = "", $pr_info = "", $contact_info = "") {

        validate_numeric_value($pr_id);
        validate_numeric_value($contact_id);

        if (!$pr_info) {
            $options = array("id" => $pr_id);
            $pr_info = $this->Purchase_model->get_estimate($pr_id);
        }

        if (!$contact_info) {
            $contact_info = $this->Users_model->get_one($contact_id);
        }

        $email_template = $this->Email_templates_model->get_final_template("purchase_request_to_contact");

        $parser_data['PR_NUMBER'] = $pr_info->pur_rq_code;
        $parser_data['PR_NAME'] = $pr_info->pur_rq_name;
        $parser_data['PR_TAX_VALUE'] =  to_currency($pr_info->total_tax, $pr_info->currency);
        $parser_data['PR_SUB_TOTAL'] =  to_currency($pr_info->subtotal, $pr_info->currency);
        $parser_data['PR_VALUE'] =  to_currency($pr_info->total, $pr_info->currency);
        $parser_data['PR_LINK'] = get_uri('purchase/view_pur_request/'.$pr_id);
        $parser_data['CONTACT_NAME'] =  $contact_info->first_name . " " . $contact_info->last_name;

        $message = $this->parser->setData($parser_data)->renderString($email_template->message);
        $message = htmlspecialchars_decode($message);
        $subject = $email_template->subject;

        if ($return_type == "json") {
            echo json_encode(array("success" => true, "message_view" => $message));
        } else {
            return array(
                "message" => $message,
                "subject" => $subject
            );
        }
    }

     /**
     * Sends an invoice.
     */
    public function send_pur_request() {


        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $pr_id = $this->request->getPost('id');

        $contact_id = $this->request->getPost('contact_id');

        $cc_array = array();
        $cc = $this->request->getPost('pq_cc');

        if ($cc) {
            $cc = explode(',', $cc);

            foreach ($cc as $cc_value) {
                if (is_numeric($cc_value)) {
                    //selected a client contact
                    array_push($cc_array, $this->Users_model->get_one($cc_value)->email);
                } else {
                    //inputted an email address
                    array_push($cc_array, $cc_value);
                }
            }
        }

        $custom_bcc = $this->request->getPost('pq_bcc');
        $subject = $this->request->getPost('subject');
        $message = decode_ajax_post_data($this->request->getPost('message'));

        $contact = $this->Users_model->get_one($contact_id);

    
        $attachement_url = $this->pur_request_pdf($pr_id, "send_email");

        $default_bcc = get_setting('send_bcc_to'); //get default settings
        $bcc_emails = "";

        if ($default_bcc && $custom_bcc) {
            $bcc_emails = $default_bcc . "," . $custom_bcc;
        } else if ($default_bcc) {
            $bcc_emails = $default_bcc;
        } else if ($custom_bcc) {
            $bcc_emails = $custom_bcc;
        }

        //add uploaded files
        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "purchase_request");
        $attachments = prepare_attachment_of_files(get_setting("timeline_file_path"), $files_data);

        //add invoice pdf
        array_unshift($attachments, array("file_path" => $attachement_url));

        if (send_app_mail($contact->email, $subject, $message, array("attachments" => $attachments, "cc" => $cc_array, "bcc" => $bcc_emails))) {
            
            // delete the temp invoice
            if (file_exists($attachement_url)) {
                unlink($attachement_url);
            }

            //delete attachments
            if ($files_data) {
                $files = unserialize($files_data);
                foreach ($files as $file) {
                    delete_app_files($target_path, array($file));
                }
            }

            echo json_encode(array('success' => true, 'message' => app_lang("pr_sent_message"), "pr_id" => $pr_id));
        } else {
            echo json_encode(array('success' => false, 'message' => app_lang('error_occurred')));
        }
    }
}
