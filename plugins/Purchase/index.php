<?php

defined('PLUGINPATH') or exit('No direct script access allowed');
use App\Controllers\Security_Controller;

/*
  Plugin Name: Purchase
  Description: Purchase Management Module is a tool for managing your day-to-day purchases. It is packed with all necessary features that are needed by any business, which has to buy raw material for manufacturing or finished good purchases for trading
  Version: 1.0.0
  Requires at least: 3.0
  Author: GreenTech Solutions
  Author URI: https://codecanyon.net/user/greentech_solutions
 */

if(!defined('PURCHASE_REVISION')){
    define('PURCHASE_REVISION', 100);
}
if(!defined('PURCHASE_MODULE_UPLOAD_FOLDER')){
    define('PURCHASE_MODULE_UPLOAD_FOLDER', 'plugins/Purchase/Uploads/');
}

app_hooks()->add_action('app_hook_head_extension', function (){
    $viewuri = $_SERVER['REQUEST_URI'];

    if (!(strpos($viewuri, '/purchase') === false)) {
        echo '<script src="' . base_url('plugins/Purchase/assets/js/main/main.js').'?v=' . PURCHASE_REVISION.'"></script>';
        echo '<script src="' . base_url('plugins/Purchase/assets/plugins/signature_pad.min.js') . '"></script>';
    }
});
app_hooks()->add_action('app_hook_head_extension', function (){
    $viewuri = $_SERVER['REQUEST_URI'];
    $ci = new Security_Controller(false);

    if (!(strpos($viewuri, '/purchase') === false)) {
        echo '<link href="' . base_url('plugins/Purchase/assets/css/main.css') .'?v=' . PURCHASE_REVISION. '"  rel="stylesheet" type="text/css" />';
    }

     if (!(strpos($viewuri, '/dashboard') === false) && $ci->login_user->user_type == 'vendor') {
         echo '<link href="' . base_url('plugins/Purchase/assets/css/main.css') .'?v=' . PURCHASE_REVISION. '"  rel="stylesheet" type="text/css" />';
     }

    if (!(strpos($viewuri, '/purchase/items') === false)) {
        echo '<link href="' . base_url('plugins/Purchase/assets/css/items/item_modal.css') .'?v=' . PURCHASE_REVISION. '"  rel="stylesheet" type="text/css" />';

    }
});
app_hooks()->add_action('app_hook_role_permissions_extension', function (){
    $ci = new Security_Controller(false);
    $access_purchase = get_array_value($permissions, "purchase");
    if (is_null($access_purchase)) {
        $access_purchase = "";
    }

    echo '<li>
        <span data-feather="key" class="icon-14 ml-20"></span>
        <h5>'. app_lang("can_access_purchases").'</h5>
        <div>'.
            form_radio(array(
                "id" => "purchase_no",
                "name" => "purchase_permission",
                "value" => "",
                "class" => "form-check-input"
                    ), $access_purchase, ($access_purchase === "") ? true : false)
            .'<label for="purchase_no">'. app_lang("no").' </label>
        </div>
        <div>
            '. form_radio(array(
                "id" => "purchase_yes",
                "name" => "purchase_permission",
                "value" => "all",
                "class" => "form-check-input"
                    ), $access_purchase, ($access_purchase === "all") ? true : false).'
            <label for="purchase_yes">'. app_lang("yes").'</label>
        </div>
    </li>';
});

app_hooks()->add_filter('app_filter_role_permissions_save_data', function($permissions, $data) {
    $purchase = $data['purchase_permission'];

    $permissions = array_merge($permissions, ['purchase' => $purchase]);

    return $permissions;
});

app_hooks()->add_filter('app_filter_email_templates', function($templates_array) {
    $templates_array['purchase'] = [
        'purchase_order_to_contact' => ['PO_NUMBER', 'PO_NAME', 'PO_TAX_VALUE', 'PO_SUBTOTAL', 'PO_VALUE', 'PO_LINK', 'ORDER_DATE', 'CONTACT_NAME'],
        'purchase_quotation_to_contact' => ['PQ_NUMBER', 'PQ_TAX_VALUE', 'PQ_SUBTOTAL', 'PQ_VALUE', 'PQ_LINK', 'DATE', 'EXPIRY_DATE', 'CONTACT_NAME'],
        'purchase_request_to_contact' => ['PR_NUMBER', 'PR_NAME', 'PR_TAX_VALUE', 'PR_SUB_TOTAL', 'PR_VALUE', 'PR_LINK' , 'CONTACT_NAME']
    ];

    return $templates_array;
});

app_hooks()->add_filter('app_filter_notification_config', function($events) {
    $view_pur_request_link = function ($options) {
        $url = "";
        if (isset($options->pur_request_id)) {
            $url = get_uri("purchase/view_pur_request/" . $options->pur_request_id);
        }

        return array("url" => $url);
    };

    $view_quotation_link = function ($options) {
        $url = "";
        if (isset($options->pur_quotation_id)) {
            $url = get_uri("purchase/view_quotation/" . $options->pur_quotation_id);
        }

        return array("url" => $url);
    };

    $view_pur_order_link = function ($options) {
        $url = "";
        if (isset($options->pur_order_id)) {
            $url = get_uri("purchase/view_pur_order/" . $options->pur_order_id);
        }

        return array("url" => $url);
    };

    $payment_invoice_link = function ($options) {
        $url = "";
        if (isset($options->pur_payment_id)) {
            $url = get_uri("purchase/payment_invoice/" . $options->pur_payment_id);
        }

        return array("url" => $url);
    };



    $events["notify_send_request_approve_pur_request"] = [
            "notify_to" => array("team_members"),
            "info" => $view_pur_request_link
    ];

    $events["notify_send_approve_pur_request"] = [
            "notify_to" => array("team_members"),
            "info" => $view_pur_request_link
    ];

    $events["notify_send_rejected_pur_request"] = [
            "notify_to" => array("team_members"),
            "info" => $view_pur_request_link
    ];


    $events["notify_send_request_approve_pur_quotation"] = [
            "notify_to" => array("team_members"),
            "info" => $view_quotation_link
    ];

    $events["notify_send_approve_pur_quotation"] = [
            "notify_to" => array("team_members"),
            "info" => $view_quotation_link
    ];

    $events["notify_send_rejected_pur_quotation"] = [
            "notify_to" => array("team_members"),
            "info" => $view_quotation_link
    ];


    $events["notify_send_request_approve_pur_order"] = [
            "notify_to" => array("team_members"),
            "info" => $view_pur_order_link
    ];

    $events["notify_send_approve_pur_order"] = [
            "notify_to" => array("team_members"),
            "info" => $view_pur_order_link
    ];

    $events["notify_send_rejected_pur_order"] = [
            "notify_to" => array("team_members"),
            "info" => $view_pur_order_link
    ];


    $events["notify_send_request_approve_pur_inv"] = [
            "notify_to" => array("team_members"),
            "info" => $payment_invoice_link
    ];

    $events["notify_send_approve_pur_inv"] = [
            "notify_to" => array("team_members"),
            "info" => $payment_invoice_link
    ];

    $events["notify_send_rejected_pur_inv"] = [
            "notify_to" => array("team_members"),
            "info" => $payment_invoice_link
    ];

    return $events;
});

//add menu item to left menu
app_hooks()->add_filter('app_filter_client_left_menu', function($sidebar_menu) {

    $CI = new Security_Controller(false);

    $purchase_submenu = array();

    $purchase_submenu["vendor_profile"] = array(
        "name" => "profile",
        "url" => "purchase/vendor/".get_vendor_user_id(),
        "class" => "users"
    );


    $purchase_submenu["purchase_items"] = array(
        "name" => "items",
        "url" => "purchase/vendor_portal_items",
        "class" => "users"
    );

    $purchase_submenu["purchase_request"] = array(
        "name" => "purchase_request",
        "url" => "purchase/purchase_request",
        "class" => "users"
    );

    $purchase_submenu["quotations"] = array(
        "name" => "quotations",
        "url" => "purchase/quotations",
        "class" => "users"
    );

    $purchase_submenu["purchase_orders"] = array(
        "name" => "purchase_orders",
        "url" => "purchase/purchase_orders",
        "class" => "users"
    );

    $purchase_submenu["invoices"] = array(
        "name" => "invoices",
        "url" => "purchase/invoices",
        "class" => "users"
    );


    if($CI->login_user->user_type == 'vendor'){
        $sidebar_menu["purchase"] = array(
            "name" => "purchase",
            "url" => "purchase",
            "class" => "shopping-cart",
            "submenu" => $purchase_submenu,
            "position" => 5,

        );

        foreach ($sidebar_menu as $key => $menu) {
            if($key != 'purchase'){
                unset($sidebar_menu[$key]);
            }
        }
    }

    return $sidebar_menu;

});

app_hooks()->add_filter('app_filter_staff_left_menu', function ($sidebar_menu) {
    $purchase_submenu = array();

    $ci = new Security_Controller(false);
    $permissions = $ci->login_user->permissions;
    if ($ci->login_user->is_admin || get_array_value($permissions, "inventory")) {

        $purchase_submenu["purchase_items"] = array(
            "name" => "items",
            "url" => "purchase/items",
            "class" => "users"
        );

        $purchase_submenu["vendors"] = array(
            "name" => "vendors",
            "url" => "purchase/vendors",
            "class" => "users"
        );

        $purchase_submenu["vendor_items"] = array(
            "name" => "vendor_items",
            "url" => "purchase/vendor_items",
            "class" => "newspaper"
        );

        $purchase_submenu["purchase_request"] = array(
            "name" => "purchase_request",
            "url" => "purchase/purchase_request",
            "class" => "users"
        );

        $purchase_submenu["quotations"] = array(
            "name" => "quotations",
            "url" => "purchase/quotations",
            "class" => "users"
        );

        $purchase_submenu["purchase_orders"] = array(
            "name" => "purchase_orders",
            "url" => "purchase/purchase_orders",
            "class" => "users"
        );


        $purchase_submenu["invoices"] = array(
            "name" => "invoices",
            "url" => "purchase/invoices",
            "class" => "users"
        );


        $purchase_submenu["settings"] = array(
            "name" => "settings",
            "url" => "purchase/settings",
            "class" => "setting"
        );

        $sidebar_menu["purchase"] = array(
            "name" => "purchase",
            "url" => "purchase",
            "class" => "shopping-cart",
            "submenu" => $purchase_submenu,
            "position" => 5,

        );
    }

    return $sidebar_menu;

});



//install dependencies
register_installation_hook("Purchase", function ($item_purchase_code) {
    /*
     * you can verify the item puchase code from here if you want.
     * you'll get the inputted puchase code with $item_purchase_code variable
     * use exit(); here if there is anything doesn't meet it's requirements
     */
    include PLUGINPATH . "Purchase/lib/gtsverify.php";
    require_once(__DIR__ . '/install.php');
});

// Active action
register_activation_hook("Purchase", function ($item_purchase_code) {
       require_once(__DIR__ . '/install.php');
});

//uninstallation: remove data from database
register_uninstallation_hook("Purchase", function () {
    require_once __DIR__ . '/uninstall.php';
});

//add setting link to the plugin setting
app_hooks()->add_filter('app_filter_action_links_of_Purchase', function () {
    $action_links_array = array(
        anchor(get_uri("purchase"), "Purchase"),
        anchor(get_uri("purchase/settings"), "Purchase settings"),
    );

    return $action_links_array;
});

//update plugin
register_update_hook("Purchase", function () {
    echo "Please follow this instructions to update:";
    echo "<br />";
    echo "Your logic to update...";
});

//uninstallation: remove data from database
register_uninstallation_hook("Purchase", function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "DROP TABLE IF EXISTS `" . $dbprefix . "purchase_settings`;";
    $db->query($sql_query);
});
app_hooks()->add_action('app_hook_purchase_init', function (){
    require_once __DIR__ .'/lib/gtsslib.php';
    $lic_purchase = new PurchaseLic();

});
app_hooks()->add_action('app_hook_uninstall_plugin_Purchase', function (){
    require_once __DIR__ .'/lib/gtsslib.php';
    $lic_purchase = new PurchaseLic();
});
