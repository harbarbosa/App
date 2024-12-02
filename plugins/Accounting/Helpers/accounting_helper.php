<?php
use App\Controllers\Security_Controller;

/**
 * check account exists
 * @param  string $key_name 
 * @return boolean or integer           
 */
if (!function_exists('acc_account_exists')) {
  function acc_account_exists($key_name){
    $db = db_connect('default');

    $Accounting_model = model("Accounting\Models\Accounting_model");
    if(get_setting('acc_add_default_account') == 0){
      $Accounting_model->add_default_account();
    }

    $sql = 'select * from '.get_db_prefix().'acc_accounts where key_name = "'.$key_name.'"';
    $account = $db->query($sql)->getRow();

    if($account){
      return $account->id;
    }else{
      return false;
    }
  }
}

/**
 * Gets the account type by name.
 *
 * @param        $name   The name
 */
if (!function_exists('get_account_type_by_name')) {
function get_account_type_by_name($name){
	$CI             = &get_instance();
	$CI->load->model('accounting/accounting_model');
	$account_types = $CI->accounting_model->get_account_types();
	
	foreach($account_types as $type){
		if($type['name'] == $name){
			return $type['id'];
		}
	}

	return false;
}
}

/**
 * Gets the account type by name.
 *
 * @param        $name   The name
 */
if (!function_exists('get_account_sub_type_by_name')) {
function get_account_sub_type_by_name($name){
	$CI             = &get_instance();
	$CI->load->model('accounting/accounting_model');
	$account_sub_types = $CI->accounting_model->get_account_type_details();

	foreach($account_sub_types as $type){
		if($type['name'] == $name){
			return $type['id'];
		}
	}

	return false;
}
}

/**
 * Gets the account by name.
 *
 * @param        $name     The name
 */
if (!function_exists('get_account_by_name')) {
function get_account_by_name($name){
	$CI             = &get_instance();
	$CI->db->where('name', $name);
	$CI->db->where('name IS NOT NULL');
	$CI->db->where('name <> ""');

	$account = $CI->db->get(db_prefix().'acc_accounts')->row();

	if($account){
		return $account->id;
	}
	return false;
}
}

/**
 * Gets the account type by id.
 *
 * @param        $id   The id
 */
if (!function_exists('get_account_type_by_id')) {
function get_account_type_by_id($id){
	$CI             = &get_instance();
	$CI->load->model('accounting/accounting_model');
	$account_types = $CI->accounting_model->get_account_types();

	foreach($account_types as $type){
		if($type['id'] == $id){
			return $type['id'];
		}
	}

	return false;
}
}
/**
 * Gets the account type by id.
 *
 * @param        $id   The id
 */
if (!function_exists('get_account_sub_type_by_id')) {
function get_account_sub_type_by_id($id){
	$CI             = &get_instance();
	$CI->load->model('accounting/accounting_model');
	$account_sub_types = $CI->accounting_model->get_account_type_details();

	foreach($account_sub_types as $type){
		if($type['id'] == $id){
			return $type['id'];
		}
	}

	return false;
}
}

/**
 * Gets the account by identifier.
 *
 * @param        $id     The identifier
 */
if (!function_exists('get_account_by_id')) {
function get_account_by_id($id){
	$CI             = &get_instance();
	$CI->db->where('id', $id);
	$account = $CI->db->get(db_prefix().'acc_accounts')->row();

	if($account){
		return $id;
	}
	return false;
}
}


/**
 * Add setting
 *
 * @since  Version 1.0.0
 *
 * @param string  $name      Option name (required|unique)
 * @param string  $value     Option value
 *
 */

if (!function_exists('add_setting')) {

  function add_setting($name, $value = '')
  {
      if (!setting_exists($name)) {
        $db = db_connect('default');
        $db_builder = $db->table(get_db_prefix() . 'settings');
        $newData = [
                'setting_name'  => $name,
                'setting_value' => $value,
            ];

        $db_builder->insert($newData);

        $insert_id = $db->insertID();

        if ($insert_id) {
            return true;
        }

        return false;
      }

      return false;
  }
}

/**
 * @since  1.0.0
 * Check whether an setting exists
 *
 * @param  string $name setting name
 *
 * @return boolean
 */
if (!function_exists('setting_exists')) {

  function setting_exists($name)
  { 
   
    $db = db_connect('default');
    $db_builder = $db->table(get_db_prefix() . 'settings');

    $count = $db_builder->where('setting_name', $name)->countAllResults();

    return $count > 0;
  }
}


/**
 * General function for all datatables, performs search,additional select,join,where,orders
 * @param  array $aColumns           table columns
 * @param  mixed $sIndexColumn       main column in table for bettter performing
 * @param  string $sTable            table name
 * @param  array  $join              join other tables
 * @param  array  $where             perform where in query
 * @param  array  $additionalSelect  select additional fields
 * @param  string $sGroupBy group results
 * @return array
 */
if (!function_exists('data_tables_init')) {

	function data_tables_init($aColumns, $sIndexColumn, $sTable, $join = [], $where = [], $additionalSelect = [], $sGroupBy = '', $searchAs = [])
	{
    	$db = db_connect('default');
    	$request = \Config\Services::request();

	    $__post      = $request->getPost();


	    $havingCount = '';
	    /*
	     * Paging
	     */
	    $sLimit = '';
	    if ((is_numeric($request->getPost('start'))) && $request->getPost('length') != '-1') {
	        $sLimit = 'LIMIT ' . intval($request->getPost('start')) . ', ' . intval($request->getPost('length'));
	    }
	    $_aColumns = [];
	    foreach ($aColumns as $column) {
	        // if found only one dot
	        if (substr_count($column, '.') == 1 && strpos($column, ' as ') === false) {
	            $_column = explode('.', $column);
	            if (isset($_column[1])) {
	                if (startsWith($_column[0], get_db_prefix())) {
	                    $_prefix = prefixed_table_fields_wildcard($_column[0], $_column[0], $_column[1]);
	                    array_push($_aColumns, $_prefix);
	                } else {
	                    array_push($_aColumns, $column);
	                }
	            } else {
	                array_push($_aColumns, $_column[0]);
	            }
	        } else {
	            array_push($_aColumns, $column);
	        }
	    }

	    /*
	     * Ordering
	     */
	    $nullColumnsAsLast = get_null_columns_that_should_be_sorted_as_last();

	    $sOrder = '';
	    if ($request->getPost('order')) {
	        $sOrder = 'ORDER BY ';
	        foreach ($request->getPost('order') as $key => $val) {
	            $columnName = $aColumns[intval($__post['order'][$key]['column'])];
	            $dir        = strtoupper($__post['order'][$key]['dir']);

	            if (strpos($columnName, ' as ') !== false) {
	                $columnName = strbefore($columnName, ' as');
	            }

	            // first checking is for eq tablename.column name
	            // second checking there is already prefixed table name in the column name
	            // this will work on the first table sorting - checked by the draw parameters
	            // in future sorting user must sort like he want and the duedates won't be always last
	            if ((in_array($sTable . '.' . $columnName, $nullColumnsAsLast)
	                || in_array($columnName, $nullColumnsAsLast))
	                ) {
	                $sOrder .= $columnName . ' IS NULL ' . $dir . ', ' . $columnName;
	            } else {
	                $sOrder .= hooks()->apply_filters('datatables_query_order_column', $columnName, $sTable);
	            }
	            $sOrder .= ' ' . $dir . ', ';
	        }
	        if (trim($sOrder) == 'ORDER BY') {
	            $sOrder = '';
	        }
	        $sOrder = rtrim($sOrder, ', ');

	        if (get_option('save_last_order_for_tables') == '1'
	            && $request->getPost('last_order_identifier')
	            && $request->getPost('order')) {
	            // https://stackoverflow.com/questions/11195692/json-encode-sparse-php-array-as-json-array-not-json-object

	            $indexedOnly = [];
	            foreach ($request->getPost('order') as $row) {
	                $indexedOnly[] = array_values($row);
	            }

	            $meta_name = $request->getPost('last_order_identifier') . '-table-last-order';

	            update_staff_meta(get_staff_user_id(), $meta_name, json_encode($indexedOnly, JSON_NUMERIC_CHECK));
	        }
	    }
	    /*
	     * Filtering
	     * NOTE this does not match the built-in DataTables filtering which does it
	     * word by word on any field. It's possible to do here, but concerned about efficiency
	     * on very large tables, and MySQL's regex functionality is very limited
	     */
	    $sWhere = '';
	    if ((isset($__post['search'])) && $__post['search']['value'] != '') {
	        $search_value = $__post['search']['value'];
	        $search_value = trim($search_value);

	        $sWhere             = 'WHERE (';
	        $sMatchCustomFields = [];
	        // Not working, do not use it
	        $useMatchForCustomFieldsTableSearch = hooks()->apply_filters('use_match_for_custom_fields_table_search', 'false');

	        for ($i = 0; $i < count($aColumns); $i++) {
	            $columnName = $aColumns[$i];
	            if (strpos($columnName, ' as ') !== false) {
	                $columnName = strbefore($columnName, ' as');
	            }
	        }

	        if (count($sMatchCustomFields) > 0) {
	            $s = $db->escape_like_str($search_value);
	            foreach ($sMatchCustomFields as $matchCustomField) {
	                $sWhere .= "MATCH ({$matchCustomField}) AGAINST (CONVERT(BINARY('{$s}') USING utf8)) OR ";
	            }
	        }

	        if (count($additionalSelect) > 0) {
	            foreach ($additionalSelect as $searchAdditionalField) {
	                if (strpos($searchAdditionalField, ' as ') !== false) {
	                    $searchAdditionalField = strbefore($searchAdditionalField, ' as');
	                }
	                if (stripos($columnName, 'AVG(') !== false || stripos($columnName, 'SUM(') !== false) {
	                } else {
	                    // Use index
	                    $sWhere .= 'convert(' . $searchAdditionalField . ' USING utf8)' . " LIKE '%" . $CI->db->escape_like_str($search_value) . "%' OR ";
	                }
	            }
	        }
	        $sWhere = substr_replace($sWhere, '', -3);
	        $sWhere .= ')';
	    } else {
	        // Check for custom filtering
	        $searchFound = 0;
	        $sWhere      = 'WHERE (';
	        
	        if ($searchFound > 0) {
	            $sWhere = substr_replace($sWhere, '', -3);
	            $sWhere .= ')';
	        } else {
	            $sWhere = '';
	        }
	    }

	    /*
	     * SQL queries
	     * Get data to display
	     */
	    $_additionalSelect = '';
	    if (count($additionalSelect) > 0) {
	        $_additionalSelect = ',' . implode(',', $additionalSelect);
	    }
	    $where = implode(' ', $where);
	    if ($sWhere == '') {
	        $where = trim($where);
	        if (startsWith($where, 'AND') || startsWith($where, 'OR')) {
	            if (startsWith($where, 'OR')) {
	                $where = substr($where, 2);
	            } else {
	                $where = substr($where, 3);
	            }
	            $where = 'WHERE ' . $where;
	        }
	    }

	    $join = implode(' ', $join);

	    $sQuery = '
	    SELECT SQL_CALC_FOUND_ROWS ' . str_replace(' , ', ' ', implode(', ', $_aColumns)) . ' ' . $_additionalSelect . "
	    FROM $sTable
	    " . $join . "
	    $sWhere
	    " . $where . "
	    $sGroupBy
	    $sOrder
	    $sLimit
	    ";

	    $rResult = $db->query($sQuery)->getResultArray();

	    $rResult = app_hooks()->apply_filters('datatables_sql_query_results', $rResult, [
	        'table' => $sTable,
	        'limit' => $sLimit,
	        'order' => $sOrder,
	    ]);

	    /* Data set length after filtering */
	    $sQuery = '
	    SELECT FOUND_ROWS()
	    ';
	    $_query         = $db->query($sQuery)->getResultArray();
	    $iFilteredTotal = $_query[0]['FOUND_ROWS()'];
	    if (startsWith($where, 'AND')) {
	        $where = 'WHERE ' . substr($where, 3);
	    }
	    /* Total data set length */
	    $sQuery = '
	    SELECT COUNT(' . $sTable . '.' . $sIndexColumn . ")
	    FROM $sTable " . $join . ' ' . $where;

	    $_query = $db->query($sQuery)->getResultArray();
	    $iTotal = $_query[0]['COUNT(' . $sTable . '.' . $sIndexColumn . ')'];
	    /*
	     * Output
	     */
	    $output = [
	        'iTotalRecords'        => $iTotal,
	        'iTotalDisplayRecords' => $iFilteredTotal,
	        'data'               => [],
	        ];

	    return [
	        'rResult' => $rResult,
	        'output'  => $output,
	        ];
	}
}

if (!function_exists('prefixed_table_fields_wildcard')) {

	function prefixed_table_fields_wildcard($table, $alias, $field)
	{
		$prefixed = prefixed_table_fields_wildcard_2($table, $alias, $field);

		return $prefixed;
	}
}

if (!function_exists('prefixed_table_fields_wildcard_2')) {
 function prefixed_table_fields_wildcard_2($table, $alias, $field)
	{

		$db = db_connect('default');

		$columns     = $db->query("SHOW COLUMNS FROM $table")->getResultArray();
		$field_names = [];
		foreach ($columns as $column) {
			$field_names[] = $column['Field'];
		}
		$prefixed = [];
		foreach ($field_names as $field_name) {
			if ($field == $field_name) {
				$prefixed[] = "`{$alias}`.`{$field_name}` AS `{$alias}.{$field_name}`";
			}
		}

		return implode(', ', $prefixed);
	}
}


/**
 * Used in data_tables_init function to fix sorting problems when duedate is null
 * Null should be always last
 * @return array
 */
if (!function_exists('get_null_columns_that_should_be_sorted_as_last')) {
function get_null_columns_that_should_be_sorted_as_last()
{
    $columns = [
        get_db_prefix() . 'projects.deadline',
        get_db_prefix() . 'tasks.duedate',
        get_db_prefix() . 'contracts.dateend',
        get_db_prefix() . 'subscriptions.date_subscribed',
    ];

    return app_hooks()->apply_filters('null_columns_sort_as_last', $columns);
}
}
/**
 * Render table used for datatables
 * @param  array  $headings           [description]
 * @param  string $class              table class / added prefix table-$class
 * @param  array  $additional_classes
 * @return string                     formatted table
 */
/**
 * Render table used for datatables
 * @param  array   $headings
 * @param  string  $class              table class / add prefix eq.table-$class
 * @param  array   $additional_classes additional table classes
 * @param  array   $table_attributes   table attributes
 * @param  boolean $tfoot              includes blank tfoot
 * @return string
 */
if (!function_exists('render_datatable')) {
function render_datatable($headings = [], $class = '', $additional_classes = [''], $table_attributes = [])
{
    $_additional_classes = '';
    $_table_attributes   = ' ';
    if (count($additional_classes) > 0) {
        $_additional_classes = ' ' . implode(' ', $additional_classes);
    }
    $CI      = & get_instance();
    $browser = $CI->agent->browser();
    $IEfix   = '';
    if ($browser == 'Internet Explorer') {
        $IEfix = 'ie-dt-fix';
    }

    foreach ($table_attributes as $key => $val) {
        $_table_attributes .= $key . '=' . '"' . $val . '" ';
    }

    $table = '<div class="' . $IEfix . '"><table' . $_table_attributes . 'class="dt-table-loading table table-' . $class . '' . $_additional_classes . '">';
    $table .= '<thead>';
    $table .= '<tr>';
    foreach ($headings as $heading) {
        if (!is_array($heading)) {
            $table .= '<th>' . $heading . '</th>';
        } else {
            $th_attrs = '';
            if (isset($heading['th_attrs'])) {
                foreach ($heading['th_attrs'] as $key => $val) {
                    $th_attrs .= $key . '=' . '"' . $val . '" ';
                }
            }
            $th_attrs = ($th_attrs != '' ? ' ' . $th_attrs : $th_attrs);
            $table .= '<th' . $th_attrs . '>' . $heading['name'] . '</th>';
        }
    }
    $table .= '</tr>';
    $table .= '</thead>';
    $table .= '<tbody></tbody>';
    $table .= '</table></div>';
    echo html_entity_decode($table);
}
}

/**
 * Translated datatables language based on app languages
 * This feature is used on both admin and customer area
 * @return array
 */
if (!function_exists('get_datatables_language_array')) {
function get_datatables_language_array()
{
    $lang = [
        'emptyTable'        => preg_replace("/{(\d+)}/", app_lang('dt_entries'), app_lang('dt_empty_table')),
        'info'              => preg_replace("/{(\d+)}/", app_lang('dt_entries'), app_lang('dt_info')),
        'infoEmpty'         => preg_replace("/{(\d+)}/", app_lang('dt_entries'), app_lang('dt_info_empty')),
        'infoFiltered'      => preg_replace("/{(\d+)}/", app_lang('dt_entries'), app_lang('dt_info_filtered')),
        'lengthMenu'        => '_MENU_',
        'loadingRecords'    => app_lang('dt_loading_records'),
        'processing'        => '<div class="dt-loader"></div>',
        'search'            => '<div class="input-group"><span class="input-group-addon"><span class="fa fa-search"></span></span>',
        'searchPlaceholder' => app_lang('dt_search'),
        'zeroRecords'       => app_lang('dt_zero_records'),
        'paginate'          => [
            'first'    => app_lang('dt_paginate_first'),
            'last'     => app_lang('dt_paginate_last'),
            'next'     => app_lang('dt_paginate_next'),
            'previous' => app_lang('dt_paginate_previous'),
        ],
        'aria' => [
            'sortAscending'  => app_lang('dt_sort_ascending'),
            'sortDescending' => app_lang('dt_sort_descending'),
        ],
    ];

    return hooks()->apply_filters('datatables_language_array', $lang);
}
}

/**
 * Function that will parse filters for datatables and will return based on a couple conditions.
 * The returned result will be pushed inside the $where variable in the table SQL
 * @param  array $filter
 * @return string
 */
if (!function_exists('prepare_dt_filter')) {
function prepare_dt_filter($filter)
{
    $filter = implode(' ', $filter);
    if (startsWith($filter, 'AND')) {
        $filter = substr($filter, 3);
    } elseif (startsWith($filter, 'OR')) {
        $filter = substr($filter, 2);
    }

    return $filter;
}
}
/**
 * Get table last order
 * @param  string $tableID table unique identifier id
 * @return string
 */
if (!function_exists('get_table_last_order')) {
function get_table_last_order($tableID)
{
    return htmlentities(get_staff_meta(get_staff_user_id(), $tableID . '-table-last-order'));
}
}

if (!function_exists('startsWith')) {
function startsWith( $haystack, $needle ) {
     $length = strlen( $needle );
     return substr( $haystack, 0, $length ) === $needle;
}
}

if (!function_exists('has_permission')) {
	function has_permission($permission, $staffid = '', $can = '')
	{
		return true;
	}

}


/**
 * Function that renders input for admin area based on passed arguments
 * @param  string $name             input name
 * @param  string $label            label name
 * @param  string $value            default value
 * @param  string $type             input type eq text,number
 * @param  array  $input_attrs      attributes on <input
 * @param  array  $form_group_attr  <div class="form-group"> html attributes
 * @param  string $form_group_class additional form group class
 * @param  string $input_class      additional class on input
 * @return string
 */
if (!function_exists('render_input')) {
	function render_input($name, $label = '', $value = '', $type = 'text', array $input_attrs = [], array $form_group_attr = [], $form_group_class = '', $input_class = '', $data_required = false, $data_required_msg = '')
	{
		$input            = '';
		$_form_group_attr = '';

		$form_group_attr['app-field-wrapper'] = $name;

		foreach ($form_group_attr as $key => $val) {
        // tooltips
			if ($key == 'title') {
				$val = app_lang($val);
			}
			$_form_group_attr .= $key . '=' . '"' . $val . '" ';
		}

		$_form_group_attr = rtrim($_form_group_attr);

		if (!empty($form_group_class)) {
			$form_group_class = ' ' . $form_group_class;
		}
		if (!empty($input_class)) {
			$input_class = ' ' . $input_class;
		}
		$input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
		if ($label != '') {
			$input .= '<label for="' . $name . '" class="control-label">' . app_lang($label, '', false) . '</label>';
		}

		$input .= form_input(array_merge(array(
			"id" => $name,
			"name" => $name,
			"value" => $value,
			"class" => "form-control".$input_class,
			"placeholder" => app_lang($label),
			"autocomplete" => "off",
			"data-rule-required" => $data_required,
			"data-msg-required" => $data_required_msg == '' ? app_lang('field_required') : app_lang($data_required_msg),
		), $input_attrs), $value, '', $type);

		$input .= '</div>';

		return $input;
	}
}

/**
 * Render date picker input for admin area
 * @param  [type] $name             input name
 * @param  string $label            input label
 * @param  string $value            default value
 * @param  array  $input_attrs      input attributes
 * @param  array  $form_group_attr  <div class="form-group"> div wrapper html attributes
 * @param  string $form_group_class form group div wrapper additional class
 * @param  string $input_class      <input> additional class
 * @return string
 */
if (!function_exists('render_date_input')) {
function render_date_input($name, $label = '', $value = '', $input_attrs = [], $form_group_attr = [], $form_group_class = '', $input_class = '')
{
    $input            = '';
    $_form_group_attr = '';

    $form_group_attr['app-field-wrapper'] = $name;

    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = app_lang($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '" ';
    }

    $_form_group_attr = rtrim($_form_group_attr);

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }

    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $input .= '<label for="' . $name . '" class="control-label">' . app_lang($label, '', false) . '</label>';
    }

    $input .= form_input(array_merge(array(
        "id" => $name,
        "name" => $name,
        "value" => $value,
        "class" => "form-control",
        "placeholder" => app_lang($label, '', false),
        "autocomplete" => "off",
    ), $input_attrs));
    
    $input .= '</div>';

    return $input;
}
}

if (!function_exists('_d')) {
	function _d($date)
	{
		return format_to_date($date);
	}
}

if (!function_exists('_l')) {
	function _l($key)
	{
		return app_lang($key);
	}
}

if (!function_exists('admin_url')) {
	function admin_url($url)
	{
		return get_uri($url);
	}
}

if (!function_exists('render_select')) {

	function render_select($name, $options, $option_attrs = [], $label = '', $selected = '', $select_attrs = [], $form_group_attr = [], $form_group_class = '', $select_class = '', $include_blank = true)
	{	
		$html = '';
		$html .= '<div class="form-group">';
		if($label != ''){
      $html .= '<label for="'. $name .'" class="">'. app_lang($label).'</label>';
		}

		$options_dropdown = [];

		if($include_blank == true){
			$options_dropdown[''] = '-';
		}

		$required = '';
		if (isset($select_attrs['required'])) {
	        $required = 'required';
	    }

	    $_select_attrs    = '';
	    if (!isset($select_attrs['data-width'])) {
	        $select_attrs['data-width'] = '100%';
	    }
	    if (!isset($select_attrs['data-none-selected-text'])) {
	        $select_attrs['data-none-selected-text'] = _l('dropdown_non_selected_tex');
	    }
	    foreach ($select_attrs as $key => $val) {
	        // tooltips
	        if ($key == 'title') {
	            $val = _l($val);
	        }
	        $_select_attrs .= $key . '=' . '"' . $val . '" ';
	    }

	    $_select_attrs = rtrim($_select_attrs);

		foreach ($options as $option) {
        $val       = '';
        $key       = '';
        if (isset($option[$option_attrs[0]]) && !empty($option[$option_attrs[0]])) {
            $key = $option[$option_attrs[0]];
        }
        if (!is_array($option_attrs[1])) {
            $val = $option[$option_attrs[1]];
        } else {
            foreach ($option_attrs[1] as $_val) {
                $val .= $option[$_val] . ' ';
            }
        }
        $val = trim($val);

        $options_dropdown[$key] = $val;
    }

    $html .= form_dropdown($name, $options_dropdown, array($selected), "class='select2 validate-hidden' id='".$name."' ".$required." ".$_select_attrs);
    
    $html .= '</div>';

		return $html;
	}
}

/**
 * Sum total from table
 * @param  string $table table name
 * @param  array  $attr  attributes
 * @return mixed
 */
if (!function_exists('sum_from_table')) {
	function sum_from_table($table, $attr = [])
	{
	    if (!isset($attr['field'])) {
	        show_error('sum_from_table(); function expect field to be passed.');
	    }

	    $db = db_connect('default');
	    $db_builder = $db->table($table);
	    if (isset($attr['where']) && is_array($attr['where'])) {
	        $i = 0;
	        foreach ($attr['where'] as $key => $val) {
	            if (is_numeric($key)) {
	                $db_builder->where($val);
	                unset($attr['where'][$key]);
	            }
	            $i++;
	        }
	        $db_builder->where($attr['where']);
	    }
	    $db_builder->selectSum($attr['field']);
	    $result = $db_builder->get()->getRow();

	    return $result->{$attr['field']};
	}
}

/**
 * Used in:
 * Search contact tickets
 * Project dropdown quick switch
 * Calendar tooltips
 * @param  [type] $userid [description]
 * @return [type]         [description]
 */
if (!function_exists('get_company_name')) {
	function get_company_name($userid, $prevent_empty_company = false)
	{
	    
	    $_userid = $userid;

	    $db = db_connect('default');
	    $db_builder = $db->table(get_db_prefix() . 'clients');
	    $client = $db_builder->select('company_name')
	        ->where('id', $_userid)
	        ->get()
	        ->getRow();
	    if ($client) {
	        return $client->company_name;
	    }

	    return '';
	}
}

if (!function_exists('get_staff_full_name')) {
	function get_staff_full_name($staffid = ''){
		if($staffid != ''){
	    	$db = db_connect('default');
	    	$db_builder = $db->table(get_db_prefix() . 'users');
	    	$db_builder->where('id', $staffid);
	    	$user = $db_builder->get()->getRow();
	    	if($user){
				return $user->first_name . " " . $user->last_name;
	    	}

			return '';
		}else{
	    	$ci = new Security_Controller(false);
			return $ci->login_user->first_name . " " . $ci->login_user->last_name;
		}
	}
}

if (!function_exists('to_sql_date')) {
	function to_sql_date($date){
		return $date;
	}
}
