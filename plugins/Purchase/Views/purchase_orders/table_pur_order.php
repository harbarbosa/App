<?php

$aColumns = [
    'pur_order_number',
    'vendor',
    'order_date',
    'type',
    'project',
    'department',
    'pur_order_name',
    'subtotal',
    'total_tax',
    'total',

    'approve_status',
    'delivery_date',
    'delivery_status',
    'number',
    'expense_convert',
    ];

if(isset($dataPost['vendor_profile_id']) || isset($project)){
    $aColumns = [
    'pur_order_number',
    'total',
    'total_tax',
    'vendor', 
    'order_date',
    'number',
    'approve_status',
    
    ];
}

$sIndexColumn = 'id';
$sTable       = db_prefix().'pur_orders';
$join         = [
                    'LEFT JOIN '.db_prefix().'pur_vendor ON '.db_prefix().'pur_vendor.userid = '.db_prefix().'pur_orders.vendor',
                    'LEFT JOIN '.db_prefix().'team ON '.db_prefix().'team.id = '.db_prefix().'pur_orders.department',
                    'LEFT JOIN '.db_prefix().'projects ON '.db_prefix().'projects.id = '.db_prefix().'pur_orders.project',
                ];


$where = [];

if(isset($dataPost['from_date']) && $dataPost['from_date'] != ''){ 

  array_push($where, 'AND date(order_date) >= "'.$dataPost['from_date'].'"');
}

if(isset($dataPost['to_date']) && $dataPost['to_date'] != ''){ 
   array_push($where, 'AND date(order_date) <= "'.$dataPost['to_date'].'"');
}

if(isset($dataPost['purchase_request']) && $dataPost['purchase_request'] != ''){ 
  array_push($where, 'AND pur_request IN  ('.implode(',', $dataPost['purchase_request']).')');
}

if(isset($dataPost['vendor']) && $dataPost['vendor'] != ''){ 
   array_push($where, 'AND vendor IN ('.implode(',', $dataPost['vendor']).')');
}

if(isset($dataPost['vendor_profile_id']) && $dataPost['vendor_profile_id'] != ''){ 
   array_push($where, 'AND vendor IN ('.implode(',', $dataPost['vendor_profile_id']).')');
}

if($dataPost['user_type'] == 'vendor'){
    $vendor_id = get_vendor_user_id();
    array_push($where, 'AND vendor = '.$vendor_id);
}

$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix().'pur_orders.id as id','company','pur_order_number','expense_convert',db_prefix().'projects.title as project_name',db_prefix().'team.title as department_name', 'currency'],'', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

   for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }

        $base_currency = get_base_currency();
        if($aRow['currency'] != ''){
            $base_currency = $aRow['currency'];
        }

        if($aColumns[$i] == 'total'){
            $_data = to_currency($aRow['total'], $base_currency);
        }elseif($aColumns[$i] == 'pur_order_number'){

            $numberOutput = '';
    
            $numberOutput = '<a href="' . get_uri('purchase/view_pur_order/' . $aRow['id']) . '"  >'.$aRow['pur_order_number']. '</a>';
            
            $_data = $numberOutput;

        }elseif($aColumns[$i] == 'vendor'){
            $_data = '<a href="' . get_uri('purchase/vendor/' . $aRow['vendor']) . '" >' .  $aRow['company'] . '</a>';
        }elseif ($aColumns[$i] == 'order_date') {
            $_data = _d($aRow['order_date']);
        }elseif($aColumns[$i] == 'approve_status'){
            $_data = get_status_approve($aRow['approve_status']);
        }elseif($aColumns[$i] == 'total_tax'){
            $_data = to_currency($aRow['total_tax'], $base_currency);
        }elseif($aColumns[$i] == 'expense_convert'){

          if($dataPost['user_type'] == 'staff'){
              $edit = '';
              if($aRow['approve_status'] != 2){
                $edit = '<li role="presentation"><a href="'.get_uri('purchase/pur_order/'. $aRow['id']).'" class="dropdown-item"><i data-feather="edit" class="icon-16"></i>&nbsp;&nbsp;'.app_lang('edit').'</a></li>';
              }

              $delete = '<li role="presentation">' . modal_anchor(get_uri("purchase/delete_pur_order_modal"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['id'], "class" => "dropdown-item")) . '</li>';

              if($aRow['expense_convert'] == 0){
               $convert = '<li role="presentation">'.modal_anchor(get_uri("purchase/convert_expense_modal_form/".$aRow['id']), "<i data-feather='repeat' class='icon-16'></i> " . app_lang('convert'), array("class" => "dropdown-item", "title" => app_lang('convert_expense'))).'</li>';
              }else{
                  $convert = modal_anchor(get_uri("expenses/expense_details"), "<i data-feather='eye' class='icon-16'></i> " . app_lang('view_expense'), array("title" => app_lang("expense_details"), "data-post-id" => $aRow['expense_convert'], 'class' => 'dropdown-item'));
              }

              $_data = '
              <span class="dropdown inline-block">
              <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
              <i data-feather="tool" class="icon-16"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" role="menu">' .$convert . $edit .  $delete. '</ul>
              </span>';
          }else if($dataPost['user_type'] == 'vendor'){
              $view = '<li role="presentation"><a href="'.get_uri('purchase/view_pur_order/'. $aRow['id']).'" class="dropdown-item"><i data-feather="eye" class="icon-16"></i>&nbsp;&nbsp;'.app_lang('view').'</a></li>';

              $_data = '
              <span class="dropdown inline-block">
              <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
              <i data-feather="tool" class="icon-16"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" role="menu">'.  $view. '</ul>
              </span>';
          }

        }elseif($aColumns[$i] == 'type'){
            $_data = ($aRow['type'] != '' ? _l($aRow['type']) : '' );
        }elseif($aColumns[$i] == 'subtotal'){
            $_data = to_currency($aRow['subtotal'],$base_currency);
        }elseif($aColumns[$i] == 'project'){
            $_data = $aRow['project_name'];
        }elseif($aColumns[$i] == 'department'){
            $_data = $aRow['department_name'];
        }elseif($aColumns[$i] == 'delivery_status'){
            $delivery_status = '';

            if($aRow['delivery_status'] == 0){
                $delivery_status = '<span class="inline-block label label-danger" id="status_span_'.$aRow['id'].'" task-status-table="undelivered">'._l('undelivered');
            }else if($aRow['delivery_status'] == 1){
                $delivery_status = '<span class="inline-block label label-success" id="status_span_'.$aRow['id'].'" task-status-table="completely_delivered">'._l('completely_delivered');
            }else if($aRow['delivery_status'] == 2){
                $delivery_status = '<span class="inline-block label label-info" id="status_span_'.$aRow['id'].'" task-status-table="pending_delivered">'._l('pending_delivered');
            }else if($aRow['delivery_status'] == 3){
                $delivery_status = '<span class="inline-block label label-warning" id="status_span_'.$aRow['id'].'" task-status-table="partially_delivered">'._l('partially_delivered');
            }
            
            if(has_permission('purchase_orders', '', 'edit') || is_admin()){
                $delivery_status .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
                $delivery_status .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                $delivery_status .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
                $delivery_status .= '</a>';

                $delivery_status .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderStatus-' . $aRow['id'] . '">';

                if($aRow['delivery_status'] == 0){
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 1 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('completely_delivered') . '
                              </a>
                           </li>';
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 2 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('pending_delivered') . '
                              </a>
                           </li>';
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 3 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('partially_delivered') . '
                              </a>
                           </li>';
                }else if($aRow['delivery_status'] == 1){
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 0 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('undelivered') . '
                              </a>
                           </li>';
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 2 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('pending_delivered') . '
                              </a>
                           </li>';
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 3 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('partially_delivered') . '
                              </a>
                           </li>';

                }else if($aRow['delivery_status'] == 2) {
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 0 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('undelivered') . '
                              </a>
                           </li>';
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 1 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('completely_delivered') . '
                              </a>
                           </li>';
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 3 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('partially_delivered') . '
                              </a>
                           </li>';
                }else if($aRow['delivery_status'] == 3){
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 0 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('undelivered') . '
                              </a>
                           </li>';
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 1 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('completely_delivered') . '
                              </a>
                           </li>';
                    $delivery_status .= '<li>
                              <a href="#" onclick="change_delivery_status( 2 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('pending_delivered') . '
                              </a>
                           </li>';
                }

                $delivery_status .= '</ul>';
                $delivery_status .= '</div>';
                
            }
            $delivery_status .= '</span>';
            $_data = $delivery_status;
        }elseif($aColumns[$i] == 'delivery_date'){
            $_data = _d($aRow['delivery_date']);
        }else if($aColumns[$i] == 'number'){
            $paid = $aRow['total'] - purorder_inv_left_to_pay($aRow['id']);

            $percent = 0;

            if($aRow['total'] > 0){

                $percent = ($paid / $aRow['total'] ) * 100;

            }

            

            $_data = '<div class="progress">

                          <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40"

                          aria-valuemin="0" aria-valuemax="100" style="width:'.round($percent).'%">

                           ' .round($percent).' % 

                          </div>

                        </div>';

        }

        $row[] = $_data;
    }
    $output['aaData'][] = $row;

}
