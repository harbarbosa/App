<?php
use App\Controllers\Security_Controller;


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
if (!function_exists('render_input1')) {
	function render_input1($name, $label = '', $value = '', $type = 'text', array $input_attrs = [], array $form_group_attr = [], $form_group_class = '', $input_class = '', $data_required = false, $data_required_msg = '', $placeholder = false)
	{
		if($value == null){
			$value = '';
		}

		$input            = '';
		$_form_group_attr = '';

		$form_group_attr['app-field-wrapper'] = $name;

		foreach ($form_group_attr as $key => $val) {
		// tooltips
			if ($key == 'title') {
				$val = _l($val);
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
			if($data_required){
				$input .= '<small class="req text-danger">* </small><label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
			}else{
				$input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
			}
		}

		if($data_required){
			$arr_required = [];
			$arr_required = [
				"data-rule-required" => $data_required,
				"data-msg-required" => $data_required_msg == '' ? app_lang('field_required') : app_lang($data_required_msg),
				"required" => true,
			];
			$input_attrs = array_merge($input_attrs, $arr_required );
		}
		$input .= form_input(array_merge(array(
			"id" => $name,
			"name" => $name,
			"value" => $value,
			"class" => "form-control".$input_class,
			"placeholder" => $placeholder == true ? app_lang($label) : '',
			"autocomplete" => "off",
			
		), $input_attrs), $value, '', $type);

		$input .= '</div>';

		return $input;
	}
}

if (!function_exists('render_textarea1')) {
	/**
	 * { render textarea1 }
	 *
	 * @param      string  $name              The name
	 * @param      string  $label             The label
	 * @param      string  $value             The value
	 * @param      array   $textarea_attrs    The textarea attributes
	 * @param      array   $form_group_attr   The form group attribute
	 * @param      string  $form_group_class  The form group class
	 * @param      string  $textarea_class    The textarea class
	 * @param      bool    $placeholder       The placeholder
	 *
	 * @return     string  (  )
	 */
	function render_textarea1($name, $label = '', $value = '', $textarea_attrs = [], $form_group_attr = [], $form_group_class = '', $textarea_class = '', $placeholder = false)
	{
		if($value == null){
			$value = '';
		}
		
		$textarea         = '';
		$_form_group_attr = '';
		$_textarea_attrs  = '';
		if (!isset($textarea_attrs['rows'])) {
			$textarea_attrs['rows'] = 4;
		}

		if (isset($textarea_attrs['class'])) {
			$textarea_class .= ' ' . $textarea_attrs['class'];
			unset($textarea_attrs['class']);
		}

		foreach ($textarea_attrs as $key => $val) {
			// tooltips
			if ($key == 'title') {
				$val = _l($val);
			}
			$_textarea_attrs .= $key . '=' . '"' . $val . '" ';
		}

		$_textarea_attrs = rtrim($_textarea_attrs);

		$form_group_attr['app-field-wrapper'] = $name;

		foreach ($form_group_attr as $key => $val) {
			if ($key == 'title') {
				$val = _l($val);
			}
			$_form_group_attr .= $key . '=' . '"' . $val . '" ';
		}

		$_form_group_attr = rtrim($_form_group_attr);

		if (!empty($textarea_class)) {
			$textarea_class = trim($textarea_class);
			$textarea_class = ' ' . $textarea_class;
		}
		if (!empty($form_group_class)) {
			$form_group_class = ' ' . $form_group_class;
		}
		$textarea .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
		if ($label != '') {
			$textarea .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
		}


		$textarea .=  form_textarea(array_merge(array(
			"id" => $name,
			"name" => $name,
			"value" => $value,
			"class" => "form-control". $textarea_class,
			"placeholder" => $placeholder == true ? app_lang($label) : '',
			"data-rich-text-editor" => true
		), $textarea_attrs), $value );

		$textarea .= '</div>';

		return $textarea;
	}
}

if (!function_exists('render_select1')) {
	/**
	 * { render select  }
	 *
	 * @param      string  $name               The name
	 * @param      <type>  $options            The options
	 * @param      array   $option_attrs       The option attributes
	 * @param      string  $label              The label
	 * @param      string  $selected           The selected
	 * @param      array   $select_attrs       The select attributes
	 * @param      array   $form_group_attr    The form group attribute
	 * @param      string  $form_group_class   The form group class
	 * @param      string  $select_class       The select class
	 * @param      bool    $include_blank      The include blank
	 * @param      bool    $data_required      The data required
	 * @param      string  $data_required_msg  The data required message
	 *
	 * @return     string  
	 */
	function render_select1($name, $options, $option_attrs = [], $label = '', $selected = '', $select_attrs = [], $form_group_attr = [], $form_group_class = '', $select_class = '', $include_blank = true, $data_required = false, $data_required_msg = '')
	{
		$callback_translate = '';
		if (isset($options['callback_translate'])) {
			$callback_translate = $options['callback_translate'];
			unset($options['callback_translate']);
		}
		$select           = '';
		$_form_group_attr = '';
		$_select_attrs    = '';
		if (!isset($select_attrs['data-width'])) {
			$select_attrs['data-width'] = '100%';
		}

		if($data_required){
			$arr_required = [];
			$arr_required = [
				"data-rule-required" => $data_required,
				"data-msg-required" => $data_required_msg == '' ? app_lang('field_required') : app_lang($data_required_msg),
				"required" => true,
			];
			$select_attrs = array_merge($select_attrs, $arr_required );
		}
		
		if (!isset($select_attrs['data-none-selected-text'])) {
			$select_attrs['data-none-selected-text'] = app_lang('dropdown_non_selected_tex');
		}
		foreach ($select_attrs as $key => $val) {
		// tooltips
			if ($key == 'title') {
				$val = app_lang($val);
			}
			$_select_attrs .= $key . '=' . '"' . $val . '" ';
		}

		$_select_attrs = rtrim($_select_attrs);

		$form_group_attr['app-field-wrapper'] = $name;
		foreach ($form_group_attr as $key => $val) {
		// tooltips
			if ($key == 'title') {
				$val = app_lang($val);
			}
			$_form_group_attr .= $key . '=' . '"' . $val . '" ';
		}
		$_form_group_attr = rtrim($_form_group_attr);
		if (!empty($select_class)) {
			$select_class = ' ' . $select_class;
		}
		if (!empty($form_group_class)) {
			$form_group_class = ' ' . $form_group_class;
		}
		$select .= '<div class="select-placeholder form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
		if ($label != '') {
			if($data_required){
				$select .= '<small class="req text-danger">* </small><label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
			}else{
				$select .= '<label for="' . $name . '" class="control-label">' . app_lang($label, '', false) . '</label>';
			}
		}
		$select .= '<select id="' . $name . '" name="' . $name . '" class="select2 validate-hidden' . $select_class . '" ' . $_select_attrs . ' data-live-search="true">';
		if ($include_blank == true) {
			$select .= '<option value="">-</option>';
		}
		foreach ($options as $option) {
			$val       = '';
			$_selected = '';
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

			if ($callback_translate != '') {
				if (function_exists($callback_translate) && is_callable($callback_translate)) {
					$val = call_user_func($callback_translate, $key);
				}
			}

			$data_sub_text = '';
			if (!is_array($selected)) {
				if ($selected != '') {
					if ($selected == $key) {
						$_selected = ' selected="selected"';
					}
				}
			} else {
				foreach ($selected as $id) {
					if ($key == $id) {
						$_selected = ' selected="selected"';
					}
				}
			}

			if (isset($option_attrs[2])) {
				if (strpos($option_attrs[2], ',') !== false) {
					$sub_text = '';
					$_temp    = explode(',', $option_attrs[2]);
					foreach ($_temp as $t) {
						if (isset($option[$t])) {
							$sub_text .= $option[$t] . ' ';
						}
					}
				} else {
					if (isset($option[$option_attrs[2]])) {
						$sub_text = $option[$option_attrs[2]];
					} else {
						$sub_text = $option_attrs[2];
					}
				}
				$data_sub_text = ' data-subtext=' . '"' . $sub_text . '"';
			}
			$data_content = '';
			if (isset($option['option_attributes'])) {
				foreach ($option['option_attributes'] as $_opt_attr_key => $_opt_attr_val) {
					$data_content .= $_opt_attr_key . '=' . '"' . $_opt_attr_val . '"';
				}
				if ($data_content != '') {
					$data_content = ' ' . $data_content;
				}
			}
			$select .= '<option value="' . $key . '"' . $_selected . $data_content . $data_sub_text . '>' . $val . '</option>';
		}
		$select .= '</select>';
		$select .= '</div>';

		return $select;
	}
}


if (!function_exists('render_color_picker1')) {
	/**
	 * { render color picker1 }
	 *
	 * @param      string  $name         The name
	 * @param      string  $label        The label
	 * @param      string  $value        The value
	 * @param      array   $input_attrs  The input attributes
	 *
	 * @return     string  
	 */
	function render_color_picker1($name, $label = '', $value = '', $input_attrs = [])
	{
		$_input_attrs = '';
		foreach ($input_attrs as $key => $val) {
        // tooltips
			if ($key == 'title') {
				$val = _l($val);
			}
			$_input_attrs .= $key . '=' . '"' . $val . '"';
		}

		$picker = '';
		$picker .= '<div class="form-group" app-field-wrapper="' . $name . '">';
		$picker .= '<label for="' . $name . '" class="control-label">' . $label . '</label>';
		$picker .= '<div class="input-group mbot15 colorpicker-input">
		<input type="color" value="' . set_value($name, $value) . '" name="' . $name . '" id="' . $name . '" class="form-control form-control-color" ' . $_input_attrs . ' />
		<span class="input-group-addon"><i></i></span>
		</div>';
		$picker .= '</div>';

		return $picker;
	}
}

if (!function_exists('render_date_input1')) {
	/**
	 * { render_date_input }
	 *
	 * @param      string  $name               The name
	 * @param      string  $label              The label
	 * @param      string  $value              The value
	 * @param      array   $input_attrs        The input attributes
	 * @param      array   $form_group_attr    The form group attribute
	 * @param      string  $form_group_class   The form group class
	 * @param      string  $input_class        The input class
	 * @param      bool    $data_required      The data required
	 * @param      string  $data_required_msg  The data required message
	 * @param      bool    $placeholder        The placeholder
	 *
	 * @return     string  ( description_of_the_return_value )
	 */
	function render_date_input1($name, $label = '', $value = '', array $input_attrs = [], array $form_group_attr = [], $form_group_class = '', $input_class = '', $data_required = false, $data_required_msg = '', $placeholder = false)
	{
		if($value == null){
			$value = '';
		}

		$type = 'text';
		$input            = '';
		$_form_group_attr = '';

		$form_group_attr['app-field-wrapper'] = $name;

		foreach ($form_group_attr as $key => $val) {
		// tooltips
			if ($key == 'title') {
				$val = _l($val);
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
			if($data_required){
				$input .= '<small class="req text-danger">* </small><label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
			}else{
				$input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
			}
		}

		if($data_required){
			$arr_required = [];
			$arr_required = [
				"data-rule-required" => $data_required,
				"data-msg-required" => $data_required_msg == '' ? app_lang('field_required') : app_lang($data_required_msg),
				"required" => true,
			];
			$input_attrs = array_merge($input_attrs, $arr_required );
		}
		$input .= form_input(array_merge(array(
			"id" => $name,
			"name" => $name,
			"value" => $value,
			"class" => "form-control datePickerInput".$input_class,
			"placeholder" => $placeholder == true ? app_lang($label) : '',
			"autocomplete" => "off",
			
		), $input_attrs), $value, '', $type);

		$input .= '</div>';

		return $input;
	}
}

if (!function_exists('get_tax_by_name')) {
	/**
	 * Gets the tax by name.
	 *
	 * @param        $name   The name
	 *
	 * @return       The tax by name.
	 */
	function get_tax_by_name($name)
	{
		$builder = db_connect('default');
		$builder = $builder->table(get_db_prefix().'taxes');
		$builder->where('id', $id);
		$CI->db->where('title', $name);
		return $builder->get()->getRow();
	}
}

if (!function_exists('valueExistsByKey')) {
	/**
	 * { valueExistsByKey }
	 *
	 * @param      array   $array  The array
	 * @param        $key    The key
	 * @param        $val    The value
	 *
	 * @return     bool    ( description_of_the_return_value )
	 */
	function valueExistsByKey($array, $key, $val)
	{
		foreach ($array as $item) {
			if (isset($item[$key]) && $item[$key] == $val) {
				return true;
			}
		}

		return false;
	}
}


if (!function_exists('get_current_date_format1')) {
	/**
	 * Gets the current date format 1.
	 *
	 * @param      bool    $php    The php
	 *
	 * @return       The current date format 1.
	 */
	function get_current_date_format1($php = false)
	{
		$format = get_setting('date_format');
		$format = explode('|', $format);

		if ($php == false) {
			return $format[1];
		}

		return $format[0];
	}
}

if (!function_exists('to_sql_date1')) {
	/**
	 * { to sql date }
	 *
	 * @param      string  $date      The date
	 * @param      bool    $datetime  The datetime
	 *
	 * @return       date formated
	 */
	function to_sql_date1($date, $datetime = false)
	{
		if ($date == '' || $date == null) {
			return null;
		}

		$to_date     = 'Y-m-d';
		$from_format = get_current_date_format1(true);

		$date = app_hooks()->apply_filters('before_sql_date_format', $date, [
			'from_format' => $from_format,
			'is_datetime' => $datetime,
		]);

		if ($datetime == false) {
			return app_hooks()->apply_filters('to_sql_date_formatted', date_format(date_create_from_format($from_format, $date), $to_date));
		}

		if (strpos($date, ' ') === false) {
			$date .= ' 00:00:00';
		} else {
			$hour12 = (get_setting('time_format') == 24 ? false : true);
			if ($hour12 == false) {
				$_temp = explode(' ', $date);
				$time  = explode(':', $_temp[1]);
				if (count($time) == 2) {
					$date .= ':00';
				}
			} else {
				$tmp  = _simplify_date_fix($date, $from_format);
				$time = date('G:i', strtotime($tmp));
				$tmp  = explode(' ', $tmp);
				$date = $tmp[0] . ' ' . $time . ':00';
			}
		}

		$date = _simplify_date_fix($date, $from_format);
		$d    = strftime('%Y-%m-%d %H:%M:%S', strtotime($date));

		return $d;
	}
}


if (!function_exists('module_views_path')) {
	/**
	 * { function_description }
	 *
	 * @param        $module  The module
	 * @param        $concat  The concatenate
	 *
	 * @return       ( description_of_the_return_value )
	 */
	function module_views_path($module, $concat = '')
	{
		/**
		 * { module dir path }
		 */
		return module_dir_path($module) . 'Views/' . $concat;
	}
}

if (!function_exists('module_dir_path')) {
	/**
	 * { module dir path  }
	 *
	 * @param      string  $module  The module
	 * @param      string  $concat  The concatenate
	 *
	 * @return     string  ( description_of_the_return_value )
	 */
	function module_dir_path($module, $concat = '')
	{
		return APP_MODULES_PATH . $module . '/' . $concat;
	}
}

if (!function_exists('endsWith')) {
	/**
	* String ends with
	* @param  string $haystack
	* @param  string $needle
	* @return boolean
	*/
	function endsWith($haystack, $needle)
	{
		return $needle === '' || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
	}
}

if (!function_exists('escape_str')) {

	function escape_str($str, $like = FALSE)
	{
		if (is_array($str))
		{
			foreach ($str as $key => $val)
			{
				$str[$key] = $this->escape_str($val, $like);
			}

			return $str;
		}

		$str = _escape_str($str);

		// escape LIKE condition wildcards
		if ($like === TRUE)
		{
			return str_replace(
				array($this->_like_escape_chr, '%', '_'),
				array($this->_like_escape_chr.$this->_like_escape_chr, $this->_like_escape_chr.'%', $this->_like_escape_chr.'_'),
				$str
			);
		}

		return $str;
	}
}

if (!function_exists('startsWith1')) {
	/**
	* String ends with
	* @param  string $haystack
	* @param  string $needle
	* @return boolean
	*/
	function startsWith1($haystack, $needle)
	{
		return $needle === '' || strrpos($haystack, $needle, -strlen($haystack)) !== false;
	}
}

if (!function_exists('strbefore1')) {
	/**
	 * { strbefore1 }
	 *
	 * @param        $string     The string
	 * @param       $substring  The substring
	 *
	 * @return       ( description_of_the_return_value )
	 */
	function strbefore1($string, $substring)
	{
		$pos = strpos($string, $substring);
		if ($pos === false) {
			return $string;
		}

		return (substr($string, 0, $pos));
	}
}

if (!function_exists('strafter')) {
	/**
	 * { strafter }
	 *
	 * @param        $string     The string
	 * @param        $substring  The substring
	 *
	 * @return     string  
	 */
	function strafter($string, $substring)
	{
		$pos = strpos($string, $substring);
		if ($pos === false) {
			return $string;
		}

		return (substr($string, $pos + strlen($substring)));
	}
}

if (!function_exists('_escape_str')) {
	/**
	 * { _escape_str }
	 *
	 * @param        $str    The string
	 *
	 * @return       
	 */
	function _escape_str($str)
	{
		return str_replace("'", "''", remove_invisible_characters($str, FALSE));
	}
}


/**
 * _l
 * @param  string $lang 
 * @return [type]       
 */
if (!function_exists('_l')) {
	function _l($lang = "") {
		if (!$lang) {
			return false;
		}

		//first check if the key is exists in custom lang
		$language_result = lang("custom_lang.$lang");
		if ($language_result === "custom_lang.$lang") {
			//this key doesn't exists in custom language, get from default language
			$language_result = lang("default_lang.$lang");
		}

		return $language_result;
	}

}

/**
 * db prefix
 * @return [type] 
 */
if (!function_exists('db_prefix')) {
	/**
	 * { db prefix }
	 *
	 * @return       prefix
	 */
	function db_prefix() {
		$db = db_connect('default');
		return $db->getPrefix();
	}

}

if (!function_exists('is_admin')) {
	/**
	 * Determines whether the specified staffid is admin.
	 *
	 * @param      string  $staffid  The staffid
	 *
	 * @return     bool    True if the specified staffid is admin, False otherwise.
	 */
	function is_admin($staffid = '')
	{
		$ci = new Security_Controller(false);
		if ($ci->login_user->is_admin) {
            return true;
		}
		return false;
	}
}

if(!function_exists('get_status_modules_pur')){
	function get_status_modules_pur($module_name){
		return false;
	}
}

if(!function_exists('total_rows')){
	/**
	 * Count total rows on table based on params
	 * @param  string $table Table from where to count
	 * @param  array  $where
	 * @return mixed  Total rows
	 */
	function total_rows($table, $where = [])
	{
	    $builder = db_connect('default');
		$builder = $builder->table($table);

	    if (is_array($where)) {
	        if (sizeof($where) > 0) {
	            $builder->where($where);
	        }
	    } elseif (strlen($where) > 0) {
	        $builder->where($where);
	    }

	    return $builder->get()->getNumRows();
	}
}

if(!function_exists('ajax_on_total_items')){
	/**
	 * { ajax_on_total_items }
	 *
	 * @return     int  
	 */
	function ajax_on_total_items(){
		return 20000;
	}
}

if(!function_exists('get_staff_user_id')){
	/**
	 * Gets the staff user identifier.
	 *
	 * @return       The staff user identifier.
	 */
	function get_staff_user_id(){
		$users_model = model("App\Models\Users_model", false);
        $created_by = $users_model->login_user_id();

        return $created_by;
	}
}

/**
 * Function that will check the date before formatting and replace the date places
 * This function is custom developed because for some date formats converting to y-m-d format is not possible
 * @param  string $date        the date to check
 * @param  string $from_format from format
 * @return string
 */
if (!function_exists('_simplify_date_fix')) {

	function _simplify_date_fix($date, $from_format)
	{
		if ($from_format == 'd/m/Y') {
			$date = preg_replace('#(\d{2})/(\d{2})/(\d{4})\s(.*)#', '$3-$2-$1 $4', $date);
		} elseif ($from_format == 'm/d/Y') {
			$date = preg_replace('#(\d{2})/(\d{2})/(\d{4})\s(.*)#', '$3-$1-$2 $4', $date);
		} elseif ($from_format == 'm.d.Y') {
			$date = preg_replace('#(\d{2}).(\d{2}).(\d{4})\s(.*)#', '$3-$1-$2 $4', $date);
		} elseif ($from_format == 'm-d-Y') {
			$date = preg_replace('#(\d{2})-(\d{2})-(\d{4})\s(.*)#', '$3-$1-$2 $4', $date);
		}

		return $date;
	}
}

if(!function_exists('app_generate_hash')){
	/**
	 * Generate md5 hash
	 * @return string
	 */
	function app_generate_hash()
	{
	    return md5(rand() . microtime() . time() . uniqid());
	}
}

if (!function_exists('update_setting')) {
  /**
   * { update setting }
   *
   * @param      <type>  $name   The name
   * @param      string  $value  The value
   *
   * @return     bool    
   */
  function update_setting($name, $value = '')
  {
    if (setting_exists($name)) {
      $db = db_connect('default');
      $db_builder = $db->table(get_db_prefix() . 'settings');

      $db_builder->where('setting_name', $name);
      $affected_rows = $db_builder->update(['setting_value' => $value]);

      if ($affected_rows > 0) {
        return true;
      }

      return false;
    }

    return false;
  }
}

if (!function_exists('get_staff_full_name1')) {
	/**
	 * Gets the staff full name 1.
	 *
	 * @param      string  $userid  The userid
	 *
	 * @return     bool    The staff full name 1.
	 */
	function get_staff_full_name1($userid = '')
	{
		$Users_model = model("Models\Users_model");

		if ($userid == '' || !is_numeric($userid)) {
			$userid = get_staff_user_id1();
		}

		$options = array(
			"id" => $userid,
		);
		$staff = $Users_model->get_details($options)->getRow();
		return $staff ? $staff->first_name . ' ' . $staff->last_name : '';
	}
}

if (!function_exists('get_staff_user_id1')) {
	/**
	 * Gets the staff user identifier 1.
	 *
	 * @return       The staff user identifier 1.
	 */
	function get_staff_user_id1()
	{

        $Users_model = model("Models\Users_model");
		return $Users_model->login_user_id();
	}
}

if ( ! function_exists('get_mime_by_extension'))
{
	/**
	 * Get Mime by Extension
	 *
	 * Translates a file extension into a mime type based on config/mimes.php.
	 * Returns FALSE if it can't determine the type, or open the mime config file
	 *
	 * Note: this is NOT an accurate way of determining file mime types, and is here strictly as a convenience
	 * It should NOT be trusted, and should certainly NOT be used for security
	 *
	 * @param	string	$filename	File name
	 * @return	string
	 */
	function get_mime_by_extension($filename)
	{
		static $mimes;

		if ( ! is_array($mimes))
		{
			$mimes = get_mimes();

			if (empty($mimes))
			{
				return FALSE;
			}
		}

		$extension = strtolower(substr(strrchr($filename, '.'), 1));

		if (isset($mimes[$extension]))
		{
			return is_array($mimes[$extension])
				? current($mimes[$extension]) // Multiple mime types, just give the first one
				: $mimes[$extension];
		}

		return FALSE;
	}
}


if( !function_exists('_maybe_create_upload_path')){
	/**
	 * { _maybe_create_upload_path }
	 *
	 * @param       $path   The path
	 */
	function _maybe_create_upload_path($path)
	{
	    if (!file_exists($path)) {
	        mkdir($path, 0755);
	        fopen(rtrim($path, '/') . '/' . 'index.html', 'w');
	    }
	}
}

if( !function_exists('get_mime_class') ){
	/**
	 * Get mime class by mime - admin system function
	 * @param  string $mime file mime type
	 * @return string
	 */
	function get_mime_class($mime)
	{
	    if (empty($mime) || is_null($mime)) {
	        return 'mime mime-file';
	    }
	    $_temp_mime = explode('/', $mime);
	    $part1      = $_temp_mime[0];
	    $part2      = $_temp_mime[1];
	    // Image
	    if ($part1 == 'image') {
	        if (strpos($part2, 'photoshop') !== false) {
	            return 'mime mime-photoshop';
	        }
	        ;

	        return 'mime mime-image';
	    }
	    // Audio
	    elseif ($part1 == 'audio') {
	        return 'mime mime-audio';
	    }
	    // Video
	    elseif ($part1 == 'video') {
	        return 'mime mime-video';
	    }
	    // Text
	    elseif ($part1 == 'text') {
	        return 'mime mime-file';
	    }
	    // Applications
	    elseif ($part1 == 'application') {
	        // Pdf
	        if ($part2 == 'pdf') {
	            return 'mime mime-pdf';
	        }
	        // Ilustrator
	        elseif ($part2 == 'illustrator') {
	            return 'mime mime-illustrator';
	        }
	        // Zip
	        elseif ($part2 == 'zip' || $part2 == 'gzip' || strpos($part2, 'tar') !== false || strpos($part2, 'compressed') !== false) {
	            return 'mime mime-zip';
	        }
	        // PowerPoint
	        elseif (strpos($part2, 'powerpoint') !== false || strpos($part2, 'presentation') !== false) {
	            return 'mime mime-powerpoint ';
	        }
	        // Excel
	        elseif (strpos($part2, 'excel') !== false || strpos($part2, 'sheet') !== false) {
	            return 'mime mime-excel';
	        }
	        // Word
	        elseif ($part2 == 'msword' || $part2 == 'rtf' || strpos($part2, 'document') !== false) {
	            return 'mime mime-word';
	        }
	        // Else

	        return 'mime mime-file';
	    }
	    // Else

	    return 'mime mime-file';
	}
}

if(!function_exists('protected_file_url_by_path')){
	/**
	 * Used in to eq preview images where the files are protected with .htaccess
	 * @param  string  $path    full path
	 * @param  boolean $preview
	 * @return string
	 */
	function protected_file_url_by_path($path, $preview = false)
	{
	    if ($preview) {
	        $fname     = pathinfo($path, PATHINFO_FILENAME);
	        $fext      = pathinfo($path, PATHINFO_EXTENSION);
	        $thumbPath = pathinfo($path, PATHINFO_DIRNAME) . '/' . $fname . '_thumb.' . $fext;
	        if (file_exists($thumbPath)) {
	            return str_replace(FCPATH, '', $thumbPath);
	        }

	        return str_replace(FCPATH, '', $path);
	    }

	    return str_replace(FCPATH, '', $path);
	}
}

if(!function_exists('markdown_parse_preview')){
	/**
	 * Parse markdown preview
	 * @param  string $path full markdown file path
	 * @return mixed
	 */
	function markdown_parse_preview($path)
	{
	    $Parsedown = new Parsedown();


	    $Parsedown->setSafeMode($markDownSafeMode == 'true' ? true : false);

	    $contents = @file_get_contents($path);

	    if (!$contents) {
	        return false;
	    }

	    return $Parsedown->text($contents);
	}
}

if(!function_exists('is_image')){
	/**
	 * Is file image
	 * @param  string  $path file path
	 * @return boolean
	 */
	function is_image($path)
	{
	    $possibleBigFiles = [
	            'pdf', 'zip', 'mp4', 'ai',
	            'psd', 'ppt',  'gzip', 'rar',
	            'tar', 'tgz',  'mpeg', 'mpg',
	            'flv', 'mov',  'wav', 'avi',
	            'dwg',
	        ];

	    $pathArray = explode('.', $path);
	    $ext       = end($pathArray);
	    // Causing performance issues if the file is too big
	    if (in_array($ext, $possibleBigFiles)) {
	        return false;
	    }

	    $image = @getimagesize($path);
	    if ($image) {
	        $image_type = $image[2];
	        if (in_array($image_type, [
	            IMAGETYPE_GIF,
	            IMAGETYPE_JPEG,
	            IMAGETYPE_PNG,
	            IMAGETYPE_BMP,
	        ])) {
	            return true;
	        }
	    }

	    return false;
	}
}

if(!function_exists('list_files')){
	/**
	 * List files in a specific folder
	 * @param  string $dir directory to list files
	 * @return array
	 */
	function list_files($dir)
	{
	    $ignored = [
	        '.',
	        '..',
	        '.svn',
	        '.htaccess',
	        'index.html',
	    ];
	    $files = [];
	    foreach (scandir($dir) as $file) {
	        if (in_array($file, $ignored)) {
	            continue;
	        }
	        $files[$file] = filectime($dir . '/' . $file);
	    }
	    arsort($files);
	    $files = array_keys($files);

	    return ($files) ? $files : [];
	}
}

if(!function_exists('delete_dir')){
	/**
	 * Delete directory
	 * @param  string $dirPath dir
	 * @return boolean
	 */
	function delete_dir($dirPath)
	{
	    if (!is_dir($dirPath)) {
	        throw new InvalidArgumentException("$dirPath must be a directory");
	    }
	    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
	        $dirPath .= '/';
	    }
	    $files = glob($dirPath . '*', GLOB_MARK);
	    foreach ($files as $file) {
	        if (is_dir($file)) {
	            delete_dir($file);
	        } else {
	            unlink($file);
	        }
	    }
	    if (rmdir($dirPath)) {
	        return true;
	    }

	    return false;
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

if (!function_exists('_d')) {
	/**
	 * { format date }
	 *
	 * @param       $date   The date
	 *
	 * @return     date 
	 */
	function _d($date)
	{
		return format_to_date($date);
	}
}