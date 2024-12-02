<?php
use App\Controllers\Security_Controller;
use App\Controllers\App_Controller;

$purchase_model = model("Purchase\Models\Purchase_model");
$Taxes_model = model("Models\Taxes_model");


$arr_inventory_min_data = [];
$filter_arr_inventory_min_max = [];
$arr_inventory_min_id = [];
$arr_inventory_max_id = [];

$aColumns = [
	'1',
	get_db_prefix() . 'items.id',
	'commodity_code',
	get_db_prefix() . 'items.title',
	get_db_prefix() . 'item_categories.title as group_name',
	'unit_id',
	'rate',
	'purchase_price',
	't1.percentage as taxrate_1',
	't2.percentage as taxrate_2',
	'2'
	
];
$sIndexColumn = 'id';
$sTable = get_db_prefix() . 'items';


$where = [];

$where[] = 'AND '.get_db_prefix().'items.deleted = 0';

if(isset($dataPost['warehouse_ft'])){
	$warehouse_ft = $dataPost['warehouse_ft'];
}
if(isset($dataPost['commodity_ft'])){
	$commodity_ft = $dataPost['commodity_ft'];
}
if(isset($dataPost['alert_filter'])){
	$alert_filter = $dataPost['alert_filter'];
}

if(isset($dataPost['item_filter'])){
	$tags_ft = $dataPost['item_filter'];
}
if(isset($dataPost['parent_item'])){
	$parent_item = $dataPost['parent_item'];
}
if(isset($dataPost['sub_commodity_ft'])){
	$sub_commodity_ft = $dataPost['sub_commodity_ft'];
}
if(isset($dataPost['filter_all_simple_variation'])){
	$filter_all_simple_variation = $dataPost['filter_all_simple_variation'];
}


$join = [
	'LEFT JOIN ' . get_db_prefix() . 'taxes t1 ON t1.id = ' . get_db_prefix() . 'items.tax',
	'LEFT JOIN ' . get_db_prefix() . 'taxes t2 ON t2.id = ' . get_db_prefix() . 'items.tax2',
	'LEFT JOIN ' . get_db_prefix() . 'item_categories ON ' . get_db_prefix() . 'item_categories.id = ' . get_db_prefix() . 'items.category_id',
];

$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, [get_db_prefix() . 'items.id', get_db_prefix() . 'items.title', get_db_prefix() . 'items.unit_id', get_db_prefix() . 'items.commodity_code', get_db_prefix() . 'items.commodity_barcode', get_db_prefix() . 'items.commodity_type', get_db_prefix() . 'items.warehouse_id', get_db_prefix() . 'items.origin', get_db_prefix() . 'items.color_id', get_db_prefix() . 'items.style_id', get_db_prefix() . 'items.model_id', get_db_prefix() . 'items.size_id', get_db_prefix() . 'items.rate', get_db_prefix() . 'items.tax', get_db_prefix() . 'items.category_id',  get_db_prefix() . 'items.sku_code', get_db_prefix() . 'items.sku_name', get_db_prefix() . 'items.sub_group', get_db_prefix() . 'items.color', get_db_prefix() . 'items.guarantee', get_db_prefix().'items.profif_ratio', get_db_prefix().'items.without_checking_warehouse', get_db_prefix().'items.parent_id', get_db_prefix().'items.tax2', 
	get_db_prefix().'items.can_be_sold', get_db_prefix().'items.can_be_purchased', get_db_prefix().'items.can_be_manufacturing', get_db_prefix().'items.can_be_inventory', 'files' ], '', [], $dataPost);

$output = $result['output'];
$rResult = $result['rResult'];


$arr_tax_rate = [];
$tax_options = array(
	"deleted" => 0,
);
$get_tax_rate = $Taxes_model->get_details($tax_options)->getResultArray();
foreach ($get_tax_rate as $key => $value) {
    $arr_tax_rate[$value['id']] = $value;
}

$arr_images =[];
$arr_inventory_min = [];
$arr_warehouse_by_item = [];
$arr_warehouse_id = [];
$arr_unit_id = [];
$get_unit_type = $purchase_model->get_unit_type();
foreach ($get_unit_type as $key => $value) {
   $arr_unit_id[$value['unit_type_id']] = $value;
}
$inventory_min = [];
$arr_inventory_number = [];

$item_have_variation = [];

	foreach ($rResult as $aRow) {
		$row = [];
		for ($i = 0; $i < count($aColumns); $i++) {

			 if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
	            $_data = $aRow[strafter($aColumns[$i], 'as ')];
	        } else {
				$_data = $aRow[$aColumns[$i]];
	        }


			/*get commodity file*/
			if($aColumns[$i] == get_db_prefix() . 'items.id'){

				if ($aRow['files']){

					$files = unserialize($aRow['files']);

					if (count($files) > 0) {
						$timeline_file_path = get_setting("timeline_file_path");
						foreach ($files as $file_key => $file) {
							if($file_key == 0){
								$file_name = get_array_value($file, "file_name");
								$thumbnail = get_source_url_of_file($file, $timeline_file_path, "thumbnail");
								if (is_viewable_image_file($file_name)) {
									$_data = "<img class='sortable-file images_w_table' src='".$thumbnail."' alt='".$file_name."'/>";

								} else {
									$_data = get_file_icon(strtolower(pathinfo($file_name, PATHINFO_EXTENSION)));
								}
							}
						}

					}else{
						$thumbnail = get_file_uri('plugins/Warehouse/Uploads/nul_image.jpg');
						$_data = "<img class='sortable-file images_w_table' src='".$thumbnail."' alt='null_image'/>";
					}
				}else{
					$_data = "<img class='sortable-file images_w_table' src='".base_url('plugins/Purchase/Uploads/nul_image.jpg' )."' alt='null_image'/>";
				}
			}

			if ($aColumns[$i] == 'commodity_code') {
				$code = '<a href="' . site_url('purchase/view_commodity_detail/' . $aRow['id']) . '">' . $aRow['commodity_code'] . '</a>';
				
				$_data = $code;

			}elseif($aColumns[$i] == '1'){
				$_data = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '" class="form-check-input"><label></label></div>';
			} elseif ($aColumns[$i] == get_db_prefix().'item.title') {

				if (isset($arr_inventory_min[$aRow['id']]) && $arr_inventory_min[$aRow['id']] == true) {
					$_data = '<a href="#" class="text-danger"  onclick="show_detail_item(this);return false;" data-name="' . $aRow[get_db_prefix().'item.title'] . '" data-warehouse_id="' . $aRow['warehouse_id'] . '" data-commodity_id="' . $aRow['id'] . '"  >' . $aRow[get_db_prefix().'item.title'] . '</a>';
				} else {

					$_data = '<a href="#" onclick="show_detail_item(this);return false;" data-name="' . $aRow[get_db_prefix().'item.title'] . '"  data-commodity_id="' . $aRow['id'] . '"  >' . $aRow[get_db_prefix().'item.title'] . '</a>';
				}

			}elseif ($aColumns[$i] == 'group_name') {
				$_data = $aRow['group_name'];

			}  elseif ($aColumns[$i] == 'unit_id') {
				if ($aRow['unit_id'] != null) {
					if(isset($arr_unit_id[$aRow['unit_id']])){
						$_data = $arr_unit_id[$aRow['unit_id']]['unit_name'];
					}else{
						$_data = '';
					}
				} else {
					$_data = '';
				}
			} elseif ($aColumns[$i] == 'rate') {
				$_data = to_decimal_format((float) $aRow['rate']);
			} elseif ($aColumns[$i] == 'purchase_price') {
				$_data = to_decimal_format((float) $aRow['purchase_price']);

			} elseif ($aColumns[$i] == 'taxrate_1') {
				$aRow['taxrate_1'] = $aRow['taxrate_1'] ?? 0;
				$_data             = '<span data-toggle="tooltip" title="' . $aRow['taxname_1'] . '" data-taxid="' . $aRow['tax_id_1'] . '">' . app_format_number($aRow['taxrate_1']) . '%' . '</span>';
			} elseif ($aColumns[$i] == 'taxrate_2') {
				$aRow['taxrate_2'] = $aRow['taxrate_2'] ?? 0;
				$_data             = '<span data-toggle="tooltip" title="' . $aRow['taxname_2'] . '" data-taxid="' . $aRow['tax_id_2'] . '">' . app_format_number($aRow['taxrate_2']) . '%' . '</span>';

			} else if($aColumns[$i] == '2'){
				$edit = '<li role="presentation">' . modal_anchor(get_uri("purchase/item_modal_form"), "<i data-feather='edit' class='icon-16'></i> " . app_lang('edit'), array("title" => app_lang('edit_item'), "data-post-id" => $aRow['id'], "class" => "dropdown-item")) . '</li>';

				$delete = '<li role="presentation">' . modal_anchor(get_uri("purchase/delete_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['id'], "class" => "dropdown-item")) . '</li>';


				$_data = '
				<span class="dropdown inline-block">
				<button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
				<i data-feather="tool" class="icon-16"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-end" role="menu">' . $edit . $delete. '</ul>
				</span>';
			}	


			$row[] = $_data;

		}
		$output['aaData'][] = $row;
	}

