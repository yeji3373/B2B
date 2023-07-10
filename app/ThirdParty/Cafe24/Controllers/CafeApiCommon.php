<?php
namespace Cafe24\Controllers;

use CodeIgniter\Controller;

class CafeApiCommon extends Controller{
  function setHeaders() {
    $header = apache_request_headers();
        
    if ( !empty($header['origin']) ) {
      $config = config('cafe24');
      if ( !in_array($header['origin'], $config->allow_origin) ) return;
    } else {
      if ( $header['Host'] !== $_SERVER['HTTP_HOST'] ) return;
    }
  
    header('Access-Control-Allow-Credentials: TRUE');
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Methods: GET, POST');
  }
}