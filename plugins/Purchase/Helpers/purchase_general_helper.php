<?php

use App\Controllers\Security_Controller;
use Purchase\Controllers\Purchase;
use App\Controllers\Notification_processor;
use App\Controllers\App_Controller;
use App\Libraries\Pdf;
use App\Libraries\Clean_data;


/**
 * link the css files 
 * 
 * @param array $array
 * @return print css links
 */
if (!function_exists('purchase_load_css')) {

    function purchase_load_css(array $array) {
        $version = get_setting("app_version");

        foreach ($array as $uri) {
            echo "<link rel='stylesheet' type='text/css' href='" . base_url(PLUGIN_URL_PATH . "Purchase/$uri") . "?v=$version' />";
        }
    }

}

/**
 * link the css files 
 * 
 * @param array $array
 * @return print css links
 */
if (!function_exists('purchase_load_js')) {

    function purchase_load_js(array $array) {
        $version = get_setting("app_version");
        foreach ($array as $uri) {
            echo "<script type='text/javascript'  src='" . base_url(PLUGIN_URL_PATH . "Purchase/$uri") . "?v=$version'></script>";
        }
    }

}

if (!function_exists('get_unit_type')) {
    function get_unit_type($id = false)
    {
        $builder = db_connect('default');
        $builder = $builder->table(get_db_prefix().'ware_unit_type');

        if (is_numeric($id)) {
            $builder->where('unit_type_id', $id);

            return $builder->get()->getRow();
        }
        if ($id == false) {
            return $builder->query('select * from '.get_db_prefix().'ware_unit_type')->getResultArray();
        }

    }
}


/**
 * Gets the status approve.
 *
 * @param      integer|string  $status  The status
 *
 * @return     string          The status approve.
 */
function get_status_approve($status){
    $result = '';
    if($status == 1){
        $result = '<span class="label label-primary"> '._l('purchase_draft').' </span>';
    }elseif($status == 2){
        $result = '<span class="label label-success"> '._l('purchase_approved').' </span>';
    }elseif($status == 3){
        $result = '<span class="label label-warning"> '._l('pur_rejected').' </span>';
    }elseif($status == 4){
        $result = '<span class="label label-danger"> '._l('pur_canceled').' </span>';
    }

    return $result;

}

/**
 * Gets the po html by pur request.
 *
 * @param  $pur_request  The pur request
 */
function get_po_html_by_pur_request($pur_request){
    $builder = db_connect('default');
    $builder = $builder->table(get_db_prefix().'pur_orders');

    $builder->where('pur_request',$pur_request);
    $list = $builder->get()->getResultArray();
    $rs = '';
    $count = 0;
    if(count($list) > 0){
        foreach($list as $li){
            $rs .= '<a href="'.admin_url('purchase/purchase_order/'.$li['id']).'" ><span class="label label-tag mbot5">'.$li['pur_order_number'].'</span></a>&nbsp;';
        }
    }
    return $rs;
}

if(!function_exists('get_base_currency')){
    function get_base_currency(){
        return get_setting('default_currency');
    }
}

function pur_convert_item_taxes($tax, $tax_rate, $tax_name)
{
    /*taxrate taxname
    5.00    TAX5
    id      rate        name
    2|1 ; 6.00|10.00 ; TAX5|TAX10%*/

    $purchase_model = model("Purchase\Models\Purchase_model");

    $taxes = [];
    if($tax != null && strlen($tax) > 0){
        $arr_tax_id = explode('|', $tax);
        if($tax_name != null && strlen($tax_name) > 0){
            $arr_tax_name = explode('|', $tax_name);
            $arr_tax_rate = explode('|', $tax_rate);
            foreach ($arr_tax_name as $key => $value) {
                $taxes[]['taxname'] = $value . '|' .  $arr_tax_rate[$key];
            }
        }elseif($tax_rate != null && strlen($tax_rate) > 0){


            $arr_tax_id = explode('|', $tax);
            $arr_tax_rate = explode('|', $tax_rate);
            foreach ($arr_tax_id as $key => $value) {
                $_tax_name = $purchase_model->get_tax_name($value);
                if(isset($arr_tax_rate[$key])){
                    $taxes[]['taxname'] = $_tax_name . '|' .  $arr_tax_rate[$key];
                }else{
                    $taxes[]['taxname'] = $_tax_name . '|' .  $purchase_model->tax_rate_by_id($value);

                }
            }
        }else{


            $arr_tax_id = explode('|', $tax);
            $arr_tax_rate = explode('|', $tax_rate);
            foreach ($arr_tax_id as $key => $value) {
                $_tax_name = $purchase_model->get_tax_name($value);
                $_tax_rate = $purchase_model->tax_rate_by_id($value);
                $taxes[]['taxname'] = $_tax_name . '|' .  $_tax_rate;
            } 
        }

    }

    return $taxes;
}

/**
 * wh get item variatiom
 * @param  [type] $id 
 * @return [type]     
 */
function pur_get_item_variatiom($id)
{

    $builder = db_connect('default');
    $builder = $builder->table(get_db_prefix().'items');
    $builder->where('id', $id);
    $item_value = $builder->get()->getRow();

    $name = '';
    if($item_value){
        $purchase_model = model("Purchase\Models\Purchase_model");
        $new_item_value = $purchase_model->row_item_to_variation($item_value);

        $name .= $item_value->commodity_code.'_'.$new_item_value->new_description;
    }

    return $name;
}

/**
 * pur get unit name
 * @param  boolean $id 
 * @return [type]      
 */
function pur_get_unit_name($id = false)
{
    $builder = db_connect('default');
    $builder = $builder->table(get_db_prefix().'ware_unit_type');

    if (is_numeric($id)) {
        $builder->where('unit_type_id', $id);

        $unit = $builder->get()->getRow();
        if($unit){
            return $unit->unit_name;
        }
        return '';
    }
}

/**
 * { pur get currency rate }
 */
function pur_get_currency_rate($currency_str){
    $default_currency = get_setting('default_currency');

    if($currency_str == $default_currency){
        return 1;
    }

    $conversion_rate = get_setting("conversion_rate");
    $conversion_rate = @unserialize($conversion_rate);

    $rate = 1;

    if ($conversion_rate && is_array($conversion_rate) && count($conversion_rate)) {
        foreach ($conversion_rate as $currency => $c_rate) {
            if($currency == $currency_str){
                $rate = $c_rate;
            }
        }
    }

    return $rate;
}

/**
 * Gets the item identifier by description.
 *
 * @param        $des       The description
 * @param        $long_des  The long description
 *
 * @return     string  The item identifier by description.
 */
function get_item_id_by_des($des){
    $builder = db_connect('default');
    $builder = $builder->table(get_db_prefix().'items');    

    $builder->where('title', $des);
    $item = $builder->get()->getRow();

    if($item){
        return $item->id;
    }
    return '';
}

/**
 * Function that format task status for the final user
 * @param  string  $id    status id
 * @param  boolean $text
 * @param  boolean $clean
 * @return string
 */
function pur_format_approve_status($status, $text = false, $clean = false)
{

    $status_name = '';
    if($status == 1){
        $status_name = _l('purchase_draft');
    }elseif($status == 2){
        $status_name = _l('purchase_approved');
    }elseif($status == 3){
        $status_name = _l('pur_rejected');
    }elseif($status == 4){
        $status_name = _l('pur_canceled');
    }

    if ($clean == true) {
        return $status_name;
    }

    $style = '';
    $class = '';
    if ($text == false) {
        if($status == 1){
            $class = 'label label-primary';
        }elseif($status == 2){
            $class = 'label label-success';
        }elseif($status == 3){
            $class = 'label label-warning';
        }elseif($status == 4){
            $class = 'label label-danger';
        }
    } else {
        if($status == 1){
            $class = 'label text-info';
        }elseif($status == 2){
            $class = 'label text-success';
        }elseif($status == 3){
            $class = 'label text-warning';
        }elseif($status == 4){
            $class = 'label text-danger';
        }
    }    

    return '<span class="' . $class . '" >' . $status_name . '</span>';
}

/**
 * Gets the status approve string.
 *
 * @param      integer  $status  The status
 *
 * @return     string   The status approve string.
 */
function get_status_approve_str($status){
    $result = '';
    if($status == 1){
        $result = _l('purchase_draft');
    }elseif($status == 2){
        $result = _l('purchase_approved');
    }elseif($status == 3){
        $result = _l('pur_rejected');
    }elseif($status == 4){
        $result = _l('pur_canceled');
    }

    return $result;

}

/**
 * Gets the item hp.
 *
 * @param      string  $id     The identifier
 *
 * @return     <type>  a item or list item.
 */
function get_item_hp($id = ''){
    $builder = db_connect('default');
    $builder = $builder->table(get_db_prefix().'items');   

    if($id != ''){
        $builder->where('id', $id);
        return $builder->get()->getRow();
    }elseif ($id == '') {
        return $builder->get()->getResultArray();
    }
}

//return site logo
if (!function_exists("get_pdf_logo_url")) {
    /**
     * Gets the pdf logo url.
     *
     * @return       The pdf logo url.
     */
    function get_pdf_logo_url() {
        return get_file_from_setting("site_logo", true);
    }

}

if (!function_exists('pur_log_notification')) {

    function pur_log_notification($event, $options = array(), $user_id = 0, $to_user_id = 0) {
        $ci = new Security_Controller(false);

            //send direct notification to the url
        $data = array(
            "event" => $event
        );

        if ($user_id) {
            $data["user_id"] = $user_id;
        } else if ($user_id === "0") {
                $data["user_id"] = $user_id; //if user id is 0 (string) we'll assume that it's system bot 
        } else if (isset($ci->login_user->id)) {
            $data["user_id"] = $ci->login_user->id;
        }

        $data['to_user_id'] = $to_user_id;

        foreach ($options as $key => $value) {
            $value = urlencode($value);
            $data[$key] = $value;
        }

        $purchase = new Purchase();
        $purchase->pur_create_notification($data);
    }
}

/**
 * warehouse process digital signature image
 * @param  string $partBase64
 * @param  string $path
 * @param  string $image_name
 * @return boolean
 */
function purchase_process_digital_signature_image($partBase64, $path, $image_name)
{
    if (empty($partBase64)) {
        return false;
    }

    if (!file_exists($path)) {
        mkdir($path, 0755);
        fopen(rtrim($path, '/') . '/' . 'index.html', 'w');
    }

    $filename = unique_filename($path, $image_name.'.png');
    $decoded_image = base64_decode($partBase64);

    $retval = false;

    $path = rtrim($path, '/') . '/' . $filename;

    $fp = fopen($path, 'w+');

    if (fwrite($fp, $decoded_image)) {
        $retval                                 = true;
        $GLOBALS['processed_digital_signature'] = $filename;
    }

    fclose($fp);

    return $retval;
}


/**
 * { handle purchase order file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean  
 */
function handle_purchase_request_file($id)
{
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {

        $path = PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_request/'. $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['file']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
         
                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['file']['type'],
                    ];

                $purchase_model = model('Purchase\Models\Purchase_model');

                $purchase_model->add_attachment_to_database($id, 'pur_request', $attachment);

                return true;
            }
        }
    }

    return false;
}

/**
 * { handle purchase order file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean  
 */
function handle_purchase_estimate_file($id)
{
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {

        $path = PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_estimate/'. $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['file']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {

                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['file']['type'],
                    ];

                $purchase_model = model('Purchase\Models\Purchase_model');
                $purchase_model->add_attachment_to_database($id, 'pur_estimate', $attachment);

                return true;
            }
        }
    }

    return false;
}

/**
 * { function_description }
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function max_number_estimates(){
    $builder = db_connect('default');
    $max = $builder->query('select MAX(number) as max from '.db_prefix().'pur_estimates')->getRow();
    return $max->max;
}

/**
 * { format pur estimate number }
 *
 * @param      <type>  $id     The identifier
 *
 * @return     string  ( estimate number )
 */
function format_pur_estimate_number($id)
{
    $builder = db_connect('default');
    $builder = $builder->table(db_prefix().'pur_estimates');
    $builder->select('date,number,prefix,number_format')->where('id', $id);
    $estimate = $builder->get()->getRow();

    if (!$estimate) {
        return '';
    }

    $number = sales_number_format($estimate->number, $estimate->number_format, $estimate->prefix, $estimate->date);

    return $number;
}

function sales_number_format($number, $format, $applied_prefix, $date)
{
    $originalNumber = $number;
    $prefixPadding  = 5;

    if ($format == 1) {
        // Number based
        $number = $applied_prefix . str_pad($number, $prefixPadding, '0', STR_PAD_LEFT);
    } elseif ($format == 2) {
        // Year based
        $number = $applied_prefix . date('Y', strtotime($date)) . '/' . str_pad($number, $prefixPadding, '0', STR_PAD_LEFT);
    } elseif ($format == 3) {
        // Number-yy based
        $number = $applied_prefix . str_pad($number, $prefixPadding, '0', STR_PAD_LEFT) . '-' . date('y', strtotime($date));
    } elseif ($format == 4) {
        // Number-mm-yyyy based
        $number = $applied_prefix . str_pad($number, $prefixPadding, '0', STR_PAD_LEFT) . '/' . date('m', strtotime($date)) . '/' . date('Y', strtotime($date));
    }

    return $number;
}

/**
 * Gets the vendor category html.
 *
 * @param      string  $category  The category
 */
function get_vendor_category_html($category){
    $rs = '';
    if($category != ''){
        $cates = explode(',', $category);
        foreach($cates as $cat){
            $cat_name = get_vendor_cate_name_by_id($cat);
            if($cat_name != ''){
                $rs .= '<span class="label label-tag">'.$cat_name.'</span>';
            }
        }
    }
    return $rs;
}

/**
 * Gets the vendor cate name by identifier.
 *
 * @param        $id     The identifier
 *
 * @return     string  The vendor cate name by identifier.
 */
function get_vendor_cate_name_by_id($id){

    $purchase_model = model("Purchase\Models\Purchase_model");
    $category = $purchase_model->get_vendor_category($id);
    if($category){
        return $category->category_name;
    }else{
        return '';
    }
}

/**
 * Gets the vendor company name.
 *
 * @param      string   $userid                 The userid
 * @param      boolean  $prevent_empty_company  The prevent empty company
 *
 * @return     string   The vendor company name.
 */
function get_vendor_company_name($userid, $prevent_empty_company = false)
{
    if ($userid !== '') {
        $_userid = $userid;
    }
    
    $builder = db_connect('default');
    $builder = $builder->table(db_prefix().'pur_vendor');

    $builder->where('userid', $userid);
    $client = $builder->get()->getRow();

    if ($client) {
        return $client->company;
    }

    return '';
}

/**
 * { handle purchase order file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean  
 */
function handle_purchase_order_file($id)
{
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {

        $path = PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_order/'. $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['file']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
      
                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['file']['type'],
                    ];

                $purchase_model = model('Purchase\Models\Purchase_model');
                $purchase_model->add_attachment_to_database($id, 'pur_order', $attachment);

                return true;
            }
        }
    }

    return false;
}

/**
 * Gets the pur order subject.
 *
 * @param      <type>  $pur_order  The pur order
 *
 * @return     string  The pur order subject.
 */
function get_pur_order_subject($pur_order){
    $builder = db_connect('default');
    $builder = $builder->table(db_prefix().'pur_orders');

    $builder->where('id',$pur_order);
    $po = $builder->get()->getRow();

    if($po){
        return $po->pur_order_number;
    }else{
        return '';
    }
}

/**
 * Gets the payment request status by inv.
 *
 * @param        $id     The identifier
 *
 * @return     string  The payment request status by inv.
 */
function get_payment_request_status_by_inv($id){
    $builder = db_connect('default');
    $builder->where('pur_invoice',$id);
    
    $payments = $builder->get(db_prefix().'pur_invoice_payment')->result_array();
    $status = '';
    $class = '';
    if(count($payments) > 0){
        $status = 'created';
        $class = 'info';
        $builder->where('pur_invoice',$id);
        $builder->where('approval_status', 2);
        $payments_approved = $builder->get(db_prefix().'pur_invoice_payment')->result_array();
        if(count($payments_approved)){
            $status = 'approved';
            $class = 'success';
        }
    }else{
        $status = 'blank';
        $class = 'warning';
    }

    if($status != ''){
        return '<span class="label label-'.$class.' s-status invoice-status-3">'._l($status).'</span>';
    }else{
        return '';
    }

}
if(!function_exists('get_tax_rate_item')){
    /**
     * Gets the tax rate item.
     *
     * @param      bool    $id     The identifier
     *
     * @return       The tax rate item.
     */
    function get_tax_rate_item($id = false)
    {
        $builder = db_connect('default');
        $builder = $builder->table(db_prefix().'taxes');

        if (is_numeric($id)) {
        $builder->where('id', $id);

            return $builder->get()->getRow();
        }
        if ($id == false) {
            return $builder->get()->getResultArray();
        }
    }
}

/**
 * Gets the vendor currency.
 *
 * @param        $vendor_id  The vendor identifier
 */
function get_vendor_currency($vendor_id){
    $builder = db_connect('default');
    $builder = $builder->table(db_prefix().'pur_vendor');

    $builder->where('userid', $vendor_id);
    $vendor = $builder->get()->getRow();

    if($vendor){
        return $vendor->default_currency;
    }
    return '';
}

/**
 * { purchase invoice left to pay }
 *
 * @param      <type>   $id     The purchase order
 *
 * @return     integer  ( purchase order left to pay )
 */
function purinvoice_left_to_pay($id)
{
    $builder = db_connect('default');
    $inv_builder = $builder->table(db_prefix() . 'pur_invoices');
    
    $inv_builder->select('total')
        ->where('id', $id);
        $invoice_total = $inv_builder->get()->getRow()->total;


    $pm_builder = $builder->table(db_prefix().'pur_invoice_payment');
    $pm_builder->where('pur_invoice',$id);
    $pm_builder->where('approval_status', 2);
    $payments = $pm_builder->get()->getResultArray();

    
    
    $totalPayments = 0;


    foreach ($payments as $payment) {
        
        $totalPayments += $payment['amount'];
        
    }

    return ($invoice_total - $totalPayments);
}


/**
 * { handle purchase order file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean  
 */
function handle_pur_invoice_file($id)
{
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {

        $path = PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_invoice/'. $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['file']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
      
                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['file']['type'],
                    ];

                $purchase_model = model('Purchase\Models\Purchase_model');
                $purchase_model->add_attachment_to_database($id, 'pur_invoice', $attachment);

                return true;
            }
        }
    }

    return false;
}

/**
 * Gets the payment mode by identifier.
 *
 * @param      <type>  $id     The identifier
 *
 * @return     string  The payment mode by identifier.
 */
function get_payment_mode_by_id($id){
    $builder = db_connect('default');

    $pm_builder = $builder->table(db_prefix().'payment_methods');
    $pm_builder->where('id',$id);
    $mode = $pm_builder->get()->getRow();
    if($mode){
        return $mode->title;
    }else{
        return '';
    }
}

/**
 * Gets the pur invoice number.
 *
 * @param        $id     The identifier
 *
 * @return     string  The pur invoice number.
 */
function get_pur_invoice_number($id){
    $builder = db_connect('default');
    $builder = $builder->table(db_prefix().'pur_invoices');
    $builder->where('id',$id);
    $inv = $builder->get()->getRow();
    if($inv){
        return $inv->invoice_number;
    }else{
        return '';
    }
}

/**
 * { purorder inv left to pay }
 *
 * @param        $pur_order  The pur order
 */
function purorder_inv_left_to_pay($pur_order){
    $builder = db_connect('default');
    $purchase_model = model('Purchase\Models\Purchase_model');

    $list_payment = $purchase_model->get_inv_payment_purchase_order($pur_order);
    $po = $purchase_model->get_pur_order($pur_order);

    $paid = 0;
    foreach($list_payment as $payment){
        if($payment['approval_status'] == 2){
            $paid += $payment['amount'];
        }
    }

    if($po){
        return $po->total - $paid;
    }
    return 0;
}

/**
 * Gets the invoice currency identifier.
 *
 * @param        $invoice_id  The invoice identifier
 *
 * @return     int     The invoice currency identifier.
 */
function get_invoice_currency_id($invoice_id){
    $builder = db_connect('default');
    $builder = $builder->table(db_prefix().'pur_invoices');

    $builder->where('id', $invoice_id);
    $invoice = $builder->get()->getRow();
    if($invoice){
        return $invoice->currency;
    }
    return 0;
}

/**
 * Determines whether the specified identifier is empty vendor company.
 *
 * @param      <type>   $id     The identifier
 *
 * @return     boolean  True if the specified identifier is empty vendor company, False otherwise.
 */
function is_empty_vendor_company($id)
{
    $builder = db_connect('default');
    $builder = $builder->table(db_prefix().'pur_vendor');

    $builder->where('userid', $id);
    $row = $builder->get()->getRow();
    if ($row) {
        if ($row->company == '') {
            return true;
        }
        return false;
    }
    return true;
}

/**
 * Gets the vendor user identifier.
 */
function get_vendor_user_id(){
    $ci = new Security_Controller(false);
    $userid = $ci->login_user->id;
    $builder = db_connect('default');
    $builder = $builder->table(db_prefix().'users');

    $builder->where('id', $userid);
    $staff = $builder->get()->getRow();

    return $staff->vendor_id;

}

if (!function_exists('has_permission')) {
    function has_permission($permission, $staffid = '', $can = '')
    {
    // return staff_can($can, $permission, $staffid);
        return true;
    }

}

/**
 * get unit type
 * @param  integer $id
 * @return array or row
 */
 function get_unit_type_item($id = false)
{
    $builder = db_connect('default');
    $builder = $builder->table(db_prefix() . 'ware_unit_type');

    if (is_numeric($id)) {
    $builder->where('unit_type_id', $id);
        return $builder->get()->getRow();
    }
    if ($id == false) {
        return $builder->get()->getResultArray();
    }

}

/**
 * { handle item password file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean
 */
function handle_vendor_item_attachment($id) {

    $path = PURCHASE_MODULE_UPLOAD_FOLDER . '/vendor_items/' . $id . '/';
    $totalUploaded = 0;

    if (isset($_FILES['attachments']['name'])
        && ($_FILES['attachments']['name'] != '' || is_array($_FILES['attachments']['name']) && count($_FILES['attachments']['name']) > 0)) {
        if (!is_array($_FILES['attachments']['name'])) {
            $_FILES['attachments']['name'] = [$_FILES['attachments']['name']];
            $_FILES['attachments']['type'] = [$_FILES['attachments']['type']];
            $_FILES['attachments']['tmp_name'] = [$_FILES['attachments']['tmp_name']];
            $_FILES['attachments']['error'] = [$_FILES['attachments']['error']];
            $_FILES['attachments']['size'] = [$_FILES['attachments']['size']];
        }

        _file_attachments_index_fix('attachments');
        for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {

            // Get the temp file path
            $tmpFilePath = $_FILES['attachments']['tmp_name'][$i];
            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
       

                _maybe_create_upload_path($path);
                $filename = unique_filename($path, $_FILES['attachments']['name'][$i]);
                $newFilePath = $path . $filename;
                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $attachment = [];
                    $attachment[] = [
                        'file_name' => $filename,
                        'filetype' => $_FILES['attachments']['type'][$i],
                    ];


                    $purchase_model = model('Purchase\Models\Purchase_model');
                    $purchase_model->add_attachment_to_database($id, 'vendor_items', $attachment);

                    $totalUploaded++;
                }
            }
        }
    }

    return (bool) $totalUploaded;
}

if(!function_exists('_file_attachments_index_fix')){
        /**
     * Performs fixes when $_FILES is array and the index is messed up
     * Eq user click on + then remove the file and then added new file
     * In this case the indexes will be 0,2 - 1 is missing because it's removed but they should be 0,1
     * @param  string $index_name $_FILES index name
     * @return null
     */
    function _file_attachments_index_fix($index_name)
    {
        if (isset($_FILES[$index_name]['name']) && is_array($_FILES[$index_name]['name'])) {
            $_FILES[$index_name]['name'] = array_values($_FILES[$index_name]['name']);
        }

        if (isset($_FILES[$index_name]['type']) && is_array($_FILES[$index_name]['type'])) {
            $_FILES[$index_name]['type'] = array_values($_FILES[$index_name]['type']);
        }

        if (isset($_FILES[$index_name]['tmp_name']) && is_array($_FILES[$index_name]['tmp_name'])) {
            $_FILES[$index_name]['tmp_name'] = array_values($_FILES[$index_name]['tmp_name']);
        }

        if (isset($_FILES[$index_name]['error']) && is_array($_FILES[$index_name]['error'])) {
            $_FILES[$index_name]['error'] = array_values($_FILES[$index_name]['error']);
        }

        if (isset($_FILES[$index_name]['size']) && is_array($_FILES[$index_name]['size'])) {
            $_FILES[$index_name]['size'] = array_values($_FILES[$index_name]['size']);
        }
    }
}


/**
 * { vendor item images }
 *
 * @param        $item_id  The item identifier
 */
function vendor_item_images($item_id){
    $builder = db_connect('default');
    $builder = $builder->table(db_prefix() . 'files');

    $builder->where('rel_id', $item_id);
    $builder->where('rel_type', 'vendor_items');

    return $builder->get()->getResultArray();
}

/**
 * get group name
 * @param  integer $id
 * @return array or row
 */
function get_group_name_pur($id = false)
{
    $builder = db_connect('default');
    $builder = $builder->table(db_prefix() . 'item_categories');

    if (is_numeric($id)) {
        $builder->where('id', $id);
        return $builder->get()->getRow();
    }
    if ($id == false) {
        return $builder->query('select * from '.db_prefix().'items_categories')->getResultArray();
    }

}

/**
 * get tax rate
 * @param  integer $id
 * @return array or row
 */
function pur_get_tax_rate($id = false)
{
    $builder = db_connect('default');
    $builder = $builder->table(db_prefix() . 'taxes');

    if (is_numeric($id)) {
        $builder->where('id', $id);

        return $builder->get()->getRow();
    }
    if ($id == false) {
        return $CI->db->query('select * from '.db_prefix().'taxes')->getResultArray();
    }

}

/**
 * get commodity name
 * @param  integer $id
 * @return array or row
 */
function pur_get_commodity_name($id)
{

    if (is_numeric($id)) {
        $builder = db_connect('default');
        $builder = $builder->table(get_db_prefix().'items');
        $builder->where('id', $id);
        return $builder->get()->getRow();
    }

}