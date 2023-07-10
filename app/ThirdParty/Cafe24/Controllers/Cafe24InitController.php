<?php
namespace Cafe24\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
// use CodeIgniter\HTTP\URI;

class Cafe24InitController extends Controller {  
  // // protected $request;
  // public $uri;
  // // protected $services = ['uri'];

  // public function __construct() {
  //   // $uri = service('uri');
  //   $uri = \CodeIgniter\HTTP\URI;
  // }

  public function reqHeaders() {
    $header = apache_request_headers();
    // print_r($header);
    // if ( !empty($header['Host']) ) {
    //   $config = new \Cafe24\Config\cafe24;
    //   if ( !in_array($header['Host'], $config->allow_origin) ) return;
    // // } else {
    // //   if ( $header['Host'] !== $_SERVER['HTTP_HOST'] ) return;
    // }
  
    header('Access-Control-Allow-Credentials: TRUE');
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Methods: GET, POST');
  }

  /* 
    $res : request result data
    $resType : response output data type NULL : return type is Array. default
  */
  public function responseConvert($res, $key = NULL, $resType = NULL) {
    $response;
    if ( $res->getStatusCode() === 200 ) {
      if ( strpos($res->header('content-type'), 'application/json') !== false ) {
        $response = json_decode($res->getBody(), true);
      } else $response = $res->getBody();

      if ( !empty($key) ) {
        if ( is_array($response) ) $response = $response[$key];
        else if ( is_object($response)) $response = $response->$key;
      }

      if ( empty($resType) ) {
        if ( is_object($response)) $response = (Array) $response;
      }
      // print_r( $this->request);
      if ( $this->request->isAJAX() ) $response = json_encode($response);
    }
    return $response;
  }
}