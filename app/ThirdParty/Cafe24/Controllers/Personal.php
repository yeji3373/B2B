<?php
namespace Cafe24\Controllers;

use Cafe24\Models\Cafe24Model;
use Cafe24\Controllers\Products;

class Personal extends Cafe24InitController {
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

  public function getCarts() {
    $this->reqHeaders();
    
    if ( empty($this->request->getVar('id')) ) {
      // return json_encode(['test' => ['type' => 'TEST', 'success' => 'fail', 'id' => $this->request->getVar()]]);
      http_response_code(400);
      return null;
    }

    try {
      $res = $this->curl->get(
                $this->config->base_url.'/api/v2/admin/carts',
                [
                  'headers' => ['Content-Type'  => 'application/json',
                                'Authorization' => 'Bearer '.$this->config->access_token],
                  'query'   => ['shop_no' => $this->config->shop_no,
                                'member_id' => $this->request->getVar('id'),
                                'limit' => 40 ]
                ]);

      $body = $this->responseConvert($res, 'carts');
      
      if ( !empty($body) ) {
        foreach($body AS $i => $cart ) :
          $body[$i] = array_merge($cart, $this->productsController->getProducts($cart['product_no']));
        endforeach;
      }

      return json_encode($body);
    } catch ( \Exeption $e ) {
      return json_encode($e);
    }
  }

  // public function setCarts() {
  //   try {
  //     $res = $this->curl->post(
  //       $this->config->base_url.'/api/v2/carts',
  //           [
  //             'headers' => ['Content-Type'  => 'application/json',
  //                           'Authorization' => 'Bearer '.$this->config->access_token],
  //             'form_params' => ['shop_no' => $this->config->shop_no,
  //                               'duplicated_item_check' => 'T', 
  //                               'product_no'  => 12790,
  //                               'basket_type' => "A0000",
  //                               'prepaid_shipping_fee' => 'P',
  //                               'variants' => [
  //                                 [ 'quantity' => 4,
  //                                   'variants_code' => 'P0000SXY000A',
  //                                   'options' => []]
  //                               ]]
  //           ]
  //     );

  //     // return json_encode($this->responseConvert($res));
  //     return json_encode(['shop_no' => $this->config->shop_no,
  //     'duplicated_item_check' => 'T', 
  //     'product_no'  => 12790,
  //     'basket_type' => "A0000",
  //     'prepaid_shipping_fee' => 'P',
  //     'variants' => [
  //       [ 'quantity' => 4,
  //         'variants_code' => 'P0000SXY000A',
  //         'options' => []]
  //     ]]);
  //   } catch ( \Exception $e ) {
  //     return json_encode($e);
  //   }
  // }
}