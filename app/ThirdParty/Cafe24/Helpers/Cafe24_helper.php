<?php

function setHeaders() {
  $header = apache_request_headers();
 
  if ( !empty($header['origin']) ) {
    $config = new Cafe24\Config\Cafe24;
    if ( !in_array($header['origin'], $config->allow_origin) ) return;
  } else {
    if ( $header['Host'] !== $_SERVER['HTTP_HOST'] ) return;
  }

  header('Access-Control-Allow-Credentials: TRUE');
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json; charset=utf-8');
  header('Access-Control-Allow-Methods: GET, POST');

  echo "???<br/>";   
}