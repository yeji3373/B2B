<?php
namespace Cafe24\Controllers;

use CodeIgniter\Controller;
use Cafe24\Models\Cafe24Model;

class Category extends Cafe24InitController {
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

  public function getCountCategory() {
    try {
      $res = $this->curl->get(
                  $this->config->base_url."/api/v2/categories/count",
                  [
                    'headers' => ['Content-Type'  => 'application/json',
                                  'X-Cafe24-Client-Id' => $this->config->client_id],
                    'query'   => ['shop_no' => $this->config->shop_no]
                  ]);
      return $this->responseConvert($res, 'count');
    } catch ( \Exeption $e) {
      if ( $this->request->isAJAX() ) { return 'error'; }
      return print_r($e);
    }
  }
  
  public function getCategory($limit = 10, $offset = 0) {
    try {
      $res = $this->curl->get(
                  $this->config->base_url."/api/v2/categories",
                  [
                    'headers' => ['Content-Type'  => 'application/json',
                                  'X-Cafe24-Client-Id' => $this->config->client_id],
                    'query'   => ['shop_no' => $this->config->shop_no,
                                  // 'category_no' => 1234,
                                  'limit' => $limit,
                                  'offset' => $offset]
                    // 'form_params' => [ 'shop_no'  => $this->config->shop_no,
                    //                     'display_location' => 'all'] 
                  ]);
      return $this->responseConvert($res, 'categories');
    } catch ( \Exeption $e) {
      if ( $this->request->isAJAX() ) { return 'error'; }
      return print_r($e);
    }
  }

  public function getCategories() {
    $this->reqHeaders();
    $cnt = 100;
    $offset = 0;
    $categories = [];
    $cate_no = [];
    $count = $this->getCountCategory();

    if ( $this->request->getVar('cate_no') ) {
      $cate_no = explode(',', $this->request->getVar('cate_no'));
    }
      
    for ( $i = 0; $i < ($count / $cnt); $i++ ) {
      $offset = ($cnt * $i) + $i;
      $temp = $this->getCategory($cnt, $offset);
      // print_r(json_encode($temp));
      // echo "<br/><br/><Br/>";
      
      foreach ($temp as $key => $value) {
        // if ($value['use_display'] == 'F') unset($temp[$key]);
        // if ( $value['use_display'] == 'T') {
          if ( !empty($cate_no) ) {
            foreach ( $cate_no AS $no ) {
              if ( $value['root_category_no'] == $no ) {
                array_push($categories, $value);
              }
            }
          } else array_push($categories, $value);

          if ( $this->request->getVar('depth') ) {
            if ( $value['category_depth'] > $this->request->getVar('depth') ) {
              if ( in_array($value, $categories) ) {
                array_splice($categories, array_search($value, $categories), 1);
              }              
            }
          }
        // }
      }
    }
    return print_r(json_encode($categories));
  }
}