<?php
namespace Cafe24\Controllers;

use Cafe24\Models\Cafe24Model;

class Personal extends Cafe24InitController {
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
                ]

      );      
      // return print_r(json_decode($res->getBody())->carts);

      if ( $res->getStatusCode() === 200 ) {
        if ( strpos($res->header('content-type'), 'application/json') !== false ) {
          $body = json_decode($res->getBody(), true);
        } else $body = $res->getBody();
        
        if ( is_array($body) ) $body = $body['carts'];
        else if ( is_object($body)) $body = (Array) $body->carts;

        if ( !empty($body) ) {
          foreach($body AS $i => $cart ) :
            $this->getProducts($cart['product_no']);
          endforeach;
        }

        // // $product_no = implode(",", array_column($body->carts, 'product_no'));
        // // return print_r(json_encode($this->getProducts($product_no)));

        // if ( !empty($body->carts) ) {
        //   foreach($body->carts AS $i => $cart) :
        //     echo gettype($body[$i])."/";
        //     // print_r($body[$i]);
        //     echo gettype($cart)."/";
        //     print_r($cart);
        //     // echo gettype($this->getProducts($cart['product_no']))."<br/><br/>";
        //     // array_merge($body->carts[$i], $this->getProducts($cart->product_no)[0]);            
        //   endforeach;
        // }

        // // return print_r(json_encode($body->carts));
      } else {
        return $res->getBody();
      }      
    } catch ( \Exeption $e ) {
      return json_encode($e);
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
                                  'product_no'    => $product_no]
                  ]);
      // return $res->getBody();
      if ( $res->getStatusCode() === 200 ) {
        if ( strpos($res->header('content-type'), 'application/json') !== false ) {
          $body = json_decode($res->getBody(), true);
        } else $body = $res->getBody();

        if ( is_array($body) ) $body = $body['products'];
        else if ( is_object($body)) $body = (Array) $body->products;

        if ( !empty($body) ) {
          foreach($body as $i => $product ) :
            print_r($product);
          endforeach;
        }

        // if ( !empty($body->products) ) {
        //   foreach($body->products AS $i => $prd) :
        //     $body->products[$i]->discountprice = $this->getDiscountPrices($prd->product_no)->discountprice;
        //   endforeach;
        // }
        return $body;
      }
      
      return $res->getBody();
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
                  
      if ( $res->getStatusCode() === 200 ) {
        if ( strpos($res->header('content-type'), 'application/json') !== false ) {
          $body = json_decode($res->getBody(), true);
        } else $body = $res->getBody();

        if ( is_array($body) ) $body = $body['discountprice'];
        else if ( is_object($body)) $body = $body->discountprice;

        return $body;
      } else return $res->getBody();
    } catch ( \Excepion $e ) {
      return json_encode($e);
    }
  }
}

