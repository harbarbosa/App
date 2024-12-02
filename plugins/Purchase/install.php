<?php

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

if(!function_exists('create_email_template')){
  function purchase_create_email_template($name, $subject, $default_message){
    $db = db_connect('default');
    $db_builder = $db->table(get_db_prefix() . 'email_templates');

    $count = $db_builder->where('template_name', $name)->countAllResults();
    if($count == 0){
      $db_builder->insert([
        'template_name' => $name,
        'email_subject' => $subject,
        'default_message' => $default_message
      ]);
    }

  }
}

$this_is_required = true;
if (!$this_is_required) {
    echo json_encode(array("success" => false, "message" => "This is required!"));
    exit();
}

//run installation sql
$db = db_connect('default');
$dbprefix = get_db_prefix();

$sql_query = "CREATE TABLE IF NOT EXISTS `" . $dbprefix . "pur_vendor` (
    `userid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `company` varchar(200) NULL,
      `vat` varchar(200) NULL,
      `phonenumber` varchar(30) NULL,
      `country` int(11) NOT NULL DEFAULT '0',
      `city` varchar(100) NULL,
      `zip` varchar(15) NULL,
      `state` varchar(50) NULL,
      `address` TEXT NULL,
      `website` varchar(150) NULL,
      `datecreated` DATETIME NOT NULL,
      `active` INT(11) NOT NULL DEFAULT '1',
      `leadid` INT(11) NULL,
      `billing_street` varchar(200) NULL,
      `billing_city` varchar(100) NULL,
      `billing_state` varchar(100) NULL,
      `billing_zip` varchar(100) NULL,
      `billing_country` int(11) NULL DEFAULT '0',
      `shipping_street` varchar(200) NULL,
      `shipping_city` varchar(100) NULL,
      `shipping_state` varchar(100) NULL,
      `shipping_zip` varchar(100) NULL,
      `shipping_country` int(11) NULL DEFAULT '0',
      `longitude` varchar(191) NULL,
      `latitude` varchar(191) NULL,
      `default_language` varchar(40) NULL,
      `default_currency` INT(11) NOT NULL DEFAULT '0',
      `show_primary_contact` INT(11) NOT NULL DEFAULT '0',
      `stripe_id` varchar(40) NULL,
      `registration_confirmed` INT(11) NOT NULL DEFAULT '1',
      `addedfrom` INT(11) NOT NULL DEFAULT '0',
      `category` TEXT NULL,
      `bank_detail` TEXT NULL,
      `payment_terms` TEXT NULL,
      `vendor_code` varchar(100) NULL,
      `return_within_day` INT(11) NULL,
      `return_order_fee` DECIMAL(15,2) NULL,
      `return_policies` TEXT NULL,
      PRIMARY KEY (`userid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$db->query($sql_query);

$sql_query = "CREATE TABLE IF NOT EXISTS `" . $dbprefix . "pur_vendor_admin` (
    `staff_id` INT(11) NOT NULL,
    `vendor_id` INT(11) NOT NULL,
    `date_assigned` DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$db->query($sql_query);

$sql_query = "CREATE TABLE IF NOT EXISTS `" . $dbprefix . "pur_approval_setting` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `related` VARCHAR(255) NOT NULL,
    `setting` LONGTEXT NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$db->query($sql_query);

$sql_query = "CREATE TABLE IF NOT EXISTS `" . $dbprefix . "pur_approval_details` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `rel_id` INT(11) NOT NULL,
      `rel_type` VARCHAR(45) NOT NULL,
      `staffid` VARCHAR(45) NULL,
      `approve` VARCHAR(45) NULL,
      `note` TEXT NULL,
      `date` DATETIME NULL,
      `approve_action` VARCHAR(255) NULL,
      `reject_action` VARCHAR(255) NULL,
      `approve_value` VARCHAR(255) NULL,
      `reject_value` VARCHAR(255) NULL,
      `staff_approve` INT(11) NULL,
      `action` VARCHAR(45) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$db->query($sql_query);

$sql_query = "CREATE TABLE IF NOT EXISTS `" . $dbprefix . "pur_estimates` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `sent` TINYINT(1) NOT NULL DEFAULT '0',
      `datesend` DATETIME NULL,
      `vendor` INT(11) NOT NULL,
      `deleted_vendor_name` VARCHAR(100) NULL,
      `pur_request` INT(11) NOT NULL,
      `number` INT(11) NOT NULL,
      `prefix` varchar(50) NULL,
      `number_format` INT(11) NOT NULL DEFAULT '0',
      `hash` VARCHAR(32) NULL,
      `datecreated` DATETIME NOT NULL,
      `date` DATE NOT NULL,
      `expirydate` DATE NULL,
      `currency` VARCHAR(20) NOT NULL,
      `subtotal` DECIMAL(15,2) NOT NULL,
      `total_tax` DECIMAL(15,2) NOT NULL,
      `total` DECIMAL(15,2) NOT NULL,
      `adjustment` DECIMAL(15,2) NULL,
      `addedfrom` INT(11) NOT NULL,
      `status` INT(11) NOT NULL DEFAULT '1',
      `vendornote` TEXT NULL,
      `adminnote` TEXT NULL,
      `discount_percent` DECIMAL(15,2) NULL DEFAULT '0.00',
      `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
      `discount_type` VARCHAR(30) NULL,
      `invoiceid` INT(11) NULL,
      `invoiced_date` DATETIME NULL,
      `terms` TEXT NULL,
      `reference_no` VARCHAR(100) NULL,
      `buyer` INT(11) NOT NULL DEFAULT '0',
      `billing_street` VARCHAR(200) NULL,
      `billing_city` VARCHAR(100) NULL,
      `billing_state` VARCHAR(100) NULL,
      `billing_zip` VARCHAR(100) NULL,
      `billing_country` INT(11) NULL,
      `shipping_street` VARCHAR(200) NULL,
      `shipping_city` VARCHAR(100) NULL,
      `shipping_state` VARCHAR(100) NULL,
      `shipping_zip` VARCHAR(100) NULL,
      `shipping_country` INT(11) NULL,
      `include_shipping` TINYINT(1) NOT NULL,
      `show_shipping_on_estimate` TINYINT(1) NOT NULL DEFAULT '1',
      `show_quantity_as` INT(11) NOT NULL DEFAULT '1',
      `pipeline_order` INT(11) NOT NULL DEFAULT '0',
      `is_expiry_notified` INT(11) NOT NULL DEFAULT '0',
      `acceptance_firstname` VARCHAR(50) NULL,
      `acceptance_lastname` VARCHAR(50) NULL,
      `acceptance_email` VARCHAR(100) NULL,
      `acceptance_date` DATETIME NULL,
      `acceptance_ip` VARCHAR(40) NULL,
      `signature` VARCHAR(40) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$db->query($sql_query);

$sql_query = "CREATE TABLE IF NOT EXISTS `" . $dbprefix . "ware_unit_type` (
      `unit_type_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `unit_code` varchar(100) NULL,
      `unit_name` text NULL,
      `unit_symbol` text NULL,
      `order` int(10) NULL,
      `display` int(1) NULL COMMENT  'display 1: display (yes)  0: not displayed (no)',
      `note` text NULL,
      PRIMARY KEY (`unit_type_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$db->query($sql_query);

$sql_query = "CREATE TABLE IF NOT EXISTS `" . $dbprefix . "wh_sub_group` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `group_id` int(11)  NULL,
      `sub_group_code` varchar(100) NULL,
      `sub_group_name` text NULL,
      `order` int(10) NULL,
      `display` int(1) NULL COMMENT  'display 1: display (yes)  0: not displayed (no)',
      `note` text NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$db->query($sql_query);

if (!$db->fieldExists('commodity_group_code', $dbprefix.'item_categories')) {
  $db->query('ALTER TABLE `' . $dbprefix . "item_categories`
    ADD COLUMN `commodity_group_code` text NULL
    ;");
}

if (!$db->fieldExists('order', $dbprefix.'item_categories')) {
  $db->query('ALTER TABLE `' . $dbprefix . "item_categories`
    ADD COLUMN `order` INT(10) NULL
    ;");
}

if (!$db->fieldExists('display', $dbprefix.'item_categories')) {
  $db->query('ALTER TABLE `' . $dbprefix . "item_categories`
    ADD COLUMN `display` INT(1) NULL
    ;");
}

if (!$db->fieldExists('note', $dbprefix.'item_categories')) {
  $db->query('ALTER TABLE `' . $dbprefix . "item_categories`
    ADD COLUMN `note` TEXT NULL
    ;");
}

if (!$db->tableExists($dbprefix . 'wh_sub_group')) {
  $db->query('CREATE TABLE `' . $dbprefix . "wh_sub_group` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sub_group_code` varchar(100) NULL,
  `sub_group_name` text NULL,
  `order` int(10) NULL,
  `display` int(1) NULL COMMENT  'display 1: display (yes)  0: not displayed (no)',
  `note` text NULL,
  `group_id` int(11)  NULL,

  PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

add_setting('pur_order_prefix', '#PO');
add_setting('pur_request_prefix', '#PR');
add_setting('pur_inv_prefix', '#BILL');
add_setting('debit_note_prefix', '#DBN');
add_setting('pur_invoice_auto_operations_hour', 21);
add_setting('pur_terms_and_conditions', '');
add_setting('vendor_note', '');
add_setting('next_purchase_order_number', 1);
add_setting('next_purchase_request_number', 1);
add_setting('purchase_order_setting', 1);
add_setting('show_purchase_tax_column', 0);
add_setting('item_by_vendor', 0);
add_setting('po_only_prefix_and_number', 0);
add_setting('send_email_welcome_for_new_contact', 1);
add_setting('reset_purchase_order_number_every_month', 1);
add_setting('pur_order_return_number_prefix', '#OR');
add_setting('next_pur_order_return_number', 1);

add_setting('pur_next_inv_number', 1);
add_setting('pur_estimate_prefix', '#EST');
add_setting('next_pur_pur_estimate_number', 1);


if (!$db->tableExists($dbprefix . 'pur_vendor_cate')) {
  $db->query('CREATE TABLE `' . $dbprefix . "pur_vendor_cate` (
   `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_name` VARCHAR(255) NULL,
    `description` text NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . 'pur_approval_setting')) {
  $db->query('CREATE TABLE `' . $dbprefix ."pur_approval_setting` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `related` VARCHAR(255) NOT NULL,
  `setting` LONGTEXT NOT NULL,
  PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . 'pur_approval_details')) {
  $db->query('CREATE TABLE `' . $dbprefix ."pur_approval_details` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `rel_id` INT(11) NOT NULL,
  `rel_type` VARCHAR(45) NOT NULL,
  `staffid` VARCHAR(45) NULL,
  `approve` VARCHAR(45) NULL,
  `note` TEXT NULL,
  `date` DATETIME NULL,
  `approve_action` VARCHAR(255) NULL,
  `reject_action` VARCHAR(255) NULL,
  `approve_value` VARCHAR(255) NULL,
  `reject_value` VARCHAR(255) NULL,
  `staff_approve` INT(11) NULL,
  `action` VARCHAR(45) NULL,
  `sender` INT(11) NULL,
  `date_send` DATETIME NULL,
  PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->fieldExists('tax', $dbprefix.'items')) {
  $db->query('ALTER TABLE `' . $dbprefix . "items`
    ADD COLUMN `tax` int(11) NULL,
    ADD COLUMN `tax2` int(11) NULL
    ;");
}

if (!$db->fieldExists('commodity_code', $dbprefix.'items')) {
  $db->query('ALTER TABLE `' . $dbprefix . "items`
    ADD COLUMN `commodity_code` varchar(100) NULL,
    ADD COLUMN `commodity_barcode` text NULL,
    ADD COLUMN `commodity_type` int(11) NULL,
    ADD COLUMN `warehouse_id` int(11) NULL,
    ADD COLUMN `origin` varchar(100) NULL,
    ADD COLUMN `color_id` int(11) NULL,
    ADD COLUMN `style_id` int(11) NULL,
    ADD COLUMN `model_id` int(11) NULL,
    ADD COLUMN `size_id` int(11) NULL,
    ADD COLUMN `unit_id` int(11) NULL,
    ADD COLUMN `sku_code` varchar(200)  NULL,
    ADD COLUMN `sku_name` varchar(200)  NULL,
    ADD COLUMN `purchase_price` decimal(15,2)  NULL DEFAULT '0.00',
    ADD COLUMN `sub_group` varchar(200)  NULL,
    ADD COLUMN `commodity_name` varchar(200) NOT NULL,
    ADD COLUMN `color` text NULL,
    ADD COLUMN `guarantee` text  NULL,
    ADD COLUMN `profif_ratio` text  NULL,
    ADD COLUMN `parent_id` int(11)  NULL  DEFAULT NULL,
    ADD COLUMN `attributes` LONGTEXT  NULL,
    ADD COLUMN `parent_attributes` LONGTEXT  NULL,
    ADD COLUMN `can_be_sold` VARCHAR(100) NULL DEFAULT 'can_be_sold',
    ADD COLUMN `can_be_purchased` VARCHAR(100) NULL DEFAULT 'can_be_purchased', 
    ADD COLUMN `can_be_manufacturing` VARCHAR(100) NULL DEFAULT 'can_be_manufacturing',
    ADD COLUMN `can_be_inventory` VARCHAR(100) NULL DEFAULT 'can_be_inventory' 
    ;");
}

if (!$db->fieldExists('without_checking_warehouse', $dbprefix.'items')) {
  $db->query('ALTER TABLE `' . $dbprefix . "items`
    ADD COLUMN `without_checking_warehouse` int(11) NULL default 0
    ;");
}

if (!$db->fieldExists('long_descriptions', $dbprefix.'items')) {
$db->query('ALTER TABLE `' . $dbprefix . "items`
      ADD COLUMN `long_descriptions` LONGTEXT NULL
  ;");
}

if($db->fieldExists('country', $dbprefix.'pur_vendor')){
  $db->query('ALTER TABLE `' . $dbprefix . "pur_vendor`
      CHANGE COLUMN `country` `country` VARCHAR(195) NULL DEFAULT NULL 
  ;");
}

if($db->fieldExists('billing_country', $dbprefix.'pur_vendor')){
  $db->query('ALTER TABLE `' . $dbprefix . "pur_vendor`
      CHANGE COLUMN `billing_country` `billing_country` VARCHAR(195) NULL DEFAULT NULL 
  ;");
}

if($db->fieldExists('shipping_country', $dbprefix.'pur_vendor')){
  $db->query('ALTER TABLE `' . $dbprefix . "pur_vendor`
      CHANGE COLUMN `shipping_country` `shipping_country` VARCHAR(195) NULL DEFAULT NULL 
  ;");
}

if($db->fieldExists('default_currency', $dbprefix.'pur_vendor')){
  $db->query('ALTER TABLE `' . $dbprefix . "pur_vendor`
      CHANGE COLUMN `default_currency` `default_currency` VARCHAR(20) NULL DEFAULT NULL 
  ;");
}

if (!$db->fieldExists('vendor_code' ,$dbprefix . 'pur_vendor')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_vendor`
      ADD COLUMN `vendor_code` VARCHAR(100)  NULL
  ;");
}

if (!$db->fieldExists('category' ,$dbprefix . 'pur_vendor')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_vendor`
      ADD COLUMN `category` TEXT  NULL
  ;");
}

if (!$db->fieldExists('bank_detail' ,$dbprefix . 'pur_vendor')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_vendor`
      ADD COLUMN `bank_detail` TEXT  NULL
  ;");
}

if (!$db->fieldExists('payment_terms' ,$dbprefix . 'pur_vendor')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_vendor`
      ADD COLUMN `payment_terms` TEXT  NULL
  ;");
}

if (!$db->fieldExists('return_within_day' ,$dbprefix . 'pur_vendor')) {
  $db->query('ALTER TABLE `' . $dbprefix . 'pur_vendor`
  ADD COLUMN `return_within_day` INT(11) NULL
  ');
}

if (!$db->fieldExists('return_order_fee' ,$dbprefix . 'pur_vendor')) {
  $db->query('ALTER TABLE `' . $dbprefix . 'pur_vendor`
  ADD COLUMN `return_order_fee` DECIMAL(15,2) NULL
  ');
}

if (!$db->fieldExists('return_policies' ,$dbprefix . 'pur_vendor')) {
  $db->query('ALTER TABLE `' . $dbprefix . 'pur_vendor`
  ADD COLUMN `return_policies` TEXT NULL
 ');
}

if (!$db->tableExists($dbprefix . 'pur_vendor_items')) {
  $db->query('CREATE TABLE `' . $dbprefix . "pur_vendor_items` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `vendor` int(11) NOT NULL,
    `group_items` int(11) NULL,
    `items` int(11) NOT NULL,
    `add_from` int(11) NULL,
    `datecreate` DATE NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . 'pur_request')) {
    $db->query('CREATE TABLE `' . $dbprefix .'pur_request` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pur_rq_code` VARCHAR(45) NOT NULL,
  `pur_rq_name` VARCHAR(100) NOT NULL,
  `rq_description` TEXT NULL,
  `requester` INT(11) NOT NULL,
  `department` INT(11) NOT NULL,
  `request_date` DATETIME NOT NULL,
  `status` INT(11) NULL,
  `status_goods` INT(11) NOT NULL DEFAULT "0", 
  PRIMARY KEY (`id`));');
}

if (!$db->tableExists($dbprefix . 'pur_request_detail')) {
    $db->query('CREATE TABLE `' . $dbprefix .'pur_request_detail` (
  `prd_id` INT(11) NOT NULL AUTO_INCREMENT,
  `pur_request` INT(11) NOT NULL,
  `item_code` VARCHAR(100) NOT NULL,
  `unit_id` INT(11) NULL,
  `unit_price` DECIMAL(15,2) NULL,
  `quantity` int(11) NOT NULL,
  `into_money` DECIMAL(15,2) NULL,
  `inventory_quantity` int(11) NULL DEFAULT "0",
  PRIMARY KEY (`prd_id`));');
}

if ($db->fieldExists('unit_price' ,$dbprefix . 'pur_request_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_request_detail`
    CHANGE COLUMN `unit_price` `unit_price` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if ($db->fieldExists('into_money' ,$dbprefix . 'pur_request_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_request_detail`
    CHANGE COLUMN `into_money` `into_money` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if (!$db->fieldExists('hash' ,$dbprefix . 'pur_request')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_request`
    ADD COLUMN `hash` VARCHAR(32) NULL
  ;");
}

if (!$db->fieldExists('type' ,$dbprefix . 'pur_request')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_request`
      ADD COLUMN `type` TEXT  NULL
  ;");
}

if (!$db->fieldExists('project' ,$dbprefix . 'pur_request')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_request`
      ADD COLUMN `project` INT(11)  NULL
  ;");
}

if (!$db->fieldExists('number' ,$dbprefix . 'pur_request')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_request`
      ADD COLUMN `number` INT(11)  NULL
  ;");
}

if (!$db->fieldExists('from_items' ,$dbprefix . 'pur_request')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_request`
      ADD COLUMN `from_items` INT(2)  NULL DEFAULT '1'
  ;");
}

if (!$db->fieldExists('item_text' ,$dbprefix . 'pur_request_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_request_detail`
      ADD COLUMN `item_text` TEXT  NULL
  ;");
}

//version 1.10 Purchase request detail
if (!$db->fieldExists('tax' ,$dbprefix . 'pur_request_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_request_detail`
      ADD COLUMN `tax` TEXT  NULL
  ;");
}

//version 1.10 Purchase request detail
if (!$db->fieldExists('tax_rate' ,$dbprefix . 'pur_request_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_request_detail`
      ADD COLUMN `tax_rate` TEXT  NULL
  ;");
}

//version 1.10 Purchase request detail
if (!$db->fieldExists('tax_value' ,$dbprefix . 'pur_request_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_request_detail`
      ADD COLUMN `tax_value` DECIMAL(15,2)  NULL
  ;");
}

//version 1.10 Purchase request detail
if (!$db->fieldExists('total' ,$dbprefix . 'pur_request_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_request_detail`
      ADD COLUMN `total` DECIMAL(15,2)  NULL
  ;");
}

if (!$db->fieldExists('subtotal' ,$dbprefix . 'pur_request')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_request`
      ADD COLUMN `subtotal` DECIMAL(15,2) NULL
  ;");
}

if (!$db->fieldExists('total_tax' ,$dbprefix . 'pur_request')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_request`
      ADD COLUMN `total_tax` DECIMAL(15,2) NULL
  ;");
}

if (!$db->fieldExists('total' ,$dbprefix . 'pur_request')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_request`
      ADD COLUMN `total` DECIMAL(15,2) NULL
  ;");
}

if (!$db->fieldExists('sale_invoice' ,$dbprefix . 'pur_request')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_request`
      ADD COLUMN `sale_invoice` int(11) NULL
  ;");
}

if ($db->fieldExists('quantity' ,$dbprefix  . 'pur_request_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix  . "pur_request_detail`
       CHANGE COLUMN `quantity` `quantity` DECIMAL(15,2) NOT NULL 
  ;");
}

if (!$db->fieldExists('compare_note' ,$dbprefix  . 'pur_request')) { 
  $db->query('ALTER TABLE `' . $dbprefix  . "pur_request`
      ADD COLUMN `compare_note` text NULL
  ;");
}

if (!$db->fieldExists('tax_name' ,$dbprefix  . 'pur_request_detail')){
    $db->query('ALTER TABLE `' . $dbprefix  . "pur_request_detail`
  ADD COLUMN `tax_name` TEXT NULL 
  ;");
}

if (!$db->fieldExists('send_to_vendors' ,$dbprefix  . 'pur_request')){
    $db->query('ALTER TABLE `' . $dbprefix  . "pur_request`
  ADD COLUMN `send_to_vendors` TEXT NULL 
  ;");
}

if (!$db->fieldExists('currency' ,$dbprefix  . 'pur_request')){
    $db->query('ALTER TABLE `' . $dbprefix  . "pur_request`
  ADD COLUMN `currency` VARCHAR(20) NULL
  ;");
}

if (!$db->fieldExists('currency_rate' ,$dbprefix  . 'pur_request')) {
  $db->query('ALTER TABLE `' . $dbprefix  . 'pur_request`
  ADD COLUMN `currency_rate` DECIMAL(15,6) NULL
  ');
}

if (!$db->fieldExists('from_currency' ,$dbprefix  . 'pur_request')) {
  $db->query('ALTER TABLE `' . $dbprefix  . 'pur_request`
  ADD COLUMN `from_currency` VARCHAR(20) NULL
  ');
}

if (!$db->fieldExists('to_currency' ,$dbprefix . 'pur_request')) {
  $db->query('ALTER TABLE `' . $dbprefix . 'pur_request`
  ADD COLUMN `to_currency` VARCHAR(20) NULL
  ');
}

if (!$db->fieldExists('sale_estimate' ,$dbprefix . 'pur_request')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_request`
    ADD COLUMN `sale_estimate` INT(11) NULL
  ;");
}


if (!$db->fieldExists('pur_request_id', $dbprefix.'notifications')) {
  $db->query('ALTER TABLE `' . $dbprefix . "notifications`

  ADD COLUMN `pur_request_id` int(1) NOT NULL DEFAULT '0',
  ADD COLUMN `pur_quotation_id` int(1) NOT NULL DEFAULT '0',
  ADD COLUMN `pur_order_id` int(1) NOT NULL DEFAULT '0',
  ADD COLUMN `pur_payment_id` int(1) NOT NULL DEFAULT '0'
  ;");
}

if (!$db->fieldExists('sender', $dbprefix .'pur_approval_details')) {
    $db->query('ALTER TABLE `'.$dbprefix . 'pur_approval_details` 
ADD COLUMN `sender` INT(11) NULL AFTER `action`;');            
}

if (!$db->fieldExists('date_send', $dbprefix .'pur_approval_details')) {
    $db->query('ALTER TABLE `'.$dbprefix . 'pur_approval_details` 
ADD COLUMN `date_send` DATETIME NULL AFTER `sender`;');            
}

if (!$db->tableExists($dbprefix . 'files')) {
  $db->query('CREATE TABLE `' . $dbprefix . 'files` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `rel_id` int(11) NOT NULL,
    `rel_type` varchar(20) NOT NULL,
    `file_name` varchar(191) NOT NULL,
    `filetype` varchar(40) DEFAULT NULL,
    `visible_to_customer` int(11) NOT NULL DEFAULT "0",
    `attachment_key` varchar(32) DEFAULT NULL,
    `external` varchar(40) DEFAULT NULL,
    `external_link` text,
    `thumbnail_link` text COMMENT "For external usage",
    `staffid` int(11) NOT NULL,
    `contact_id` int(11) DEFAULT "0",
    `task_comment_id` int(11) NOT NULL DEFAULT "0",
    `dateadded` datetime NOT NULL,
    PRIMARY KEY (`id`),
    KEY `rel_id` (`rel_id`),
    KEY `rel_type` (`rel_type`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
}

if (!$db->tableExists($dbprefix . 'pur_estimates')) {
    $db->query('CREATE TABLE `' . $dbprefix . "pur_estimates` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `sent` TINYINT(1) NOT NULL DEFAULT '0',
      `datesend` DATETIME NULL,
      `vendor` INT(11) NOT NULL,
      `deleted_vendor_name` VARCHAR(100) NULL,
      `pur_request` INT(11) NULL,
      `number` INT(11) NOT NULL,
      `prefix` varchar(50) NULL,
      `number_format` INT(11) NOT NULL DEFAULT '0',
      `hash` VARCHAR(32) NULL,
      `datecreated` DATETIME NOT NULL,
      `date` DATE NOT NULL,
      `expirydate` DATE NULL,
      `currency` VARCHAR(20) NULL,
      `subtotal` DECIMAL(15,2) NOT NULL,
      `total_tax` DECIMAL(15,2) NOT NULL,
      `total` DECIMAL(15,2) NOT NULL,
      `adjustment` DECIMAL(15,2) NULL,
      `addedfrom` INT(11) NOT NULL,
      `status` INT(11) NOT NULL DEFAULT '1',
      `vendornote` TEXT NULL,
      `adminnote` TEXT NULL,
      `discount_percent` DECIMAL(15,2) NULL DEFAULT '0.00',
      `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
      `discount_type` VARCHAR(30) NULL,
      `invoiceid` INT(11) NULL,
      `invoiced_date` DATETIME NULL,
      `terms` TEXT NULL,
      `reference_no` VARCHAR(100) NULL,
      `buyer` INT(11) NOT NULL DEFAULT '0',
      `billing_street` VARCHAR(200) NULL,
      `billing_city` VARCHAR(100) NULL,
      `billing_state` VARCHAR(100) NULL,
      `billing_zip` VARCHAR(100) NULL,
      `billing_country` INT(11) NULL,
      `shipping_street` VARCHAR(200) NULL,
      `shipping_city` VARCHAR(100) NULL,
      `shipping_state` VARCHAR(100) NULL,
      `shipping_zip` VARCHAR(100) NULL,
      `shipping_country` INT(11) NULL,
      `include_shipping` TINYINT(1) NOT NULL,
      `show_shipping_on_estimate` TINYINT(1) NOT NULL DEFAULT '1',
      `show_quantity_as` INT(11) NOT NULL DEFAULT '1',
      `pipeline_order` INT(11) NOT NULL DEFAULT '0',
      `is_expiry_notified` INT(11) NOT NULL DEFAULT '0',
      `acceptance_firstname` VARCHAR(50) NULL,
      `acceptance_lastname` VARCHAR(50) NULL,
      `acceptance_email` VARCHAR(100) NULL,
      `acceptance_date` DATETIME NULL,
      `acceptance_ip` VARCHAR(40) NULL,
      `signature` VARCHAR(40) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if ($db->fieldExists('pur_request' ,$dbprefix . 'pur_estimates')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_estimates`
    CHANGE COLUMN `pur_request` `pur_request` INT(11) NULL
  ;");
}

if (!$db->fieldExists('make_a_contract' ,$dbprefix . 'pur_estimates')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_estimates`
      ADD COLUMN `make_a_contract` text NULL
  ;");
}

if (!$db->fieldExists('currency_rate' ,$dbprefix . 'pur_estimates')) {
  $db->query('ALTER TABLE `' . $dbprefix . 'pur_estimates`
  ADD COLUMN `currency_rate` DECIMAL(15,6) NULL
  ');
}

if (!$db->fieldExists('from_currency' ,$dbprefix . 'pur_estimates')) {
  $db->query('ALTER TABLE `' . $dbprefix . 'pur_estimates`
  ADD COLUMN `from_currency` VARCHAR(20) NULL
  ');
}

if (!$db->fieldExists('to_currency' ,$dbprefix . 'pur_estimates')) {
  $db->query('ALTER TABLE `' . $dbprefix . 'pur_estimates`
  ADD COLUMN `to_currency` VARCHAR(20) NULL
  ');
}

if (!$db->fieldExists('shipping_fee' ,$dbprefix . 'pur_estimates')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_estimates`
      ADD COLUMN `shipping_fee` decimal(15,2) NULL
  ;");
}

if (!$db->tableExists($dbprefix . 'pur_estimate_detail')) {
    $db->query('CREATE TABLE `' . $dbprefix .'pur_estimate_detail` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pur_estimate` INT(11) NOT NULL,
  `item_code` VARCHAR(100) NOT NULL,
  `unit_id` INT(11) NULL,
  `unit_price` DECIMAL(15,0) NULL,
  `quantity` int(11) NOT NULL,
  `into_money` DECIMAL(15,0) NULL,
  `tax` text NULL,
  `total` DECIMAL(15,0) NULL,
  PRIMARY KEY (`id`));');
}

if (!$db->fieldExists('discount_%' ,$dbprefix . 'pur_estimate_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_estimate_detail`
    ADD COLUMN `discount_%` DECIMAL(15,0) NULL AFTER `total`
  ;");
}

if (!$db->fieldExists('discount_money' ,$dbprefix . 'pur_estimate_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_estimate_detail`
    ADD COLUMN `discount_money` DECIMAL(15,0) NULL AFTER `total`
  ;");
}

if (!$db->fieldExists('total_money' ,$dbprefix . 'pur_estimate_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_estimate_detail`
    ADD COLUMN `total_money` DECIMAL(15,0) NULL AFTER `total`
  ;");
}

if ($db->fieldExists('unit_price' ,$dbprefix . 'pur_estimate_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_estimate_detail`
    CHANGE COLUMN `unit_price` `unit_price` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if ($db->fieldExists('into_money' ,$dbprefix . 'pur_estimate_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_estimate_detail`
    CHANGE COLUMN `into_money` `into_money` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if ($db->fieldExists('total' ,$dbprefix . 'pur_estimate_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_estimate_detail`
    CHANGE COLUMN `total` `total` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if ($db->fieldExists('total_money' ,$dbprefix . 'pur_estimate_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_estimate_detail`
    CHANGE COLUMN `total_money` `total_money` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if ($db->fieldExists('discount_money' ,$dbprefix . 'pur_estimate_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_estimate_detail`
    CHANGE COLUMN `discount_money` `discount_money` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if ($db->fieldExists('discount_%' ,$dbprefix . 'pur_estimate_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_estimate_detail`
    CHANGE COLUMN `discount_%` `discount_%` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if (!$db->fieldExists('tax_value' ,$dbprefix . 'pur_estimate_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_estimate_detail`
      ADD COLUMN `tax_value` DECIMAL(15,2) NULL
  ;");
}

if (!$db->fieldExists('tax_rate' ,$dbprefix . 'pur_estimate_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_estimate_detail`
      ADD COLUMN `tax_rate` TEXT NULL
  ;");
}

if ($db->fieldExists('quantity' ,$dbprefix . 'pur_estimate_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_estimate_detail`
       CHANGE COLUMN `quantity` `quantity` DECIMAL(15,2) NOT NULL 
  ;");
}

if ($db->fieldExists('unit_price' ,$dbprefix . 'pur_estimate_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_estimate_detail`
    CHANGE COLUMN `unit_price` `unit_price` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if (!$db->fieldExists('tax_name' ,$dbprefix . 'pur_estimate_detail')){
    $db->query('ALTER TABLE `' . $dbprefix . "pur_estimate_detail`
  ADD COLUMN `tax_name` TEXT NULL 
  ;");
}

if (!$db->fieldExists('item_name' ,$dbprefix . 'pur_estimate_detail')){
    $db->query('ALTER TABLE `' . $dbprefix . "pur_estimate_detail`
  ADD COLUMN `item_name` TEXT NULL 
  ;");
}

if (!$db->tableExists($dbprefix . 'pur_orders')) {
    $db->query('CREATE TABLE `' . $dbprefix . "pur_orders` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `pur_order_name` varchar(100) NOT NULL,
      `vendor` INT(11) NOT NULL,
      `estimate` INT(11) NOT NULL,
      `pur_order_number` VARCHAR(30) NOT NULL,
      `order_date` date NOT NULL,
      `status` INT(32) NOT NULL DEFAULT '1',
      `approve_status` INT(32) NOT NULL DEFAULT '1',
      `datecreated` DATETIME NOT NULL,
      `days_owed` INT(11) NOT NULL,
      `delivery_date` DATE NULL,
      `subtotal` DECIMAL(15,2) NOT NULL,
      `total_tax` DECIMAL(15,2) NOT NULL,
      `total` DECIMAL(15,2) NOT NULL,
      `addedfrom` INT(11) NOT NULL,
      `vendornote` TEXT NULL,
      `terms` TEXT NULL,
      `discount_percent` DECIMAL(15,2) NULL DEFAULT '0.00',
      `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
      `discount_type` VARCHAR(30) NULL,
      `buyer` INT(11) NOT NULL DEFAULT '0',
      `status_goods` INT(11) NOT NULL DEFAULT '0', 
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . 'pur_order_detail')) {
    $db->query('CREATE TABLE `' . $dbprefix .'pur_order_detail` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pur_order` INT(11) NOT NULL,
  `item_code` VARCHAR(100) NOT NULL,
  `unit_id` INT(11) NULL,
  `unit_price` DECIMAL(15,0) NULL,
  `quantity` int(11) NOT NULL,
  `into_money` DECIMAL(15,0) NULL,
  `tax` text NULL,
  `total` DECIMAL(15,0) NULL,
  `discount_%` DECIMAL(15,0) NULL,
  `discount_money` DECIMAL(15,0) NULL,
  `total_money` DECIMAL(15,0) NULL,
  PRIMARY KEY (`id`));');
}

if (!$db->fieldExists('number', $dbprefix .'pur_orders')) {
    $db->query('ALTER TABLE `'.$dbprefix . 'pur_orders` 
  ADD COLUMN `number` INT(11) NULL;');            
}

if (!$db->fieldExists('expense_convert', $dbprefix .'pur_orders')) {
    $db->query('ALTER TABLE `'.$dbprefix . 'pur_orders` 
  ADD COLUMN `expense_convert` INT(11) NULL DEFAULT "0";');            
}

// purchase order hash
if (!$db->fieldExists('hash' ,$dbprefix . 'pur_orders')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_orders`
    ADD COLUMN `hash` VARCHAR(32) NULL
  ;");
}

// version 1.0.6  purchase order client
if (!$db->fieldExists('clients' ,$dbprefix . 'pur_orders')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_orders`
    ADD COLUMN `clients` TEXT NULL
  ;");
}

if (!$db->fieldExists('delivery_status' ,$dbprefix . 'pur_orders')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_orders`
      ADD COLUMN `delivery_status` int(2)  NULL DEFAULT '0'
  ;");
}

if (!$db->fieldExists('type' ,$dbprefix . 'pur_orders')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_orders`
      ADD COLUMN `type` TEXT  NULL
  ;");
}

if (!$db->fieldExists('project' ,$dbprefix . 'pur_orders')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_orders`
      ADD COLUMN `project` INT(11)  NULL
  ;");
}

if (!$db->fieldExists('pur_request' ,$dbprefix . 'pur_orders')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_orders`
      ADD COLUMN `pur_request` INT(11)  NULL
  ;");
}

if (!$db->fieldExists('department' ,$dbprefix . 'pur_orders')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_orders`
      ADD COLUMN `department` INT(11)  NULL
  ;");
}

if (!$db->fieldExists('tax_order_rate' ,$dbprefix . 'pur_orders')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_orders`
      ADD COLUMN `tax_order_rate` DECIMAL(15,2)  NULL
  ;");
}

if (!$db->fieldExists('tax_order_amount' ,$dbprefix . 'pur_orders')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_orders`
      ADD COLUMN `tax_order_amount` DECIMAL(15,2)  NULL
  ;");
}

if (!$db->fieldExists('sale_invoice' ,$dbprefix . 'pur_orders')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_orders`
      ADD COLUMN `sale_invoice` int(11) NULL
  ;");
}


if (!$db->fieldExists('currency' ,$dbprefix . 'pur_orders')){
    $db->query('ALTER TABLE `' . $dbprefix . "pur_orders`
  ADD COLUMN `currency` VARCHAR(20) NULL 
  ;");
}

// --- Version 1.2.2

if (!$db->fieldExists('order_status' ,$dbprefix . 'pur_orders')){
    $db->query('ALTER TABLE `' . $dbprefix . "pur_orders`
  ADD COLUMN `order_status` VARCHAR(30) NULL
  ;");
}

if (!$db->fieldExists('shipping_note' ,$dbprefix . 'pur_orders')){
    $db->query('ALTER TABLE `' . $dbprefix . "pur_orders`
  ADD COLUMN `shipping_note` TEXT NULL
  ;");
}

if (!$db->fieldExists('currency_rate' ,$dbprefix . 'pur_orders')) {
  $db->query('ALTER TABLE `' . $dbprefix . 'pur_orders`
  ADD COLUMN `currency_rate` DECIMAL(15,6) NULL
  ');
}

if (!$db->fieldExists('from_currency' ,$dbprefix . 'pur_orders')) {
  $db->query('ALTER TABLE `' . $dbprefix . 'pur_orders`
  ADD COLUMN `from_currency` VARCHAR(20) NULL
  ');
}

if (!$db->fieldExists('to_currency' ,$dbprefix . 'pur_orders')) {
  $db->query('ALTER TABLE `' . $dbprefix . 'pur_orders`
  ADD COLUMN `to_currency` VARCHAR(20) NULL
  ');
}

if (!$db->fieldExists('shipping_fee' ,$dbprefix . 'pur_orders')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_orders`
      ADD COLUMN `shipping_fee` decimal(15,2) NULL
  ;");
}

if (!$db->fieldExists('description' ,$dbprefix . 'pur_order_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_order_detail`
    ADD COLUMN `description` TEXT NULL AFTER `item_code`
  ;");
}

if ($db->fieldExists('unit_price' ,$dbprefix . 'pur_order_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_order_detail`
    CHANGE COLUMN `unit_price` `unit_price` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if ($db->fieldExists('into_money' ,$dbprefix . 'pur_order_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_order_detail`
    CHANGE COLUMN `into_money` `into_money` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if ($db->fieldExists('total' ,$dbprefix . 'pur_order_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_order_detail`
    CHANGE COLUMN `total` `total` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if ($db->fieldExists('discount_%' ,$dbprefix . 'pur_order_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_order_detail`
    CHANGE COLUMN `discount_%` `discount_%` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if ($db->fieldExists('discount_money' ,$dbprefix . 'pur_order_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_order_detail`
    CHANGE COLUMN `discount_money` `discount_money` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if ($db->fieldExists('total_money' ,$dbprefix . 'pur_order_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_order_detail`
    CHANGE COLUMN `total_money` `total_money` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if (!$db->fieldExists('tax_value' ,$dbprefix . 'pur_order_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_order_detail`
      ADD COLUMN `tax_value` DECIMAL(15,2) NULL
  ;");
}

if (!$db->fieldExists('tax_rate' ,$dbprefix . 'pur_order_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_order_detail`
      ADD COLUMN `tax_rate` TEXT NULL
  ;");
}

if ($db->fieldExists('quantity' ,$dbprefix . 'pur_order_detail')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_order_detail`
      CHANGE COLUMN `quantity` `quantity` DECIMAL(15,2) NOT NULL 
  ;");
}

if (!$db->fieldExists('tax_name' ,$dbprefix . 'pur_order_detail')){
    $db->query('ALTER TABLE `' . $dbprefix . "pur_order_detail`
  ADD COLUMN `tax_name` TEXT NULL 
  ;");
}

if (!$db->fieldExists('item_name' ,$dbprefix . 'pur_order_detail')){
    $db->query('ALTER TABLE `' . $dbprefix . "pur_order_detail`
  ADD COLUMN `item_name` TEXT NULL 
  ;");
}

if (!$db->tableExists($dbprefix . 'pur_invoices')) {
    $db->query('CREATE TABLE `' . $dbprefix . "pur_invoices` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `number` int(11) NOT NULL,
      `invoice_number` TEXT NULL,
      `invoice_date` DATE NULL,
      `subtotal` DECIMAL(15,2) NULL,
      `tax_rate` INT(11) NULL,
      `tax` DECIMAL(15,2) NULL,
      `total` DECIMAL(15,2) NULL,
      `contract` int(11) NULL,
      `vendor` int(11) NULL,
      `transactionid` MEDIUMTEXT NULL,
      `transaction_date` DATE NULL,
      `payment_request_status` VARCHAR(30) NULL,
      `payment_status` VARCHAR(30) NULL,
      `vendor_note` TEXT NULL, 
      `adminnote` TEXT NULL, 
      `terms` TEXT NULL,
      `add_from` INT(11) NULL,
      `date_add` DATE NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if ($db->fieldExists('contract' ,$dbprefix . 'pur_invoices')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_invoices`
    CHANGE COLUMN `contract` `contract` INT(11) NULL
  ;");
}

if ($db->fieldExists('vendor' ,$dbprefix . 'pur_invoices')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_invoices`
    CHANGE COLUMN `vendor` `vendor` INT(11) NULL
  ;");
}

if (!$db->fieldExists('pur_order' ,$dbprefix . 'pur_invoices')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_invoices`
    ADD COLUMN `pur_order` INT(11) NULL
  ;");
}

if (!$db->fieldExists('recurring' ,$dbprefix . 'pur_invoices')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_invoices`
      ADD COLUMN `recurring` INT(11) NULL
  ;");
}

if (!$db->fieldExists('recurring_type' ,$dbprefix . 'pur_invoices')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_invoices`
      ADD COLUMN `recurring_type` VARCHAR(10) NULL
  ;");
}

if (!$db->fieldExists('cycles' ,$dbprefix . 'pur_invoices')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_invoices`
      ADD COLUMN `cycles` INT(11) NULL DEFAULT '0'
  ;");
}

if (!$db->fieldExists('total_cycles' ,$dbprefix . 'pur_invoices')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_invoices`
      ADD COLUMN `total_cycles` INT(11) NULL DEFAULT '0'
  ;");
}

if (!$db->fieldExists('last_recurring_date' ,$dbprefix . 'pur_invoices')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_invoices`
      ADD COLUMN `last_recurring_date` DATE NULL
  ;");
}

if (!$db->fieldExists('is_recurring_from' ,$dbprefix . 'pur_invoices')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_invoices`
      ADD COLUMN `is_recurring_from` INT(11) NULL
  ;");
}

if (!$db->fieldExists('duedate' ,$dbprefix . 'pur_invoices')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_invoices`
      ADD COLUMN `duedate` DATE NULL
  ;");
}

if (!$db->fieldExists('add_from_type' ,$dbprefix . 'pur_invoices')){
    $db->query('ALTER TABLE `' . $dbprefix . "pur_invoices`
  ADD COLUMN `add_from_type` varchar(20) NULL
  ;");
}

if (!$db->fieldExists('currency' ,$dbprefix . 'pur_invoices')){
    $db->query('ALTER TABLE `' . $dbprefix . "pur_invoices`
  ADD COLUMN `currency` VARCHAR(20) NULL
  ;");
}

if (!$db->fieldExists('discount_total' ,$dbprefix . 'pur_invoices')) {
  $db->query('ALTER TABLE `' . $dbprefix . 'pur_invoices`
  ADD COLUMN `discount_total` DECIMAL(15,2) NULL
  ');
}

if (!$db->fieldExists('discount_percent' ,$dbprefix . 'pur_invoices')) {
  $db->query('ALTER TABLE `' . $dbprefix . 'pur_invoices`
  ADD COLUMN `discount_percent` DECIMAL(15,2) NULL
  ');
}

if (!$db->fieldExists('currency_rate' ,$dbprefix . 'pur_invoices')) {
  $db->query('ALTER TABLE `' . $dbprefix . 'pur_invoices`
  ADD COLUMN `currency_rate` DECIMAL(15,6) NULL
  ');
}

if (!$db->fieldExists('from_currency' ,$dbprefix . 'pur_invoices')) {
  $db->query('ALTER TABLE `' . $dbprefix . 'pur_invoices`
  ADD COLUMN `from_currency` VARCHAR(20) NULL
  ');
}

if (!$db->fieldExists('to_currency' ,$dbprefix . 'pur_invoices')) {
  $db->query('ALTER TABLE `' . $dbprefix . 'pur_invoices`
  ADD COLUMN `to_currency` VARCHAR(20) NULL
  ');
}

if (!$db->fieldExists('shipping_fee' ,$dbprefix . 'pur_invoices')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_invoices`
      ADD COLUMN `shipping_fee` decimal(15,2) NULL
  ;");
}

if (!$db->tableExists($dbprefix . 'pur_invoice_details')) {
    $db->query('CREATE TABLE `' . $dbprefix .'pur_invoice_details` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pur_invoice` INT(11) NOT NULL,
  `item_code` VARCHAR(100) NULL,
  `description` TEXT NULL,
  `unit_id` INT(11) NULL,
  `unit_price` DECIMAL(15,2) NULL,
  `quantity` DECIMAL(15,2) NULL,
  `into_money` DECIMAL(15,2) NULL,
  `tax` TEXT NULL,
  `total` DECIMAL(15,2) NULL,
  `discount_percent` DECIMAL(15,2) NULL,
  `discount_money` DECIMAL(15,2) NULL,
  `total_money` DECIMAL(15,2) NULL,
  `tax_value` DECIMAL(15,2) NULL,
  `tax_rate` TEXT NULL,
  `tax_name` TEXT NULL,
  `item_name` TEXT NULL,
  PRIMARY KEY (`id`));');
}

if (!$db->tableExists($dbprefix . 'pur_invoice_payment')) {
    $db->query('CREATE TABLE `' . $dbprefix . "pur_invoice_payment` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `pur_invoice` int(11) NOT NULL,
      `amount` DECIMAL(15,2) NOT NULL,
      `paymentmode` LONGTEXT NULL,
      `date` DATE NOT NULL,
      `daterecorded` DATETIME NOT NULL,
      `note` TEXT NOT NULL,
      `transactionid` MEDIUMTEXT NULL,
      `approval_status` INT(2) NULL,
      PRIMARY KEY (`id`));");
}

if (!$db->fieldExists('requester' ,$dbprefix . 'pur_invoice_payment')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "pur_invoice_payment`
    ADD COLUMN `requester` INT(11) NULL
  ;");
}

if ($db->fieldExists('user_type' ,$dbprefix . 'users')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "users`
    CHANGE COLUMN `user_type` `user_type` ENUM('staff', 'client', 'lead', 'vendor') NOT NULL DEFAULT 'client' ;");
}

if (!$db->fieldExists('vendor_id' ,$dbprefix . 'users')) { 
  $db->query('ALTER TABLE `' . $dbprefix . "users`
    ADD COLUMN `vendor_id` INT(11) NULL
  ;");
}

if (!$db->tableExists($dbprefix . 'items_of_vendor')) {
    $db->query('CREATE TABLE `' . $dbprefix . "items_of_vendor` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `vendor_id` INT(11) NOT NULL,
      `description` TEXT NOT NULL,
      `long_description` TEXT NULL,
      `rate` DECIMAL(15,2) NULL,
      `tax` int(11) NULL,
      `tax2` int(11) NULL,
      `unit` varchar(40) NULL,
      `group_id` int(11) NOT NULL,
      `commodity_code` varchar(100) NOT NULL,
      `commodity_barcode` TEXT NULL,
      `unit_id` int(11) NULL,
      `sku_code` VARCHAR(200) NULL,
      `sku_name` VARCHAR(200) NULL,
      `sub_group` VARCHAR(200) NULL,
      `active` INT(11) NULL,
      `parent` INT(11) NULL,
      `attributes` LONGTEXT NULL,
      `parent_attributes` LONGTEXT NULL,
      `commodity_type` INT(11) NULL,
      `origin` VARCHAR(100) NULL,
      `commodity_name` VARCHAR(200) NOT NULL,
      `series_id` TEXT NULL,
      `long_descriptions` LONGTEXT NULL,
      PRIMARY KEY (`id`));");
}

if (!$db->fieldExists('share_status' ,$dbprefix . 'items_of_vendor')){
    $db->query('ALTER TABLE `' . $dbprefix . "items_of_vendor`
  ADD COLUMN `share_status` int(1) NULL DEFAULT 0
  ;");
}

if (!$db->fieldExists('title' ,$dbprefix . 'items_of_vendor')){
    $db->query('ALTER TABLE `' . $dbprefix . "items_of_vendor`
  ADD COLUMN `title` TEXT NULL 
  ;");
}

if (!$db->fieldExists('from_vendor_item' ,$dbprefix . 'items')){
    $db->query('ALTER TABLE `' . $dbprefix . "items`
  ADD COLUMN `from_vendor_item` int(11) NULL
  ;");
}

purchase_create_email_template('purchase_order_to_contact', 'Purchase Order', '<span style=\"font-size: 12pt;\"> Hello !</span><br /><span style=\"font-size: 12pt;\"> We would like to share with you a link of Purchase Order information with the number {PO_NUMBER} </span><br /><span style=\"font-size: 12pt;\"><br />Please click on the link to view information: <a href="{PO_LINK}">PO link</a>
  </span><br /><br />');
purchase_create_email_template('purchase_quotation_to_contact', 'Purchase Quotation', '<span style=\"font-size: 12pt;\"> Hello  </span><br /><span style=\"font-size: 12pt;\"> We would like to share with you a link of Purchase Quotation information with the number {PQ_NUMBER} </span><br /><span style=\"font-size: 12pt;\"><br />Please click on the link to view information: <a href="{PQ_LINK}">Quotation</a> <br/ >
  </span><br /><br />');
purchase_create_email_template('purchase_request_to_contact', 'Purchase Request', '<span style=\"font-size: 12pt;\"> Hello !</span><br /><span style=\"font-size: 12pt;\"> We would like to share with you a link of Purchase Request information with the number {PR_NUMBER} </span><br /><span style=\"font-size: 12pt;\"><br />Please click on the link to view information: <a href="{PR_LINK}">Purchase Request</a><br/ >
  </span><br /><br />');