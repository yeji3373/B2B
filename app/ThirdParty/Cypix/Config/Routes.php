<?php
$routes->group('', ['namespace' => 'Cypix\Controllers'], function($routes) {
  $routes->get('signature', 'Signature::getSignature');
  $routes->get('transaction', 'Signature::transaction');
});