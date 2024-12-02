<?php

namespace Purchase\Models;

use App\Models\Crud_model;

/**
 * This class describes a purchase model.
 */
class Purchase_model extends Crud_model {

    private $shipping_fields = ['shipping_street', 'shipping_city', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country'];

    private $contact_columns;

    function __construct() {
       

        parent::__construct();

        $this->contact_columns =  ['firstname', 'lastname', 'email', 'phonenumber', 'title', 'password', 'send_set_password_email', 'donotsendwelcomeemail', 'permissions', 'direction', 'invoice_emails', 'estimate_emails', 'credit_note_emails', 'contract_emails', 'task_emails', 'project_emails', 'ticket_emails', 'is_primary'];
    }

    /**
     * Gets the vendor table.
     *
     * @param      array   $options  The options
     *
     * @return       The vendor table.
     */
    function get_vendor_table($options = array()){
    	$vendor_table = $this->db->prefixTable('pur_vendor');
    	$sql = "SELECT $vendor_table.* FROM $vendor_table";

    	return $this->db->query($sql);
    }

    /**
     * get unit type
     * @param  boolean $id
     * @return array or object
     */
    public function get_unit_type($id = false)
    {

        if (is_numeric($id)) {
            $builder = $this->db->table(get_db_prefix().'ware_unit_type');
            $builder->where('unit_type_id', $id);

            return $builder->get()->getRow();
        }
        if ($id == false) {
            return $this->db->query('select * from '.get_db_prefix().'ware_unit_type')->getResultArray();
        }

    }

    /**
     * Gets the unit table.
     *
     * @param      array  $options  The options
     */
    public function get_unit_table($options = array()){
        return $this->db->query('select * from '.get_db_prefix().'ware_unit_type');
    }

    /**
     * Adds an unit.
     */
    public function add_unit($data){
        $builder = $this->db->table(get_db_prefix().'ware_unit_type');

        if(isset($data['display'])){
            $data['display'] = 1;
        }

        $builder->insert($data);
        $insert_id = $this->db->insertID();
        if($insert_id){
            return $insert_id;
        }
        return false;
    }


    /**
     * Adds an unit.
     */
    public function update_unit($data){
        $builder = $this->db->table(get_db_prefix().'ware_unit_type');

        if(isset($data['display'])){
            $data['display'] = 1;
        }

        $builder->where('unit_type_id', $data['unit_type_id']);
        $builder->update($data);
        if($this->db->affectedRows() > 0){
            return true;
        }
        return false;
    }

    /**
     * { delete unit }
     *
     * @param        $id     The identifier
     */
    public function delete_unit($id){
        $builder = $this->db->table(get_db_prefix().'ware_unit_type');

        $builder->where('unit_type_id', $id);
        $builder->delete();
        if($this->db->affectedRows() > 0){
            return true;
        }
        return false;
    }

    /**
     * Gets the item categories table.
     */
    public function get_item_categories_table($option = array()){
        return $this->db->query('select * from '.get_db_prefix().'item_categories');
    }

     /**
     * get commodity group type
     * @param  boolean $id
     * @return array or object
     */
    public function get_commodity_group($id = false)
    {

        if (is_numeric($id)) {
            $builder = $this->db->table(get_db_prefix().'item_categories');
            $builder->where('id', $id);

            return $builder->get()->getRow();
        }
        if ($id == false) {
            return $this->db->query('select * from '.get_db_prefix().'item_categories')->getResult();
        }

    }


    /**
     * Adds an commodity group.
     */
    public function add_commodity_group($data){
        $builder = $this->db->table(get_db_prefix().'item_categories');

        if(isset($data['display'])){
            $data['display'] = 1;
        }

        $builder->insert($data);
        $insert_id = $this->db->insertID();
        if($insert_id){
            return $insert_id;
        }
        return false;
    }

    /**
     * Adds an commodity group.
     */
    public function update_commodity_group($data){
        $builder = $this->db->table(get_db_prefix().'item_categories');

        if(isset($data['display'])){
            $data['display'] = 1;
        }

        $builder->where('id', $data['id']);
        $builder->update($data);
        if($this->db->affectedRows() > 0){
            return true;
        }
        return false;
    }

    /**
     * { delete commodity group }
     *
     * @param        $id     The identifier
     */
    public function delete_commodity_group($id){
        $builder = $this->db->table(get_db_prefix().'item_categories');

        $builder->where('id', $id);
        $builder->delete();
        if($this->db->affectedRows() > 0){
            return true;
        }
        return false;
    }

    /**
     * Gets the sub group table.
     */
    public function get_subgroup_table($option = array()){
        return $this->db->query('select * from '.get_db_prefix().'wh_sub_group');
    }

    /**
     * get unit type
     * @param  boolean $id
     * @return array or object
     */
    public function get_sub_group($id = false)
    {

        if (is_numeric($id)) {
            $builder = $this->db->table(get_db_prefix().'wh_sub_group');
            $builder->where('id', $id);

            return $builder->get()->getRow();
        }
        if ($id == false) {
            return $this->db->query('select * from '.get_db_prefix().'wh_sub_group')->getResultArray();
        }

    }


    /**
     * Adds an sub group.
     */
    public function add_sub_group($data){
        $builder = $this->db->table(get_db_prefix().'wh_sub_group');

        if(isset($data['display'])){
            $data['display'] = 1;
        }

        $builder->insert($data);
        $insert_id = $this->db->insertID();
        if($insert_id){
            return $insert_id;
        }
        return false;
    }

    /**
     * Update an sub group.
     */
    public function update_sub_group($data){
        $builder = $this->db->table(get_db_prefix().'wh_sub_group');

        if(isset($data['display'])){
            $data['display'] = 1;
        }

        $builder->where('id', $data['id']);
        $builder->update($data);
        if($this->db->affectedRows() > 0){
            return true;
        }
        return false;
    }

    /**
     * { delete sub group }
     *
     * @param        $id     The identifier
     */
    public function delete_sub_group($id){
        $builder = $this->db->table(get_db_prefix().'wh_sub_group');

        $builder->where('id', $id);
        $builder->delete();
        if($this->db->affectedRows() > 0){
            return true;
        }
        return false;
    }

    /**
     * { update po setting }
     */
    public function update_po_setting($data){
        $setting = $this->db->table(get_db_prefix().'settings');

        $rs = 0;
        foreach($data as $key => $value){
            $setting->where('setting_name', $key);
            $setting->update(['setting_value' => $value ]);
            if($this->db->affectedRows() > 0){
                $rs++;
            }
        }

        if($rs > 0){
            return true;
        }
        return false;
    }

    /**
     * Gets the vendor category table.
     *
     * @param      array  $option  The option
     */
    public function get_vendor_category_table($option = array()){
        return $this->db->query('select * from '.get_db_prefix().'pur_vendor_cate');
    }

    /**
     * get unit type
     * @param  boolean $id
     * @return array or object
     */
    public function get_vendor_category($id = false)
    {

        if (is_numeric($id)) {
            $builder = $this->db->table(get_db_prefix().'pur_vendor_cate');
            $builder->where('id', $id);

            return $builder->get()->getRow();
        }
        if ($id == false) {
            return $this->db->query('select * from '.get_db_prefix().'pur_vendor_cate')->getResult();
        }

    }

    /**
     * Adds a vendor category.
     *
     * @return     bool  
     */
    public function add_vendor_category($data){

        $builder = $this->db->table(get_db_prefix().'pur_vendor_cate');

        $builder->insert($data);
        $insert_id = $this->db->insertID();
        if($insert_id){
            return $insert_id;
        }
        return false;
    }

    /**
     * Update an sub group.
     */
    public function update_vendor_category($data){
        $builder = $this->db->table(get_db_prefix().'pur_vendor_cate');

        $builder->where('id', $data['id']);
        $builder->update($data);
        if($this->db->affectedRows() > 0){
            return true;
        }
        return false;
    }

    /**
     * { delete sub group }
     *
     * @param        $id     The identifier
     */
    public function delete_vendor_category($id){
        $builder = $this->db->table(get_db_prefix().'pur_vendor_cate');

        $builder->where('id', $id);
        $builder->delete();
        if($this->db->affectedRows() > 0){
            return true;
        }
        return false;
    }

    /**
     * { update purchase setting }
     *
     * @param        $data   The data
     */
    public function update_purchase_setting($data){

        $val = $data['input_name_status'] == 'true' ? 1 : 0;
        $builder = $this->db->table(get_db_prefix().'settings');

        $builder->where('setting_name', $data['input_name']);
        $builder->update(['setting_value' => $val]);
        if($this->db->affectedRows() > 0){
            return true;
        }
        return false;

    }

    /**
     * get approval setting
     * @param  boolean $id
     * @return array or object
     */
    public function get_approval_setting($id = '') {
        if (is_numeric($id)) {
            $builder = $this->db->table(get_db_prefix().'pur_approval_setting');
            $builder->where('id', $id);
            return $builder->get()->getRow();
        }
        $builder = $this->db->table(get_db_prefix().'pur_approval_setting');
        return $builder->get()->getResultArray();
    }

    /**
     * Creates an approval setting row template.
     *
     * @param      array   $staff_data  The staff data
     * @param      string  $name        The name
     * @param      string  $approver    The approver
     * @param      string  $staff       The staff
     * @param      string  $action      The action
     * @param      string  $item_key    The item key
     *
     * @return     string  
     */
    public function create_approval_setting_row_template($staff_data = [], $name = '', $approver = 'staff', $staff = '', $action = '', $item_key = '') {
        
        $row = '';

        $name_staff = 'staff';
        $name_action = 'action';
        $name_approver = 'approver';

        if ($name == '') {

            $row .= '<div class="row main"><tr class="main hide">
                  <td></td>';

        } else {
            $row .= '<div class="row item"><tr class="sortable item">
                    <td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';

            $name_staff = $name . '[staff]';
            $name_action = $name . '[action]';
            $name_approver = $name . '[approver]';
        }

        $action_data = [];
        $action_data[] = [
            "name" => "approve",
            "label" => app_lang("approve"),
        ];
        $action_data[] = [
            "name" => "sign",
            "label" => app_lang("sign"),
        ];

        $approver_data = [];
        $approver_data[] = [
            "name" => "direct_manager",
            "label" => app_lang("direct_manager"),
        ];
        $approver_data[] = [
            "name" => "department_manager",
            "label" => app_lang("department_manager"),
        ];
        $approver_data[] = [
            "name" => "staff",
            "label" => app_lang("staff"),
        ];

        $row .= '<div class="col-md-4 d-none"><td class="approver d-none">' .
        render_select1($name_approver, $approver_data,array('name', array('label')),'',$approver,[], ["data-none-selected-text" => app_lang('approver_name')], 'no-margin').
        '</td></div>';

        $row .= '<div class="col-md-7"><td class="staff">' .
        render_select1($name_staff, $staff_data,array('id', array('first_name', 'last_name')),'staff_name',$staff,['data-none-selected-text' => 'asasa'], ["data-none-selected-text" => app_lang('staff_name')], 'no-margin').
        '</td></div>';
        

        $row .= '<div class="col-md-4"><td class="action">' .
        render_select1($name_action, $action_data,array('name', 'label'),'wh_action',$action,[], ["data-none-selected-text" => app_lang('wh_action')], 'no-margin').
        '</td></div>';


        if ($name == '') {

            $row .= '<div class="col-md-1 new_vendor_requests_button">
            <div class="float-start mt25">
            <a href="#" class="btn btn-info text-white new_wh_approval btn-success" onclick="wh_add_item_to_table(\'undefined\',\'undefined\'); return false;" title="Add item" name="add" data-title="Add item" ><span data-feather="plus-circle" class="icon-16"></span></a>
            </div>
            </div></div>';

        } else {
            $row .= '<div class="col-md-1 new_vendor_requests_button">
            <div class="float-start mt25">
            <a href="#" class="btn btn-info text-white new_wh_approval btn-danger" onclick="wh_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;" title="Add item" name="add" data-title="Add item" ><span data-feather="x" class="icon-16"></span></a>
            </div>
            </div></div>';

        }
        $row .= '</tr>';
        return $row;
    }

     /**
     * add approval setting
     * @param  array $data
     * @return boolean
     */
    public function add_approval_setting($data) {
        $insert_data = [];
        $insert_data['name'] = $data['name'];
        $insert_data['related'] = $data['related'];
        $setting = [];

        if(isset($data['newitems'])){
            foreach ($data['newitems'] as $key => $value) {
                if(is_numeric($value['staff']) && strlen($value['action']) > 0){

                    $setting[] = [
                        'approver' => $value['approver'],
                        'staff' => $value['staff'],
                        'action' => $value['action'],
                    ];
                }
            }
        }
        if(count($setting) > 0){
            $insert_data['setting'] = json_encode($setting);
        }
        $builder = $this->db->table(get_db_prefix().'pur_approval_setting');
        $builder->insert($insert_data);
        $insert_id = $this->db->insertID();
        if ($insert_id) {
            return $insert_id;
        }
        return false;
    }

    /**
     * edit approval setting
     * @param  integer $id
     * @param   array $data
     * @return    boolean
     */
    public function edit_approval_setting($id, $data) {
        $update_data = [];
        $update_data['name'] = $data['name'];
        $update_data['related'] = $data['related'];
        $setting = [];

        if(isset($data['newitems'])){
            foreach ($data['newitems'] as $key => $value) {
                if(is_numeric($value['staff']) && strlen($value['action']) > 0){

                    $setting[] = [
                        'approver' => $value['approver'],
                        'staff' => $value['staff'],
                        'action' => $value['action'],
                    ];
                }
            }
        }
        if(count($setting) > 0){
            $update_data['setting'] = json_encode($setting);
        }else{
            $update_data['setting'] = '';
        }

        $builder = $this->db->table(get_db_prefix().'pur_approval_setting');
        $builder->where('id', $id);
        $affected_rows = $builder->update($update_data);

        if ($affected_rows > 0) {
            return true;
        }
        return false;
    }

    /**
     * delete approval setting
     * @param  integer $id
     * @return boolean
     */
    public function delete_approval_setting($id) {
        if (is_numeric($id)) {
            $builder = $this->db->table(get_db_prefix().'pur_approval_setting');
            $builder->where('id', $id);
            $affected_rows = $builder->delete();

            if ($affected_rows > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * get unit add item 
     * @return array
     */
    public function get_unit_add_item()
    {
        return $this->db->query('select * from '.get_db_prefix().'ware_unit_type where display = 1 order by '.get_db_prefix().'ware_unit_type.order asc ')->getResultArray();
    }

     /**
     * get commodity group add commodity
     * @return array
     */
    public function get_commodity_group_add_commodity()
    {

        return $this->db->query('select * from '.get_db_prefix().'item_categories where display = 1 order by '.get_db_prefix().'item_categories.order asc ')->getResultArray();
    }

    /**
     * Function that will parse table data from the tables folder for amin area
     * @param  string $table  table filename
     * @param  array  $params additional params
     * @return void
     */
    public function get_table_data($table, $dataPost, $params = [])
    {

        $params = app_hooks()->apply_filters('table_params', $params, $table);

        foreach ($params as $key => $val) {
            $$key = $val;
        }

        $customFieldsColumns = [];

        $path = VIEWPATH . 'admin/tables/' . $table . EXT;


        if (!file_exists($path)) {
            $path = $table;

            if (!endsWith($path, EXT)) {
                $path .= EXT;
            }
        } else {
            $myPrefixedPath = VIEWPATH . 'admin/tables/my_' . $table . EXT;
            if (file_exists($myPrefixedPath)) {
                $path = $myPrefixedPath;
            }
        }

        include_once($path);

        echo json_encode($output);
        die;
    }

    /**
     * generate commodity barcode
     *
     * @return     string
     */
    public function generate_commodity_barcode() {
        $item = false;
        do {
            $length = 11;
            $chars = '0123456789';
            $count = mb_strlen($chars);
            $password = '';
            for ($i = 0; $i < $length; $i++) {
                $index = rand(0, $count - 1);
                $password .= mb_substr($chars, $index, 1);
            }
            $commodity_barcode = $this->db->table(get_db_prefix().'items');
            $commodity_barcode->where('commodity_barcode', $password);
            $item = $commodity_barcode->get()->getRow();
        } while ($item);

        return $password;
    }

    /**
     * get commodity
     * @param  boolean $id
     * @return array or object
     */
    public function get_commodity($id = false) {

        if (is_numeric($id)) {
            $builder = $this->db->table(get_db_prefix().'items');
            $builder->where('id', $id);

            return $builder->get()->getRow();
        }
        if ($id == false) {
            return $this->db->query('select * from '.get_db_prefix().'items')->getResultArray();
        }

    }

    /**
     * add commodity one item
     * @param array $data
     * @return integer
     */
    public function add_commodity_one_item($data) {
        $arr_insert_cf=[];
 
        $arr_custom_fields=[];


        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }


        $data['rate'] = $data['rate'];

        if(isset($data['purchase_price']) && $data['purchase_price']){
            
            $data['purchase_price'] = $data['purchase_price'];
        }
        /*create sku code*/
        if($data['sku_code'] != ''){
            $data['sku_code'] = str_replace(' ', '', $data['sku_code']) ;

        }else{

            $data['sku_code'] = $this->create_sku_code($data['category_id'], isset($data['sub_group']) ? $data['sub_group'] : '' );
            /*create sku code*/
        }

        if(isset($data['can_be_sold']) && $data['can_be_sold'] == 'can_be_sold'){
            $data['can_be_sold'] = 'can_be_sold';
        }else{
            $data['can_be_sold'] = '';
        }
        if(isset($data['can_be_inventory']) && $data['can_be_inventory'] == 'can_be_inventory'){
            $data['can_be_inventory'] = 'can_be_inventory';
        }else{
            $data['can_be_inventory'] = '';
        }
       
        $data['can_be_purchased'] = 'can_be_purchased';
        
        if(isset($data['can_be_manufacturing']) && $data['can_be_manufacturing'] == 'can_be_manufacturing'){
            $data['can_be_manufacturing'] = 'can_be_manufacturing';
        }else{
            $data['can_be_manufacturing'] = '';
        }

        $tags = '';
        if (isset($data['tags'])) {
            $tags = $data['tags'];
            unset($data['tags']);
        }

        //update column unit name use sales/items
        $unit_type = get_unit_type($data['unit_id']);
        if(isset($unit_type->unit_name)){
            $data['unit_type'] = $unit_type->unit_name;
        }

        if(isset($data['name'])){
            unset($data['name']);
        }
        if(isset($data['options'])){
            unset($data['options']);
        }

        if(isset($data['file_names'])){
            unset($data['file_names']);
        }
        if(isset($data['file_sizes'])){
            unset($data['file_sizes']);
        }

        $builder = $this->db->table(get_db_prefix().'items');
        $builder->insert($data);
        $insert_id = $this->db->insertID();

   
        if ($insert_id) {
            app_hooks()->do_action('item_created', $insert_id);

            return $insert_id;

        }

        return false;

    }


    /**
     * create sku code
     * @param  int commodity_group
     * @param  int sub_group
     * @return string
     */
    public function create_sku_code($commodity_group, $sub_group, $flag_insert_id = false) {
        // input  commodity group, sub group
        //get commodity group from id
        $group_character = '';
        if (isset($commodity_group) && $commodity_group != '') {

            $sql_group_where = 'SELECT * FROM ' .get_db_prefix(). 'item_categories where id = "' . $commodity_group . '"';
            $group_value = $this->db->query($sql_group_where)->getRow();
            if ($group_value) {

                if ($group_value->commodity_group_code != '') {
                    $group_character = mb_substr($group_value->commodity_group_code, 0, 1, "UTF-8") . '-';

                }
            }

        }

        //get sku code from sku id
        $sub_code = '';
        if (isset($sub_group) && $sub_group != '') {

            $sql_sub_group_where = 'SELECT * FROM ' .get_db_prefix(). 'wh_sub_group where id = "' . $sub_group . '"';
            $sub_group_value = $this->db->query($sql_sub_group_where)->getRow();
            if ($sub_group_value) {
                $sub_code = $sub_group_value->sub_group_code . '-';
            }

        }

        if($flag_insert_id != 0 && $flag_insert_id != false){
            $last_commodity_id = $flag_insert_id;
        }else{

            $sql_where = 'SELECT * FROM ' .get_db_prefix(). 'items order by id desc limit 1';
            $res = $this->db->query($sql_where)->getRow();
            $last_commodity_id = 0;
            if (isset($res)) {
                $last_commodity_id = $this->db->query($sql_where)->getRow()->id;
            }
        }

        $next_commodity_id = (int) $last_commodity_id + 1;


        return $group_character . $sub_code .str_pad($next_commodity_id,5,'0',STR_PAD_LEFT); // X_X_000.id auto increment
    }

    /**
     * update commodity one item
     * @param  array $data
     * @param  integer $id
     * @return boolean
     */
    public function update_commodity_one_item($data, $id) {

        $arr_insert_cf=[];
 
        $arr_custom_fields=[];


        /*handle custom fields*/

        if(isset($formdata)){
            $data_insert_cf = [];
            handle_custom_fields_post($id, $arr_insert_cf, true);
        }

        
        $data['sku_code'] = str_replace(' ', '', $data['sku_code']) ;

        $data['rate'] = $data['rate'];
        $data['purchase_price'] = $data['purchase_price'];

        //update column unit name use sales/items
        $unit_type = get_unit_type($data['unit_id']);
        if(isset($unit_type->unit_name)){
            $data['unit_type'] = $unit_type->unit_name;
        }

        if(isset($data['name'])){
            unset($data['name']);
        }
        if(isset($data['options'])){
            unset($data['options']);
        }

        if(isset($data['file_names'])){
            unset($data['file_names']);
        }
        if(isset($data['file_sizes'])){
            unset($data['file_sizes']);
        }
        if(isset($data['delete_file'])){
            unset($data['delete_file']);
        }

        if(isset($data['can_be_sold']) && $data['can_be_sold'] == 'can_be_sold'){
            $data['can_be_sold'] = 'can_be_sold';
        }else{
            $data['can_be_sold'] = '';
        }
        if(isset($data['can_be_inventory']) && $data['can_be_inventory'] == 'can_be_inventory'){
            $data['can_be_inventory'] = 'can_be_inventory';
        }else{
            $data['can_be_inventory'] = '';
        }
        if(isset($data['can_be_purchased']) && $data['can_be_purchased'] == 'can_be_purchased'){
            $data['can_be_purchased'] = 'can_be_purchased';
        }else{
            $data['can_be_purchased'] = '';
        }
        if(isset($data['can_be_manufacturing']) && $data['can_be_manufacturing'] == 'can_be_manufacturing'){
            $data['can_be_manufacturing'] = 'can_be_manufacturing';
        }else{
            $data['can_be_manufacturing'] = '';
        }

        $builder = $this->db->table(get_db_prefix().'items');
        $builder->where('id', $id);
        $builder->update($data);

        //update commodity min
        $data_inventory_min=[];
        $data_inventory_min['commodity_code'] = $data['commodity_code'];
        $data_inventory_min['commodity_name'] = $data['description'];

        return true;
    }

    /**
     * delete commodity
     * @param  integer $id
     * @return boolean
     */
    public function delete_commodity($id) {

        //check child item before delete
        $builder = $this->db->table(get_db_prefix() . 'items');
        $builder->where('parent_id', $id);
        $items = $builder->get()->getResultArray();
        if (count($items) > 0) {
            return [
                'referenced' => true,
            ];
        }

        app_hooks()->do_action('delete_item_on_woocommerce', $id);
        
        /*delete commodity min*/
        $builder = $this->db->table(get_db_prefix() . 'inventory_commodity_min');
        $builder->where('commodity_id', $id);
        $builder->delete();
        
        $builder = $this->db->table(get_db_prefix() . 'items');
        $builder->where('id', $id);
        $affected_rows = $builder->delete();

        if ($affected_rows > 0) {

            return true;
        }
        return false;

    }

    /**
     * { clone_item }
     */
    public function clone_item($id){
        $current_items = $this->get_commodity($id);
        $item_attachments = $this->get_item_attachments($id);
        if($current_items){
            $item_data['description'] = $current_items->description;
            $item_data['purchase_price'] = $current_items->purchase_price;
            $item_data['unit_id'] = $current_items->unit_id;
            $item_data['rate'] = $current_items->rate;
            $item_data['sku_code'] = '';
            $item_data['commodity_barcode'] = $this->generate_commodity_barcode();
            $item_data['commodity_code'] = $this->generate_commodity_barcode();
            $item_data['category_id'] = $current_items->category_id;
            $item_data['sub_group'] = $current_items->sub_group;
            $item_data['tax'] = $current_items->tax;
            $item_data['commodity_type'] = $current_items->commodity_type;
            $item_data['warehouse_id'] = $current_items->warehouse_id;
            $item_data['profif_ratio'] = $current_items->profif_ratio;
            $item_data['origin'] = $current_items->origin;
            $item_data['style_id'] = $current_items->style_id;
            $item_data['model_id'] = $current_items->model_id;
            $item_data['size_id'] = $current_items->size_id;
            $item_data['color'] = $current_items->color;
            $item_data['guarantee'] = $current_items->guarantee;
            $item_data['without_checking_warehouse'] = $current_items->without_checking_warehouse;
            $item_data['long_description'] = $current_items->long_description;
            $item_id = $this->add_commodity_one_item_clone($item_data);
            if($item_id){
                if(count($item_attachments) > 0){
                    $source = WAREHOUSE_MODULE_UPLOAD_FOLDER.'/item_img/'.$id;
                    if(!is_dir($source)){
                        if(get_status_modules_wh('purchase')){
                            $source = PURCHASE_MODULE_UPLOAD_FOLDER.'/item_img/'.$id;
                        }
                    }
                    $destination = WAREHOUSE_MODULE_UPLOAD_FOLDER.'/item_img/'.$item_id;
                    if(xcopy($source, $destination)){
                        foreach($item_attachments as $attachment){
                        
                            $attachment_db   = [];
                            $attachment_db[] = [
                                'file_name' => $attachment['file_name'],
                                'filetype'  => $attachment['filetype'],
                                ];

                            $this->misc_model->add_attachment_to_database($item_id, 'commodity_item_file', $attachment_db);
                        }
                    }
                }

                $this->db->where('relid', $current_items->id);
                $this->db->where('fieldto', 'items_pr');
                $customfields = $this->db->get(get_db_prefix() .'customfieldsvalues')->get()->getResultArray();
                if(count($customfields) > 0){
                    foreach($customfields as $cf){
                        $this->db->insert(get_db_prefix() .'customfieldsvalues', [
                            'relid' => $item_id,
                            'fieldid' => $cf['fieldid'],
                            'fieldto' => $cf['fieldto'],
                            'value' => $cf['value']
                        ]);
                    }
                }

                $this->db->where('rel_id', $current_items->id);
                $this->db->where('rel_type', 'item_tags');
                $tags = $this->db->get(get_db_prefix() .'taggables')->get()->getResultArray();
                if(count($tags) > 0){
                    foreach($tags as $tag){
                        $this->db->insert(get_db_prefix() .'taggables', [
                            'rel_id' => $item_id,
                            'rel_type' => $tag['rel_type'],
                            'tag_id' => $tag['tag_id'],
                            'tag_order' => $tag['tag_order']
                        ]);

                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * get warehourse attachments
     * @param  integer $commodity_id 
     * @return array               
     */
    public function get_item_attachments($commodity_id){

        $this->db->order_by('dateadded', 'desc');
        $this->db->where('rel_id', $commodity_id);
        $this->db->where('rel_type', 'commodity_item_file');

        return $this->db->get(get_db_prefix() . 'files')->get()->getResultArray();

    }


    /**
     * add commodity one item
     * @param array $data
     * @return integer
     */
    public function add_commodity_one_item_clone($data) {
        

        /*add data '.get_db_prefix().'item*/
        $data['rate'] = $data['rate'];

        if(isset($data['purchase_price']) && $data['purchase_price']){
            
            $data['purchase_price'] = $data['purchase_price'];
        }
        /*create sku code*/
        if($data['sku_code'] != ''){
            $data['sku_code'] = str_replace(' ', '', $data['sku_code']) ;

        }else{

            $data['sku_code'] = $this->create_sku_code($data['category_id'], isset($data['sub_group']) ? $data['sub_group'] : '' );
            /*create sku code*/
        }

      


        $this->db->insert(get_db_prefix() . 'items', $data);
        $insert_id = $this->db->insert_id();

        /*add data '.get_db_prefix().'inventory*/
        if ($insert_id) {
            

            /*habdle add tags*/
            handle_tags_save($tags, $insert_id, 'item_tags');


            /*handle custom fields*/

            app_hooks()->do_action('item_created', $insert_id);


        }

        return $insert_id;

    }

    /**
     * commodity udpate profit rate
     * @param  [type] $id      
     * @param  [type] $percent 
     * @param  [type] $type    
     * @return [type]          
     */
    public function commodity_udpate_profit_rate($id, $percent, $type)
    {
        $integer_part = get_setting('warehouse_integer_part');

        $affected_rows=0;
        $item = $this->get_commodity($id);
        $profit_rate=0;

        if($item){
            $selling_price = (float)$item->rate;
            $purchase_price = (float)$item->purchase_price;

            if($type == 'selling_percent'){
                //selling_percent
                $new_selling_price = $selling_price + $selling_price*(float)$percent/100;

                $profit_rate = $this->caculator_profit_rate_model($purchase_price, $new_selling_price);

                $builder = $this->db->table(get_db_prefix().'items');
                $builder->where('id', $id);
                $affected_rows = $builder->update(['rate' => $new_selling_price, 'profif_ratio' => $profit_rate]);
                if ($affected_rows > 0) {
                    $affected_rows++;
                }

            }else{
                //purchase_percent
                $new_purchase_price = $purchase_price + $purchase_price*(float)$percent/100;
        

                $profit_rate = $this->caculator_profit_rate_model($new_purchase_price, $selling_price);

                $builder = $this->db->table(get_db_prefix().'items');
                $builder->where('id', $id);
                $affected_rows = $builder->update(['purchase_price' => $new_purchase_price, 'profif_ratio' => $profit_rate]);
                if ($affected_rows > 0) {
                    $affected_rows++;
                }

            }

        }

        if($affected_rows > 0){
            return true;
        }
        return false;

    }

    /**
     * caculator purchase price
     * @return json 
     */
    public function caculator_profit_rate_model($purchase_price, $sale_price)
    {

        $profit_rate = 0;

        /*type : 0 purchase price, 1: sale price*/
        $profit_type = get_setting('profit_rate_by_purchase_price_sale');
        $the_fractional_part = get_setting('warehouse_the_fractional_part');
        $integer_part = get_setting('warehouse_integer_part');

        $purchase_price = $purchase_price;
        $sale_price = $sale_price;


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
        return $profit_rate;

    }

    /**
     * Adds a vendor.
     *
     * @param      <type>   $data       The data
     * @param      integer  $client_id  The client identifier
     *
     * @return     integer  ( id vendor )
     */
    public function add_vendor($data, $client_id = null,$client_or_lead_convert_request = false)
    {
        $contact_data = [];
        foreach ($this->contact_columns as $field) {
            if (isset($data[$field])) {
                $contact_data[$field] = $data[$field];
                // Phonenumber is also used for the company profile
                if ($field != 'phonenumber') {
                    unset($data[$field]);
                }
            }
        }
        // From customer profile register
        if (isset($data['contact_phonenumber'])) {
            $contact_data['phonenumber'] = $data['contact_phonenumber'];
            unset($data['contact_phonenumber']);
        }

        if (isset($data['is_primary'])) {
            $contact_data['is_primary'] = $data['is_primary'];
            unset($data['is_primary']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        if(isset($data['category']) && count($data['category']) > 0){
            $data['category'] = implode(',', $data['category']);
        }

        if (isset($data['groups_in'])) {
            $groups_in = $data['groups_in'];
            unset($data['groups_in']);
        }

        $data = $this->check_zero_columns($data);

        $data['datecreated'] = date('Y-m-d H:i:s');


        $users_model = model("App\Models\Users_model", false);
        $created_by = $users_model->login_user_id();
       
        $data['addedfrom'] = $created_by;

        // New filter action

        $builder = $this->db->table(get_db_prefix().'pur_vendor');

        if(isset($client_id) && $client_id > 0){
            $userid = $client_id;
        } else {
            $builder->insert($data);
            $userid = $this->db->insertID();  
        }
        
        if ($userid) {
            /**
             * Used in Import, Lead Convert, Register
             */
            if ($client_or_lead_convert_request == true) {
                $contact_id = $this->add_contact($contact_data, $userid, $client_or_lead_convert_request);
            }
            
            /**
             * Used in Import, Lead Convert, Register
             */        


         
        }

        return $userid;
    }

    /**
     * { check zero columns }
     *
     * @param      <type>  $data   The data
     *
     * @return     array  
     */
    private function check_zero_columns($data)
    {
        if (!isset($data['show_primary_contact'])) {
            $data['show_primary_contact'] = 0;
        }

        if (isset($data['default_currency']) && $data['default_currency'] == '' || !isset($data['default_currency'])) {
            $data['default_currency'] = 0;
        }

        if (isset($data['country']) && $data['country'] == '' || !isset($data['country'])) {
            $data['country'] = 0;
        }

        if (isset($data['billing_country']) && $data['billing_country'] == '' || !isset($data['billing_country'])) {
            $data['billing_country'] = 0;
        }

        if (isset($data['shipping_country']) && $data['shipping_country'] == '' || !isset($data['shipping_country'])) {
            $data['shipping_country'] = 0;
        }

        return $data;
    }

    /**
     * Gets the vendor.
     *
     * @param      string        $id     The identifier
     * @param      array|string  $where  The where
     *
     * @return     <type>        The vendor or list vendors.
     */
    public function get_vendor($id)
    {

        $builder = $this->db->table(get_db_prefix().'pur_vendor');

        $builder->where('userid', $id);
        return $builder->get()->getRow();
        
    }

    /**
     * { update vendor }
     *
     * @param      <type>   $data            The data
     * @param      <type>   $id              The identifier
     * @param      boolean  $client_request  The client request
     *
     * @return     boolean 
     */
    public function update_vendor($data, $id, $client_request = false)
    {
        if (isset($data['update_all_other_transactions'])) {
            $update_all_other_transactions = true;
            unset($data['update_all_other_transactions']);
        }

        if (isset($data['update_credit_notes'])) {
            $update_credit_notes = true;
            unset($data['update_credit_notes']);
        }

        $affectedRows = 0;
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        if(isset($data['category']) && count($data['category']) > 0){
            $data['category'] = implode(',', $data['category']);
        }

        $data = $this->check_zero_columns($data);

        $builder = $this->db->table(get_db_prefix().'pur_vendor');

        $builder->where('userid', $id);
        $affected_rows = $builder->update( $data);

        if ($affected_rows) {
            $affectedRows++;
        }


        if ($affectedRows > 0) {


            return true;
        }

        return false;
    }

    /**
     * Gets the vendors.
     *
     * @return       The vendors.
     */
    public function get_vendors(){
        $builder = $this->db->table(get_db_prefix().'pur_vendor');

        return $builder->get()->getResultArray();
    }

    /**
     * wh get grouped
     * @param  string  $can_be     
     * @param  boolean $search_all 
     * @return [type]              
     */
    public function pur_get_grouped($can_be = '', $search_all = false, $vendor = '')
    {

        $items = [];

        $builder = $this->db->table(get_db_prefix().'item_categories');
        $builder->orderBy('title', 'asc');
        $groups = $builder->get()->getResultArray();

        array_unshift($groups, [
            'id'   => 0,
            'title' => '',
        ]);

        foreach ($groups as $group) {
            $it_builder = $this->db->table(get_db_prefix().'items');
            $it_builder->select('*,' . get_db_prefix() . 'item_categories.title as group_name,' . get_db_prefix() . 'items.id as id, '.get_db_prefix().'items.title as item_title');
            if(strlen($can_be) > 0){
                $it_builder->where(get_db_prefix() . 'items.'.$can_be, $can_be);
            }

            if($vendor != ''){
                $it_builder->where(db_prefix().'items.id in (SELECT items from '.db_prefix().'pur_vendor_items WHERE vendor = '.$vendor.')');
            }
            $it_builder->where('category_id', $group['id']);
            $it_builder->where(get_db_prefix().'items.deleted', 0);
            $it_builder->join(get_db_prefix() . 'item_categories', '' . get_db_prefix() . 'item_categories.id = ' . get_db_prefix() . 'items.category_id', 'left');
            $it_builder->orderBy('description', 'asc');

            $_items = $it_builder->get()->getResultArray();

            if (count($_items) > 0) {
                $items[$group['id']] = [];
                foreach ($_items as $i) {
                    array_push($items[$group['id']], $i);
                }
            }
        }

        return $items;
    }

    /**
     * Gets the item by group.
     *
     * @param        $group  The group
     *
     * @return      The item by group.
     */
    public function get_item_by_group($group){
        $builder = $this->db->table(get_db_prefix().'items');

        $builder->where('category_id',$group);
        return $builder->get()->getResultArray();
    }  

    /**
     * get commodity
     * @param  boolean $id
     * @return array or object
     */
    public function get_item($id = false)
    {

        if (is_numeric($id)) {
            $builder = $this->db->table(get_db_prefix().'items');
            $builder->where('id', $id);

            return $builder->get()->getRow();
        }
        if ($id == false) {
            return $this->db->query('select * from ' . get_db_prefix() . 'items where deleted = 0 order by id desc')->getResultArray();

        }

    }

    /**
     * Adds vendor items.
     *
     * @param      $data   The data
     *
     * @return     boolean 
     */
    public function add_vendor_items($data){
        $rs = 0;
        $users_model = model("App\Models\Users_model", false);
        $created_by = $users_model->login_user_id();

        $data['add_from'] = $created_by;

        $data['datecreate'] = date('Y-m-d');
        foreach($data['items'] as $val){
            $builder = $this->db->table(get_db_prefix().'pur_vendor_items');
            $builder->insert([
                'vendor' => $data['vendor'],
                'group_items' => $data['group_item'],
                'items' => $val,
                'add_from' => $data['add_from'],
                'datecreate' => $data['datecreate'],
            ]);
            $insert_id = $this->db->insertID();

            if($insert_id){
                $rs++;
            }
        }

        if($rs > 0){
            return true;
        }
        return false;
    } 


    /**
     * { delete vendor items }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean  
     */
    public function delete_vendor_items($id){
        $builder = $this->db->table(get_db_prefix().'pur_vendor_items');

        $builder->where('id',$id);
        $affected_rows = $builder->delete();
        if ($affected_rows > 0) {
            
            return true;
        }
        return false;
    }

    /**
     * { delete vendor }
     *
     * @param        $id     The identifier
     *
     * @return     bool    
     */
    public function delete_vendor($id){
        $builder = $this->db->table(get_db_prefix().'pur_vendor');
        $builder->where('userid',$id);
        $affected_rows = $builder->delete();
        if ($affected_rows > 0) {
            return true;
        }
        return false;
    }

    /**
     * caculator purchase price model
     * @return float 
     */
    public function caculator_purchase_price_model($profit_rate, $sale_price)
    {

        $purchase_price = 0;

        /*type : 0 purchase price, 1: sale price*/
        $profit_type = get_setting('profit_rate_by_purchase_price_sale');
        $the_fractional_part = get_setting('warehouse_the_fractional_part');
        $integer_part = get_setting('warehouse_integer_part');

        $profit_rate = reformat_currency_j($profit_rate);
        $sale_price = reformat_currency_j($sale_price);


        switch ($profit_type) {
            case '0':
                # Calculate the selling price based on the purchase price rate of profit
                # sale price = purchase price * ( 1 + profit rate)
            if( ($profit_rate =='') || ($profit_rate == '0')|| ($profit_rate == 'null') ){
                $purchase_price = (float)$sale_price;

            }else{
                $purchase_price = (float)$sale_price/(1+((float)$profit_rate/100));

            }
            break;

            case '1':
                # Calculate the selling price based on the selling price rate of profit
                # sale price = purchase price / ( 1 - profit rate)
            if( ($profit_rate =='') || ($profit_rate == '0')|| ($profit_rate == 'null') ){
                $purchase_price = (float)$sale_price;
            }else{

                $purchase_price = (float)$purchase_price*(1-((float)$profit_rate/100));

            }
            break;
            
        }

        //round purchase_price
        $purchase_price = round($purchase_price, (int)$the_fractional_part);

        if($integer_part != '0'){
            $integer_part = 0 - (int)($integer_part);
            $purchase_price = round($purchase_price, $integer_part);
        }

        return $purchase_price;
    }

    /**
     * Creates a purchase request row template.
     *
     * @param      array   $unit_data  The unit data
     * @param      string  $name       The name
     */
    public function create_purchase_request_row_template($name = '', $item_code = '', $item_text = '', $unit_price = '', $quantity = '', $unit_name = '', $into_money = '', $item_key = '', $tax_value = '', $total = '', $tax_name = '', $tax_rate = '', $tax_id = '', $is_edit = false, $currency_rate = 1, $to_currency = ''){

        $row = '';

        $name_item_code = 'item_code';
        $name_item_text = 'item_text';
        $name_unit_id = 'unit_id';
        $name_unit_name = 'unit_name';
        $name_unit_price = 'unit_price';
        $name_quantity = 'quantity';
        $name_into_money = 'into_money';
        $name_tax = 'tax';
        $name_tax_value = 'tax_value';
        $name_tax_name = 'tax_name';
        $name_tax_rate = 'tax_rate';
        $name_tax_id_select = 'tax_select';
        $name_total = 'total';

        $array_rate_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_qty_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_subtotal_attr = ['readonly' => true];

        $text_right_class = 'text-right';

        if ($name == '') {
            $tax_rate_class = ' refresh_tax1';


            $row .= '<tr class="main">
                  <td></td>';
            $vehicles = [];
            $array_attr = ['placeholder' => _l('unit_price')];

            $manual             = true;
            $invoice_item_taxes = '';
            $total = '';
            $into_money = 0;
        } else {
            $tax_rate_class = ' refresh_tax2';
            $manual             = false;
            $row .= '<tr class="sortable item">
                    <td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
            $name_item_code = $name . '[item_code]';
            $name_item_text = $name . '[item_text]';
            $name_unit_id = $name . '[unit_id]';
            $name_unit_name = $name . '[unit_name]';
            $name_unit_price = $name . '[unit_price]';
            $name_quantity = $name . '[quantity]';
            $name_into_money = $name . '[into_money]';
            $name_tax = $name .'[tax]';
            $name_tax_value = $name . '[tax_value]';
            $name_tax_name = $name . '[tax_name]';
            $name_tax_rate = $name . '[tax_rate]';
            $name_tax_id_select = $name . '[tax_select][]';
            $name_total = $name . '[total]';

            $array_rate_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('unit_price')];

            $array_qty_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantity];

            $tax_money = 0;
            $tax_rate_value = 0;

            if($is_edit){
                $invoice_item_taxes = pur_convert_item_taxes($tax_id, $tax_rate, $tax_name);
                $arr_tax_rate = explode('|', $tax_rate);
                foreach ($arr_tax_rate as $key => $value) {
                    $tax_rate_value += (float)$value;
                }
            }else{
                $invoice_item_taxes = $tax_name;
                $tax_rate_data = $this->pur_get_tax_rate($tax_name);
                $tax_rate_value = $tax_rate_data['tax_rate'];
            }

            if((float)$tax_rate_value != 0){
                $tax_money = (float)$unit_price * (float)$quantity * (float)$tax_rate_value / 100;
               
                $amount = (float)$unit_price * (float)$quantity + (float)$tax_money;
            }else{
                
                $amount = (float)$unit_price * (float)$quantity;
            }

            $into_money = (float)$unit_price * (float)$quantity;
            $total = $amount;

        }


        $row .= '<td class="">' . render_textarea1($name_item_text, '', $item_text, ['rows' => 2, 'placeholder' => _l('pur_item_name')] ) . '</td>';
        $row .= '<td class="rate">' . render_input1($name_unit_price, '', $unit_price, 'number', $array_rate_attr, [], 'no-margin', $text_right_class) ;
        if( $unit_price != ''){
            $original_price = round( ($unit_price/$currency_rate), 2);
            $base_currency = get_base_currency();
            if($to_currency != '' && $to_currency != $base_currency){
                $row .= render_input1('original_price', '',to_currency($original_price, $base_currency), 'text', ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => _l('original_price'), 'disabled' => true], [], 'no-margin', 'input-transparent text-right pur_input_none');
            }

            $row .= '<input class="hide" name="og_price" disabled="true" value="'.$original_price.'">';
        }

        $row .=  '</td>';

        $row .= '<td class="quantities">' . 
        render_input1($name_quantity, '', $quantity, 'number', $array_qty_attr, [], 'no-margin', $text_right_class) . 
        render_input1($name_unit_name, '', $unit_name, 'text', ['placeholder' => _l('unit'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right pur_input_none').
        '</td>';

        $row .= '<td class="into_money">' . render_input1($name_into_money, '', $into_money, 'number', $array_subtotal_attr, [], '', $text_right_class) . '</td>';
        $row .= '<td class="taxrate '.$tax_rate_class.'">' . $this->get_taxes_dropdown_template($name_tax_id_select, $invoice_item_taxes, 'invoice', $item_key, true, $manual) . '</td>';
        $row .= '<td class="tax_value">' . render_input1($name_tax_value, '', $tax_value, 'number', $array_subtotal_attr, [], '', $text_right_class) . '</td>';
        $row .= '<td class="hide item_code">' . render_input1($name_item_code, '', $item_code, 'text', ['placeholder' => _l('item_code')]) . '</td>';
        $row .= '<td class="_total">' . render_input1($name_total, '', $total, 'number', $array_subtotal_attr, [], '', $text_right_class) . '</td>';

        if ($name == '') {
            $row .= '<td><button type="button" onclick="pur_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info text-white"><i data-feather="plus-circle" class="icon-16"></i></button></td>';
        } else {
            $row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="pur_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i data-feather="x" class="icon-16"></i></a></td>';
        }
        $row .= '</tr>';
        return $row;
    }

    /**
     * Gets the tax name.
     *
     * @param        $tax    The tax
     *
     * @return     string  The tax name.
     */
    public function get_tax_name($tax){
        $builder = $this->db->table(get_db_prefix().'taxes');
        $builder->where('id', $tax);
        $tax_if = $builder->get()->getRow();
        if($tax_if){
            return $tax_if->title;
        }
        return '';
    }

    /**
     * { tax rate by id }
     *
     * @param        $tax_id  The tax identifier
     */
    public function tax_rate_by_id($tax_id){
        $builder = $this->db->table(get_db_prefix().'taxes');
        $builder->where('id', $tax_id);
        $tax = $builder->get()->getRow();
        if($tax){
            return $tax->percentage;
        }
        return 0;
    }

    /**
     * get taxes dropdown template
     * @param  [type]  $name     
     * @param  [type]  $taxname  
     * @param  string  $type     
     * @param  string  $item_key 
     * @param  boolean $is_edit  
     * @param  boolean $manual   
     * @return [type]            
     */
    public function get_taxes_dropdown_template($name, $taxname, $type = '', $item_key = '', $is_edit = false, $manual = false)
    {
        // if passed manually - like in proposal convert items or project
        if($taxname != '' && !is_array($taxname)){
            $taxname = explode(',', $taxname);
        }

        if ($manual == true) {
            // + is no longer used and is here for backward compatibilities
            if (is_array($taxname) || strpos($taxname, '+') !== false) {
                if (!is_array($taxname)) {
                    $__tax = explode('+', $taxname);
                } else {
                    $__tax = $taxname;
                }
                // Multiple taxes found // possible option from default settings when invoicing project
                $taxname = [];
                foreach ($__tax as $t) {
                    $tax_array = explode('|', $t);
                    if (isset($tax_array[0]) && isset($tax_array[1])) {
                        array_push($taxname, $tax_array[0] . '|' . $tax_array[1]);
                    }
                }
            } else {
                $tax_array = explode('|', $taxname);
                // isset tax rate
                if (isset($tax_array[0]) && isset($tax_array[1])) {
                    $tax = get_tax_by_name($tax_array[0]);
                    if ($tax) {
                        $taxname = $tax->name . '|' . $tax->taxrate;
                    }
                }
            }
        }
        // First get all system taxes
        $Taxes_model = model("Models\Taxes_model");
        $tax_options = array(
            "deleted" => 0,
        );
        $taxes = $Taxes_model->get_details($tax_options)->getResultArray();

        $i     = 0;
        foreach ($taxes as $tax) {
            unset($taxes[$i]['id']);
            $taxes[$i]['name'] = $tax['title'] . '|' . $tax['percentage'];
            $i++;
        }
        if ($is_edit == true) {

            // Lets check the items taxes in case of changes.
            // Separate functions exists to get item taxes for Invoice, Estimate, Proposal, Credit Note
            if($type == 'invoice'){
                $item_taxes = [];
            }else{

                $func_taxes = 'get_' . $type . '_item_taxes';
                if (function_exists($func_taxes)) {
                    $item_taxes = call_user_func($func_taxes, $item_key);
                }
            }

            foreach ($item_taxes as $item_tax) {
                $new_tax            = [];
                $new_tax['name']    = $item_tax['taxname'];
                $new_tax['taxrate'] = $item_tax['taxrate'];
                $taxes[]            = $new_tax;
            }
        }

        // In case tax is changed and the old tax is still linked to estimate/proposal when converting
        // This will allow the tax that don't exists to be shown on the dropdowns too.
        if (is_array($taxname)) {
            foreach ($taxname as $tax) {
                // Check if tax empty
                if ((!is_array($tax) && $tax == '') || is_array($tax) && $tax['taxname'] == '') {
                    continue;
                };
                // Check if really the taxname NAME|RATE don't exists in all taxes
                if (!valueExistsByKey($taxes, 'name', $tax)) {
                    if (!is_array($tax)) {
                        $tmp_taxname = $tax;
                        $tax_array   = explode('|', $tax);
                    } else {
                        $tax_array   = explode('|', $tax['taxname']);
                        $tmp_taxname = $tax['taxname'];
                        if ($tmp_taxname == '') {
                            continue;
                        }
                    }
                    $taxes[] = ['name' => $tmp_taxname, 'percentage' => $tax_array[1]];
                }
            }
        }

        // Clear the duplicates
        $taxes = $this->pur_uniqueByKey($taxes, 'name');

        $select = '<select class="select2 display-block taxes" data-width="100%" name="' . $name . '" multiple placeholder="' . _l('no_tax') . '">';

        foreach ($taxes as $tax) {
            $selected = '';
            if (is_array($taxname)) {
                foreach ($taxname as $_tax) {
                    if (is_array($_tax)) {
                        if ($_tax['taxname'] == $tax['name']) {
                            $selected = 'selected';
                        }
                    } else {
                        if ($_tax == $tax['name']) {
                            $selected = 'selected';
                        }
                    }
                }
            } else {
                if ($taxname == $tax['title']) {
                    $selected = 'selected';
                }
            }

            $select .= '<option value="' . $tax['name'] . '" ' . $selected . ' data-taxrate="' . $tax['percentage'] . '" data-taxname="' . $tax['name'] . '" data-subtext="' . $tax['name'] . '">' . $tax['percentage'] . '%</option>';
        }
        $select .= '</select>';

        return $select;
    }

     /**
     * row item to variation
     * @param  [type] $item_value 
     * @return [type]             
     */
    public function row_item_to_variation($item_value)
    {
        if($item_value){

                $name = '';
                if($item_value->attributes != null && $item_value->attributes != ''){
                    $attributes_decode = json_decode($item_value->attributes);

                    foreach ($attributes_decode as $value) {
                        if(strlen($name) > 0){
                            $name .= '#'.$value->name.' ( '.$value->option.' ) ';
                        }else{
                            $name .= ' #'.$value->name.' ( '.$value->option.' ) ';
                        }
                    }


                }

                $item_value->new_description = $item_value->description;
                
        }

        return $item_value;
    }

    /**
     * Gets the pur request detail.
     *
     * @param      <int>  $pur_request  The pur request
     *
     * @return     <array>  The pur request detail.
     */
    public function get_pur_request_detail($pur_request){
        $builder = $this->db->table(db_prefix().'pur_request_detail');
        $builder->where('pur_request',$pur_request);
        $pur_request_lst = $builder->get()->getResultArray();

        foreach($pur_request_lst as $key => $detail){
            $pur_request_lst[$key]['into_money'] = (float) $detail['into_money'];
            $pur_request_lst[$key]['total'] = (float) $detail['total'];
            $pur_request_lst[$key]['unit_price'] = (float) $detail['unit_price'];
            $pur_request_lst[$key]['tax_value'] = (float) $detail['tax_value'];
        }

        return $pur_request_lst;
    }

     /**
     * Gets the taxes.
     *
     * @return     <array>  The taxes.
     */
     public function get_taxes()
     {
        return $this->db->query('select id, title as label, percentage from '.get_db_prefix().'taxes')->getResultArray();
     }

    /**
     * Gets the units.
     *
     * @return     <array>  The list units.
     */
    public function get_units(){
        return $this->db->query('select unit_type_id as id, unit_name as label from '.db_prefix().'ware_unit_type')->getResultArray();
    }

    /**
     * wh uniqueByKey
     * @param  [type] $array 
     * @param  [type] $key   
     * @return [type]        
     */
    public function pur_uniqueByKey($array, $key)
    {
        $temp_array = [];
        $i          = 0;
        $key_array  = [];

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i]  = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }

        return $temp_array;
    }

    /**
     * Gets the item v 2.
     *
     * @param      string  $id     The identifier
     *
     * @return     <type>  The item v 2.
     */
    public function get_item_v2($id = '')
    {
        $builder = $this->db->table(get_db_prefix().'items');
        $columns             = $this->db->getFieldNames(get_db_prefix().'items');
        $rateCurrencyColumns = '';
        foreach ($columns as $column) {
            if (strpos($column, 'rate_currency_') !== false) {
                $rateCurrencyColumns .= $column . ',';
            }
        }

        $builder = $this->db->table(get_db_prefix().'items');
        $builder->select($rateCurrencyColumns . '' .get_db_prefix(). 'items.id as itemid,rate,
            t1.percentage as taxrate,t1.id as taxid,t1.title as taxname,
            t2.percentage as taxrate_2,t2.id as taxid_2,t2.title as taxname_2,
            CONCAT(commodity_code,"_",'.get_db_prefix().'items.title) as code_description,description,category_id,' .get_db_prefix(). 'item_categories.title as group_name,unit_type as unit,'.get_db_prefix().'ware_unit_type.unit_name as unit_name, purchase_price, unit_id, guarantee');
        $builder->join('' .get_db_prefix(). 'taxes t1', 't1.id = ' .get_db_prefix(). 'items.tax', 'left');
        $builder->join('' .get_db_prefix(). 'taxes t2', 't2.id = ' .get_db_prefix(). 'items.tax2', 'left');
        $builder->join(get_db_prefix() . 'item_categories', '' .get_db_prefix(). 'item_categories.id = ' .get_db_prefix(). 'items.category_id', 'left');
        $builder->join(get_db_prefix() . 'ware_unit_type', '' .get_db_prefix(). 'ware_unit_type.unit_type_id = ' .get_db_prefix(). 'items.unit_id', 'left');
        $builder->orderBy(get_db_prefix().'items.title', 'asc');
        if (is_numeric($id)) {
            $builder->where(get_db_prefix() . 'items.id', $id);
            return $builder->get()->getRow();
        }

        return $builder->get()->getResultArray();
    }

    /**
     * wh get tax rate
     * @param  [type] $taxname 
     * @return [type]          
     */
    public function pur_get_tax_rate($taxname)
    {   
        $tax_rate = 0;
        $tax_rate_str = '';
        $tax_id_str = '';
        $tax_name_str = '';
        if(is_array($taxname)){
            foreach ($taxname as $key => $value) {
                $_tax = explode("|", $value);
                if(isset($_tax[1])){
                    $tax_rate += (float)$_tax[1];
                    if(strlen($tax_rate_str) > 0){
                        $tax_rate_str .= '|'.$_tax[1];
                    }else{
                        $tax_rate_str .= $_tax[1];
                    }

                    $_t_name = $_tax[0];
                    if(!(strpos($_tax[0], '(TDS)') === false) ){
                        $tax_n = explode('(TDS)', $_tax[0]);
                        $_t_name = $tax_n[0];

                    }

                    $builder = $this->db->table(get_db_prefix().'taxes');
                    $builder->where('title', $_t_name);
                    $taxes = $builder->get()->getRow();
                    if($taxes){
                        if(strlen($tax_id_str) > 0){
                            $tax_id_str .= '|'.$taxes->id;
                        }else{
                            $tax_id_str .= $taxes->id;
                        }
                    }

                    if(strlen($tax_name_str) > 0){
                        $tax_name_str .= '|'.$_tax[0];
                    }else{
                        $tax_name_str .= $_tax[0];
                    }
                }
            }
        }
        return ['tax_rate' => $tax_rate, 'tax_rate_str' => $tax_rate_str, 'tax_id_str' => $tax_id_str, 'tax_name_str' => $tax_name_str];
    }

    /**
     * Adds a pur request.
     *
     * @param      <array>   $data   The data
     *
     * @return     boolean  
     */
    public function add_pur_request($data){

        $data['request_date'] = date('Y-m-d H:i:s');
        $check_appr = $this->get_approve_setting('pur_request');
        $data['status'] = 1;
        if($check_appr && $check_appr != false){
            $data['status'] = 1;
        }else{
            $data['status'] = 2;
        }

        $detail_data = [];
        if(isset($data['newitems'])){
            $detail_data = $data['newitems'];
            unset($data['newitems']);
        }

        $data['to_currency'] = $data['currency'];

        unset($data['item_text']);
        unset($data['unit_price']);
        unset($data['quantity']);
        unset($data['into_money']);
        unset($data['tax_select']);
        unset($data['tax_value']);
        unset($data['total']);
        unset($data['item_select']);
        unset($data['item_code']);
        unset($data['unit_name']);
        unset($data['request_detail']);
  

        if(isset($data['send_to_vendors']) && count($data['send_to_vendors']) > 0){
            $data['send_to_vendors'] = implode(',', $data['send_to_vendors']);
        }

        if(isset($data['total_mn'])){
            $data['total'] = $data['total_mn'];    
            unset($data['total_mn']);
        }

        $data['total_tax'] = $data['total'] - $data['subtotal'];
       
        $prefix = get_setting('pur_request_prefix');

        $pr_builder = $this->db->table(get_db_prefix().'pur_request');

        $pr_builder->where('pur_rq_code',$data['pur_rq_code']);
        $check_exist_number = $pr_builder->get()->getRow();

        while($check_exist_number) {
          $data['number'] = $data['number'] + 1;
          $data['pur_rq_code'] =  $prefix.'-'.str_pad($data['number'],5,'0',STR_PAD_LEFT).'-'.date('Y');
          $pr_builder->where('pur_rq_code',$data['pur_rq_code']);
          $check_exist_number = $pr_builder->get()->getRow();
        }

        $data['hash'] = app_generate_hash();

        
      
        $pr_builder->insert($data);
        $insert_id = $this->db->insertID();
        if($insert_id){

            // Update next purchase order number in settings
            $next_number = $data['number']+1;
            update_setting('next_purchase_request_number', $next_number);

            if(count($detail_data) > 0){
                foreach($detail_data as $key => $rqd){
                    $dt_data = [];
                    $dt_data['pur_request'] = $insert_id;
                    $dt_data['item_code'] = $rqd['item_code'];
                    $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                    $dt_data['unit_price'] = $rqd['unit_price'];
                    $dt_data['into_money'] = $rqd['into_money'];
                    $dt_data['total'] = $rqd['total'];
                    $dt_data['tax_value'] = $rqd['tax_value'];
                    $dt_data['item_text'] = $rqd['item_text'];

                    $tax_money = 0;
                    $tax_rate_value = 0;
                    $tax_rate = null;
                    $tax_id = null;
                    $tax_name = null;

                    if(isset($rqd['tax_select'])){
                        $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                        $tax_rate_value = $tax_rate_data['tax_rate'];
                        $tax_rate = $tax_rate_data['tax_rate_str'];
                        $tax_id = $tax_rate_data['tax_id_str'];
                        $tax_name = $tax_rate_data['tax_name_str'];
                    }

                    $dt_data['tax'] = $tax_id;
                    $dt_data['tax_rate'] = $tax_rate;
                    $dt_data['tax_name'] = $tax_name;

                    $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                    if($data['status'] == 2 && ($rqd['item_code'] == '' || $rqd['item_code'] == null) ){
                        $item_data['description'] = $rqd['item_text'];
                        $item_data['purchase_price'] = $rqd['unit_price'];
                        $item_data['unit_id'] = $rqd['unit_id'];
                        $item_data['rate'] = '';
                        $item_data['sku_code'] = '';
                        $item_data['commodity_barcode'] = $this->generate_commodity_barcode();
                        $item_data['commodity_code'] = $this->generate_commodity_barcode();
                        $item_id = $this->add_commodity_one_item($item_data);
                        if($item_id){
                           $dt_data['item_code'] = $item_id; 
                        }
                        
                    }

                    $pr_detail_builder = $this->db->table(get_db_prefix().'pur_request_detail');
                    $pr_detail_builder->insert($dt_data);
                }
                

            }

            return $insert_id;
        }
        return false;
    }

    /**
     * Gets the approve setting.
     *
     * @param      <type>   $type    The type
     * @param      string   $status  The status
     *
     * @return     boolean  The approve setting.
     */
    public function get_approve_setting($type, $status = ''){
        $builder = $this->db->table(get_db_prefix().'pur_approval_setting');

        $builder->select('*');
        $builder->where('related', $type);
        $approval_setting = $builder->get()->getRow();
        if($approval_setting){
            return json_decode($approval_setting->setting);
        }else{
            return false;
        }
    }

    /**
     * Gets the purchase request.
     *
     * @param      string  $id     The identifier
     *
     * @return     <row or array>  The purchase request.
     */
    public function get_purchase_request($id = ''){
        $builder = $this->db->table(get_db_prefix().'pur_request');
        if($id == ''){
            return $builder->get()->getResultArray();
        }else{
            $builder->where('id',$id);
            return $builder->get()->getRow();
        }
    }

    /**
     * Gets the html tax pur request.
     */
    public function get_html_tax_pur_request($id){
        $html = '';
        $preview_html = '';
        $pdf_html = '';
        $taxes = [];
        $t_rate = [];
        $tax_val = [];
        $tax_val_rs = [];
        $tax_name = [];
        $rs = [];
  
        $base_currency = get_base_currency();
        $base_currency_symbol = get_setting('currency_symbol');

        $pur_request = $this->get_purchase_request($id);
        if($pur_request->currency != $base_currency){
            $base_currency_symbol = $pur_request->currency;
        }

        $builder = $this->db->table(db_prefix().'pur_request_detail');

        $builder->where('pur_request', $id);
        $details = $builder->get()->getResultArray();
        foreach($details as $row){
            if($row['tax'] != ''){
                $tax_arr = explode('|', $row['tax']);

                $tax_rate_arr = [];
                if($row['tax_rate'] != ''){
                    $tax_rate_arr = explode('|', $row['tax_rate']);
                }

                foreach($tax_arr as $k => $tax_it){
                    if(!isset($tax_rate_arr[$k]) ){
                        $tax_rate_arr[$k] = $this->tax_rate_by_id($tax_it);
                    }

                    if(!in_array($tax_it, $taxes)){
                        $taxes[$tax_it] = $tax_it;
                        $t_rate[$tax_it] = $tax_rate_arr[$k];
                        $tax_name[$tax_it] = $this->get_tax_name($tax_it).' ('.$tax_rate_arr[$k].'%)';
                    }
                }
            }
        }

        if(count($tax_name) > 0){
            foreach($tax_name as $key => $tn){
                $tax_val[$key] = 0;
                foreach($details as $row_dt){
                    if(!(strpos($row_dt['tax'], $taxes[$key]) === false)){
                        $tax_val[$key] += ($row_dt['into_money']*$t_rate[$key]/100);
                    }
                }
                $pdf_html .= '<tr id="subtotal"><td width="33%"></td><td>'.$tn.'</td><td>'.to_currency($tax_val[$key], $base_currency_symbol).'</td></tr>';
                $preview_html .= '<tr id="subtotal"><td>'.$tn.'</td><td>'.to_currency($tax_val[$key], $base_currency_symbol).'</td><tr>';
                $html .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.to_currency($tax_val[$key], $base_currency_symbol).'</td></tr>';
                $tax_val_rs[] = $tax_val[$key];
            }
        }
        
        $rs['pdf_html'] = $pdf_html;
        $rs['preview_html'] = $preview_html;
        $rs['html'] = $html;
        $rs['taxes'] = $taxes;
        $rs['taxes_val'] = $tax_val_rs;
        return $rs;
    }

    /**
     * { update pur request }
     *
     * @param      <array>   $data   The data
     * @param      <int>   $id     The identifier
     *
     * @return     boolean   
     */
    public function update_pur_request($data,$id){
        $affectedRows = 0;
        $purq = $this->get_purchase_request($id);

        $data['subtotal'] = $data['subtotal'];

        $data['to_currency'] = $data['currency'];

        $new_purchase_request = [];
        if (isset($data['newitems'])) {
            $new_purchase_request = $data['newitems'];
            unset($data['newitems']);
        }

        $update_purchase_request = [];
        if (isset($data['items'])) {
            $update_purchase_request = $data['items'];
            unset($data['items']);
        }

        $remove_purchase_request = [];
        if (isset($data['removed_items'])) {
            $remove_purchase_request = $data['removed_items'];
            unset($data['removed_items']);
        }

        unset($data['item_text']);
        unset($data['unit_price']);
        unset($data['quantity']);
        unset($data['into_money']);
        unset($data['tax_select']);
        unset($data['tax_value']);
        unset($data['total']);
        unset($data['item_select']);
        unset($data['item_code']);
        unset($data['unit_name']);
        unset($data['request_detail']);
        unset($data['isedit']);

        if(isset($data['send_to_vendors']) && count($data['send_to_vendors']) > 0){
            $data['send_to_vendors'] = implode(',', $data['send_to_vendors']);
        }

        if(isset($data['total_mn'])){
            $data['total'] = $data['total_mn'];    
            unset($data['total_mn']);
        }

        $data['total_tax'] = (float)$data['total'] -  (float)$data['subtotal'];

        if(isset($data['from_items'])){
            $data['from_items'] = 1;
        }else{
            $data['from_items'] = 0;
        }

        
        $pr_builder = $this->db->table(db_prefix().'pur_request');
        $pr_builder->where('id',$id);
        $affected_rows_update = $pr_builder->update($data);
        if($affected_rows_update > 0){
            $affectedRows++;
        }

        if(count($new_purchase_request) > 0){
            foreach($new_purchase_request as $key => $rqd){
                $dt_data = [];
                $dt_data['pur_request'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_text'] = $rqd['item_text'];

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                if($purq->status == 2 && ($rqd['item_code'] == '' || $rqd['item_code'] == null) ){
                    $item_data['description'] = $rqd['item_text'];
                    $item_data['purchase_price'] = $rqd['unit_price'];
                    $item_data['unit_id'] = $rqd['unit_id'];
                    $item_data['rate'] = '';
                    $item_data['sku_code'] = '';
                    $item_data['commodity_barcode'] = $this->generate_commodity_barcode();
                    $item_data['commodity_code'] = $this->generate_commodity_barcode();
                    $item_id = $this->add_commodity_one_item($item_data);
                    if($item_id){
                       $rq_detail[$key]['item_code'] = $item_id; 
                    }
                }

                $pr_detail_builder = $this->db->table(db_prefix().'pur_request_detail');

                $_new_detail_id = $pr_detail_builder->insert( $dt_data);
                if($_new_detail_id){
                    $affectedRows++;
                }
            }
        }

        if(count($update_purchase_request) > 0){
            foreach($update_purchase_request as $_key => $rqd){
                $dt_data = [];
                $dt_data['pur_request'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_text'] = $rqd['item_text'];

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                if($purq->status == 2 && ($rqd['item_code'] == '' || $rqd['item_code'] == null) ){
                    $item_data['description'] = $rqd['item_text'];
                    $item_data['purchase_price'] = $rqd['unit_price'];
                    $item_data['unit_id'] = $rqd['unit_id'];
                    $item_data['rate'] = '';
                    $item_data['sku_code'] = '';
                    $item_data['commodity_barcode'] = $this->generate_commodity_barcode();
                    $item_data['commodity_code'] = $this->generate_commodity_barcode();
                    $item_id = $this->add_commodity_one_item($item_data);
                    if($item_id){
                       $dt_data['item_code'] = $item_id; 
                    }
                }

                $pr_detail_builder = $this->db->table(db_prefix().'pur_request_detail');
                $pr_detail_builder->where('prd_id', $rqd['id']);
                $update_affected_rows = $pr_detail_builder->update($dt_data);


                if($update_affected_rows > 0){
                    $affectedRows++;
                }
            }
        }

        if(count($remove_purchase_request) > 0){ 
            foreach($remove_purchase_request as $remove_id){
                $pr_detail_builder = $this->db->table(db_prefix().'pur_request_detail');

                $pr_detail_builder->where('prd_id', $remove_id);
                if ($pr_detail_builder->delete()) {
                    $affectedRows++;
                }
            }
        }


        if($affectedRows > 0){
            return true;
        }
        return false;
    }

    /**
     * { delete pur request }
     */
    public function delete_pur_request($id){
        $affectedRows = 0;
        $builder = $this->db->table(db_prefix().'pur_request');
        $builder->where('id', $id);
        if($builder->delete()){
            $affectedRows++;
            $detail_builder = $this->db->table(db_prefix().'pur_request_detail');
            $detail_builder->where('pur_request', $id);
            if( $detail_builder->delete()){
                $affectedRows++;
            }

            $attachment = $this->get_purchase_request_attachments($id);
            if(count($attachment) > 0){
                foreach($attachment as $item){
                    if($this->delete_purrequest_attachment($item['id'])){
                        $affectedRows++;
                    }
                }
            }

        }

        if($affectedRows > 0){
            return true;
        }
        return false;
    }

    /**
     * Gets the staff sign.
     *
     * @param      <type>  $rel_id    The relative identifier
     * @param      <type>  $rel_type  The relative type
     *
     * @return     array   The staff sign.
     */
    public function get_staff_sign($rel_id, $rel_type){
        $builder = $this->db->table(db_prefix().'pur_approval_details');

        $builder->select('*');

        $builder->where('rel_id', $rel_id);
        $builder->where('rel_type', $rel_type);
        $builder->where('action', 'sign');    
        $approve_status = $builder->get()->getResultArray();
        if(isset($approve_status))
        {
            $array_return = [];
            foreach ($approve_status as $key => $value) {
               array_push($array_return, $value['staffid']);
            }
            return $array_return;
        }
        return [];
    }

    /**
     * { check approval details }
     *
     * @param      <type>          $rel_id    The relative identifier
     * @param      <type>          $rel_type  The relative type
     *
     * @return     boolean|string 
     */
    public function check_approval_details($rel_id, $rel_type){
        $builder = $this->db->table(db_prefix().'pur_approval_details');

        $builder->where('rel_id', $rel_id);
        $builder->where('rel_type', $rel_type);
        $approve_status = $builder->get()->getResultArray();
        if(count($approve_status) > 0){
            foreach ($approve_status as $value) {
                if($value['approve'] == -1){
                    return 'reject';
                }
                if($value['approve'] == 0){
                    $value['staffid'] = explode(', ',$value['staffid']);
                    return $value;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Gets the list approval details.
     *
     * @param      <type>  $rel_id    The relative identifier
     * @param      <type>  $rel_type  The relative type
     *
     * @return     <array>  The list approval details.
     */
    public function get_list_approval_details($rel_id, $rel_type){
        $builder = $this->db->table(db_prefix().'pur_approval_details');

        $builder->select('*');
        $builder->where('rel_id', $rel_id);
        $builder->where('rel_type', $rel_type);
        return $builder->get()->getResultArray();
    }

    /**
     * Gets the items.
     *
     * @return     <array>  The items.
     */
    public function get_items(){
       return $this->db->query('select id as id, CONCAT(commodity_code," - " ,description) as label from '.db_prefix().'items')->getResultArray();
    }



    /**
     * Gets the pur request pdf html.
     *
     * @param      <type>  $pur_request_id  The pur request identifier
     *
     * @return     string  The pur request pdf html.
     */
    public function get_pur_request_pdf_html($pur_request_id){

        $pur_request = $this->get_purchase_request($pur_request_id);

        $project_name = '';

        if($pur_request->project > 0){
            $projects_model = model("Models\Projects_model");
            $project = $projects_model->get_details(['id' => $pur_request->project])->getRow();
            if($project && isset($project->title)){
                $project_name = $project->title;
            }
        }

        $tax_data = $this->get_html_tax_pur_request($pur_request_id);

        if($pur_request->currency != ''){
            $base_currency = $pur_request->currency;
        }else{
            $base_currency = get_base_currency();
        }

        if($base_currency == get_setting('default_currency')){
            $base_currency = get_setting('currency_symbol');
        }
        

        $pur_request_detail = $this->get_pur_request_detail($pur_request_id);

        $company_name = ''; 

        $company_model =  model("Models\Company_model");
        $company = $company_model->get_details(['is_default' => 1])->getRow();
        if(isset($company->name)){
            $company_name = $company->name;
        }

        $address = ''; 
        if(isset($company->address)){
            $address = $company->address;
        }

        $teams_model = model("Models\Team_model");
        $dpm_name = $teams_model->get_details(['id' => $pur_request->department ])->getRow()->title;
        
        $day = date('d',strtotime($pur_request->request_date));
        $month = date('m',strtotime($pur_request->request_date));
        $year = date('Y',strtotime($pur_request->request_date));
        $list_approve_status = $this->get_list_approval_details($pur_request_id,'pur_request');

    $html = '<table class="table">
        <tbody>
          <tr>
            <td class="font_td_cpn width70">'. _l('purchase_company_name').': '. $company_name.'</td>
            <td rowspan="3" class="text-right width30"><img src="'.get_pdf_logo_url().'"></td>
          </tr>
          <tr>
            <td class="font_500">'. _l('address').': '. $address.'</td>
          </tr>
          <tr>
            <td class="font_500">'.$pur_request->pur_rq_code.'</td>
          </tr>
        </tbody>
      </table>
      <table class="table">
        <tbody>
          <tr>
            
            <td class="td_ali_font"><h2 class="h2_style">'.mb_strtoupper(_l('purchase_request')).'</h2></td>
           
          </tr>
          <tr>
            
            <td class="align_cen">'. _l('days').' '.$day.' '._l('month').' '.$month.' '._l('year') .' '.$year.'</td>
            
          </tr>
          
        </tbody>
      </table>
      <table class="table">
        <tbody>
          <tr>
            <td class="td_width_25"><h4>'. _l('requester').':</h4></td>
            <td class="td_width_75">'. get_staff_full_name1($pur_request->requester).'</td>
          </tr>
          <tr>
            <td class="font_500"><h4>'. _l('department').':</h4></td>
            <td>'. $dpm_name.'</td>
          </tr>
          <tr>
            <td class="font_500"><h4>'. _l('type').':</h4></td>
            <td>'. _l($pur_request->type).'</td>
          </tr>
          <tr>
            <td class="font_500"><h4>'. _l('project').':</h4></td>
            <td>'.  $project_name.'</td>
          </tr>
        </tbody>
      </table>
      <br><br>
      ';

      $html .=  '<table class="table pur_request-item">
            <thead>
              <tr class="border_tr">
                <th align="left" class="thead-dark">'._l('items').'</th>
                <th align="right" class="thead-dark">'._l('purchase_unit_price').'</th>
                <th align="right" class="thead-dark">'._l('purchase_quantity').'</th>
                <th align="right" class="thead-dark">'._l('into_money').'</th>';
                if(get_setting('show_purchase_tax_column')){
                        $html .= '<th align="right" class="thead-dark">'._l('tax_value').'</th>';
                  }
                $html .= '<th align="right" class="thead-dark">'._l('total').'</th>
              </tr>
            </thead>
          <tbody>';

      $tmn = 0;  
      $_total = 0;  
      foreach($pur_request_detail as $row){
        $items = $this->get_items_by_id($row['item_code']);
        $units = $this->get_units_by_id($row['unit_id']);
        if($items){
            $unit_name = isset($units->unit_name) ? $units->unit_name : '';

            $html .= '<tr class="border_tr">
                <td >'.$items->commodity_code.' - '.$items->title.'</td>

                <td align="right">'.to_currency($row['unit_price'],$base_currency).'</td>
                <td align="right">'.to_decimal_format($row['quantity']).' '.$unit_name.'</td>
                <td align="right">'.to_currency($row['into_money'],$base_currency).'</td>';
                if(get_setting('show_purchase_tax_column')){    
                    $html .= '<td align="right">'.to_currency($row['tax_value'],$base_currency).'</td>';
                }
                $html .= '<td align="right">'.to_currency($row['total'],$base_currency).'</td>
              </tr>';
        }else{
            $unit_name = isset($units->unit_name) ? $units->unit_name : '';
            $html .= '<tr class="border_tr">
                <td >'.$row['item_text'].'</td>

                <td align="right">'.to_currency($row['unit_price'],$base_currency).'</td>
                <td align="right">'.$row['quantity'].'</td>
                <td align="right">'.to_currency($row['into_money'],$base_currency).'</td>';
                if(get_setting('show_purchase_tax_column')){    
                    $html .= '<td align="right">'.to_currency($row['tax_value'],$base_currency).'</td>';
                }
                $html .= '<td align="right">'.to_currency($row['total'],$base_currency).'</td>
              </tr>';
        }

        $tmn += $row['into_money'];
        $_total += $row['total'];
      }  
      $html .=  '</tbody>
      </table><br><br>';

      $html .= '<table class="table text-right"><tbody>';
      $html .= '<tr>
                 <td class="width33"></td>
                 <td>'. _l('subtotal').'</td>
                 <td class="subtotal">
                    '. to_currency($tmn, $base_currency).'
                 </td>
              </tr>';

      $html .= $tax_data['pdf_html'];
      $html .= '<tr>
                 <td class="width33"></td>
                 <td>'. _l('total').'</td>
                 <td class="subtotal">
                    '. to_currency($pur_request->total, $base_currency).'
                 </td>
              </tr>';

      $html .= ' </tbody></table>';

      $html .= '<br>
      <br>
      <br>
      <br>
      <table class="table">
        <tbody>
          <tr>';
     if(count($list_approve_status) > 0){
      
        foreach ($list_approve_status as $value) {
     $html .= '<td class="td_appr">';
        if($value['action'] == 'sign'){
            $html .= '<h3>'.mb_strtoupper(get_staff_full_name1($value['staffid'])).'</h3>';
            if($value['approve'] == 2){ 
                $html .= '<img src="'.FCPATH.PLUGIN_URL_PATH.'Purchase/Uploads/pur_request/signature/'.$pur_request->id.'/signature_'.$value['id'].'.png" class="img_style">';
            }
                
        }else{ 
        $html .= '<h3>'.mb_strtoupper(get_staff_full_name1($value['staffid'])).'</h3>';
              if($value['approve'] == 2){ 
        $html .= '<img src="'.FCPATH.PLUGIN_URL_PATH .'Purchase/Uploads/approval/approved.png" class="img_style">';
             }elseif($value['approve'] == 3){
        $html .= '<img src="'.FCPATH.PLUGIN_URL_PATH.'Purchase/Uploads/approval/rejected.png" class="img_style">';
             }
              
                }
       $html .= '</td>';
        }
       
    
    
     } 
            $html .= '<td class="td_ali_font"><h3>'.mb_strtoupper(_l('purchase_requestor')).'</h3></td>
            <td class="td_ali_font"><h3>'.mb_strtoupper(_l('purchase_treasurer')).'</h3></td></tr>
        </tbody>
      </table>';
      $html .=  '<link href="' . FCPATH. PLUGIN_URL_PATH. 'Purchase/assets/css/pur_order_pdf.css' . '"  rel="stylesheet" type="text/css" />';
      return $html;
    }

    /**
     * Gets the pur request pdf html.
     *
     * @param      <type>  $pur_request_id  The pur request identifier
     *
     * @return     string  The pur request pdf html.
     */
    public function get_purestimate_pdf_html($pur_estimate_id){
        

        $pur_estimate = $this->get_estimate($pur_estimate_id);
        $pur_estimate_detail = $this->get_pur_estimate_detail($pur_estimate_id);

        $company_name = ''; 

        $company_model =  model("Models\Company_model");
        $company = $company_model->get_details(['is_default' => 1])->getRow();
        if(isset($company->name)){
            $company_name = $company->name;
        }

        $address = ''; 
        if(isset($company->address)){
            $address = $company->address;
        }
        
        if($pur_estimate->currency != ''){
            $base_currency = $pur_estimate->currency;
        }else{
            $base_currency = get_base_currency();
        }

        if($base_currency == get_setting('default_currency')){
            $base_currency = get_setting('currency_symbol');
        }
        
        $day = date('d',strtotime($pur_estimate->date));
        $month = date('m',strtotime($pur_estimate->date));
        $year = date('Y',strtotime($pur_estimate->date));
        $tax_data = $this->get_html_tax_pur_estimate($pur_estimate_id);
        
    $html = '<table class="table">
        <tbody>
          <tr>
            <td class="font_td_cpn width70" >'. _l('purchase_company_name').': '. $company_name.'</td>
            <td rowspan="2" class="text-right width30"><img src="'.get_pdf_logo_url().'"></td>
            
          </tr>
          <tr>
            <td class="font_500">'. _l('address').': '. $address.'</td>
            <td></td>
            
          </tr>
        </tbody>
      </table>
      <table class="table">
        <tbody>
          <tr>
            
            <td class="td_ali_font"><h2 class="h2_style">'.mb_strtoupper(_l('estimate')).'</h2></td>
           
          </tr>
          <tr>
            
            <td class="align_cen">'. _l('days').' '.$day.' '._l('month').' '.$month.' '._l('year') .' '.$year.'</td>
            
          </tr>
          
        </tbody>
      </table>
      <table class="table">
        <tbody>
          <tr>
            <td class="td_width_25"><h4>'. _l('add_from').':</h4></td>
            <td class="td_width_75">'. get_staff_full_name($pur_estimate->addedfrom).'</td>
          </tr>
          <tr>
            <td class="td_width_25"><h4>'. _l('vendor').':</h4></td>
            <td class="td_width_75">'. get_vendor_company_name($pur_estimate->vendor).'</td>
          </tr>
          
        </tbody>
      </table>

      <h3>
       '. html_entity_decode(format_pur_estimate_number($pur_estimate_id)).'
       </h3>
      <br><br>
      ';

      $html .=  '<table class="table purorder-item">
        <thead>
          <tr>
            <th class="thead-dark">'._l('items').'</th>
            <th class="thead-dark" align="right">'._l('purchase_unit_price').'</th>
            <th class="thead-dark" align="right">'._l('purchase_quantity').'</th>';
         
            if(get_setting('show_purchase_tax_column') == 1){    
                $html .= '<th class="thead-dark" align="right">'._l('tax').'</th>';
            }
 
            $html .= '<th class="thead-dark" align="right">'._l('discount').'</th>
            <th class="thead-dark" align="right">'._l('total').'</th>
          </tr>
          </thead>
          <tbody>';
        $t_mn = 0;
      foreach($pur_estimate_detail as $row){
        $items = $this->get_items_by_id($row['item_code']);
        $units = $this->get_units_by_id($row['unit_id']);
        $item_name = isset($items->commodity_code) ? $items->commodity_code.' - '.$items->title : $row['item_name'];

        $html .= '<tr nobr="true" class="sortable">
            <td >'.$item_name.'</td>
            <td align="right">'.to_currency($row['unit_price'],$base_currency).'</td>
            <td align="right">'.to_decimal_format($row['quantity'], 0).'</td>';
         
            if(get_setting('show_purchase_tax_column') == 1){  
                $html .= '<td align="right">'.to_currency($row['total'] - $row['into_money'],$base_currency).'</td>';
            }
       
            $html .= '<td align="right">'.to_currency($row['discount_money'],$base_currency).'</td>
            <td align="right">'.to_currency($row['total_money'],$base_currency).'</td>
          </tr>';

        $t_mn += $row['total_money'];
      }  
      $html .=  '</tbody>
      </table><br><br>';

      $html .= '<table class="table text-right"><tbody>';
      $html .= '<tr id="subtotal">
                    <td class="width33"></td>
                     <td>'._l('subtotal').' </td>
                     <td class="subtotal">
                        '.to_currency($pur_estimate->subtotal,$base_currency).'
                     </td>
                  </tr>';
      $html .= $tax_data['pdf_html'];
      if($pur_estimate->discount_total > 0){
        $html .= '<tr id="subtotal">
                  <td class="width33"></td>
                     <td>'._l('discount(money)').'</td>
                     <td class="subtotal">
                        '.to_currency($pur_estimate->discount_total, $base_currency).'
                     </td>
                  </tr>';
      }
      if($pur_estimate->shipping_fee > 0){
        $html .= '<tr id="subtotal">
                  <td class="width33"></td>
                     <td>'._l('pur_shipping_fee').'</td>
                     <td class="subtotal">
                        '.to_currency($pur_estimate->shipping_fee, $base_currency).'
                     </td>
                  </tr>';
      }
      $html .= '<tr id="subtotal">
                 <td class="width33"></td>
                 <td>'. _l('total').'</td>
                 <td class="subtotal">
                    '. to_currency($pur_estimate->total, $base_currency).'
                 </td>
              </tr>';

      $html .= ' </tbody></table>';

      $html .= '<div class="col-md-12 mtop15">
                        <h4>'. _l('terms_and_conditions').': </h4><p>'. html_entity_decode($pur_estimate->terms).'</p>
                       
                     </div>';
      $html .= '<br>
      <br>
      <br>
      <br>';
      $html .=  '<link href="' . FCPATH. PLUGIN_URL_PATH. 'Purchase/assets/css/pur_order_pdf.css' . '"  rel="stylesheet" type="text/css" />';
      return $html;
    }


    /**
     * Gets the pur request pdf html.
     *
     * @param      <type>  $pur_request_id  The pur request identifier
     *
     * @return     string  The pur request pdf html.
     */
    public function get_purorder_pdf_html($pur_order_id){
        

        $pur_order = $this->get_pur_order($pur_order_id);
        $pur_order_detail = $this->get_pur_order_detail($pur_order_id);
        $list_approve_status = $this->get_list_approval_details($pur_order_id,'pur_order');

        $company_name = ''; 

        $company_model =  model("Models\Company_model");
        $company = $company_model->get_details(['is_default' => 1])->getRow();
        if(isset($company->name)){
            $company_name = $company->name;
        }

        $vendor = $this->get_vendor($pur_order->vendor);
        $tax_data = $this->get_html_tax_pur_order($pur_order_id);


        if($pur_order->currency != ''){
            $base_currency = $pur_order->currency;
        }else{
            $base_currency = get_base_currency();
        }

        if($base_currency == get_setting('default_currency')){
            $base_currency = get_setting('currency_symbol');
        }

        $company_address = ''; 
        if(isset($company->address)){
            $company_address = $company->address;
        }

        $vendor_name = '';
        $ship_to = $company_address;
        if($vendor){
          

            $address = $vendor->address.', '.$vendor->country;
            $vendor_name = $vendor->company;
            
        }

        $day = _d($pur_order->order_date);
       
        
    $html = '<table class="table">
        <tbody>
          <tr>
            <td rowspan="6" class="text-left width70" >
            <img src="'.get_pdf_logo_url().'">
             <br>'.company_widget().'
            </td>
            <td class="text-right width30">
                <strong class="fsize20">'.mb_strtoupper(_l('purchase_order')).'</strong><br>
                <strong>'.mb_strtoupper($pur_order->pur_order_number).'</strong><br>
            </td>
          </tr>

          <tr>
            <td class="text-right width30">
                <br><strong>'._l('pur_vendor').'</strong>    
                <br>'. $vendor_name.'
                <br>'. $address.'
            </td>
            <td></td>
          </tr>

          <tr>
            <td></td>
          </tr>
          <tr>
            <td class="text-right width30">
                <br><strong>'._l('pur_ship_to').'</strong>    
                <br>'. $ship_to.'
            </td>
            <td></td>
          </tr>

          <tr>
            <td></td>
          </tr>
          <tr>
            <td class="text-right">'. _l('order_date').': '. $day.'</td>
            <td></td>
          </tr>

        </tbody>
      </table>
      <br><br><br>
      ';

      $html .=  '<table class="table purorder-item">
        <thead>
          <tr>
            <th class="thead-dark width30" >'._l('items').'</th>
            <th class="thead-dark width15" align="right">'._l('purchase_unit_price').'</th>
            <th class="thead-dark width15"  align="right">'._l('purchase_quantity').'</th>';
         
            if(get_setting('show_purchase_tax_column') == 1){ 

                $html .= '<th class="thead-dark width10" align="right">'._l('tax').'</th>';
            }
 
            $html .= '<th class="thead-dark width15" align="right" >'._l('discount').'</th>
            <th class="thead-dark width15" align="right">'._l('total').'</th>
          </tr>
          </thead>
          <tbody>';
        $t_mn = 0;
        $item_discount = 0;
      foreach($pur_order_detail as $row){
        $items = $this->get_items_by_id($row['item_code']);
        $des_html = ($items) ? $items->commodity_code.' - '.$items->title : $row['item_name'];

        $units = $this->get_units_by_id($row['unit_id']);
        $unit_name = isset($units->unit_name) ? $units->unit_name : '';
        
        $html .= '<tr nobr="true" class="sortable">
            <td class="width30" ><strong>'.$des_html.'</strong><br><span>'.$row['description'].'</span></td>
            <td class="width15" align="right">'.to_currency($row['unit_price'],$base_currency).'</td>
            <td  class="width15" align="right">'.to_decimal_format($row['quantity'],0).' '. $unit_name.'</td>';
         
            if(get_setting('show_purchase_tax_column') == 1){  
                $html .= '<td class="width10" align="right" >'.to_currency($row['total'] - $row['into_money'],$base_currency).'</td>';
            }
       
            $html .= '<td class="width15" align="right">'.to_currency($row['discount_money'],$base_currency).'</td>
            <td class="width15" align="right" >'.to_currency($row['total_money'],$base_currency).'</td>
          </tr>';

        $t_mn += $row['total_money'];
        $item_discount += $row['discount_money'];
      }  
      $html .=  '</tbody>
      </table><br><br>';

      $html .= '<table class="table text-right"><tbody>';
      $html .= '<tr id="subtotal">
                    <td class="width33" ></td>
                     <td>'._l('subtotal').' </td>
                     <td class="subtotal">
                        '.to_currency($pur_order->subtotal,$base_currency).'
                     </td>
                  </tr>';

      $html .= $tax_data['pdf_html'];

      if(($pur_order->discount_total + $item_discount) > 0){
        $html .= '
                  
                  <tr id="subtotal">
                  <td class="width33"></td>
                     <td>'._l('discount_total(money)').'</td>
                     <td class="subtotal">
                        '.to_currency(($pur_order->discount_total + $item_discount), $base_currency).'
                     </td>
                  </tr>';
      }

      if($pur_order->shipping_fee  > 0){
        $html .= '
                  
                  <tr id="subtotal">
                  <td class="width33"></td>
                     <td>'._l('pur_shipping_fee').'</td>
                     <td class="subtotal">
                        '.to_currency($pur_order->shipping_fee, $base_currency).'
                     </td>
                  </tr>';
      }
      $html .= '<tr id="subtotal">
                 <td class="width33"></td>
                 <td>'. _l('total').'</td>
                 <td class="subtotal">
                    '. to_currency($pur_order->total, $base_currency).'
                 </td>
              </tr>';

      $html .= ' </tbody></table>';

      $html .= '<div class="col-md-12 mtop15">
                        <h4>'. _l('terms_and_conditions').':</h4><p>'. $pur_order->terms .'</p>
                       
                     </div>';
      if(count($list_approve_status) > 0){
          $html .= '<br>
          <br>
          <br>
          <br>';

          $html .=  '<table class="table">
            <tbody>
              <tr>';

            foreach ($list_approve_status as $value) {
         $html .= '<td class="td_appr">';
            if($value['action'] == 'sign'){
                $html .= '<h3>'.mb_strtoupper(get_staff_full_name1($value['staffid'])).'</h3>';
                if($value['approve'] == 2){ 
                    $html .= '<img src="'.FCPATH.PLUGIN_URL_PATH. 'Purchase/Uploads/pur_order/signature/'.$pur_order->id.'/signature_'.$value['id'].'.png" class="img_style">';
                }
                    
            }else{ 
            $html .= '<h3>'.mb_strtoupper(get_staff_full_name1($value['staffid'])).'</h3>';
                  if($value['approve'] == 2){ 
            $html .= '<img src="'.FCPATH.PLUGIN_URL_PATH .'Purchase/Uploads/approval/approved.png" class="img_style">';
                 }elseif($value['approve'] == 3){
            $html .= '<img src="'.FCPATH.PLUGIN_URL_PATH.'Purchase/Uploads/approval/rejected.png" class="img_style">';
                 }
                  
                    }
           $html .= '</td>';
            }
           
         $html .= '</tr>
            </tbody>
          </table>';
        
    }
      $html .=  '<link href="' . FCPATH. PLUGIN_URL_PATH. 'Purchase/assets/css/pur_order_pdf.css' . '"  rel="stylesheet" type="text/css" />';
      return $html;
    }

    /**
     * Gets the items by identifier.
     *
     * @param      <type>  $id     The identifier
     *
     * @return     <row>  The items by identifier.
     */
    public function get_items_by_id($id){
        $builder = $this->db->table(db_prefix().'items');
        $builder->where('id',$id);
        return $builder->get()->getRow();
    }

    /**
     * Gets the units by identifier.
     *
     * @param      <type>  $id     The identifier
     *
     * @return     <row>  The units by identifier.
     */
    public function get_units_by_id($id){
        $builder = $this->db->table(db_prefix().'ware_unit_type');
        $builder->where('unit_type_id',$id);
        return $builder->get()->getRow();
    }

    /**
     * Sends a mail.
     *
     * @param      <type>  $data   The data
     */
    public function send_mail($data){

        if(!isset($data['status'])){
            $data['status'] = '';
        }
        $get_staff_enter_charge_code = '';
        $mes = 'notify_send_request_approve_project';
        $staff_addedfrom = 0;
        $additional_data = $data['rel_type'];
        $object_type = $data['rel_type'];
        switch ($data['rel_type']) {
            case 'pur_request':
                $type = _l('pur_request');
                $staff_addedfrom = $this->get_purchase_request($data['rel_id'])->requester;
                $additional_data = $this->get_purchase_request($data['rel_id'])->pur_rq_name;
                $list_approve_status = $this->get_list_approval_details($data['rel_id'],$data['rel_type']);
                $mes = 'notify_send_request_approve_pur_request';
                $mes_approve = 'notify_send_approve_pur_request';
                $mes_reject = 'notify_send_rejected_pur_request';
                $link = 'purchase/view_pur_request/' . $data['rel_id'];
                $notify_data = ['pur_request_id' => $data['rel_id']];
                break;

            case 'pur_quotation':
                $type = _l('pur_quotation');
                $staff_addedfrom = $this->get_estimate($data['rel_id'])->addedfrom;
                $additional_data = format_pur_estimate_number($data['rel_id']);
                $list_approve_status = $this->get_list_approval_details($data['rel_id'],$data['rel_type']);
                $mes = 'notify_send_request_approve_pur_quotation';
                $mes_approve = 'notify_send_approve_pur_quotation';
                $mes_reject = 'notify_send_rejected_pur_quotation';
                $link = 'purchase/quotations/' . $data['rel_id'];
                $notify_data = ['pur_quotation_id' => $data['rel_id']];
                break;

            case 'pur_order':
                $type = _l('pur_order');
                $pur_order = $this->get_pur_order($data['rel_id']);
                $staff_addedfrom = $pur_order->addedfrom;
                $additional_data = $pur_order->pur_order_number;
                $list_approve_status = $this->get_list_approval_details($data['rel_id'],$data['rel_type']);
                $mes = 'notify_send_request_approve_pur_order';
                $mes_approve = 'notify_send_approve_pur_order';
                $mes_reject = 'notify_send_rejected_pur_order';
                $link = 'purchase/purchase_order/' . $data['rel_id'];
                $notify_data = ['pur_order_id' => $data['rel_id']];
                break;        
            case 'payment_request':
                $type = _l('payment_request');
                $pur_inv = $this->get_payment_pur_invoice($data['rel_id']);
                $staff_addedfrom = $pur_inv->requester;
                $additional_data = _l('payment_for').' '.get_pur_invoice_number($pur_inv->pur_invoice);
                $list_approve_status = $this->get_list_approval_details($data['rel_id'],$data['rel_type']);
                $mes = 'notify_send_request_approve_pur_inv';
                $mes_approve = 'notify_send_approve_pur_inv';
                $mes_reject = 'notify_send_rejected_pur_inv';
                $link = 'purchase/payment_invoice/' . $data['rel_id'];
                $notify_data = ['pur_payment_id' => $data['rel_id']];
                break;
            default:
                
                break;
        }


        $check_approve_status = $this->check_approval_details($data['rel_id'], $data['rel_type'], $data['status']);
        if(isset($check_approve_status['staffid'])){

            $mail_template = 'send-request-approve';
            $Users_model = model("Models\Users_model");

            if(!in_array(get_staff_user_id1(),$check_approve_status['staffid'])){
                foreach ($check_approve_status['staffid'] as $value) {
                    
                    if($value != ''){
                            $options = array(
                                "id" => $value,
                                "user_type" => "staff",
                            );
                            $staff = $Users_model->get_details($options)->getRow();

                        if($staff){
                        
                            /*Send notify*/
                            $notify_data['to_user_id'] = $staff->id;
                            pur_log_notification($mes, $notify_data, get_staff_user_id1() ,$staff->id);

                            //send mail
                            //get the login details template TODO
                            $subject = app_lang('request_approval');
                            $message = app_lang('email_send_request_approve').' '. $type .' <a href="'.site_url($link).'">'.site_url($link).'</a> '.app_lang('pur_from_staff').' '. get_staff_full_name1($staff_addedfrom);
                            send_app_mail($staff->email, $subject, $message);
                        }
                    }
                }
            }
        }

        if(isset($data['approve'])){
            if($data['approve'] == 2){
                $mes = $mes_approve;
                $mail_template = 'send_approve';
            }else{
                $mes = $mes_reject;
                $mail_template = 'send_rejected';
            }

            
            $Users_model = model("Models\Users_model");
            $options = array(
                "id" => $staff_addedfrom,
                "user_type" => "staff",
            );
            $staff = $Users_model->get_details($options)->getRow();

            // Send notify
            $notify_data['to_user_id'] = $staff->id;
            pur_log_notification($mes, $notify_data, get_staff_user_id1() ,$staff->id);
            
            //send mail
            $subject = app_lang('approval_notification');
            $message = app_lang($mail_template).' '. $type.' <a href="'.site_url($link).'">'.site_url($link).'</a> '.' '._l('pur_by_staff'). ' '.get_staff_full_name1(get_staff_user_id1());
            send_app_mail($staff->email, $subject, $message);

            foreach($list_approve_status as $key => $value){
            $value['staffid'] = explode(', ',$value['staffid']);
                if($value['approve'] == 1 && !in_array(get_staff_user_id1(),$value['staffid'])){
                    foreach ($value['staffid'] as $staffid) {
                      
                        $options = array(
                            "id" => $staffid,
                            "user_type" => "staff",
                        );
                        $staff = $Users_model->get_details($options)->getRow();
                        
                        /*Send notify*/
                        $notify_data['to_user_id'] = $staff->id;
                        pur_log_notification($mes, $notify_data, get_staff_user_id1() ,$staff->id);
                        
                        //send mail
                        $subject = app_lang('approval_notification');
                        $message = app_lang($mail_template).' '. $type.' <a href="'.site_url($link).'">'.site_url($link).'</a> '.' '._l('pur_by_staff').' '. get_staff_full_name1($staff_id);
                        send_app_mail($staff->email, $subject, $message);
                    }
                }
            }
        }
    }

    /**
     * pur create notification
     * @param  [type]  $event      
     * @param  [type]  $user_id    
     * @param  array   $options    
     * @param  integer $to_user_id 
     * @return [type]              
     */
    function pur_create_notification($event, $user_id, $options = array(), $to_user_id = 0) {
        $notification_settings_table = $this->db->prefixTable('notification_settings');
        $users_table = $this->db->prefixTable('users');
        $roles_table = $this->db->prefixTable('roles');
        $clients_table = $this->db->prefixTable('clients');

        $where = "";
        $options = $this->escape_array($options);
        $project_id = get_array_value($options, "project_id");
        $task_id = get_array_value($options, "task_id");
        $leave_id = get_array_value($options, "leave_id");
        $ticket_id = get_array_value($options, "ticket_id");
        $project_comment_id = get_array_value($options, "project_comment_id");
        $ticket_comment_id = get_array_value($options, "ticket_comment_id");
        $project_file_id = get_array_value($options, "project_file_id");
        $post_id = get_array_value($options, "post_id");
        $activity_log_id = get_array_value($options, "activity_log_id");
        $client_id = get_array_value($options, "client_id");
        $invoice_payment_id = get_array_value($options, "invoice_payment_id");
        $invoice_id = get_array_value($options, "invoice_id");
        $estimate_id = get_array_value($options, "estimate_id");
        $order_id = get_array_value($options, "order_id");
        $estimate_request_id = get_array_value($options, "estimate_request_id");
        $actual_message_id = get_array_value($options, "actual_message_id");
        $parent_message_id = get_array_value($options, "parent_message_id");
        $event_id = get_array_value($options, "event_id");
        $announcement_id = get_array_value($options, "announcement_id");
        $exclude_ticket_creator = get_array_value($options, "exclude_ticket_creator");
        $notify_to_admins_only = get_array_value($options, "notify_to_admins_only");
        $notification_multiple_tasks = get_array_value($options, "notification_multiple_tasks");
        $lead_id = get_array_value($options, "lead_id");
        $contract_id = get_array_value($options, "contract_id");
        $proposal_id = get_array_value($options, "proposal_id");
        $estimate_comment_id = get_array_value($options, "estimate_comment_id");


        $inventory_goods_receiving_id = get_array_value($options, "inventory_goods_receiving_id");
        $inventory_goods_delivery_id = get_array_value($options, "inventory_goods_delivery_id");
        $packing_list_id = get_array_value($options, "packing_list_id");
        $internal_delivery_note_id = get_array_value($options, "internal_delivery_note_id");
        $loss_adjustment_is = get_array_value($options, "loss_adjustment_is");
        $receiving_exporting_return_order_id = get_array_value($options, "receiving_exporting_return_order_id");


        $pur_request_id = get_array_value($options, "pur_request_id");
        $pur_quotation_id = get_array_value($options, "pur_quotation_id");
        $pur_order_id = get_array_value($options, "pur_order_id");
        $pur_payment_id = get_array_value($options, "pur_payment_id");


        $to_user_id = get_array_value($options, "to_user_id");


        $extra_data = array();

        $notify_to_terms = array();

        $extra_where = "";


        $exclude_notification_creator = " $users_table.id!=$user_id ";

        $web_notify_to = "";
        $email_notify_to = "";


        $data = array(
            "user_id" => $user_id,
            "description" => "",
            "created_at" => get_current_utc_time(),
            "notify_to" => $to_user_id,
            "read_by" => "",
            "event" => $event, //Subject of notify
            "project_id" => $project_id ? $project_id : "",
            "task_id" => $task_id ? $task_id : "",
            "project_comment_id" => $project_comment_id ? $project_comment_id : "",
            "ticket_id" => $ticket_id ? $ticket_id : "",
            "ticket_comment_id" => $ticket_comment_id ? $ticket_comment_id : "",
            "project_file_id" => $project_file_id ? $project_file_id : "",
            "leave_id" => $leave_id ? $leave_id : "",
            "post_id" => $post_id ? $post_id : "",
            "to_user_id" => $to_user_id ? $to_user_id : "",
            "activity_log_id" => $activity_log_id ? $activity_log_id : "",
            "client_id" => $client_id ? $client_id : "",
            "invoice_payment_id" => $invoice_payment_id ? $invoice_payment_id : "",
            "invoice_id" => $invoice_id ? $invoice_id : "",
            "estimate_request_id" => $estimate_request_id ? $estimate_request_id : "",
            "estimate_id" => $estimate_id ? $estimate_id : "",
            "contract_id" => $contract_id ? $contract_id : "",
            "proposal_id" => $proposal_id ? $proposal_id : "",
            "order_id" => $order_id ? $order_id : "",
            "actual_message_id" => $actual_message_id ? $actual_message_id : "",
            "parent_message_id" => $parent_message_id ? $parent_message_id : "",
            "event_id" => $event_id ? $event_id : "",
            "announcement_id" => $announcement_id ? $announcement_id : "",
            "lead_id" => $lead_id ? $lead_id : "",
            "estimate_comment_id" => $estimate_comment_id ? $estimate_comment_id : "",


            "pur_request_id" => $pur_request_id ? $pur_request_id : "",
            "pur_quotation_id" => $pur_quotation_id ? $pur_quotation_id : "",
            "pur_order_id" => $pur_order_id ? $pur_order_id : "",
            "pur_payment_id" => $pur_payment_id ? $pur_payment_id : "",

        );

        //get data from plugin by persing 'plugin_'
        foreach ($options as $key => $value) {
            if (strpos($key, 'plugin_') !== false) {
                $data[$key] = $value;
            }
        }

        $builder = $this->db->table(get_db_prefix().'notifications');
        $builder->insert($data);
        $notification_id = $this->db->insertID();


        //send push notifications
        if (get_setting("enable_push_notification")) {
            //send push notifications to all web notifiy to users
            //but in receiving portal, it will be checked if the user disable push notification or not
            send_push_notifications($event, $web_notify_to, $user_id, $notification_id);
        }

        //send slack notifications
        if(1 == 2){
            $Notifications_model = model("Models\Notifications_model");
            $Notifications_model->prepare_sending_slack_notification($event, $user_id, $notification_id, $notification_settings, $project_id);
        }

    }


    /**
     *  send request approve
     * @param  array $data
     * @return boolean
     */
    public function send_request_approve($data) {
        if (!isset($data['status'])) {
            $data['status'] = '';
        }

        $date_send = date('Y-m-d H:i:s');
        $data_new = $this->get_approve_setting($data['rel_type'], $data['status']);
        if(!$data_new){
            return false;
        }

        $this->delete_approval_details($data['rel_id'], $data['rel_type']);
        $list = [];
        $staff_addedfrom = $data['addedfrom'];
        $sender = get_staff_user_id1();
        $Users_model = model("Models\Users_model");

        foreach ($data_new as $value) {
            $row = [];

            if ($value->approver !== 'staff') {
                $value->staff_addedfrom = $staff_addedfrom;
                $value->rel_type = $data['rel_type'];
                $value->rel_id = $data['rel_id'];

                $approve_value = $this->get_staff_id_by_approve_value($value, $value->approver);
                if (is_numeric($approve_value)) {
                    /*get Email by User id*/
                    $options = array(
                        "id" => $approve_value,
                        "user_type" => "staff",
                    );
                    $user = $Users_model->get_details($options)->getRow();
                    if($User){
                        $approve_value = $user->email;
                    }else{
                        $approve_value = '';
                    }

                } else {
                    $builder =$this->db->table(get_db_prefix().'pur_approval_details');
                    $builder->where('rel_id', $data['rel_id']);
                    $builder->where('rel_type', $data['rel_type']);
                    $builder->delete();

                    return $value->approver;
                }
                $row['approve_value'] = $approve_value;

                $staffid = $this->get_staff_id_by_approve_value($value, $value->approver);

                if (empty($staffid)) {
                    $builder = $this->db->table(get_db_prefix().'pur_approval_details');
                    $builder->where('rel_id', $data['rel_id']);
                    $builder->where('rel_type', $data['rel_type']);
                    $builder->delete();

                    return $value->approver;
                }

                $row['action'] = $value->action;
                $row['staffid'] = $staffid;
                $row['date_send'] = $date_send;
                $row['rel_id'] = $data['rel_id'];
                $row['rel_type'] = $data['rel_type'];
                $row['sender'] = $sender;
                $builder = $this->db->table(get_db_prefix().'pur_approval_details');
                $builder->insert($row);

            } else if ($value->approver == 'staff') {
                $row['action'] = $value->action;
                $row['staffid'] = $value->staff;
                $row['date_send'] = $date_send;
                $row['rel_id'] = $data['rel_id'];
                $row['rel_type'] = $data['rel_type'];
                $row['sender'] = $sender;

                $builder = $this->db->table(get_db_prefix().'pur_approval_details');
                $builder->insert($row);
            }
        }
        return true;
    }

    /**
     * delete approval details
     * @param  integer $rel_id
     * @param  string $rel_type
     * @return  boolean
     */
    public function delete_approval_details($rel_id, $rel_type) {
        $builder = $this->db->table(get_db_prefix().'pur_approval_details');
        $builder->where('rel_id', $rel_id);
        $builder->where('rel_type', $rel_type);
        $affected_rows = $builder->delete();
        if ($affected_rows > 0) {
            return true;
        }
        return false;
    }

    /**
     * get staff id by approve value
     * @param  array $data
     * @param  integer $approve_value
     * @return boolean
     */
    public function get_staff_id_by_approve_value($data, $approve_value) {
        $list = [];
        $staffid = [];

        if ($approve_value == 'department_manager') {
            $staffid = $this->departments_model->get_staff_departments($data->staff_addedfrom)[0]['manager_id'];
        } elseif ($approve_value == 'direct_manager') {
            $staffid = $this->staff_model->get($data->staff_addedfrom)->team_manage;
        }

        return $staffid;
    }

    /**
     * { update approve request }
     *
     * @param      <type>   $rel_id    The relative identifier
     * @param      <type>   $rel_type  The relative type
     * @param      <type>   $status    The status
     *
     * @return     boolean
     */
    public function update_approve_request($rel_id , $rel_type, $status){ 
        $data_update = [];
        
        switch ($rel_type) {
            case 'pur_request':
                $builder = $this->db->table(db_prefix().'pur_request');

                $data_update['status'] = $status;
                $this->update_item_pur_request($rel_id);
                $builder->where('id', $rel_id);
                $builder->update($data_update);
                return true;
                break;
            case 'pur_quotation':
                $builder = $this->db->table(db_prefix().'pur_estimates');

                $data_update['status'] = $status;
                $builder->where('id', $rel_id);
                $builder->update($data_update);
                return true;
                break;
            case 'pur_order':
                $builder = $this->db->table(db_prefix().'pur_orders');
                $data_update['approve_status'] = $status;
                $builder->where('id', $rel_id);
                $builder->update($data_update);

                // warehouse module hook after purchase order approve
                app_hooks()->do_action('after_purchase_order_approve', $rel_id);

                return true;
                break;

            case 'payment_request':
                $data_update['approval_status'] = $status;

                $builder = $this->db->table(db_prefix().'pur_invoice_payment');
                $builder->where('id', $rel_id);
                $builder->update($data_update);

                $this->update_invoice_after_approve($rel_id);

                return true;
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * { update item pur request }
     *
     * @param      $id     The identifier
     */
    public function update_item_pur_request($id){
        $pur_rq = $this->get_purchase_request($id);
        if($pur_rq){
            $builder = $this->db->table(db_prefix().'pur_request');
            $builder->where('id',$id);
            $builder->update(['from_items' => 1]);

            $pur_rqdt = $this->get_pur_request_detail($id);
            if(count($pur_rqdt) > 0){
                foreach($pur_rqdt as $rqdt){
                    if($rqdt['item_text'] != '' && ($rqdt['item_code'] == '' || $rqdt['item_code'] == null) ){
                        $item_data['description'] = $rqdt['item_text'];
                        $item_data['purchase_price'] = $rqdt['unit_price'];
                        $item_data['unit_id'] = $rqdt['unit_id'];
                        $item_data['rate'] = '';
                        $item_data['sku_code'] = '';
                        $item_data['commodity_barcode'] = $this->generate_commodity_barcode();
                        $item_data['commodity_code'] = $this->generate_commodity_barcode();
                        $item_id = $this->add_commodity_one_item($item_data);

                        $dt_builder = $this->db->table(db_prefix().'pur_request_detail');
                        $dt_builder->where('prd_id',$rqdt['prd_id']);
                        $dt_builder->update(['item_code' => $item_id]);
                    }
                }
            }
            
        }
    }

    /**
     *  update approval details
     * @param  integer $id
     * @param  array $data
     * @return boolean
     */
    public function update_approval_details($id, $data) {
        $data['date'] = date('Y-m-d H:i:s');
        $builder = $this->db->table(get_db_prefix().'pur_approval_details');
        $builder->where('id', $id);
        $affected_rows = $builder->update($data);
        if ($affected_rows > 0) {
            return true;
        }
        return false;
    }

    /**
     * Adds an attachment to database.
     *
     * @param        $rel_id      The relative identifier
     * @param        $rel_type    The relative type
     * @param        $attachment  The attachment
     * @param      bool    $external    The external
     *
     * @return     <type>  
     */
    public function add_attachment_to_database($rel_id, $rel_type, $attachment, $external = false)
    {
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['rel_id']    = $rel_id;
        if (!isset($attachment[0]['staffid'])) {
            $data['staffid'] = get_staff_user_id1();
        } else {
            $data['staffid'] = $attachment[0]['staffid'];
        }

        if (isset($attachment[0]['task_comment_id'])) {
            $data['task_comment_id'] = $attachment[0]['task_comment_id'];
        }

        $data['rel_type'] = $rel_type;

        if (isset($attachment[0]['contact_id'])) {
            $data['contact_id']          = $attachment[0]['contact_id'];
            $data['visible_to_customer'] = 1;
            if (isset($data['staffid'])) {
                unset($data['staffid']);
            }
        }

        $data['attachment_key'] = app_generate_hash();

        if ($external == false) {
            $data['file_name'] = $attachment[0]['file_name'];
            $data['filetype']  = $attachment[0]['filetype'];
        } else {
            $path_parts            = pathinfo($attachment[0]['name']);
            $data['file_name']     = $attachment[0]['name'];
            $data['external_link'] = $attachment[0]['link'];
            $data['filetype']      = !isset($attachment[0]['mime']) ? get_mime_by_extension('.' . $path_parts['extension']) : $attachment[0]['mime'];
            $data['external']      = $external;
            if (isset($attachment[0]['thumbnailLink'])) {
                $data['thumbnail_link'] = $attachment[0]['thumbnailLink'];
            }
        }

        $builder = $this->db->table(db_prefix().'files');
        $builder->insert($data);
        $insert_id = $this->db->insertID();

        return $insert_id;
    }

    /**
     * Gets the purchase order attachments.
     *
     * @param      <type>  $id     The purchase order
     *
     * @return     <type>  The purchase order attachments.
     */
    public function get_purchase_request_attachments($id){
   
        $builder = $this->db->table(db_prefix().'files');
        $builder->where('rel_id',$id);
        $builder->where('rel_type','pur_request');
        return $builder->get()->getResultArray();
    }

    /**
     * Gets the file.
     *
     * @param      <type>   $id      The file id
     * @param      boolean  $rel_id  The relative identifier
     *
     * @return     boolean  The file.
     */
    public function get_file($id, $rel_id = false)
    {   
        $builder = $this->db->table(db_prefix().'files');
        $builder->where('id', $id);
        $file = $builder->get()->getRow();

        if ($file && $rel_id) {
            if ($file->rel_id != $rel_id) {
                return false;
            }
        }
        return $file;
    }

    /**
     * Gets the part attachments.
     *
     * @param      <type>  $surope  The surope
     * @param      string  $id      The identifier
     *
     * @return     <type>  The part attachments.
     */
    public function get_purrequest_attachments($surope, $id = '')
    {
        $builder = $this->db->table(db_prefix().'files');
        // If is passed id get return only 1 attachment
        if (is_numeric($id)) {
            $builder->where('id', $id);
        } 
        $builder->where('rel_type', 'pur_request');
        $result = $builder->get();
        if (is_numeric($id)) {
            return $result->getRow();
        }

        return $result->getResultArray();
    }

    /**
     * { delete purorder attachment }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean 
     */
    public function delete_purrequest_attachment($id)
    {
        $attachment = $this->get_purrequest_attachments('', $id);
        $deleted    = false;

        $builder = $this->db->table(db_prefix().'files');

        $builder->where('id', $id);
        $builder->delete();        
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_request/'. $attachment->rel_id . '/' . $attachment->file_name);
            }
            $builder->where('id', $attachment->id);
            $affected_rows = $builder->delete();
            if ($affected_rows > 0) {
                $deleted = true;
            }

            if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_request/'. $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_request/'. $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_request/'. $attachment->rel_id);
                }
            }
        }

        return true;
    }

    /**
     * Sends to vendors.
     */
    public function send_to_vendors($id, $data){
        $builder = $this->db->table('pur_request');

        $share_to_vendor = implode(',', $data['send_to_vendors']);

        $builder->where('id', $id);
        $affected_rows = $builder->update(['send_to_vendors' => $share_to_vendor]);

        if($affected_rows > 0){
            return true;
        }
        return false;
    }

    /**
     * Gets the pur request by status.
     *
     * @param      <type>  $status  The status
     *
     * @return     <array>  The pur request by status.
     */
    public function get_pur_request_by_status($status){
        $builder = $this->db->table(db_prefix().'pur_request');
        $builder->where('status',$status);
        return $builder->get()->getResultArray();
    }

    /**
     * Creates a quotation row template.
     *
     * @param      string      $name            The name
     * @param      string      $item_name       The item name
     * @param      int|string  $quantity        The quantity
     * @param      string      $unit_name       The unit name
     * @param      int|string  $unit_price      The unit price
     * @param      string      $taxname         The taxname
     * @param      string      $item_code       The item code
     * @param      string      $unit_id         The unit identifier
     * @param      string      $tax_rate        The tax rate
     * @param      string      $total_money     The total money
     * @param      string      $discount        The discount
     * @param      string      $discount_money  The discount money
     * @param      string      $total           The total
     * @param      string      $into_money      Into money
     * @param      string      $tax_id          The tax identifier
     * @param      string      $tax_value       The tax value
     * @param      string      $item_key        The item key
     * @param      bool        $is_edit         Indicates if edit
     *
     * @return     string      
     */
    public function create_quotation_row_template($name = '', $item_name = '', $quantity = '', $unit_name = '', $unit_price = '', $taxname = '',  $item_code = '', $unit_id = '', $tax_rate = '', $total_money = '', $discount = '', $discount_money = '', $total = '', $into_money = '', $tax_id = '', $tax_value = '', $item_key = '',$is_edit = false, $currency_rate = 1, $to_currency = '') {
        
        $row = '';

        $name_item_code = 'item_code';
        $name_item_name = 'item_name';
        $name_unit_id = 'unit_id';
        $name_unit_name = 'unit_name';
        $name_quantity = 'quantity';
        $name_unit_price = 'unit_price';
        $name_tax_id_select = 'tax_select';
        $name_tax_id = 'tax_id';
        $name_total = 'total';
        $name_tax_rate = 'tax_rate';
        $name_tax_name = 'tax_name';
        $name_tax_value = 'tax_value';
        $array_attr = [];
        $array_attr_payment = ['data-payment' => 'invoice'];
        $name_into_money = 'into_money';
        $name_discount = 'discount';
        $name_discount_money = 'discount_money';
        $name_total_money = 'total_money';

        $array_available_quantity_attr = [ 'min' => '0.0', 'step' => 'any', 'readonly' => true];
        $array_qty_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_rate_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_discount_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_discount_money_attr = [ 'min' => '0.0', 'step' => 'any'];
        $str_rate_attr = 'min="0.0" step="any"';

        $array_subtotal_attr = ['readonly' => true];
        $text_right_class = 'text-right';

        if ($name == '') {
            $tax_rate_class = ' refresh_tax1';
            $row .= '<tr class="main">
                  <td></td>';
            $vehicles = [];
            $array_attr = ['placeholder' => _l('unit_price')];
         
            $manual             = true;
            $invoice_item_taxes = '';
            $amount = '';
            $sub_total = 0;

        } else {
            $tax_rate_class = ' refresh_tax2';
            $row .= '<tr class="sortable item">
                    <td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
            $name_item_code = $name . '[item_code]';
            $name_item_name = $name . '[item_name]';
            $name_unit_id = $name . '[unit_id]';
            $name_unit_name = '[unit_name]';
            $name_quantity = $name . '[quantity]';
            $name_unit_price = $name . '[unit_price]';
            $name_tax_id_select = $name . '[tax_select][]';
            $name_tax_id = $name . '[tax_id]';
            $name_total = $name . '[total]';
            $name_tax_rate = $name . '[tax_rate]';
            $name_tax_name = $name .'[tax_name]';
            $name_into_money = $name .'[into_money]';
            $name_discount = $name .'[discount]';
            $name_discount_money = $name .'[discount_money]';
            $name_total_money = $name . '[total_money]';
            $name_tax_value = $name. '[tax_value]';
      
           
            $array_qty_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantity];
            

            $array_rate_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('rate')];
            $array_discount_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];

            $array_discount_money_attr = ['onblur' => 'pur_calculate_total(1);', 'onchange' => 'pur_calculate_total(1);', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];

            $manual             = false;

            $tax_money = 0;
            $tax_rate_value = 0;

            if($is_edit){
                $invoice_item_taxes = pur_convert_item_taxes($tax_id, $tax_rate, $taxname);
                $arr_tax_rate = explode('|', $tax_rate);
                foreach ($arr_tax_rate as $key => $value) {
                    $tax_rate_value += (float)$value;
                }
            }else{
                $invoice_item_taxes = $taxname;
                $tax_rate_data = $this->pur_get_tax_rate($taxname);
                $tax_rate_value = $tax_rate_data['tax_rate'];
            }

            if((float)$tax_rate_value != 0){
                $tax_money = (float)$unit_price * (float)$quantity * (float)$tax_rate_value / 100;
                $goods_money = (float)$unit_price * (float)$quantity + (float)$tax_money;
                $amount = (float)$unit_price * (float)$quantity + (float)$tax_money;
            }else{
                $goods_money = (float)$unit_price * (float)$quantity;
                $amount = (float)$unit_price * (float)$quantity;
            }

            $sub_total = (float)$unit_price * (float)$quantity;
            $amount = $amount;

        }
 

        $row .= '<td class="">' . render_textarea1($name_item_name, '', $item_name, ['rows' => 2, 'placeholder' => _l('pur_item_name'), 'readonly' => true] ) . '</td>';

        $row .= '<td class="rate">' . render_input1($name_unit_price, '', $unit_price, 'number', $array_rate_attr, [], 'no-margin', $text_right_class);
        if( $unit_price != ''){
            $original_price = round( ($unit_price/$currency_rate), 2);
            $base_currency = get_base_currency();
            if($to_currency != 0 && $to_currency != $base_currency){
                $row .= render_input('original_price', '',to_currency($original_price, $base_currency), 'text', ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => _l('original_price'), 'disabled' => true], [], 'no-margin', 'input-transparent text-right pur_input_none');
            }

            $row .= '<input class="hide" name="og_price" disabled="true" value="'.$original_price.'">';
        }

        $row .=  '</td>';
       
        $row .= '<td class="quantities">' . 
        render_input1($name_quantity, '', $quantity, 'number', $array_qty_attr, [], 'no-margin', $text_right_class) . 
        render_input1($name_unit_name, '', $unit_name, 'text', ['placeholder' => _l('unit'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right pur_input_none').
        '</td>';
        $row .= '<td class="into_money">' . $into_money . '</td>';
        
        $row .= '<td class="taxrate '.$tax_rate_class.'">' . $this->get_taxes_dropdown_template($name_tax_id_select, $invoice_item_taxes, 'invoice', $item_key, true, $manual) . '</td>';

        $row .= '<td class="tax_value">' . render_input1($name_tax_value, '', $tax_value, 'number', $array_subtotal_attr, [], '', $text_right_class) . '</td>';

        $row .= '<td class="_total" align="right">' . $total . '</td>';

        if($discount_money > 0){
            $discount = '';
        }

        $row .= '<td class="discount">' . render_input1($name_discount, '', $discount, 'number', $array_discount_attr, [], '', $text_right_class) . '</td>';
        $row .= '<td class="discount_money" align="right">' . render_input1($name_discount_money, '', $discount_money, 'number', $array_discount_money_attr, [], '', $text_right_class.' item_discount_money') . '</td>';
        $row .= '<td class="label_total_after_discount" align="right">' . $total_money . '</td>';

        $row .= '<td class="hide commodity_code">' . render_input1($name_item_code, '', $item_code, 'text', ['placeholder' => _l('commodity_code')]) . '</td>';
        $row .= '<td class="hide unit_id">' . render_input1($name_unit_id, '', $unit_id, 'text', ['placeholder' => _l('unit_id')]) . '</td>';

        $row .= '<td class="hide _total_after_tax">' . render_input1($name_total, '', $total, 'number', []) . '</td>';

        $row .= '<td class="hide total_after_discount">' . render_input1($name_total_money, '', $total_money, 'number', []) . '</td>';
        $row .= '<td class="hide _into_money">' . render_input1($name_into_money, '', $into_money, 'number', []) . '</td>';

        if ($name == '') {
            $row .= '<td><button type="button" onclick="pur_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info text-white"><i data-feather="plus-circle" class="icon-16"></i></button></td>';
        } else {
            $row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="pur_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i data-feather="x" class="icon-16"></i></a></td>';
        }
        $row .= '</tr>';
        return $row;
    }

    /**
     * Gets the items by vendor variations.
     *
     * @return       The items.
     */
    public function get_items_by_vendor_variation($vendor){
       $arr_value = $this->db->query('select * from ' . db_prefix() . 'items where deleted = 0 AND id not in ( SELECT distinct parent_id from '.db_prefix().'items WHERE parent_id is not null AND parent_id != "0" ) AND id IN ( select items from '.db_prefix().'pur_vendor_items where vendor = '.$vendor.' ) order by id desc')->getResultArray();
        return $this->item_to_variation($arr_value);
    }

    /**
     * { item to variation }
     *
     * @param        $array_value  The array value
     *
     * @return     array   
     */
    public function item_to_variation($array_value)
    {
        $new_array=[];
        foreach ($array_value as $key =>  $values) {

            $name = '';
            if($values['attributes'] != null && $values['attributes'] != ''){
                $attributes_decode = json_decode($values['attributes']);

                foreach ($attributes_decode as $n_value) {
                    if(is_array($n_value)){
                        foreach ($n_value as $n_n_value) {
                            if(strlen($name) > 0){
                                $name .= '#'.$n_n_value->name.' ( '.$n_n_value->option.' ) ';
                            }else{
                                $name .= ' #'.$n_n_value->name.' ( '.$n_n_value->option.' ) ';
                            }
                        }
                    }else{

                        if(strlen($name) > 0){
                            $name .= '#'.$n_value->name.' ( '.$n_value->option.' ) ';
                        }else{
                            $name .= ' #'.$n_value->name.' ( '.$n_value->option.' ) ';
                        }
                    }
                }


            }
            array_push($new_array, [
                'id' => $values['id'],
                'label' => $values['commodity_code'].'_'.$values['title'],

            ]);
        }
        return $new_array;
    }

    /**
     * { estimate by vendor }
     *
     * @param      <type>  $vendor  The vendor
     *
     * @return     <array>  ( list estimate by vendor )
     */
    public function estimate_by_vendor($vendor){
        $builder = $this->db->table(db_prefix().'pur_estimates');
        $builder->where('vendor',$vendor);
        $builder->where('status', 2);
        return $builder->get()->getResultArray();
    }

    /**
     * Adds an estimate.
     *
     * @param      <type>   $data   The data
     *
     * @return     boolean  or in estimate
     */
    public function add_estimate($data)
    {   

        unset($data['item_select']);
        unset($data['item_name']);
        unset($data['total']);
        unset($data['quantity']);
        unset($data['unit_price']);
        unset($data['unit_name']);
        unset($data['item_code']);
        unset($data['unit_id']);
        unset($data['discount']);
        unset($data['into_money']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['discount_money']);
        unset($data['total_money']);
        unset($data['additional_discount']);
        unset($data['tax_value']);

        $check_appr = $this->get_approve_setting('pur_quotation');
        $data['status'] = 1;
        if($check_appr && $check_appr != false){
            $data['status'] = 1;
        }else{
            $data['status'] = 2;
        }

        $data['to_currency'] = $data['currency'];

        $data['date'] = to_sql_date1($data['date']);
        $data['expirydate'] = to_sql_date1($data['expirydate']);

        $data['datecreated'] = date('Y-m-d H:i:s');

        $data['addedfrom'] = get_staff_user_id();

        $data['prefix'] = get_setting('pur_estimate_prefix');

        $data['number_format'] = 1;

        $builder = $this->db->table(db_prefix().'pur_estimates');

        $builder->where('prefix',$data['prefix']);
        $builder->where('number',$data['number']);
        $check_exist_number = $builder->get()->getRow();

        while($check_exist_number) {
          $data['number'] = $data['number'] + 1;
          
          $builder->where('prefix',$data['prefix']);
          $builder->where('number',$data['number']);
          $check_exist_number = $builder->get()->getRow();
        }

        $save_and_send = isset($data['save_and_send']);

        $data['hash'] = app_generate_hash();

        $data = $this->map_shipping_columns($data);

        $es_detail = [];
        if (isset($data['newitems'])) {
            $es_detail = $data['newitems'];
            unset($data['newitems']);
        }

        if (isset($data['shipping_street'])) {
            $data['shipping_street'] = trim($data['shipping_street']);
            $data['shipping_street'] = nl2br($data['shipping_street']);
        }

        if(isset($data['dc_total'])){
            $data['discount_total'] = $data['dc_total'];
            unset($data['dc_total']);
        }

        if(isset($data['dc_percent'])){
            $data['discount_percent'] = $data['dc_percent'];
            unset($data['dc_percent']);
        }
        
        if(isset($data['total_mn'])){
            $data['subtotal'] = $data['total_mn'];
            unset($data['total_mn']);
        }

        if(isset($data['grand_total'])){
            $data['total'] = $data['grand_total'];
            unset($data['grand_total']);
        }

        

        $builder->insert($data);
        $insert_id = $this->db->insertID();

        if ($insert_id) {
            $next_number = $data['number']+1;
            update_setting('next_pur_pur_estimate_number', $next_number);

            $total = [];
            $total['total_tax'] = 0;
    
            if(count($es_detail) > 0){
                foreach($es_detail as $key => $rqd){
   
                    $dt_data = [];
                    $dt_data['pur_estimate'] = $insert_id;
                    $dt_data['item_code'] = $rqd['item_code'];
                    $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                    $dt_data['unit_price'] = $rqd['unit_price'];
                    $dt_data['into_money'] = $rqd['into_money'];
                    $dt_data['total'] = $rqd['total'];
                    $dt_data['tax_value'] = $rqd['tax_value'];
                    $dt_data['item_name'] = $rqd['item_name'];
                    $dt_data['total_money'] = $rqd['total_money'];
                    $dt_data['discount_money'] = $rqd['discount_money'];
                    $dt_data['discount_%'] = $rqd['discount'];

                    $tax_money = 0;
                    $tax_rate_value = 0;
                    $tax_rate = null;
                    $tax_id = null;
                    $tax_name = null;

                    if(isset($rqd['tax_select'])){
                        $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                        $tax_rate_value = $tax_rate_data['tax_rate'];
                        $tax_rate = $tax_rate_data['tax_rate_str'];
                        $tax_id = $tax_rate_data['tax_id_str'];
                        $tax_name = $tax_rate_data['tax_name_str'];
                    }

                    $dt_data['tax'] = $tax_id;
                    $dt_data['tax_rate'] = $tax_rate;
                    $dt_data['tax_name'] = $tax_name;

                    $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                    $dt_builder = $this->db->table(db_prefix().'pur_estimate_detail');
                    $dt_builder->insert( $dt_data);


                    $total['total_tax'] += $rqd['tax_value'];
                }
            }

            $builder->where('id',$insert_id);
            $builder->update($total);

            return $insert_id;
        }

        return false;
    }

    /**
     * { function_description }
     *
     * @param      <type>  $data   The data
     *
     * @return     <array> data
     */
    private function map_shipping_columns($data)
    {
        if (!isset($data['include_shipping'])) {
            foreach ($this->shipping_fields as $_s_field) {
                if (isset($data[$_s_field])) {
                    $data[$_s_field] = null;
                }
            }
            $data['show_shipping_on_estimate'] = 1;
            $data['include_shipping']          = 0;
        } else {
            $data['include_shipping'] = 1;
            // set by default for the next time to be checked
            if (isset($data['show_shipping_on_estimate']) && ($data['show_shipping_on_estimate'] == 1 || $data['show_shipping_on_estimate'] == 'on')) {
                $data['show_shipping_on_estimate'] = 1;
            } else {
                $data['show_shipping_on_estimate'] = 0;
            }
        }

        return $data;
    }

    /**
     * Gets the estimate.
     *
     * @param      string  $id     The identifier
     * @param      array   $where  The where
     *
     * @return     <row , array>  The estimate, list estimate.
     */
    public function get_estimate($id = '', $where = [])
    {
        $builder = $this->db->table(db_prefix().'pur_estimates');
        $builder->select('*,' . db_prefix() . 'pur_estimates.id as id, '.db_prefix().'pur_estimates.currency as currency ');
        $builder->where($where);
        if (is_numeric($id)) {
            $builder->where(db_prefix() . 'pur_estimates.id', $id);
            $estimate = $builder->get()->getRow();
            if ($estimate) {
                
                $estimate->visible_attachments_to_customer_found = false;
                
                

                if ($estimate->pur_request != 0) {
                   
                    $estimate->pur_request = $this->get_purchase_request($estimate->pur_request);
                }else{
                    $estimate->pur_request = '';
                }


            }

            return $estimate;
        }
        $builder->order_by('number,YEAR(date)', 'desc');

        return $builder->get()->getResultArray();
    }

    /**
     * Gets the html tax pur estimate.
     */
    public function get_html_tax_pur_estimate($id){
        $html = '';
        $preview_html = '';
        $pdf_html = '';
        $taxes = [];
        $t_rate = [];
        $tax_val = [];
        $tax_val_rs = [];
        $tax_name = [];
        $rs = [];

        $base_currency = get_base_currency();
        $base_currency_symbol = get_setting('currency_symbol');

        $estimate = $this->get_estimate($id);
        if($estimate->currency != $base_currency){
            $base_currency_symbol = $estimate->currency;
        }

        $builder = $this->db->table(db_prefix().'pur_estimate_detail');
        $builder->where('pur_estimate', $id);
        $details = $builder->get()->getResultArray();

        foreach($details as $row){
            if($row['tax'] != ''){
                $tax_arr = explode('|', $row['tax']);

                $tax_rate_arr = [];
                if($row['tax_rate'] != ''){
                    $tax_rate_arr = explode('|', $row['tax_rate']);
                }

                foreach($tax_arr as $k => $tax_it){
                    if(!isset($tax_rate_arr[$k]) ){
                        $tax_rate_arr[$k] = $this->tax_rate_by_id($tax_it);
                    }

                    if(!in_array($tax_it, $taxes)){
                        $taxes[$tax_it] = $tax_it;
                        $t_rate[$tax_it] = $tax_rate_arr[$k];
                        $tax_name[$tax_it] = $this->get_tax_name($tax_it).' ('.$tax_rate_arr[$k].'%)';
                    }
                }
            }
        }

        if(count($tax_name) > 0){
            foreach($tax_name as $key => $tn){
                $tax_val[$key] = 0;
                foreach($details as $row_dt){
                    if(!(strpos($row_dt['tax'], $taxes[$key]) === false)){
                        $tax_val[$key] += ($row_dt['into_money']*$t_rate[$key]/100);
                    }
                }
                $pdf_html .= '<tr id="subtotal"><td width="33%"></td><td>'.$tn.'</td><td>'.to_currency($tax_val[$key], $base_currency_symbol).'</td></tr>';
                $preview_html .= '<tr id="subtotal"><td>'.$tn.'</td><td>'.to_currency($tax_val[$key], $base_currency_symbol).'</td><tr>';
                $html .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.to_currency($tax_val[$key], $base_currency_symbol).'</td></tr>';
                $tax_val_rs[] = $tax_val[$key];
            }
        }
        
        $rs['pdf_html'] = $pdf_html;
        $rs['preview_html'] = $preview_html;
        $rs['html'] = $html;
        $rs['taxes'] = $taxes;
        $rs['taxes_val'] = $tax_val_rs;
        return $rs;
    }


    /**
     * Gets the pur estimate detail.
     *
     * @param      <int>  $pur_request  The pur request
     *
     * @return     <array>  The pur estimate detail.
     */
    public function get_pur_estimate_detail($pur_request){
        $builder = $this->db->table(db_prefix().'pur_estimate_detail');

        $builder->where('pur_estimate',$pur_request);
        $estimate_details = $builder->get()->getResultArray();

        foreach($estimate_details as $key => $detail){
            $estimate_details[$key]['discount_money'] = (float) $detail['discount_money'];
            $estimate_details[$key]['into_money'] = (float) $detail['into_money'];
            $estimate_details[$key]['total'] = (float) $detail['total'];
            $estimate_details[$key]['total_money'] = (float) $detail['total_money'];
            $estimate_details[$key]['unit_price'] = (float) $detail['unit_price'];
            $estimate_details[$key]['tax_value'] = (float) $detail['tax_value'];
        }

        return $estimate_details;
    }

     /**
     * { update estimate }
     *
     * @param      <type>   $data   The data
     * @param      <type>   $id     The identifier
     *
     * @return     boolean  
     */
    public function update_estimate($data, $id)
    {
        $data['date'] = to_sql_date1($data['date']);
        $data['expirydate'] = to_sql_date1($data['expirydate']);
        $affectedRows = 0;

        $new_quote = [];
        if (isset($data['newitems'])) {
            $new_quote = $data['newitems'];
            unset($data['newitems']);
        }

        $update_quote = [];
        if (isset($data['items'])) {
            $update_quote = $data['items'];
            unset($data['items']);
        }

        $remove_quote = [];
        if (isset($data['removed_items'])) {
            $remove_quote = $data['removed_items'];
            unset($data['removed_items']);
        }

        $data['to_currency'] = $data['currency'];

        unset($data['item_select']);
        unset($data['item_name']);
        unset($data['total']);
        unset($data['quantity']);
        unset($data['unit_price']);
        unset($data['unit_name']);
        unset($data['item_code']);
        unset($data['unit_id']);
        unset($data['discount']);
        unset($data['into_money']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['discount_money']);
        unset($data['total_money']);
        unset($data['additional_discount']);
        unset($data['tax_value']);

        $data['number'] = trim($data['number']);

        $original_estimate = $this->get_estimate($id);

        $original_status = $original_estimate->status;

        $original_number = $original_estimate->number;

        $original_number_formatted = format_pur_estimate_number($id);

        $data = $this->map_shipping_columns($data);
        
        unset($data['isedit']);


        if(isset($data['total_mn'])){
            $data['subtotal'] = $data['total_mn'];
            unset($data['total_mn']);
        }

        if(isset($data['grand_total'])){
            $data['total'] = $data['grand_total'];
            unset($data['grand_total']);
        }

        if(isset($data['dc_total'])){
            $data['discount_total'] = $data['dc_total'];
            unset($data['dc_total']);
        }

        $builder = $this->db->table(db_prefix().'pur_estimates');

        $builder->where('id', $id);
        $affect = $builder->update( $data);

        if ($affect > 0) {
            
            $affectedRows++;
        }

        if(count($new_quote) > 0){
            foreach($new_quote as $key => $rqd){

                $dt_data = [];
                $dt_data['pur_estimate'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_name'] = $rqd['item_name'];
                $dt_data['total_money'] = $rqd['total_money'];
                $dt_data['discount_money'] = $rqd['discount_money'];
                $dt_data['discount_%'] = $rqd['discount'];

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                $dt_builder = $this->db->table(db_prefix().'pur_estimate_detail');

                $dt_builder->insert($dt_data);
                $new_quote_insert_id = $this->db->insertID();
                if($new_quote_insert_id){
                    $affectedRows++;
                }
                
            }

        }

        if(count($update_quote) > 0){
            foreach($update_quote as $_key => $rqd){
                $dt_data = [];
                $dt_data['pur_estimate'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_name'] = $rqd['item_name'];
                $dt_data['total_money'] = $rqd['total_money'];
                $dt_data['discount_money'] = $rqd['discount_money'];
                $dt_data['discount_%'] = $rqd['discount'];

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                $dt_builder = $this->db->table(db_prefix().'pur_estimate_detail');

                $dt_builder->where('id', $rqd['id']);
                $aff = $dt_builder->update( $dt_data);
                if($aff > 0){
                    $affectedRows++;
                }
            }
        }

        if(count($remove_quote) > 0){ 
            foreach($remove_quote as $remove_id){
                $dt_builder = $this->db->table(db_prefix().'pur_estimate_detail');
                $dt_builder->where('id', $remove_id);
                if ($dt_builder->delete()) {
                    $affectedRows++;
                }
            }
        }

        $quote_detail_after_update = $this->get_pur_estimate_detail($id);
        $total = [];
        $total['total_tax'] = 0;
        if(count($quote_detail_after_update) > 0){
            foreach($quote_detail_after_update as $dt){
                $total['total_tax'] += $dt['tax_value'];
            }
        }

        $builder->where('id',$id);
        $affected = $builder->update($total);
        if ($affected > 0) {
            $affectedRows++;
        }
        
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    /**
     * Gets the purchase estimate attachments.
     *
     * @param        $id     The purchase estimate
     *
     * @return       The purchase estimate attachments.
     */
    public function get_purchase_estimate_attachments($id){
    
        $builder = $this->db->table('files');
        $builder->where('rel_id',$id);
        $builder->where('rel_type','pur_estimate');
        return $builder->get()->getResultArray();
    }

    /**
     * { change status pur estimate }
     *
     * @param      <type>   $status  The status
     * @param      <type>   $id      The identifier
     *
     * @return     boolean   
     */
    public function change_status_pur_estimate($status,$id){
        $builder = $this->db->table('pur_estimates');

        $builder->where('id',$id);
        $affected = $builder->update(['status' => $status]);
        if($affected > 0){
            return true;
        }
        return false;
    }

    /**
     * Gets the pur request detail in estimate.
     *
     * @param      <int>  $pur_request  The pur request
     *
     * @return     <array>  The pur request detail in estimate.
     */
    public function get_pur_request_detail_in_estimate($pur_request){
        
        $pur_request_lst = $this->db->query('SELECT item_code, prq.unit_id as unit_id, unit_price, quantity, into_money, long_description as description, prq.tax as tax, tax_name, tax_rate, item_text, tax_value, total as total_money, total as total FROM '.db_prefix().'pur_request_detail prq LEFT JOIN '.db_prefix().'items it ON prq.item_code = it.id WHERE prq.pur_request = '.$pur_request)->getResultArray();

        foreach($pur_request_lst as $key => $detail){
            $pur_request_lst[$key]['into_money'] = (float) $detail['into_money'];
            $pur_request_lst[$key]['total'] = (float) $detail['total'];
            $pur_request_lst[$key]['total_money'] = (float) $detail['total_money'];
            $pur_request_lst[$key]['unit_price'] = (float) $detail['unit_price'];
            $pur_request_lst[$key]['tax_value'] = (float) $detail['tax_value'];
        }

        return $pur_request_lst;
    }

    /**
     * { function_description }
     */
    public function delete_pur_estimate($id){

        $affected_rows = 0;

        $builder = $this->db->table('pur_estimates');

        $builder->where('id', $id);
        $aff = $builder->delete();
        if($aff > 0){
            $affected_rows++;
        }

        $dt_builder = $this->db->table('pur_estimate_detail');
        $dt_builder->where('pur_estimate', $id);
        $dt_aff = $dt_builder->delete();
        if($dt_aff > 0){
            $affected_rows++;
        }

        $attachment = $this->get_purchase_estimate_attachments($id);
        if(count($attachment) > 0){
            foreach($attachment as $item){
                if($this->delete_estimate_attachment($item['id'])){
                    $affected_rows++;
                }
            }
        }


        $file_builder = $this->db->table(db_prefix().'files');
        $file_builder->where('rel_id', $id);
        $file_builder->where('rel_type', 'pur_estimate');
        $file_aff = $file_builder->delete();
        if($file_aff > 0){
            $affected_rows++;
        }

        if($affected_rows > 0){
            return true;
        }
        return false;
    }

    /**
     * { delete estimate attachment }
     *
     * @param         $id     The identifier
     *
     * @return     boolean 
     */
    public function delete_estimate_attachment($id)
    {
        $attachment = $this->get_estimate_attachments('', $id);
        $deleted    = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_estimate/'. $attachment->rel_id . '/' . $attachment->file_name);
            }
            $builder = $this->db->table(db_prefix().'files');

            $builder->where('id', $attachment->id);
            $aff = $builder->delete();
            if ($aff > 0) {
                $deleted = true;
            }

            if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_estimate/'. $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_estimate/'. $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_estimate/'. $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * Gets the purcahse estimate attachments.
     *
     * @param      <type>  $surope  The surope
     * @param      string  $id      The identifier
     *
     * @return     <type>  The part attachments.
     */
    public function get_estimate_attachments($surope, $id = '')
    {
        // If is passed id get return only 1 attachment
        $builder = $this->db->table(db_prefix().'files');
        if (is_numeric($id)) {
            $builder->where('id', $id);
        } else {
            $builder->where('rel_id', $assets);
        }
        $builder->where('rel_type', 'pur_estimate');
        $result = $builder->get();
        if (is_numeric($id)) {
            return $result->getRow();
        }

        return $result->getResultArray();
    }

    /**
     * Gets the estimates by status.
     *
     * @param      <type>  $status  The status
     *
     * @return     <array>  The estimates by status.
     */
    public function get_estimates_by_status($status){
        $builder = $this->db->table(db_prefix().'pur_estimates');
        $builder->where('status',$status);
        return $builder->get()->getResultArray();
    }

    /**
     * Creates a purchase order row template.
     *
     * @param      string      $name              The name
     * @param      string      $item_name         The item name
     * @param      string      $item_description  The item description
     * @param      int|string  $quantity          The quantity
     * @param      string      $unit_name         The unit name
     * @param      int|string  $unit_price        The unit price
     * @param      string      $taxname           The taxname
     * @param      string      $item_code         The item code
     * @param      string      $unit_id           The unit identifier
     * @param      string      $tax_rate          The tax rate
     * @param      string      $total_money       The total money
     * @param      string      $discount          The discount
     * @param      string      $discount_money    The discount money
     * @param      string      $total             The total
     * @param      string      $into_money        Into money
     * @param      string      $tax_id            The tax identifier
     * @param      string      $tax_value         The tax value
     * @param      string      $item_key          The item key
     * @param      bool        $is_edit           Indicates if edit
     *
     * @return     string      
     */
    public function create_purchase_order_row_template($name = '', $item_name = '', $item_description = '', $quantity = '', $unit_name = '', $unit_price = '', $taxname = '',  $item_code = '', $unit_id = '', $tax_rate = '', $total_money = '', $discount = '', $discount_money = '', $total = '', $into_money = '', $tax_id = '', $tax_value = '', $item_key = '',$is_edit = false, $currency_rate = 1, $to_currency = '') {
        
    
        $row = '';

        $name_item_code = 'item_code';
        $name_item_name = 'item_name';
        $name_item_description = 'description';
        $name_unit_id = 'unit_id';
        $name_unit_name = 'unit_name';
        $name_quantity = 'quantity';
        $name_unit_price = 'unit_price';
        $name_tax_id_select = 'tax_select';
        $name_tax_id = 'tax_id';
        $name_total = 'total';
        $name_tax_rate = 'tax_rate';
        $name_tax_name = 'tax_name';
        $name_tax_value = 'tax_value';
        $array_attr = [];
        $array_attr_payment = ['data-payment' => 'invoice'];
        $name_into_money = 'into_money';
        $name_discount = 'discount';
        $name_discount_money = 'discount_money';
        $name_total_money = 'total_money';

        $array_available_quantity_attr = [ 'min' => '0.0', 'step' => 'any', 'readonly' => true];
        $array_qty_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_rate_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_discount_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_discount_money_attr = [ 'min' => '0.0', 'step' => 'any'];
        $str_rate_attr = 'min="0.0" step="any"';

        $array_subtotal_attr = ['readonly' => true];
        $text_right_class = 'text-right';

        if ($name == '') {
            $tax_rate_class = ' refresh_tax1';
            $row .= '<tr class="main">
                  <td></td>';
            $vehicles = [];
            $array_attr = ['placeholder' => _l('unit_price')];
         
            $manual             = true;
            $invoice_item_taxes = '';
            $amount = '';
            $sub_total = 0;

        } else {
            $tax_rate_class = ' refresh_tax2';
            $row .= '<tr class="sortable item">
                    <td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
            $name_item_code = $name . '[item_code]';
            $name_item_name = $name . '[item_name]';
            $name_item_description = $name . '[item_description]';
            $name_unit_id = $name . '[unit_id]';
            $name_unit_name = '[unit_name]';
            $name_quantity = $name . '[quantity]';
            $name_unit_price = $name . '[unit_price]';
            $name_tax_id_select = $name . '[tax_select][]';
            $name_tax_id = $name . '[tax_id]';
            $name_total = $name . '[total]';
            $name_tax_rate = $name . '[tax_rate]';
            $name_tax_name = $name .'[tax_name]';
            $name_into_money = $name .'[into_money]';
            $name_discount = $name .'[discount]';
            $name_discount_money = $name .'[discount_money]';
            $name_total_money = $name . '[total_money]';
            $name_tax_value = $name. '[tax_value]';
      
           
            $array_qty_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantity];
            

            $array_rate_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('rate')];
            $array_discount_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];

            $array_discount_money_attr = ['onblur' => 'pur_calculate_total(1);', 'onchange' => 'pur_calculate_total(1);', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];


            $manual             = false;

            $tax_money = 0;
            $tax_rate_value = 0;

            if($is_edit){
                $invoice_item_taxes = pur_convert_item_taxes($tax_id, $tax_rate, $taxname);
                $arr_tax_rate = explode('|', $tax_rate);
                foreach ($arr_tax_rate as $key => $value) {
                    $tax_rate_value += (float)$value;
                }
            }else{
                $invoice_item_taxes = $taxname;
                $tax_rate_data = $this->pur_get_tax_rate($taxname);
                $tax_rate_value = $tax_rate_data['tax_rate'];
            }

            if((float)$tax_rate_value != 0){
                $tax_money = (float)$unit_price * (float)$quantity * (float)$tax_rate_value / 100;
                $goods_money = (float)$unit_price * (float)$quantity + (float)$tax_money;
                $amount = (float)$unit_price * (float)$quantity + (float)$tax_money;
            }else{
                $goods_money = (float)$unit_price * (float)$quantity;
                $amount = (float)$unit_price * (float)$quantity;
            }

            $sub_total = (float)$unit_price * (float)$quantity;
            $amount = to_decimal_format($amount);

        }
 

        $row .= '<td class="">' . render_textarea1($name_item_name, '', $item_name, ['rows' => 2, 'placeholder' => _l('pur_item_name'), 'readonly' => true] ) . '</td>';

        $row .= '<td class="">' . render_textarea1($name_item_description, '', $item_description, ['rows' => 2, 'placeholder' => _l('item_description')] ) . '</td>';

        $row .= '<td class="rate">' . render_input1($name_unit_price, '', $unit_price, 'number', $array_rate_attr, [], 'no-margin', $text_right_class);

        if( $unit_price != ''){
            $original_price = round( ($unit_price/$currency_rate), 2);
            $base_currency = get_base_currency();
            if($to_currency != '' && $to_currency != $base_currency){
                $row .= render_input1('original_price', '',to_currency($original_price, $base_currency), 'text', ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => _l('original_price'), 'disabled' => true], [], 'no-margin', 'input-transparent text-right pur_input_none');
            }

            $row .= '<input class="hide" name="og_price" disabled="true" value="'.$original_price.'">';
        }
       
        $row .= '<td class="quantities">' . 
        render_input1($name_quantity, '', $quantity, 'number', $array_qty_attr, [], 'no-margin', $text_right_class) . 
        render_input1($name_unit_name, '', $unit_name, 'text', ['placeholder' => _l('unit'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right pur_input_none').
        '</td>';
        
        $row .= '<td class="taxrate '.$tax_rate_class.'">' . $this->get_taxes_dropdown_template($name_tax_id_select, $invoice_item_taxes, 'invoice', $item_key, true, $manual) . '</td>';

        $row .= '<td class="tax_value">' . render_input1($name_tax_value, '', $tax_value, 'number', $array_subtotal_attr, [], '', $text_right_class) . '</td>';

        $row .= '<td class="_total" align="right">' . $total . '</td>';

        if($discount_money > 0){
            $discount = '';
        }

        $row .= '<td class="discount">' . render_input1($name_discount, '', $discount, 'number', $array_discount_attr, [], '', $text_right_class) . '</td>';
        $row .= '<td class="discount_money" align="right">' . render_input1($name_discount_money, '', $discount_money, 'number', $array_discount_money_attr, [], '', $text_right_class.' item_discount_money') . '</td>';
        $row .= '<td class="label_total_after_discount" align="right">' . $total_money . '</td>';

        $row .= '<td class="hide commodity_code">' . render_input1($name_item_code, '', $item_code, 'text', ['placeholder' => _l('commodity_code')]) . '</td>';
        $row .= '<td class="hide unit_id">' . render_input1($name_unit_id, '', $unit_id, 'text', ['placeholder' => _l('unit_id')]) . '</td>';

        $row .= '<td class="hide _total_after_tax">' . render_input1($name_total, '', $total, 'number', []) . '</td>';


        $row .= '<td class="hide total_after_discount">' . render_input1($name_total_money, '', $total_money, 'number', []) . '</td>';
        $row .= '<td class="hide _into_money">' . render_input1($name_into_money, '', $into_money, 'number', []) . '</td>';

        if ($name == '') {
            $row .= '<td><button type="button" onclick="pur_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info text-white"><i data-feather="plus-circle" class="icon-16"></i></button></td>';
        } else {
            $row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="pur_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i data-feather="x" class="icon-16"></i></a></td>';
        }

        $row .= '</tr>';
        return $row;
    }

    /**
     * Adds a pur order.
     *
     * @param      <array>   $data   The data
     *
     * @return     boolean , int id purchase order
     */
    public function add_pur_order($data){

        unset($data['item_select']);
        unset($data['item_name']);
        unset($data['description']);
        unset($data['total']);
        unset($data['quantity']);
        unset($data['unit_price']);
        unset($data['unit_name']);
        unset($data['item_code']);
        unset($data['unit_id']);
        unset($data['discount']);
        unset($data['into_money']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['discount_money']);
        unset($data['total_money']);
        unset($data['additional_discount']);
        unset($data['tax_value']);

        $check_appr = $this->get_approve_setting('pur_order');
        $data['approve_status'] = 1;
        if($check_appr && $check_appr != false){
            $data['approve_status'] = 1;
        }else{
            $data['approve_status'] = 2;
        }

        $data['to_currency'] = $data['currency'];

        $order_detail = [];
        if(isset($data['newitems'])){
            $order_detail = $data['newitems'];
            unset($data['newitems']);
        }

        $prefix = get_setting('pur_order_prefix');

        $builder = $this->db->table('pur_orders');

        $builder->where('pur_order_number',$data['pur_order_number']);
        $check_exist_number = $builder->get()->getRow();

        while($check_exist_number) {
          $data['number'] = $data['number'] + 1;
          $data['pur_order_number'] =  $prefix.'-'.str_pad($data['number'],5,'0',STR_PAD_LEFT).'-'.date('M-Y').'-'.get_vendor_company_name($data['vendor']);
          if(get_setting('po_only_prefix_and_number') == 1){
            $data['pur_order_number'] =  $prefix.'-'.str_pad($data['number'],5,'0',STR_PAD_LEFT);
          }

          $builder->where('pur_order_number',$data['pur_order_number']);
          $check_exist_number = $builder->get()->getRow();
        }

        $data['order_date'] = to_sql_date1($data['order_date']);

        if($data['delivery_date'] != ''){
            $data['delivery_date'] = to_sql_date1($data['delivery_date']);
        }else{
           $data['delivery_date'] = null; 
        }


        $data['datecreated'] = date('Y-m-d H:i:s');

        $data['addedfrom'] = get_staff_user_id();

        $data['hash'] = app_generate_hash();

        $data['order_status'] = 'new';

        if(isset($data['clients']) && count($data['clients']) > 0){
            $data['clients'] = implode(',', $data['clients']);
        } 

        if(isset($data['order_discount'])){
            $order_discount = $data['order_discount'];
            if($data['add_discount_type'] == 'percent'){
                $data['discount_percent'] = $order_discount;
            }

            unset($data['order_discount']);
        }

        unset($data['add_discount_type']);

        if(isset($data['dc_total'])){
            $data['discount_total'] = $data['dc_total'];
            unset($data['dc_total']);
        }

        if(isset($data['total_mn'])){
            $data['subtotal'] = $data['total_mn'];
            unset($data['total_mn']);
        }

        if(isset($data['grand_total'])){
            $data['total'] = $data['grand_total'];
            unset($data['grand_total']);
        }

        $builder->insert($data);
        $insert_id = $this->db->insertID();
        if ($insert_id) {
            // Update next purchase order number in settings
            $next_number = $data['number']+1;
            update_setting('next_purchase_order_number', $next_number);

            $total = [];
            $total['total_tax'] = 0;
            
            if(count($order_detail) > 0){
                foreach($order_detail as $key => $rqd){ 
                    $dt_data = [];
                    $dt_data['pur_order'] = $insert_id;
                    $dt_data['item_code'] = $rqd['item_code'];
                    $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                    $dt_data['unit_price'] = $rqd['unit_price'];
                    $dt_data['into_money'] = $rqd['into_money'];
                    $dt_data['total'] = $rqd['total'];
                    $dt_data['tax_value'] = $rqd['tax_value'];
                    $dt_data['item_name'] = $rqd['item_name'];
                    $dt_data['description'] = $rqd['item_description'];
                    $dt_data['total_money'] = $rqd['total_money'];
                    $dt_data['discount_money'] = $rqd['discount_money'];
                    $dt_data['discount_%'] = $rqd['discount'];

                    $tax_money = 0;
                    $tax_rate_value = 0;
                    $tax_rate = null;
                    $tax_id = null;
                    $tax_name = null;

                    if(isset($rqd['tax_select'])){
                        $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                        $tax_rate_value = $tax_rate_data['tax_rate'];
                        $tax_rate = $tax_rate_data['tax_rate_str'];
                        $tax_id = $tax_rate_data['tax_id_str'];
                        $tax_name = $tax_rate_data['tax_name_str'];
                    }

                    $dt_data['tax'] = $tax_id;
                    $dt_data['tax_rate'] = $tax_rate;
                    $dt_data['tax_name'] = $tax_name;

                    $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                    $dt_builder = $this->db->table(db_prefix().'pur_order_detail');
                    $dt_builder->insert($dt_data);

                    $total['total_tax'] += $rqd['tax_value'];
                }
            }


            $builder->where('id',$insert_id);
            $builder->update($total);

            // warehouse module hook after purchase order add
            app_hooks()->do_action('after_purchase_order_add', $insert_id);

            return $insert_id;
        }

        return false;
    }


    /**
     * { update pur order }
     *
     * @param      <type>   $data   The data
     * @param      <type>   $id     The identifier
     *
     * @return     boolean 
     */
    public function update_pur_order($data, $id)
    {
        $affectedRows = 0;

        unset($data['item_select']);
        unset($data['item_name']);
        unset($data['description']);
        unset($data['total']);
        unset($data['quantity']);
        unset($data['unit_price']);
        unset($data['unit_name']);
        unset($data['item_code']);
        unset($data['unit_id']);
        unset($data['discount']);
        unset($data['into_money']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['discount_money']);
        unset($data['total_money']);
        unset($data['additional_discount']);
        unset($data['tax_value']);

        unset($data['isedit']);

        $new_order = [];
        if(isset($data['newitems'])){
            $new_order = $data['newitems'];
            unset($data['newitems']);
        }

        $update_order = [];
        if(isset($data['items'])) {
            $update_order = $data['items'];
            unset($data['items']);
        }

        $remove_order = [];
        if(isset($data['removed_items'])){
            $remove_order = $data['removed_items'];
            unset($data['removed_items']);
        }

        $data['to_currency'] = $data['currency'];

        $prefix = get_setting('pur_order_prefix');
        $data['pur_order_number'] = $data['pur_order_number'];

        $data['order_date'] = to_sql_date1($data['order_date']);

        if($data['delivery_date'] != ''){
            $data['delivery_date'] = to_sql_date1($data['delivery_date']);
        }else{
           $data['delivery_date'] = null; 
        }

        $data['datecreated'] = date('Y-m-d H:i:s');

        $data['addedfrom'] = get_staff_user_id();

        if(isset($data['clients']) && count($data['clients']) > 0){
            $data['clients'] = implode(',', $data['clients']);
        }

        if(isset($data['order_discount'])){
            $order_discount = $data['order_discount'];
            if($data['add_discount_type'] == 'percent'){
                $data['discount_percent'] = $order_discount;
            }

            unset($data['order_discount']);
        }

        unset($data['add_discount_type']);

        if(isset($data['dc_total'])){
            $data['discount_total'] = $data['dc_total'];
            unset($data['dc_total']);
        }

        if(isset($data['total_mn'])){
            $data['subtotal'] = $data['total_mn'];
            unset($data['total_mn']);
        }

        if(isset($data['grand_total'])){
            $data['total'] = $data['grand_total'];
            unset($data['grand_total']);
        }

        $builder = $this->db->table(db_prefix().'pur_orders');
        $builder->where('id', $id);
        $aff = $builder->update($data);

        if ($aff > 0) {
            $affectedRows++;
        }

        if(count($new_order) > 0){
            foreach($new_order as $key => $rqd){

                $dt_data = [];
                $dt_data['pur_order'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_name'] = $rqd['item_name'];
                $dt_data['total_money'] = $rqd['total_money'];
                $dt_data['discount_money'] = $rqd['discount_money'];
                $dt_data['discount_%'] = $rqd['discount'];
                $dt_data['description'] = $rqd['item_description'];

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                $dt_builder = $this->db->table(db_prefix().'pur_order_detail');
                $dt_builder->insert($dt_data);
                $new_quote_insert_id = $this->db->insertID();
                if($new_quote_insert_id){
                    $affectedRows++;
                }
                
            }

        }

        if(count($update_order) > 0){
            foreach($update_order as $_key => $rqd){
                $dt_data = [];
                $dt_data['pur_order'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_name'] = $rqd['item_name'];
                $dt_data['total_money'] = $rqd['total_money'];
                $dt_data['discount_money'] = $rqd['discount_money'];
                $dt_data['discount_%'] = $rqd['discount'];
                $dt_data['description'] = $rqd['item_description'];

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                $dt_builder = $this->db->table(db_prefix().'pur_order_detail');
                $dt_builder->where('id', $rqd['id']);
                $aff = $dt_builder->update($dt_data);
                if($aff > 0){
                    $affectedRows++;
                }
            }
        }

        if(count($remove_order) > 0){ 
            foreach($remove_order as $remove_id){

                $dt_builder = $this->db->table(db_prefix().'pur_order_detail');
                $dt_builder->where('id', $remove_id);
                if ($dt_builder->delete()) {
                    $affectedRows++;
                }
            }
        }

        $order_detail_after_update = $this->get_pur_order_detail($id);
        $total = [];
        $total['total_tax'] = 0;
        if(count($order_detail_after_update) > 0){
            foreach($order_detail_after_update as $dt){
                $total['total_tax'] += $dt['tax_value'];
            }
        }
        
        $builder->where('id',$id);
        $affect = $builder->update($total);
        if ($affect > 0) {
            $affectedRows++;
        }
        
        if ($affectedRows > 0) {
           

            return true;
        }

        return false;
    }

    /**
     * Gets the pur order detail.
     *
     * @param      <int>  $pur_request  The pur request
     *
     * @return     <array>  The pur order detail.
     */
    public function get_pur_order_detail($pur_request){

        $builder = $this->db->table(db_prefix().'pur_order_detail');
        $builder->where('pur_order',$pur_request);
        $pur_order_details = $builder->get()->getResultArray();

        foreach($pur_order_details as $key => $detail){
            $pur_order_details[$key]['discount_money'] = (float) $detail['discount_money'];
            $pur_order_details[$key]['into_money'] = (float) $detail['into_money'];
            $pur_order_details[$key]['total'] = (float) $detail['total'];
            $pur_order_details[$key]['total_money'] = (float) $detail['total_money'];
            $pur_order_details[$key]['unit_price'] = (float) $detail['unit_price'];
            $pur_order_details[$key]['tax_value'] = (float) $detail['tax_value'];
        }

        return $pur_order_details;
    }

    /**
     * Gets the pur order.
     *
     * @param      <int>  $id     The identifier
     *
     * @return     <row>  The pur order.
     */
    public function get_pur_order($id){

        $builder = $this->db->table(db_prefix().'pur_orders');
        $builder->where('id',$id);
        return $builder->get()->getRow();
    }

     /**
     * Gets the html tax pur order.
     */
    public function get_html_tax_pur_order($id){
        $html = '';
        $preview_html = '';
        $pdf_html = '';
        $taxes = [];
        $t_rate = [];
        $tax_val = [];
        $tax_val_rs = [];
        $tax_name = [];
        $rs = [];

        $base_currency = get_base_currency();
        $base_currency_symbol = get_setting('currency_symbol');

        $pur_order = $this->get_pur_order($id);
        if($pur_order->currency != $base_currency){
            $base_currency_symbol = $pur_order->currency;
        }

        $builder = $this->db->table(db_prefix().'pur_order_detail');
        $builder->where('pur_order', $id);
        $details = $builder->get()->getResultArray();

        foreach($details as $row){
            if($row['tax'] != ''){
                $tax_arr = explode('|', $row['tax']);

                $tax_rate_arr = [];
                if($row['tax_rate'] != ''){
                    $tax_rate_arr = explode('|', $row['tax_rate']);
                }

                foreach($tax_arr as $k => $tax_it){
                    if(!isset($tax_rate_arr[$k]) ){
                        $tax_rate_arr[$k] = $this->tax_rate_by_id($tax_it);
                    }

                    if(!in_array($tax_it, $taxes)){
                        $taxes[$tax_it] = $tax_it;
                        $t_rate[$tax_it] = $tax_rate_arr[$k];
                        $tax_name[$tax_it] = $this->get_tax_name($tax_it).' ('.$tax_rate_arr[$k].'%)';
                    }
                }
            }
        }

        if(count($tax_name) > 0){
            foreach($tax_name as $key => $tn){
                $tax_val[$key] = 0;
                foreach($details as $row_dt){
                    if(!(strpos($row_dt['tax'], $taxes[$key]) === false)){
                        $tax_val[$key] += ($row_dt['into_money']*$t_rate[$key]/100);
                    }
                }
                $pdf_html .= '<tr id="subtotal"><td width="33%"></td><td>'.$tn.'</td><td>'.to_currency($tax_val[$key], $base_currency_symbol).'</td></tr>';
                $preview_html .= '<tr id="subtotal"><td>'.$tn.'</td><td>'.to_currency($tax_val[$key], $base_currency_symbol).'</td><tr>';
                $html .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.to_currency($tax_val[$key], $base_currency_symbol).'</td></tr>';
                $tax_val_rs[] = $tax_val[$key];
            }
        }
        
        $rs['pdf_html'] = $pdf_html;
        $rs['preview_html'] = $preview_html;
        $rs['html'] = $html;
        $rs['taxes'] = $taxes;
        $rs['taxes_val'] = $tax_val_rs;
        return $rs;
    }

    /**
     * Gets the purchase order attachments.
     *
     * @param      <type>  $id     The purchase order
     *
     * @return     <type>  The purchase order attachments.
     */
    public function get_purchase_order_attachments($id){
        $builder = $this->db->table(db_prefix().'files');
        $builder->where('rel_id',$id);
        $builder->where('rel_type','pur_order');
        return $builder->get()->getResultArray();
    }

     /**
     * { delete purorder attachment }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean 
     */
    public function delete_purorder_attachment($id)
    {
        $attachment = $this->get_purorder_attachments('', $id);
        $deleted    = false;

        $builder = $this->db->table('files');
             
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_order/'. $attachment->rel_id . '/' . $attachment->file_name);
            }
            $builder->where('id', $attachment->id);
            $aff = $builder->delete();
            if ($aff > 0) {
                $deleted = true;
            }

            if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_order/'. $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_order/'. $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_order/'. $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * Gets the part attachments.
     *
     * @param      <type>  $surope  The surope
     * @param      string  $id      The identifier
     *
     * @return     <type>  The part attachments.
     */
    public function get_purorder_attachments($surope, $id = '')
    {
        // If is passed id get return only 1 attachment
        $builder = $this->db->table(db_prefix().'files');
        if (is_numeric($id)) {
            $builder->where('id', $id);
        } else {
            $builder->where('rel_id', $assets);
        }
        $builder->where('rel_type', 'pur_order');
        $result = $builder->get();
        if (is_numeric($id)) {
            return $result->getRow();
        }

        return $result->getResultArray();
    }

    /**
     * { change status pur order }
     *
     * @param      <type>   $status  The status
     * @param      <type>   $id      The identifier
     *
     * @return     boolean  ( description_of_the_return_value )
     */
    public function change_status_pur_order($status,$id){
        $builder = $this->db->table(db_prefix().'pur_orders');
        $builder->where('id',$id);
        $aff = $builder->update(['approve_status' => $status]);
        if($aff > 0){

            app_hooks()->apply_filters('create_goods_receipt',['status' => $status,'id' => $id]);
            return true;
        }
        return false;
    }

    /**
     * { mark_pur_order_as }
     *
     * @param      string  $status     The status
     * @param      <type>  $pur_order  The pur order
     *
     * @return     bool    
     */
    public function mark_pur_order_as($status, $pur_order){

        $builder = $this->db->table(db_prefix().'pur_orders');
        $builder->where('id', $pur_order);
        $aff = $builder->update( ['order_status' => $status]);
        if ($aff > 0) {
            if($status == 'delivered'){
                $builder->where('id', $pur_order);
                $builder->update(['delivery_status' => 1, 'delivery_date' => date('Y-m-d')]);
            }else{
                $builder->where('id', $pur_order);
                $builder->update(['delivery_status' => 0]);
            }

            return true;
        }
    }


    /**
     * { function_description }
     */
    public function delete_pur_order($id){

        $affected_rows = 0;

        $builder = $this->db->table('pur_orders');

        $builder->where('id', $id);
        $aff = $builder->delete();
        if($aff > 0){
            $affected_rows++;
        }

        $dt_builder = $this->db->table('pur_order_detail');
        $dt_builder->where('pur_order', $id);
        $dt_aff = $dt_builder->delete();
        if($dt_aff > 0){
            $affected_rows++;
        }

        $attachment = $this->get_purchase_order_attachments($id);
        if(count($attachment) > 0){
            foreach($attachment as $item){
                if($this->delete_purorder_attachment($item['id'])){
                    $affected_rows++;
                }
            }
        }


        $file_builder = $this->db->table(db_prefix().'files');
        $file_builder->where('rel_id', $id);
        $file_builder->where('rel_type', 'pur_order');
        $file_aff = $file_builder->delete();
        if($file_aff > 0){
            $affected_rows++;
        }

        if($affected_rows > 0){
            return true;
        }
        return false;
    }

    /**
     * { mark converted purchase order }
     */
    public function mark_converted_purchase_order($pur_order_id, $expense_id){
        $builder =  $this->db->table('pur_orders');

        $builder->where('id', $pur_order_id);
        $aff = $builder->update(['expense_convert' => $expense_id]);
        if($aff > 0){
            return true;
        }

        return false;
    }

    /**
     * Gets the pur request detail in po.
     *
     * @param      <int>  $pur_request  The pur request
     *
     * @return     <array>  The pur request detail in po.
     */
    public function get_pur_request_detail_in_po($pur_request){
        
        $pur_request_lst = $this->db->query('SELECT item_code, prq.unit_id as unit_id, unit_price, quantity, into_money, long_description as description, prq.tax as tax, tax_name, tax_rate, item_text, tax_value, total as total_money, total as total FROM '.db_prefix().'pur_request_detail prq LEFT JOIN '.db_prefix().'items it ON prq.item_code = it.id WHERE prq.pur_request = '.$pur_request)->getResultArray();

        foreach($pur_request_lst as $key => $detail){
            $pur_request_lst[$key]['into_money'] = (float) $detail['into_money'];
            $pur_request_lst[$key]['total'] = (float) $detail['total'];
            $pur_request_lst[$key]['total_money'] = (float) $detail['total_money'];
            $pur_request_lst[$key]['unit_price'] = (float) $detail['unit_price'];
            $pur_request_lst[$key]['tax_value'] = (float) $detail['tax_value'];
        }

        return $pur_request_lst;
    }

    /**
     * Gets the estimate html by pr vendor.
     *
     * @param        $pur_request  The pur request
     * @param      string  $vendor       The vendor
     *
     * @return     string  The estimate html by pr vendor.
     */
    public function get_estimate_html_by_pr_vendor($pur_request, $vendor = '')
    {

        $builder = $this->db->table(db_prefix().'pur_estimates');
        $builder->where('pur_request', $pur_request);
        $builder->where('status', 2);
        if($vendor != ''){
            $builder->where('vendor', $vendor);
        }

        $estimates = $builder->get()->getResultArray();

        $html = '<option value=""></option>';
        foreach($estimates as $es){
            $html .= '<option value="'.$es['id'].'">'.format_pur_estimate_number($es['id']).'</option>';
        }

        return $html;
    }

    /**
     * Gets the pur estimate detail in order.
     *
     * @param      <int>  $pur_estimate  The pur estimate
     *
     * @return     <array>  The pur estimate detail in order.
     */
    public function get_pur_estimate_detail_in_order($pur_estimate){
        $estimates = $this->db->query('SELECT * FROM '.db_prefix().'pur_estimate_detail prq WHERE prq.pur_estimate = '.$pur_estimate)->getResultArray();

        foreach($estimates as $key => $detail){
            $estimates[$key]['discount_money'] = (float) $detail['discount_money'];
            $estimates[$key]['into_money'] = (float) $detail['into_money'];
            $estimates[$key]['total'] = (float) $detail['total'];
            $estimates[$key]['total_money'] = (float) $detail['total_money'];
            $estimates[$key]['unit_price'] = (float) $detail['unit_price'];
            $estimates[$key]['tax_value'] = (float) $detail['tax_value'];
        }

        return $estimates;
    }

    /**
     * Gets the list pur orders.
     *
     * @return       The list pur orders.
     */
    public function get_list_pur_orders(){
        $builder = $this->db->table(db_prefix().'pur_orders');
        return $builder->get()->getResultArray();
    }

    /**
     * Gets the pur order approved.
     *
     * @return     <array>  The pur order approved.
     */
    public function get_pur_order_approved(){
        $builder = $this->db->table(db_prefix().'pur_orders');
        $builder->where('approve_status', 2);
        return $builder->get()->getResultArray();
    }

    /**
     * Gets the pur invoice.
     *
     * @param      string  $id     The identifier
     *
     * @return       The pur invoice.
     */
    public function get_pur_invoice($id = ''){
         $builder = $this->db->table(db_prefix().'pur_invoices');

        if($id != ''){
            $builder->where('id',$id);
            return $builder->get()->getRow();
        }else{
            return $builder->get()->getResultArray();
        }
    }

    /**
     * Gets the pur order detail.
     *
     * @param      <int>  $pur_request  The pur request
     *
     * @return     <array>  The pur order detail.
     */
    public function get_pur_invoice_detail($pur_request){
        $builder = $this->db->table(db_prefix().'pur_invoice_details');
        $builder->where('pur_invoice',$pur_request);
        $pur_invoice_details = $builder->get()->getResultArray();

        foreach($pur_invoice_details as $key => $detail){
            $pur_invoice_details[$key]['discount_money'] = (float) $detail['discount_money'];
            $pur_invoice_details[$key]['into_money'] = (float) $detail['into_money'];
            $pur_invoice_details[$key]['total'] = (float) $detail['total'];
            $pur_invoice_details[$key]['total_money'] = (float) $detail['total_money'];
            $pur_invoice_details[$key]['unit_price'] = (float) $detail['unit_price'];
            $pur_invoice_details[$key]['tax_value'] = (float) $detail['tax_value'];
        }

        return $pur_invoice_details;
    }

    /**
     * get pur order approved for inv
     *
     * @return       The pur order approved.
     */
    public function get_pur_order_approved_for_inv(){
        $builder = $this->db->table(db_prefix().'pur_orders');
        $builder->where('approve_status', 2);
        $list_po = $builder->get()->getResultArray();
        $data_rs = [];
        if(count($list_po) > 0){
            foreach($list_po as $po){

                $inv_builder = $this->db->table(db_prefix().'pur_invoices');
                $inv_builder->where('pur_order', $po['id']);
                $list_inv = $inv_builder->get()->getResultArray();
                $total_inv_value = 0;
                foreach($list_inv as $inv){
                    $total_inv_value += $inv['total'];
                }

                if($total_inv_value < $po['total']){
                    $data_rs[] = $po;
                }
            }    
        }
        
        return $data_rs;
    }

     /**
     * Creates a purchase invoice row template.
     *
     * @param      string      $name              The name
     * @param      string      $item_name         The item name
     * @param      string      $item_description  The item description
     * @param      int|string  $quantity          The quantity
     * @param      string      $unit_name         The unit name
     * @param      int|string  $unit_price        The unit price
     * @param      string      $taxname           The taxname
     * @param      string      $item_code         The item code
     * @param      string      $unit_id           The unit identifier
     * @param      string      $tax_rate          The tax rate
     * @param      string      $total_money       The total money
     * @param      string      $discount          The discount
     * @param      string      $discount_money    The discount money
     * @param      string      $total             The total
     * @param      string      $into_money        Into money
     * @param      string      $tax_id            The tax identifier
     * @param      string      $tax_value         The tax value
     * @param      string      $item_key          The item key
     * @param      bool        $is_edit           Indicates if edit
     *
     * @return     string      
     */
    public function create_purchase_invoice_row_template($name = '', $item_name = '', $item_description = '', $quantity = '', $unit_name = '', $unit_price = '', $taxname = '',  $item_code = '', $unit_id = '', $tax_rate = '', $total_money = '', $discount = '', $discount_money = '', $total = '', $into_money = '', $tax_id = '', $tax_value = '', $item_key = '',$is_edit = false, $currency_rate = 1, $to_currency = '') {
        
    
        $row = '';

        $name_item_code = 'item_code';
        $name_item_name = 'item_name';
        $name_item_description = 'description';
        $name_unit_id = 'unit_id';
        $name_unit_name = 'unit_name';
        $name_quantity = 'quantity';
        $name_unit_price = 'unit_price';
        $name_tax_id_select = 'tax_select';
        $name_tax_id = 'tax_id';
        $name_total = 'total';
        $name_tax_rate = 'tax_rate';
        $name_tax_name = 'tax_name';
        $name_tax_value = 'tax_value';
        $array_attr = [];
        $array_attr_payment = ['data-payment' => 'invoice'];
        $name_into_money = 'into_money';
        $name_discount = 'discount';
        $name_discount_money = 'discount_money';
        $name_total_money = 'total_money';

        $array_available_quantity_attr = [ 'min' => '0.0', 'step' => 'any', 'readonly' => true];
        $array_qty_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_rate_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_discount_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_discount_money_attr = [ 'min' => '0.0', 'step' => 'any'];
        $str_rate_attr = 'min="0.0" step="any"';

        $array_subtotal_attr = ['readonly' => true];
        $text_right_class = 'text-right';

        if ($name == '') {
            $tax_rate_class = ' refresh_tax1';
            $row .= '<tr class="main">
                  <td></td>';
            $vehicles = [];
            $array_attr = ['placeholder' => _l('unit_price')];
         
            $manual             = true;
            $invoice_item_taxes = '';
            $amount = '';
            $sub_total = 0;

        } else {
            $tax_rate_class = ' refresh_tax2';
            $row .= '<tr class="sortable item">
                    <td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
            $name_item_code = $name . '[item_code]';
            $name_item_name = $name . '[item_name]';
            $name_item_description = $name . '[item_description]';
            $name_unit_id = $name . '[unit_id]';
            $name_unit_name = '[unit_name]';
            $name_quantity = $name . '[quantity]';
            $name_unit_price = $name . '[unit_price]';
            $name_tax_id_select = $name . '[tax_select][]';
            $name_tax_id = $name . '[tax_id]';
            $name_total = $name . '[total]';
            $name_tax_rate = $name . '[tax_rate]';
            $name_tax_name = $name .'[tax_name]';
            $name_into_money = $name .'[into_money]';
            $name_discount = $name .'[discount]';
            $name_discount_money = $name .'[discount_money]';
            $name_total_money = $name . '[total_money]';
            $name_tax_value = $name. '[tax_value]';
      
           
            $array_qty_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantity];
            

            $array_rate_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('rate')];
            $array_discount_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];

            $array_discount_money_attr = ['onblur' => 'pur_calculate_total(1);', 'onchange' => 'pur_calculate_total(1);', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];


            $manual             = false;

            $tax_money = 0;
            $tax_rate_value = 0;

            if($is_edit){
                $invoice_item_taxes = pur_convert_item_taxes($tax_id, $tax_rate, $taxname);
                $arr_tax_rate = explode('|', $tax_rate);
                foreach ($arr_tax_rate as $key => $value) {
                    $tax_rate_value += (float)$value;
                }
            }else{
                $invoice_item_taxes = $taxname;
                $tax_rate_data = $this->pur_get_tax_rate($taxname);
                $tax_rate_value = $tax_rate_data['tax_rate'];
            }

            if((float)$tax_rate_value != 0){
                $tax_money = (float)$unit_price * (float)$quantity * (float)$tax_rate_value / 100;
                $goods_money = (float)$unit_price * (float)$quantity + (float)$tax_money;
                $amount = (float)$unit_price * (float)$quantity + (float)$tax_money;
            }else{
                $goods_money = (float)$unit_price * (float)$quantity;
                $amount = (float)$unit_price * (float)$quantity;
            }

            $sub_total = (float)$unit_price * (float)$quantity;
            $amount = to_decimal_format($amount);

        }
 

        $row .= '<td class="">' . render_textarea1($name_item_name, '', $item_name, ['rows' => 2, 'placeholder' => _l('pur_item_name'), 'readonly' => true] ) . '</td>';

        $row .= '<td class="">' . render_textarea1($name_item_description, '', $item_description, ['rows' => 2, 'placeholder' => _l('item_description')] ) . '</td>';

        $row .= '<td class="rate">' . render_input1($name_unit_price, '', $unit_price, 'number', $array_rate_attr, [], 'no-margin', $text_right_class);

        if( $unit_price != ''){
            $original_price = round( ($unit_price/$currency_rate), 2);
            $base_currency = get_base_currency();
            if($to_currency != '' && $to_currency != $base_currency){
                $row .= render_input1('original_price', '',to_currency($original_price, $base_currency), 'text', ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => _l('original_price'), 'disabled' => true], [], 'no-margin', 'input-transparent text-right pur_input_none');
            }

            $row .= '<input class="hide" name="og_price" disabled="true" value="'.$original_price.'">';
        }
       
        $row .= '<td class="quantities">' . 
        render_input1($name_quantity, '', $quantity, 'number', $array_qty_attr, [], 'no-margin', $text_right_class) . 
        render_input1($name_unit_name, '', $unit_name, 'text', ['placeholder' => _l('unit'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right pur_input_none').
        '</td>';
        
        $row .= '<td class="taxrate '.$tax_rate_class.'">' . $this->get_taxes_dropdown_template($name_tax_id_select, $invoice_item_taxes, 'invoice', $item_key, true, $manual) . '</td>';

        $row .= '<td class="tax_value">' . render_input1($name_tax_value, '', $tax_value, 'number', $array_subtotal_attr, [], '', $text_right_class) . '</td>';

        $row .= '<td class="_total" align="right">' . $total . '</td>';

        if($discount_money > 0){
            $discount = '';
        }

        $row .= '<td class="discount">' . render_input1($name_discount, '', $discount, 'number', $array_discount_attr, [], '', $text_right_class) . '</td>';
        $row .= '<td class="discount_money" align="right">' . render_input1($name_discount_money, '', $discount_money, 'number', $array_discount_money_attr, [], '', $text_right_class.' item_discount_money') . '</td>';
        $row .= '<td class="label_total_after_discount" align="right">' . $total_money . '</td>';

        $row .= '<td class="hide commodity_code">' . render_input1($name_item_code, '', $item_code, 'text', ['placeholder' => _l('commodity_code')]) . '</td>';
        $row .= '<td class="hide unit_id">' . render_input1($name_unit_id, '', $unit_id, 'text', ['placeholder' => _l('unit_id')]) . '</td>';

        $row .= '<td class="hide _total_after_tax">' . render_input1($name_total, '', $total, 'number', []) . '</td>';

        $row .= '<td class="hide total_after_discount">' . render_input1($name_total_money, '', $total_money, 'number', []) . '</td>';
        $row .= '<td class="hide _into_money">' . render_input1($name_into_money, '', $into_money, 'number', []) . '</td>';

        if ($name == '') {
            $row .= '<td><button type="button" onclick="pur_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info text-white"><i data-feather="plus-circle" class="icon-16"></i></button></td>';
        } else {
            $row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="pur_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i data-feather="x" class="icon-16"></i></a></td>';
        }

        $row .= '</tr>';
        return $row;
    }

    /**
     * Adds a pur invoice.
     *
     * @param        $data   The data
     */
    public function add_pur_invoice($data){

        unset($data['item_select']);
        unset($data['item_name']);
        unset($data['description']);
        unset($data['total']);
        unset($data['quantity']);
        unset($data['unit_price']);
        unset($data['unit_name']);
        unset($data['item_code']);
        unset($data['unit_id']);
        unset($data['discount']);
        unset($data['into_money']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['discount_money']);
        unset($data['total_money']);
        unset($data['additional_discount']);
        unset($data['tax_value']);

        $order_detail = [];
        if(isset($data['newitems'])){
            $order_detail = $data['newitems'];
            unset($data['newitems']);
        }

        $data['to_currency'] = $data['currency'];

        if(isset($data['add_from'])){
            $data['add_from'] = $data['add_from'];
        }else{
            $users_model = model("App\Models\Users_model", false);
            $created_by = $users_model->login_user_id();

            $data['add_from'] = $created_by;
            $data['add_from_type'] = 'admin';
        }

        $data['date_add'] = date('Y-m-d');
        $data['payment_status'] = 'unpaid';
        $prefix = get_setting('pur_inv_prefix');

        $builder = $this->db->table(db_prefix().'pur_invoices');

        $builder->where('invoice_number',$data['invoice_number']);
        $check_exist_number = $builder->get()->getRow();

        while($check_exist_number) {
          $data['number'] = $data['number'] + 1;
          $data['invoice_number'] =  $prefix.str_pad($data['number'],5,'0',STR_PAD_LEFT);
          $builder->where('invoice_number',$data['invoice_number']);
          $check_exist_number = $builder->get()->getRow();
        }

        $data['invoice_date'] = to_sql_date1($data['invoice_date']);
        if($data['duedate'] != ''){
           $data['duedate'] = to_sql_date1($data['duedate']); 
        }else{
           $data['duedate'] = null;
        }

        if($data['transaction_date'] != ''){
           $data['transaction_date'] = to_sql_date1($data['transaction_date']); 
        }else{
           $data['transaction_date'] = null;
        }


        if(isset($data['order_discount'])){
            $order_discount = $data['order_discount'];
            if($data['add_discount_type'] == 'percent'){
                $data['discount_percent'] = $order_discount;
            }

            unset($data['order_discount']);
        }

        unset($data['add_discount_type']);

        if(isset($data['dc_total'])){
            $data['discount_total'] = $data['dc_total'];
            unset($data['dc_total']);
        }

        if(isset($data['total_mn'])){
            $data['subtotal'] = $data['total_mn'];
            unset($data['total_mn']);
        }

        if(isset($data['grand_total'])){
            $data['total'] = $data['grand_total'];
            unset($data['grand_total']);
        }


        $builder->insert($data);
        $insert_id = $this->db->insertID();
        if($insert_id){
            $next_number = $data['number']+1;
            update_setting('pur_next_inv_number', $next_number);

            $total = [];
            $total['tax'] = 0;

            if(count($order_detail) > 0){
                foreach($order_detail as $key => $rqd){ 
                    $dt_data = [];
                    $dt_data['pur_invoice'] = $insert_id;
                    $dt_data['item_code'] = $rqd['item_code'];
                    $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                    $dt_data['unit_price'] = $rqd['unit_price'];
                    $dt_data['into_money'] = $rqd['into_money'];
                    $dt_data['total'] = $rqd['total'];
                    $dt_data['tax_value'] = $rqd['tax_value'];
                    $dt_data['item_name'] = $rqd['item_name'];
                    $dt_data['description'] = $rqd['item_description'];
                    $dt_data['total_money'] = $rqd['total_money'];
                    $dt_data['discount_money'] = $rqd['discount_money'];
                    $dt_data['discount_percent'] = $rqd['discount'];

                    $tax_money = 0;
                    $tax_rate_value = 0;
                    $tax_rate = null;
                    $tax_id = null;
                    $tax_name = null;

                    if(isset($rqd['tax_select'])){
                        $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                        $tax_rate_value = $tax_rate_data['tax_rate'];
                        $tax_rate = $tax_rate_data['tax_rate_str'];
                        $tax_id = $tax_rate_data['tax_id_str'];
                        $tax_name = $tax_rate_data['tax_name_str'];
                    }

                    $dt_data['tax'] = $tax_id;
                    $dt_data['tax_rate'] = $tax_rate;
                    $dt_data['tax_name'] = $tax_name;

                    $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                    $dt_builder = $this->db->table(db_prefix().'pur_invoice_details');
                    $dt_builder->insert($dt_data);


                    $total['tax'] += $rqd['tax_value'];
                }
            }

            $builder->where('id',$insert_id);
            $builder->update($total);

            return $insert_id;
        }
        return false;
    }

    /**
     * { update pur invoice }
     *
     * @param        $id     The identifier
     * @param        $data   The data
     */
    public function update_pur_invoice($id,$data){
        $data['invoice_date'] = to_sql_date($data['invoice_date']);
        if($data['transaction_date'] != ''){
           $data['transaction_date'] = to_sql_date1($data['transaction_date']); 
        }else{
           $data['transaction_date'] = null;
        }

        
        $affectedRows = 0;

        unset($data['item_select']);
        unset($data['item_name']);
        unset($data['description']);
        unset($data['total']);
        unset($data['quantity']);
        unset($data['unit_price']);
        unset($data['unit_name']);
        unset($data['item_code']);
        unset($data['unit_id']);
        unset($data['discount']);
        unset($data['into_money']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['discount_money']);
        unset($data['total_money']);
        unset($data['additional_discount']);
        unset($data['tax_value']);

        unset($data['isedit']);

        if(isset($data['dc_total'])){
            $data['discount_total'] = $data['dc_total'];
            unset($data['dc_total']);
        }

        $data['to_currency'] = $data['currency'];

        if(isset($data['total_mn'])){
            $data['subtotal'] = $data['total_mn'];
            unset($data['total_mn']);
        }

        if(isset($data['grand_total'])){
            $data['total'] = $data['grand_total'];
            unset($data['grand_total']);
        }

        $new_order = [];
        if(isset($data['newitems'])){
            $new_order = $data['newitems'];
            unset($data['newitems']);
        }

        $update_order = [];
        if(isset($data['items'])) {
            $update_order = $data['items'];
            unset($data['items']);
        }

        $remove_order = [];
        if(isset($data['removed_items'])){
            $remove_order = $data['removed_items'];
            unset($data['removed_items']);
        }

        if($data['duedate'] != ''){
           $data['duedate'] = to_sql_date1($data['duedate']); 
        }else{
           $data['duedate'] = null;
        }

        if(isset($data['order_discount'])){
            $order_discount = $data['order_discount'];
            if($data['add_discount_type'] == 'percent'){
                $data['discount_percent'] = $order_discount;
            }

            unset($data['order_discount']);
        }

        unset($data['add_discount_type']);

        if(count($new_order) > 0){
            foreach($new_order as $key => $rqd){

                $dt_data = [];
                $dt_data['pur_invoice'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_name'] = $rqd['item_name'];
                $dt_data['total_money'] = $rqd['total_money'];
                $dt_data['discount_money'] = $rqd['discount_money'];
                $dt_data['discount_percent'] = $rqd['discount'];
                $dt_data['description'] = $rqd['item_description'];

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                $dt_builder = $this->db->table(db_prefix().'pur_invoice_details');
                $dt_builder->insert($dt_data);
                $new_quote_insert_id = $this->db->insertID();
                if($new_quote_insert_id){
                    $affectedRows++;
                }
                
            }

        }

        if(count($update_order) > 0){
            foreach($update_order as $_key => $rqd){
                $dt_data = [];
                $dt_data['pur_invoice'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_name'] = $rqd['item_name'];
                $dt_data['total_money'] = $rqd['total_money'];
                $dt_data['discount_money'] = $rqd['discount_money'];
                $dt_data['discount_percent'] = $rqd['discount'];
                $dt_data['description'] = $rqd['item_description'];

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                $dt_builder = $this->db->table(db_prefix().'pur_invoice_details');
                $dt_builder->where('id', $rqd['id']);
                $aff = $dt_builder->update($dt_data);
                if($aff > 0){
                    $affectedRows++;
                }
            }
        }

        if(count($remove_order) > 0){ 
            foreach($remove_order as $remove_id){
                $dt_builder = $this->db->table(db_prefix().'pur_invoice_details');
                $dt_builder->where('id', $remove_id);
                if ($dt_builder->delete()) {
                    $affectedRows++;
                }
            }
        }

        $order_detail_after_update = $this->get_pur_invoice_detail($id);
        $total = [];
        $data['tax'] = 0;
        if(count($order_detail_after_update) > 0){
            foreach($order_detail_after_update as $dt){
                $data['tax'] += $dt['tax_value'];
            }
        }

        $builder = $this->db->table(db_prefix().'pur_invoices');

        $builder->where('id',$id);
        $affected = $builder->update($data);
        if($affected){
            $affectedRows++;
        }

        if($affectedRows > 0){
            return true;
        }
        return false;
    }


    /**
     * Gets the html tax pur order.
     */
    public function get_html_tax_pur_invoice($id){
        $html = '';
        $preview_html = '';
        $pdf_html = '';
        $taxes = [];
        $t_rate = [];
        $tax_val = [];
        $tax_val_rs = [];
        $tax_name = [];
        $rs = [];

        $base_currency = get_base_currency();
        $base_currency_symbol = get_setting('currency_symbol');

        $pur_invoice = $this->get_pur_invoice($id);
        if($pur_invoice->currency != $base_currency){
            $base_currency_symbol = $pur_invoice->currency;
        }

        $builder = $this->db->table(db_prefix().'pur_invoice_details');
        $builder->where('pur_invoice', $id);
        $details = $builder->get()->getResultArray();

        foreach($details as $row){
            if($row['tax'] != ''){
                $tax_arr = explode('|', $row['tax']);

                $tax_rate_arr = [];
                if($row['tax_rate'] != ''){
                    $tax_rate_arr = explode('|', $row['tax_rate']);
                }

                foreach($tax_arr as $k => $tax_it){
                    if(!isset($tax_rate_arr[$k]) ){
                        $tax_rate_arr[$k] = $this->tax_rate_by_id($tax_it);
                    }

                    if(!in_array($tax_it, $taxes)){
                        $taxes[$tax_it] = $tax_it;
                        $t_rate[$tax_it] = $tax_rate_arr[$k];
                        $tax_name[$tax_it] = $this->get_tax_name($tax_it).' ('.$tax_rate_arr[$k].'%)';
                    }
                }
            }
        }

        if(count($tax_name) > 0){
            foreach($tax_name as $key => $tn){
                $tax_val[$key] = 0;
                foreach($details as $row_dt){
                    if(!(strpos($row_dt['tax'], $taxes[$key]) === false)){
                        $tax_val[$key] += ($row_dt['into_money']*$t_rate[$key]/100);
                    }
                }
                $pdf_html .= '<tr id="subtotal"><td width="33%"></td><td>'.$tn.'</td><td>'.to_currency($tax_val[$key], $base_currency_symbol).'</td></tr>';
                $preview_html .= '<tr id="subtotal"><td>'.$tn.'</td><td>'.to_currency($tax_val[$key], $base_currency_symbol).'</td><tr>';
                $html .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.to_currency($tax_val[$key], $base_currency_symbol).'</td></tr>';
                $tax_val_rs[] = $tax_val[$key];
            }
        }
        
        $rs['pdf_html'] = $pdf_html;
        $rs['preview_html'] = $preview_html;
        $rs['html'] = $html;
        $rs['taxes'] = $taxes;
        $rs['taxes_val'] = $tax_val_rs;
        return $rs;
    }

    /**
     * Gets the purchase order attachments.
     *
     * @param      <type>  $id     The purchase order
     *
     * @return     <type>  The purchase order attachments.
     */
    public function get_purchase_invoice_attachments($id){
   
        $builder = $this->db->table(db_prefix().'files');
        $builder->where('rel_id',$id);
        $builder->where('rel_type','pur_invoice');
        return $builder->get()->getResultArray();
    }

    /**
     * Gets the payment invoice.
     *
     * @param        $invoice  The invoice
     *
     * @return       The payment invoice.
     */
    public function get_payment_invoice($invoice){
        $builder = $this->db->table(db_prefix().'pur_invoice_payment');
        $builder->where('pur_invoice',$invoice);
        return $builder->get()->getResultArray();
    }

    /**
     * Gets the inv attachments.
     *
     * @param      <type>  $surope  The surope
     * @param      string  $id      The identifier
     *
     * @return     <type>  The part attachments.
     */
    public function get_purinv_attachments($surope, $id = '')
    {
        // If is passed id get return only 1 attachment
        $builder = $this->db->table(db_prefix().'files');
        if (is_numeric($id)) {
            $builder->where('id', $id);
        } else {
            $builder->where('rel_id', $assets);
        }
        $builder->where('rel_type', 'pur_invoice');
        $result = $builder->get();
        if (is_numeric($id)) {
            return $result->getRow();
        }

        return $result->getResultArray();
    }

    /**
     * { delete purchase invoice attachment }
     *
     * @param         $id     The identifier
     *
     * @return     boolean 
     */
    public function delete_purinv_attachment($id)
    {
        $attachment = $this->get_purinv_attachments('', $id);
        $deleted    = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_invoice/'. $attachment->rel_id . '/' . $attachment->file_name);
            }

            $builder = $this->db->table(db_prefix().'files');

            $builder->where('id', $attachment->id);
            $aff = $builder->delete();
            if ($aff > 0) {
                $deleted = true;
            }

            if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_invoice/'. $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_invoice/'. $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_invoice/'. $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * get pur order approved for inv
     *
     * @return       The pur order approved.
     */
    public function get_pur_order_approved_for_inv_by_vendor($vendor){
        $po_builder = $this->db->table(db_prefix().'pur_orders');

        $po_builder->where('approve_status', 2);
        $po_builder->where('vendor', $vendor);
        $list_po = $po_builder->get()->getResultArray();
        $data_rs = [];
        if(count($list_po) > 0){
            foreach($list_po as $po){
                $pi_builder = $this->db->table(db_prefix().'pur_invoices');

                $pi_builder->where('pur_order', $po['id']);
                $list_inv = $pi_builder->get()->getResultArray();
                $total_inv_value = 0;
                foreach($list_inv as $inv){
                    $total_inv_value += $inv['total'];
                }

                if($total_inv_value < $po['total']){
                    $data_rs[] = $po;
                }
            }    
        }
        
        return $data_rs;
    }


     /**
     * Adds a invoice payment.
     *
     * @param         $data       The data
     * @param         $invoice  The invoice id
     *
     * @return     boolean  
     */
    public function add_invoice_payment($data, $invoice){
        $data['date'] = to_sql_date($data['payment_date']);
        unset($data['payment_date']);

        $data['daterecorded'] = date('Y-m-d H:i:s');
        
        $data['pur_invoice'] = $invoice;
        $data['approval_status'] = 1;
        $data['requester'] = get_staff_user_id();
        $check_appr = $this->get_approve_setting('payment_request');
        if($check_appr && $check_appr != false){
            $data['approval_status'] = 1;
        }else{
            $data['approval_status'] = 2;
        }

        $builder = $this->db->table(db_prefix().'pur_invoice_payment');
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        if($insert_id){

            if($data['approval_status'] == 2){
                $pur_invoice = $this->get_pur_invoice($invoice);
                if($pur_invoice){
                    $status_inv = $pur_invoice->payment_status;
                    if(purinvoice_left_to_pay($invoice) > 0){
                        $status_inv = 'partially_paid';
                    }else{
                        $status_inv = 'paid';
                    }

                    $inv_builder = $this->db->table(db_prefix().'pur_invoices');
                    $inv_builder->where('id',$invoice);
                    $inv_builder->update([ 'payment_status' => $status_inv ]);
                }
            }

            return $insert_id;
        }
        return false;
    }

    /**
     * Gets the payment pur invoice.
     *
     * @param      string  $id     The identifier
     */
    public function get_payment_pur_invoice($id = ''){
        $builder = $this->db->table('pur_invoice_payment');
        if($id != ''){
            $builder->where('id',$id);
            return $builder->get()->getRow();
        }else{
            return $builder->get()->getResultArray();
        }
    }

    /**
     * { delete pur invoice }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean  
     */
    public function delete_pur_invoice($id){

        $file_builder = $this->db->table(db_prefix().'files');
        $file_builder->where('rel_type','pur_invoice');
        $file_builder->where('rel_id', $id);
        $file_builder->delete();

        if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_invoice/'. $id)) {
            delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_invoice/'. $id);
        }

        $dt_builder = $this->db->table(db_prefix().'pur_invoices');
        $dt_builder->where('id');

        $inv_builder = $this->db->table(db_prefix().'pur_invoices');
        $inv_builder->where('id',$id);
        $aff = $inv_builder->delete();
        if($aff > 0){
            $payments = $this->get_payment_invoice($id);
            foreach($payments as $payment){
                $this->delete_payment_pur_invoice($payment['id']);
            }
            return true;
        }
        return false;
    }


    /**
     * { delete invoice payment }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean  ( delete payment )
     */
    public function delete_payment_pur_invoice($id){
        $payment = $this->get_payment_pur_invoice($id);


        $pm_builder = $this->db->table(db_prefix().'pur_invoice_payment');
        $pm_builder->where('id',$id);
        $aff = $pm_builder->delete();
        if ($aff > 0) {
            $pur_invoice = $this->get_pur_invoice($payment->pur_invoice);
            if($pur_invoice){
                $status_inv = $pur_invoice->payment_status;
                if(purinvoice_left_to_pay($payment->pur_invoice) > 0){
                    $status_inv = 'partially_paid';
                    if(purinvoice_left_to_pay($payment->pur_invoice) == $pur_invoice->total){
                        $status_inv = 'unpaid';
                    }
                }else{
                    $status_inv = 'paid';
                }
                $inv_builder = $this->db->table(db_prefix().'pur_invoices');
                $inv_builder->where('id',$payment->pur_invoice);
                $inv_builder->update([ 'payment_status' => $status_inv ]);
            }

            app_hooks()->do_action('after_payment_pur_invoice_deleted', $id);

            return true;
        }
        return false;
    }

    /**
     * { update invoice after approve }
     *
     * @param        $id     The identifier
     */
    public function update_invoice_after_approve($id){
        $payment = $this->get_payment_pur_invoice($id);

        if($payment){
            $pur_invoice = $this->get_pur_invoice($payment->pur_invoice);
            if($pur_invoice){
                $status_inv = $pur_invoice->payment_status;
                if(purinvoice_left_to_pay($payment->pur_invoice) > 0){
                    if(purinvoice_left_to_pay($payment->pur_invoice) == $pur_invoice->total){
                        $status_inv = 'unpaid';
                    }else{
                        $status_inv = 'partially_paid';
                    }
                }else{
                    $status_inv = 'paid';
                }

                $inv_builder = $this->db->table(db_prefix().'pur_invoices');
                $inv_builder->where('id',$payment->pur_invoice);
                $inv_builder->update([ 'payment_status' => $status_inv, ]);
            }
        }
    }

    /**
     * Gets the inv payment purchase order.
     *
     * @param        $pur_order  The pur order
     */
    public function get_inv_payment_purchase_order($pur_order){
        $inv_builder = $this->db->table(db_prefix().'pur_invoices');
        $inv_builder->where('pur_order', $pur_order);
        $list_inv = $inv_builder->get()->getResultArray();
        $data_rs = [];
        foreach($list_inv as $inv){
            $pm_builder = $this->db->table(db_prefix().'pur_invoice_payment');
            $pm_builder->where('pur_invoice', $inv['id']);
            $inv_payments = $pm_builder->get()->getResultArray();
            foreach($inv_payments as $payment){
                $data_rs[] = $payment;
            }
        }

        return $data_rs; 
    }

    /**
     * Gets the payment invoices by vendor.
     */
    public function get_payment_invoices_by_vendor($vendor){
        $invoices = $this->get_invoices_by_vendor($vendor);
        $data_rs = array();
        if(count($invoices)  > 0){
            foreach($invoices as $inv){
                $payments = $this->get_payment_invoice($inv['id']);
                if(count($invoices)  > 0){
                    foreach($payments as $pm){
                        $data_rs[] = $pm; 
                    }
                }
            }
        }

        return $data_rs;
    }

    /**
     * Gets the invoices by vendor.
     */
    public function get_invoices_by_vendor($vendor){
        $builder = $this->db->table(db_prefix().'pur_invoices');
        $builder->where('vendor', $vendor);
        $invoices = $builder->get()->getResultArray();

        return $invoices;
    }

    /**
     * get unit add commodity
     * @return [type] 
     */
    public function get_unit_add_commodity() {
        return $this->db->query('select * from '.get_db_prefix().'ware_unit_type where display = 1 order by '.get_db_prefix().'ware_unit_type.order asc ')->getResultArray();
    }

    /**
     * Gets the contact.
     *
     * @param      <type>  $id     The identifier
     *
     * @return     <type>  The contact.
     */
    public function get_contact($id)
    {
        $builder = $this->db->table(db_prefix() . 'users');
        $builder->where('id', $id);
        return $builder->get()->getRow();
    }

    /**
     * Adds a contact.
     *
     * @param      <type>   $data                The data
     * @param      <type>   $customer_id         The customer identifier
     * @param      boolean  $not_manual_request  Not manual request
     *
     * @return     boolean  or contact id
     */
    public function add_contact($data, $customer_id, $not_manual_request = false)
    {
        $send_set_password_email = isset($data['send_set_password_email']) ? true : false;

        if (isset($data['fakeusernameremembered'])) {
            unset($data['fakeusernameremembered']);
        }
        if (isset($data['fakepasswordremembered'])) {
            unset($data['fakepasswordremembered']);
        }

        $builder = $this->db->table(db_prefix() . 'users');
        if (isset($data['is_primary_contact'])) {
            $data['is_primary_contact'] = 1;
            $builder->where('vendor_id', $customer_id);
            $builder->update([
                'is_primary_contact' => 0,
            ]);
        } else {
            $data['is_primary_contact'] = 0;
        }

        $password_before_hash = '';
        $data['vendor_id']       = $customer_id;
        $data['user_type'] = 'vendor';
        if (isset($data['password'])) {
            $password_before_hash = $data['password'];
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $data['created_at'] = date('Y-m-d H:i:s');

        $data['email'] = trim($data['email']);


        $builder->insert($data);
        $contact_id = $this->db->insertID();

        if ($contact_id) {
            

            return $contact_id;
        }

        return false;
    }

    /**
     * { update contact }
     *
     * @param      <type>   $data            The data
     * @param      <type>   $id              The identifier
     * @param      boolean  $client_request  The client request
     *
     * @return     boolean 
     */
    public function update_contact($data, $id, $client_request = false)
    {
        $affectedRows = 0;
        $contact      = $this->get_contact($id);
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password']             = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (isset($data['fakeusernameremembered'])) {
            unset($data['fakeusernameremembered']);
        }
        if (isset($data['fakepasswordremembered'])) {
            unset($data['fakepasswordremembered']);
        }

        $send_set_password_email = isset($data['send_set_password_email']) ? true : false;
        $set_password_email_sent = false;
      
        $data['is_primary_contact'] = isset($data['is_primary_contact']) ? 1 : 0;

        // Contact cant change if is primary or not
        if ($client_request == true) {
            unset($data['is_primary_contact']);
        }

        $builder = $this->db->table(db_prefix().'users');

        $builder->where('id', $id);
        $affected = $builder->update($data);

        if ($affected > 0) {
            $affectedRows++;
            if (isset($data['is_primary_contact']) && $data['is_primary_contact'] == 1) {
                $builder->where('vendor_id', $contact->vendor_id);
                $builder->where('id !=', $id);
                $builder->update([
                    'is_primary_contact' => 0,
                ]);
            }
        }

       
        if ($affectedRows > 0 ) {
            return true;
        } 

        return false;
    }


     /**
     * { delete contact }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean  
     */
    public function delete_vendor_contact($id)
    {

        $builder = $this->db->table(db_prefix().'users');
        $builder->where('id', $id);
        $aff = $builder->update(['deleted' => 1]);

        if ($aff > 0) {
            return true;
        }

        return false;
    }

    /**
     * Gets the vendor item.
     *
     * @param        $vendorid  The vendorid
     *
     * @return       The vendor item.
     */
    public function get_vendor_item($vendorid){
        $builder = $this->db->table(db_prefix().'items_of_vendor');
        $builder->where('vendor_id', $vendorid);
        return $builder->get()->getResultArray();
    }

     /**
     * Gets the item by vendor.
     *
     * @param      $vendor  The vendor
     */
    public function get_item_by_vendor($vendor){
        $builder = $this->db->table(db_prefix().'pur_vendor_items');
        $builder->where('vendor',$vendor);
        return $builder->get()->getResultArray();  
    }

    /**
     * Gets the item of vendor.
     *
     * @param        $item_id  The item identifier
     *
     * @return       The item of vendor.
     */
    public function get_item_of_vendor($item_id){
        $builder = $this->db->table(db_prefix().'items_of_vendor');
        $builder->where('id', $item_id);
        return $builder->get()->getRow();
    }

    /**
     * Adds a vendor item.
     */
    public function add_vendor_item($data, $vendor_id){
        $data['vendor_id'] = $vendor_id;

        if(isset($data['attachments'])){
            unset($data['attachments']);
        }

        if($data['sku_code'] != ''){
            $data['sku_code'] = $data['sku_code'];
        }else{
            $data['sku_code'] = $this->create_vendor_item_sku_code('', '');
        }
        
        //update column unit name use sales/items
        $unit_type = get_unit_type_item($data['unit_id']);
        if($unit_type && !is_array($unit_type)){
            $data['unit'] = $unit_type->unit_name;
        }

        $builder = $this->db->table(db_prefix().'items_of_vendor');
        $builder->insert($data);
        $insert_id = $this->db->insertID();

        if($insert_id){
            return $insert_id;
        }
        return false;
    }


    /**
     * create sku code 
     * @param  int commodity_group 
     * @param  int sub_group 
     * @return string
     */
    public function  create_vendor_item_sku_code($commodity_group, $sub_group)
    {
        // input  commodity group, sub group
        //get commodity group from id
        $group_character = '';
        if(isset($commodity_group)){

            $sql_group_where = 'SELECT * FROM '.db_prefix().'item_categories where id = "'.$commodity_group.'"';
            $group_value = $this->db->query($sql_group_where)->getRow();
            if($group_value){

                if($group_value->commodity_group_code != ''){
                    $group_character = mb_substr($group_value->commodity_group_code, 0, 1, "UTF-8").'-';
                }
            }
        }
        //get sku code from sku id
        $sub_code = '';
        
        $sql_where = 'SELECT * FROM '.db_prefix().'items_of_vendor order by id desc limit 1';
        $last_commodity_id = $this->db->query($sql_where)->getRow();
        if($last_commodity_id){
            $next_commodity_id = (int)$last_commodity_id->id + 1;
        }else{
            $next_commodity_id = 1;
        }
        $commodity_id_length = strlen((string)$next_commodity_id);

        $commodity_str_betwen ='';

        $create_candidate_code='';

        switch ($commodity_id_length) {
            case 1:
                $commodity_str_betwen = '000';
                break;
            case 2:
                $commodity_str_betwen = '00';
                break;
            case 3:
                $commodity_str_betwen = '0';
                break;

            default:
                $commodity_str_betwen = '0';
                break;
        }
        return  $group_character.$sub_code.$commodity_str_betwen.$next_commodity_id; // X_X_000.id auto increment
    }

    /**
     * { update vendor item }
     *
     * @param        $data   The data
     * @param        $id     The identifier
     */
    public function update_vendor_item($data, $id){
        $unit_type = get_unit_type_item($data['unit_id']);
        if($unit_type && !is_array($unit_type)){
            $data['unit'] = $unit_type->unit_name;
        }

        $builder = $this->db->table(db_prefix().'items_of_vendor');

        $builder->where('id', $id);
        $aff = $builder->update( $data);
        if ($aff > 0) { 

            $vendor_currency_id = get_vendor_currency(get_vendor_user_id());

            $base_currency = get_base_currency();
            $vendor_currency = get_base_currency();
            if($vendor_currency_id != ''){
                $vendor_currency = $vendor_currency_id;
            }

            $convert_rate = 1;
            if($base_currency != $vendor_currency){
                $convert_rate = pur_get_currency_rate($base_currency);
            }

            $purchase_price = round(($data['rate'] * $convert_rate), 2);


            $data['purchase_price'] = $purchase_price;
            $data['rate'] = '';

            if(isset($data['unit'])){
                $data['unit_type'] = $data['unit'];
                unset($data['unit']);
            }
                
            $fr_builder = $this->db->table(db_prefix().'items');
            $fr_builder->where('from_vendor_item', $id);
            $fr_builder->update($data);

            return true;
        }
        return false;

    }

    /**
     * Gets the vendor item file.
     */
    public function get_vendor_item_file($item_id){
        $builder = $this->db->table(db_prefix().'files');
 
        $builder->where('rel_id', $item_id);
        $builder->where('rel_type', 'vendor_items');

        return $builder->get()->getResultArray();
    }

    /**
     * { share vendor item }
     *
     * @param        $item_id  The item identifier
     */
    public function share_vendor_item($item_id){
        $item = $this->get_item_of_vendor($item_id);

        $vendor_currency_id = get_vendor_currency($item->vendor_id);

        $base_currency = get_base_currency();
        $vendor_currency = get_base_currency();
        if($vendor_currency_id != ''){
            $vendor_currency = $vendor_currency_id;
        }

        $convert_rate = 1;
        if($base_currency != $vendor_currency){
            $convert_rate = pur_get_currency_rate($vendor_currency);
        }

        $purchase_price = round(($item->rate / $convert_rate), 2);

        $item_data['title'] = $item->title;
        $item_data['description'] = $item->description;
        $item_data['purchase_price'] = $purchase_price;
        $item_data['unit_id'] = $item->unit_id;
        $item_data['sku_code'] = $item->sku_code;
        $item_data['commodity_barcode'] = $item->commodity_barcode;
        $item_data['commodity_code'] = $item->commodity_code;
        $item_data['sku_name'] = $item->sku_name;
        $item_data['sub_group'] = $item->sub_group;
        $item_data['category_id'] = $item->group_id;
        $item_data['long_description'] = $item->long_description;
        $item_data['from_vendor_item'] = $item->id;
        $item_data['rate'] = '';
        $item_data['tax'] = $item->tax;
        $item_data['tax2'] = $item->tax2;

        $item_id_rs = $this->add_commodity_one_item($item_data);

        if($item_id){
            $builder = $this->db->table(db_prefix().'pur_vendor_items');
            $builder->insert([
                'vendor' => $item->vendor_id,
                'items' => $item_id_rs,
                'datecreate' => date('Y-m-d'),
                'add_from' => 0
            ]);

            $iv_builder = $this->db->table(db_prefix().'items_of_vendor');
            $iv_builder->where('id', $item_id);
            $iv_builder->update( ['share_status' => 1]);

            return true;
        }

        return false;
    }

    /**
     * { delete vendor item }
     *
     * @param        $item_id    The item identifier
     * @param        $vendor_id  The vendor identifier
     */
    public function delete_vendor_item($item_id, $vendor_id){
        $item = $this->get_item_of_vendor($item_id);
        if(!$item->vendor_id || $item->vendor_id != $vendor_id){
            return false;
        }

        $builder = $this->db->table(db_prefix().'items_of_vendor');
        $builder->where('id', $item_id);
        $aff = $builder->delete();
        if($aff > 0){

            $file_builder = $this->db->table(db_prefix().'files');
            $file_builder->where('rel_id',$item_id);
            $file_builder->where('rel_type','vendor_items');
            $aff = $file_builder->delete();


            if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/vendor_items/'. $item_id)) {
                delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/vendor_items/'. $item_id);
            }

            return true;
        }
        return false;
    }

    /**
     * Gets the primary contact name of vendor.
     */
    public function get_primary_contact_name_of_vendor($vendorid){
        $builder = $this->db->table(db_prefix().'users');

        $builder->where('vendor_id', $vendorid);
        $builder->where('is_primary_contact', 1);
        $contact = $builder->get()->getRow();

        if($contact){
            return $contact->first_name.' '. $contact->last_name;
        }
    }


    /**
     * Gets the primary contact email of vendor.
     *
     * @param        $vendorid  The vendorid
     *
     * @return       The primary contact email of vendor.
     */
    public function get_primary_contact_email_of_vendor($vendorid){
        $builder = $this->db->table(db_prefix().'users');

        $builder->where('vendor_id', $vendorid);
        $builder->where('is_primary_contact', 1);
        $contact = $builder->get()->getRow();

        if($contact){
            return $contact->email;
        }
    }

    /**
     * Gets the purchase request by vendor.
     *
     * @param        $vendorid  The vendorid
     */
    public function get_purchase_request_by_vendor($vendorid){
        $builder = $this->db->table(db_prefix().'pur_request');

        $builder->where('find_in_set('.$vendorid.', send_to_vendors)');
        $builder->where('status', 2);
        return $builder->get()->getResultArray();
    }

    /**
     * Gets the pur order by vendor.
     *
     * @param      <type>  $vendor  The vendor
     */
    public function get_pur_order_by_vendor($vendor){
        $builder = $this->db->table(db_prefix().'pur_orders');

        $builder->where('vendor',$vendor);
        return $builder->get()->getResultArray();
    }

    /**
     * Gets the pur order approved.
     *
     * @return     <array>  The pur order approved.
     */
    public function get_pur_order_approved_by_vendor($vendor){
        $builder = $this->db->table(db_prefix().'pur_orders');
        $builder->where('approve_status', 2);
        $builder->where('vendor', $vendor);
        return $builder->get()->getResultArray();
    }

    /**
     * Gets the contact details.
     *
     * @param      array   $options  The options
     *
     * @return       The contact details.
     */
    public function get_contact_details($options = array()) {
        $users_table = $this->db->prefixTable('users');
        $team_member_job_info_table = $this->db->prefixTable('team_member_job_info');
        $clients_table = $this->db->prefixTable('clients');
        $roles_table = $this->db->prefixTable('roles');

        $where = "";
        $id = get_array_value($options, "id");
        $status = get_array_value($options, "status");
        $user_type = get_array_value($options, "user_type");
        $client_id = get_array_value($options, "client_id");
        $vendor_id = get_array_value($options, "vendor_id");
        $exclude_user_id = get_array_value($options, "exclude_user_id");
        $first_name = get_array_value($options, "first_name");
        $last_name = get_array_value($options, "last_name");

        if ($id) {
            $where .= " AND $users_table.id=$id";
        }
        if ($status === "active") {
            $where .= " AND $users_table.status='active'";
        } else if ($status === "inactive") {
            $where .= " AND $users_table.status='inactive'";
        }

        if ($user_type) {
            $where .= " AND $users_table.user_type='$user_type'";
        }

        if ($user_type == 'client') {
            $where .= " AND $clients_table.deleted=0";
        }

        if ($first_name) {
            $where .= " AND $users_table.first_name='$first_name'";
        }

        if ($last_name) {
            $where .= " AND $users_table.last_name='$last_name'";
        }

        if ($client_id) {
            $client_id = $this->db->escapeString($client_id);
            $where .= " AND $users_table.client_id=$client_id";
        }

        if ($vendor_id) {
            $vendor_id = $this->db->escapeString($vendor_id);
            $where .= " AND $users_table.vendor_id=$vendor_id";
        }

        if ($exclude_user_id) {
            $where .= " AND $users_table.id!=$exclude_user_id";
        }

        $non_admin_users_only = get_array_value($options, "non_admin_users_only");
        if ($non_admin_users_only) {
            $where .= " AND $users_table.is_admin=0";
        }

        $show_own_clients_only_user_id = get_array_value($options, "show_own_clients_only_user_id");
        if ($user_type == "client" && $show_own_clients_only_user_id) {
            $where .= " AND $users_table.client_id IN(SELECT $clients_table.id FROM $clients_table WHERE $clients_table.deleted=0 AND $clients_table.created_by=$show_own_clients_only_user_id)";
        }

        $quick_filter = get_array_value($options, "quick_filter");
        if ($quick_filter) {
            $where .= $this->make_quick_filter_query($quick_filter, $users_table);
        }

        $client_groups = get_array_value($options, "client_groups");
        if ($client_groups) {
            $client_groups_where = $this->prepare_allowed_client_groups_query($clients_table, $client_groups);
            if ($client_groups_where) {
                $where .= " AND $users_table.client_id IN(SELECT $clients_table.id FROM $clients_table WHERE $clients_table.deleted=0 $client_groups_where)";
            }
        }

        $custom_field_type = "team_members";
        if ($user_type === "client") {
            $custom_field_type = "client_contacts";
        } else if ($user_type === "lead") {
            $custom_field_type = "lead_contacts";
        }

        //prepare custom fild binding query
        $custom_fields = get_array_value($options, "custom_fields");
        $custom_field_filter = get_array_value($options, "custom_field_filter");
        $custom_field_query_info = $this->prepare_custom_field_query_string($custom_field_type, $custom_fields, $users_table, $custom_field_filter);
        $select_custom_fieds = get_array_value($custom_field_query_info, "select_string");
        $join_custom_fieds = get_array_value($custom_field_query_info, "join_string");
        $custom_fields_where = get_array_value($custom_field_query_info, "where_string");

        //prepare full query string
        $sql = "SELECT $users_table.*, $roles_table.title AS role_title,
            $team_member_job_info_table.date_of_hire, $team_member_job_info_table.salary, $team_member_job_info_table.salary_term $select_custom_fieds
        FROM $users_table
        LEFT JOIN $team_member_job_info_table ON $team_member_job_info_table.user_id=$users_table.id
        LEFT JOIN $clients_table ON $clients_table.id=$users_table.client_id
        LEFT JOIN $roles_table ON $roles_table.id=$users_table.role_id
        $join_custom_fieds    
        WHERE $users_table.deleted=0 $where $custom_fields_where
        ORDER BY $users_table.first_name";
        return $this->db->query($sql);
    }
}
