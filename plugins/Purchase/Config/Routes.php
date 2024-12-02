<?php

namespace Config;

$routes = Services::routes();

$routes->get('purchase', 'Purchase::index', ['namespace' => 'Purchase\Controllers']);
$routes->get('purchase/(:any)', 'Purchase::$1', ['namespace' => 'Purchase\Controllers']);

$routes->get('purchase/(:any)', 'Purchase::$1', ['namespace' => 'Purchase\Controllers']);
$routes->post('purchase/(:any)', 'Purchase::$1', ['namespace' => 'Purchase\Controllers']);