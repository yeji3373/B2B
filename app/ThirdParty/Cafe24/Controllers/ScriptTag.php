<?php
namespace Cafe24\Controllers;

use Cafe24\Models\Cafe24Model;

class ScriptTag extends Cafe24InitController {
  public function __construct() {
    helper('cafe24');
    $this->curl = \Config\Services::curlrequest();
    $this->config = config('Cafe24');
    $this->cafe24Model = new Cafe24Model;
    $this->productsController = new Products;
    
    $this->cafe24 = $this->cafe24Model->first();
    if ( !empty($this->cafe24) ) {
      $this->config->access_token = $this->cafe24['access_token'];
      $this->config->access_token_expires_at = $this->cafe24['access_token_expires_at'];
      $this->config->refresh_token = $this->cafe24['refresh_token'];
      $this->config->refresh_token_expires_at = $this->cafe24['refresh_token_expires_at'];
    }
  }
  
  public function getScripts() {
    try {
      $res = $this->curl->get(
                  $this->config->base_url."/api/v2/admin/scripttags",
                  [
                    'headers' => ['Content-Type'  => 'application/json',
                                  'Authorization' => 'Bearer '.$this->config->access_token],
                    // 'query'   => ['shop_no' => $this->config->shop_no]
                    // 'form_params' => [ 'shop_no'  => $this->config->shop_no,
                    //                     'display_location' => 'all'] 
                  ]);

      return print_r($res);
      // return print_r($this->responseConvert($res));
    } catch ( \Exeption $e) {
      if ( $this->request->isAJAX() ) { return 'error'; }
      return print_r($e);
    }
  }
}