<?php
namespace Cafe24\Controllers;

use Cafe24\Models\Cafe24Model;

class Products extends Cafe24InitController {
  public function __construct() {
    helper('cafe24');
    $this->curl = \Config\Services::curlrequest();
    $this->config = config('Cafe24');
    $this->cafe24Model = new Cafe24Model;
    
    $this->cafe24 = $this->cafe24Model->first();
    if ( !empty($this->cafe24) ) {
      $this->config->access_token = $this->cafe24['access_token'];
      $this->config->access_token_expires_at = $this->cafe24['access_token_expires_at'];
      $this->config->refresh_token = $this->cafe24['refresh_token'];
      $this->config->refresh_token_expires_at = $this->cafe24['refresh_token_expires_at'];
    }
  }

  public function getProducts($product_no = NULL) {
    try {
      $res = $this->curl->get(
                  $this->config->base_url.'/api/v2/admin/products',
                  [
                    'headers' => ['Content-Type'  => 'application/json',
                                  'Authorization' => 'Bearer '.$this->config->access_token],
                    'query'   => ['shop_no'       => $this->config->shop_no,
                                  'product_no'    => $product_no,
                                  'embed'         => 'options,discountprice,variants']
                  ]);
      
      // if ( !empty($body) ) {
      //   if ( count($body) > 1 ) {
      //     foreach($body as $i => $product ) :
      //       // print_r(array_merge($product, $this->getDiscountPrices($product['product_no'])));
      //       $body[$i] = array_merge($product, $this->getDiscountPrices($product['product_no']));
      //       // print_r($result);
      //     endforeach;
      //   } else {
      //     $body = array_merge($body[0], $this->getDiscountPrices($body[0]['product_no']));
      //   }
      // }
      $body = $this->responseConvert($res, 'products');
      if ( gettype($product_no) != 'array' ) {
        $body = $body[0];
      }
      return $body;
      // // if ( !empty($body) ) { // embed에 discountprice 포함했을 때.
      // //   if ( count($body) == 1 ) $body = $body[0];
      // // }
    } catch ( \Execption $e ) {
      return json_encode($e);
    }
  }

  public function getDiscountPrices($product_no) {
    try {
      $res = $this->curl->get(
                  $this->config->base_url."/api/v2/admin/products/{$product_no}/discountprice",
                  [
                    'headers' => ['Content-Type'  => 'application/json',
                                  'Authorization' => 'Bearer '.$this->config->access_token],
                    'query'   => ['shop_no' => $this->config->shop_no]
                  ]);
                  
    return $this->responseConvert($res);
    } catch ( \Excepion $e ) {
      return json_encode($e);
    }
  }

  public function getVariants() {
    try {
      $res = $this->curl->get(
        $this->config->base_url.`/api/v2/admin/products/{$product_no}/variants`,
        [
          'headers' => ['Content-Type'  => 'application/json',
                        'Authorization' => 'Bearer '.$this->config->access_token],
          'query'   => ['shop_no' => $this->config->shop_no]
        ]);
      return $this->resonseConvert($res);
    } catch ( \Exeption $e ) {
      return json_encode($e);
    }
  }
}

