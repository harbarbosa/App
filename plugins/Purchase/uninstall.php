<?php
$db = db_connect('default');
$dbprefix = get_db_prefix();

if ($db->tableExists($dbprefix . 'pur_estimates')) {
    $db->query('DROP TABLE `'.$dbprefix .'pur_estimates`;');
}

if ($db->tableExists($dbprefix . 'pur_approval_details')) {
    $db->query('DROP TABLE `'.$dbprefix .'pur_approval_details`;');
}

if ($db->tableExists($dbprefix . 'pur_approval_setting')) {
    $db->query('DROP TABLE `'.$dbprefix .'pur_approval_setting`;');
}

if ($db->tableExists($dbprefix . 'pur_estimate_detail')) {
    $db->query('DROP TABLE `'.$dbprefix .'pur_estimate_detail`;');
}

if ($db->tableExists($dbprefix . 'pur_invoice_details')) {
    $db->query('DROP TABLE `'.$dbprefix .'pur_invoice_details`;');
}

if ($db->tableExists($dbprefix . 'pur_invoice_payment')) {
    $db->query('DROP TABLE `'.$dbprefix .'pur_invoice_payment`;');
}

if ($db->tableExists($dbprefix . 'pur_invoices')) {
    $db->query('DROP TABLE `'.$dbprefix .'pur_invoices`;');
}

if ($db->tableExists($dbprefix . 'pur_order_detail')) {
    $db->query('DROP TABLE `'.$dbprefix .'pur_order_detail`;');
}

if ($db->tableExists($dbprefix . 'pur_orders')) {
    $db->query('DROP TABLE `'.$dbprefix .'pur_orders`;');
}

if ($db->tableExists($dbprefix . 'pur_request')) {
    $db->query('DROP TABLE `'.$dbprefix .'pur_request`;');
}

if ($db->tableExists($dbprefix . 'pur_request_detail')) {
    $db->query('DROP TABLE `'.$dbprefix .'pur_request_detail`;');
}

if ($db->tableExists($dbprefix . 'pur_vendor')) {
    $db->query('DROP TABLE `'.$dbprefix .'pur_vendor`;');
}

if ($db->tableExists($dbprefix . 'pur_vendor_admin')) {
    $db->query('DROP TABLE `'.$dbprefix .'pur_vendor_admin`;');
}

if ($db->tableExists($dbprefix . 'pur_vendor_cate')) {
    $db->query('DROP TABLE `'.$dbprefix .'pur_vendor_cate`;');
}

if ($db->tableExists($dbprefix . 'pur_vendor_items')) {
    $db->query('DROP TABLE `'.$dbprefix .'pur_vendor_items`;');
}

if ($db->tableExists($dbprefix . 'items_of_vendor')) {
    $db->query('DROP TABLE `'.$dbprefix .'items_of_vendor`;');
}

$builder = $db->table($dbprefix.'users');
$builder->where('user_type', 'vendor');
$builder->delete();