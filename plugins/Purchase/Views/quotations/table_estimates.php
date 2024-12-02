<?php

$aColumns = [
    db_prefix() . 'pur_estimates.number',
    db_prefix() . 'pur_estimates.total',
    db_prefix() . 'pur_estimates.total_tax',

    'vendor',
    'pur_request',
    
    'date',
    'expirydate',

    db_prefix() . 'pur_estimates.status',
    db_prefix().'pur_estimates.id',
    ];

$join = [
    
    'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'pur_estimates.vendor',
    'LEFT JOIN ' . db_prefix() . 'pur_request ON ' . db_prefix() . 'pur_request.id = ' . db_prefix() . 'pur_estimates.pur_request',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'pur_estimates';


$where  = [];

if(isset($dataPost['pur_request']) && $dataPost['pur_request'] != ''){ 
   array_push($where, 'AND pur_request IN ('.implode(',', $dataPost['pur_request']).')');
}

if(isset($dataPost['vendor']) && $dataPost['vendor'] != ''){ 
   array_push($where, 'AND vendor IN ('.implode(',', $dataPost['vendor']).')');
}

if($dataPost['user_type'] == 'vendor'){
    $vendor_id = get_vendor_user_id();
    array_push($where, 'AND vendor = '.$vendor_id);
}

$filter = [];

$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, [
    db_prefix() . 'pur_estimates.id',
    db_prefix() . 'pur_estimates.vendor',
    db_prefix() . 'pur_estimates.invoiceid',
  
    'pur_request',
    'deleted_vendor_name',
    db_prefix() . 'pur_estimates.currency',
    'company',
    'pur_rq_name',
    'pur_rq_code'
],'', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $base_currency = get_base_currency();

    if($aRow['currency'] != ''){
        $base_currency = $aRow['currency'];
    }

    $numberOutput = '';
    // If is from client area table or projects area request
    
    $numberOutput = '<a href="' . get_uri('purchase/view_quotation/' . $aRow['id']) . '" >' . format_pur_estimate_number($aRow['id']) . '</a>';

    

    $row[] = $numberOutput;

    $amount = to_currency($aRow[db_prefix() . 'pur_estimates.total'], $base_currency);

    if ($aRow['invoiceid']) {
        $amount .= '<br /><span class="hide"> - </span><span class="text-success">' . _l('estimate_invoiced') . '</span>';
    }

    $row[] = $amount;

    $row[] = to_currency($aRow[db_prefix() . 'pur_estimates.total_tax'], $base_currency);


    $row[] = '<a href="' . get_uri('purchase/vendor/' . $aRow['vendor']) . '" >' .  $aRow['company'] . '</a>';


    $row[] = '<a href="' . get_uri('purchase/view_pur_request/' . $aRow['pur_request']) . '" >' . $aRow['pur_rq_code'] .'</a>' ;
   

    $row[] = _d($aRow['date']);

    $row[] = _d($aRow['expirydate']);

    $row[] = get_status_approve($aRow[db_prefix() . 'pur_estimates.status']);

    $edit = '';
      if($aRow[db_prefix() . 'pur_estimates.status'] != 2){
        $edit = '<li role="presentation"><a href="'.get_uri('purchase/estimate/'. $aRow['id']).'" class="dropdown-item"><i data-feather="edit" class="icon-16"></i>&nbsp;&nbsp;'.app_lang('edit').'</a></li>';
    }

    $delete = '';
    if($dataPost['user_type'] == 'vendor'){
        if($aRow[db_prefix() . 'pur_estimates.status'] != 2){
            $delete = '<li role="presentation">' . modal_anchor(get_uri("purchase/delete_estimate_modal"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['id'], "class" => "dropdown-item")) . '</li>';
        }
    }else{
        $delete = '<li role="presentation">' . modal_anchor(get_uri("purchase/delete_estimate_modal"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['id'], "class" => "dropdown-item")) . '</li>';
    }
    
    if($edit != '' || $delete != ''){
        $_data = '
        <span class="dropdown inline-block">
        <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
        <i data-feather="tool" class="icon-16"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" role="menu">' . $edit .  $delete. '</ul>
        </span>';
    }else{
        $_data = '';
    }

    $row[] = $_data;

    $row['DT_RowClass'] = 'has-row-options';

    $output['aaData'][] = $row;
}

echo json_encode($output);
die();
