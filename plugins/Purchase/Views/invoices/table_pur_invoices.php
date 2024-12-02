<?php

$aColumns = [
    'invoice_number',
    db_prefix().'pur_invoices.vendor',
    db_prefix().'pur_invoices.pur_order',
    'invoice_date',
    'subtotal',
    'tax', 
    'total',
    'payment_request_status',
    'payment_status',
    'transactionid',
    db_prefix().'pur_invoices.id',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'pur_invoices';
$join         = [  ];

$i = 0;

$where = [];

if(isset($dataPost['from_date']) && $dataPost['from_date'] != ''){ 

  array_push($where, 'AND date(invoice_date) >= "'.$dataPost['from_date'].'"');
}

if(isset($dataPost['to_date']) && $dataPost['to_date'] != ''){ 
   array_push($where, 'AND date(invoice_date) <= "'.$dataPost['to_date'].'"');
}

if(isset($dataPost['vendor']) && $dataPost['vendor'] != ''){ 
   array_push($where, 'AND vendor IN ('.implode(',', $dataPost['vendor']).')');
}

if(isset($dataPost['pur_orders']) && $dataPost['pur_orders'] != ''){ 
   array_push($where, 'AND pur_order IN ('.implode(',', $dataPost['pur_orders']).')');
}

if($dataPost['user_type'] == 'vendor'){
    $vendor_id = get_vendor_user_id();
    array_push($where, 'AND vendor = '.$vendor_id);
}

$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix().'pur_invoices.id as id', 'invoice_number', 'currency'
],'', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

   for ($i = 0; $i < count($aColumns); $i++) {

        $base_currency = get_base_currency();
        if($aRow['currency'] != ''){
            $base_currency = $aRow['currency'];
        }

        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if($aColumns[$i] == 'invoice_number'){
            $numberOutput = '';
    
            $numberOutput = '<a href="' . get_uri('purchase/purchase_invoice/' . $aRow['id']) . '"  >'.$aRow['invoice_number']. '</a>';


            $_data = $numberOutput;
        }elseif($aColumns[$i] == 'invoice_date'){
            $_data = _d($aRow['invoice_date']);
        }elseif($aColumns[$i] == 'subtotal'){
            $_data = to_currency($aRow['subtotal'],$base_currency);
        }elseif($aColumns[$i] == 'tax'){
            $_data = to_currency($aRow['tax'],$base_currency);
        }elseif($aColumns[$i] == 'total'){
            $_data = to_currency($aRow['total'],$base_currency);
        }elseif($aColumns[$i] == 'payment_status'){
            $class = '';
            if($aRow['payment_status'] == 'unpaid'){
                $class = 'danger';
            }elseif($aRow['payment_status'] == 'paid'){
                $class = 'success';
            }elseif ($aRow['payment_status'] == 'partially_paid') {
                $class = 'warning';
            }

            $_data = '<span class="label label-'.$class.' s-status invoice-status-3">'._l($aRow['payment_status']).'</span>';
        }elseif($aColumns[$i] == 'payment_request_status'){
            $_data = '';//get_payment_request_status_by_inv($aRow['id']);
        }elseif($aColumns[$i] == db_prefix().'pur_invoices.pur_order'){
            $_data = '<a href="'.get_uri('purchase/view_pur_order/'.$aRow[db_prefix().'pur_invoices.pur_order']).'">'.get_pur_order_subject($aRow[ db_prefix().'pur_invoices.pur_order']).'</a>';
        }elseif($aColumns[$i] == db_prefix().'pur_invoices.vendor'){
            $_data = '<a href="' . get_uri('purchase/vendor/' . $aRow[db_prefix().'pur_invoices.vendor']) . '" >' .  get_vendor_company_name($aRow[db_prefix().'pur_invoices.vendor']) . '</a>'; 
        }elseif($aColumns[$i] == db_prefix().'pur_invoices.id'){
            $_data = '';

            $edit = '';
            $edit = '<li role="presentation"><a href="'.get_uri('purchase/pur_invoice/'. $aRow['id']).'" class="dropdown-item"><i data-feather="edit" class="icon-16"></i>&nbsp;&nbsp;'.app_lang('edit').'</a></li>';
            
            $delete = '<li role="presentation">' . modal_anchor(get_uri("purchase/delete_pur_invoice_modal"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['id'], "class" => "dropdown-item")) . '</li>';


            $_data = '
            <span class="dropdown inline-block">
            <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
            <i data-feather="tool" class="icon-16"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" role="menu">' .$edit .  $delete. '</ul>
            </span>';
        }

        $row[] = $_data;
    }
    $output['aaData'][] = $row;

}
