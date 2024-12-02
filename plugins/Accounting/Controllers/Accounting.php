<?php

namespace Accounting\Controllers;

use App\Controllers\Security_Controller;

class Accounting extends Security_Controller
{

    protected $Accounting_model;

    function __construct() {
        parent::__construct();
        $this->Accounting_model = new \Accounting\Models\Accounting_model();
        app_hooks()->do_action('app_hook_accounting_init');
    }

	
    /**
     * manage transaction
     * @return view
     */
    public function transaction($group = '')
    {
       
        
        $data['currency'] = get_setting('default_currency');

        $data['tab_2'] = $this->request->getGet('tab');
        

        $data['group'] = $this->request->getGet('group');

        

      
        $data['tab'][] = 'sales';
        $data['tab'][] = 'expenses';
        
        if ($data['group'] == '') {
            $data['group'] = 'payment';
        }

        if($data['group'] == 'sales'){
            $data['count_invoice'] = $this->Accounting_model->count_invoice_not_convert_yet();
            $data['count_payment'] = $this->Accounting_model->count_payment_not_convert_yet();


            $data['payment_method_dropdown'] = $this->get_payment_method_dropdown();

          
            $data['payment_modes'] = [];
            $data['invoices'] = [];

            if ($data['tab_2'] == '') {
                $data['tab_2'] = 'Accounting\Views/transaction/payment';
            }


        }elseif ($data['group'] == 'expenses') {
            $data['categories_dropdown'] = $this->_get_categories_dropdown();
            $data['members_dropdown'] = $this->_get_team_members_dropdown();
            $data['payment_method_dropdown'] = $this->get_payment_method_dropdown();
        }elseif ($data['group'] == 'warehouse') {
            $data['count_stock_import'] = $this->Accounting_model->count_stock_import_not_convert_yet();
            $data['count_stock_export'] = $this->Accounting_model->count_stock_export_not_convert_yet();
            $data['count_loss_adjustment'] = $this->Accounting_model->count_loss_adjustment_not_convert_yet();
            $data['count_opening_stock'] = $this->Accounting_model->count_opening_stock_not_convert_yet();


            if ($data['tab_2'] == '') {
                $data['tab_2'] = 'stock_import';
            }
        }elseif ($data['group'] == 'purchase') {
            
            $data['count_purchase_order'] = $this->Accounting_model->count_purchase_order_not_convert_yet();
            $data['count_purchase_payment'] = $this->Accounting_model->count_purchase_payment_not_convert_yet();

            if ($data['tab_2'] == '') {
                $data['tab_2'] = 'purchase_order';
            }
        }
        
        $data['accounts'] = $this->Accounting_model->get_accounts();
        $data['account_to_select'] = $this->Accounting_model->get_data_account_to_select();
        $data['title']        = app_lang($data['group']);
        $data['tabs']['view'] = 'Accounting\Views/transaction/' . $data['group'];
        return $this->template->rander('Accounting\Views\transaction/manage', $data);
    }

    /**
     * sales table
     * @return json
     */
    function sales_table($id = '', $return_ajax = true)
    {

        $currency_symbol = get_setting("currency_symbol");

        $acc_closing_date = '';
        if(get_setting('acc_close_the_books') == 1){
            $acc_closing_date = get_setting('acc_closing_date');
        }

        $select = [
            get_db_prefix() . 'invoice_payments.id as id',
            'amount',
            'invoice_id',
            get_db_prefix() . 'payment_methods.title as name',
            get_db_prefix() .'invoice_payments.payment_date as date',
            '(select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'invoice_payments.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "payment") as count_account_historys'
        ];
        $where = [];
            array_push($where, 'AND '. get_db_prefix() . 'invoice_payments.deleted = 0');

        $payment_mode = $this->request->getPost('payment_method_id') ? $this->request->getPost('payment_method_id') : "";
        if ($payment_mode != '') {
            array_push($where, 'AND payment_method_id = "' . $payment_mode . '"');
        }

        if ($id != '') {
            array_push($where, 'AND '.get_db_prefix() . 'invoice_payments.id = "' . $id . '"');
        }

        $status = $this->request->getPost('status') ? $this->request->getPost('status') : "";
        if ($status != '') {
            $where_status = '';
            foreach ($status as $key => $value) {
                if($value == 'converted'){
                    if($where_status != ''){
                        $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'invoice_payments.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "payment") > 0)';
                    }else{
                        $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'invoice_payments.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "payment") > 0)';
                    }
                }

                if($value == 'has_not_been_converted'){
                    if($where_status != ''){
                        $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'invoice_payments.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "payment") = 0)';
                    }else{
                        $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'invoice_payments.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "payment") = 0)';
                    }
                }
            }

            if($where_status != ''){
                array_push($where, 'AND ('. $where_status . ')');
            }
        }

        $from_date = $this->request->getPost('from_date') ? $this->request->getPost('from_date') : "";
        $to_date = $this->request->getPost('to_date') ? $this->request->getPost('to_date') : "";
       
        if ($from_date != '' && $to_date != '') {
            array_push($where, 'AND (' . get_db_prefix() . 'invoice_payments.payment_date >= "' . $from_date . '" and ' . get_db_prefix() . 'invoice_payments.payment_date <= "' . $to_date . '")');
        } elseif ($from_date != '') {
            array_push($where, 'AND (' . get_db_prefix() . 'invoice_payments.payment_date >= "' . $from_date . '")');
        } elseif ($to_date != '') {
            array_push($where, 'AND (' . get_db_prefix() . 'invoice_payments.payment_date <= "' . $to_date . '")');
        }

        $aColumns     = $select;
        $sIndexColumn = 'id';
        $sTable       = get_db_prefix() . 'invoice_payments';
        $join         = ['LEFT JOIN ' . get_db_prefix() . 'payment_methods ON ' . get_db_prefix() . 'payment_methods.id = ' . get_db_prefix() . 'invoice_payments.payment_method_id',
                        'LEFT JOIN ' . get_db_prefix() . 'acc_account_history ON ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'invoice_payments.id and ' . get_db_prefix() . 'acc_account_history.rel_id = "payment"',
                        'LEFT JOIN ' . get_db_prefix() . 'invoices ON ' . get_db_prefix() . 'invoices.id = ' . get_db_prefix() . 'invoice_payments.invoice_id',
                    ];
        $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['payment_method_id']);

        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row   = [];

            $row[] = format_to_date($aRow['date']);

            $row[] = to_currency($aRow['amount'], $currency_symbol);

            $row[] = $aRow['name'];
            $row[] = '<a href="' . get_uri('invoices/view/' . $aRow['invoice_id']) . '" target="_blank">' . get_invoice_id($aRow['invoice_id']) . '</a>';

            $status_name = app_lang('has_not_been_converted');
            $label_class = 'bg-secondary';

            if ($aRow['count_account_historys'] > 0) {
                $label_class = 'bg-success';
                $status_name = app_lang('acc_converted');
            } 

            $row[] = '<span class="mt0 badge large ' . $label_class . ' payment-status-' . $aRow['id'] . '">' . $status_name . '</span>';
            if($aRow['count_account_historys'] == 0){

                $options = '';

                $options .= modal_anchor(get_uri("accounting/convert_modal_form"), "<i data-feather='external-link' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('acc_convert'), "data-post-id" => $aRow['id'], "data-post-type" => 'payment'));

                $row[] =  $options;
            }else{
                $row[] =  $this->_convert_make_options_dropdown($aRow['id'], 'payment');
            }

            $output['data'][] = $row;
        }

        if($return_ajax) {
            echo json_encode($output);
            die();
        }else{
            if($id != '' && isset($output['data'][0])){
                return $output['data'][0];
            }else{

                return $output['data'];
            }
        }
    }

    /**
     * sales table
     * @return json
     */
    public function sales_invoice_table($id = '', $return_ajax = true)
    {
            $acc_closing_date = '';
            if(get_setting('acc_close_the_books') == 1){
                $acc_closing_date = get_setting('acc_closing_date');
            }

            $currency_symbol = get_setting("currency_symbol");

            $invoice_value_calculation_query = $this->Accounting_model->acc_get_invoice_value_calculation_query();

            $select = [
                'client_id',
                get_db_prefix() . 'invoices.id as id',
                get_db_prefix() . 'invoices.due_date as due_date',
                get_db_prefix() .'invoices.bill_date as date',
                '(select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'invoices.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "invoice") as count_account_historys',
                get_db_prefix() . 'invoices.status'
            ];
            $where = [];
            array_push($where, 'AND '. get_db_prefix() . 'invoices.deleted = 0');

            $invoice = $this->request->getPost('invoice') ? implode(",", $this->request->getPost('invoice')) : "";
            if ($invoice != '') {
                array_push($where, 'AND invoice_id IN (' . $invoice . ')');
            }

            $payment_mode = $this->request->getPost('payment_mode') ? implode(",", $this->request->getPost('payment_mode')) : "";
            if ($payment_mode != '') {
                array_push($where, 'AND payment_method_id IN (' . $payment_mode . ')');
            }  

            $status = $this->request->getPost('status') ? $this->request->getPost('status') : "";
            if ($status != '') {
                $where_status = '';
                foreach ($status as $key => $value) {
                    if($value == 'converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'invoices.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "invoice") > 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'invoices.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "invoice") > 0)';
                        }
                    }

                    if($value == 'has_not_been_converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'invoices.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "invoice") = 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'invoices.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "invoice") = 0)';
                        }
                    }
                }

                if($where_status != ''){
                    array_push($where, 'AND ('. $where_status . ')');
                }
            }

            $from_date = $this->request->getPost('from_date') ? $this->request->getPost('from_date') : "";
            $to_date = $this->request->getPost('to_date') ? $this->request->getPost('to_date') : "";
           
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'invoices.bill_date >= "' . $from_date . '" and ' . get_db_prefix() . 'invoices.bill_date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'invoices.bill_date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'invoices.bill_date <= "' . $to_date . '")');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'invoices';
            $join         = ['LEFT JOIN ' . get_db_prefix() . 'clients ON ' . get_db_prefix() . 'clients.id = ' . get_db_prefix() . 'invoices.client_id',
                            'LEFT JOIN (SELECT '.get_db_prefix().'taxes.* FROM '.get_db_prefix().'taxes) AS tax_table ON tax_table.id = '. get_db_prefix() . 'invoices.tax_id',
                            'LEFT JOIN (SELECT '.get_db_prefix().'taxes.* FROM '.get_db_prefix().'taxes) AS tax_table2 ON tax_table2.id = '. get_db_prefix() . 'invoices.tax_id2',
                            'LEFT JOIN (SELECT '.get_db_prefix().'taxes.* FROM '.get_db_prefix().'taxes) AS tax_table3 ON tax_table3.id = '. get_db_prefix() . 'invoices.tax_id3',
                        'LEFT JOIN (SELECT invoice_id, SUM(total) AS invoice_value FROM '.get_db_prefix().'invoice_items WHERE deleted=0 GROUP BY invoice_id) AS items_table ON items_table.invoice_id = '. get_db_prefix() . 'invoices.id'
                        ];

            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['company_name', $invoice_value_calculation_query . ' as invoice_value']);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $row[] = '<a href="' . get_uri('invoices/view/' . $aRow['id']) . '" target="_blank">' . get_invoice_id($aRow['id']) . '</a>';

                $row[] = format_to_date($aRow['date']);
                $row[] = to_currency($aRow['invoice_value'], $currency_symbol);

                $row[] = anchor(get_uri("clients/view/" . $aRow['client_id']), $aRow['company_name']);


                $status_name = app_lang('has_not_been_converted');
                $label_class = 'bg-secondary';

                $row[] = $this->_get_invoice_status_label($aRow['id']);

                if ($aRow['count_account_historys'] > 0) {
                    $label_class = 'bg-success';
                    $status_name = app_lang('acc_converted');
                } 

                $row[] = '<span class="mt0 badge large ' . $label_class . ' payment-status-' . $aRow['id'] . '">' . $status_name . '</span>';



                $options = '';
               
                if($aRow['count_account_historys'] == 0){

                    $options = '';
                    $options .= modal_anchor(get_uri("accounting/convert_modal_form"), "<i data-feather='external-link' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('acc_convert'), "data-post-id" => $aRow['id'], "data-post-type" => 'invoice'));

                    $row[] =  $options;
                }else{
                    $row[] =  $this->_convert_make_options_dropdown($aRow['id'], 'invoice');
                }

                $row[] =  $options;

                $output['data'][] = $row;
            }

            if($return_ajax) {
                echo json_encode($output);
                die();
            }else{
                if($id != '' && isset($output['data'][0])){
                    return $output['data'][0];
                }else{

                    return $output['data'];
                }
            }
    }

    /**
     * expenses table
     * @return json
     */
    public function expenses_table($id = '', $return_ajax = true)
    {
            $acc_closing_date = '';
            if(get_setting('acc_close_the_books') == 1){
                $acc_closing_date = get_setting('acc_closing_date');
            }
            $currency_symbol = get_setting("currency_symbol");
            $Expenses_model = model('Expenses_model');
            $select = [
                get_db_prefix() . 'expenses.id as id',
                'amount',
                get_db_prefix() . 'expense_categories.title as category_name',
                get_db_prefix() . 'expenses.title as title',
                get_db_prefix() . 'expenses.expense_date as expense_date',
                '(select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'expenses.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "expense") as count_account_historys'
            ];
            $where = [];
            array_push($where, 'AND '. get_db_prefix() . 'expenses.deleted = 0');

            $status = $this->request->getPost('status') ? $this->request->getPost('status') : "";
            if ($status != '') {
                $where_status = '';
                foreach ($status as $key => $value) {
                    if($value == 'converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'expenses.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "expense") > 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'expenses.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "expense") > 0)';
                        }
                    }

                    if($value == 'has_not_been_converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'expenses.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "expense") = 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'expenses.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "expense") = 0)';
                        }
                    }
                }

                if($where_status != ''){
                    array_push($where, 'AND ('. $where_status . ')');
                }
            }

            $from_date = $this->request->getPost('start_date') ? $this->request->getPost('start_date') : "";
            $to_date = $this->request->getPost('end_date') ? $this->request->getPost('end_date') : "";
           
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'expenses.expense_date >= "' . $from_date . '" and ' . get_db_prefix() . 'expenses.expense_date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'expenses.expense_date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'expenses.expense_date <= "' . $to_date . '")');
            }

            if ($id != '') {
                array_push($where, 'AND '.get_db_prefix() . 'expenses.id = "' . $id . '"');
            }

            $select_purchase = '0 as count_purchases';

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'expenses';
            $join         = [
                'JOIN ' . get_db_prefix() . 'expense_categories ON ' . get_db_prefix() . 'expense_categories.id = ' . get_db_prefix() . 'expenses.category_id',
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [$select_purchase]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $row[] = format_to_date($aRow['expense_date']);
                
                $row[] = $aRow['title'];

                $row[] = $aRow['category_name'];

                $expense = $Expenses_model->get_details(['id' => $aRow['id']])->getRow();

                $tax = 0;
                $tax2 = 0;
                if ($expense->tax_percentage) {
                    $tax = $expense->amount * ($expense->tax_percentage / 100);
                }
                if ($expense->tax_percentage2) {
                    $tax2 = $expense->amount * ($expense->tax_percentage2 / 100);
                }

                $row[] = to_currency($aRow['amount'] + $tax + $tax2, $currency_symbol);

                $status_name = app_lang('has_not_been_converted');
                $label_class = 'bg-secondary';

                if ($aRow['count_account_historys'] > 0) {
                    $label_class = 'bg-success';
                    $status_name = app_lang('acc_converted');
                } 

                $row[] = '<span class="mt0 badge large ' . $label_class . ' payment-status-' . $aRow['id'] . '">' . $status_name . '</span>';
                
                $options = '';
                if($aRow['count_account_historys'] == 0){

                    $options = '';

                    $options .= modal_anchor(get_uri("accounting/convert_modal_form"), "<i data-feather='external-link' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('acc_convert'), "data-post-id" => $aRow['id'], "data-post-type" => 'expense'));

                    $row[] =  $options;
                }else{
                    $row[] =  $this->_convert_make_options_dropdown($aRow['id'], 'expense');
                }

                $row[] =  $options;

                $output['data'][] = $row;
            }

            if($return_ajax) {
                echo json_encode($output);
                die();
            }else{
                if($id != '' && isset($output['data'][0])){
                    return $output['data'][0];
                }else{

                    return $output['data'];
                }
            }
    }

    /**
     * banking table
     * @return json
     */
    public function banking_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $acc_closing_date = '';
            if(get_setting('acc_close_the_books') == 1){
                $acc_closing_date = get_setting('acc_closing_date');
            }
            $select = [
                '1', // bulk actions
                'id',
                get_db_prefix() . 'acc_transaction_bankings.date as date',
                'withdrawals',
                'deposits',
                'payee',
                'description',
                '(select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'acc_transaction_bankings.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "banking") as count_account_historys'
            
            ];
            $where = [];

            $from_date = '';
            $to_date   = '';
            if ($this->request->getPost('from_date')) {
                $from_date = $this->request->getPost('from_date');
                if (!$this->Accounting_model->check_format_date($from_date)) {
                    $from_date = to_sql_date($from_date);
                }
            }

            if ($this->request->getPost('to_date')) {
                $to_date = $this->request->getPost('to_date');
                if (!$this->Accounting_model->check_format_date($to_date)) {
                    $to_date = to_sql_date($to_date);
                }
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'acc_transaction_bankings.date >= "' . $from_date . '" and ' . get_db_prefix() . 'acc_transaction_bankings.date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'acc_transaction_bankings.date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'acc_transaction_bankings.date <= "' . $to_date . '")');
            }

            if ($this->request->getPost('status')) {
                $status = $this->request->getPost('status');
                $where_status = '';
                foreach ($status as $key => $value) {
                    if($value == 'converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'acc_transaction_bankings.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "banking") > 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'acc_transaction_bankings.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "banking") > 0)';
                        }
                    }

                    if($value == 'has_not_been_converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'acc_transaction_bankings.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "banking") = 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'acc_transaction_bankings.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "banking") = 0)';
                        }
                    }
                }

                if($where_status != ''){
                    array_push($where, 'AND ('. $where_status . ')');
                }
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'acc_transaction_bankings';
            $join         = [];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
                $categoryOutput = format_to_date($aRow['date']);
                $amount = $aRow['withdrawals'] > 0 ? $aRow['withdrawals'] : $aRow['deposits'];
                $categoryOutput .= '<div class="row-options">';
                if ($aRow['count_account_historys'] == 0) {
                    if (has_permission('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))) {
                        $categoryOutput .= '<a href="#" onclick="convert(this); return false;" class="text-success" id="banking-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="banking" data-amount="'.$amount.'">' . app_lang('acc_convert') . '</a>';
                    }
                }else{
                    if (has_permission('accounting_transaction', '', 'edit')) {
                        $categoryOutput .= '<a href="#" onclick="convert(this); return false;" id="banking-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="banking" data-amount="'.$amount.'">' . app_lang('edit') . '</a>';
                    }
                    if (has_permission('accounting_transaction', '', 'delete')) {
                        $categoryOutput .= ' | <a href="#" onclick="delete_convert('.$aRow['id'].', \'banking\'); return false;" class="text-danger">' . app_lang('delete') . '</a>';
                    }
                }

                $categoryOutput .= '</div>';
                $row[] = $categoryOutput;

                $row[] = to_currency($aRow['withdrawals'], $currency->name);
                $row[] = to_currency($aRow['deposits'], $currency->name);

                $row[] = $aRow['payee'];
                $row[] = $aRow['description'];

                $status_name = app_lang('has_not_been_converted');
                $label_class = 'default';

                if ($aRow['count_account_historys'] > 0) {
                    $label_class = 'success';
                    $status_name = app_lang('acc_converted');
                } 

                $row[] = '<span class="label label-' . $label_class . ' s-status payment-status-' . $aRow['id'] . '">' . $status_name . '</span>';

                $options = '';
                if($aRow['count_account_historys'] == 0 && has_permission('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))){
                    $options = icon_btn('#', 'share', 'btn-success', [
                        'title' => app_lang('acc_convert'),
                        'data-id' =>$aRow['id'],
                        'data-amount' => $amount,
                        'data-type' => 'banking',
                        'onclick' => 'convert(this); return false;'
                    ]);
                }

                $row[] =  $options;

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * manage chart of accounts
     */
    public function chart_of_accounts(){
        if (!has_permission('accounting_chart_of_accounts', '', 'view')) {
            access_denied('chart_of_accounts');
        }

        $data['title'] = app_lang('chart_of_accounts');
        $data['account_types'] = $this->Accounting_model->get_account_types();
        $data['detail_types'] = $this->Accounting_model->get_account_type_details();
        $data['accounts'] = $this->Accounting_model->get_accounts();
        return $this->template->rander('Accounting\Views\chart_of_accounts/manage', $data);
    }

    /**
     * setting
     * @return view
     */
    public function setting()
    {
        if (!has_permission('accounting_setting', '', 'view')) {
            access_denied('setting');
        }
        
        $data          = [];
        $data['group'] = $this->request->getGet('group');
        $data['accounts'] = $this->Accounting_model->get_accounts();

        $data['tab'][] = 'general';
        $data['tab'][] = 'banking_rules';
        $data['tab'][] = 'mapping_setup';
        $data['tab'][] = 'account_type_details';
        $data['tab'][] = 'plaid_environment';
        
        $data['tab_2'] = $this->request->getGet('tab');
        if ($data['group'] == '') {
            $data['group'] = 'general';
        }

        if ($data['group'] == 'mapping_setup') {
            if ($data['tab_2'] == '') {
                $data['tab_2'] = 'general_mapping_setup';
            }

            $accounts_dropdown = [];
            foreach ($data['accounts'] as $account) {
                $accounts_dropdown[$account['id']] = $account['name'];
            }

            $data['accounts_dropdown'] = $accounts_dropdown;

            $data['items'] = $this->Accounting_model->get_items_not_yet_auto();

            $Items_model = model('Items_model');
            $data['_items'] = $Items_model->get_details()->getResultArray();

            $Taxes_model = model('Taxes_model');
            $data['_taxes'] = $Taxes_model->get_details()->getResultArray();

            $data['taxes'] = $this->Accounting_model->get_taxes_not_yet_auto();

            $Expense_categories_model = model('Expense_categories_model');
            $data['_categories'] = $Expense_categories_model->get_details()->getResultArray();
            $data['categories'] = $this->Accounting_model->get_expense_category_not_yet_auto();

            $Payment_methods_model = model('Payment_methods_model');
            $data['_payment_modes'] = $Payment_methods_model->get_details()->getResultArray();
            $data['payment_modes'] = $this->Accounting_model->get_payment_mode_not_yet_auto();
        }elseif ($data['group'] == 'account_type_details') {
            $data['account_types'] = $this->Accounting_model->get_account_types();
        }
        $data['title']        = app_lang($data['group']);
        $data['tabs']['view'] = 'Accounting\Views\setting/' . $data['group'];
        $data['tab_2'] = 'Accounting\Views\setting/'.$data['tab_2'];

        return $this->template->rander('Accounting\Views\setting/manage', $data);
    }

    /**
     * update general setting
     */
    public function update_general_setting(){
        if (!has_permission('accounting_setting', '', 'edit') && !is_admin()) {
            access_denied('accounting_setting');
        }
        $data = $this->request->getPost();
        $success = $this->Accounting_model->update_general_setting($data);
        if($success == true){
            $message = sprintf(app_lang('updated_successfully'), app_lang('setting'));
            $this->session->setFlashdata("success_message", $message);
        }

        app_redirect('accounting/setting?group=general');
    }

    /**
     * update automatic conversion
     */
    public function update_automatic_conversion(){
        if (!has_permission('accounting_setting', '', 'edit') && !is_admin()) {
            access_denied('accounting_setting');
        }
        $data = $this->request->getPost();
        $success = $this->Accounting_model->update_automatic_conversion($data);
        if($success == true){
            $message = sprintf(app_lang('updated_successfully'), app_lang('setting'));
            $this->session->setFlashdata("success_message", $message);
        }
        app_redirect('accounting/setting?group=mapping_setup');
    }

    /**
     * accounts table
     * @return json
     */
    public function accounts_table()
    {
            $acc_enable_account_numbers = get_setting('acc_enable_account_numbers');
            $acc_show_account_numbers = get_setting('acc_show_account_numbers');

            $accounts = $this->Accounting_model->get_accounts();
            $account_types = $this->Accounting_model->get_account_types();
            $detail_types = $this->Accounting_model->get_account_type_details();

            $account_name = [];
            $account_type_name = [];
            $detail_type_name = [];

            foreach ($accounts as $key => $value) {
                $account_name[$value['id']] = $value['name'];
            }

            foreach ($account_types as $key => $value) {
                $account_type_name[$value['id']] = $value['name'];
            }

            foreach ($detail_types as $key => $value) {
                $detail_type_name[$value['id']] = $value['name'];
            }

            $array_history = [2,3,4,5,7,8,9,10];
            
            if($acc_enable_account_numbers == 1 && $acc_show_account_numbers == 1){
                $select = [
                    'id',
                    'number',
                    'name',
                    'parent_account',
                    'account_type_id',
                    'account_detail_type_id',
                    'balance',
                    'key_name',
                    'active',
                ];
            }else {
                $select = [
                    'id',
                    'name',
                    'parent_account',
                    'account_type_id',
                    'account_detail_type_id',
                    'balance',
                    'key_name',
                    'active',
                ];
            }

            $where = [];

            $accounting_method = get_setting('acc_accounting_method');

            if($accounting_method == 'cash'){
                $debit = '(SELECT sum(debit) as debit FROM '.get_db_prefix().'acc_account_history where (account = '.get_db_prefix().'acc_accounts.id or parent_account = '.get_db_prefix().'acc_accounts.id) AND (('.get_db_prefix().'acc_account_history.rel_type = "invoice" AND '.get_db_prefix().'acc_account_history.paid = 1) or rel_type != "invoice")) as debit';
                $credit = '(SELECT sum(credit) as credit FROM '.get_db_prefix().'acc_account_history where (account = '.get_db_prefix().'acc_accounts.id or parent_account = '.get_db_prefix().'acc_accounts.id) AND (('.get_db_prefix().'acc_account_history.rel_type = "invoice" AND '.get_db_prefix().'acc_account_history.paid = 1) or rel_type != "invoice")) as credit';
            }else{
                $debit = '(SELECT sum(debit) as debit FROM '.get_db_prefix().'acc_account_history where (account = '.get_db_prefix().'acc_accounts.id or parent_account = '.get_db_prefix().'acc_accounts.id)) as debit';
                $credit = '(SELECT sum(credit) as credit FROM '.get_db_prefix().'acc_account_history where (account = '.get_db_prefix().'acc_accounts.id or parent_account = '.get_db_prefix().'acc_accounts.id)) as credit';
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'acc_accounts';
            $join         = [];
            $result       = $this->Accounting_model->get_account_data_tables($aColumns, $sIndexColumn, $sTable, $join, $where, ['number', 'description', 'balance_as_of', $debit, $credit, 'default_account']);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $categoryOutput = '';
                if(isset($aRow['level'])){
                    for ($i=0; $i < $aRow['level']; $i++) { 
                        $categoryOutput .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    }
                }
                
                if($acc_enable_account_numbers == 1 && $acc_show_account_numbers == 1 && $aRow['number'] != ''){
                    $categoryOutput .= $aRow['number'] .' - ';
                }

                if($aRow['name'] == ''){
                    $categoryOutput .= app_lang($aRow['key_name']);
                }else{
                    $categoryOutput .= $aRow['name'];
                }

                
                $row[] = $categoryOutput;
                if($aRow['parent_account'] != '' && $aRow['parent_account'] != 0){
                    $row[] = (isset($account_name[$aRow['parent_account']]) ? $account_name[$aRow['parent_account']] : '');
                }else{
                    $row[] = '';
                }
                $row[] = isset($account_type_name[$aRow['account_type_id']]) ? $account_type_name[$aRow['account_type_id']] : '';
                $row[] = isset($detail_type_name[$aRow['account_detail_type_id']]) ? $detail_type_name[$aRow['account_detail_type_id']] : '';
                if($aRow['account_type_id'] == 11 || $aRow['account_type_id'] == 12 || $aRow['account_type_id'] == 8 || $aRow['account_type_id'] == 9 || $aRow['account_type_id'] == 10 || $aRow['account_type_id'] == 7){
                    $row[] = to_decimal_format($aRow['credit'] - $aRow['debit']);
                }else{
                    $row[] = to_decimal_format($aRow['debit'] - $aRow['credit']);
                }
                $row[] = '';

                $checked = '';
                if ($aRow['active'] == 1) {
                    $checked = 'checked';
                }

                $_data = '<div class="onoffswitch">
                    <input type="checkbox" ' . '' . ' data-switch-url="' . get_uri() . 'accounting/change_account_status" name="onoffswitch" class="onoffswitch-checkbox form-check-input" id="c_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . $checked . '>
                    <label class="onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
                </div>';

                // For exporting
                $_data .= '<span class="hide">' . ($checked == 'checked' ? app_lang('is_active_export') : app_lang('is_not_active_export')) . '</span>';
                $row[] = $_data;
                
                $options = "";

                if (has_permission('accounting_chart_of_accounts', '', 'edit')) {
                    $options .= '<a href="#" onclick="edit_account(' . $aRow['id'] . '); return false;" class="edit"><i data-feather="edit" class="icon-16"></i></a>';
                }

                if (has_permission('accounting_chart_of_accounts', '', 'delete') && $aRow['default_account'] == 0) {
                    $options .= '<a href="' . get_uri('accounting/delete_account/' . $aRow['id']) . '" class="delete"><i data-feather="x" class="icon-16"></i></a>';
                }


                $row[] =  $options;

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     *
     *  add or edit account
     *  @param  integer  $id     The identifier
     *  @return view
     */
    public function account()
    {
        if (!has_permission('accounting_chart_of_accounts', '', 'edit') && !has_permission('accounting_chart_of_accounts', '', 'create')) {
            access_denied('accounting');
        }

        if ($this->request->getPost()) {
            $data = $this->request->getPost();
            $message = '';
            if ($data['id'] == '') {
                if (!has_permission('accounting_chart_of_accounts', '', 'create')) {
                    access_denied('accounting');
                }
                $success = $this->Accounting_model->add_account($data);
                if ($success) {
                    $message = sprintf(app_lang('added_successfully'), app_lang('acc_account'));
                    $this->session->setFlashdata("success_message", $message);
                }else {
                    $message = app_lang('add_failure');
                    $this->session->setFlashdata("error_message", $message);
                }
            } else {
                if (!has_permission('accounting_chart_of_accounts', '', 'edit')) {
                    access_denied('accounting');
                }
                $id = $data['id'];
                unset($data['id']);
                $success = $this->Accounting_model->update_account($data, $id);
                if ($success) {
                    $message = sprintf(app_lang('updated_successfully'), app_lang('acc_account'));
                    $this->session->setFlashdata("success_message", $message);
                }else {
                    $message = app_lang('updated_fail');
                    $this->session->setFlashdata("error_message", $message);
                }
            }

            app_redirect('accounting/chart_of_accounts');
        }
    }

    /**
     * get data convert
     * @param  integer $id   
     * @param  string $type 
     * @return json       
     */
    public function convert_modal_form(){

        $html = '';
        $list_item = [];
        $id = $this->request->getPost('id');
        $type = $this->request->getPost('type');
        $currency_symbol = get_setting("currency_symbol");
        $amount = 0;

        if($type == 'payment'){
            $Invoice_payments_model = model('Invoice_payments_model');
            $payment = $Invoice_payments_model->get_one(['id' => $id]);
            $Payment_methods_model = model('Payment_methods_model');
            $payment_method = $Payment_methods_model->get_one(['id' => $payment->payment_method_id]);
            $Invoices_model = model('Invoices_model');
            $invoice = $Invoices_model->get_one(['id' => $payment->invoice_id]);
            $html = '<table class="table border table-striped no-margin">
                      <tbody>
                         <tr class="project-overview">
                            <td class="bold" width="30%">'. app_lang('invoice').'</td>
                            <td>'. '<a href="' . get_uri('invoices/view/' . $payment->invoice_id) . '" target="_blank">' . get_invoice_id($payment->invoice_id) . '</a>' .'</td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('acc_amount').'</td>
                            <td>'. to_decimal_format($payment->amount) .'</td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('payment_date').'</td>
                            <td>'. format_to_date($payment->payment_date) .'</td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('payment_method').'</td>
                            <td>'. html_entity_decode($payment_method->title) .'</td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('note').'</td>
                            <td colspan="2">'. html_entity_decode($payment->note) .'</td>
                         </tr>';
            $amount = $payment->amount;
            
            $_html = '';
           
            $html .=   '</tbody>
                  </table>';
            
            $debit = get_setting('acc_payment_deposit_to');
            $credit = get_setting('acc_payment_payment_account');
        }elseif ($type == 'expense') {
            $Expenses_model = model('Expenses_model');
            $expense = $Expenses_model->get_details(['id' => $id])->getRow();

            $Expense_categories_model = model('Expense_categories_model');
            $category = $Expense_categories_model->get_one($expense->category_id);

            $tax = 0;
            $tax2 = 0;
            if ($expense->tax_percentage) {
                $tax = $expense->amount * ($expense->tax_percentage / 100);
            }
            if ($expense->tax_percentage2) {
                $tax2 = $expense->amount * ($expense->tax_percentage2 / 100);
            }

            $html = '<table class="table border table-striped no-margin">
                      <tbody>
                        <tr class="project-overview">
                            <td class="bold" width="30%">'. app_lang('category').'</td>
                            <td>'. $category->title  .'</td>
                            <td></td>
                         </tr>
                        <tr class="project-overview">
                            <td class="bold">'. app_lang('title').'</td>
                            <td>'. $expense->title  .'</td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('acc_amount').'</td>
                            <td>'. to_currency($expense->amount + $tax + $tax2, $currency_symbol) .'</td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('date').'</td>
                            <td>'. format_to_date($expense->expense_date) .'</td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('note').'</td>
                            <td colspan="2">'. html_entity_decode($expense->description) .'</td>
                         </tr>';


                

            $amount = $expense->amount;

            $html .=    '</tbody>
                  </table>';

            $debit = get_setting('acc_expense_deposit_to');
            $credit = get_setting('acc_expense_payment_account');
        }elseif ($type == 'banking') {
            $banking = $this->Accounting_model->get_transaction_banking($id);
            $html = '<table class="table border table-striped no-margin">
                      <tbody>
                        <tr class="project-overview">
                            <td class="bold" width="30%">'. app_lang('expense_dt_table_heading_date').'</td>
                            <td>'. format_to_date($banking->date)  .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('withdrawals').'</td>
                            <td>'. to_currency($banking->withdrawals, $currency->name) .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('deposits').'</td>
                            <td>'. to_currency($banking->deposits, $currency->name) .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('payee').'</td>
                            <td>'. $banking->payee .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('description').'</td>
                            <td>'. $banking->description .'</td>
                         </tr>
                        </tbody>
                  </table>';

            $debit = 0;
            $credit = 0;
        }elseif ($type == 'invoice') {
            $Invoices_model = model('Invoices_model');
            $invoice = $Invoices_model->get_one(['id' => $id]);
            $invoice_summary = $Invoices_model->get_invoice_total_summary($id);
            $accounts = $this->Accounting_model->get_accounts();
            $accounts_dropdown = [];
            foreach ($accounts as $account) {
                $accounts_dropdown[$account['id']] = $account['name'];
            }

            $html = '<table class="table border table-striped no-margin">
                      <tbody>
                        <tr class="project-overview">
                            <td class="bold" width="30%">'. app_lang('number').'</td>
                            <td>'. get_invoice_id($invoice->id)  .'</td>
                            <td></td>
                        </tr>
                        <tr class="project-overview">
                            <td class="bold" width="30%">'. app_lang('bill_date').'</td>
                            <td>'. format_to_date($invoice->bill_date)  .'</td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="30%">'. app_lang('due_date').'</td>
                            <td>'. format_to_date($invoice->due_date)  .'</td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('customer').'</td>
                            <td>'. get_company_name($invoice->client_id) .'</td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('total').'</td>
                            <td>'. to_currency($invoice_summary->invoice_total, $currency_symbol) .'</td>
                            <td></td>
                         </tr>';

            
            $amount = 1;

            $html .=    '</tbody>
                  </table>';

            $Invoice_items_model = model('Invoice_items_model');
            $items = $Invoice_items_model->get_details(array("invoice_id" => $id))->getResultArray();


            if($items){
                $payment_account = get_setting('acc_invoice_payment_account');
                $deposit_to = get_setting('acc_invoice_deposit_to');

                $html .= '<h4>'.app_lang('list_of_items').'</h4>';
                foreach ($items as $value) {
                    $item = $this->Accounting_model->get_item_by_name($value['description']);
                    $item_id = '-1';
                    if(isset($item->id)){
                        $item_id = $item->id;
                    }
                    $list_item[] = $item_id;

                    $db = db_connect('default');
                    $db_builder = $db->table(get_db_prefix(). 'acc_account_history');

                    $db_builder->where('rel_id', $id);
                    $db_builder->where('rel_type', $type);
                    $db_builder->where('(itemable_id = '.$value['id'].' or item = '.$item_id.')');
                    $account_history = $db_builder->get()->getResultArray();
                    foreach ($account_history as $key => $val) {
                        if($val['debit'] > 0){
                            $debit = $val['account'];
                        }

                        if($val['credit'] > 0){
                            $credit =  $val['account'];
                        }
                    }

                    if($account_history){
                        $html .= '
                        <div class="div_content">
                        <h5>'.$value['title'].': '.to_currency(($value['quantity'] * $value['rate']), $currency_symbol).'</h5>
                        <div class="row">
                                '.form_hidden('item_amount['.$value['id'].']', $value['quantity'] * $value['rate']).'
                              <div class="col-md-6"> 
                                <div class="form-group">
                                    <label for="payment_account" class="">'. app_lang('payment_account').'</label>
                                     '.
                                     form_dropdown('payment_account['.$value['id'].']', $accounts_dropdown, array($credit), "class='select2 validate-hidden' id='payment_account' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'").'
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                    <label for="deposit_to" class="">'. app_lang('deposit_to').'</label>
                                    '.
                                     form_dropdown('deposit_to['.$value['id'].']', $accounts_dropdown, array($debit), "class='select2 validate-hidden' id='deposit_to' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'")
                                    .'
                                </div>
                              </div>
                          </div>
                        </div>';
                    }else{
                        $item_automatic = $this->Accounting_model->get_item_automatic($item_id);

                        if($item_automatic){
                            $html .= '
                            <div class="div_content">
                                <h5>'.$value['title'].'</h5>
                                <div class="row">
                                '.form_hidden('item_amount['.$value['id'].']', $value['quantity'] * $value['rate']).'
                                  <div class="col-md-6"> 
                                    <div class="form-group">
                                        <label for="payment_account" class="">'. app_lang('payment_account').'</label>
                                         '.
                                         form_dropdown('payment_account['.$value['id'].']', $accounts_dropdown, array($item_automatic->income_account), "class='select2 validate-hidden' id='payment_account' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'").'
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="deposit_to" class="">'. app_lang('deposit_to').'</label>
                                            '.
                                             form_dropdown('deposit_to['.$value['id'].']', $accounts_dropdown, array($deposit_to ? $deposit_to : ''), "class='select2 validate-hidden' id='deposit_to' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'")
                                            .'
                                        </div>
                                  </div>
                              </div>
                            </div>';
                        }else{

                            $html .= '
                            <div class="div_content">
                                <h5>'.$value['title'].'</h5>
                                <div class="row">
                                '.form_hidden('item_amount['.$value['id'].']', $value['quantity'] * $value['rate']).'
                                  <div class="col-md-6">
                                   <div class="form-group">
                                    <label for="payment_account" class="">'. app_lang('payment_account').'</label>
                                         '.
                                         form_dropdown('payment_account['.$value['id'].']', $accounts_dropdown, array($payment_account), "class='select2 validate-hidden' id='payment_account' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'").'
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                  <div class="form-group">
                                        <label for="deposit_to" class="">'. app_lang('deposit_to').'</label>
                                        '.
                                             form_dropdown('deposit_to['.$value['id'].']', $accounts_dropdown, array($deposit_to ? $deposit_to : ''), "class='select2 validate-hidden' id='deposit_to' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'")
                                            .'
                                    </div>
                                  </div>
                              </div>
                            </div>';
                        }
                    }
                }
            }

            $debit = get_setting('acc_invoice_deposit_to');
            $credit = get_setting('acc_invoice_payment_account');
        }elseif ($type == 'payslip') {
            $this->db->where('id', $id);
            $payslip = $this->db->get(get_db_prefix(). 'hrp_payslips')->row();

            $this->db->where('payslip_id', $id);
            $payslip_details = $this->db->get(get_db_prefix(). 'hrp_payslip_details')->result_array();

            $accounts = $this->Accounting_model->get_accounts();


            $payment_account = get_setting('acc_pl_total_insurance_payment_account');
            $deposit_to = get_setting('acc_pl_total_insurance_deposit_to');

            if($payslip->payslip_status == 'payslip_closing'){
                $_data_status = ' <span class="label label-success "> '.app_lang($payslip->payslip_status).' </span>';
            }else{
                $_data_status = ' <span class="label label-primary"> '.app_lang($payslip->payslip_status).' </span>';
            }
            $total_insurance = 0;
            $net_pay = 0;
            $income_tax_paye = 0;
            foreach ($payslip_details as $key => $value) {
                if(is_numeric($value['total_insurance'])){
                    $total_insurance += $value['total_insurance'];
                }

                if(is_numeric($value['net_pay'])){
                    $net_pay += $value['net_pay'];
                }

                if(is_numeric($value['income_tax_paye'])){
                    $income_tax_paye += $value['income_tax_paye'];
                }
            }

            $html = '<table class="table border table-striped no-margin">
                      <tbody>
                        <tr class="project-overview">
                            <td class="bold" width="30%">'. app_lang('payslip_name').'</td>
                            <td>'. $payslip->payslip_name  .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="30%">'. app_lang('payslip_name').'</td>
                            <td>'. get_payslip_template_name($payslip->payslip_template_id) .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="30%">'. app_lang('payslip_month').'</td>
                            <td>'. date('m-Y', strtotime($payslip->payslip_month))  .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="30%">'. app_lang('date_created').'</td>
                            <td>'. _dt($payslip->date_created)  .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold" width="30%">'. app_lang('status').'</td>
                            <td>'. $_data_status  .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('ps_total_insurance').'</td>
                            <td>'. to_currency($total_insurance, $currency->name) .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('ps_income_tax_paye').'</td>
                            <td>'. to_currency($income_tax_paye, $currency->name) .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('ps_net_pay').'</td>
                            <td>'. to_currency($net_pay, $currency->name) .'</td>
                         </tr>
                        </tbody>
                  </table>';

                $this->db->where('rel_id', $id);
                $this->db->where('rel_type', $type);
                $this->db->where('payslip_type', 'total_insurance');
                $account_history = $this->db->get(get_db_prefix(). 'acc_account_history')->result_array();
                
                $payment_account_insurance = get_setting('acc_pl_total_insurance_payment_account');
                $deposit_to_insurance = get_setting('acc_pl_total_insurance_deposit_to');
                foreach ($account_history as $key => $val) {
                    if($val['debit'] > 0){
                        $deposit_to_insurance =  $val['account'];
                    }

                    if($val['credit'] > 0){
                        $payment_account_insurance = $val['account'];
                    }
                }

                $html .= '
                        <div class="div_content">
                            <h5>'.app_lang('ps_total_insurance').'</h5>
                            <div class="row">
                            '.form_hidden('total_insurance', $total_insurance).'
                              <div class="col-md-6"> '.
                                render_select('payment_account_insurance',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account_insurance,array(),array(),'','',false) .'
                              </div>
                              <div class="col-md-6">
                                '. render_select('deposit_to_insurance',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to_insurance,array(),array(),'','',false).'
                              </div>
                          </div>
                        </div>';

                $this->db->where('rel_id', $id);
                $this->db->where('rel_type', $type);
                $this->db->where('payslip_type', 'tax_paye');
                $account_history = $this->db->get(get_db_prefix(). 'acc_account_history')->result_array();
                
                $payment_account_tax_paye = get_setting('acc_pl_tax_paye_payment_account');
                $deposit_to_tax_paye = get_setting('acc_pl_tax_paye_deposit_to');
                foreach ($account_history as $key => $val) {
                    if($val['debit'] > 0){
                        $deposit_to_tax_paye =  $val['account'];
                    }

                    if($val['credit'] > 0){
                        $payment_account_tax_paye = $val['account'];
                    }
                }

                $html .= '
                        <div class="div_content">
                            <h5>'.app_lang('ps_income_tax_paye').'</h5>
                            <div class="row">
                            '.form_hidden('tax_paye', $income_tax_paye).'
                              <div class="col-md-6"> '.
                                render_select('payment_account_tax_paye',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account_tax_paye,array(),array(),'','',false) .'
                              </div>
                              <div class="col-md-6">
                                '. render_select('deposit_to_tax_paye',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to_tax_paye,array(),array(),'','',false).'
                              </div>
                          </div>
                        </div>';
                        
                $this->db->where('rel_id', $id);
                $this->db->where('rel_type', $type);
                $this->db->where('payslip_type', 'net_pay');
                $account_history = $this->db->get(get_db_prefix(). 'acc_account_history')->result_array();
                
                $payment_account_net_pay = get_setting('acc_pl_net_pay_payment_account');
                $deposit_to_net_pay = get_setting('acc_pl_net_pay_deposit_to');
                foreach ($account_history as $key => $val) {
                    if($val['debit'] > 0){
                        $deposit_to_net_pay =  $val['account'];
                    }

                    if($val['credit'] > 0){
                        $payment_account_net_pay = $val['account'];
                    }
                }

                $html .= '
                        <div class="div_content">
                            <h5>'.app_lang('ps_net_pay').'</h5>
                            <div class="row">
                            '.form_hidden('net_pay', $net_pay).'
                              <div class="col-md-6"> '.
                                render_select('payment_account_net_pay',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account_net_pay,array(),array(),'','',false) .'
                              </div>
                              <div class="col-md-6">
                                '. render_select('deposit_to_net_pay',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to_net_pay,array(),array(),'','',false).'
                              </div>
                          </div>
                        </div>';

            $debit = get_setting('acc_expense_deposit_to');
            $credit = get_setting('acc_expense_payment_account');
        }elseif ($type == 'purchase_order') {
            $accounts = $this->Accounting_model->get_accounts();

            $this->load->model('purchase/purchase_model');
            $purchase_order = $this->purchase_model->get_pur_order($id);
            $purchase_order_detail = $this->purchase_model->get_pur_order_detail($id);

            $html = '<table class="table border table-striped no-margin">
                      <tbody>
                        <tr class="project-overview">
                            <td class="bold" width="30%">'. app_lang('purchase_order').'</td>
                            <td>'. '<a href="' . get_uri('purchase/purchase_order/' . $purchase_order->id) . '">'.$purchase_order->pur_order_number. '</a>'  .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('order_date').'</td>
                            <td>'. format_to_date($purchase_order->order_date) .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('vendor').'</td>
                            <td>'. '<a href="' . get_uri('purchase/vendor/' . $purchase_order->vendor) . '" >' .  get_vendor_company_name($purchase_order->vendor) . '</a>' .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('po_value').'</td>
                            <td>'. to_currency($purchase_order->subtotal, $currency->name) .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('tax_value').'</td>
                            <td>'. to_currency($purchase_order->total_tax, $currency->name) .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('po_value_included_tax').'</td>
                            <td>'. to_currency($purchase_order->total, $currency->name) .'</td>
                         </tr>
                        </tbody>
                  </table>';

            if($purchase_order_detail){
                $payment_account = get_setting('acc_pur_order_payment_account');
                $deposit_to = get_setting('acc_pur_order_deposit_to');

                $html .= '<h4>'.app_lang('list_of_items').'</h4>';
                foreach ($purchase_order_detail as $value) {

                    $this->db->where('id', $value['item_code']);
                    $item = $this->db->get(get_db_prefix().'items')->row();

                    $item_description = '';
                    if(isset($item) && isset($item->commodity_code) && isset($item->description)){
                       $item_description = $item->commodity_code.' - '.$item->description;
                    }

                    $item_id = 0;
                    if(isset($item->id)){
                        $item_id = $item->id;
                    }

                    if($item_id == 0){
                        continue;
                    }
                    $list_item[] = $item_id;

                    $this->db->where('rel_id', $id);
                    $this->db->where('rel_type', $type);
                    $this->db->where('item', $item_id);
                    $account_history = $this->db->get(get_db_prefix(). 'acc_account_history')->result_array();
                    
                    foreach ($account_history as $key => $val) {
                        if($val['debit'] > 0){
                            $debit = $val['account'];
                        }

                        if($val['credit'] > 0){
                            $credit =  $val['account'];
                        }
                    }

                    if($account_history){
                        $html .= '
                        <div class="div_content">
                        <h5>'.$item_description.'</h5>
                        <div class="row">
                                '.form_hidden('item_amount['.$item_id.']', $value['into_money']).'
                              <div class="col-md-6"> '.
                                render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$credit,array(),array(),'','',false) .'
                              </div>
                              <div class="col-md-6">
                                '. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$debit,array(),array(),'','',false).'
                              </div>
                          </div>
                        </div>';
                    }else{
                        $item_automatic = $this->Accounting_model->get_item_automatic($item_id);

                        if($item_automatic){
                            $html .= '
                        <div class="div_content">
                            <h5>'.$item_description.'</h5>
                            <div class="row">
                            '.form_hidden('item_amount['.$item_id.']', $value['into_money']).'
                              <div class="col-md-6"> '.
                                render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account,array(),array(),'','',false) .'
                              </div>
                              <div class="col-md-6">
                                '. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$item_automatic->expence_account,array(),array(),'','',false).'
                              </div>
                          </div>
                        </div>';
                        }else{

                            $html .= '
                        <div class="div_content">
                            <h5>'.$item_description.'</h5>
                            <div class="row">
                            '.form_hidden('item_amount['.$item_id.']', $value['into_money']).'
                              <div class="col-md-6"> '.
                                render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account,array(),array(),'','',false) .'
                              </div>
                              <div class="col-md-6">
                                '. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to,array(),array(),'','',false).'
                              </div>
                          </div>
                        </div>';
                        }
                    }
                }
            }

            $debit = 0;
            $credit = 0;
        }elseif ($type == 'stock_export') {
            $this->load->model('warehouse/warehouse_model');
            $goods_delivery = $this->warehouse_model->get_goods_delivery($id);
            $goods_delivery_detail = $this->warehouse_model->get_goods_delivery_detail($id);
            $accounts = $this->Accounting_model->get_accounts();
            $status = '';

            if($goods_delivery->approval == 1){
                $status = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">'.app_lang('approved').'</span><span class="hide">, </span></span>&nbsp';
            }elseif($goods_delivery->approval == 0){
                $status = '<span class="label label-tag tag-id-1 label-tab2"><span class="tag">'.app_lang('not_yet_approve').'</span><span class="hide">, </span></span>&nbsp';
            }elseif($goods_delivery->approval == -1){
                $status = '<span class="label label-tag tag-id-1 label-tab3"><span class="tag">'.app_lang('reject').'</span><span class="hide">, </span></span>&nbsp';
            }

            $html = '<table class="table border table-striped no-margin">
                      <tbody>
                        <tr class="project-overview">
                            <td class="bold" width="30%">'. app_lang('expense_dt_table_heading_date').'</td>
                            <td><a href="' . get_uri('warehouse/view_delivery/' . $goods_delivery->id ).'">' . $goods_delivery->goods_delivery_code . '</a></td>
                         </tr>
                        <tr class="project-overview">
                            <td class="bold" width="30%">'. app_lang('accounting_date').'</td>
                            <td>'. format_to_date($goods_delivery->date_c)  .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('status').'</td>
                            <td>'. $status .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('subtotal').'</td>
                            <td>'. to_currency($goods_delivery->total_money, $currency->name) .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('total_discount').'</td>
                            <td>'. to_currency($goods_delivery->total_discount, $currency->name) .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('total_money').'</td>
                            <td>'. to_currency($goods_delivery->after_discount, $currency->name) .'</td>
                         </tr>
                        </tbody>
                  </table>';

            if($goods_delivery_detail){
                $payment_account = get_setting('acc_wh_stock_export_payment_account');
                $deposit_to = get_setting('acc_wh_stock_export_deposit_to');

                $html .= '<h4>'.app_lang('list_of_items').'</h4>';

                foreach ($goods_delivery_detail as $value) {

                    $this->db->where('id', $value['commodity_code']);
                    $item = $this->db->get(get_db_prefix().'items')->row();

                    $item_description = '';
                    if(isset($item) && isset($item->commodity_code) && isset($item->description)){
                       $item_description = $item->commodity_code.' - '.$item->description;
                    }

                    $item_id = 0;
                    if(isset($item->id)){
                        $item_id = $item->id;
                    }

                    if($item_id == 0){
                        continue;
                    }

                    $list_item[] = $item_id;

                    $this->db->where('rel_id', $id);
                    $this->db->where('rel_type', $type);
                    $this->db->where('item', $item_id);
                    $account_history = $this->db->get(get_db_prefix(). 'acc_account_history')->result_array();
                    
                    foreach ($account_history as $key => $val) {
                        if($val['debit'] > 0){
                            $debit = $val['account'];
                        }

                        if($val['credit'] > 0){
                            $credit =  $val['account'];
                        }
                    }

                    if($account_history){
                        $html .= '
                        <div class="div_content">
                        <h5>'.$item_description.'</h5>
                        <div class="row">
                                '.form_hidden('item_amount['.$item_id.']', ($value['quantities'] * $value['unit_price'])).'
                              <div class="col-md-6"> '.
                                render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$credit,array(),array(),'','',false) .'
                              </div>
                              <div class="col-md-6">
                                '. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$debit,array(),array(),'','',false).'
                              </div>
                          </div>
                        </div>';
                    }else{
                        $item_automatic = $this->Accounting_model->get_item_automatic($item_id);

                        if($item_automatic){
                            $html .= '
                        <div class="div_content">
                            <h5>'.$item_description.'</h5>
                            <div class="row">
                            '.form_hidden('item_amount['.$item_id.']', ($value['quantities'] * $value['unit_price'])).'
                              <div class="col-md-6"> '.
                                render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$item_automatic->inventory_asset_account,array(),array(),'','',false) .'
                              </div>
                              <div class="col-md-6">
                                '. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to,array(),array(),'','',false).'
                              </div>
                          </div>
                        </div>';
                        }else{

                            $html .= '
                        <div class="div_content">
                            <h5>'.$item_description.'</h5>
                            <div class="row">
                            '.form_hidden('item_amount['.$item_id.']', ($value['quantities'] * $value['unit_price'])).'
                              <div class="col-md-6"> '.
                                render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account,array(),array(),'','',false) .'
                              </div>
                              <div class="col-md-6">
                                '. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to,array(),array(),'','',false).'
                              </div>
                          </div>
                        </div>';
                        }
                    }
                }
            }

            $debit = 0;
            $credit = 0;
        }elseif ($type == 'stock_import') {
            $accounts = $this->Accounting_model->get_accounts();

            $this->load->model('warehouse/warehouse_model');
            $goods_receipt = $this->warehouse_model->get_goods_receipt($id);
            $goods_receipt_detail = $this->warehouse_model->get_goods_receipt_detail($id);

            $status = '';

            if($goods_receipt->approval == 1){
                $status = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">'.app_lang('approved').'</span><span class="hide">, </span></span>&nbsp';
            }elseif($goods_receipt->approval == 0){
                $status = '<span class="label label-tag tag-id-1 label-tab2"><span class="tag">'.app_lang('not_yet_approve').'</span><span class="hide">, </span></span>&nbsp';
            }elseif($goods_receipt->approval == -1){
                $status = '<span class="label label-tag tag-id-1 label-tab3"><span class="tag">'.app_lang('reject').'</span><span class="hide">, </span></span>&nbsp';
            }

            $html = '<table class="table border table-striped no-margin">
                      <tbody>
                        <tr class="project-overview">
                            <td class="bold">'. app_lang('withdrawals').'</td>
                            <td><a href="' . get_uri('warehouse/view_purchase/' . $goods_receipt->id) . '" target="_blank">' . $goods_receipt->goods_receipt_code . '</a></td>
                        </tr>
                        <tr class="project-overview">
                            <td class="bold" width="30%">'. app_lang('accounting_date').'</td>
                            <td>'. format_to_date($goods_receipt->date_c)  .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('status').'</td>
                            <td>'. $status .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('total_tax_money').'</td>
                            <td>'. to_currency($goods_receipt->total_tax_money, $currency->name) .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('total_goods_money').'</td>
                            <td>'. to_currency($goods_receipt->total_goods_money, $currency->name) .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('value_of_inventory').'</td>
                            <td>'. to_currency($goods_receipt->value_of_inventory, $currency->name) .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('total_money').'</td>
                            <td>'. to_currency($goods_receipt->total_money, $currency->name) .'</td>
                         </tr>
                        </tbody>
                  </table>';

            if($goods_receipt_detail){
                $payment_account = get_setting('acc_wh_stock_import_payment_account');
                $deposit_to = get_setting('acc_wh_stock_import_deposit_to');

                $html .= '<h4>'.app_lang('list_of_items').'</h4>';

                foreach ($goods_receipt_detail as $value) {

                    $this->db->where('id', $value['commodity_code']);
                    $item = $this->db->get(get_db_prefix().'items')->row();

                    $item_description = '';
                    if(isset($item) && isset($item->commodity_code) && isset($item->description)){
                       $item_description = $item->commodity_code.' - '.$item->description;
                    }

                    $item_id = 0;
                    if(isset($item->id)){
                        $item_id = $item->id;
                    }

                    if($item_id == 0){
                        continue;
                    }

                    $list_item[] = $item_id;

                    $this->db->where('rel_id', $id);
                    $this->db->where('rel_type', $type);
                    $this->db->where('item', $item_id);
                    $account_history = $this->db->get(get_db_prefix(). 'acc_account_history')->result_array();
                    
                    foreach ($account_history as $key => $val) {
                        if($val['debit'] > 0){
                            $debit = $val['account'];
                        }

                        if($val['credit'] > 0){
                            $credit =  $val['account'];
                        }
                    }

                    if($account_history){
                        $html .= '
                        <div class="div_content">
                        <h5>'.$item_description.'</h5>
                        <div class="row">
                                '.form_hidden('item_amount['.$item_id.']', $value['goods_money']).'
                              <div class="col-md-6"> '.
                                render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$credit,array(),array(),'','',false) .'
                              </div>
                              <div class="col-md-6">
                                '. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$debit,array(),array(),'','',false).'
                              </div>
                          </div>
                        </div>';
                    }else{
                        $item_automatic = $this->Accounting_model->get_item_automatic($item_id);

                        if($item_automatic){
                            $html .= '
                        <div class="div_content">
                            <h5>'.$item_description.'</h5>
                            <div class="row">
                            '.form_hidden('item_amount['.$item_id.']', $value['goods_money']).'
                              <div class="col-md-6"> '.
                                render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account,array(),array(),'','',false) .'
                              </div>
                              <div class="col-md-6">
                                '. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$item_automatic->inventory_asset_account,array(),array(),'','',false).'
                              </div>
                          </div>
                        </div>';
                        }else{

                            $html .= '
                        <div class="div_content">
                            <h5>'.$item_description.'</h5>
                            <div class="row">
                            '.form_hidden('item_amount['.$item_id.']', $value['goods_money']).'
                              <div class="col-md-6"> '.
                                render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account,array(),array(),'','',false) .'
                              </div>
                              <div class="col-md-6">
                                '. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to,array(),array(),'','',false).'
                              </div>
                          </div>
                        </div>';
                        }
                    }
                }
            }

            $debit = 0;
            $credit = 0;
        }elseif ($type == 'loss_adjustment') {
            $accounts = $this->Accounting_model->get_accounts();

            $this->load->model('warehouse/warehouse_model');

            $loss_adjustment = $this->warehouse_model->get_loss_adjustment($id);
            $loss_adjustment_detail = $this->warehouse_model->get_loss_adjustment_detailt_by_masterid($id);

            $banking = $this->Accounting_model->get_transaction_banking($id);

            $status = '';

            if ((int) $loss_adjustment->status == 0) {
                $status = '<div class="btn btn-warning" >' . app_lang('draft') . '</div>';
            } elseif ((int) $loss_adjustment->status == 1) {
                $status = '<div class="btn btn-success" >' . app_lang('Adjusted') . '</div>';
            } elseif((int) $loss_adjustment->status == -1){

                $status = '<div class="btn btn-danger" >' . app_lang('reject') . '</div>';
            }

            $html = '<table class="table border table-striped no-margin">
                      <tbody>
                        <tr class="project-overview">
                            <td class="bold">'. app_lang('type').'</td>
                            <td><a href="' . get_uri('warehouse/view_lost_adjustment/' . $loss_adjustment->id) . '" target="_blank">' . app_lang($loss_adjustment->type) . '</a></td>
                        </tr>
                        <tr class="project-overview">
                            <td class="bold" width="30%">'. app_lang('_time').'</td>
                            <td>'. format_to_date($loss_adjustment->time)  .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('status').'</td>
                            <td>'. $status .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('reason').'</td>
                            <td>'. html_entity_decode($loss_adjustment->reason) .'</td>
                         </tr>
                        </tbody>
                  </table>';

            if($loss_adjustment_detail){
                $decrease_payment_account = get_setting('acc_wh_decrease_payment_account');
                $decrease_deposit_to = get_setting('acc_wh_decrease_deposit_to');

                $increase_payment_account = get_setting('acc_wh_increase_payment_account');
                $increase_deposit_to = get_setting('acc_wh_increase_deposit_to');


                $html .= '<h4>'.app_lang('list_of_items').'</h4>';

                foreach ($loss_adjustment_detail as $value) {
                    if($value['current_number'] < $value['updates_number']){
                        $number = $value['updates_number'] - $value['current_number'];
                        $payment_account = $increase_payment_account;
                        $deposit_to = $increase_deposit_to;
                    }else{
                        $number = $value['current_number'] - $value['updates_number'];
                        $payment_account = $decrease_payment_account;
                        $deposit_to = $decrease_deposit_to;
                    }

                    $this->db->where('id', $value['items']);
                    $item = $this->db->get(get_db_prefix().'items')->row();

                    $item_description = '';
                    if(isset($item) && isset($item->commodity_code) && isset($item->description)){
                       $item_description = $item->commodity_code.' - '.$item->description;
                    }

                    $item_id = 0;
                    if(isset($item->id)){
                        $item_id = $item->id;
                    }

                    if($item_id == 0){
                        continue;
                    }
                    $list_item[] = $item_id;

                    $this->db->where('rel_id', $id);
                    $this->db->where('rel_type', $type);
                    $this->db->where('item', $item_id);
                    $account_history = $this->db->get(get_db_prefix(). 'acc_account_history')->result_array();
                    
                    foreach ($account_history as $key => $val) {
                        if($val['debit'] > 0){
                            $debit = $val['account'];
                        }

                        if($val['credit'] > 0){
                            $credit =  $val['account'];
                        }
                    }

                    $price = 0;
                    if($value['lot_number'] != ''){
                        $this->db->where('lot_number', $value['lot_number']);
                        $this->db->where('expiry_date', $value['expiry_date']);
                        $receipt_detail = $this->db->get(get_db_prefix().'goods_receipt_detail')->row();
                        if($receipt_detail){
                            $price = $receipt_detail->unit_price;
                        }else{
                            $this->db->where('id' ,$item_id);
                            $item = $this->db->get(get_db_prefix().'items')->row();
                            if($item){
                                $price = $item->purchase_price;
                            }
                        }
                    }else{
                        $this->db->where('id' ,$item_id);
                        $item = $this->db->get(get_db_prefix().'items')->row();
                        if($item){
                            $price = $item->purchase_price;
                        }
                    }

                    if($account_history){
                        $html .= '
                        <div class="div_content">
                        <h5>'.$item_description.'</h5>
                        <div class="row">
                                '.form_hidden('item_amount['.$item_id.']', $number * $price).'
                              <div class="col-md-6"> '.
                                render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$credit,array(),array(),'','',false) .'
                              </div>
                              <div class="col-md-6">
                                '. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$debit,array(),array(),'','',false).'
                              </div>
                          </div>
                        </div>';
                    }else{
                        $item_automatic = $this->Accounting_model->get_item_automatic($item_id);

                        if($item_automatic){
                            $html .= '
                        <div class="div_content">
                            <h5>'.$item_description.'</h5>
                            <div class="row">
                            '.form_hidden('item_amount['.$item_id.']', $number * $price).'
                              <div class="col-md-6"> '.
                                render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account,array(),array(),'','',false) .'
                              </div>
                              <div class="col-md-6">
                                '. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$item_automatic->inventory_asset_account,array(),array(),'','',false).'
                              </div>
                          </div>
                        </div>';
                        }else{

                            $html .= '
                        <div class="div_content">
                            <h5>'.$item_description.'</h5>
                            <div class="row">
                            '.form_hidden('item_amount['.$item_id.']', $number * $price).'
                              <div class="col-md-6"> '.
                                render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account,array(),array(),'','',false) .'
                              </div>
                              <div class="col-md-6">
                                '. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to,array(),array(),'','',false).'
                              </div>
                          </div>
                        </div>';
                        }
                    }
                }
            }

            $debit = 0;
            $credit = 0;
        }elseif ($type == 'opening_stock') {

            $accounts = $this->Accounting_model->get_accounts();
            $opening_stock = $this->Accounting_model->get_opening_stock_data($id);
            $deposit_to = get_setting('acc_wh_opening_stock_deposit_to');
            $payment_account = get_setting('acc_wh_opening_stock_payment_account');
            $acc_first_month_of_financial_year = get_setting('acc_first_month_of_financial_year');

            $date_financial_year = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.date('Y')));

            $html = '<table class="table border table-striped no-margin">
                      <tbody>
                        <tr class="project-overview">
                            <td class="bold">'. app_lang('commodity_code').'</td>
                            <td><a href="' . get_uri('warehouse/view_commodity_detail/' . $opening_stock->id) . '" target="_blank">' . $opening_stock->commodity_code . '</a></td>
                        </tr>
                        <tr class="project-overview">
                            <td class="bold" width="30%">'. app_lang('commodity_name').'</td>
                            <td>'. $opening_stock->description .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('sku_code').'</td>
                            <td>'. $opening_stock->sku_code .'</td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('opening_stock').'</td>
                            <td>'. to_currency($opening_stock->opening_stock, $currency->name) .'</td>
                         </tr>
                        </tbody>
                  </table><br>';

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', $type);
            $this->db->where('date >= "'.$date_financial_year.'"');
            $account_history = $this->db->get(get_db_prefix(). 'acc_account_history')->result_array();

            foreach ($account_history as $key => $value) {
                if($value['debit'] > 0){
                    $deposit_to = $value['account'];
                }

                if($value['credit'] > 0){
                    $payment_account =  $value['account'];
                }
            }

            $html .= '
                    <div class="row">
                      <div class="col-md-6"> '.
                        render_select('payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account,array(),array(),'','',false) .'
                      </div>
                      <div class="col-md-6">
                        '. render_select('deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to,array(),array(),'','',false).'
                      </div>
                </div>';

            $debit = 0;
            $credit = 0;
        }elseif($type == 'purchase_payment'){
            $this->load->model('purchase/purchase_model');
            $payment = $this->purchase_model->get_payment_pur_invoice($id);

            $invoice = $this->purchase_model->get_pur_invoice($payment->pur_invoice);

            $html = '<table class="table border table-striped no-margin">
                      <tbody>
                         <tr class="project-overview">
                            <td class="bold" width="30%">'. app_lang('purchase_order').'</td>
                            <td>'.'<a href="'.get_uri('purchase/purchase_order/'.$invoice->pur_order).'">'.get_pur_order_subject($invoice->pur_order).'</a>' .'</td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('acc_amount').'</td>
                            <td>'. to_currency($payment->amount, $currency->name) .'</td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('expense_dt_table_heading_date').'</td>
                            <td>'. format_to_date($payment->date) .'</td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('payment_modes').'</td>
                            <td>'. get_payment_mode_name_by_id($payment->paymentmode) .'</td>
                            <td></td>
                         </tr>
                         <tr class="project-overview">
                            <td class="bold">'. app_lang('note').'</td>
                            <td colspan="2">'. html_entity_decode($payment->note) .'</td>
                         </tr>';
            $amount = 1;
            
            
            $html .=   '</tbody>
                  </table>';
           
            $debit = get_setting('acc_pur_payment_deposit_to');
            $credit = get_setting('acc_pur_payment_payment_account');
        }
        $db = db_connect('default');
        $db_builder = $db->table(get_db_prefix().'acc_account_history');

        $db_builder->where('rel_id', $id);
        $db_builder->where('rel_type', $type);
        $db_builder->where('(tax = 0 or tax is null)');
        $account_history = $db_builder->get()->getResultArray();
        foreach ($account_history as $key => $value) {
            if($value['debit'] > 0){
                $debit = $value['account'];
            }

            if($value['credit'] > 0){
                $credit =  $value['account'];
            }
        }

        $accounts = $this->Accounting_model->get_accounts();

        $accounts_dropdown = [];
        foreach ($accounts as $account) {
            $accounts_dropdown[$account['id']] = $account['name'];
        }

        return $this->template->view('Accounting\Views\transaction\convert_form', ['html' => $html, 'id' => $id, 'type' => $type, 'amount' => $amount, 'debit' => $debit, 'credit' => $credit, 'list_item' => $list_item, 'accounts_dropdown' => $accounts_dropdown]);
    }

    /**
     * convert
     * @return json 
     */
    public function convert(){
        if (!has_permission('accounting_transaction', '', 'create')) {
            access_denied('accounting');
        }
        $data = $this->request->getPost();
        $success = $this->Accounting_model->add_account_history($data);

        if ($success === 'close_the_book') {
            $message = app_lang('has_closed_the_book');
            $this->session->setFlashdata("error_message", $message);
        }elseif($success){
            $message = app_lang('successfully_converted');
            $this->session->setFlashdata("success_message", $message);
        }else {
            $message = app_lang('conversion_failed');
            $this->session->setFlashdata("error_message", $message);
        }

        switch ($data['type']) {
            case 'payment':
                $url = 'accounting/transaction?group=sales';
                break;
            case 'invoice':
                $url = 'accounting/transaction?group=sales';
                break;
            case 'expense':
                $url = 'accounting/transaction?group=expenses';
                break;
            default:
                // code...
                break;
        }

        app_redirect($url);
    }

    /**
     * transfer
     * @return view
     */
    public function transfer(){
        if (!has_permission('accounting_transfer', '', 'view')) {
            access_denied('accounting');
        }

        //prepare groups dropdown list

        $data['title']         = app_lang('transfer');
        $data['accounts'] = $this->Accounting_model->get_accounts('', 'find_in_set(account_type_id, "2,3,4,5,7,8,9,10")');


        $accounts_from_dropdown = [];
        $accounts_to_dropdown = [];
        $accounts_from_dropdown[] = ['id' => '', 'text' => '- '.app_lang('transfer_funds_from').' -'];
        $accounts_to_dropdown[] = ['id' => '', 'text' => '- '.app_lang('transfer_funds_to').' -'];
        foreach ($data['accounts'] as $account) {
            $accounts_from_dropdown[] = ['id' => $account['id'], 'text' => $account['name']];
            $accounts_to_dropdown[] = ['id' => $account['id'], 'text' => $account['name']];
        }

        $data['accounts_from_dropdown'] = $accounts_from_dropdown;
        $data['accounts_to_dropdown'] = $accounts_to_dropdown;

        return $this->template->rander('Accounting\Views\transfer/manage', $data);
    }

    /**
     * accounts table
     * @return json
     */
    public function transfer_table()
    {
            $accounts = $this->Accounting_model->get_accounts();
            $account_name = [];
            $currency_symbol = get_setting("currency_symbol");

            foreach ($accounts as $key => $value) {
                $account_name[$value['id']] = $value['name'];
            }

            $select = [
                'id',
                'transfer_funds_from',
                'transfer_funds_to',
                'transfer_amount',
                '1',
            ];

            $where = [];

            $ft_transfer_funds_from = $this->request->getPost('ft_transfer_funds_from') ? $this->request->getPost('ft_transfer_funds_from') : "";

            if ($ft_transfer_funds_from != "") {
                array_push($where, 'AND transfer_funds_from = "' .  $ft_transfer_funds_from . '"');
            }

            $ft_transfer_funds_to = $this->request->getPost('ft_transfer_funds_to') ? $this->request->getPost('ft_transfer_funds_to') : "";

            if ($ft_transfer_funds_to != "") {
                array_push($where, 'AND transfer_funds_to = "' .  $ft_transfer_funds_to . '"');
            }

            $from_date = $this->request->getPost('from_date') ? $this->request->getPost('from_date') : "";
            $to_date = $this->request->getPost('to_date') ? $this->request->getPost('to_date') : "";
            
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (date >= "' . $from_date . '" and date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (date <= "' . $to_date . '")');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'acc_transfers';
            $join         = [];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['date']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $row[] = (isset($account_name[$aRow['transfer_funds_from']]) ? $account_name[$aRow['transfer_funds_from']] : '');
                $row[] = (isset($account_name[$aRow['transfer_funds_to']]) ? $account_name[$aRow['transfer_funds_to']] : '');
                $row[] = to_currency($aRow['transfer_amount'], $currency_symbol);

                $row[] = format_to_date($aRow['date']);

                $edit = '<li role="presentation">' . modal_anchor(get_uri("accounting/transfer_form"), "<i data-feather='edit' class='icon-16'></i> " . app_lang('edit'), array("title" => app_lang('edit'), "data-post-id" => $aRow['id'], "class" => "dropdown-item")) . '</li>';

                //show the delete menu if user has access to delete the tickets
                $delete = '<li role="presentation">' . js_anchor("<i data-feather='x' class='icon-16'></i>" . app_lang('delete'), array('title' => app_lang('delete'), "class" => "delete dropdown-item", "data-id" => $aRow['id'], "data-action-url" => get_uri("accounting/delete_transfer"), "data-action" => "delete-confirmation")) . '</li>';

                $actions = '
                            <span class="dropdown inline-block">
                                <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
                                    <i data-feather="tool" class="icon-16"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" role="menu">' . $edit . $delete . '</ul>
                            </span>';

                $row[] = $actions;

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * add transfer
     * @return json
     */
    public function add_transfer(){
        $data = $this->request->getPost();
        if($data['id'] == ''){
            if (!has_permission('accounting_transfer', '', 'create')) {
                access_denied('accounting');
            }
            $success = $this->Accounting_model->add_transfer($data);
            if ($success === 'close_the_book') {
                $message = app_lang('has_closed_the_book');
            }elseif($success){
                $message = app_lang('successfully_transferred');
            }else {
                $message = app_lang('transfer_failed');
            }
        }else{
            if (!has_permission('accounting_transfer', '', 'edit')) {
                access_denied('accounting');
            }
            $id = $data['id'];
            unset($data['id']);
            $success = $this->Accounting_model->update_transfer($data, $id);
            if ($success === 'close_the_book') {
                $message = app_lang('has_closed_the_book');
            }elseif ($success) {
                $message = sprintf(app_lang('updated_successfully'), app_lang('transfer'));
            }
        }
        echo json_encode(['success' => $success, 'message' => $message]);
        die();
    }

    /**
     * journal entry
     * @return view
     */
    public function journal_entry(){
        if (!has_permission('accounting_journal_entry', '', 'view')) {
            access_denied('accounting');
        }
        $data['title']         = app_lang('journal_entry');
        $data['accounts'] = $this->Accounting_model->get_accounts();
        $data['accounts_to_select'] = $this->Accounting_model->get_data_account_to_select();

        return $this->template->rander('Accounting\Views\journal_entry/manage', $data);
    }

    /**
     * journal entry table
     * @return json
     */
    public function journal_entry_table(){
           
            $select = [
                'id',
                'number',
                'journal_date',
                '1', 
            ];

            $currency_symbol = get_setting("currency_symbol");

            $where = [];
            $from_date = $this->request->getPost('from_date') ? $this->request->getPost('from_date') : "";
            $to_date = $this->request->getPost('to_date') ? $this->request->getPost('to_date') : "";
            
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (journal_date >= "' . $from_date . '" and journal_date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (journal_date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (journal_date <= "' . $to_date . '")');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'acc_journal_entries';
            $join         = [];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['amount', 'description']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $row[] = format_to_date($aRow['journal_date']);

                if(strlen($aRow['number'].' - '.html_entity_decode($aRow['description'])) > 150){
                    $row[] = '<div data-toggle="tooltip" data-title="'. $aRow['number'].' - '.html_entity_decode(strip_tags($aRow['description'])).'">'.substr($aRow['number'].' - '.html_entity_decode($aRow['description']), 0, 150).'...</div>';
                }else{
                    $row[] = $aRow['number'].' - '.html_entity_decode($aRow['description']);
                }
                $row[] = to_currency($aRow['amount'], $currency_symbol);

                $export_to_excel = '<li role="presentation"><a href="' . get_uri('accounting/journal_entry_export/' . $aRow['id']) . '" class="dropdown-item"><i data-feather="file-text" class="icon-16"></i> ' . app_lang('acc_export_excel') . '</a></li>';


                $edit = '<li role="presentation"><a href="' . get_uri('accounting/new_journal_entry/' . $aRow['id']) . '" class="dropdown-item"><i data-feather="edit" class="icon-16"></i> ' . app_lang('edit') . '</a></li>';

                //show the delete menu if user has access to delete the tickets
                $delete = '<li role="presentation">' . js_anchor("<i data-feather='x' class='icon-16'></i>" . app_lang('delete'), array('title' => app_lang('delete'), "class" => "delete dropdown-item", "data-id" => $aRow['id'], "data-action-url" => get_uri("accounting/delete_journal_entry"), "data-action" => "delete-confirmation")) . '</li>';

                $actions = '
                            <span class="dropdown inline-block">
                                <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
                                    <i data-feather="tool" class="icon-16"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" role="menu">' . $export_to_excel . $edit . $delete . '</ul>
                            </span>';


                $row[] = $actions;

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * add journal entry
     * @return view
     */
    public function new_journal_entry($id = ''){
        if ($this->request->getPost()) {
            $data                = $this->request->getPost();
            if($id == ''){
                if (!has_permission('accounting_journal_entry', '', 'create')) {
                    access_denied('accounting_journal_entry');
                }

                $success = $this->Accounting_model->add_journal_entry($data);
                if ($success === 'close_the_book') {
                    $this->session->setFlashdata("error_message", app_lang('has_closed_the_book'));
                }elseif ($success) {
                    $this->session->setFlashdata("success_message", sprintf(app_lang('added_successfully'), app_lang('journal_entry')));
                }
            }else{
                if (!has_permission('accounting_journal_entry', '', 'edit')) {
                    access_denied('accounting_journal_entry');
                }
                $success = $this->Accounting_model->update_journal_entry($data, $id);
                if ($success === 'close_the_book') {
                    $this->session->setFlashdata("error_message", app_lang('has_closed_the_book'));
                }elseif ($success) {
                    $this->session->setFlashdata("success_message", sprintf(app_lang('updated_successfully'), app_lang('journal_entry')));
                }
            }

            app_redirect('accounting/journal_entry');
        }

        if($id != ''){
            $data['journal_entry'] = $this->Accounting_model->get_journal_entry($id);
        }
        
        $data['next_number'] = $this->Accounting_model->get_journal_entry_next_number();
        $data['title'] = app_lang('journal_entry');
        $data['account_to_select'] = $this->Accounting_model->get_data_account_to_select();
        $data['currency_symbol'] = get_setting("currency_symbol");
        
        return $this->template->rander('Accounting\Views\journal_entry/journal_entry', $data);
    }

    /**
     * delete journal entry
     * @param  integer $id
     * @return
     */
    public function delete_journal_entry()
    {
        if (!has_permission('accounting_journal_entry', '', 'delete')) {
            access_denied('accounting_journal_entry');
        }

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');

        $success = $this->Accounting_model->delete_journal_entry($id);

        if ($success) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    /**
     * report manage
     * @return view
     */
    public function report(){
        if (!has_permission('accounting_report', '', 'view')) {
            access_denied('accounting_report');
        }
        $data['title'] = app_lang('accounting_report');

        return $this->template->rander('Accounting\Views\report/manage', $data);
    }

    /**
     * report balance sheet
     * @return view
     */
    public function rp_balance_sheet(){
        $data['title'] = app_lang('balance_sheet');
        $data['from_date'] = date('Y-m-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/balance_sheet', $data);
    }

    /**
     * report balance sheet comparison
     * @return view
     */
    public function rp_balance_sheet_comparison(){
        $data['title'] = app_lang('balance_sheet_comparison');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');

        return $this->template->rander('Accounting\Views\report/includes/balance_sheet_comparison', $data);
        
    }

    /**
     * report balance sheet detail
     * @return view
     */
    public function rp_balance_sheet_detail(){
        $data['title'] = app_lang('balance_sheet_detail');
        $data['from_date'] = date('Y-m-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/balance_sheet_detail', $data);
    }

    /**
     * report balance sheet summary
     * @return view 
     */
    public function rp_balance_sheet_summary(){
        $data['title'] = app_lang('balance_sheet_summary');
        $data['from_date'] = date('Y-m-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/balance_sheet_summary', $data);
    }

    /**
     * report business snapshot
     * @return view
     */
    public function rp_business_snapshot(){
        $this->load->model('currencies_model');
        $data['title'] = app_lang('business_snapshot');
        $data['currency'] = $this->currencies_model->get_base_currency();
        $data['data_report'] = $this->Accounting_model->get_data_balance_sheet_summary([]);
        $this->load->view('report/includes/balance_sheet_summary', $data);
    }

    /**
     * custom summary report
     * @return view
     */
    public function rp_custom_summary_report(){
        $data['title'] = app_lang('custom_summary_report');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_display_rows_by'] = '';
        $data['accounting_display_columns_by'] = '';
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/custom_summary_report', $data);
    }

    /**
     * report profit and loss as of total income
     * @return view
     */
    public function rp_profit_and_loss_as_of_total_income(){
        $data['title'] = app_lang('profit_and_loss_as_of_total_income');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/profit_and_loss_as_of_total_income', $data);
    }

    /**
     * report profit and loss comparison
     * @return view
     */
    public function rp_profit_and_loss_comparison(){
        $data['title'] = app_lang('profit_and_loss_comparison');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/profit_and_loss_comparison', $data);
    }

    /**
     * report profit and loss detail
     * @return view
     */
    public function rp_profit_and_loss_detail(){
        $data['title'] = app_lang('profit_and_loss_detail');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/profit_and_loss_detail', $data);
    }

    /**
     * report profit and loss year to date comparison
     * @return view
     */
    public function rp_profit_and_loss_year_to_date_comparison(){
        $data['title'] = app_lang('profit_and_loss_year_to_date_comparison');
        $data['from_date'] = date('Y-m-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/profit_and_loss_year_to_date_comparison', $data);
    }

    /**
     * report profit and loss
     * @return view
     */
    public function rp_profit_and_loss(){
        $data['title'] = app_lang('profit_and_loss');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/profit_and_loss', $data);
    }

    /**
     * report statement of cash flows
     * @return view
     */
    public function rp_statement_of_cash_flows(){
        $data['title'] = app_lang('statement_of_cash_flows');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        return $this->template->rander('Accounting\Views\report/includes/statement_of_cash_flows', $data);
    }

    /**
     * report statement of changes in equity description
     * @return view
     */
    public function rp_statement_of_changes_in_equity(){
        $data['title'] = app_lang('statement_of_changes_in_equity');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/statement_of_changes_in_equity', $data);
    }

    /**
     * report deposit detail
     * @return view
     */
    public function rp_deposit_detail(){
        $data['title'] = app_lang('deposit_detail');
        $data['from_date'] = date('Y-m-01');
        $data['to_date'] = date('Y-m-d');
        return $this->template->rander('Accounting\Views\report/includes/deposit_detail', $data);
    }

    /**
     * report income by customer summary
     * @return view
     */
    public function rp_income_by_customer_summary(){
        $data['title'] = app_lang('income_by_customer_summary');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/income_by_customer_summary', $data);
    }
    
    /**
     * report check detail
     * @return view
     */
    public function rp_check_detail(){
        $data['title'] = app_lang('cheque_detail');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        return $this->template->rander('Accounting\Views\report/includes/check_detail', $data);
    }

    /**
     * report account list
     * @return view
     */
    public function rp_account_list(){
        $data['title'] = app_lang('account_list');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        return $this->template->rander('Accounting\Views\report/includes/account_list', $data);
    }

    /**
     * report account history
     * @return view
     */
    public function rp_account_history(){
        $data['title'] = app_lang('account_history');
        $data['account'] = $this->request->getGet('account');
        $data['from_date'] = date('Y-m-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounts'] = $this->Accounting_model->get_accounts('', 'find_in_set(account_type_id, "2,3,4,5,7,8,9,10")');

        $accounts_dropdown = [];
        foreach ($data['accounts'] as $account) {
            $accounts_dropdown[$account['id']] = $account['name'];
        }

        $data['accounts_dropdown'] = $accounts_dropdown;

        return $this->template->rander('Accounting\Views\report/includes/account_history', $data);
    }
    
    /**
     * report general ledger
     * @return view
     */
    public function rp_general_ledger(){
        $data['title'] = app_lang('general_ledger');
        $data['from_date'] = date('Y-m-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/general_ledger', $data);
    }

    /**
     * report journal
     * @return view
     */
    public function rp_journal(){
        $data['title'] = app_lang('journal');
        $data['from_date'] = date('Y-m-01');
        $data['to_date'] = date('Y-m-d');
        return $this->template->rander('Accounting\Views\report/includes/journal', $data);
    }

    /**
     * report recent transactions
     * @return view
     */
    public function rp_recent_transactions(){
        $data['title'] = app_lang('recent_transactions');
        $data['from_date'] = date('Y-m-01');
        $data['to_date'] = date('Y-m-d');
        return $this->template->rander('Accounting\Views\report/includes/recent_transactions', $data);
    }

    /**
     * report transaction detail by account
     * @return view
     */
    public function rp_transaction_detail_by_account(){
        $data['title'] = app_lang('transaction_detail_by_account');
        $data['from_date'] = date('Y-m-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/transaction_detail_by_account', $data);
    }

    /**
     * report transaction list by date
     * @return view
     */
    public function rp_transaction_list_by_date(){
        $data['title'] = app_lang('transaction_list_by_date');
        $data['from_date'] = date('Y-m-01');
        $data['to_date'] = date('Y-m-d');
        return $this->template->rander('Accounting\Views\report/includes/transaction_list_by_date', $data);
    }

    /**
     * report trial balance
     * @return view
     */
    public function rp_trial_balance(){
        $data['title'] = app_lang('trial_balance');
        $data['from_date'] = date('Y-m-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/trial_balance', $data);
    }

    /**
     * dashboard
     * @return view
     */
    public function dashboard(){
        if (!has_permission('accounting_dashboard', '', 'view')) {
            access_denied('accounting_dashboard');
        }
        $data['title'] = app_lang('dashboard');
        
        $data['currency'] = get_setting('default_currency');
        $data['currencys'] = get_international_currency_code_dropdown();

        return $this->template->rander('Accounting\Views\dashboard/manage', $data);
    }

    /**
     * import xlsx banking
     * @return view
     */
    public function import_xlsx_banking() {
        if (!has_permission('accounting_transaction', '', 'create')) {
            access_denied('accounting_transaction');
        }

        $this->load->model('staff_model');
        $data_staff = $this->staff_model->get($this->login_user->id);

        /*get language active*/
        if ($data_staff) {
            if ($data_staff->default_language != '') {
                $data['active_language'] = $data_staff->default_language;

            } else {

                $data['active_language'] = get_setting('active_language');
            }

        } else {
            $data['active_language'] = get_setting('active_language');
        }
        $data['title'] = app_lang('import_excel');

        $this->load->view('transaction/import_banking', $data);
    }

    /**
     * import file xlsx banking
     * @return json
     */
    public function import_file_xlsx_banking(){
        if(!class_exists('XLSXReader_fin')){
            require_once(module_dir_path(ACCOUNTING_MODULE_NAME).'assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(ACCOUNTING_MODULE_NAME).'assets/plugins/XLSXWriter/xlsxwriter.class.php');

        $filename ='';
        if($this->request->getPost()){
            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                $this->delete_error_file_day_before(1, ACCOUTING_IMPORT_ITEM_ERROR);

                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];                
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $rows          = [];
                    $arr_insert          = [];

                    $tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 0755);
                    }

                    if (!file_exists($tmpDir)) {
                        mkdir($tmpDir, 0755);
                    }

                    // Setup our new file path
                    $newFilePath = $tmpDir . $_FILES['file_csv']['name'];                    

                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        //Writer file
                        $writer_header = array(
                            app_lang('date').' (dd/mm/YYYY)'            =>'string',
                            app_lang('withdrawals')     =>'string',
                            app_lang('deposits')    =>'string',
                            app_lang('payee')      =>'string',
                            app_lang('description')     =>'string',
                            app_lang('error')       =>'string',
                        );

                        $rowstyle[] =array('widths'=>[10,20,30,40]);

                        $writer = new \XLSXWriter();
                        $writer->writeSheetHeader('Sheet1', $writer_header,  $col_options = ['widths'=>[40,40,40,40,50,50]]);

                        //Reader file
                        $xlsx = new \XLSXReader_fin($newFilePath);
                        $sheetNames = $xlsx->getSheetNames();
                        $data = $xlsx->getSheetData($sheetNames[1]);

                        $arr_header = [];

                        $arr_header['date'] = 0;
                        $arr_header['withdrawals'] = 1;
                        $arr_header['deposits'] = 2;
                        $arr_header['payee'] = 3;
                        $arr_header['description'] = 4;

                        $total_rows = 0;
                        $total_row_false    = 0; 

                        for ($row = 1; $row < count($data); $row++) {

                            $total_rows++;

                            $rd = array();
                            $flag = 0;
                            $flag2 = 0;

                            $string_error ='';
                            $flag_position_group;
                            $flag_department = null;

                            $value_date  = isset($data[$row][$arr_header['date']]) ? $data[$row][$arr_header['date']] : '' ;
                            $value_withdrawals   = isset($data[$row][$arr_header['withdrawals']]) ? $data[$row][$arr_header['withdrawals']] : '' ;
                            $value_deposits     = isset($data[$row][$arr_header['deposits']]) ? $data[$row][$arr_header['deposits']] : '' ;
                            $value_payee    = isset($data[$row][$arr_header['payee']]) ? $data[$row][$arr_header['payee']] : '' ;
                            $value_description   = isset($data[$row][$arr_header['description']]) ? $data[$row][$arr_header['description']] : '' ;
                            
                            $reg_day = '/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/'; /*yyyy-mm-dd*/

                            if(is_null($value_date) != true){
                                if(preg_match($reg_day, $value_date, $match) != 1){
                                    $string_error .=app_lang('date'). app_lang('invalid');
                                    $flag = 1; 
                                }
                            }else{
                                $string_error .= app_lang('date') . app_lang('not_yet_entered');
                                $flag = 1;
                            }

                            if (is_null($value_withdrawals) == true) {
                                $string_error .= app_lang('withdrawals') . app_lang('not_yet_entered');
                                $flag = 1;
                            }else{
                                if(!is_numeric($value_withdrawals) && $value_deposits == ''){
                                    $string_error .= app_lang('withdrawals') . app_lang('invalid');
                                    $flag = 1;
                                }
                            }

                            if (is_null($value_deposits) == true) {
                                $string_error .= app_lang('deposits') . app_lang('not_yet_entered');
                                $flag = 1;
                            }else{
                                if(!is_numeric($value_deposits) && $value_withdrawals == ''){
                                    $string_error .= app_lang('deposits') . app_lang('invalid');
                                    $flag = 1;
                                }
                            }

                            if (is_null($value_payee) == true) {
                                $string_error .= app_lang('payee') . app_lang('not_yet_entered');
                                $flag = 1;
                            }
                            

                            if(($flag == 1) || $flag2 == 1 ){
                                //write error file
                                $writer->writeSheetRow('Sheet1', [
                                    $value_date,
                                    $value_withdrawals,
                                    $value_deposits,
                                    $value_payee,
                                    $value_description,
                                    $string_error,
                                ]);

                                // $numRow++;
                                $total_row_false++;
                            }

                            if($flag == 0 && $flag2 == 0){

                                $rd['date']       = $value_date;
                                $rd['withdrawals']         = $value_withdrawals;
                                $rd['deposits']        = $value_deposits;
                                $rd['payee']       = $value_payee;
                                $rd['description']               = $value_description;
                                $rd['datecreated']               = date('Y-m-d H:i:s');
                                $rd['addedfrom']               = $this->login_user->id;

                                $rows[] = $rd;
                                array_push($arr_insert, $rd);

                            }

                        }

                        //insert batch
                        if(count($arr_insert) > 0){
                            $this->Accounting_model->insert_batch_banking($arr_insert);
                        }

                        $total_rows = $total_rows;
                        $total_row_success = isset($rows) ? count($rows) : 0;
                        $dataerror = '';
                        $message ='Not enought rows for importing';

                        if($total_row_false != 0){
                            $filename = 'Import_banking_error_'.$this->login_user->id.'_'.strtotime(date('Y-m-d H:i:s')).'.xlsx';
                            $writer->writeToFile(str_replace($filename, ACCOUTING_IMPORT_ITEM_ERROR.$filename, $filename));
                        }


                    }
                }
            }
        }


        if (file_exists($newFilePath)) {
            @unlink($newFilePath);
        }

        echo json_encode([
            'message'           => $message,
            'total_row_success' => $total_row_success,
            'total_row_false'   => $total_row_false,
            'total_rows'        => $total_rows,
            'site_url'          => site_url(),
            'staff_id'          => $this->login_user->id,
            'filename'          => ACCOUTING_IMPORT_ITEM_ERROR.$filename,
        ]);
    }
    /**
     * get data transfer
     * @param  integer $id 
     * @return json     
     */
    public function get_data_transfer($id){
        $transfer = $this->Accounting_model->get_transfer($id);
        $transfer->date = format_to_date($transfer->date);
        echo json_encode($transfer);
    }

    /**
     * delete transfer
     * @param  integer $id
     * @return
     */
    public function delete_transfer()
    {
        if (!has_permission('accounting_transfer', '', 'delete')) {
            access_denied('accounting_transfer');
        }

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');

        $success = $this->Accounting_model->delete_transfer($id);

        if ($success) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    /**
     * get data account
     * @param  integer $id 
     * @return json     
     */
    public function get_data_account($id){
        $account = $this->Accounting_model->get_accounts($id);
        $account->balance_as_of = format_to_date($account->balance_as_of);
        $account->name = $account->name != '' ? $account->name : app_lang($account->key_name);

        if($account->balance == 0){
            if($account->account_type_id > 10 || $account->account_type_id == 1 || $account->account_type_id == 6){
                $account->balance = 1;
            }else{
                $db = db_connect('default');
                $db_builder = $db->table(get_db_prefix().'acc_account_history');
                $db_builder->where('account', $id);
                $count = $db_builder->countAllResults();
                if($count > 0){
                    $account->balance = 1;
                }
            }
        }

        echo json_encode($account);
    }
    
    /**
     * delete account
     * @param  integer $id
     * @return
     */
    public function delete_account($id)
    {
        if (!has_permission('accounting_chart_of_accounts', '', 'delete')) {
            access_denied('accounting_chart_of_accounts');
        }
        $success = $this->Accounting_model->delete_account($id);
        $message = '';
        
        if ($success === 'have_transaction') {
            $message = app_lang('cannot_delete_transaction_already_exists');
            $this->session->setFlashdata("error_message", $message);
        }elseif ($success) {
            $message = sprintf(app_lang('deleted'), app_lang('acc_account'));
            $this->session->setFlashdata("success_message", $message);
        } else {
            $message = app_lang('can_not_delete');
            $this->session->setFlashdata("error_message", $message);
        }
        app_redirect('accounting/chart_of_accounts');
    }

    /**
     * add rule
     * @return view
     */
    public function new_rule($id = ''){
        if (!has_permission('accounting_rule', '', 'create') && !is_admin() ) {
            access_denied('accounting_rule');
        }

        if ($this->request->getPost()) {
            $data                = $this->request->getPost();
            if($id == ''){
                $success = $this->Accounting_model->add_rule($data);
                if ($success) {
                    $message = sprintf(app_lang('added_successfully'), app_lang('banking_rule'));
                    $this->session->setFlashdata("success_message", $message);
                }
            }else{
                $success = $this->Accounting_model->update_rule($data, $id);
                if ($success) {
                    $message = sprintf(app_lang('updated_successfully'), app_lang('banking_rule'));
                    $this->session->setFlashdata("success_message", $message);
                }
            }
            app_redirect('accounting/setting?group=banking_rules');
        }

        if($id != ''){
            $data['rule'] = $this->Accounting_model->get_rule($id);
        }
        $data['accounts'] = $this->Accounting_model->get_accounts();
        $data['title'] = app_lang('banking_rule');
        $data['account_to_select'] = $this->Accounting_model->get_data_account_to_select();

        return $this->template->rander('Accounting\Views\setting/rule', $data);
    }

    /**
     * delete convert
     * @param  integer $id
     * @return json
     */
    public function delete_convert($type)
    {
        if (!has_permission('accounting_transaction', '', 'delete')) {
            access_denied('accounting_transaction');
        }

        $id = $this->request->getPost('id');

        $success = $this->Accounting_model->delete_convert($id,$type);

        $message = sprintf(app_lang('problem_deleting'), app_lang('acc_convert'));

        if ($success) {
            $message = sprintf(app_lang('deleted'), app_lang('acc_convert'));
            $this->session->setFlashdata("success_message", $message);
        }else{
            $this->session->setFlashdata("error_message", $message);
        }

        echo json_encode(array("success" => true, 'message' => $message));
        die;
    }

    /**
     * reconcile
     * @return view or redirect
     */
    public function reconcile(){
        if (!has_permission('accounting_reconcile', '', 'view')) {
            access_denied('accounting_reconcile');
        }
        if ($this->request->getPost()) {
            if (!has_permission('accounting_reconcile', '', 'create')) {
                access_denied('accounting_reconcile');
            }
            $data                = $this->request->getPost();
            if($data['resume'] == 0){
                unset($data['resume']);
                $success = $this->Accounting_model->add_reconcile($data);
            }
            app_redirect('accounting/reconcile_account/'.$data['account']);

        }

        $data['title']         = app_lang('reconcile');
        $data['accounts'] = $this->Accounting_model->get_accounts('', 'find_in_set(account_type_id, "2,3,4,5,7,8,9,10,20,21,22,23,24,25")');
        $data['beginning_balance'] = 0;
        $data['resume'] = 0;

        $accounts_dropdown = [];
        foreach ($data['accounts'] as $account) {
            $accounts_dropdown[$account['id']] = $account['name'];
        }

        $data['accounts_dropdown'] = $accounts_dropdown;

        $closing_date = false;
        $reconcile = $this->Accounting_model->get_reconcile_by_account($data['accounts'][0]['id']);
        if($reconcile){
            if(get_setting('acc_close_the_books') == 1){
                if(strtotime($reconcile->ending_date) <= strtotime(get_setting('acc_closing_date')) && strtotime(date('Y-m-d')) > strtotime(get_setting('acc_closing_date'))){
                    $closing_date = true;
                }
            }
            $data['beginning_balance'] = $reconcile->ending_balance;
            if($reconcile->finish == 0){
                $data['resume'] = 1;
            }
        }
        $data['accounts_to_select'] = $this->Accounting_model->get_data_account_to_select();

        $hide_restored=' hide';

        $check_reconcile_restored = $this->Accounting_model->check_reconcile_restored($data['accounts'][0]['id']);
        if($check_reconcile_restored){
            $hide_restored='';
        }

        $data['hide_restored'] = $closing_date == false ? $hide_restored : 'hide';
        $data['currency_symbol'] = get_setting("currency_symbol");

        return $this->template->rander('Accounting\Views\reconcile/reconcile', $data);

    }

    /**
     * reconcile account
     * @param  integer $account 
     * @return view          
     */
    public function reconcile_account($account){
        if (!has_permission('accounting_reconcile', '', 'create') && !is_admin() ) {
            access_denied('accounting_reconcile');
        }
        $data['accounts'] = $this->Accounting_model->get_accounts();
        $data['account'] = $this->Accounting_model->get_accounts($account);
        $data['reconcile'] = $this->Accounting_model->get_reconcile_by_account($account);
        $data['title'] = app_lang('reconcile');
        $data['currency_symbol'] = get_setting("currency_symbol");

        return $this->template->rander('Accounting\Views\reconcile/reconcile_account', $data);
    }

    /**
     * get info reconcile
     * @param  integer $account
     * @return json
     */
    public function get_info_reconcile($account) {
        $reconcile = $this->Accounting_model->get_reconcile_by_account($account);
        $beginning_balance = 0;
        $resume_reconciling = false;
        $hide_restored = true;

        $check_reconcile_restored = $this->Accounting_model->check_reconcile_restored($account);
        if($check_reconcile_restored){
            $hide_restored = false;
        }
        $closing_date = false;

        if ($reconcile) {
            if(get_setting('acc_close_the_books') == 1){
                if(strtotime($reconcile->ending_date) <= strtotime(get_setting('acc_closing_date')) && strtotime(date('Y-m-d')) > strtotime(get_setting('acc_closing_date'))){
                    $closing_date = true;
                }
            }

            $beginning_balance = $reconcile->ending_balance;
            if ($reconcile->finish == 0) {
                $resume_reconciling = true;
            }
        }

        echo json_encode(['beginning_balance' => $beginning_balance, 'resume_reconciling' => $resume_reconciling, 'hide_restored' => $hide_restored, 'closing_date' => $closing_date]);
        die();
    }

    /**
     * reconcile history table
     * @return json
     */
    public function reconcile_history_table(){
        $currency_symbol = get_setting("currency_symbol");
            $accounts = $this->Accounting_model->get_accounts();
            $account_name = [];

            foreach ($accounts as $key => $value) {
                $account_name[$value['id']] = $value['name'];
            }

           
            $select = [
                get_db_prefix() .'acc_account_history.id as id',
                'account',
                'rel_type',
                'debit',
                'credit',
                get_db_prefix() .'acc_account_history.description as description',
                get_db_prefix() . 'acc_account_history.customer as history_customer'
            ];

            $where = [];

            if ($this->request->getPost('account') && $this->request->getPost('reconcile')) {
                $account = $this->request->getPost('account');
                array_push($where, 'AND (account = ' . $account.') and (reconcile = 0 or reconcile = '.$this->request->getPost('reconcile').') ');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'acc_account_history';
            $join         = ['LEFT JOIN ' . get_db_prefix() . 'acc_transfers ON ' . get_db_prefix() . 'acc_transfers.id = ' . get_db_prefix() . 'acc_account_history.rel_id and ' . get_db_prefix() . 'acc_account_history.rel_type = "transfer"',
            'LEFT JOIN ' . get_db_prefix() . 'acc_journal_entries ON ' . get_db_prefix() . 'acc_journal_entries.id = ' . get_db_prefix() . 'acc_account_history.rel_id and ' . get_db_prefix() . 'acc_account_history.rel_type = "journal_entry"',
            'LEFT JOIN ' . get_db_prefix() . 'invoice_payments ON ' . get_db_prefix() . 'invoice_payments.id = ' . get_db_prefix() . 'acc_account_history.rel_id and ' . get_db_prefix() . 'acc_account_history.rel_type = "payment"',
                        'LEFT JOIN ' . get_db_prefix() . 'invoices ON ' . get_db_prefix() . 'invoices.id = ' . get_db_prefix() . 'invoice_payments.invoice_id and ' . get_db_prefix() . 'acc_account_history.rel_type = "payment"',
                            'LEFT JOIN ' . get_db_prefix() . 'expenses ON ' . get_db_prefix() . 'expenses.id = ' . get_db_prefix() . 'acc_account_history.rel_id and ' . get_db_prefix() . 'acc_account_history.rel_type = "expense"'];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [ get_db_prefix() . 'expenses.client_id as expenses_customer', get_db_prefix() . 'expenses.expense_date as expenses_date', get_db_prefix() . 'invoices.client_id as payment_customer', get_db_prefix() . 'invoice_payments.payment_date as payment_date', get_db_prefix() . 'acc_journal_entries.journal_date as journal_date', get_db_prefix() . 'acc_transfers.date as transfer_date', 'date_format('.get_db_prefix() . 'acc_account_history.datecreated, \'%Y-%m-%d\') as history_date', 'reconcile','split']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $checked = '';
                if($aRow['reconcile'] != 0){
                    $checked = 'checked';
                }
                $row[] = '<div class="checkbox"><input '.$checked.' type="checkbox" id="history_checkbox_' . $aRow['id'] . '" value="' . $aRow['id'] . '" data-payment="'.$aRow['credit'] .'" data-deposit="'.$aRow['debit'] .'"><label class="label_checkbox"></label></div>';
                if($aRow['rel_type'] == 'payment'){
                    $row[] = format_to_date($aRow['payment_date']);
                }elseif ($aRow['rel_type'] == 'expense') {
                    $row[] = format_to_date($aRow['expenses_date']);
                }elseif ($aRow['rel_type'] == 'journal_entry') {
                    $row[] = format_to_date($aRow['journal_date']);
                }elseif ($aRow['rel_type'] == 'transfer') {
                    $row[] = format_to_date($aRow['transfer_date']);
                }else{
                    $row[] = format_to_date($aRow['history_date']);
                }
                $row[] = app_lang($aRow['rel_type']);
                if($aRow['split'] > 0 && isset($account_name[$aRow['split']])){
                    $row[] = $account_name[$aRow['split']];
                }else{
                    $row[] = '-Split-';
                }

                if($aRow['rel_type'] == 'payment'){
                    $row[] = get_company_name($aRow['payment_customer']);
                }elseif ($aRow['rel_type'] == 'expense') {
                    $row[] = get_company_name($aRow['expenses_customer']);
                }else{
                    $row[] = get_company_name($aRow['history_customer']);
                }

                $row[] = $aRow['description'];
                if($aRow['credit'] > 0){
                    $row[] = to_currency($aRow['credit'], $currency_symbol);
                }else{
                    $row[] = '';
                }

                if($aRow['debit'] > 0){
                    $row[] = to_currency($aRow['debit'], $currency_symbol);
                }else{
                    $row[] = '';
                }

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     *
     *  add adjustment
     *  @return view
     */
    public function adjustment()
    {
        if (!has_permission('accounting_reconcile', '', 'create')) {
            access_denied('accounting');
        }

        if ($this->request->getPost()) {
            $data = $this->request->getPost();
            $message = '';
            $success = $this->Accounting_model->add_adjustment($data);

            if ($success === 'close_the_book') {
                $message = app_lang('has_closed_the_book');
                $this->session->setFlashdata("error_message", $message);
            }elseif ($success) {
                $message = sprintf(app_lang('added_successfully'), app_lang('adjustment'));
                $this->session->setFlashdata("success_message", $message);
            }else {
                $message = app_lang('add_failure');
                $this->session->setFlashdata("error_message", $message);
            }

            app_redirect('accounting/reconcile');
        }
    }

    /**
     * reconcile account
     * @param  integer $account 
     * @return view          
     */
    public function finish_reconcile_account(){
        if (!has_permission('accounting_reconcile', '', 'create') && !is_admin() ) {
            access_denied('accounting_reconcile');
        }

        if ($this->request->getPost()) {
            $data = $this->request->getPost();
            $message = '';
            $success = $this->Accounting_model->finish_reconcile_account($data);

            if ($success) {
                $message = sprintf(app_lang('added_successfully'), app_lang('reconcile'));
                $this->session->setFlashdata("success_message", $message);
            }else {
                $message = app_lang('add_failure');
                $this->session->setFlashdata("error_message", $message);
            }
        }

        app_redirect('accounting/reconcile');
    }

    /**
     * edit reconcile
     * @return redirect 
     */
    public function edit_reconcile(){
        if (!has_permission('accounting_reconcile', '', 'edit') && !is_admin() ) {
            access_denied('accounting_reconcile');
        }

        if ($this->request->getPost()) {
            $data = $this->request->getPost();
            $id = $data['reconcile_id'];
            $account = $data['account'];
            unset($data['reconcile_id']);
            $message = '';
            $success = $this->Accounting_model->update_reconcile($data, $id);

            if ($success) {
                $message = sprintf(app_lang('updated_successfully'), app_lang('reconcile'));
                $this->session->setFlashdata("success_message", $message);
            }
        }

        app_redirect('accounting/reconcile_account/'.$account);
    }

    /**
     * banking rules table
     * @return json
     */
    public function banking_rules_table(){
            $select = [
                'id',
                'name',
            ];

            $where = [];
            $from_date = '';
            $to_date   = '';

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'acc_banking_rules';
            $join         = [];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['transaction']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $categoryOutput = $aRow['name'];

                $row[] = $categoryOutput;
                $row[] = app_lang($aRow['transaction']);

                $options = "";
                if (has_permission('accounting_setting', '', 'edit')) {
                    $options .= '<a href="' . get_uri('accounting/new_rule/' . $aRow['id']) . '"><i data-feather="edit" class="icon-16"></i></a>';
                }

                if (has_permission('accounting_setting', '', 'delete')) {
                    $options .= '<a href="' . get_uri('accounting/delete_rule/' . $aRow['id']) . '" class="delete"><i data-feather="x" class="icon-16"></i></a>';
                }

                $row[] =  $options;

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * delete rule
     * @param  integer $id
     * @return
     */
    public function delete_rule($id)
    {
        if (!has_permission('accounting_setting', '', 'delete')) {
            access_denied('accounting_setting');
        }

        $success = $this->Accounting_model->delete_rule($id);
        $message = '';
        if ($success) {
            $message = sprintf(app_lang('deleted'), app_lang('rule'));
            $this->session->setFlashdata("success_message", $message);
        } else {
            $message = app_lang('can_not_delete');
            $this->session->setFlashdata("error_message", $message);
        }
        app_redirect('accounting/setting?group=banking_rules');
    }

    /**
     * view report
     * @return view
     */
    public function view_report(){
        $data_filter = $this->request->getPost();
        
        $data['title'] = app_lang($data_filter['type']);
        $data['currency_symbol'] = get_setting("currency_symbol");

        switch ($data_filter['type']) {
            case 'balance_sheet':
                    $data['data_report'] = $this->Accounting_model->get_data_balance_sheet($data_filter);
                break;
            case 'balance_sheet_comparison':
                    $data['data_report'] = $this->Accounting_model->get_data_balance_sheet_comparison($data_filter);
                break;
            case 'balance_sheet_detail':
                    $data['data_report'] = $this->Accounting_model->get_data_balance_sheet_detail($data_filter);
                break;
            case 'balance_sheet_summary':
                    $data['data_report'] = $this->Accounting_model->get_data_balance_sheet_summary($data_filter);
                break;
            case 'custom_summary_report':
                    switch ($data_filter['display_rows_by']) {
                        case 'customers':
                            $data_filter['type'] = 'custom_summary_report_by_customer';
                            $data['data_report'] = $this->Accounting_model->get_data_custom_summary_report_by_customer($data_filter);
                            break;

                        case 'vendors':
                            $data_filter['type'] = 'custom_summary_report_by_vendors';
                            $data['data_report'] = $this->Accounting_model->get_data_custom_summary_report_by_vendors($data_filter);
                            break;

                        case 'employees':
                            $data_filter['type'] = 'custom_summary_report_by_employees';
                            $data['data_report'] = $this->Accounting_model->get_data_custom_summary_report_by_employees($data_filter);
                            break;

                        case 'product_service':
                            $data_filter['type'] = 'custom_summary_report_by_product_service';
                            $data['data_report'] = $this->Accounting_model->get_data_custom_summary_report_by_product_service($data_filter);
                            break;

                        case 'income_statement':
                            $data_filter['type'] = 'custom_summary_report_by_income_statement';
                            $data['data_report'] = $this->Accounting_model->get_data_custom_summary_report_by_income_statement($data_filter);
                            break;

                        case 'balance_sheet':
                            $data_filter['type'] = 'custom_summary_report_by_balance_sheet';
                            $data['data_report'] = $this->Accounting_model->get_data_custom_summary_report_by_balance_sheet($data_filter);
                            break;

                        case 'balance_sheet_summary':
                            $data_filter['type'] = 'custom_summary_report_by_balance_sheet_summary';
                            $data['data_report'] = $this->Accounting_model->get_data_custom_summary_report_by_balance_sheet($data_filter);
                            break;

                        default:
                            // code...
                            break;
                    }
                    
                    
                break;
            case 'profit_and_loss_as_of_total_income':
                    $data['data_report'] = $this->Accounting_model->get_data_profit_and_loss_as_of_total_income($data_filter);
                break;
            case 'profit_and_loss_comparison':
                    $data['data_report'] = $this->Accounting_model->get_data_profit_and_loss_comparison($data_filter);
                break;
            case 'profit_and_loss_detail':
                    $data['data_report'] = $this->Accounting_model->get_data_profit_and_loss_detail($data_filter);
                break;
            case 'profit_and_loss_year_to_date_comparison':
                    $data['data_report'] = $this->Accounting_model->get_data_profit_and_loss_year_to_date_comparison($data_filter);
                break;
            case 'profit_and_loss':
                    $data['data_report'] = $this->Accounting_model->get_data_profit_and_loss($data_filter);
                break;
            case 'statement_of_cash_flows':
                    $data['data_report'] = $this->Accounting_model->get_data_statement_of_cash_flows($data_filter);
                break;
            case 'statement_of_changes_in_equity':
                    $data['data_report'] = $this->Accounting_model->get_data_statement_of_changes_in_equity($data_filter);
                break;
            case 'deposit_detail':
                    $data['data_report'] = $this->Accounting_model->get_data_deposit_detail($data_filter);
                break;
            case 'income_by_customer_summary':
                    $data['data_report'] = $this->Accounting_model->get_data_income_by_customer_summary($data_filter);
                break;
            case 'check_detail':
                    $data['data_report'] = $this->Accounting_model->get_data_check_detail($data_filter);
                break;
            case 'general_ledger':
                    $data['data_report'] = $this->Accounting_model->get_data_general_ledger($data_filter);
                break;
            case 'journal':
                    $data['data_report'] = $this->Accounting_model->get_data_journal($data_filter);
                break;
            case 'recent_transactions':
                    $data['data_report'] = $this->Accounting_model->get_data_recent_transactions($data_filter);
                break;
            case 'transaction_detail_by_account':
                    $data['data_report'] = $this->Accounting_model->get_data_transaction_detail_by_account($data_filter);
                break;
            case 'transaction_list_by_date':
                    $data['data_report'] = $this->Accounting_model->get_data_transaction_list_by_date($data_filter);
                break;
            case 'trial_balance':
                    $data['data_report'] = $this->Accounting_model->get_data_trial_balance($data_filter);
                break;
            case 'account_history':
                    $data['data_report'] = $this->Accounting_model->get_data_account_history($data_filter);
                break;
            case 'tax_detail_report':
                    $data['data_report'] = $this->Accounting_model->get_data_tax_detail_report($data_filter);
                break;
            case 'tax_summary_report':
                    $data['data_report'] = $this->Accounting_model->get_data_tax_summary_report($data_filter);
                break;
            case 'tax_liability_report':
                    $data['data_report'] = $this->Accounting_model->get_data_tax_liability_report($data_filter);
                break;
            case 'account_list':
                    $data['data_report'] = $this->Accounting_model->get_data_account_list($data_filter);
                break;
            case 'accounts_receivable_ageing_detail':
                    $data['data_report'] = $this->Accounting_model->get_data_accounts_receivable_ageing_detail($data_filter);
                break;
            case 'accounts_receivable_ageing_summary':
                    $data['data_report'] = $this->Accounting_model->get_data_accounts_receivable_ageing_summary($data_filter);
                break;
            case 'accounts_payable_ageing_detail':
                    $data['data_report'] = $this->Accounting_model->get_data_accounts_payable_ageing_detail($data_filter);
                break;
            case 'accounts_payable_ageing_summary':
                    $data['data_report'] = $this->Accounting_model->get_data_accounts_payable_ageing_summary($data_filter);
                break;
            case 'profit_and_loss_12_months':
                    $data['data_report'] = $this->Accounting_model->get_data_profit_and_loss_12_months($data_filter);
                break;
            case 'budget_overview':
                    $data['data_report'] = $this->Accounting_model->get_data_budget_overview($data_filter);
                break;
            case 'budget_variance':
                    $data['data_report'] = $this->Accounting_model->get_data_budget_variance($data_filter);
                break;
            case 'budget_comparison':
                    $data['data_report'] = $this->Accounting_model->get_data_budget_comparison($data_filter);
                break;
            case 'profit_and_loss_budget_performance':
                    $data['data_report'] = $this->Accounting_model->get_data_profit_and_loss_budget_performance($data_filter);
                break;
            case 'profit_and_loss_budget_vs_actual':
                    $data['data_report'] = $this->Accounting_model->get_data_profit_and_loss_budget_vs_actual($data_filter);
                break;
            default:
                break;
        }

        return $this->template->view('Accounting\Views\report/details/'.$data_filter['type'], $data);
    }

    /**
     * get data dashboard
     * @return json
     */
    public function get_data_dashboard(){
        $data_filter = $this->request->getGet();

        $data['profit_and_loss_chart'] = $this->Accounting_model->get_data_profit_and_loss_chart($data_filter);
        $data['expenses_chart'] = $this->Accounting_model->get_data_expenses_chart($data_filter);
        $data['income_chart'] = $this->Accounting_model->get_data_income_chart($data_filter);
        $data['sales_chart'] = $this->Accounting_model->get_data_sales_chart($data_filter);
        $data['bank_accounts'] = $this->Accounting_model->get_data_bank_accounts_dashboard($data_filter);
        $data['convert_status'] = $this->Accounting_model->get_data_convert_status_dashboard($data_filter);

        echo json_encode($data);
    }

    /**
     * update reset all data accounting module
     */
    public function reset_data(){
        if (!has_permission('accounting_setting', '', 'delete') && !is_admin() ) {
            access_denied('accounting_setting');
        }

        $success = $this->Accounting_model->reset_data();
        if($success == true){
            $message = app_lang('reset_data_successfully');
            $this->session->setFlashdata("success_message", $message);
        }
        redirect(get_uri('accounting/setting?group=general'));
    }

    /* Change status to account active or inactive / ajax */
    public function change_account_status($id, $status)
    {
        if (has_permission('accounting_chart_of_accounts', '', 'edit')) {
            $this->Accounting_model->change_account_status($id, $status);
        }
    }

    /**
     * item automatic table
     * @return json
     */
    public function item_automatic_table()
    {
            $currency_symbol = get_setting("currency_symbol");
            $select = [
                get_db_prefix() . 'acc_item_automatics.id as id',
                get_db_prefix() . 'items.title as title',
                'description',
                'rate',
            ];
            $where = [];

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'acc_item_automatics';
            $join         = ['LEFT JOIN ' . get_db_prefix() . 'items ON ' . get_db_prefix() . 'items.id = ' . get_db_prefix() . 'acc_item_automatics.item_id',
                            'LEFT JOIN ' . get_db_prefix() . 'item_categories ON ' . get_db_prefix() . 'item_categories.id = ' . get_db_prefix() . 'items.category_id',
                        ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [get_db_prefix() . 'item_categories.title as group_name', 'inventory_asset_account', 'income_account', 'expense_account','item_id']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $categoryOutput = $aRow['title'];

                $row[] = $categoryOutput;
                $row[] = $aRow['description'];


                $row[] = $aRow['group_name'];

                $row[] = to_currency($aRow['rate'], $currency_symbol);

                $options = "";
                if (has_permission('accounting_setting', '', 'edit')) {
                    $options .= '<a href="#" onclick="edit_item_automatic(this); return false;" class="edit" data-id="'.$aRow['id'].'" data-inventory-asset-account="'.$aRow['inventory_asset_account'].'" data-income-account="'.$aRow['income_account'].'" data-expense-account="'.$aRow['expense_account'].'" data-item-id="'.$aRow['item_id'].'"><i data-feather="edit" class="icon-16"></i></a>';
                }
                if (has_permission('accounting_setting', '', 'delete')) {
                    $options .= '<a href="' . get_uri('accounting/delete_item_automatic/' . $aRow['id']) . '" class="delete"><i data-feather="x" class="icon-16"></i></a>';
                }

                $row[] =  $options;

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * add or edit item automatic
     * @return json
     */
    public function item_automatic(){
        $data = $this->request->getPost();
        if($data['id'] == ''){
            if (!has_permission('accounting_setting', '', 'create')) {
                access_denied('accounting');
            }
            $success = $this->Accounting_model->add_item_automatic($data);
            if($success){
                $message = sprintf(app_lang('added_successfully'), app_lang('item_automatic'));
                $this->session->setFlashdata("success_message", $message);
            }else {
                $message = app_lang('add_failure');
                $this->session->setFlashdata("error_message", $message);
            }
        }else{
            if (!has_permission('accounting_setting', '', 'edit')) {
                access_denied('accounting');
            }
            $id = $data['id'];
            unset($data['id']);
            $success = $this->Accounting_model->update_item_automatic($data, $id);
            $message = app_lang('fail');
            if ($success) {
                $message = sprintf(app_lang('updated_successfully'), app_lang('item_automatic'));
                $this->session->setFlashdata("success_message", $message);
            }else{
                $this->session->setFlashdata("error_message", $message);
            }
        }

        app_redirect('accounting/setting?group=mapping_setup');
    }

    /**
     * delete item automatic
     * @param  integer $id
     * @return
     */
    public function delete_item_automatic($id)
    {
        if (!has_permission('accounting_setting', '', 'delete')) {
            access_denied('accounting');
        }

        $success = $this->Accounting_model->delete_item_automatic($id);
        $message = '';
        if ($success) {
            $message = sprintf(app_lang('deleted'), app_lang('item_automatic'));
            $this->session->setFlashdata("success_message", $message);
        } else {
            $message = app_lang('can_not_delete');
            $this->session->setFlashdata("error_message", $message);
        }
        app_redirect('accounting/setting?group=mapping_setup');
    }

    /**
     * transaction bulk action
     */
    public function transaction_bulk_action()
    {
        $total_deleted = 0;
        if ($this->request->getPost()) {
            $type    = $this->request->getPost('type');
            $ids       = $this->request->getPost('ids');

            $is_admin  = is_admin();
            if (is_array($ids)) {
                if($type == 'payment'){
                    foreach ($ids as $id) {
                        if ($this->request->getPost('mass_convert') === 'true') {
                            if (has_permission('accounting_transaction', '', 'create')) {
                                if ($this->Accounting_model->automatic_payment_conversion($id)) {
                                    $total_deleted++;
                                }
                            }
                        }elseif($this->request->getPost('mass_delete_convert') === 'true'){
                            if (has_permission('accounting_transaction', '', 'delete')) {
                                if ($this->Accounting_model->delete_convert($id, 'payment')) {
                                    $total_deleted++;
                                }
                            }
                        }
                    }
                }elseif ($type == 'invoice') {
                    foreach ($ids as $id) {
                        if ($this->request->getPost('mass_convert') === 'true') {
                            if (has_permission('accounting_transaction', '', 'create')) {
                                if ($this->Accounting_model->automatic_invoice_conversion($id)) {
                                    $total_deleted++;
                                }
                            }
                        }elseif($this->request->getPost('mass_delete_convert') === 'true'){
                            if (has_permission('accounting_transaction', '', 'delete')) {
                                if ($this->Accounting_model->delete_convert($id, 'invoice')) {
                                    $total_deleted++;
                                }
                            }
                        }
                    }
                }elseif ($type == 'expense') {
                    foreach ($ids as $id) {
                        if ($this->request->getPost('mass_convert') === 'true') {
                            if (has_permission('accounting_transaction', '', 'create')) {
                                if ($this->Accounting_model->automatic_expense_conversion($id)) {
                                    $total_deleted++;
                                }
                            }
                        }elseif($this->request->getPost('mass_delete_convert') === 'true'){
                            if (has_permission('accounting_transaction', '', 'delete')) {
                                if ($this->Accounting_model->delete_convert($id, 'expense')) {
                                    $total_deleted++;
                                }
                            }
                        }
                    }
                }elseif ($type == 'banking') {
                    foreach ($ids as $id) {
                        if ($this->request->getPost('mass_delete') === 'true') {
                            if (has_permission('accounting_transaction', '', 'create')) {
                                if ($this->Accounting_model->delete_banking($id)) {
                                    $total_deleted++;
                                }
                            }
                        }elseif($this->request->getPost('mass_delete_convert') === 'true'){
                            if (has_permission('accounting_transaction', '', 'delete')) {
                                if ($this->Accounting_model->delete_convert($id, 'banking')) {
                                    $total_deleted++;
                                }
                            }
                        }
                    }
                }elseif ($type == 'payslip') {
                    foreach ($ids as $id) {
                        if ($this->request->getPost('mass_convert') === 'true') {
                            if (has_permission('accounting_transaction', '', 'create')) {
                                if ($this->Accounting_model->automatic_payslip_conversion($id)) {
                                    $total_deleted++;
                                }
                            }
                        }elseif($this->request->getPost('mass_delete_convert') === 'true'){
                            if (has_permission('accounting_transaction', '', 'delete')) {
                                if ($this->Accounting_model->delete_convert($id, 'payslip')) {
                                    $total_deleted++;
                                }
                            }
                        }
                    }
                }elseif ($type == 'purchase_order') {
                    foreach ($ids as $id) {
                        if ($this->request->getPost('mass_convert') === 'true') {
                            if (has_permission('accounting_transaction', '', 'create')) {
                                if ($this->Accounting_model->automatic_purchase_order_conversion($id)) {
                                    $total_deleted++;
                                }
                            }
                        }elseif($this->request->getPost('mass_delete_convert') === 'true'){
                            if (has_permission('accounting_transaction', '', 'delete')) {
                                if ($this->Accounting_model->delete_convert($id, 'purchase_order')) {
                                    $total_deleted++;
                                }
                            }
                        }
                    }
                }elseif ($type == 'purchase_payment') {
                    foreach ($ids as $id) {
                        if ($this->request->getPost('mass_convert') === 'true') {
                            if (has_permission('accounting_transaction', '', 'create')) {
                                if ($this->Accounting_model->automatic_purchase_payment_conversion($id)) {
                                    $total_deleted++;
                                }
                            }
                        }elseif($this->request->getPost('mass_delete_convert') === 'true'){
                            if (has_permission('accounting_transaction', '', 'delete')) {
                                if ($this->Accounting_model->delete_convert($id, 'purchase_payment')) {
                                    $total_deleted++;
                                }
                            }
                        }
                    }
                }elseif ($type == 'stock_import') {
                    foreach ($ids as $id) {
                        if ($this->request->getPost('mass_convert') === 'true') {
                            if (has_permission('accounting_transaction', '', 'create')) {
                                if ($this->Accounting_model->automatic_stock_import_conversion($id)) {
                                    $total_deleted++;
                                }
                            }
                        }elseif($this->request->getPost('mass_delete_convert') === 'true'){
                            if (has_permission('accounting_transaction', '', 'delete')) {
                                if ($this->Accounting_model->delete_convert($id, 'stock_import')) {
                                    $total_deleted++;
                                }
                            }
                        }
                    }
                }elseif ($type == 'stock_export') {
                    foreach ($ids as $id) {
                        if ($this->request->getPost('mass_convert') === 'true') {
                            if (has_permission('accounting_transaction', '', 'create')) {
                                if ($this->Accounting_model->automatic_stock_export_conversion($id)) {
                                    $total_deleted++;
                                }
                            }
                        }elseif($this->request->getPost('mass_delete_convert') === 'true'){
                            if (has_permission('accounting_transaction', '', 'delete')) {
                                if ($this->Accounting_model->delete_convert($id, 'stock_export')) {
                                    $total_deleted++;
                                }
                            }
                        }
                    }
                }elseif ($type == 'loss_adjustment') {
                    foreach ($ids as $id) {
                        if ($this->request->getPost('mass_convert') === 'true') {
                            if (has_permission('accounting_transaction', '', 'create')) {
                                if ($this->Accounting_model->automatic_loss_adjustment_conversion($id)) {
                                    $total_deleted++;
                                }
                            }
                        }elseif($this->request->getPost('mass_delete_convert') === 'true'){
                            if (has_permission('accounting_transaction', '', 'delete')) {
                                if ($this->Accounting_model->delete_convert($id, 'loss_adjustment')) {
                                    $total_deleted++;
                                }
                            }
                        }
                    }
                }elseif ($type == 'opening_stock') {
                    foreach ($ids as $id) {
                        if ($this->request->getPost('mass_convert') === 'true') {
                            if (has_permission('accounting_transaction', '', 'create')) {
                                if ($this->Accounting_model->automatic_opening_stock_conversion($id)) {
                                    $total_deleted++;
                                }
                            }
                        }elseif($this->request->getPost('mass_delete_convert') === 'true'){
                            if (has_permission('accounting_transaction', '', 'delete')) {
                                if ($this->Accounting_model->delete_convert($id, 'opening_stock')) {
                                    $total_deleted++;
                                }
                            }
                        }
                    }
                }
            }
            if ($this->request->getPost('mass_convert') === 'true') {
                $message = sprintf(app_lang('total_converted'), $total_deleted);
                $this->session->setFlashdata("success_message", $message);

            }elseif ($this->request->getPost('mass_delete_convert') === 'true') {
                $message = sprintf(app_lang('total_convert_deleted'), $total_deleted);
                $this->session->setFlashdata("success_message", $message);

            }elseif ($this->request->getPost('mass_delete') === 'true') {
                $message = sprintf(app_lang('total_deleted'), $total_deleted);
                $this->session->setFlashdata("success_message", $message);
            }
        }
    }

    /**
     * journal entry bulk action
     */
    public function journal_entry_bulk_action()
    {
        $total_deleted = 0;
        if ($this->request->getPost()) {
            $ids       = $this->request->getPost('ids');
            $is_admin  = is_admin();
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if($this->request->getPost('mass_delete') === 'true'){
                        if (has_permission('accounting_journal_entry', '', 'delete')) {
                            if ($this->Accounting_model->delete_journal_entry($id)) {
                                $total_deleted++;
                            }
                        }
                    }
                }
                
            }
            if ($this->request->getPost('mass_delete') === 'true') {
                $message = sprintf(app_lang('total_deleted'), $total_deleted);
                $this->session->setFlashdata("success_message", $message);
            }
        }
    }

    /**
     * transfer bulk action
     */
    public function transfer_bulk_action()
    {
        $total_deleted = 0;
        if ($this->request->getPost()) {
            $ids       = $this->request->getPost('ids');
            $is_admin  = is_admin();
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if($this->request->getPost('mass_delete') === 'true'){
                        if (has_permission('accounting_transfer', '', 'delete')) {
                            if ($this->Accounting_model->delete_transfer($id)) {
                                $total_deleted++;
                            }
                        }
                    }
                }
                
            }
            if ($this->request->getPost('mass_delete') === 'true') {
                $message = sprintf(app_lang('total_deleted'), $total_deleted);
                $this->session->setFlashdata("success_message", $message);
            }
        }
    }

    /**
     * tax mapping table
     * @return json
     */
    public function tax_mapping_table()
    {
           
            $select = [
                get_db_prefix() . 'acc_tax_mappings.id as id',
                'title as name',
                'percentage',
            ];
            $where = [];

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'acc_tax_mappings';
            $join         = ['LEFT JOIN ' . get_db_prefix() . 'taxes ON ' . get_db_prefix() . 'taxes.id = ' . get_db_prefix() . 'acc_tax_mappings.tax_id'];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tax_id', 'payment_account', 'deposit_to', 'expense_deposit_to', 'expense_payment_account']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $categoryOutput = $aRow['tax_id'];

                $row[] = $categoryOutput;
                $row[] = $aRow['name'];

                $row[] = $aRow['percentage'];

                $options = "";
                if (has_permission('accounting_setting', '', 'edit')) {
                    $options .= '<a href="#" onclick="edit_tax_mapping(this); return false;" class="edit" data-id="'.$aRow['id'].'" data-deposit-to="'.$aRow['deposit_to'].'" data-payment-account="'.$aRow['payment_account'].'" data-expense-deposit-to="'.$aRow['expense_deposit_to'].'" data-expense-payment-account="'.$aRow['expense_payment_account'].'" data-tax-id="'.$aRow['tax_id'].'"><i data-feather="edit" class="icon-16"></i></a>';
                }
                if (has_permission('accounting_setting', '', 'delete')) {
                    $options .= '<a href="' . get_uri('accounting/delete_tax_mapping/' . $aRow['id']) . '" class="delete"><i data-feather="x" class="icon-16"></i></a>';
                }

                $row[] =  $options;

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * add or edit tax mapping
     * @return json
     */
    public function tax_mapping(){
        $data = $this->request->getPost();
        if($data['id'] == ''){
            if (!has_permission('accounting_setting', '', 'create')) {
                access_denied('accounting');
            }
            $success = $this->Accounting_model->add_tax_mapping($data);
            if($success){
                $message = sprintf(app_lang('added_successfully'), app_lang('tax_mapping'));
                $this->session->setFlashdata("success_message", $message);
            }else {
                $message = app_lang('add_failure');
                $this->session->setFlashdata("error_message", $message);
            }
        }else{
            if (!has_permission('accounting_setting', '', 'edit')) {
                access_denied('accounting');
            }
            $id = $data['id'];
            unset($data['id']);
            $success = $this->Accounting_model->update_tax_mapping($data, $id);
            $message = app_lang('fail');
            if ($success) {
                $message = sprintf(app_lang('updated_successfully'), app_lang('tax_mapping'));
                $this->session->setFlashdata("success_message", $message);
            }else{
                $this->session->setFlashdata("error_message", $message);
            }
        }

        app_redirect('accounting/setting?group=mapping_setup');
    }

    /**
     * delete tax mapping
     * @param  integer $id
     * @return
     */
    public function delete_tax_mapping($id)
    {
        if (!has_permission('accounting_setting', '', 'delete')) {
            access_denied('accounting');
        }

        $success = $this->Accounting_model->delete_tax_mapping($id);
        $message = '';
        if ($success) {
            $message = sprintf(app_lang('deleted'), app_lang('tax_mapping'));
            $this->session->setFlashdata("success_message", $message);
        } else {
            $message = app_lang('can_not_delete');
            $this->session->setFlashdata("error_message", $message);

        }
        app_redirect('accounting/setting?group=mapping_setup');
    }

    /**
     * accounts bulk action
     */
    public function accounts_bulk_action()
    {
        $total_deleted = 0;
        if ($this->request->getPost()) {
            $ids       = $this->request->getPost('ids');
            $is_admin  = is_admin();
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if($this->request->getPost('mass_delete') === 'true'){
                        if (has_permission('accounting_chart_of_accounts', '', 'delete')) {
                            $success = $this->Accounting_model->delete_account($id);
                            if ($success === 'have_transaction') {
                                $message = app_lang('cannot_delete_transaction_already_exists');
                                $this->session->setFlashdata("error_message", $message);
                            }elseif ($success) {
                                $total_deleted++;
                            } 
                        }
                    }elseif($this->request->getPost('mass_activate') === 'true'){
                        if (has_permission('accounting_chart_of_accounts', '', 'edit')) {
                            if ($this->Accounting_model->change_account_status($id, 1)) {
                                $total_deleted++;
                            }
                        }
                    }elseif($this->request->getPost('mass_deactivate') === 'true'){
                        if (has_permission('accounting_chart_of_accounts', '', 'edit')) {
                            if ($this->Accounting_model->change_account_status($id, 0)) {
                                $total_deleted++;
                            }
                        }
                    }
                }
                
            }
            if ($this->request->getPost('mass_delete') === 'true') {
                $this->session->setFlashdata("success_message", app_lang('total_deleted', $total_deleted));
            }elseif ($this->request->getPost('mass_activate') === 'true') {
                $this->session->setFlashdata("success_message", app_lang('total_activate', $total_deleted));
            }elseif ($this->request->getPost('mass_deactivate') === 'true') {
                $this->session->setFlashdata("success_message", app_lang('total_deactivate', $total_deleted));
            }
        }
    }

    /**
     * expense category mapping table
     * @return json
     */
    public function expense_category_mapping_table()
    {
           
            $select = [
                get_db_prefix() . 'acc_expense_category_mappings.id as id',
                'title',
            ];
            $where = [];

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'acc_expense_category_mappings';
            $join         = ['LEFT JOIN ' . get_db_prefix() . 'expense_categories ON ' . get_db_prefix() . 'expense_categories.id = ' . get_db_prefix() . 'acc_expense_category_mappings.category_id'];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['category_id', 'payment_account', 'deposit_to']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $categoryOutput = $aRow['category_id'];

                $categoryOutput .= '<div class="row-options">';
                    
                

                $categoryOutput .= '</div>';
                $row[] = $categoryOutput;
                $row[] = $aRow['title'];

                $options = "";
                if (has_permission('accounting_setting', '', 'edit')) {
                    $options .= '<a href="#" onclick="edit_expense_category_mapping(this); return false;" class="edit" data-id="'.$aRow['id'].'" data-deposit-to="'.$aRow['deposit_to'].'" data-payment-account="'.$aRow['payment_account'].'" data-category-id="'.$aRow['category_id'].'"><i data-feather="edit" class="icon-16"></i></a>';
                }

                if (has_permission('accounting_setting', '', 'delete')) {
                    $options .= '<a href="' . get_uri('accounting/delete_expense_category_mapping/' . $aRow['id']) . '" class="delete"><i data-feather="x" class="icon-16"></i></a>';
                }

                $row[] =  $options;

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * add or edit expense category mapping
     * @return json
     */
    public function expense_category_mapping(){
        $data = $this->request->getPost();
        if($data['id'] == ''){
            if (!has_permission('accounting_setting', '', 'create')) {
                access_denied('accounting');
            }
            $success = $this->Accounting_model->add_expense_category_mapping($data);
            if($success){
                $message = sprintf(app_lang('added_successfully'), app_lang('expense_category_mapping'));
                $this->session->setFlashdata("success_message", $message);
            }else {
                $message = app_lang('add_failure');
                $this->session->setFlashdata("error_message", $message);
            }
        }else{
            if (!has_permission('accounting_setting', '', 'edit')) {
                access_denied('accounting');
            }
            $id = $data['id'];
            unset($data['id']);
            $success = $this->Accounting_model->update_expense_category_mapping($data, $id);
            $message = app_lang('fail');
            if ($success) {
                $message = sprintf(app_lang('updated_successfully'), app_lang('expense_category_mapping'));
                $this->session->setFlashdata("success_message", $message);
            }else{
                $this->session->setFlashdata("error_message", $message);
            }
        }


        app_redirect('accounting/setting?group=mapping_setup');
    }

    /**
     * delete expense_category mapping
     * @param  integer $id
     * @return
     */
    public function delete_expense_category_mapping($id)
    {
        if (!has_permission('accounting_setting', '', 'delete')) {
            access_denied('accounting');
        }

        $success = $this->Accounting_model->delete_expense_category_mapping($id);
        $message = '';
        if ($success) {
            $message = sprintf(app_lang('deleted'), app_lang('expense_category_mapping'));
            $this->session->setFlashdata("success_message", $message);

        } else {
            $message = app_lang('can_not_delete');
            $this->session->setFlashdata("error_message", $message);

        }
        app_redirect('accounting/setting?group=mapping_setup');
    }

    /**
     * tax detail report
     * @return view
     */
    public function rp_tax_detail_report(){
        $data['title'] = app_lang('tax_detail_report');
        $data['from_date'] = date('Y-m-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/tax_detail_report', $data);
    }

    /**
     * tax summary report
     * @return view
     */
    public function rp_tax_summary_report(){
        $data['taxes'] = $this->Taxes_model->get_details()->getResultArray();

        $data['title'] = app_lang('tax_summary_report');
        $data['from_date'] = date('Y-m-01');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        $data['to_date'] = date('Y-m-d');
        return $this->template->rander('Accounting\Views\report/includes/tax_summary_report', $data);
    }

    /**
     * tax liability report
     * @return view
     */
    public function rp_tax_liability_report(){
        $data['taxes'] = $this->Taxes_model->get_details()->getResultArray();

        $data['title'] = app_lang('tax_liability_report');
        $data['from_date'] = date('Y-m-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/tax_liability_report', $data);
    }


    /**
     * get data convert status dashboard
     * @return json
     */
    public function get_data_convert_status_dashboard(){
        $data_filter = $this->request->getGet();

        $data['convert_status'] = $this->Accounting_model->get_data_convert_status_dashboard($data_filter);

        echo json_encode($data);
    }

    /**
     * get data income chart
     * @return json
     */
    public function get_data_income_chart(){
        $data_filter = $this->request->getGet();

        $data['income_chart'] = $this->Accounting_model->get_data_income_chart($data_filter);

        echo json_encode($data);
    }

    /**
     * get data sales chart
     * @return json
     */
    public function get_data_sales_chart(){
        $data_filter = $this->request->getGet();

        $data['sales_chart'] = $this->Accounting_model->get_data_sales_chart($data_filter);

        echo json_encode($data);
    }

    /**
     * payment mode mapping table
     * @return json
     */
    public function payment_mode_mapping_table()
    {
            $select = [
                get_db_prefix() . 'acc_payment_mode_mappings.id as id',
                'title',
            ];
            $where = [];

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'acc_payment_mode_mappings';
            $join         = ['LEFT JOIN ' . get_db_prefix() . 'payment_methods ON ' . get_db_prefix() . 'payment_methods.id = ' . get_db_prefix() . 'acc_payment_mode_mappings.payment_mode_id'];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['payment_mode_id', 'payment_account', 'deposit_to',  'expense_payment_account', 'expense_deposit_to','description']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $categoryOutput = $aRow['title'];

                $row[] = $categoryOutput;

                $row[] = $aRow['description'];

                $options = "";
                if (has_permission('accounting_setting', '', 'edit')) {
                    $options .= '<a href="#" onclick="edit_payment_mode_mapping(this); return false;" class="edit" data-id="'.$aRow['id'].'" data-deposit-to="'.$aRow['deposit_to'].'" data-payment-account="'.$aRow['payment_account'].'" data-expense-deposit-to="'.$aRow['expense_deposit_to'].'" data-expense-payment-account="'.$aRow['expense_payment_account'].'" data-payment-mode-id="'.$aRow['payment_mode_id'].'"><i data-feather="edit" class="icon-16"></i></a>';
                }
                if (has_permission('accounting_setting', '', 'delete')) {
                    $options .= '<a href="' . get_uri('accounting/delete_payment_mode_mapping/' . $aRow['id']) . '" class="delete"><i data-feather="x" class="icon-16"></i></a>';
                }

                $row[] =  $options;

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * add or edit payment mode mapping
     * @return json
     */
    public function payment_mode_mapping(){
        $data = $this->request->getPost();
        if($data['id'] == ''){
            if (!has_permission('accounting_setting', '', 'create')) {
                access_denied('accounting');
            }
            $success = $this->Accounting_model->add_payment_mode_mapping($data);
            if($success){
                $message = sprintf(app_lang('added_successfully'), app_lang('payment_mode_mapping'));
                $this->session->setFlashdata("success_message", $message);
            }else {
                $message = app_lang('add_failure');
                $this->session->setFlashdata("error_message", $message);
            }
        }else{
            if (!has_permission('accounting_setting', '', 'edit')) {
                access_denied('accounting');
            }
            $id = $data['id'];
            unset($data['id']);
            $success = $this->Accounting_model->update_payment_mode_mapping($data, $id);
            $message = app_lang('fail');
            if ($success) {
                $message = sprintf(app_lang('updated_successfully'), app_lang('payment_mode_mapping'));
                $this->session->setFlashdata("success_message", $message);
            }else{
                $this->session->setFlashdata("error_message", $message);
            }
        }


        app_redirect('accounting/setting?group=mapping_setup');
    }

    /**
     * delete payment mode mapping
     * @param  integer $id
     * @return
     */
    public function delete_payment_mode_mapping($id)
    {
        if (!has_permission('accounting_setting', '', 'delete')) {
            access_denied('accounting');
        }

        $success = $this->Accounting_model->delete_payment_mode_mapping($id);
        $message = '';
        if ($success) {
            $message = sprintf(app_lang('deleted'), app_lang('payment_mode_mapping'));
            $this->session->setFlashdata("success_message", $message);
        } else {
            $message = app_lang('can_not_delete');
            $this->session->setFlashdata("error_message", $message);
        }
        app_redirect('accounting/setting?group=mapping_setup');
    }

    /* Change status to payment mode mapping active or inactive / ajax */
    public function change_active_payment_mode_mapping($status)
    {
        if (has_permission('accounting_setting', '', 'edit')) {
            $this->Accounting_model->change_active_payment_mode_mapping($status);
        }
    }

    /* Change status to expense category mapping active or inactive / ajax */
    public function change_active_expense_category_mapping($status)
    {
        if (has_permission('accounting_setting', '', 'edit')) {
            $this->Accounting_model->change_active_expense_category_mapping($status);
        }
    }

    /**
     * account type details table
     * @return json
     */
    public function account_type_details_table(){
            $account_types = $this->Accounting_model->get_account_types();

            $account_type_name = [];
            foreach ($account_types as $key => $value) {
                $account_type_name[$value['id']] = $value['name'];
            }

            $select = [
                'id',
                'name',
            ];

            $where = [];
            $from_date = '';
            $to_date   = '';

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'acc_account_type_details';
            $join         = [];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['account_type_id']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $categoryOutput = $aRow['name'];

                $row[] = $categoryOutput;
                $row[] = isset($account_type_name[$aRow['account_type_id']]) ? $account_type_name[$aRow['account_type_id']] : '';
                $options = "";
                if (has_permission('accounting_setting', '', 'edit')) {
                    $options .= '<a href="#" onclick="edit_account_type_detail(' . $aRow['id'] . '); return false;" class="edit"><i data-feather="edit" class="icon-16"></i></a>';
                }

                if (has_permission('accounting_setting', '', 'delete')) {
                    $options .= '<a href="' . get_uri('accounting/delete_account_type_detail/' . $aRow['id']) . '" class="delete"><i data-feather="x" class="icon-16"></i></a>';
                }

                $row[] =  $options;

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     *
     *  add or edit account type detail
     *  @param  integer  $id     The identifier
     *  @return view
     */
    public function account_type_detail()
    {
        if (!has_permission('accounting_setting', '', 'edit') && !has_permission('accounting_setting', '', 'create')) {
            access_denied('accounting');
        }

        if ($this->request->getPost()) {
            $data = $this->request->getPost();
            $message = '';
            if ($data['id'] == '') {
                if (!has_permission('accounting_setting', '', 'create')) {
                    access_denied('accounting');
                }
                $success = $this->Accounting_model->add_account_type_detail($data);
                if ($success) {
                    $message = sprintf(app_lang('added_successfully'), app_lang('account_type_detail'));
                    $this->session->setFlashdata("success_message", $message);
                }else {
                    $message = app_lang('add_failure');
                    $this->session->setFlashdata("error_message", $message);
                }
            } else {
                if (!has_permission('accounting_setting', '', 'edit')) {
                    access_denied('accounting');
                }
                $id = $data['id'];
                unset($data['id']);
                $success = $this->Accounting_model->update_account_type_detail($data, $id);
                if ($success) {
                    $message = sprintf(app_lang('updated_successfully'), app_lang('account_type_detail'));
                    $this->session->setFlashdata("success_message", $message);
                }else {
                    $message = app_lang('updated_fail');
                    $this->session->setFlashdata("error_message", $message);
                }
            }

            app_redirect('accounting/setting?group=account_type_details');
        }
    }

    /**
     * delete account type detail
     * @param  integer $id
     * @return
     */
    public function delete_account_type_detail($id)
    {
        if (!has_permission('accounting_setting', '', 'delete')) {
            access_denied('accounting_setting');
        }
        $success = $this->Accounting_model->delete_account_type_detail($id);
        $message = '';
        
        if ($success === 'have_account') {
            $message = app_lang('cannot_delete_account_already_exists');
            $this->session->setFlashdata("error_message", $message);
        }elseif ($success) {
            $message = sprintf(app_lang('deleted'), app_lang('account_type_detail'));
            $this->session->setFlashdata("success_message", $message);
        } else {
            $message = app_lang('can_not_delete');
            $this->session->setFlashdata("error_message", $message);
        }

        app_redirect('accounting/setting?group=account_type_details');
    }

    /**
     * get data account type detail
     * @param  integer $id 
     * @return json     
     */
    public function get_data_account_type_detail($id){
        $account_type_detail = $this->Accounting_model->get_data_account_type_details($id);

        echo json_encode($account_type_detail);
    }

    /**
     * journal entry export
     * @param  integer $id
     */
    public function journal_entry_export($id){
        $this->delete_error_file_day_before(1,ACCOUTING_EXPORT_XLSX); 

        $currency_symbol = get_setting("currency_symbol");

        $header = [];
        $header = [ app_lang('asp_order'), app_lang('asp_date'), app_lang('asp_creation_date'), app_lang('asp_invoice_number'), app_lang('asp_reference'), app_lang('asp_book'), app_lang('asp_account'), app_lang('asp_nif'), app_lang('asp_desc'), app_lang('asp_total_invoice'), app_lang('asp_subtotal_1'), app_lang('asp_vat_1'), app_lang('asp_subtotal_2'), app_lang('asp_vat_2'), app_lang('asp_subtotal_3'), app_lang('asp_vat_3'),  app_lang('asp_subtotal_4'), app_lang('asp_vat_4'),  app_lang('asp_subtotal_5'), app_lang('asp_vat_5'), app_lang('asp_libro_contrapartida'), app_lang('asp_cuenta_contrapartida'), app_lang('asp_lote_a_contabilizar')];

        $accounts = $this->Accounting_model->get_accounts();

        $account_name = [];
        foreach ($accounts as $key => $value) {
            $account_name[$value['id']] = $value['name'];
        }

        $journal_entry = $this->Accounting_model->get_journal_entry($id);

        if(!class_exists('XLSXWriter')){
            require_once(PLUGINPATH. 'Accounting/assets/plugins/XLSXWriter/xlsxwriter.class.php');             
        }

       

        $header = [ 
           1 => app_lang('acc_account'), 
           2 => app_lang('debit'), 
           3 => app_lang('credit'), 
           4 => app_lang('description'), 
        ];

        $widths_arr = array();
       
        for($i = 1; $i <= count($header); $i++ ){
            if($i == 1){
                $widths_arr[] = 60;
            }else if($i == 8){
                $widths_arr[] = 60;
            }else{
                $widths_arr[] = 40;
            }
        }

        $writer = new \XLSXWriter();
        $writer->writeSheetRow('Sheet1', []);
        $writer->writeSheetRow('Sheet1', [1 => app_lang('journal_date').': '. format_to_date($journal_entry->journal_date), ]);
        $writer->writeSheetRow('Sheet1', [1 => app_lang('number').': '. $journal_entry->number, ]);
        $writer->writeSheetRow('Sheet1', [1 => app_lang('description').': '. $journal_entry->description, ]);
        $writer->writeSheetRow('Sheet1', []);

        
        $style3 = array('fill' => '#C65911', 'height'=>25, 'font-style'=>'bold', 'color' => '#FFFFFF', 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 15, 'font' => 'Calibri');
        $style1 = array('fill' => '#F8CBAD', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 15, 'font' => 'Calibri', 'color' => '#000000');
        $style2 = array('fill' => '#FCE4D6', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 15, 'font' => 'Calibri', 'color' => '#000000');

        $writer->writeSheetRow('Sheet1', $header, $style3);

        foreach($journal_entry->details as $k => $row){
            $row['account'] = isset($account_name[$row['account']]) ? $account_name[$row['account']] : $row['account'];
            $row['debit'] =$row['debit'] > 0 ? to_currency($row['debit'], $currency_symbol) : '';
            $row['credit'] =$row['credit'] > 0 ? to_currency($row['credit'], $currency_symbol) : '';

            if(($k%2) == 0){
                $writer->writeSheetRow('Sheet1', $row , $style1);
            }else{
                $writer->writeSheetRow('Sheet1', $row , $style2);
            }
        }

        $writer->writeSheetRow('Sheet1', [1 => app_lang('total'), 2 => to_currency($journal_entry->amount, $currency_symbol), 3 => to_currency($journal_entry->amount, $currency_symbol), 4 => ''], $style3);

        $filename = 'journal_entry_'.time().'.xlsx';
        $writer->writeToFile(str_replace($filename, ACCOUTING_EXPORT_XLSX.$filename, $filename));
        $this->download_xlsx_file(ACCOUTING_EXPORT_XLSX.$filename);
        die();
    }

    /**
     * download xlsx file
     * @param  string $filename
     */
    public function download_xlsx_file($filename){
        $file = $filename;
        $mime = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        ob_end_clean();
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime);
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . basename($file) . "\"");
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        readfile($file);
        unlink($file);
        exit();
    }

    /**
     * delete error file day before
     * @param  string $before_day  
     * @param  string $folder_name 
     * @return boolean              
     */
    public function delete_error_file_day_before($before_day ='', $folder_name='')
    {
        if($before_day != ''){
            $day = $before_day;
        }else{
            $day = '7';
        }

        if($folder_name != ''){
            $folder = $folder_name;
        }else{
            $folder = ACCOUTING_IMPORT_ITEM_ERROR;
        }

        //Delete old file before 7 day
        $date = date_create(date('Y-m-d H:i:s'));
        date_sub($date,date_interval_create_from_date_string($day." days"));
        $before_7_day = strtotime(date_format($date,"Y-m-d H:i:s"));

        foreach(glob($folder . '*') as $file) {

            $file_arr = explode("/",$file);
            $filename = array_pop($file_arr);

            if(file_exists($file)) {
                //don't delete index.html file
                if($filename != 'index.html'){
                    $file_name_arr = explode("_",$filename);
                    $date_create_file = array_pop($file_name_arr);
                    $date_create_file =  str_replace('.xlsx', '', $date_create_file);

                    if((float)$date_create_file <= (float)$before_7_day){
                        unlink($folder.$filename);
                    }
                }
            }
        }
        return true;
    }

    /* Change status to preferred payment method on or off / ajax */
    public function change_preferred_payment_method($id, $status)
    {
        if (has_permission('staff', '', 'edit')) {
            if ($this->input->is_ajax_request()) {
                $this->Accounting_model->change_preferred_payment_method($id, $status);
            }
        }
    }

    /**
     * payslips table
     * @return json
     */
    public function payslips_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $acc_closing_date = '';
            if(get_setting('acc_close_the_books') == 1){
                $acc_closing_date = get_setting('acc_closing_date');
            }
            $select = [
                '1',
                'payslip_name',
                'payslip_template_id',
                'payslip_month',
                'staff_id_created',
                'date_created',
                'payslip_status',
                '(select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'hrp_payslips.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "payslip") as count_account_historys',
                'id',
            ];

            $where = [];

            if ($this->request->getPost('status')) {
                $status = $this->request->getPost('status');
                $where_status = '';
                foreach ($status as $key => $value) {
                    if($value == 'converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'hrp_payslips.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "payslip") > 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'hrp_payslips.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "payslip") > 0)';
                        }
                    }

                    if($value == 'has_not_been_converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'hrp_payslips.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "payslip") = 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'hrp_payslips.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "payslip") = 0)';
                        }
                    }
                }

                if($where_status != ''){
                    array_push($where, 'AND ('. $where_status . ')');
                }
            }
            
            $from_date = '';
            $to_date   = '';
            if ($this->request->getPost('from_date')) {
                $from_date = $this->request->getPost('from_date');
                if (!$this->Accounting_model->check_format_date($from_date)) {
                    $from_date = to_sql_date($from_date);
                }
            }

            if ($this->request->getPost('to_date')) {
                $to_date = $this->request->getPost('to_date');
                if (!$this->Accounting_model->check_format_date($to_date)) {
                    $to_date = to_sql_date($to_date);
                }
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'hrp_payslips.payslip_month >= "' . $from_date . '" and ' . get_db_prefix() . 'hrp_payslips.payslip_month <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'hrp_payslips.payslip_month >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'hrp_payslips.payslip_month <= "' . $to_date . '")');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'hrp_payslips';
            $join         = [
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
                //load by manager
                if(!is_admin() && !has_permission('hrp_payslip','','view')){
                    //View own
                    $code = '<a href="' . get_uri('hr_payroll/view_payslip_detail_v2/' . $aRow['id']) . '" target="_blank">' . $aRow['payslip_name'] . '</a>';
                    $code .= '<div class="row-options">';
                }else{
                    //admin or view global
                    $code = '<a href="' . get_uri('hr_payroll/view_payslip_detail/' . $aRow['id']) . '" target="_blank">' . $aRow['payslip_name'] . '</a>';
                    $code .= '<div class="row-options">';
                }

                if ($aRow['count_account_historys'] == 0) {
                    if (has_permission('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['payslip_month'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))) {
                        $code .= '<a href="#" onclick="convert(this); return false;" class="text-success" id="payslip-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="payslip">' . app_lang('acc_convert') . '</a>';
                    }
                }else{
                    if (has_permission('accounting_transaction', '', 'edit')) {
                        $code .= '<a href="#" onclick="convert(this); return false;" id="payslip-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="payslip">' . app_lang('edit') . '</a>';
                    }
                    if (has_permission('accounting_transaction', '', 'delete')) {
                        $code .= ' | <a href="#" onclick="delete_convert('.$aRow['id'].', \'payslip\'); return false;" class="text-danger">' . app_lang('delete') . '</a>';
                    }
                }

                $code .= '</div>';

                $row[] = $code;

                $row[] = get_payslip_template_name($aRow['payslip_template_id']);

                $row[] =  date('m-Y', strtotime($aRow['payslip_month']));

                $_data = '<a href="' . get_uri('staff/profile/' . $aRow['staff_id_created']) . '" target="_blank">' . staff_profile_image($aRow['staff_id_created'], [
                'staff-profile-image-small',
                ]) . '</a>';
                $_data .= ' <a href="' . get_uri('staff/profile/' . $aRow['staff_id_created']) . '" target="_blank">' . get_staff_full_name($aRow['staff_id_created']) . '</a>';

                $row[] = $_data;
                $row[] = _dt($aRow['date_created']);

                if($aRow['payslip_status'] == 'payslip_closing'){
                    $row[] = ' <span class="label label-success "> '.app_lang($aRow['payslip_status']).' </span>';
                }else{
                    $row[] = ' <span class="label label-primary"> '.app_lang($aRow['payslip_status']).' </span>';
                }

                $status_name = app_lang('has_not_been_converted');
                $label_class = 'default';

                if ($aRow['count_account_historys'] > 0) {
                    $label_class = 'success';
                    $status_name = app_lang('acc_converted');
                } 
                $row[] = '<span class="label label-' . $label_class . ' s-status payslip-status-' . $aRow['id'] . '">' . $status_name . '</span>';
                
                $options = '';
                if($aRow['count_account_historys'] == 0 && has_permission('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['payslip_month'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))){
                    $options = icon_btn('#', 'share', 'btn-success', [
                        'title' => app_lang('acc_convert'),
                        'data-id' =>$aRow['id'],
                        'data-type' => 'payslip',
                        'onclick' => 'convert(this); return false;'
                    ]);
                }

                $row[] =  $options;

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * purchase order table
     * @return json
     */
    public function purchase_order_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $acc_closing_date = '';
            if(get_setting('acc_close_the_books') == 1){
                $acc_closing_date = get_setting('acc_closing_date');
            }
            $select = [
                '1',
                'pur_order_number',
                'order_date',
                get_db_prefix().'pur_orders.vendor as vendor',
                'subtotal',
                'total_tax',
                'total',
                'number',
                'expense_convert',
                '(select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'pur_orders.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "purchase_order") as count_account_historys',
                get_db_prefix() .'pur_orders.id as id',
            ];

            $where = [];

            if ($this->request->getPost('status')) {
                $status = $this->request->getPost('status');
                $where_status = '';
                foreach ($status as $key => $value) {
                    if($value == 'converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'pur_orders.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "purchase_order") > 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'pur_orders.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "purchase_order") > 0)';
                        }
                    }

                    if($value == 'has_not_been_converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'pur_orders.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "purchase_order") = 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'pur_orders.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "purchase_order") = 0)';
                        }
                    }
                }

                if($where_status != ''){
                    array_push($where, 'AND ('. $where_status . ')');
                }
            }
            
            $from_date = '';
            $to_date   = '';
            if ($this->request->getPost('from_date')) {
                $from_date = $this->request->getPost('from_date');
                if (!$this->Accounting_model->check_format_date($from_date)) {
                    $from_date = to_sql_date($from_date);
                }
            }

            if ($this->request->getPost('to_date')) {
                $to_date = $this->request->getPost('to_date');
                if (!$this->Accounting_model->check_format_date($to_date)) {
                    $to_date = to_sql_date($to_date);
                }
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'pur_orders.order_date >= "' . $from_date . '" and ' . get_db_prefix() . 'pur_orders.order_date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'pur_orders.order_date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'pur_orders.order_date <= "' . $to_date . '")');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'pur_orders';
            $join         = [
                'LEFT JOIN '.get_db_prefix().'pur_vendor ON '.get_db_prefix().'pur_vendor.userid = '.get_db_prefix().'pur_orders.vendor',
                'LEFT JOIN '.get_db_prefix().'departments ON '.get_db_prefix().'departments.departmentid = '.get_db_prefix().'pur_orders.department',
                'LEFT JOIN '.get_db_prefix().'projects ON '.get_db_prefix().'projects.id = '.get_db_prefix().'pur_orders.project',
                'LEFT JOIN '.get_db_prefix().'expenses ON '.get_db_prefix().'expenses.id = '.get_db_prefix().'pur_orders.expense_convert',
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['company','pur_order_number','expense_convert',get_db_prefix().'projects.name as project_name',get_db_prefix().'departments.name as department_name', get_db_prefix().'expenses.id as expense_id', get_db_prefix().'expenses.expense_name as expense_name']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';

                $numberOutput = '';
    
                $numberOutput = '<a href="' . get_uri('purchase/purchase_order/' . $aRow['id']) . '"  onclick="init_pur_order(' . $aRow['id'] . '); return false;" >'.$aRow['pur_order_number']. '</a>';
                
                $numberOutput .= '<div class="row-options">';

                if ($aRow['count_account_historys'] == 0) {
                    if (has_permission('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['order_date'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))) {
                        $numberOutput .= '<a href="#" onclick="convert(this); return false;" class="text-success" id="purchase-order-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="purchase_order">' . app_lang('acc_convert') . '</a>';
                    }
                }else{
                    if (has_permission('accounting_transaction', '', 'edit')) {
                        $numberOutput .= '<a href="#" onclick="convert(this); return false;" id="purchase-order-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="purchase_order">' . app_lang('edit') . '</a>';
                    }
                    if (has_permission('accounting_transaction', '', 'delete')) {
                        $numberOutput .= ' | <a href="#" onclick="delete_convert('.$aRow['id'].', \'purchase_order\'); return false;" class="text-danger">' . app_lang('delete') . '</a>';
                    }
                }

                $numberOutput .= '</div>';

                $row[] = $numberOutput;

                $row[] = format_to_date($aRow['order_date']);

                $row[] = '<a href="' . get_uri('purchase/vendor/' . $aRow['vendor']) . '" >' .  $aRow['company'] . '</a>';

                $row[] = to_currency($aRow['subtotal'], $currency->name);

                $row[] = to_currency($aRow['total_tax'], $currency->name);

                $row[] = to_currency($aRow['total'], $currency->name);

                $paid = $aRow['total'] - purorder_inv_left_to_pay($aRow['id']);

                $percent = 0;

                if($aRow['total'] > 0){

                    $percent = ($paid / $aRow['total'] ) * 100;

                }

                $row[] = '<div class="progress">

                              <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40"

                              aria-valuemin="0" aria-valuemax="100" style="width:'.round($percent).'%">

                               ' .round($percent).' % 

                              </div>

                            </div>';

                if($aRow['expense_convert'] == 0){
                    $row[] = '';
                }else{
                    if($aRow['expense_name'] != ''){
                        $row[] = '<a href="'.get_uri('expenses/list_expenses/'.$aRow['expense_convert']).'">#'.$aRow['expense_id'].' - '. $aRow['expense_name'].'</a>';
                    }else{
                        $row[] = '<a href="'.get_uri('expenses/list_expenses/'.$aRow['expense_convert']).'">#'.$aRow['expense_id'].'</a>';
                    }
                }

                $status_name = app_lang('has_not_been_converted');
                $label_class = 'default';

                if ($aRow['count_account_historys'] > 0) {
                    $label_class = 'success';
                    $status_name = app_lang('acc_converted');
                } 

                $row[] = '<span class="label label-' . $label_class . ' s-status purchase_order-status-' . $aRow['id'] . '">' . $status_name . '</span>';
                
                $options = '';
                if($aRow['count_account_historys'] == 0 && has_permission('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['order_date'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))){
                    $options = icon_btn('#', 'share', 'btn-success', [
                        'title' => app_lang('acc_convert'),
                        'data-id' =>$aRow['id'],
                        'data-type' => 'purchase_order',
                        'onclick' => 'convert(this); return false;'
                    ]);
                }

                $row[] =  $options;

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * stock import table
     * @return json
     */
    public function stock_import_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $acc_closing_date = '';
            if(get_setting('acc_close_the_books') == 1){
                $acc_closing_date = get_setting('acc_closing_date');
            }
            $select = [
                '1',
                'goods_receipt_code',
                'date_c',
                'total_tax_money', 
                'total_goods_money',
                'value_of_inventory',
                'total_money',
                'approval',
                '(select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'goods_receipt.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "stock_import") as count_account_historys',
                'id',
            ];

            $where = [];

            if ($this->request->getPost('status')) {
                $status = $this->request->getPost('status');
                $where_status = '';
                foreach ($status as $key => $value) {
                    if($value == 'converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'goods_receipt.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "stock_import") > 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'goods_receipt.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "stock_import") > 0)';
                        }
                    }

                    if($value == 'has_not_been_converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'goods_receipt.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "stock_import") = 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'goods_receipt.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "stock_import") = 0)';
                        }
                    }
                }

                if($where_status != ''){
                    array_push($where, 'AND ('. $where_status . ')');
                }
            }
            
            $from_date = '';
            $to_date   = '';
            if ($this->request->getPost('from_date')) {
                $from_date = $this->request->getPost('from_date');
                if (!$this->Accounting_model->check_format_date($from_date)) {
                    $from_date = to_sql_date($from_date);
                }
            }

            if ($this->request->getPost('to_date')) {
                $to_date = $this->request->getPost('to_date');
                if (!$this->Accounting_model->check_format_date($to_date)) {
                    $to_date = to_sql_date($to_date);
                }
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'goods_receipt.date_c >= "' . $from_date . '" and ' . get_db_prefix() . 'goods_receipt.date_c <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'goods_receipt.date_c >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'goods_receipt.date_c <= "' . $to_date . '")');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'goods_receipt';
            $join         = [
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['date_add','goods_receipt_code', 'supplier_code']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';

                $name = '<a href="' . get_uri('warehouse/edit_purchase/' . $aRow['id'] ).'">' . $aRow['goods_receipt_code'] . '</a>';

                $name .= '<div class="row-options">';

                if ($aRow['count_account_historys'] == 0) {
                    if (has_permission('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date_c'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))) {
                        $name .= '<a href="#" onclick="convert(this); return false;" class="text-success" id="stock-import-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="stock_import">' . app_lang('acc_convert') . '</a>';
                    }
                }else{
                    if (has_permission('accounting_transaction', '', 'edit')) {
                        $name .= '<a href="#" onclick="convert(this); return false;" id="stock-import-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="stock_import">' . app_lang('edit') . '</a>';
                    }
                    if (has_permission('accounting_transaction', '', 'delete')) {
                        $name .= ' | <a href="#" onclick="delete_convert('.$aRow['id'].', \'stock_import\'); return false;" class="text-danger">' . app_lang('delete') . '</a>';
                    }
                }
                
                $name .= '</div>';

                $row[] = $name;

                $row[] =  format_to_date($aRow['date_c']);

                $row[] = to_currency((float)$aRow['total_tax_money'],'');

                $row[] = to_currency((float)$aRow['total_goods_money'],'');

                $row[] = to_currency((float)$aRow['value_of_inventory'],'');

                $row[] = to_currency((float)$aRow['total_money'],'');

                if($aRow['approval'] == 1){
                    $row[] = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">'.app_lang('approved').'</span><span class="hide">, </span></span>&nbsp';
                }elseif($aRow['approval'] == 0){
                    $row[] = '<span class="label label-tag tag-id-1 label-tab2"><span class="tag">'.app_lang('not_yet_approve').'</span><span class="hide">, </span></span>&nbsp';
                }elseif($aRow['approval'] == -1){
                    $row[] = '<span class="label label-tag tag-id-1 label-tab3"><span class="tag">'.app_lang('reject').'</span><span class="hide">, </span></span>&nbsp';
                }

                $status_name = app_lang('has_not_been_converted');
                $label_class = 'default';

                if ($aRow['count_account_historys'] > 0) {
                    $label_class = 'success';
                    $status_name = app_lang('acc_converted');
                } 
                $row[] = '<span class="label label-' . $label_class . ' s-status stock-import-status-' . $aRow['id'] . '">' . $status_name . '</span>';
                
                $options = '';
                if($aRow['count_account_historys'] == 0 && has_permission('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date_c'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))){
                    $options = icon_btn('#', 'share', 'btn-success', [
                        'title' => app_lang('acc_convert'),
                        'data-id' =>$aRow['id'],
                        'data-type' => 'stock_import',
                        'onclick' => 'convert(this); return false;'
                    ]);
                }

                $row[] =  $options;

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * stock export table
     * @return json
     */
    public function stock_export_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $acc_closing_date = '';
            if(get_setting('acc_close_the_books') == 1){
                $acc_closing_date = get_setting('acc_closing_date');
            }
            $select = [
                '1',
                'goods_delivery_code',
                'customer_code',
                'date_add',
                'invoice_id',
                'approval',
                '(select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'goods_delivery.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "stock_export") as count_account_historys',
                'id',
            ];

            $where = [];

            if ($this->request->getPost('status')) {
                $status = $this->request->getPost('status');
                $where_status = '';
                foreach ($status as $key => $value) {
                    if($value == 'converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'goods_delivery.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "stock_export") > 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'goods_delivery.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "stock_export") > 0)';
                        }
                    }

                    if($value == 'has_not_been_converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'goods_delivery.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "stock_export") = 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'goods_delivery.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "stock_export") = 0)';
                        }
                    }
                }

                if($where_status != ''){
                    array_push($where, 'AND ('. $where_status . ')');
                }
            }
            
            $from_date = '';
            $to_date   = '';
            if ($this->request->getPost('from_date')) {
                $from_date = $this->request->getPost('from_date');
                if (!$this->Accounting_model->check_format_date($from_date)) {
                    $from_date = to_sql_date($from_date);
                }
            }

            if ($this->request->getPost('to_date')) {
                $to_date = $this->request->getPost('to_date');
                if (!$this->Accounting_model->check_format_date($to_date)) {
                    $to_date = to_sql_date($to_date);
                }
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'goods_delivery.date_c >= "' . $from_date . '" and ' . get_db_prefix() . 'goods_delivery.date_c <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'goods_delivery.date_c >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'goods_delivery.date_c <= "' . $to_date . '")');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'goods_delivery';
            $join         = [
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['date_add','date_c','goods_delivery_code','total_money']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';

                $name = '<a href="' . get_uri('warehouse/edit_delivery/' . $aRow['id'] ).'">' . $aRow['goods_delivery_code'] . '</a>';

                $name .= '<div class="row-options">';

                if ($aRow['count_account_historys'] == 0) {
                    if (has_permission('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date_c'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))) {
                        $name .= '<a href="#" onclick="convert(this); return false;" class="text-success" id="stock-export-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="stock_export">' . app_lang('acc_convert') . '</a>';
                    }
                }else{
                    if (has_permission('accounting_transaction', '', 'edit')) {
                        $name .= '<a href="#" onclick="convert(this); return false;" id="stock-export-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="stock_export">' . app_lang('edit') . '</a>';
                    }
                    if (has_permission('accounting_transaction', '', 'delete')) {
                        $name .= ' | <a href="#" onclick="delete_convert('.$aRow['id'].', \'stock_export\'); return false;" class="text-danger">' . app_lang('delete') . '</a>';
                    }
                }

                $name .= '</div>';

                $row[] = $name;

                $_data = '';
                if($aRow['customer_code']){
                    $this->db->where(get_db_prefix() . 'clients.userid', $aRow['customer_code']);
                    $client = $this->db->get(get_db_prefix() . 'clients')->row();
                    if($client){
                        $_data = $client->company;
                    }

                }

                $row[] = $_data;

                $row[] =  format_to_date($aRow['date_c']);

                $_data = '';

                if($aRow['invoice_id']){
                   $_data = get_invoice_id($aRow['invoice_id']).get_invoice_company_projecy($aRow['invoice_id']);
                }

                $row[] = $_data;

                if($aRow['approval'] == 1){
                    $row[] = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">'.app_lang('approved').'</span><span class="hide">, </span></span>&nbsp';
                }elseif($aRow['approval'] == 0){
                    $row[] = '<span class="label label-tag tag-id-1 label-tab2"><span class="tag">'.app_lang('not_yet_approve').'</span><span class="hide">, </span></span>&nbsp';
                }elseif($aRow['approval'] == -1){
                    $row[] = '<span class="label label-tag tag-id-1 label-tab3"><span class="tag">'.app_lang('reject').'</span><span class="hide">, </span></span>&nbsp';
                }

                $status_name = app_lang('has_not_been_converted');
                $label_class = 'default';

                if ($aRow['count_account_historys'] > 0) {
                    $label_class = 'success';
                    $status_name = app_lang('acc_converted');
                } 
                $row[] = '<span class="label label-' . $label_class . ' s-status stock-export-status-' . $aRow['id'] . '">' . $status_name . '</span>';
                
                $options = '';
                if($aRow['count_account_historys'] == 0 && has_permission('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date_c'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))){
                    $options = icon_btn('#', 'share', 'btn-success', [
                        'title' => app_lang('acc_convert'),
                        'data-id' =>$aRow['id'],
                        'data-type' => 'stock_export',
                        'onclick' => 'convert(this); return false;'
                    ]);
                }

                $row[] =  $options;

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * loss adjustment table
     * @return json
     */
    public function loss_adjustment_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();

            $time_filter = $this->request->getPost('time_filter');
            $date_create = $this->request->getPost('date_create');
            $type_filter = $this->request->getPost('type_filter');
            $status_filter = $this->request->getPost('status_filter');

            $acc_closing_date = '';
            if(get_setting('acc_close_the_books') == 1){
                $acc_closing_date = get_setting('acc_closing_date');
            }
            $select = [
                '1',
                'time',
                'type',
                'status',
                '(select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'wh_loss_adjustment.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "loss_adjustment") as count_account_historys',
                'id',
            ];

            $where = [];

            if ($this->request->getPost('status')) {
                $status = $this->request->getPost('status');
                $where_status = '';
                foreach ($status as $key => $value) {
                    if($value == 'converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'wh_loss_adjustment.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "loss_adjustment") > 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'wh_loss_adjustment.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "loss_adjustment") > 0)';
                        }
                    }

                    if($value == 'has_not_been_converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'wh_loss_adjustment.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "loss_adjustment") = 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'wh_loss_adjustment.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "loss_adjustment") = 0)';
                        }
                    }
                }

                if($where_status != ''){
                    array_push($where, 'AND ('. $where_status . ')');
                }
            }
            
            $from_date = '';
            $to_date   = '';
            if ($this->request->getPost('from_date')) {
                $from_date = $this->request->getPost('from_date');
                if (!$this->Accounting_model->check_format_date($from_date)) {
                    $from_date = to_sql_date($from_date);
                }
            }

            if ($this->request->getPost('to_date')) {
                $to_date = $this->request->getPost('to_date');
                if (!$this->Accounting_model->check_format_date($to_date)) {
                    $to_date = to_sql_date($to_date);
                }
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'wh_loss_adjustment.date_create >= "' . $from_date . '" and ' . get_db_prefix() . 'wh_loss_adjustment.date_create <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'wh_loss_adjustment.date_create >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'wh_loss_adjustment.date_create <= "' . $to_date . '")');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'wh_loss_adjustment';
            $join         = [
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';

                $name = app_lang($aRow['type']);
                $name .= '<div class="row-options">';

                if ($aRow['count_account_historys'] == 0) {
                    if (has_permission('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date_create'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))) {
                        $name .= '<a href="#" onclick="convert(this); return false;" class="text-success" id="loss-adjustment-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="loss_adjustment">' . app_lang('acc_convert') . '</a>';
                    }
                }else{
                    if (has_permission('accounting_transaction', '', 'edit')) {
                        $name .= '<a href="#" onclick="convert(this); return false;" id="loss-adjustment-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="loss_adjustment">' . app_lang('edit') . '</a>';
                    }
                    if (has_permission('accounting_transaction', '', 'delete')) {
                        $name .= ' | <a href="#" onclick="delete_convert('.$aRow['id'].', \'loss_adjustment\'); return false;" class="text-danger">' . app_lang('delete') . '</a>';
                    }
                }
                $name .= '</div>';
                $row[] = $name;

                $row[] = _dt($aRow['time']);

                $status = '';
                if ((int) $aRow['status'] == 0) {
                    $status = '<div class="btn btn-warning" >' . app_lang('draft') . '</div>';
                } elseif ((int) $aRow['status'] == 1) {
                    $status = '<div class="btn btn-success" >' . app_lang('Adjusted') . '</div>';
                } elseif((int) $aRow['status'] == -1){

                    $status = '<div class="btn btn-danger" >' . app_lang('reject') . '</div>';
                }

                $row[] = $status;

                $status_name = app_lang('has_not_been_converted');
                $label_class = 'default';

                if ($aRow['count_account_historys'] > 0) {
                    $label_class = 'success';
                    $status_name = app_lang('acc_converted');
                } 
                $row[] = '<span class="label label-' . $label_class . ' s-status stock-export-status-' . $aRow['id'] . '">' . $status_name . '</span>';
                
                $options = '';
                if($aRow['count_account_historys'] == 0 && has_permission('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date_create'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))){
                    $options = icon_btn('#', 'share', 'btn-success', [
                        'title' => app_lang('acc_convert'),
                        'data-id' =>$aRow['id'],
                        'data-type' => 'loss_adjustment',
                        'onclick' => 'convert(this); return false;'
                    ]);
                }

                $row[] =  $options;

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * update payslip automatic conversion
     */
    public function update_payslip_automatic_conversion(){
        if (!has_permission('accounting_setting', '', 'edit') && !is_admin()) {
            access_denied('accounting_setting');
        }
        $data = $this->request->getPost();
        $success = $this->Accounting_model->update_payslip_automatic_conversion($data);
        if($success == true){
            $message = sprintf(app_lang('updated_successfully'), app_lang('setting'));
            $this->session->setFlashdata("success_message", $message);
        }
        redirect(get_uri('accounting/setting?group=mapping_setup&tab=payslip'));
    }

    /**
     * opening stock table
     * @return json
     */
    public function opening_stock_table()
    {
        if ($this->input->is_ajax_request()) {
            $acc_first_month_of_financial_year = get_setting('acc_first_month_of_financial_year');

            $date_financial_year = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.date('Y')));

            $this->load->model('warehouse/warehouse_model');
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $acc_closing_date = '';
            if(get_setting('acc_close_the_books') == 1){
                $acc_closing_date = get_setting('acc_closing_date');
            }
            $select = [
                '1',
                'commodity_code',
                'description',
                'sku_code',
                '(select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'items.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "opening_stock" and ' . get_db_prefix() . 'acc_account_history.date >= "'.$date_financial_year.'") as count_account_historys',
                'id',
            ];

            $where = [];

            if ($this->request->getPost('status')) {
                $status = $this->request->getPost('status');
                $where_status = '';
                foreach ($status as $key => $value) {
                    if($value == 'converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'items.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "opening_stock" and ' . get_db_prefix() . 'acc_account_history.date >= "'.$date_financial_year.'") > 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'items.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "opening_stock" and ' . get_db_prefix() . 'acc_account_history.date >= "'.$date_financial_year.'") > 0)';
                        }
                    }

                    if($value == 'has_not_been_converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'items.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "opening_stock" and ' . get_db_prefix() . 'acc_account_history.date >= "'.$date_financial_year.'") = 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'items.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "opening_stock" and ' . get_db_prefix() . 'acc_account_history.date >= "'.$date_financial_year.'") = 0)';
                        }
                    }
                }

                if($where_status != ''){
                    array_push($where, 'AND ('. $where_status . ')');
                }
            }
            
            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'items';
            $join         = [
            ];

            $result = $this->Accounting_model->get_opening_stock_data_tables($aColumns, $sIndexColumn, $sTable, $join, $where, []);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';

                $code = '<a href="' . get_uri('warehouse/view_commodity_detail/' . $aRow['id']) . '">' . $aRow['commodity_code'] . '</a>';
                $code .= '<div class="row-options">';

                if ($aRow['count_account_historys'] == 0) {
                    if (has_permission('accounting_transaction', '', 'create') && ($acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))) {
                        $code .= '<a href="#" onclick="convert(this); return false;" class="text-success" id="opening-stock-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="opening_stock" data-amount="'.$aRow['opening_stock'].'">' . app_lang('acc_convert') . '</a>';
                    }
                }else{
                    if (has_permission('accounting_transaction', '', 'edit')) {
                        $code .= '<a href="#" onclick="convert(this); return false;" id="opening-stock-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="opening_stock" data-amount="'.$aRow['opening_stock'].'">' . app_lang('edit') . '</a>';
                    }
                    if (has_permission('accounting_transaction', '', 'delete')) {
                        $code .= ' | <a href="#" onclick="delete_convert('.$aRow['id'].', \'opening_stock\'); return false;" class="text-danger">' . app_lang('delete') . '</a>';
                    }
                }

                $code .= '</div>';

                $row[] = $code;

                $inventory = $this->warehouse_model->check_inventory_min($aRow['id']);

                if ($inventory) {
                    $row[] = '<a href="#" onclick="show_detail_item(this);return false;" data-name="' . $aRow['description'] . '"  data-commodity_id="' . $aRow['id'] . '"  >' . $aRow['description'] . '</a>';
                } else {

                    $row[] = '<a href="#" class="text-danger"  onclick="show_detail_item(this);return false;" data-name="' . $aRow['description'] . '" data-warehouse_id="' . $aRow['warehouse_id'] . '" data-commodity_id="' . $aRow['id'] . '"  >' . $aRow['description'] . '</a>';
                    
                }

                $row[] = '<span class="label label-tag tag-id-1"><span class="tag">' . $aRow['sku_code'] . '</span><span class="hide">, </span></span>&nbsp';
                $row[] = to_currency($aRow['opening_stock'], $currency->name);

                $status_name = app_lang('has_not_been_converted');
                $label_class = 'default';

                if ($aRow['count_account_historys'] > 0) {
                    $label_class = 'success';
                    $status_name = app_lang('acc_converted');
                } 
                $row[] = '<span class="label label-' . $label_class . ' s-status stock-export-status-' . $aRow['id'] . '">' . $status_name . '</span>';
                
                $options = '';
                if($aRow['count_account_historys'] == 0 && has_permission('accounting_transaction', '', 'create') && ($acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))){
                    $options = icon_btn('#', 'share', 'btn-success', [
                        'title' => app_lang('acc_convert'),
                        'data-id' =>$aRow['id'],
                        'data-type' => 'opening_stock',
                        'data-amount' => $aRow['opening_stock'],
                        'onclick' => 'convert(this); return false;'
                    ]);
                }

                $row[] =  $options;

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * update warehouse automatic conversion
     */
    public function update_warehouse_automatic_conversion(){
        if (!has_permission('accounting_setting', '', 'edit') && !is_admin()) {
            access_denied('accounting_setting');
        }
        $data = $this->request->getPost();
        $success = $this->Accounting_model->update_warehouse_automatic_conversion($data);
        if($success == true){
            $message = sprintf(app_lang('updated_successfully'), app_lang('setting'));
            $this->session->setFlashdata("success_message", $message);
        }
        redirect(get_uri('accounting/setting?group=mapping_setup&tab=warehouse'));
    }
    
    /**
     * purchase payment table
     * @return json
     */
    public function purchase_payment_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $acc_closing_date = '';
            if(get_setting('acc_close_the_books') == 1){
                $acc_closing_date = get_setting('acc_closing_date');
            }
            $select = [
                '1', // bulk actions
                get_db_prefix() . 'pur_invoice_payment.id as id',
                'amount',
                get_db_prefix() . 'payment_modes.name as name',
                get_db_prefix() . 'pur_invoices.pur_order',
                get_db_prefix() .'pur_invoice_payment.date as date',
                '(select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'pur_invoice_payment.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "purchase_payment") as count_account_historys'
            ];
            $where = [];
            
            if ($this->request->getPost('status')) {
                $status = $this->request->getPost('status');
                $where_status = '';
                foreach ($status as $key => $value) {
                    if($value == 'converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'pur_invoice_payment.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "purchase_payment") > 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'pur_invoice_payment.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "purchase_payment") > 0)';
                        }
                    }

                    if($value == 'has_not_been_converted'){
                        if($where_status != ''){
                            $where_status .= ' or ((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'pur_invoice_payment.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "purchase_payment") = 0)';
                        }else{
                            $where_status .= '((select count(*) from ' . get_db_prefix() . 'acc_account_history where ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'pur_invoice_payment.id and ' . get_db_prefix() . 'acc_account_history.rel_type = "purchase_payment") = 0)';
                        }
                    }
                }

                if($where_status != ''){
                    array_push($where, 'AND ('. $where_status . ')');
                }
            }

            $from_date = '';
            $to_date   = '';
            if ($this->request->getPost('from_date')) {
                $from_date = $this->request->getPost('from_date');
                if (!$this->Accounting_model->check_format_date($from_date)) {
                    $from_date = to_sql_date($from_date);
                }
            }

            if ($this->request->getPost('to_date')) {
                $to_date = $this->request->getPost('to_date');
                if (!$this->Accounting_model->check_format_date($to_date)) {
                    $to_date = to_sql_date($to_date);
                }
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'pur_invoice_payment.date >= "' . $from_date . '" and ' . get_db_prefix() . 'pur_invoice_payment.date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'pur_invoice_payment.date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'pur_invoice_payment.date <= "' . $to_date . '")');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'pur_invoice_payment';
            $join         = ['LEFT JOIN ' . get_db_prefix() . 'payment_modes ON ' . get_db_prefix() . 'payment_modes.id = ' . get_db_prefix() . 'pur_invoice_payment.paymentmode',
                            'LEFT JOIN ' . get_db_prefix() . 'acc_account_history ON ' . get_db_prefix() . 'acc_account_history.rel_id = ' . get_db_prefix() . 'pur_invoice_payment.id and ' . get_db_prefix() . 'acc_account_history.rel_id = "purchase_payment"',
                            'LEFT JOIN ' . get_db_prefix() . 'pur_invoices ON ' . get_db_prefix() . 'pur_invoices.id = ' . get_db_prefix() . 'pur_invoice_payment.pur_invoice',
                        ];

            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['paymentmode', get_db_prefix() . 'pur_invoices.pur_order']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';

                $categoryOutput = format_to_date($aRow['date']);

                $categoryOutput .= '<div class="row-options">';
                if ($aRow['count_account_historys'] == 0) {
                    if (has_permission('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))) {
                        $categoryOutput .= '<a href="#" onclick="convert(this); return false;" class="text-success" id="purchase-payment-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="purchase_payment" data-amount="'.$aRow['amount'].'">' . app_lang('acc_convert') . '</a>';
                    }
                }else{
                    if (has_permission('accounting_transaction', '', 'edit')) {
                        $categoryOutput .= '<a href="#" onclick="convert(this); return false;" id="purchase-payment-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="purchase_payment" data-amount="'.$aRow['amount'].'">' . app_lang('edit') . '</a>';
                    }
                    if (has_permission('accounting_transaction', '', 'delete')) {
                        $categoryOutput .= ' | <a href="#" onclick="delete_convert('.$aRow['id'].', \'purchase_payment\'); return false;" class="text-danger">' . app_lang('delete') . '</a>';
                    }
                }



                $categoryOutput .= '</div>';
                $row[] = $categoryOutput;

                $row[] = to_currency($aRow['amount'], $currency->name);

                $row[] = $aRow['name'];

                $row[] = '<a href="'.get_uri('purchase/purchase_order/'.$aRow[get_db_prefix().'pur_invoices.pur_order']).'">'.get_pur_order_subject($aRow[ get_db_prefix().'pur_invoices.pur_order']).'</a>';

                $status_name = app_lang('has_not_been_converted');

                $label_class = 'default';

                if ($aRow['count_account_historys'] > 0) {
                    $label_class = 'success';
                    $status_name = app_lang('acc_converted');
                } 

                $row[] = '<span class="label label-' . $label_class . ' s-status payment-status-' . $aRow['id'] . '">' . $status_name . '</span>';

                $options = '';
                if($aRow['count_account_historys'] == 0 && has_permission('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))){
                    $options = icon_btn('#', 'share', 'btn-success', [
                        'title' => app_lang('acc_convert'),
                        'data-id' =>$aRow['id'],
                        'data-amount' => $aRow['amount'],
                        'data-type' => 'purchase_payment',
                        'onclick' => 'convert(this); return false;'
                    ]);
                }

                $row[] =  $options;

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * update purchase automatic conversion
     */
    public function update_purchase_automatic_conversion(){
        if (!has_permission('accounting_setting', '', 'edit') && !is_admin()) {
            access_denied('accounting_setting');
        }
        $data = $this->request->getPost();
        $success = $this->Accounting_model->update_purchase_automatic_conversion($data);
        if($success == true){
            $message = sprintf(app_lang('updated_successfully'), app_lang('setting'));
            $this->session->setFlashdata("success_message", $message);
        }
        redirect(get_uri('accounting/setting?group=mapping_setup&tab=purchase'));
    }

    /**
     * Budget
     * @return view
     */
    public function budget(){
        if ($this->request->getPost()) {
            $data = $this->request->getPost();
            $message = '';

            if (!has_permission('accounting_budget', '', 'edit')) {
                access_denied('accounting_budget');
            }

            $success = $this->Accounting_model->update_budget_detail($data);
            if ($success) {
                $message = sprintf(app_lang('updated_successfully'), app_lang('budget'));
            }

            echo json_encode([
                'success' => $success,
                'message' => $message,
            ]);
            die();
        }
        if (!has_permission('accounting_budget', '', 'view')) {
            access_denied('budget');
        }

        $data['budgets'] = $this->Accounting_model->get_budgets();

        $budgets_dropdown = [];
        foreach ($data['budgets'] as $key => $value) {
            $budgets_dropdown[$value['id']] = $value['name'];
        }

        $data['budgets_dropdown'] = $budgets_dropdown;

        if(count($data['budgets']) > 0){
            $data_fill = [];
            $data_fill['budget'] = $data['budgets'][0]['id'];
            $data_fill['view_type'] = 'monthly';

            $data['nestedheaders'] = $this->Accounting_model->get_nestedheaders_budget($data['budgets'][0]['id'], 'monthly');
            $data['columns'] = $this->Accounting_model->get_columns_budget($data['budgets'][0]['id'], 'monthly');
            $data['data_budget'] = $this->Accounting_model->get_data_budget($data_fill);
        }else{
            $data['nestedheaders'] = [];
            $data['columns'] = [];
            $data['data_budget'] =[];
            $data['hide_handson'] = 'true';
        }

        $data['title'] = app_lang('budget');

        return $this->template->rander('Accounting\Views\budget/manage', $data);

    }

    /**
     * Gets the data budget.
     * @return json data budget
     */
    public function get_data_budget() {
        $data = $this->request->getPost();
        
        $data_budget = $this->Accounting_model->get_data_budget($data);
        $nestedheaders = $this->Accounting_model->get_nestedheaders_budget($data['budget'], $data['view_type']);
        $columns = $this->Accounting_model->get_columns_budget($data['budget'], $data['view_type']);
        echo json_encode([
            'columns' => $columns,
            'nestedheaders' => $nestedheaders,
            'data_budget' => $data_budget,
        ]);
        die();
    }

     /**
     * Add budget.
     * @return json data budget
     */
    public function add_budget() {
        $data = $this->request->getPost();

        $budget = $this->Accounting_model->add_budget($data);
        $budget_id = '';
        $success = false;
        $message = app_lang('add_failure');
        $name = $data['year'].' - '. app_lang($data['type']);

        if($budget){
            $message = sprintf(app_lang('added_successfully'), app_lang('acc_account'));
            $success = true;
            $budget_id = $budget;
        }
        echo json_encode([
            'name' => $name,
            'id' => $budget_id,
            'success' => $success,
            'message' => $message
        ]);
        die();
    }

     /**
     * check budget.
     * @return json data budget
     */
    public function check_budget() {
        $data = $this->request->getPost();

        $success = $this->Accounting_model->check_budget($data);

        echo json_encode([
            'success' => $success,
        ]);
        die();
    }

    /**
     * update budget.
     * @return json data budget
     */
     public function update_budget() {
        $data = $this->request->getPost();
        $success = false;
        if (isset($data['budget'])) {
            $id = $data['budget'];
            unset($data['budget']);
            
            $success = $this->Accounting_model->update_budget($data, $id);
        }

        echo json_encode([
            'success' => $success,
        ]);
        die();
     }

     /**
     * reconcile restored
     * @param  [type] $account 
     * @param  [type] $company 
     * @return [type]          
     */
    public function reconcile_restored($account) {
        $success = false;
        $message = app_lang('acc_restored_failure');
        $hide_restored = true;
        
        $reconcile_restored = $this->Accounting_model->reconcile_restored($account);
        if($reconcile_restored){
            $success = true;
            $message = app_lang('acc_restored_successfully');
        }

        $check_reconcile_restored = $this->Accounting_model->check_reconcile_restored($account);
        if($check_reconcile_restored){
            $hide_restored = false;
        }

        $closing_date = false;
        $reconcile = $this->Accounting_model->get_reconcile_by_account($account);

        if ($reconcile) {
            if(get_setting('acc_close_the_books') == 1){
                $closing_date = (strtotime(get_setting('acc_closing_date')) > strtotime(date('Y-m-d'))) ? true : false;
            }
        }

        echo json_encode([
            'success' => $success,
            'hide_restored' => $hide_restored,
            'closing_date' => $closing_date,
            'message' => $message,
        ]);
        die();
    }

    /**
     * report Accounts receivable ageing detail
     * @return view
     */
    public function rp_accounts_receivable_ageing_detail() {
        $data['title'] = app_lang('accounts_receivable_ageing_detail');
        $data['from_date'] = date('Y-m-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/accounts_receivable_ageing_detail', $data);
    }

    /**
     * report Accounts payable ageing detail
     * @return view
     */
    public function rp_accounts_payable_ageing_detail() {
        $data['title'] = app_lang('accounts_payable_ageing_detail');
        $data['from_date'] = date('Y-m-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/accounts_payable_ageing_detail', $data);
    }

    /**
     * report Accounts receivable ageing summary
     * @return view
     */
    public function rp_accounts_receivable_ageing_summary() {
        $data['title'] = app_lang('accounts_receivable_ageing_summary');
        $data['from_date'] = date('Y-m-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/accounts_receivable_ageing_summary', $data);
    }

    /**
     * report Accounts payable ageing summary
     * @return view
     */
    public function rp_accounts_payable_ageing_summary() {
        $data['title'] = app_lang('accounts_payable_ageing_summary');
        $data['from_date'] = date('Y-m-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/accounts_payable_ageing_summary', $data);
    }

    /**
     * report profit and loss trailing 12 months
     * @return view
     */
    public function rp_profit_and_loss_12_months() {
        $data['title'] = app_lang('profit_and_loss_12_months');
        $acc_first_month_of_financial_year = get_setting('acc_first_month_of_financial_year');

        $data['from_date'] = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.date('Y')));
        $data['to_date'] = date('Y-m-t', strtotime($data['from_date'] . '  - 1 month + 1 year '));

        $data['accounting_method'] = get_setting('acc_accounting_method');
        return $this->template->rander('Accounting\Views\report/includes/profit_and_loss_12_months', $data);
    }

    /**
     * report budget overview
     * @return view
     */
    public function rp_budget_overview() {
        $data['title'] = app_lang('budget_overview');
        $acc_first_month_of_financial_year = get_setting('acc_first_month_of_financial_year');

        $data['from_date'] = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.date('Y')));
        $data['to_date'] = date('Y-m-t', strtotime($data['from_date'] . '  - 1 month + 1 year '));

        $data['accounting_method'] = get_setting('acc_accounting_method');
        $data['budgets'] = $this->Accounting_model->get_budgets();

        return $this->template->rander('Accounting\Views\report/includes/budget_overview', $data);
    }

    /**
     * rp profit and loss budget performance
     */
    public function rp_profit_and_loss_budget_performance(){
        $data['title'] = app_lang('profit_and_loss_budget_performance');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        $data['budgets'] = $this->Accounting_model->get_budgets('', 'type = "profit_and_loss_accounts"');

        return $this->template->rander('Accounting\Views\report/includes/profit_and_loss_budget_performance', $data);
    }

    /**
     * profit and loss budget vs actual
     */
    public function rp_profit_and_loss_budget_vs_actual(){
        $data['title'] = app_lang('profit_and_loss_budget_vs_actual');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        $data['accounting_method'] = get_setting('acc_accounting_method');
        $data['budgets'] = $this->Accounting_model->get_budgets('', 'type = "profit_and_loss_accounts"');
        
        return $this->template->rander('Accounting\Views\report/includes/profit_and_loss_budget_vs_actual', $data);
    }

    /**
     * delete budget
     * @param  integer $id
     * @return
     */
    public function delete_budget($id)
    {
        if (!has_permission('accounting_budget', '', 'delete')) {
            access_denied('accounting_budget');
        }
        $success = $this->Accounting_model->delete_budget($id);
        $message = '';
        if ($success) {
            $message = sprintf(app_lang('deleted'), app_lang('budget'));
        } else {
            $message = app_lang('can_not_delete');
        }

        echo json_encode(['success' => $success, 'message' => $message]);
        die();
    }

    /**
     * { accounts import }
     */
    public function accounts_import(){
        if (!has_permission('accounting_chart_of_accounts', '', 'create')) {
            access_denied('chart_of_accounts');
        }

        $this->load->model('staff_model');
        $data_staff = $this->staff_model->get($this->login_user->id);

        /*get language active*/
        if ($data_staff) {
            if ($data_staff->default_language != '') {
                $data['active_language'] = $data_staff->default_language;

            } else {

                $data['active_language'] = get_setting('active_language');
            }

        } else {
            $data['active_language'] = get_setting('active_language');
        }
        $data['title'] = app_lang('import_excel');

        $this->load->view('chart_of_accounts/import_excel', $data);
    }

    /**
     * import file xlsx banking
     * @return json
     */
    public function import_file_xlsx_account() {
        if (!class_exists('XLSXReader_fin')) {
            require_once module_dir_path(ACCOUNTING_MODULE_NAME) . 'assets/plugins/XLSXReader/XLSXReader.php';
        }
        require_once module_dir_path(ACCOUNTING_MODULE_NAME) . 'assets/plugins/XLSXWriter/xlsxwriter.class.php';

        $filename = '';
        $account_types = $this->Accounting_model->get_account_types();
        if ($this->request->getPost()) {
            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                $this->delete_error_file_day_before(1, ACCOUTING_IMPORT_ITEM_ERROR);

                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $rows = [];
                    $arr_insert = [];

                    $tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 0755);
                    }

                    if (!file_exists($tmpDir)) {
                        mkdir($tmpDir, 0755);
                    }

                    // Setup our new file path
                    $newFilePath = $tmpDir . $_FILES['file_csv']['name'];

                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {

                        $accounts = $this->Accounting_model->get_accounts();

                        $account_name = [];
                        foreach($accounts as $account){
                            $_name = '';
                            if ($account['name'] == '') {
                                $_name .= app_lang($account['key_name']);
                            } else {
                                $_name .= $account['name'];
                            }
                            $account_name[trim($_name)] = $account['id'];
                        }


                        //Writer file
                        $writer_header = array(
                            app_lang('type') => 'string',
                            app_lang('sub_type') => 'string',
                            app_lang('account_code') => 'string',
                            app_lang('account_name') => 'string',
                            app_lang('sub_account_of') => 'string',
                            app_lang('error') => 'string',
                        );

                        $rowstyle[] = array('widths' => [10, 20, 30, 40]);

                        $writer = new \XLSXWriter();
                        $writer->writeSheetHeader('Sheet1', $writer_header, $col_options = ['widths' => [40, 40, 40, 40, 50, 50]]);

                        //Reader file
                        $xlsx = new \XLSXReader_fin($newFilePath);
                        $sheetNames = $xlsx->getSheetNames();
                        $data = $xlsx->getSheetData(array_shift($sheetNames));

                        $arr_header = [];

                        $arr_header['type'] = 0;
                        $arr_header['sub_type'] = 1;
                        $arr_header['account_code'] = 2;
                        $arr_header['account_name'] = 3;
                        $arr_header['sub_account_of'] = 4;

                        $total_rows = 0;
                        $total_row_false = 0;

                        $check_arr = [];
                        $check_arr_account_name = [];

                        for($row_check = 1; $row_check < count($data); $row_check++){
                            $sub_account_of = isset($data[$row_check][$arr_header['sub_account_of']]) ? $data[$row_check][$arr_header['sub_account_of']] : '';

                            if((is_null($sub_account_of) == true || $sub_account_of == '') && isset($data[$row_check][$arr_header['account_name']])){
                                $check_arr[] = $data[$row_check];
                                $check_arr_account_name[] = $data[$row_check][$arr_header['account_name']];
                            }
                        }


                        for ($row = 1; $row < count($data); $row++) {

                            $total_rows++;

                            $rd = array();
                            $flag = 0;
                            $flag2 = 0;

                            $string_error = '';
                            $flag_position_group;
                            $flag_department = null;

                            $value_type = isset($data[$row][$arr_header['type']]) ? $data[$row][$arr_header['type']] : '';
                            $value_sub_type = isset($data[$row][$arr_header['sub_type']]) ? $data[$row][$arr_header['sub_type']] : '';
                            $value_account_code = isset($data[$row][$arr_header['account_code']]) ? $data[$row][$arr_header['account_code']] : '';
                            $value_account_name = isset($data[$row][$arr_header['account_name']]) ? $data[$row][$arr_header['account_name']] : '';
                            $value_sub_account_of = isset($data[$row][$arr_header['sub_account_of']]) ? $data[$row][$arr_header['sub_account_of']] : '';

                            $reg_day = '/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/'; /*yyyy-mm-dd*/

                            if (is_null($value_type) != true) {
                                if(is_numeric($value_type)){
                                    if(get_account_type_by_id($value_type) == false){
                                        $string_error .= app_lang('type') .' '. app_lang('invalid').' ';
                                        $flag = 1;
                                    }else{
                                        $value_type = get_account_type_by_id($value_type);
                                    }
                                }else{
                                    if(get_account_type_by_name($value_type) == false){
                                        $string_error .= app_lang('type') .' '. app_lang('invalid').' ';
                                        $flag = 1;
                                    }else{
                                        $value_type = get_account_type_by_name($value_type);
                                    }
                                }
                            }

                            if (is_null($value_sub_type) != true) {
                                if(is_numeric($value_sub_type)){
                                    if(get_account_sub_type_by_id($value_sub_type) == false){
                                        $string_error .= app_lang('sub_type') .' '. app_lang('invalid').' ';
                                        $flag = 1;
                                    }else{
                                        $value_sub_type = get_account_sub_type_by_id($value_sub_type);
                                    }
                                }else{
                                    if(get_account_sub_type_by_name($value_sub_type) == false){
                                        $string_error .= app_lang('sub_type') .' '. app_lang('invalid').' ';
                                        $flag = 1;
                                    }else{
                                        $value_sub_type = get_account_sub_type_by_name($value_sub_type);
                                    }
                                }
                            }

                            if (is_null($value_account_name) == true || $value_account_name == '') {
                                $string_error .= app_lang('account_name') .' '. app_lang('not_yet_entered').' ';
                                $flag = 1;
                            }

                            if (is_null($value_sub_account_of) == false && $value_sub_account_of != '') {
                                if(!in_array($value_sub_account_of, $check_arr_account_name)){
                                    if(is_numeric($value_sub_account_of)){
                                        if(get_account_by_id($value_sub_account_of) == false){
                                            $string_error .= app_lang('sub_account_of') .' '. app_lang('invalid').' ';
                                            $flag = 1;
                                        }else{
                                            $value_sub_account_of = get_account_by_id($value_sub_account_of);
                                        }
                                    }else{
                                        if(!array_key_exists($value_sub_account_of, $account_name)){
                                            if($string_error != ''){
                                                $string_error .= ', ';
                                            }
                                            $string_error .= app_lang('sub_account_of') .' '. app_lang('invalid');
                                            $flag = 1;
                                        }else{
                                            $value_sub_account_of = $account_name[$value_sub_account_of];
                                        }
                                    }
                                }
                            }

                            if (($flag == 1) || $flag2 == 1) {
                                //write error file
                                $writer->writeSheetRow('Sheet1', [
                                    $value_type,
                                    $value_sub_type,
                                    $value_account_code,
                                    $value_account_name,
                                    $value_sub_account_of,
                                    $string_error,
                                ]);

                           
                                $total_row_false++;
                            }

                            if ($flag == 0 && $flag2 == 0) {

                                $rd['account_type_id'] = $value_type;
                                $rd['account_detail_type_id'] = $value_sub_type;
                                $rd['number'] = $value_account_code;
                                $rd['name'] = $value_account_name;
                                $rd['parent_account'] = $value_sub_account_of;
                                $rd['active'] = 1;

                                $rows[] = $rd;
                                array_push($arr_insert, $rd);

                            }

                        }

                        //insert batch
                        if (count($arr_insert) > 0) {
                            $this->Accounting_model->insert_batch_account($arr_insert);
                        }

                        $total_rows = $total_rows;
                        $total_row_success = isset($rows) ? count($rows) : 0;
                        $dataerror = '';
                        $message = 'Not enought rows for importing';

                        if ($total_row_false != 0) {
                            $filename = 'Import_account_error_' . $this->login_user->id . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
                            $writer->writeToFile(str_replace($filename, ACCOUTING_IMPORT_ITEM_ERROR . $filename, $filename));
                        }

                    }
                }
            }
        }

        if (file_exists($newFilePath)) {
            @unlink($newFilePath);
        }

        echo json_encode([
            'message' => $message,
            'total_row_success' => $total_row_success,
            'total_row_false' => $total_row_false,
            'total_rows' => $total_rows,
            'site_url' => site_url(),
            'staff_id' => $this->login_user->id,
            'filename' => ACCOUTING_IMPORT_ITEM_ERROR . $filename,
        ]);
    }

    /**
     * { budget import }
     */
    public function budget_import(){
        if (!has_permission('accounting_budget', '', 'create')) {
            access_denied('accounting_budget');
        }

        $data['active_language'] = get_setting("user_" . $this->login_user->id . "_personal_language");
        $data['title'] = app_lang('import_excel');

        return $this->template->rander('Accounting\Views\budget/import_excel', $data);
    }

    /**
     * import file xlsx banking
     * @return json
     */
    public function import_file_xlsx_budget() {
        if (!class_exists('XLSXReader_fin')) {
            require_once PLUGINPATH . 'Accounting/assets/plugins/XLSXReader/XLSXReader.php';
        }

        if(!class_exists('XLSXWriter')){
            require_once(PLUGINPATH. 'Accounting/assets/plugins/XLSXWriter/xlsxwriter.class.php');             
        }


        $filename = '';

        if ($this->request->getPost()) {
            $year = $this->request->getPost('year');
            $type = $this->request->getPost('type');
            $name = $year.' - '. app_lang($type);

            $import_type = $this->request->getPost('import_type');

            $accounts = $this->Accounting_model->get_accounts();

            $data_return = [];

            $account_name = [];
            foreach($accounts as $account){
                $_name = '';
                if ($account['name'] == '') {
                    $_name .= app_lang($account['key_name']);
                } else {
                    $_name .= $account['name'];
                }
                $account_name[trim($_name)] = $account['id'];
            }

            $db = db_connect('default');
            $db_builder = $db->table(get_db_prefix() . 'acc_budgets');
            $db_builder->where('year', $year);
            $db_builder->where('type', $type);
            $budget = $db_builder->get()->getRow();

            if($budget){
                if($name != $budget->name){
                    $db_builder->where('id', $budget->id);
                    $db_builder->update(['name' => $name]);
                }

                $budget_id = $budget->id;
            }else{
                $db_builder->insert(['name' => $name, 'year' => $year, 'type' => $type]);
                $budget_id = $db->insertID();
            }

            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                $this->delete_error_file_day_before(1, ACCOUTING_IMPORT_ITEM_ERROR);

                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $rows = [];
                    $arr_insert = [];

                    $tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 0755);
                    }

                    if (!file_exists($tmpDir)) {
                        mkdir($tmpDir, 0755);
                    }

                    // Setup our new file path
                    $newFilePath = $tmpDir . $_FILES['file_csv']['name'];

                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        //Writer file
                        
                        if($import_type == 'month'){
                            $writer_header = array(
                                app_lang('acc_account') => 'string',
                                app_lang('acc_month_1') => 'string',
                                app_lang('acc_month_2') => 'string',
                                app_lang('acc_month_3') => 'string',
                                app_lang('acc_month_4') => 'string',
                                app_lang('acc_month_5') => 'string',
                                app_lang('acc_month_6') => 'string',
                                app_lang('acc_month_7') => 'string',
                                app_lang('acc_month_8') => 'string',
                                app_lang('acc_month_9') => 'string',
                                app_lang('acc_month_10') => 'string',
                                app_lang('acc_month_11') => 'string',
                                app_lang('acc_month_12') => 'string',
                                app_lang('error') => 'string',
                            );
                        }elseif ($import_type == 'quarter') {
                            $writer_header = array(
                                app_lang('acc_account') => 'string',
                                app_lang('quarter').' 1' => 'string',
                                app_lang('quarter').' 2' => 'string',
                                app_lang('quarter').' 3' => 'string',
                                app_lang('quarter').' 4' => 'string',
                                app_lang('error') => 'string',
                            );
                        }else{
                            $writer_header = array(
                                app_lang('acc_account') => 'string',
                                app_lang('acc_amount') => 'string',
                                app_lang('error') => 'string',
                            );
                        }


                        $rowstyle[] = array('widths' => [10, 20, 30, 40]);

                        $writer = new \XLSXWriter();
                        $writer->writeSheetHeader('Sheet1', $writer_header, $col_options = ['widths' => [40, 40, 40, 40, 40, 40, 40, 40, 40, 40, 40, 40, 40]]);

                        //Reader file
                        $xlsx = new \XLSXReader_fin($newFilePath);
                        $sheetNames = $xlsx->getSheetNames();
                        $data = $xlsx->getSheetData($sheetNames[1]);

                        $arr_header = [];

                        if($import_type == 'month'){
                            $arr_header['account'] = 0;
                            $arr_header['month_1'] = 1;
                            $arr_header['month_2'] = 2;
                            $arr_header['month_3'] = 3;
                            $arr_header['month_4'] = 4;
                            $arr_header['month_5'] = 5;
                            $arr_header['month_6'] = 6;
                            $arr_header['month_7'] = 7;
                            $arr_header['month_8'] = 8;
                            $arr_header['month_9'] = 9;
                            $arr_header['month_10'] = 10;
                            $arr_header['month_11'] = 11;
                            $arr_header['month_12'] = 12;
                        }elseif ($import_type == 'quarter') {
                            $arr_header['account'] = 0;
                            $arr_header['quarter_1'] = 1;
                            $arr_header['quarter_2'] = 2;
                            $arr_header['quarter_3'] = 3;
                            $arr_header['quarter_4'] = 4;
                        }else{
                            $arr_header['account'] = 0;
                            $arr_header['amount'] = 1;
                        }


                        $total_rows = 0;
                        $total_row_false = 0;

                        for ($row = 1; $row < count($data); $row++) {

                            $total_rows++;

                            $rd = array();
                            $flag = 0;
                            $flag2 = 0;

                            $string_error = '';


                            if($import_type == 'month'){
                                $value_account = isset($data[$row][$arr_header['account']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['account']])) : '';
                                $value_month_1 = isset($data[$row][$arr_header['month_1']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['month_1']])) : '';
                                $value_month_2 = isset($data[$row][$arr_header['month_2']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['month_2']])) : '';
                                $value_month_3 = isset($data[$row][$arr_header['month_3']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['month_3']])) : '';
                                $value_month_4 = isset($data[$row][$arr_header['month_4']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['month_4']])) : '';
                                $value_month_5 = isset($data[$row][$arr_header['month_5']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['month_5']])) : '';
                                $value_month_6 = isset($data[$row][$arr_header['month_6']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['month_6']])) : '';
                                $value_month_7 = isset($data[$row][$arr_header['month_7']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['month_7']])) : '';
                                $value_month_8 = isset($data[$row][$arr_header['month_8']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['month_8']])) : '';
                                $value_month_9 = isset($data[$row][$arr_header['month_9']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['month_9']])) : '';
                                $value_month_10 = isset($data[$row][$arr_header['month_10']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['month_10']])) : '';
                                $value_month_11 = isset($data[$row][$arr_header['month_11']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['month_11']])) : '';
                                $value_month_12 = isset($data[$row][$arr_header['month_12']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['month_12']])) : '';
                            }elseif ($import_type == 'quarter') {
                                $value_account = isset($data[$row][$arr_header['account']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['account']])) : '';
                                $value_quarter_1 = isset($data[$row][$arr_header['quarter_1']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['quarter_1']])) : '';
                                $value_quarter_2 = isset($data[$row][$arr_header['quarter_2']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['quarter_2']])) : '';
                                $value_quarter_3 = isset($data[$row][$arr_header['quarter_3']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['quarter_3']])) : '';
                                $value_quarter_4 = isset($data[$row][$arr_header['quarter_4']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['quarter_4']])) : '';
                            }else{
                                $value_account = isset($data[$row][$arr_header['account']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['account']])) : '';
                                $value_amount = isset($data[$row][$arr_header['amount']]) ? trim(str_replace(' ',' ',$data[$row][$arr_header['amount']])) : '';
                            }

                        
                            if(is_null($value_account) == true || $value_account == ''){
                                if($string_error != ''){
                                    $string_error .= ', ';
                                }
                                $string_error .= app_lang('acc_account') .' '. app_lang('not_yet_entered');
                                $flag = 1;
                            }else {
                                if(is_numeric($value_account)){
                                    if(get_account_by_id($value_account) == false){
                                        if($string_error != ''){
                                            $string_error .= ', ';
                                        }
                                        $string_error .= app_lang('acc_account') .' '. app_lang('invalid');
                                        $flag = 1;
                                    }else{
                                        $value_account = get_account_by_id($value_account);
                                    }
                                }else{
                                    
                                    if(!array_key_exists($value_account, $account_name)){
                                        if($string_error != ''){
                                            $string_error .= ', ';
                                        }
                                        $string_error .= app_lang('acc_account') .' '. app_lang('invalid');
                                        $flag = 1;
                                    }else{
                                        $value_account = $account_name[$value_account];
                                    }
                                }
                            }

                            if($import_type == 'month'){
                                if((is_null($value_month_1) || $value_month_1 == '')){
                                    if($string_error != ''){
                                        $string_error .= ', ';
                                    }
                                    $string_error .= app_lang('acc_month_1') .' '. app_lang('not_yet_entered');
                                    $flag = 1;
                                }else{
                                    if(!is_numeric($value_month_1)){
                                        if($string_error != ''){
                                            $string_error .= ', ';
                                        }
                                        $string_error .= app_lang('acc_month_1') .' '. app_lang('invalid');
                                        $flag = 1;
                                    }
                                }

                                if((is_null($value_month_2) || $value_month_2 == '')){
                                    if($string_error != ''){
                                        $string_error .= ', ';
                                    }
                                    $string_error .= app_lang('acc_month_2') .' '. app_lang('not_yet_entered');
                                    $flag = 1;
                                }else{
                                    if(!is_numeric($value_month_2)){
                                        if($string_error != ''){
                                            $string_error .= ', ';
                                        }
                                        $string_error .= app_lang('acc_month_2') .' '. app_lang('invalid');
                                        $flag = 1;
                                    }
                                }

                                if((is_null($value_month_3) || $value_month_3 == '')){
                                    if($string_error != ''){
                                        $string_error .= ', ';
                                    }
                                    $string_error .= app_lang('acc_month_3') .' '. app_lang('not_yet_entered');
                                    $flag = 1;
                                }else{
                                    if(!is_numeric($value_month_3)){
                                        if($string_error != ''){
                                            $string_error .= ', ';
                                        }
                                        $string_error .= app_lang('acc_month_3') .' '. app_lang('invalid');
                                        $flag = 1;
                                    }
                                }

                                if((is_null($value_month_4) || $value_month_4 == '')){
                                    if($string_error != ''){
                                        $string_error .= ', ';
                                    }
                                    $string_error .= app_lang('acc_month_4') .' '. app_lang('not_yet_entered');
                                    $flag = 1;
                                }else{
                                    if(!is_numeric($value_month_4)){
                                        if($string_error != ''){
                                            $string_error .= ', ';
                                        }
                                        $string_error .= app_lang('acc_month_4') .' '. app_lang('invalid');
                                        $flag = 1;
                                    }
                                }

                                if((is_null($value_month_5) || $value_month_5 == '')){
                                    if($string_error != ''){
                                        $string_error .= ', ';
                                    }
                                    $string_error .= app_lang('acc_month_5') .' '. app_lang('not_yet_entered');
                                    $flag = 1;
                                }else{
                                    if(!is_numeric($value_month_5)){
                                        if($string_error != ''){
                                            $string_error .= ', ';
                                        }
                                        $string_error .= app_lang('acc_month_5') .' '. app_lang('invalid');
                                        $flag = 1;
                                    }
                                }

                                if((is_null($value_month_6) || $value_month_6 == '')){
                                    if($string_error != ''){
                                        $string_error .= ', ';
                                    }
                                    $string_error .= app_lang('acc_month_6') .' '. app_lang('not_yet_entered');
                                    $flag = 1;
                                }else{
                                    if(!is_numeric($value_month_6)){
                                        if($string_error != ''){
                                            $string_error .= ', ';
                                        }
                                        $string_error .= app_lang('acc_month_6') .' '. app_lang('invalid');
                                        $flag = 1;
                                    }
                                }

                                if((is_null($value_month_7) || $value_month_7 == '')){
                                    if($string_error != ''){
                                        $string_error .= ', ';
                                    }
                                    $string_error .= app_lang('acc_month_7') .' '. app_lang('not_yet_entered');
                                    $flag = 1;
                                }else{
                                    if(!is_numeric($value_month_7)){
                                        if($string_error != ''){
                                            $string_error .= ', ';
                                        }
                                        $string_error .= app_lang('acc_month_7') .' '. app_lang('invalid');
                                        $flag = 1;
                                    }
                                }

                                if((is_null($value_month_8) || $value_month_8 == '')){
                                    if($string_error != ''){
                                        $string_error .= ', ';
                                    }
                                    $string_error .= app_lang('acc_month_8') .' '. app_lang('not_yet_entered');
                                    $flag = 1;
                                }else{
                                    if(!is_numeric($value_month_8)){
                                        if($string_error != ''){
                                            $string_error .= ', ';
                                        }
                                        $string_error .= app_lang('acc_month_8') .' '. app_lang('invalid');
                                        $flag = 1;
                                    }
                                }

                                if((is_null($value_month_9) || $value_month_9 == '')){
                                    if($string_error != ''){
                                        $string_error .= ', ';
                                    }
                                    $string_error .= app_lang('acc_month_9') .' '. app_lang('not_yet_entered');
                                    $flag = 1;
                                }else{
                                    if(!is_numeric($value_month_9)){
                                        if($string_error != ''){
                                            $string_error .= ', ';
                                        }
                                        $string_error .= app_lang('acc_month_9') .' '. app_lang('invalid');
                                        $flag = 1;
                                    }
                                }

                                if((is_null($value_month_10) || $value_month_10 == '')){
                                    if($string_error != ''){
                                        $string_error .= ', ';
                                    }
                                    $string_error .= app_lang('acc_month_10') .' '. app_lang('not_yet_entered');
                                    $flag = 1;
                                }else{
                                    if(!is_numeric($value_month_10)){
                                        if($string_error != ''){
                                            $string_error .= ', ';
                                        }
                                        $string_error .= app_lang('acc_month_10') .' '. app_lang('invalid');
                                        $flag = 1;
                                    }
                                }

                                if((is_null($value_month_11) || $value_month_11 == '')){
                                    if($string_error != ''){
                                        $string_error .= ', ';
                                    }
                                    $string_error .= app_lang('acc_month_11') .' '. app_lang('not_yet_entered');
                                    $flag = 1;
                                }else{
                                    if(!is_numeric($value_month_11)){
                                        if($string_error != ''){
                                            $string_error .= ', ';
                                        }
                                        $string_error .= app_lang('acc_month_11') .' '. app_lang('invalid');
                                        $flag = 1;
                                    }
                                }

                                if((is_null($value_month_12) || $value_month_12 == '')){
                                    if($string_error != ''){
                                        $string_error .= ', ';
                                    }
                                    $string_error .= app_lang('acc_month_12') .' '. app_lang('not_yet_entered');
                                    $flag = 1;
                                }else{
                                    if(!is_numeric($value_month_12)){
                                        if($string_error != ''){
                                            $string_error .= ', ';
                                        }
                                        $string_error .= app_lang('acc_month_12') .' '. app_lang('invalid');
                                        $flag = 1;
                                    }
                                }

                            }elseif ($import_type == 'quarter') {
                                if((is_null($value_quarter_1) || $value_quarter_1 == '')){
                                    if($string_error != ''){
                                        $string_error .= ', ';
                                    }
                                    $string_error .= app_lang('quarter').' 1' .' '. app_lang('not_yet_entered');
                                    $flag = 1;
                                }else{
                                    if(!is_numeric($value_quarter_1)){
                                        if($string_error != ''){
                                            $string_error .= ', ';
                                        }
                                        $string_error .= app_lang('quarter').' 1' .' '. app_lang('invalid');
                                        $flag = 1;
                                    }
                                }

                                if((is_null($value_quarter_2) || $value_quarter_2 == '')){
                                    if($string_error != ''){
                                        $string_error .= ', ';
                                    }
                                    $string_error .= app_lang('quarter').' 2' .' '. app_lang('not_yet_entered');
                                    $flag = 1;
                                }else{
                                    if(!is_numeric($value_quarter_2)){
                                        if($string_error != ''){
                                            $string_error .= ', ';
                                        }
                                        $string_error .= app_lang('quarter').' 2' .' '. app_lang('invalid');
                                        $flag = 1;
                                    }
                                }

                                if((is_null($value_quarter_3) || $value_quarter_3 == '')){
                                    if($string_error != ''){
                                        $string_error .= ', ';
                                    }
                                    $string_error .= app_lang('quarter').' 3' .' '. app_lang('not_yet_entered');
                                    $flag = 1;
                                }else{
                                    if(!is_numeric($value_quarter_3)){
                                        if($string_error != ''){
                                            $string_error .= ', ';
                                        }
                                        $string_error .= app_lang('quarter').' 3' .' '. app_lang('invalid');
                                        $flag = 1;
                                    }
                                }

                                if((is_null($value_quarter_4) || $value_quarter_4 == '')){
                                    if($string_error != ''){
                                        $string_error .= ', ';
                                    }
                                    $string_error .= app_lang('quarter').' 4' .' '. app_lang('not_yet_entered');
                                    $flag = 1;
                                }else{
                                    if(!is_numeric($value_quarter_4)){
                                        if($string_error != ''){
                                            $string_error .= ', ';
                                        }
                                        $string_error .= app_lang('quarter').' 4' .' '. app_lang('invalid');
                                        $flag = 1;
                                    }
                                }
                            }else{
                                if((is_null($value_amount) || $value_amount == '')){
                                    if($string_error != ''){
                                        $string_error .= ', ';
                                    }
                                    $string_error .= app_lang('amount') .' '. app_lang('not_yet_entered');
                                    $flag = 1;
                                }else{
                                    if(!is_numeric($value_amount)){
                                        if($string_error != ''){
                                            $string_error .= ', ';
                                        }
                                        $string_error .= app_lang('amount') .' '. app_lang('invalid');
                                        $flag = 1;
                                    }
                                }
                            }

                            if (($flag == 1) || $flag2 == 1) {
                                //write error file
                                if($import_type == 'month'){
                                    $writer->writeSheetRow('Sheet1', [
                                        $value_account,
                                        $value_month_1,
                                        $value_month_2,
                                        $value_month_3,
                                        $value_month_4,
                                        $value_month_5,
                                        $value_month_6,
                                        $value_month_7,
                                        $value_month_8,
                                        $value_month_9,
                                        $value_month_10,
                                        $value_month_11,
                                        $value_month_12,
                                        $string_error,
                                    ]);
                                }elseif ($import_type == 'quarter') {
                                    $writer->writeSheetRow('Sheet1', [
                                        $value_account,
                                        $value_quarter_1,
                                        $value_quarter_2,
                                        $value_quarter_3,
                                        $value_quarter_4,
                                        $string_error,
                                    ]);
                                }else{
                                    $writer->writeSheetRow('Sheet1', [
                                        $value_account,
                                        $value_amount,
                                        $string_error,
                                    ]);
                                }
                        
                                $total_row_false++;
                            }

                            if ($flag == 0 && $flag2 == 0) {
                                if($import_type == 'month'){
                                    $rd['account'] = $value_account;
                                    $rd['month_1'] = $value_month_1;
                                    $rd['month_2'] = $value_month_2;
                                    $rd['month_3'] = $value_month_3;
                                    $rd['month_4'] = $value_month_4;
                                    $rd['month_5'] = $value_month_5;
                                    $rd['month_6'] = $value_month_6;
                                    $rd['month_7'] = $value_month_7;
                                    $rd['month_8'] = $value_month_8;
                                    $rd['month_9'] = $value_month_9;
                                    $rd['month_10'] = $value_month_10;
                                    $rd['month_11'] = $value_month_11;
                                    $rd['month_12'] = $value_month_12;
                                }elseif ($import_type == 'quarter') {
                                    $rd['account'] = $value_account;
                                    $rd['quarter_1'] = $value_quarter_1;
                                    $rd['quarter_2'] = $value_quarter_2;
                                    $rd['quarter_3'] = $value_quarter_3;
                                    $rd['quarter_4'] = $value_quarter_4;
                                }else{
                                    $rd['account'] = $value_account;
                                    $rd['amount'] = $value_amount;
                                }

                                $rows[] = $rd;
                                array_push($arr_insert, $rd);

                            }

                        }

                        //insert batch
                        if (count($arr_insert) > 0) {
                            $this->Accounting_model->insert_batch_budget($arr_insert, $budget_id, $import_type);
                        }

                        $total_rows = $total_rows;
                        $total_row_success = isset($rows) ? count($rows) : 0;
                        $dataerror = '';
                        $message = 'Not enought rows for importing';

                        if ($total_row_false != 0) {
                            $filename = 'Import_budget_error_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
                            $writer->writeToFile(str_replace($filename, ACCOUTING_IMPORT_ITEM_ERROR . $filename, $filename));
                        }

                    }
                }
            }
        }

        if (file_exists($newFilePath)) {
            @unlink($newFilePath);
        }

        echo json_encode([
            'message' => $message,
            'total_row_success' => $total_row_success,
            'total_row_false' => $total_row_false,
            'total_rows' => $total_rows,
            'site_url' => site_url(),
            'staff_id' => $this->login_user->id,
            'filename' => ACCOUTING_IMPORT_ITEM_ERROR . $filename,
        ]);
    }

    /**
     * update reset all data account detail type
     */
    public function reset_account_detail_types(){
        if (!has_permission('accounting_setting', '', 'delete') && !is_admin() ) {
            access_denied('accounting_setting');
        }

        $success = $this->Accounting_model->reset_account_detail_types();
        if($success == true){
            $message = app_lang('reset_data_successfully');
            $this->session->setFlashdata("success_message", $message);
        }

        app_redirect('accounting/setting?group=account_type_details');
    }


    function transaction_invoices_list() {

        $view_data  = [];
        return $this->template->view('Accounting\Views\transaction\invoice', $view_data);
    }

    function transfer_form(){
        $accounts = $this->Accounting_model->get_accounts('', 'find_in_set(account_type_id, "2,3,4,5,7,8,9,10")');

        $accounts_dropdown = [];
        foreach ($accounts as $account) {
            $accounts_dropdown[$account['id']] = $account['name'];
        }

        $view_data['id'] = $this->request->getPost('id') ? $this->request->getPost('id') : '';

        $view_data['model_info'] = $this->Accounting_model->get_transfer($this->request->getPost('id'));

        //prepare groups dropdown list
        $view_data['accounts_dropdown'] = $accounts_dropdown;


        return $this->template->view('Accounting\Views\transfer\transfer_form', $view_data);

    }

    //prepare options dropdown for invoices list
    private function _convert_make_options_dropdown($rel_id = 0, $rel_type = '') {

        $options = "";
        $options .= modal_anchor(get_uri("accounting/convert_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit'), "data-post-id" => $rel_id, "data-post-type" => $rel_type));

        $options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_convert'), "class" => "delete", "data-id" => $rel_id, "data-action-url" => get_uri("accounting/delete_convert/".$rel_type), "data-action" => "delete-confirmation", "data-reload-on-success" => true));

        return $options;
    }

    function get_payment_method_dropdown() {
        

        $payment_methods = $this->Payment_methods_model->get_all_where(array("deleted" => 0))->getResult();

        $payment_method_dropdown = array(array("id" => "", "text" => "- " . app_lang("payment_methods") . " -"));
        foreach ($payment_methods as $value) {
            $payment_method_dropdown[] = array("id" => $value->id, "text" => $value->title);
        }

        return json_encode($payment_method_dropdown);
    }

    function _get_invoice_status_label($invoice_id, $return_html = true) {
        $invoice_status_class = "bg-secondary";
        $status = "not_paid";
        $now = get_my_local_time("Y-m-d");

        $invoice_info = $this->Invoices_model->get_details(['id' => $invoice_id])->getRow();
        
        //ignore the hidden value. check only 2 decimal place.
        $invoice_info->invoice_value = floor($invoice_info->invoice_value * 100) / 100;

        if ($invoice_info->status == "cancelled") {
            $invoice_status_class = "bg-danger";
            $status = "cancelled";
        } else if ($invoice_info->status != "draft" && $invoice_info->due_date < $now && $invoice_info->payment_received < $invoice_info->invoice_value) {
            $invoice_status_class = "bg-danger";
            $status = "overdue";
        } else if ($invoice_info->status !== "draft" && $invoice_info->payment_received <= 0) {
            $invoice_status_class = "bg-warning";
            $status = "not_paid";
        } else if ($invoice_info->payment_received * 1 && $invoice_info->payment_received >= $invoice_info->invoice_value) {
            $invoice_status_class = "bg-success";
            $status = "fully_paid";
        } else if ($invoice_info->payment_received > 0 && $invoice_info->payment_received < $invoice_info->invoice_value) {
            $invoice_status_class = "bg-primary";
            $status = "partially_paid";
        } else if ($invoice_info->status === "draft") {
            $invoice_status_class = "bg-secondary";
            $status = "draft";
        }

        $invoice_status = "<span class='mt0 badge $invoice_status_class large'>" . app_lang($status) . "</span>";
        if ($return_html) {
            return $invoice_status;
        } else {
            return $status;
        }
    }

    //get categories dropdown
    private function _get_categories_dropdown() {
        $categories = $this->Expense_categories_model->get_all_where(array("deleted" => 0), 0, 0, "title")->getResult();

        $categories_dropdown = array(array("id" => "", "text" => "- " . app_lang("category") . " -"));
        foreach ($categories as $category) {
            $categories_dropdown[] = array("id" => $category->id, "text" => $category->title);
        }

        return json_encode($categories_dropdown);
    }

    //get team members dropdown
    private function _get_team_members_dropdown() {
        $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff"), 0, 0, "first_name")->getResult();

        $members_dropdown = array(array("id" => "", "text" => "- " . app_lang("member") . " -"));
        foreach ($team_members as $team_member) {
            $members_dropdown[] = array("id" => $team_member->id, "text" => $team_member->first_name . " " . $team_member->last_name);
        }

        return json_encode($members_dropdown);
    }

    /**
     * manage banking
     * @return view
     */
    public function banking()
    {
        if (!has_permission('accounting_banking', '', 'view')) {
            access_denied('banking');
        }

        $data          = [];
        $data['tab_2'] = $this->request->getGet('tab');

        $data['group'] = $this->request->getGet('group');
        $data['tab'][] = 'banking_register';
        $data['tab'][] = 'posted_bank_transactions';
        $data['tab'][] = 'reconcile_bank_account';
      
        if ($data['group'] == '') {
            $data['group'] = 'banking_register';
        }

        $data['bank_accounts'] = $this->Accounting_model->get_accounts('', ['account_detail_type_id' => 14]);
        $data['accounts'] = $this->Accounting_model->get_accounts();

        $bank_accounts_dropdown = [];
        $bank_accounts_dropdown[] = ['id' => '', 'text' => '- '.app_lang('bank_account').' -'];
        foreach ($data['bank_accounts'] as $account) {
            $bank_accounts_dropdown[] = ['id' => $account['id'], 'text' => $account['name']];
        }

        $data['bank_account_dropdown'] = json_encode($bank_accounts_dropdown);

        if($data['group'] == 'reconcile_bank_account'){
            $data['bank_account'] = $this->request->getGet('bank_account');
            if($data['bank_account'] != ''){
                $data['account'] = $this->Accounting_model->get_accounts($data['bank_account']);
                $data['reconcile'] = $this->Accounting_model->get_reconcile_by_account($data['bank_account']);
                $data['reconcile_difference_info'] = $this->Accounting_model->get_reconcile_difference_info($data['reconcile']->id);
                
                $data['title'] = _l('reconcile');
                $data['account_adjust'] = $this->Accounting_model->get_account_id_by_number('2110-000');
            }else{
                if ($this->request->getPost()) {
                    if (!has_permission('accounting_reconcile', '', 'create')) {
                        access_denied('accounting_reconcile');
                    }
                    $data = $this->request->getPost();
                    if ($data['resume'] == 0) {
                        unset($data['resume']);
                        $success = $this->Accounting_model->add_bank_reconcile($data);
                    }
                    app_redirect('accounting/banking?group=reconcile_bank_account&bank_account=' . $data['account']);

                }
               

                $data['title'] = _l('reconcile');
                $data['beginning_balance'] = 0;
                $data['resume'] = 0;
                $data['approval'] = 0;

                //get default company

                $default_company='';
                $hide_restored=' hide';

                $closing_date = false;
                
                if(isset($data['bank_accounts'][0])){
                    $check_reconcile_restored = $this->Accounting_model->check_reconcile_restored($data['bank_accounts'][0]['id'], $default_company);
                    if($check_reconcile_restored){
                        $hide_restored='';
                    }

                    $reconcile = $this->Accounting_model->get_reconcile_by_account($data['bank_accounts'][0]['id'], $default_company);


                    if ($reconcile) {
                        if(get_setting('acc_close_the_books') == 1){
                            $closing_date = (strtotime($reconcile->ending_balance) > strtotime(date('Y-m-d'))) ? true : false;
                        }
                        $data['beginning_balance'] = $reconcile->ending_balance;
                        if ($reconcile->finish == 0) {
                            $data['resume'] = 1;
                        }

                    }
                }
                $data['accounts_to_select'] = $this->Accounting_model->get_data_account_to_select();
                $data['hide_restored'] = $closing_date == false ? $hide_restored : 'hide';
            }
        }

        $data['_status'] = '';
        if ($this->request->getGet('status')) {
            $data['_status'] = [$this->request->getGet('status')];
        }

        $data['account_to_select'] = $this->Accounting_model->get_data_account_to_select();
        $data['currency_symbol'] = get_setting("currency_symbol");
        $data['title']        = app_lang($data['group']);
        $data['tabs']['view'] = 'Accounting\Views\banking/' . $data['group'];
        $data['tab_2'] = 'Accounting\Views\banking/'.$data['tab_2'];
        return $this->template->rander('Accounting\Views\banking/manage', $data);
    }

    public function check_plaid_connect($bank_id = ''){
        $success = false;
        if($bank_id != ''){
            $account_data = $this->Accounting_model->get_account_bank_data($bank_id);
            if(isset($account_data) && $account_data != NULL && $account_data[0]['plaid_status'] == 1){
                $success = true;
            }
        }

        echo json_encode($success);
        die();
    }

    /**
     * posted bank transactions table
     * @return json
     */
    public function posted_bank_transactions_table() {
            
            $currency_symbol = get_setting("currency_symbol");

            $select = [
                'date',
                'payee',
                'description',
                'withdrawals',
                'deposits',
                'matched',
            ];
            $where = [];

            $from_date = $this->request->getPost('from_date') ? $this->request->getPost('from_date') : "";
            $to_date = $this->request->getPost('to_date') ? $this->request->getPost('to_date') : "";
            
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'acc_transaction_bankings.date >= "' . $from_date . '" and ' . get_db_prefix() . 'acc_transaction_bankings.date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'acc_transaction_bankings.date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (' . get_db_prefix() . 'acc_transaction_bankings.date <= "' . $to_date . '")');
            }

           
            $bank_account = $this->request->getPost('bank_account') ? $this->request->getPost('bank_account') : "";
            if ($bank_account != '') {
                array_push($where, 'AND '.get_db_prefix().'acc_transaction_bankings.bank_id ='. $bank_account);
            }else{
                array_push($where, 'AND '.get_db_prefix().'acc_transaction_bankings.bank_id = "-1"');
            }

            $status = $this->request->getPost('status') ? $this->request->getPost('status') : "";
            if ($status != '') {
                $where_status = '';
                foreach ($status as $key => $value) {
                    if ($value == 'converted') {
                        if ($where_status != '') {
                            $where_status .= ' or (matched > 0)';
                        } else {
                            $where_status .= '(matched > 0)';
                        }
                    }

                    if ($value == 'has_not_been_converted') {
                        if ($where_status != '') {
                            $where_status .= ' or (matched = 0)';
                        } else {
                            $where_status .= '(matched = 0)';
                        }
                    }
                }

                if ($where_status != '') {
                    array_push($where, 'AND (' . $where_status . ')');
                }
            }

            $aColumns = $select;
            $sIndexColumn = 'id';
            $sTable = get_db_prefix() . 'acc_transaction_bankings';
            $join = [];
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['matched']);

            $output = $result['output'];
            $rResult = $result['rResult'];
            $balance = 0;

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = _d($aRow['date']);

                $row[] = $aRow['payee'];
                $row[] = $aRow['description'];

                $row[] = $aRow['withdrawals'] != 0 ? to_currency($aRow['withdrawals'], $currency_symbol) : '';
                $row[] = $aRow['deposits'] != 0 ? to_currency($aRow['deposits'], $currency_symbol) : '';

                if ($aRow['matched'] > 0) {
                    $row[] = '<i data-feather="check" class="icon-16 text-success"></i>';

                }elseif($aRow['matched'] == 0){
                    $row[] = '';
                }else{
                    $row[] = '<i data-feather="x" class="icon-16 text-danger"></i>';
                }


                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }
    
    public function plaid_bank_new_transactions(){
        $data['last_updated'] = '';
        if(isset($_GET['id'])){
            $transactions = $this->Accounting_model->get_plaid_transaction($_GET['id']);
            $data['transactions'] = $transactions;
            $account_data = $this->Accounting_model->get_account_bank_data($_GET['id']);
            $data['account_data'] = $account_data;
            $refresh_data = $this->Accounting_model->get_last_refresh_data($_GET['id']);
            $data['refresh_data'] = $refresh_data;
            $data['last_updated'] = $this->Accounting_model->get_date_last_updated($_GET['id']);
        }
        $data['title'] = _l('acc_plaid_transaction');
        $data['status'] = '';
        if ($this->request->getGet('status')) {
            $data['status'] = [$this->input->get('status')];
        }


        $data['bank_accounts'] = $this->Accounting_model->get_accounts('', ['account_detail_type_id' => 14]);

        $bank_accounts_dropdown = [];
        foreach ($data['bank_accounts'] as $account) {
            $bank_accounts_dropdown[$account['id']] = $account['name'];
        }

        $data['bank_account_dropdown'] = json_encode($bank_accounts_dropdown);

        $data['accounts'] = $this->Accounting_model->get_accounts();
        $data['account_to_select'] = $this->Accounting_model->get_data_account_to_select();
        return $this->template->rander('Accounting\Views\banking/plaid_new_transaction', $data);
    }

    //Create Plaid Link Token
    public function create_plaid_token(){
        $link_token = $this->Accounting_model->get_plaid_link_token(); 

        echo json_encode(array(
                    'link_token' => $link_token,
            ));
        die;
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(500)
            ->set_output(json_encode(array(
                    'link_token' => $link_token,
            )));
    }

    /**
     * update plaid environment
     */
    public function update_plaid_environment() {
        if (!has_permission('accounting_setting', '', 'edit') && !is_admin()) {
            access_denied('accounting_setting');
        }
        $data = $this->request->getPost();
        $success = $this->Accounting_model->update_plaid_environment($data);

        if ($success == true) {
            $message = sprintf(_l('updated_successfully'), _l('setting'));
            $this->session->setFlashdata("success_message", $message);
        }

        app_redirect('accounting/setting?group=plaid_environment');
    }

    public function update_plaid_bank_accounts(){ 
        $public_token = $_GET['public_token'];  
        $bank_id = $_GET['bankId'];

        $accessToken = $this->Accounting_model->get_access_token($public_token); 
        $accounts = $this->Accounting_model->plaid_get_account($accessToken); 

        $accountId = $accounts[0]->account_id;
        $accountName = $accounts[0]->name;

        $db = db_connect('default');
        $db_builder = $db->table(get_db_prefix() . 'acc_accounts');
        $db_builder->where('id', $bank_id);

        $db_builder->where('id', $bank_id);
        $db_builder->update([
            'access_token' => $accessToken,
            'account_id' => $accountId,
            'plaid_status' => 1,
            'plaid_account_name' => $accountName
        ]);
        
        echo json_encode(['error' => '']);
        die;

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(500)
            ->set_output(json_encode(array(
                    'error' => '',
            )));
    }

    /**
     * banking table
     * @return json
     */
    public function banking_register_table() {

            $currency_symbol = get_setting("currency_symbol");

            $select = [
                'date',
                'customer',
                'description',
                'credit',
                'debit',
                'cleared',
            ];
            $where = [];

            
            $from_date = $this->request->getPost('from_date') ? $this->request->getPost('from_date') : "";
            $to_date = $this->request->getPost('to_date') ? $this->request->getPost('to_date') : "";
            
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (date >= "' . $from_date . '" and date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (date <= "' . $to_date . '")');
            }

           
            $bank_account = $this->request->getPost('bank_account') ? $this->request->getPost('bank_account') : "";
            if ($bank_account != '') {
                array_push($where, 'AND account ='. $bank_account);
            }else{
                array_push($where, 'AND account = "-1"');
            }

            $status = $this->request->getPost('status') ? $this->request->getPost('status') : "";
            if ($status != '') {
                $where_status = '';
                foreach ($status as $key => $value) {
                    if ($value == 'converted') {
                        if ($where_status != '') {
                            $where_status .= ' or (cleared > 0)';
                        } else {
                            $where_status .= '(cleared > 0)';
                        }
                    }

                    if ($value == 'has_not_been_converted') {
                        if ($where_status != '') {
                            $where_status .= ' or (cleared = 0)';
                        } else {
                            $where_status .= '(cleared = 0)';
                        }
                    }
                }

                if ($where_status != '') {
                    array_push($where, 'AND (' . $where_status . ')');
                }
            }

            $aColumns = $select;
            $sIndexColumn = 'id';
            $sTable = get_db_prefix() . 'acc_account_history';
            $join = [
            ];
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['customer', 'rel_type', 'rel_id', 'account', 'vendor']);

            $output = $result['output'];
            $rResult = $result['rResult'];

            $balance = 0;

            foreach ($rResult as $aRow) {
                $row = [];
                
                $row[] = _d($aRow['date']);

           

                $credit = 0;
                $debit = 0;

                    $row[] = get_company_name($aRow['customer']);
              

                $row[] = $aRow['description'];

                if($aRow['credit'] != 0){
                    $credit = $aRow['credit'];
                    $row[] = to_currency($aRow['credit'], $currency_symbol);
                }else{
                    $row[] = '';
                }

                if($aRow['debit'] != 0){
                    $debit = $aRow['debit'];
                    $row[] = to_currency($aRow['debit'], $currency_symbol);
                }else{
                    $row[] = '';
                }
               

                $status_name = _l('not_yet_match');
                $label_class = 'default';

                if ($aRow['cleared'] > 0) {
                    $row[] = '<i data-feather="check" class="icon-16 text-success"></i>';

                }elseif($aRow['cleared'] == 0){
                    $row[] = '';
                }else{
                    $row[] = '<i data-feather="x" class="icon-16 text-danger"></i>';
                }

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        /**
     * banking table
     * @return json
     */
    public function import_banking_table() {
        $currency_symbol = get_setting("currency_symbol");
            
            $select = [
                'date',
              
                'payee',
                'description',
                'withdrawals',
                'deposits',
                'datecreated',
            ];
            $where = [];

            $from_date = $this->request->getPost('from_date') ? $this->request->getPost('from_date') : "";
            $to_date = $this->request->getPost('to_date') ? $this->request->getPost('to_date') : "";
            
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (date >= "' . $from_date . '" and date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (date <= "' . $to_date . '")');
            }

            $bank_account = $this->request->getPost('bank_account') ? $this->request->getPost('bank_account') : "";
            if ($bank_account != '') {
                array_push($where, 'AND bank_id ='. $bank_account);
            }else{
                array_push($where, 'AND bank_id = "-1"');
            }

            $aColumns = $select;
            $sIndexColumn = 'id';
            $sTable = get_db_prefix() . 'acc_transaction_bankings';
            $join = [];
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id']);

            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = _d($aRow['date']);

            
 
                $row[] = $aRow['payee'];
                $row[] = $aRow['description'];

                $row[] = to_currency($aRow['withdrawals'], $currency_symbol);
                $row[] = to_currency($aRow['deposits'], $currency_symbol);

                $row[] = _d($aRow['datecreated']);

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
        }


    public function update_plaid_transaction(){
        if ($this->request->getPost()) { 
            $bank_id = $_POST['bank_id'];
            $end_date = date('Y-m-d');

            $start_date = $_POST['from_date'];
        
            //Make Entry of Transaction Log
            $logData = ['bank_id' => $_POST['bank_id'], 'last_updated' => date('Y-m-d'), 'addedFrom' => ''];

            $db = db_connect('default');
            $db_builder = $db->table(get_db_prefix() . 'acc_plaid_transaction_logs');
            $db_builder->insert($logData);
            
            //Call Curl function to get Transaction
            if($db->affectedRows() > 0){
                $this->transactionData($start_date, $end_date, $_POST['bank_id']);
                $transactions = $this->Accounting_model->get_plaid_transaction($_POST['bank_id']);
                $data['transactions'] = $transactions;
                $data['bank_id'] = $_POST['bank_id'];
                $data['title'] = _l('acc_plaid_transaction');
                $data['status'] = '';
                if ($this->request->getGet('status')) {
                    $data['status'] = [$this->request->getGet('status')];
                }

                $data['bank_accounts'] = $this->Accounting_model->get_accounts('', ['account_detail_type_id' => 14]);

                $data['accounts'] = $this->Accounting_model->get_accounts();
                $data['account_to_select'] = $this->Accounting_model->get_data_account_to_select();
                
            }
        }
    }

    public function transactionData($start_date, $end_date, $bank_id){
        //Get the Paid Key and Secret Key and also access token
        $accounts = $this->Accounting_model->get_accounts($bank_id);
        $transactions = $this->Accounting_model->plaid_get_transactions(['access_token' => $accounts->access_token, 'start_date' => $start_date, 'end_date' => $end_date]);

        if($transactions){
           //Call the transaction Insert Function in Table
           $success = $this->insertTransactionRecord($transactions, $bank_id);
           if($success){
                $this->session->setFlashdata("success_message", app_lang('imported_successfully'));
           }else{
                $this->session->setFlashdata("error_message", app_lang('imported_fail'));
           }
        }else{
            $this->session->setFlashdata("error_message", app_lang('no_transaction'));
        }


    }

    public function insertTransactionRecord($datas, $bankId){
        $i = 0;
        foreach($datas as $data){
            $amount = $data->amount;
            $checkNumber = $data->check_number;
            $date = $data->date;
            $description = $data->original_description;
            $payment_status = $data->pending;
            $transaction_id = $data->transaction_id;
            $payee = $data->payment_meta->payee;

            if($payment_status == false){
               $paymentData = [];
               $paymentData['date'] = $date;
               $paymentData['datecreated'] = date('Y-m-d H:i:s');
               $paymentData['status'] = 1;
               $paymentData['transaction_id'] = $transaction_id;
               $paymentData['withdrawals'] = $amount < 0 ? 0 : abs($amount);
               $paymentData['deposits'] = $amount > 0 ? 0 : abs($amount);
               $paymentData['addedFrom'] = '';
               $paymentData['description'] = $description;
               $paymentData['payee'] = $payee;
               $paymentData['bank_id'] = $bankId;


               //Check if Transaction Id Already Exists or not
               $db = db_connect('default');
                $db_builder = $db->table(get_db_prefix() . 'acc_transaction_bankings');
               $db_builder->where('transaction_id', $transaction_id);
               $db_builder->where('bank_id', $bankId);
                $query = $db_builder->get()->getRow();
               
                if(!$query){
                    $db_builder->insert($paymentData);
                    $id = $db->insertID();
                    if($id){
                        $i++;
                    }
                }
            }
        }

        if($i > 0){
            return true;
        }

        return false;
    }

    /**
     * { match transactions }
     *
     * @param        $reconcile_id  The reconcile identifier
     * @param        $account_id    The account identifier
     */
    public function match_transactions($reconcile_id, $account_id){

        $success = $this->Accounting_model->match_transactions($reconcile_id, $account_id);
        $message = _l('match_fail');
        if($success == 1){
            $message = _l('matched_successfully');
        }

        echo json_encode([
            'success' => $success,
            'message' => $message
        ]);
        die;
    }

    /**
     * { unmatch transactions }
     *
     * @param        $reconcile_id  The reconcile identifier
     * @param        $account_id  The bank account identifier
     */
    public function unmatch_transactions($reconcile_id, $account_id){

        $success = $this->Accounting_model->unmatch_transactions($reconcile_id, $account_id);
        $message = _l('unmatch_fail');
        if($success == true){
            $message = _l('unmatched_successfully');
        }

        echo json_encode([
            'success' => $success,
            'message' => $message
        ]);
        die;
    }

    /**
     * { reconcile transactions table }
     */
    public function reconcile_transactions_table(){
            $currency_symbol = get_setting("currency_symbol");
             

            $select = [
                'date',
                'vendor',
                'description',
                'credit',
                'debit',
                'cleared',
            ];
            $where = [];

            $from_date = '';
            $to_date = '';

            $bank_account = '';
            if ($this->request->getPost('account')) {
                $bank_account = $this->request->getPost('account');
                array_push($where, 'AND account ='. $bank_account);
            }

            if($this->request->getPost('reconcile')){
                $reconcile_id = $this->request->getPost('reconcile');

                $reconcile = $this->Accounting_model->get_reconcile($reconcile_id);
                if($reconcile){
                    $to_date = $reconcile->ending_date;
                }

                if($bank_account != ''){
                    $recently_reconcile = $this->Accounting_model->get_recently_reconcile_by_account($bank_account, $reconcile_id);
                    if($recently_reconcile){
                        $from_date = $recently_reconcile->ending_date;
                    }
                }

                array_push($where, 'AND ('.get_db_prefix() . 'acc_account_history.reconcile ='. $reconcile_id.' or '.get_db_prefix() . 'acc_account_history.reconcile = 0)');

            }

            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (date >= "' . $from_date . '" and date <= "' . $to_date . '")');
            } elseif ($to_date != '' && $from_date == '') {
                array_push($where, 'AND (date <= "' . $to_date . '")');
            }

            $aColumns = $select;
            $sIndexColumn = 'id';
            $sTable = get_db_prefix() . 'acc_account_history';
            $join = [
                    ];
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [get_db_prefix() . 'acc_account_history.id as id', 'account', 'description', 'customer', 'rel_type', 'cleared']);

            $output = $result['output'];
            $rResult = $result['rResult'];

            $balance = 0;

            foreach ($rResult as $aRow) {
                $row = [];
                
                $row[] = _d($aRow['date']);


                if($aRow['vendor'] != 0){
                        $row[] = '';
                }else{
                    $row[] = '';
                }

                $row[] = $aRow['description'];


                if($aRow['credit'] != 0){
                    $row[] = to_currency($aRow['credit'], $currency_symbol);
                }else{
                    $row[] = '';
                }

                if($aRow['debit'] != 0){
                    $row[] = to_currency($aRow['debit'], $currency_symbol);
                }else{
                    $row[] = '';
                }

                $status_name = _l('not_yet_match');
                $label_class = 'default';

                if ($aRow['cleared'] > 0) {
                    $row[] = '<i data-feather="check" class="icon-16 text-success"></i>';

                }elseif($aRow['cleared'] == 0){
                    $row[] = '';
                }else{
                    $row[] = '<i data-feather="x" class="icon-16 text-danger"></i>';
                }

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * { reconcile posted bank table }
     */
    public function reconcile_posted_bank_table(){
            $currency_symbol = get_setting("currency_symbol");
            
            $select = [
                'id',
                'date',
                'payee',
                'withdrawals',
                'deposits',
                'bank_id',
            ];
            $where = [];

            $from_date = '';
            $to_date = '';

            $bank_account = '';
            if ($this->request->getPost('account')) {
                $bank_account = $this->request->getPost('account');
                array_push($where, 'AND bank_id ='. $bank_account);
            }

            if($this->request->getPost('reconcile')){
                $reconcile_id = $this->request->getPost('reconcile');
                array_push($where, 'AND (reconcile = 0 or reconcile = '.$reconcile_id.')');

                $reconcile = $this->Accounting_model->get_reconcile($reconcile_id);


                if($reconcile){
                    $to_date = $reconcile->ending_date;
                }


                if($bank_account != ''){
                    $recently_reconcile = $this->Accounting_model->get_recently_reconcile_by_account($bank_account, $reconcile_id);
                    if($recently_reconcile){
                        $from_date = $recently_reconcile->ending_date;
                    }
                }
            }

            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (date > "' . $from_date . '" and date <= "' . $to_date . '")');
            } elseif ($to_date != '' && $from_date == '') {
                array_push($where, 'AND (date <= "' . $to_date . '")');
            }


            $aColumns = $select;
            $sIndexColumn = 'id';
            $sTable = get_db_prefix() . 'acc_transaction_bankings';
            $join = [];
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['description', 'datecreated', 'matched']);

            $output = $result['output'];
            $rResult = $result['rResult'];

            $balance = 0;

            foreach ($rResult as $aRow) {
                $row = [];
                
                $row[] = _d($aRow['date']);

                $row[] = $aRow['payee'];
                $row[] = $aRow['description'];

                if($aRow['withdrawals'] != 0){
                    $row[] = to_currency($aRow['withdrawals'], $currency_symbol);
                }else{
                    $row[] = '';
                }

                if($aRow['deposits'] != 0){
                    $row[] = to_currency($aRow['deposits'], $currency_symbol);
                }else{
                    $row[] = '';
                }

                $status_name = _l('not_yet_match');
                $label_class = 'default';

                if ($aRow['matched'] == 1) {
                    $row[] = '<i data-feather="check" class="icon-16 text-success"></i>';
                }elseif($aRow['matched'] == 0){
                    $row[] = '';
                }else{
                    $row[] = '<i data-feather="x" class="icon-16 text-danger"></i>';
                }

                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    public function get_transaction_uncleared(){
        $data = $this->request->getPost();
        $transaction_bankings = $this->Accounting_model->get_transaction_uncleared($data['reconcile_id']);
        $status = 0;
        $html = '';

        if(count($transaction_bankings) > 0){
            foreach($transaction_bankings as $transaction){
                if($transaction['adjusted'] == 1){
                    $html .= '<tr><td>'._d($transaction['date']).'</td><td>'.$transaction['payee'].'</td><td>'.$transaction['description'].'</td><td>'.$transaction['withdrawals'].'</td><td>'.$transaction['deposits'].'</td><td><i class="fa fa-check-circle text-success fa-2x" aria-hidden="true"></i></td></tr>';
                }else{
                    $status = 1;
                    $html .= '<tr><td>'._d($transaction['date']).'</td><td>'.$transaction['payee'].'</td><td>'.$transaction['description'].'</td><td>'.$transaction['withdrawals'].'</td><td>'.$transaction['deposits'].'</td><td><a href="#" class="btn btn-info" onclick="make_adjusting_entry('.$transaction['id'].'); return false;"><i data-feather="tool" class="icon-16"></i> '. _l('make_adjusting_entry').'</a><br><br><a href="#" class="btn btn-warning" onclick="leave_it_uncleared(this); return false;" data-id="'.$transaction['id'].'"><i data-feather="x" class="icon-16"></i> '. _l('leave_it_uncleared').'</a></td></tr>';
                }
            }
        }

        echo json_encode([
            'status' => $status,
            'html' => $html,
        ]);
        die;
    }

    public function get_make_adjusting_entry(){
      
        $currency_symbol = get_setting("currency_symbol");
        
        $data = $this->request->getPost();

        $transaction_banking = $this->Accounting_model->get_transaction_banking($data['transaction_bank_id']);

        $amount = to_currency($transaction_banking->withdrawals, $currency_symbol);

        if($transaction_banking->deposits > 0){
            $amount = to_currency(-$transaction_banking->deposits, $currency_symbol);
        }

        $transaction_uncleared = $this->Accounting_model->get_bank_transaction_uncleared($data['reconcile_id']);
        $tran_html = '';
        $tran_withdrawals = 0;
        $tran_deposits = 0;
        foreach($transaction_uncleared as $key => $tran){
            $payee = '';
           
            $date = _d($tran['date']);
            

            $selected = '';

            if($key < 1){
                $selected = 'selected';
            }

            $name = 'Date: '.$date.' Payee: '.$payee;
            if($tran['credit'] > 0){
                $withdrawals = number_format($tran['credit'],2);
                $name .= ' Withdrawals: '.$withdrawals;
                if($key < 1){
                    $tran_withdrawals = $withdrawals;
                }
            }else{
                $deposits = number_format($tran['debit'],2);
                $name .= ' Deposits: '.$deposits;
                if($key < 1){
                    $tran_deposits = $deposits;
                }
            }

            $tran_html .= '<option value="'.$tran['id'].'" '.$selected.'>'.$name.'</option>';
        }

        echo json_encode([
            'date' => date('m/d/Y', strtotime($transaction_banking->date)),
            'amount' => $amount,
            'payee' => $transaction_banking->payee ? $transaction_banking->payee : '',
            'tran_html' => $tran_html,
            'date_value' => _d($transaction_banking->date),
            'tran_deposit' => $tran_deposits,
            'tran_withdrawal' => $tran_withdrawals
        ]);
        die;
    }

    public function make_adjusting_entry_save(){
        $data = $this->request->getPost();
        
        $success = $this->Accounting_model->make_adjusting_entry_save($data);

        echo json_encode([
            'success' => $success,
            'message' => _l('updated_successfully', _l('transaction'))
        ]);
        die;
    }

    public function leave_it_uncleared(){
        $data = $this->request->getPost();
        $success = $this->Accounting_model->leave_it_uncleared($data['transaction_bank_id']);

        echo json_encode([
            'success' => $success,
            'message' => _l('updated_successfully', _l('transaction'))
        ]);
        die;
    }

    public function check_complete_reconcile(){
        $currency_symbol = get_setting("currency_symbol");

        $data = $this->request->getPost();
        $leave_uncleared = 0;
        $transaction_bankings = $this->Accounting_model->get_transaction_leave_uncleared($data['reconcile_id']);
        $reconcile_difference_info = $this->Accounting_model->get_reconcile_difference_info($data['reconcile_id']);

        if(count($transaction_bankings) > 0){
            $leave_uncleared = 1;
        }


        $difference_withdrawals = abs($reconcile_difference_info['banking_register_withdrawals'] - $reconcile_difference_info['posted_bank_withdrawals']);
        $difference_deposits = abs($reconcile_difference_info['banking_register_deposits'] - $reconcile_difference_info['posted_bank_deposits']);

        $html = '';
        if($leave_uncleared == 1){
            $html .= '
            <table class="table table-checks-to-print scroll-responsive dataTable">
                 <tbody>
                 <tr>
                    <td colspan="3">'. _l('you_are_reconciling_with_uncleared_transactions') .'</td>
                  </tr>
                  <tr>
                    <td>'. _l('acc_banking_register') .'</td>
                    <td>'.to_currency($reconcile_difference_info['banking_register_withdrawals'], $currency_symbol).'</td>
                    <td>'.to_currency($reconcile_difference_info['banking_register_deposits'], $currency_symbol).'</td>
                  </tr>
                  <tr>
                    <td>'. _l('posted_bank_transactions') .'</td>
                    <td>'.to_currency($reconcile_difference_info['posted_bank_withdrawals'], $currency_symbol).'</td>
                    <td>'.to_currency($reconcile_difference_info['posted_bank_deposits'], $currency_symbol).'</td>
                  </tr>
                  <tr>
                    <td>'. _l('difference') .'</td>
                    <td>'.to_currency($difference_withdrawals, $currency_symbol).'</td>
                    <td>'.to_currency($difference_deposits, $currency_symbol).'</td>
                  </tr>
                  <tr>
                    <td>'. _l('total_difference') .'</td>
                    <td>'.to_currency(($difference_withdrawals + $difference_deposits), $currency_symbol).'</td>
                    <td></td>
                  </tr>
                </tbody>
            </table>';
        }

        echo json_encode([
            'leave_uncleared' => $leave_uncleared,
            'html' => $html,
        ]);
        die;

    }

    /**
     *
     *  add adjustment
     *  @return view
     */
    public function bank_account_adjustment() {
        if (!has_permission('accounting_reconcile', '', 'create')) {
            access_denied('accounting');
        }
        if ($this->request->getPost()) {
            $data = $this->request->getPost();

            $message = '';
            $success = $this->Accounting_model->add_bank_account_adjustment($data);

            if ($success === 'close_the_book') {
                $message = _l('has_closed_the_book');
            } elseif ($success) {
                $message = _l('added_successfully', _l('adjustment'));
            } else {
                $message = _l('add_failure');
            }

            echo json_encode(['success' => $success, 'message' => $message]);
            die();
        }
    }

    /**
     * finish reconcile bank account
     * @return view
     */
    public function finish_reconcile_bank_account() {
        if (!has_permission('accounting_reconcile', '', 'create') && !is_admin()) {
            access_denied('accounting_reconcile');
        }

        if ($this->request->getPost()) {
            $data = $this->request->getPost();
            $message = '';
            $success = $this->Accounting_model->finish_reconcile_bank_account($data);

            if ($success) {
                $message = _l('added_successfully', _l('reconcile'));
                $this->session->setFlashdata("success_message", $message);
            } else {
                $message = _l('add_failure');
                $this->session->setFlashdata("error_message", $message);
            }
        }

        app_redirect('accounting/banking?group=reconcile_bank_account');
    }

    /**
     * reconcile restored
     * @param  [type] $account 
     * @param  [type] $company 
     * @return [type]          
     */
    public function reconcile_bank_account_restored($account) {
            $success = false;
            $message = _l('acc_restored_failure');
            $hide_restored = true;
            
            $reconcile_restored = $this->Accounting_model->reconcile_bank_account_restored($account);
            if($reconcile_restored){
                $success = true;
                $message = _l('acc_restored_successfully');
            }

            $check_reconcile_restored = $this->Accounting_model->check_reconcile_restored($account);
            if($check_reconcile_restored){
                $hide_restored = false;
            }
            
            echo json_encode([
                'success' => $success,
                'hide_restored' => $hide_restored,
                'message' => $message,
            ]);
            die();
    }
    
    /**
     * get info reconcile
     * @param  integer $account
     * @return json
     */
    public function get_info_reconcile_bank_account($account) {
        $reconcile = $this->Accounting_model->get_reconcile_by_account($account);
        $beginning_balance = 0;
        $resume_reconciling = false;
        $approval_reconciling = false;
        $hide_restored = true;

        $edit_debits_for_period = 0;
        $edit_credits_for_period = 0;
        $edit_ending_date = '';
        $edit_ending_balance = 0;
        $edit_beginning_balance = 0;
        $edit_reconcile_id = 0;

        $check_reconcile_restored = $this->Accounting_model->check_reconcile_restored($account);
        if($check_reconcile_restored){
            $hide_restored = false;
        }
        $closing_date = false;

        if ($reconcile) {
            if(get_setting('acc_close_the_books') == 1){
                $closing_date = (strtotime($reconcile->ending_balance) > strtotime(date('Y-m-d'))) ? true : false;
            }
            $beginning_balance = $reconcile->ending_balance;
            if ($reconcile->finish == 0 || $reconcile->finish == null) {
                $resume_reconciling = true;
            }

            $edit_debits_for_period = $reconcile->debits_for_period;
            $edit_credits_for_period = $reconcile->credits_for_period;
            $edit_ending_date = _d($reconcile->ending_date);
            $edit_ending_balance = $reconcile->ending_balance;
            $edit_beginning_balance = $reconcile->beginning_balance;
            $edit_reconcile_id = $reconcile->id;

        }


        echo json_encode(['beginning_balance' => $beginning_balance, 'resume_reconciling' => $resume_reconciling, 'hide_restored' => $hide_restored, 'closing_date' => $closing_date, 'edit_debits_for_period' => $edit_debits_for_period, 'edit_credits_for_period' => $edit_credits_for_period, 'edit_ending_date' => $edit_ending_date, 'edit_ending_balance' => $edit_ending_balance, 'edit_beginning_balance' => $edit_beginning_balance, 'edit_reconcile_id' => $edit_reconcile_id, 'approval_reconciling' => $approval_reconciling ]);
        die();
    }

    /**
     * report bank reconciliation summary
     * @return view
     */
    public function rp_bank_reconciliation_summary() {
        $this->load->model('currencies_model');
        $data['title'] = _l('bank_reconciliation_summary');

        $data['from_date'] = date('Y-m-d');
        $data['to_date'] = date('Y-m-d');

        $data['bank_accounts'] = $this->Accounting_model->get_accounts('', ['account_detail_type_id' => 14]);

        $data['default_account'] = '';
        if (isset($data['bank_accounts'][0])) {
            $data['default_account'] = $data['bank_accounts'][0]['id'];
        }

        $data['reconcile'] = $this->Accounting_model->get_reconcile('', 'account = "'.$data['default_account'].'"');
        foreach($data['reconcile'] as $key => $reconcile){
            $data['reconcile'][$key]['ending_date'] = date('m/d/Y', strtotime($reconcile['ending_date']));
        }

        $data['default_reconcile'] = '';
        if (isset($data['reconcile'][0])) {
            $data['default_reconcile'] = $data['reconcile'][0]['id'];
        }

        $data['currency'] = $this->currencies_model->get_base_currency();
        $this->load->view('report/includes/bank_reconciliation_summary', $data);
    }

    /**
     * report bank reconciliation summary
     * @return view
     */
    public function rp_bank_reconciliation_detail() {
        $this->load->model('currencies_model');
        $data['title'] = _l('bank_reconciliation_detail');

        $data['from_date'] = date('Y-m-d');
        $data['to_date'] = date('Y-m-d');
     
        $data['bank_accounts'] = $this->Accounting_model->get_accounts('', ['account_detail_type_id' => 14]);

        $data['default_account'] = '';
        if (isset($data['bank_accounts'][0])) {
            $data['default_account'] = $data['bank_accounts'][0]['id'];
        }

        $data['reconcile']= $this->Accounting_model->get_reconcile('', 'account = "'.$data['default_account'].'"');

        foreach($data['reconcile'] as $key => $reconcile){
            $data['reconcile'][$key]['ending_date'] = date('m/d/Y', strtotime($reconcile['ending_date']));
        }

        $data['default_reconcile'] = '';
        if (isset($data['reconcile'][0])) {
            $data['default_reconcile'] = $data['reconcile'][0]['id'];
        }

        $data['currency'] = $this->currencies_model->get_base_currency();
        $this->load->view('report/includes/bank_reconciliation_detail', $data);
    }

    /**
     * { reconcile account change }
     *
     * @param      <string>  $type   The type
     */
     public function reconcile_account_change($account = ''){
        $html = '';

        $reconcile = $this->Accounting_model->get_reconcile('', 'opening_balance = 0 and account = "'.$account.'"');

        $html = ''; 
        foreach($reconcile as $key => $value){
            $selected = '';

            if($key < 1){
                $selected = 'selected';
            }

            $html .= '<option value="'.$value['id'].'" '.$selected.'>'._d($value['ending_date']).'</option>';
        }

        echo json_encode($html);

     }

     public function update_bank_reconcile() {
            $data = $this->request->getGet();

            if(isset($data['csrf_token_name'])){
                unset($data['csrf_token_name']);
            }

            $id = 0;
            if(isset($data['reconcile_id'])){
                $id = $data['reconcile_id'];
                unset($data['reconcile_id']);
            }

            $success = false;
            $message = _l('accounting_no_data_changes');
            
            $update_reconcile = $this->Accounting_model->ajax_update_reconcile($data, $id);
            if($update_reconcile){
                $success = true;
                $message = _l('saved_successfully');
            }

            echo json_encode([
                'success' => $success,
                'message' => $message,
            ]);
            die();
    }

    /**
     * import xlsx banking
     * @return view
     */
    public function import_xlsx_posted_bank_transactions() {
        if (!has_permission('accounting_transaction', '', 'create')) {
            access_denied('accounting_transaction');
        }

        
        $data['active_language'] = get_setting("user_" . $this->login_user->id . "_personal_language");
        $data['title'] = _l('import_excel');
        $data['bank_accounts'] = $this->Accounting_model->get_accounts('', ['account_detail_type_id' => 14]);

        return $this->template->rander('Accounting\Views\banking/import_banking', $data);
    }

    /**
     * import file xlsx banking
     * @return json
     */
    public function import_file_xlsx_posted_bank_transactions(){
        if(!class_exists('XLSXReader_fin')){
            require_once PLUGINPATH . 'Accounting/assets/plugins/XLSXReader/XLSXReader.php';
        }
        require_once PLUGINPATH . 'Accounting/assets/plugins/XLSXWriter/xlsxwriter.class.php';

        $filename ='';
        if($this->request->getPost()){
            $data_filter = $this->request->getPost();
            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                $this->delete_error_file_day_before(1, ACCOUTING_IMPORT_ITEM_ERROR);

                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];                
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $rows          = [];
                    $arr_insert          = [];

                    $tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 0755);
                    }

                    if (!file_exists($tmpDir)) {
                        mkdir($tmpDir, 0755);
                    }

                    // Setup our new file path
                    $newFilePath = $tmpDir . $_FILES['file_csv']['name'];                    

                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        //Writer file
                        $writer_header = array(
                            _l('date').' (dd/mm/YYYY)'            =>'string',
                            _l('withdrawals')     =>'string',
                            _l('deposits')    =>'string',
                            _l('payee')      =>'string',
                            _l('description')     =>'string',
                            _l('error')       =>'string',
                        );

                        $rowstyle[] =array('widths'=>[10,20,30,40]);

                        $writer = new \XLSXWriter();
                        $writer->writeSheetHeader('Sheet1', $writer_header,  $col_options = ['widths'=>[40,40,40,40,50,50]]);

                        //Reader file
                        $xlsx = new \XLSXReader_fin($newFilePath);
                        $sheetNames = $xlsx->getSheetNames();
                        $data = $xlsx->getSheetData($sheetNames[1]);

                        $arr_header = [];

                        $arr_header['date'] = 0;
                        $arr_header['withdrawals'] = 1;
                        $arr_header['deposits'] = 2;
                        $arr_header['payee'] = 3;
                        $arr_header['description'] = 4;

                        $total_rows = 0;
                        $total_row_false    = 0; 

                        for ($row = 1; $row < count($data); $row++) {

                            $total_rows++;

                            $rd = array();
                            $flag = 0;
                            $flag2 = 0;

                            $string_error ='';
                            $flag_position_group;
                            $flag_department = null;

                            $value_date  = isset($data[$row][$arr_header['date']]) ? $data[$row][$arr_header['date']] : '' ;
                            $value_withdrawals   = isset($data[$row][$arr_header['withdrawals']]) ? $data[$row][$arr_header['withdrawals']] : '' ;
                            $value_deposits     = isset($data[$row][$arr_header['deposits']]) ? $data[$row][$arr_header['deposits']] : '' ;
                            $value_payee    = isset($data[$row][$arr_header['payee']]) ? $data[$row][$arr_header['payee']] : '' ;
                            $value_description   = isset($data[$row][$arr_header['description']]) ? $data[$row][$arr_header['description']] : '' ;
                            
                            $reg_day = '/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/'; /*yyyy-mm-dd*/

                            if(is_numeric($value_date)){
                                $value_date = $this->Accounting_model->convert_excel_date($value_date);
                            }

                            if(is_null($value_date) != true){
                                if(preg_match($reg_day, $value_date, $match) != 1){
                                    $string_error .=_l('date'). _l('invalid');
                                    $flag = 1; 
                                }
                            }else{
                                $string_error .= _l('date') . _l('not_yet_entered');
                                $flag = 1;
                            }

                            if (is_null($value_withdrawals) == true) {
                                $string_error .= _l('withdrawals') . _l('not_yet_entered');
                                $flag = 1;
                            }else{
                                if(!is_numeric($value_withdrawals) && $value_deposits == ''){
                                    $string_error .= _l('withdrawals') . _l('invalid');
                                    $flag = 1;
                                }
                            }

                            if (is_null($value_deposits) == true) {
                                $string_error .= _l('deposits') . _l('not_yet_entered');
                                $flag = 1;
                            }else{
                                if(!is_numeric($value_deposits) && $value_withdrawals == ''){
                                    $string_error .= _l('deposits') . _l('invalid');
                                    $flag = 1;
                                }
                            }

                            if (is_null($value_payee) == true) {
                                $string_error .= _l('payee') . _l('not_yet_entered');
                                $flag = 1;
                            }
                            

                            if(($flag == 1) || $flag2 == 1 ){
                                //write error file
                                $writer->writeSheetRow('Sheet1', [
                                    $value_date,
                                    $value_withdrawals,
                                    $value_deposits,
                                    $value_payee,
                                    $value_description,
                                    $string_error,
                                ]);

                                $total_row_false++;
                            }

                            if($flag == 0 && $flag2 == 0){

                                $rd['date']       = $value_date;
                                $rd['withdrawals']         = $value_withdrawals;
                                $rd['deposits']        = $value_deposits;
                                $rd['payee']       = $value_payee;
                                $rd['bank_id']       = $data_filter['bank_account'];
                                $rd['description']               = $value_description;
                                $rd['datecreated']               = date('Y-m-d H:i:s');
                                $rd['addedfrom']               = $this->login_user->id;

                                $rows[] = $rd;
                                array_push($arr_insert, $rd);

                            }

                        }

                        //insert batch
                        if(count($arr_insert) > 0){
                            $this->Accounting_model->insert_batch_banking($arr_insert);
                        }

                        $total_rows = $total_rows;
                        $total_row_success = isset($rows) ? count($rows) : 0;
                        $dataerror = '';
                        $message ='Not enought rows for importing';

                        if($total_row_false != 0){
                            $filename = 'Import_banking_error_'.strtotime(date('Y-m-d H:i:s')).'.xlsx';
                            $writer->writeToFile(str_replace($filename, ACCOUTING_IMPORT_ITEM_ERROR.$filename, $filename));
                        }


                    }
                }
            }
        }


        if (file_exists($newFilePath)) {
            @unlink($newFilePath);
        }

        echo json_encode([
            'message'           => $message,
            'total_row_success' => $total_row_success,
            'total_row_false'   => $total_row_false,
            'total_rows'        => $total_rows,
            'site_url'          => site_url(),
            'staff_id'          => $this->login_user->id,
            'filename'          => ACCOUTING_IMPORT_ITEM_ERROR.$filename,
        ]);
    }

    public function update_plaid_status(){
        if ($this->request->getPost()) { 
            $bank_id = $_POST['bank_id'];   
                
            $db = db_connect('default');
            $db_builder = $db->table(get_db_prefix() . 'acc_accounts');
            $db_builder->where('id', $bank_id);
            $db_builder->update([
                'plaid_status' => 0
            ]);

            $db_builder = $db->table(get_db_prefix() . 'acc_transaction_bankings');
            $db_builder->where('bank_id', $bank_id);
            $db_builder->delete();

            $db_builder = $db->table(get_db_prefix() . 'acc_plaid_transaction_logs');
            $db_builder->where('bank_id', $bank_id);
            $db_builder->delete();
        }
    }

    public function make_adjusting_transaction_change($id){
        $account_history = $this->Accounting_model->get_account_history($id);
        $withdrawal = 0;
        $deposit = 0;
        if($account_history){
            $withdrawal = to_decimal_format($account_history->credit);
            $deposit = to_decimal_format($account_history->debit);
        }

        echo json_encode(['withdrawal' => $withdrawal, 'deposit' => $deposit]);
    }
}