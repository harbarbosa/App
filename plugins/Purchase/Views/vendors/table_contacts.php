<?php


$total_client_contacts = total_rows(db_prefix() . 'users', ['vendor_id' => $dataPost['vendor'][0] ]);


$aColumns        = [ 'CONCAT(first_name, \' \', last_name) as full_name'];

$aColumns = array_merge($aColumns, [
    'email',
    'job_title',
    'phone',
]);

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'users';
$join         = [];


$where = ['AND deleted = 0 AND vendor_id=' . $dataPost['vendor'][0]];

// Fix for big queries. Some hosting have max_join_limit

$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'users.id as id', 'vendor_id', 'is_primary_contact'],'', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $rowName = $aRow['full_name'];

    $row[] = $rowName;


    $row[] = '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>';

    $row[] = $aRow['job_title'];

    $row[] = '<a href="tel:' . $aRow['phone'] . '">' . $aRow['phone'] . '</a>';

    $edit = '';
    if(has_permission('purchase_vendors','','edit')){
        $edit = '<li role="presentation">'.modal_anchor(get_uri("purchase/vendor_contact_modal_form/".$aRow['vendor_id']."/".$aRow['id']), "<i data-feather='edit' class='icon-16'></i> " . app_lang('edit_contact'), array("class" => "dropdown-item", "title" => app_lang('edit_contact'))).'</li>';
    }

    $delete = '';
    if (has_permission('purchase_vendors', '', 'delete') || is_admin()) {
        if ($aRow['is_primary_contact'] == 0 || ($aRow['is_primary_contact'] == 1 && $total_client_contacts == 1)) {
            $delete = '<li role="presentation">' . modal_anchor(get_uri("purchase/delete_contact_modal"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['id'], "class" => "dropdown-item")) . '</li>';
        }
    }

    $_data = '
    <span class="dropdown inline-block">
    <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
    <i data-feather="tool" class="icon-16"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end" role="menu">' . $edit .  $delete. '</ul>
    </span>';

    $row[] = $_data;

    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
