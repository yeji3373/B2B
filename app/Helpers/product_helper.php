<?php
use App\Models\ProductModel;
use App\Models\ProductPriceModel;
use App\Models\SupplyPriceModel;
use App\Models\CartModel;
use App\Models\CartStatusModel;
use App\Models\ProductSpqModel;
use App\Models\CurrencyRateModel;
use App\Models\MarginModel;


if ( !function_exists('productDefaultSelect') ) {
  function productDefaultSelect($sel = null) {
    $temp = "barcode, sample, img_url, name_en AS name, type_en AS type, edition_en AS edition, box,
            in_the_box, contents_of_box, contents_type_of_box, spec, spec2,container,spec_detail, spec_pcs, 
            package, package_detail, etc, shipping_weight, renewal, renewal_date";
    
    if ( !empty($sel) ) $temp = $sel;
    return $temp;
  }
}

if ( !function_exists('productPriceDefaultSelect') ) {
  function productPriceDefaultSelect($sel = null) {
    $temp = 'idx AS product_price_idx, product_idx, retail_price, supply_price';

    if ( !empty($sel) ) $temp = $sel;
    return $temp;
  }
}

if ( !function_exists('supplyPriceDefaultSelect') ) {
  function supplyPriceDefaultSelect($sel = null) {
    $temp = 'idx AS supply_price_idx, margin_level, price';

    if ( !empty($sel) ) $temp = $sel;
    return $temp;
  }
}

if ( !function_exists('get_product') ) {
  function get_product($sql = array()) {
    $callType = null;
    $limit = 0;
    $offset = 0;

    if ( array_key_exists('callType', $sql) ) {
      $callType = $sql['callType'];
      unset($sql['callType']);
    } 
    
    if ( array_key_exists('limit', $sql) ) {
      $limit = $sql['limit'];
      unset($sql['limit']);
    } 

    if ( array_key_exists('offset', $sql) ) {
      $offset = $sql['offset'];      
      unset($sql['offset']);
    }

    $productModel = new ProductModel();
    $productModel->products($sql);
    if ( is_null($callType) ) $product = $productModel->first();
    else {
      if ( !empty($limit) ) $product = $productModel->findAll($limit, $offset);
      else $product = $productModel->findAll();
    }

    return $product;
  }
}

if ( !function_exists('get_product_price') ) {
  function get_product_price($sql = array()) {
    $callType = null;
    if ( array_key_exists('callType', $sql) ) {
      $callType = $sql['callType'];
      unset($sql['callType']);
    }
    $productPriceModel = new ProductPriceModel();

    $productPrice = $productPriceModel->get_product_price($sql);
    if ( is_null($callType) ) $productPrice = $productPriceModel->first();
    else $productPrice = $productPriceModel->findAll();

    return $productPrice;
  }
}

if ( !function_exists('get_product_total') ) {
  function get_product_total($sql = array()){
    $productModel = new ProductModel();

    $Cnt = $productModel->productCnt();
    
    return $Cnt;
  }
}

if ( !function_exists('get_supply_price') ) {
  function get_supply_price($sql = array()) {
    $callType = null;
    
    if ( array_key_exists('callType', $sql) ) {
      $callType = $sql['callType'];
      unset($sql['callType']);
    }

    $supplyPriceModel = new SupplyPriceModel();    
    $supplyPrice = $supplyPriceModel->get_supply_price($sql);

    if ( is_null($callType) ) $supplyPrice = $supplyPriceModel->first();
    else $supplyPrice = $supplyPriceModel->findAll();

    return $supplyPrice;
  }
}

if ( !function_exists('combine_price_info') ) {
  function combine_price_info($product_price = array(), $supply_price = array()) {
    $productInfo = array_merge( get_product_price($product_price)
                              , get_supply_price($supply_price));

    return $productInfo;    
  }
}

if ( !function_exists('combine_product_price_info') ) {
  function combine_product_price_info(Array $product, Array $product_price, Array $supply_price) {
    $productInfo = array_merge( get_product($product)
                              , get_product_price($product_price)
                              , get_supply_price($supply_price));

    return $productInfo;    
  }
}

if ( !function_exists('combine_product_cart_info') ) {
  function combine_product_cart_info() {
      
  }
}

if ( !function_exists('get_cart')) {
  function get_cart($sql = array()) {
    $callType = null;
    $cartModel = new CartModel();

    if ( array_key_exists('callType', $sql) ) {
      $callType = $sql['callType'];
      unset($sql['callType']);
    }

    if ( is_null($callType) ) {
      $cartList = $cartModel->combine_cart_status($sql)->first();
      if ( !empty($cartList) ) $cartList['dataType'] = 'update';
      else {
        $cartList['dataType'] = 'insert';
        // $cartList = combine_product_price_info()
      }
    } else {
      $cartList = $cartModel->combine_cart_status($sql)->findAll();
    }
    return $cartList;
  }
}

if ( !function_exists('get_cart_total') ) {
  function get_cart_total($req = 0) {
    $cartModel = new CartModel();
    $cartRqTotalPrice = 0;
    $totalPrice = 0;

    if ( empty($req) ) $cartModel->where(['buyer_id' => session()->userData['buyerId']]);
    $cartRqTotalPrice = $cartModel->select('ROUND(SUM(req_price), 2) AS totalPrice')->first();
    
    if ( !empty($cartRqTotalPrice['totalPrice']) ) $totalPrice = $cartRqTotalPrice['totalPrice'];
    
    return $totalPrice;
  }
}

if ( !function_exists('get_cart_product_info') ) {
  function get_cart_product_info($sql = array()) {
    $carts = get_cart($sql);
    // var_dump($carts);
    if ( !empty($carts) ) {
      foreach($carts AS $i => $cart) {
        $carts[$i] = array_merge($cart
                                , get_product(['select' => 'id AS product_idx, brand_id, barcode, img_url, name_en AS name,
                                                           type_en AS type, spec, shipping_weight, container, spec_detail, spec_pcs'
                                              , 'where' => ['id' => $cart['prd_id']]]));
      }
    }

    return $carts;   
  }
}

if ( !function_exists('set_cart_status') ) { // 임시..... 나중에 쓰면 안됨
  function set_cart_status($sql = array()) {
    helper('brand');
    $currencyRateModel = new CurrencyRateModel();
    $cartModel = new CartModel();
    $cartStatusModel = new CartStatusModel();
    $carts = $cartModel->findAll();
    $exchange_rate = null;
    // d($carts);
    $currencyRate = $currencyRateModel->currency_rate()->select('exchange_rate')->where(['currency_idx' => 2, 'default_set' => 1])->first();
    if ( !empty($currencyRate) ) $exchange_rate = $currencyRate['exchange_rate'];
    else return 'exchange rate is null';

    if ( !empty($carts) ) {
      foreach($carts AS $cart) {
        // d($cart);
        $cartStatus = $cartStatusModel->where(['cart_idx' => $cart['idx']])->first();
        if ( empty($cartStatus) ) {          
          $cartStatusInsertData = [];
          $cartStatusInsertData['cart_idx'] = $cart['idx'];

          if ( !is_null($exchange_rate) ) $cartStatusInsertData['exchange_rate'] = $exchange_rate;
          $getPrd = get_product(['select' => 'id AS product_idx, brand_id', 'where' => ['id' => $cart['prd_id']]]);          
          if ( !empty($getPrd) ) {
            $cartStatusInsertData = array_merge($cartStatusInsertData, $getPrd);
          } else return;
          
          $getBrand = brand(['select' => 'brand_id, brand_name', 'where' => ['brand_id' => $getPrd['brand_id']]]);
          if ( !empty($getBrand) ) {
            $cartStatusInsertData = array_merge($cartStatusInsertData, $getBrand);
          } else return;

          $getProductPrice = get_product_price(['select'  => 'idx AS product_price_idx, retail_price, supply_price', 'where' => ['product_idx' => $cart['prd_id']]]);
          if ( !empty($getProductPrice) ) {
            $cartStatusInsertData = array_merge($cartStatusInsertData, $getProductPrice);
          } else return;

          $getSupplyPrice = get_supply_price(['select' => 'idx AS supply_price_idx, price', 'where' => ['product_idx' => $cart['prd_id']], 'orderby' => 'margin_level DESC']);
          if ( !empty($getSupplyPrice) ) {
            $cartStatusInsertData['applied_price'] = round(($getSupplyPrice['price'] / $currencyRate['exchange_rate']), 2);
            $cartStatusInsertData = array_merge($cartStatusInsertData, $getSupplyPrice);
          } else return;

          $getSpq = get_spq(['select' => 'id AS spq_idx, moq, spq, spq_criteria, spq_inBox, spq_outBox, calc_code, calc_unit', 'where' => ['product_idx' => $cart['prd_id']]]);
          if ( !empty($getSpq) ) {
            $cartStatusInsertData = array_merge($cartStatusInsertData, $getSpq);
          } else return;
          
          $cartStatusModel->transBegin();
          if ( !$cartStatusModel->save($cartStatusInsertData) ) {
            $cartStatusModel->transRollback();
            return;
          }
          $cartStatusModel->transCommit();
        // } else {
        //   // d($cartStatus);
        //   // $cartStatusInsertData['cart_status_idx'] = $cartStatus['cart_status_idx'];
        //   if ( $cartStatus['exchange_rate'] != $exchange_rate ) {
        //     $cartStatus['exchange_rate'] = $exchange_rate;
        //   }
          // d($cartStatus);
        }
      }
    } else {
      echo 'cart is empty';
      return;
    }
  }
}

if ( !function_exists('update_cart') ) {
  function update_cart() {
    // $spq = get_spq()
  }
}

if ( !function_exists('get_spq') ) {
  function get_spq($sql = array()) {
    $callType = null;
    $select = 'id AS spq_idx, moq, spq, spq_inBox, spq_outBox, calc_code, calc_unit';
    $where = null;

    if ( !empty($sql) ) {
      if ( !empty($sql['callType']) ) {
        $callType = $sql['callType'];
        unset($sql['callType']);
      }
      if ( empty($sql['select']) ) $sql['select'] = $select;
      // if ( !empty($sql['where']) ) $sql['where'];
    }

    $productSpqModel = new ProductSpqModel();
    $productSqp = $productSpqModel->get_spq($sql);
    if ( is_null($callType) ) $productSqp = $productSqp->first();
    else $productSqp = $productSpq->findAll();

    return $productSqp;
  }
}

if ( !function_exists('get_margin_rate') ) {
  function get_margin_rate($sql = array()) {
    $select = '*';
    $where = null;
    $orderby = '';

    $marginRateModel = new MarginModel();

    if ( !empty($sql) ) {
      if ( array_key_exists('where', $sql) ) {
        $where = $sql['where'];
      }
      if ( array_key_exists('orderby', $sql) ) {
        if ( !empty($sql['orderby']) ) $orderby = $sql['orderby'];
      }
    }
        
    if ( !empty($where) ) $marginRateModel->where($where);
    if ( !empty($orderby) ) $marginRateModel->orderby($orderby);

    $marginRate = $marginRateModel->select($select)->first();
    return $marginRate;
  }
}
// if ( !function_exists('view_product_item')) {
//   function view_product_item($data = array()) {
//     $msg = $data;
//     helper('product_item');
//     $a = json_encode(product_item($msg));

//     return $a;
//   }
// }

