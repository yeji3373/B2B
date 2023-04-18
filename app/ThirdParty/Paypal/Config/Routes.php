<?php

$routes->group('paypal', ['namespace' => 'Paypal\Controllers'], function($routes) {
  $routes->get('', 'PaypalController::index');
  $routes->post('', 'PaypalController::index');

  $routes->get('index', 'PaypalController::index');
});