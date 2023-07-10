<?php
$routes->group('cafe24', ['namespace' => 'Cafe24\Controllers'], function($routes) {
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
});

// http://127.0.0.8/cafe24/authorization?code=dDRq43gYCRGkpifGETEyeD&state=MTIzNDU2Nzg=
// https://koreacosmeticmall.com/cafe24/authentication?code=FYsif6yyHaADV8rLLfYD8P&state=MTIzNDU2Nzg=