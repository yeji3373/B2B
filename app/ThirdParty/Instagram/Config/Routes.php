<?php
$routes->group('instagram', ['namespace' => 'Instagram\Controllers'], function($routes) {
  $routes->get('', 'InstagramController::index');
  $routes->post('', 'InstagramController::index');
});