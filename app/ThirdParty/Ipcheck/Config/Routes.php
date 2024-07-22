<?php
$routes->group('ipcheck', ['namespace' => 'Ipcheck\Controllers'], function($routes) {
	$routes->get('ip_lookup', 'IpcheckController::ipLookup');
	// $routes->get('ip_lookup/(:any)', 'IpcheckController::ipLookup/$1');
});