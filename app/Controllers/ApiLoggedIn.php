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

use App\Controllers\Order;

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
    $totalpage = 0;
    $where = null;
    $empty = "is empty";

    $this->data['param'] = $params;

    $returnType = empty($params['returnType']) ? 'json' : $params['returnType'];

    $limit = empty($params['limit']) ? 15 : $params['limit'];
    $offset = empty($params['offset']) ? 0 : ((($params['offset'] - 1) * 1) * $limit);
    
    $orderBy = empty($params['orderby']) ? 'brand_id ASC, product.id ASC' : $params['orderby'];

    if ( !empty($params['totalpage']) ) {
      $totalpage = $params['totalpage'];
      unset($params['totalpage']);
    }

    if ( !empty($params['null_msg']) ) {
      $empty = $params['null_msg'];
      unset($params['null_msg']);
    }

    unset($params['offset'], $params['limit'], $params['orderby']);
    unset($params['request_unit']);
    
    if ( !empty($params) )  $where = product_query_string_return($params);
    
    if ( empty($totalpage) ) {
      $getTotal = get_product(['select' => 'COUNT(*) AS totalpage', 'where' => $where]);
      $totalpage = ceil($getTotal['totalpage'] / $limit);    
    }
    
    $products = get_product([ 'select' => '*', 
                              'where' => $where,
                              'orderby' => $orderBy, 
                              'callType' => 1,
                              'limit' => $limit, 
                              'offset' => $offset]);

    if ( !empty($products) ) {
      foreach($products AS $i => $product) {
        $brand = brand(['select' => 'brand_id, brand_name', 'where' => ['brand_id' => $product['brand_id']]]);
        if ( !empty($brand) ) {
          $products[$i] = array_merge($products[$i], $brand);
          $productPrice = get_product_price([ 'select'  => productPriceDefaultSelect()
                                            , 'where' => ['product_idx' => $product['id']]]);
          if ( !empty($productPrice) ) {
            $products[$i] = array_merge($products[$i], $productPrice);
            $productSupplyPrice = get_supply_price([ 'select' => supplyPriceDefaultSelect()
                                                    , 'where' => ['product_idx' => $product['id']]
                                                    , 'orderby' => 'margin_level DESC']);
            if ( !empty($productSupplyPrice) ) {
              $products[$i] = array_merge($products[$i], $productSupplyPrice);
              $products[$i]['product_price'] = round(($productSupplyPrice['price'] / session()->currency['exchangeRate']), 2);
            } else {
              unset($product[$i]);
              continue;
            }
          } else {
            unset($products[$i]);
            continue;
          }
        } else {
          unset($products[$i]);
          continue;
        }
      }
    }
    $products = array_values($products);

    $this->data['totalpage'] = $totalpage;
    $this->data['products'] = $products;
    if ( $returnType == 'html' ) {
      helper(['product_item', 'html']);
      if ( !empty($products) ) {
        $this->data['html'] = product_item($products);
      } else $this->data['html'] = is_empty($empty);
    }
    
    return $this->respond($this->data);
  }

  // // 안씀
  // function carts() {
  //   $data = $this->request->getPost();
  //   $cartSet = ['where'     => ['cart.buyer_id' => session()->userData['buyerId']]
  //             , 'callType'  => 'finaAll' ];

  //   $cartList = get_cart($cartSet);

  //   foreach($cartList AS $i => $cartItem) {
  //     $cartItem['retail_price'] = round(($cartItem['retail_price'] / $cartItem['exchange_rate']), 2);
  //     $cartList[$i] = array_merge($cartItem, get_product(['select' => productDefaultSelect(), 'where' => ['id' => $cartItem['prd_id']]]));
  //   }

  //   $this->data = $cartList;
  //   return $this->respond($this->data);
  // }

  // // 안씀...
  // function cartItem() {
  //   $data = $this->request->getPost();
  //   $cartSet = ['where' => ['cart.buyer_id' => session()->userData['buyerId']]];

  //   if ( !empty($data['where']) ) $cartSet['where'] = array_merge($cartSet['where'], $data['where']);
  //   if ( !empty($data['prd_id']) ) $cartSet['where'] = array_merge($cartSet['where'], ['cart.prd_id' => $data['prd_id']]);

  //   $cartList = get_cart($cartSet);
    
  //   $this->data['cartList'] = $cartList;
  //   $this->data['cartSet'] = $cartSet;
  //   $this->data['data'] = $data;
  //   return $this->respond($this->data);
  // }

  public function cartList() {
    $data = $this->request->getPost();
    
    $returnType = empty($data['returnType']) ? 'json' : $data['returnType'];
    $cartSet = ['where'     => ['cart.buyer_id' => session()->userData['buyerId']]
              , 'callType'  => 1 ];

    $cartList = get_cart_product_info($cartSet);

    $this->data['carts'] = $cartList;
    if ($returnType == 'html') {
      helper(['cart_item', 'html']);
      $this->data['html'] = cart_list($cartList);
    }
    return $this->respond($this->data);
  }

  function getSpq() {
    $data = $this->request->getPost();
    
    $getSpq = get_spq($data);
    $this->data['spqList'] = $getSpq;
    return $this->respond($this->data);
  }

  // function productSelect() {
  //   $products = new ProductModel();
  //   $this->data = $products->selects()->select(['name_en', 'name'])->findAll();
  //   // echo $products->getLastQuery(); 
  //   // echo "<br/><br/>";
  //   return $this->respond($this->data);
  // }
}
