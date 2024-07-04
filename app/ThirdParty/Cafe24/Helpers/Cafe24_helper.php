<?php
function setHeaders() {
  /**
   * Cors 참고
   * URL : https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
   * https://gist.github.com/kenjis/e757d2b4193b6843724e447e6eaa1254 
   */
  $response = service('response');
  $request_headers = apache_request_headers();
  $origin = NULL;
  
  $response->setHeaders('Auth', 'key=?');
  if ( isset($request_headers['origin']) && !empty($request_headers['origin']) ) {  
    if ( !in_array($request_headers['origin'], config('Cafe24')->allow_origin)) return;
    
    $origin = $request_headers['origin'];    
    $response->setHeader('Access-Control-Allow-Credentials', 'TRUE');
    $response->setHeader("Access-Control-Allow-Origin", $origin);
    $response->setHeader('Content-Type', 'application/json; charset=utf-8');
    $response->setHeader('Access-Control-Allow-Methods','GET, POST');
    // header('Access-Control-Allow-Credentials: TRUE');
    // header("Access-Control-Allow-Origin: {$origin}");
    // header('Content-Type: application/json; charset=utf-8');
    // header('Access-Control-Allow-Methods: GET, POST');
  }
  return $response;
}