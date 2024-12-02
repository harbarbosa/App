<?php
defined('PLUGINPATH') or exit('No direct script access allowed');

/*
  Plugin Name: Accounting and Bookkeeping
  Description: This plugin offers the ability to automate many processes which will not only save time but will also ensure accuracy and efficiency with financial reports.
  Version: 1.0.0
  Requires at least: 3.0
  Author: GreenTech Solutions
  Author URI: https://codecanyon.net/user/greentech_solutions
*/
use App\Controllers\Security_Controller;

if(!defined('ACCOUNTING_REVISION')){
    define('ACCOUNTING_REVISION', 1);    
}
if(!defined('ACCOUTING_EXPORT_XLSX')){
    define('ACCOUTING_EXPORT_XLSX', 'plugins/Accounting/uploads/export_xlsx/');    
}
if(!defined('ACCOUTING_IMPORT_ITEM_ERROR')){
    define('ACCOUTING_IMPORT_ITEM_ERROR', 'plugins/Accounting/uploads/import_item_error/');    
}
if(!defined('TEMP_FOLDER')){
    define('TEMP_FOLDER', ROOTPATH . 'files/temp' . '/');    
}

//add menu item to left menu
app_hooks()->add_filter('app_filter_staff_left_menu', function ($sidebar_menu) {

    $accounting_submenu = array();
    $ci = new Security_Controller(false);
    $permissions = $ci->login_user->permissions;

    if ($ci->login_user->is_admin || get_array_value($permissions, "accounting")) {
        $accounting_submenu["accounting_dashboard"] = array(
            "name" => "dashboard", 
            "url" => "accounting/dashboard", 
            "class" => "home"
        );

        $accounting_submenu["accounting_banking"] = array(
            "name" => "banking", 
            "url" => "accounting/banking?group=banking_register", 
            "class" => "repeat"
        );

        $accounting_submenu["accounting_transaction"] = array(
            "name" => "transaction", 
            "url" => "accounting/transaction?group=sales", 
            "class" => "repeat"
        );

        $accounting_submenu["accounting_journal_entry"] = array(
            "name" => "journal_entry", 
            "url" => "accounting/journal_entry", 
            "class" => "repeat"
        );

        $accounting_submenu["accounting_transfer"] = array(
            "name" => "transfer", 
            "url" => "accounting/transfer", 
            "class" => "home"
        );

        $accounting_submenu["accounting_chart_of_accounts"] = array(
            "name" => "chart_of_accounts", 
            "url" => "accounting/chart_of_accounts", 
            "class" => "home"
        );

        $accounting_submenu["accounting_reconcile"] = array(
            "name" => "reconcile", 
            "url" => "accounting/reconcile", 
            "class" => "home"
        );

        $accounting_submenu["accounting_budget"] = array(
            "name" => "budget", 
            "url" => "accounting/budget", 
            "class" => "home"
        );

        $accounting_submenu["accounting_reports"] = array(
            "name" => "reports", 
            "url" => "accounting/report", 
            "class" => "home"
        );

        $accounting_submenu["accounting_setting"] = array(
            "name" => "setting", 
            "url" => "accounting/setting?group=general", 
            "class" => "home"
        );

        $sidebar_menu["accounting"] = array(
            "name" => "als_accounting",
            "url" => "accounting/dashboard",
            "class" => "book",
            "submenu" => $accounting_submenu,
            "position" => 3,
        );
    }


    return $sidebar_menu;
});


//install dependencies
register_installation_hook("Accounting", function ($item_purchase_code) {    
    include PLUGINPATH . "Accounting/lib/gtsverify.php";
    require_once __DIR__ . '/install.php';
});

//activation
register_activation_hook("Accounting", function () {    
    require_once __DIR__ . '/install.php';
});


//update plugin
register_update_hook("Accounting", function () {
    require_once __DIR__ . '/install.php';
});

//uninstallation: remove data from database
register_uninstallation_hook("Accounting", function () {    
    require_once __DIR__ . '/uninstall.php';
});

app_hooks()->add_action('app_hook_accounting_init', function (){
    require_once __DIR__ .'/lib/gtsslib.php';
    $lic_accounting = new AccountingLic();	/* Asasi
    $accounting_gtssres = $lic_accounting->verify_license(true);    
    if(!$accounting_gtssres || ($accounting_gtssres && isset($accounting_gtssres['status']) && !$accounting_gtssres['status'])){
        echo '<strong>YOUR ACCOUNTING & BOOKKEEPING PLUGIN FAILED ITS VERIFICATION. PLEASE <a href="/index.php/Plugins">REINSTALL</a> OR CONTACT SUPPORT</strong>';
        exit();
    }	*/ 
});
app_hooks()->add_action('app_hook_uninstall_plugin_Accounting', function (){
    require_once __DIR__ .'/lib/gtsslib.php';
    $lic_accounting = new AccountingLic();
    $lic_accounting->deactivate_license();    
});


/**
 * init add head component
 */
app_hooks()->add_action('app_hook_head_extension', function (){
    $viewuri = $_SERVER['REQUEST_URI'];

  if (!(strpos($viewuri, 'index.php/accounting') === false)) {
    echo '<link href="' . base_url('plugins/Accounting/assets/css/custom.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
  }

  if (!(strpos($viewuri, 'index.php/accounting/new_journal_entry') === false)) {
    echo '<link href="' . base_url('plugins/Accounting/assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . base_url('plugins/Accounting/assets/plugins/handsontable/chosen.css') . '"  rel="stylesheet" type="text/css" />';
    echo '<script src="' . base_url('plugins/Accounting/assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
  }

  if (!(strpos($viewuri, 'index.php/accounting/rp_') === false) || !(strpos($viewuri, 'index.php/accounting/report') === false)) {
    echo '<link href="' . base_url('plugins/Accounting/assets/css/report.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . base_url('plugins/Accounting/assets/plugins/treegrid/css/jquery.treegrid.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . base_url('plugins/Accounting/assets/css/box_loading.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
  }

  if (!(strpos($viewuri, 'index.php/accounting/reconcile_account') === false)) {
    echo '<link href="' . base_url('plugins/Accounting/assets/css/reconcile_account.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
  }

  if (!(strpos($viewuri, 'index.php/accounting/dashboard') === false)) {
    echo '<link href="' . base_url('plugins/Accounting/assets/css/box_loading.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . base_url('plugins/Accounting/assets/css/dashboard.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
  }

  if (!(strpos($viewuri, 'index.php/accounting/setting') === false)) {
    echo '<link href="' . base_url('plugins/Accounting/assets/css/setting.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
  }

  if (!(strpos($viewuri, 'index.php/accounting/budget') === false) || !(strpos($viewuri, 'index.php/accounting/user_register_view') === false)) {
    echo '<link href="' . base_url('plugins/Accounting/assets/plugins/handsontable/handsontable.full.min.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . base_url('plugins/Accounting/assets/plugins/handsontable/chosen.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
    echo '<script src="' . base_url('plugins/Accounting/assets/plugins/handsontable/handsontable.full.min.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
    echo '<link href="' . base_url('plugins/Accounting/assets/css/box_loading.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';

  }
});

/**
 * init add footer component
 */
app_hooks()->add_action('app_hook_head_extension', function(){
    $viewuri = $_SERVER['REQUEST_URI'];

  if (!(strpos($viewuri, 'index.php/accounting') === false)) {
    echo '<script src="' . base_url('plugins/Accounting/assets/js/accounting_main.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
  }

  if (!(strpos($viewuri, 'index.php/accounting/setting?group=general') === false)) {
    echo '<script src="' . base_url('plugins/Accounting/assets/js/setting/general.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
  }

  if (!(strpos($viewuri, 'index.php/accounting/new_rule') === false)) {
    echo '<script src="' . base_url('plugins/Accounting/assets/js/setting/new_rule.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
  }

  if (!(strpos($viewuri, 'index.php/accounting/plaid_bank_new_transactions') === false)) {
        echo '<script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>';
    }
    
  if (!(strpos($viewuri, 'index.php/accounting/new_journal_entry') === false)) {
    echo '<script src="' . base_url('plugins/Accounting/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
    echo '<script src="' . base_url('plugins/Accounting/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
  }

  if (!(strpos($viewuri, 'index.php/accounting/reconcile') === false)) {
    echo '<script src="' . base_url('plugins/Accounting/assets/js/reconcile/reconcile.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
  }

  if(!(strpos($viewuri,'index.php/accounting/rp_') === false)){
        echo '<script src="'. base_url('plugins/Accounting/assets/plugins/treegrid/js/jquery.treegrid.min.js').'?v=' . ACCOUNTING_REVISION.'"></script>';
        echo '<script src="' . base_url('plugins/Accounting/assets/plugins/jspdf/jspdf.min.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
        
        echo '<script src="' . base_url('plugins/Accounting/assets/plugins/html2pdf/html2pdf.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
        echo '<script src="' . base_url('plugins/Accounting/assets/plugins/tableHTMLExport/tableHTMLExport.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
        echo '<script src="' . base_url('plugins/Accounting/assets/js/report/main.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
    }

  if (!(strpos($viewuri, '/index.php/accounting/dashboard') === false)) {
    echo '<script src="' . base_url('plugins/Accounting/assets/plugins/highcharts/highcharts.js') . '"></script>';
    echo '<script src="' . base_url('plugins/Accounting/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
    echo '<script src="' . base_url('plugins/Accounting/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
    echo '<script src="' . base_url('plugins/Accounting/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
    echo '<script src="' . base_url('plugins/Accounting/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
    echo '<script src="' . base_url('plugins/Accounting/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
  }
});

app_hooks()->add_action("app_hook_data_update", function(){
    $Accounting_model = model("Accounting\Models\Accounting_model");
    switch ($data['table']) {
        case get_db_prefix().'invoice_items':
                if (get_setting('acc_invoice_automatic_conversion') == 1) {
                    $Accounting_model->automatic_invoice_conversion('', $data['id']);
                }
            break;
        case get_db_prefix().'invoice_payments':
                if (get_setting('acc_payment_automatic_conversion') == 1) {
                    $Accounting_model->automatic_payment_conversion($data['id']);
                }
            break;
        case get_db_prefix().'expenses':
                if (get_setting('acc_expense_automatic_conversion') == 1) {
                    $Accounting_model->automatic_expense_conversion($data['id']);
                }
            break;
        default:
            // code...
            break;
    }

    return $data;
});

app_hooks()->add_action("app_hook_data_insert", function(){
    $Accounting_model = model("Accounting\Models\Accounting_model");
    switch ($data['table']) {
        case get_db_prefix().'invoice_items':
                if (get_setting('acc_invoice_automatic_conversion') == 1) {
                    $Accounting_model->automatic_invoice_conversion('', $data['id']);
                }
            break;
        case get_db_prefix().'invoice_payments':
                if (get_setting('acc_payment_automatic_conversion') == 1) {
                    $Accounting_model->automatic_payment_conversion($data['id']);
                }
            break;
        case get_db_prefix().'expenses':
                if (get_setting('acc_expense_automatic_conversion') == 1) {
                    $Accounting_model->automatic_expense_conversion($data['id']);
                }
            break;
        default:
            // code...
            break;
    }

    return $data;
});

app_hooks()->add_action("app_hook_data_delete", function(){
    $Accounting_model = model("Accounting\Models\Accounting_model");
    switch ($data['table']) {
        case get_db_prefix().'invoices':
                $Accounting_model->delete_invoice_convert($data['id']);
            break;
        case get_db_prefix().'invoice_items':
                if (get_setting('acc_invoice_automatic_conversion') == 1) {
                    $Accounting_model->automatic_invoice_conversion('', $data['id']);
                }
            break;
        case get_db_prefix().'invoice_payments':
                $Accounting_model->delete_convert($data['id'], 'payment');
            break;
        case get_db_prefix().'expenses':
                $Accounting_model->delete_convert($data['id'], 'expense');
            break;
        default:
            // code...
            break;
    }

    return $data;
});

app_hooks()->add_action("app_hook_role_permissions_extension", function(){
    $ci = new Security_Controller(false);
    $access_accounting = get_array_value($permissions, "accounting");
    if (is_null($access_accounting)) {
        $access_accounting = "";
    }

    echo '<li>
        <span data-feather="key" class="icon-14 ml-20"></span>
        <h5>'. app_lang("can_access_accountings").'</h5>
        <div>'.
            form_radio(array(
                "id" => "accounting_no",
                "name" => "accounting_permission",
                "value" => "",
                "class" => "form-check-input"
                    ), $access_accounting, ($access_accounting === "") ? true : false)
            .'<label for="accounting_no">'. app_lang("no").' </label>
        </div>
        <div>
            '. form_radio(array(
                "id" => "accounting_yes",
                "name" => "accounting_permission",
                "value" => "all",
                "class" => "form-check-input"
                    ), $access_accounting, ($access_accounting === "all") ? true : false).'
            <label for="accounting_yes">'. app_lang("yes").'</label>
        </div>
    </li>';
});

app_hooks()->add_filter("app_filter_role_permissions_save_data", function(){
    $accounting = $data['accounting_permission'];

    $permissions = array_merge($permissions, ['accounting' => $accounting]);

    return $permissions;
});

