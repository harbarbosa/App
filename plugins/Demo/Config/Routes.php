<?php

namespace Config;

$routes = Services::routes();

//$routes->get('demo', 'Demo::index', ['namespace' => 'Demo\Controllers']);
//$routes->get('composicao', 'Demo::index', ['namespace' => 'Demo\Controllers']);
//$routes->get('demo/(:any)', 'Demo::$1', ['namespace' => 'Demo\Controllers']);

$routes->get('demo_settings', 'Demo_settings::index', ['namespace' => 'Demo\Controllers']);
$routes->get('demo_settings/(:any)', 'Demo_settings::$1', ['namespace' => 'Demo\Controllers']);
$routes->post('demo_settings/(:any)', 'Demo_settings::$1', ['namespace' => 'Demo\Controllers']);


//ROTAS PLUGIN COMPOSIÇÃO
$routes->get('composicao', 'Composicao::index', ['namespace' => 'Demo\Controllers']);
$routes->get('composicao/(:any)', 'Composicao::$1', ['namespace' => 'Demo\Controllers']);

//$routes->get('insumos', 'Insumos::index', ['namespace' => 'Demo\Controllers']);

