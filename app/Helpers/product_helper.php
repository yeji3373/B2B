<?php
use App\Models\ProductModel;
use App\Models\ProductPriceModel;
use App\Models\SupplyPriceModel;
use App\Models\CartModel;
use App\Models\CartStatusModel;
// use App\Views\layout\includes\productItem;

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
      $limit = $sql['limit'];
      $offset = $sql['offset'];
      unset($sql['callType']);
      unset($sql['limit']);
      unset($sql['offset']);
    }
    $productModel = new ProductModel();
    // var_dump($sql);
    $productModel->products($sql);
    if ( is_null($callType) ) $product = $productModel->first();
    else $product = $productModel->findAll($limit, $offset);

    return $product;
  }
}

if ( !function_exists('get_product_price') ) {
  function get_product_price($sql = array()) {
    $callType = 0;
    if ( array_key_exists('callType', $sql) ) {
      $callType = $sql['callType'];
      unset($sql['callType']);
    }
    $productPriceModel = new ProductPriceModel();

    $productPrice = $productPriceModel->get_product_price($sql);
    if ( $callType ) $productPrice = $productPriceModel->findAll();
    else $productPrice = $productPriceModel->first();

    return $productPrice;
  }
}

if ( !function_exists('get_supply_price') ) {
  function get_supply_price($sql = array()) {
    $callType = 0;
    if ( array_key_exists('callType', $sql) ) {
      $callType = $sql['callType'];
      unset($sql['callType']);
    }
    $supplyPriceModel = new SupplyPriceModel();
    
    $supplyPrice = $supplyPriceModel->get_supply_price($sql);

    if ( $callType ) $supplyPrice = $supplyPriceModel->findAll();
    else $supplyPrice = $supplyPriceModel->first();

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

if ( !function_exists('view_product_item')) {
  function view_product_item($data = array()) {    
    // if ( empty($data) ) return;
    return view('layout\includes\test');
  }
}

