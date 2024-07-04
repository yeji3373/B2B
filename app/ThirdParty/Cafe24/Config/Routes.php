<?php
$routes->group('cafe24', ['namespace' => 'Cafe24\Controllers', 'filter' => 'Cafe24\Filters\Cors'], function($routes) {
// $routes->group('cafe24', ['namespace' => 'Cafe24\Controllers'], function($routes) {
  // $routes->get('cafe', 'Authorization::index');
  $routes->get('authorization', 'Authorization::auth_request');
  $routes->get('accesscode', 'Authorization::access_code_request');
  $routes->get('carries', 'Shipping::getCarriers');

  $routes->get('carts', 'Personal::getCarts');
  $routes->post('carts', 'Personal::setCarts');
  
  $routes->get('products', 'Products::getProducts');
  
  $routes->get('scripts', 'ScriptTag::getScripts');

  $routes->get('category', 'Category::getCategories');
  // $routes->get('category', 'Category::getCategory');
  
  // $routes->get('variants', 'Products::getVariants');
  $routes->match(['get', 'post'], 'get_ip', 'Cafe24Api::getIp');

  $routes->group('bnk', function($routes) {
    // BNK _remap에 1번방식으로 함수 호출 시에는 두 방법다 1번 호출을 사용
    $routes->match(['get', 'post'], '/', 'BNK');
    // $routes->get('test', 'BNK::test'); // _remap을 통해 2번 호출일 경우에만 사용 가능
  });
});