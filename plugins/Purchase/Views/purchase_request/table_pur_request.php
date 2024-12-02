<?php


$aColumns = [
     
    'pur_rq_code',
    'pur_rq_name',
    'requester',
    'department', 
    'request_date',
    db_prefix().'pur_request.status',
    db_prefix().'pur_request.id',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'pur_request';
$join         = [ 
                  'LEFT JOIN '.db_prefix().'team ON '.db_prefix().'team.id = '.db_prefix().'pur_request.department',
                  'LEFT JOIN '.db_prefix().'users ON '.db_prefix().'users.id = '.db_prefix().'pur_request.requester',
               ];
$where = [];

if(isset($dataPost['from_date']) && $dataPost['from_date'] != ''){ 

  array_push($where, 'AND date(request_date) >= "'.$dataPost['from_date'].'"');
}

if(isset($dataPost['to_date']) && $dataPost['to_date'] != ''){ 
   array_push($where, 'AND date(request_date) <= "'.$dataPost['to_date'].'"');
}

if(isset($dataPost['department']) && $dataPost['department'] != ''){ 
   array_push($where, 'AND department = "'.$dataPost['department'].'"');
}

if($dataPost['user_type'] == 'vendor'){
  $vendor_id = get_vendor_user_id();
  array_push($where, 'AND find_in_set('.$vendor_id.', send_to_vendors) AND '.db_prefix().'pur_request.status = 2');
}

$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix().'pur_request.id','pur_rq_code', 'title', 'first_name', 'last_name'], '', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

   for ($i = 0; $i < count($aColumns); $i++) {

        $_data = $aRow[$aColumns[$i]];
        if($aColumns[$i] == 'request_date'){
            $_data = format_to_date($aRow['request_date'], false);
        }elseif($aColumns[$i] == 'requester'){
            $_data = $aRow['first_name'].' '. $aRow['last_name'];
        }elseif($aColumns[$i] == 'department'){
            $_data = $aRow['title'];
        }elseif ($aColumns[$i] == db_prefix().'pur_request.status') {
            
            $approve_status = get_status_approve($aRow[db_prefix().'pur_request.status']);

            if($aRow[db_prefix().'pur_request.status'] == 1){
                $approve_status = '<span class="label label-primary" id="status_span_'.$aRow['id'].'"> '._l('purchase_draft');
            }elseif($aRow[db_prefix().'pur_request.status'] == 2){
                $approve_status = '<span class="label label-success" id="status_span_'.$aRow['id'].'"> '._l('purchase_approved');
            }elseif($aRow[db_prefix().'pur_request.status'] == 3){
                $approve_status = '<span class="label label-warning" id="status_span_'.$aRow['id'].'"> '._l('pur_rejected');
            }elseif($aRow[db_prefix().'pur_request.status'] == 4){
                $approve_status = '<span class="label label-danger" id="status_span_'.$aRow['id'].'"> '._l('pur_canceled');
            }

            if(is_admin()){
                $approve_status .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
                $approve_status .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                $approve_status .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
                $approve_status .= '</a>';

                $approve_status .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderStatus-' . $aRow['id'] . '">';

                if($aRow[db_prefix().'pur_request.status'] == 1){
                   
                    $approve_status .= '<li>
                              <a href="#" onclick="change_pr_approve_status( 2 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('purchase_approved') . '
                              </a>
                           </li>';
                    $approve_status .= '<li>
                              <a href="#" onclick="change_pr_approve_status( 3 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('pur_rejected') . '
                              </a>
                           </li>';
                }else if($aRow[db_prefix().'pur_request.status'] == 2){
                    $approve_status .= '<li>
                              <a href="#" onclick="change_pr_approve_status( 1 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('purchase_draft') . '
                              </a>
                           </li>';
                   
                    $approve_status .= '<li>
                              <a href="#" onclick="change_pr_approve_status( 3 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('pur_rejected') . '
                              </a>
                           </li>';

                }else if($aRow[db_prefix().'pur_request.status'] == 3) {
                   
                    $approve_status .= '<li>
                              <a href="#" onclick="change_pr_approve_status( 1 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('purchase_draft') . '
                              </a>
                           </li>';
                    $approve_status .= '<li>
                              <a href="#" onclick="change_pr_approve_status( 2 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('purchase_approved') . '
                              </a>
                           </li>';
                }

                $approve_status .= '</ul>';
                $approve_status .= '</div>';
            }

            $approve_status .= '</span>';

            $_data = $approve_status;

        }elseif($aColumns[$i] == 'pur_rq_name'){
            $name = '<a href="' . get_uri('purchase/view_pur_request/' . $aRow['id'] ).'">'.$aRow['pur_rq_name'] . '</a>';

           

            $_data = $name;
        }elseif($aColumns[$i] ==  db_prefix().'pur_request.id'){
            

            $share_request = '';

            if($aRow[db_prefix().'pur_request.status'] == 2){
              $share_request = '<li role="presentation">' . modal_anchor(get_uri("purchase/share_request_modal/".$aRow['id']), "<i data-feather='share-2' class='icon-16'></i> " . app_lang('share_pur_request'), array("title" => app_lang('share_request'), "data-post-id" => $aRow['id'], "class" => "dropdown-item")) . '</li>';
            }

            $view = '<li role="presentation"><a href="'.get_uri('purchase/view_pur_request/'. $aRow['id']).'" class="dropdown-item"><i data-feather="eye" class="icon-16"></i>&nbsp;&nbsp;'.app_lang('view').'</a></li>';

            $edit = '';
            if($aRow[db_prefix().'pur_request.status'] != 2){
              $edit = '<li role="presentation"><a href="'.get_uri('purchase/pur_request/'. $aRow['id']).'" class="dropdown-item"><i data-feather="edit" class="icon-16"></i>&nbsp;&nbsp;'.app_lang('edit').'</a></li>';
            }

            $delete = '';
            if(is_admin()){
              $delete = '<li role="presentation">' . modal_anchor(get_uri("purchase/delete_pur_request_modal"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['id'], "class" => "dropdown-item")) . '</li>';
            }

            if($dataPost['user_type'] == 'staff'){
              $_data = '
              <span class="dropdown inline-block">
              <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
              <i data-feather="tool" class="icon-16"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" role="menu">' . $view . $share_request . $edit .  $delete. '</ul>
              </span>';
            }else if($dataPost['user_type'] == 'vendor'){
              $_data = '
              <span class="dropdown inline-block">
              <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
              <i data-feather="tool" class="icon-16"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" role="menu">' . $view. '</ul>
              </span>';
            }


        }elseif($aColumns[$i] == 'pur_rq_code'){
            $_data = '<a href="' . get_uri('purchase/view_pur_request/' . $aRow['id'] ).'">'.$aRow['pur_rq_code'] . '</a>';
        }


        $row[] = $_data;
    }
    $output['aaData'][] = $row;

}
