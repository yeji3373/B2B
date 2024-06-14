<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Config\Services;
use App\Models\CountryModel;
use App\Models\BrandModel;
use App\Models\ProductModel;
use App\Models\BuyerModel;
// use CodeIgniter\Database\RawSql;

class ApiLoggedIn extends ResourceController {
  use ResponseTrait;
  protected $data;
  protected $format = 'json';

  public function __construct() {
    helper('merge');
    helper('auth');
    helper('brand');
    helper('product');

    current_user();
  }

  public function __remap(...$params) {
    $method = $this->request->getMethod();
    $params = [($params[0] !== 'index' ? $params[0] : false)];
    $this->data = $this->request->getJSON();

    if (method_exists($this, $method)) {
      return call_user_func_array([$this, $method], $params);
    } else {
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
  }

  function products() {
    helper('querystring');
    $params = $this->request->getVar();
    $total = 0;
    $where = null;

    $limit = empty($params['limit']) ? 15 : $params['limit'];
    $offset = empty($params['offset']) ? 0 : ((($params['offset'] - 1) * 1) * $limit);

    if ( !empty($offset) ) {
      if ( !empty($params['brand_id']) ) {
        $totSelect = 'brand_id';
        $totWhere = ['where' => 'brand_id = '.$params['brand_id']];
      } else {
        $totSelect = '*';
        $totWhere = [];
      }
      $total = get_product_total(['select' => $totSelect, $totWhere]);

      if ( $total < $offset ) {
        $this->data = null;
        return $this->respond($this->data);
      }
    }


    $orderBy = empty($params['orderby']) ? 'brand_id ASC, product.id ASC' : $params['orderby'];
    unset($params['offset'], $params['limit'], $params['orderby']);
    unset($params['request_unit']);
    
    
    if ( !empty($params) )  $where = product_query_string_return($params);;
    
    $products = get_product([ 'select' => '*', 
                              'where' => $where,
                              'orderby' => $orderBy, 
                              'callType' => 1, 
                              'limit' => $limit, 
                              'offset' => $offset]);

    foreach($products AS $i => $product) {
      $price = combine_price_info([ 'select'  => productPriceDefaultSelect()
                                    , 'where' => ['product_idx' => $product['id']]]
                                , [ 'select' => supplyPriceDefaultSelect()
                                    , 'where' => ['product_idx' => $product['id']]
                                    , 'orderby' => 'margin_level DESC']);
      $price['product_price'] = round(($price['price'] / session()->currency['exchangeRate']), 2);
      $products[$i] = array_merge($product 
                                  , brand(['select' => 'brand_name', 'where' => ['brand_id' => $product['brand_id']]])
                                  , $price);
    }

    // $this->data['products'] = $products;
    // $this->data['params'] = $params;
    $this->data = $products;
    return $this->respond($this->data);
  }

  function carts() {
    $data = $this->request->getPost();
    $cartSet = ['where' => ['cart.buyer_id' => session()->userData['buyerId']]];

    $cartList = get_cart($cartSet);

    foreach($cartList AS $i => $cartItem) {
      $cartItem['retail_price'] = round(($cartItem['retail_price'] / $cartItem['exchange_rate']), 2);
      $cartList[$i] = array_merge($cartItem, get_product(['select' => productDefaultSelect(), 'where' => ['id' => $cartItem['prd_id']]]));
    }

    $this->data = $cartList;
    return $this->respond($this->data);
  }

  // function add_cart() {
    

  //   return "a";

  // }

  // function productSelect() {
  //   $products = new ProductModel();
  //   $this->data = $products->selects()->select(['name_en', 'name'])->findAll();
  //   // echo $products->getLastQuery(); 
  //   // echo "<br/><br/>";
  //   return $this->respond($this->data);
  // }
}
