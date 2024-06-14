<?php
namespace App\Controllers;

use App\Models\CartModel;
use App\Models\ProductModel;
use App\Models\StockModel;

use CodeIgniter\I18n\Time;

class CartController extends BaseController {
  protected $tax = 1.1;
  protected $products;
  protected $data;
  
  public $basedDiscountVal = 7000; // B구간일 때 A구간 변경되는 금액
  public $basedMinimumOrderVal = 1000;
  public $checkDate;

  public function __construct() {
    helper('product');
    $this->cart = new CartModel();
    $this->product = new ProductModel();
    $this->stocks = new StockModel();
    $this->checkDate = date_format(new Time('-7 days'), 'Y-m-d 23:59:59');
  }

  public function simpleCartList($params = []) {
    $sql['where'] = ['buyer_id' => session()->userData['buyerId']];
    
    return $this->cart->carts($sql)->findAll();
  }

  public function getCartList($params = []) {
    $query['select'] = ", product_spq.moq, product_spq.spq_criteria, product_spq.spq_inBox
                        , product_spq.spq_outBox, product_spq.calc_code, product_spq.calc_unit
                        , cart.idx AS cart_idx, cart.chkd, cart.order_qty";
    $query['join']  = " LEFT OUTER JOIN product_spq ON product_spq.product_idx = product.id
                        LEFT OUTER JOIN ( SELECT * FROM cart WHERE buyer_id = ".session()->userData['buyerId']." ) AS cart ON cart.prd_id = product.id";
    // $query['from'] = ", ( SELECT * FROM product_spq ) AS product_spq
    //                   , ( SELECT * FROM cart WHERE buyer_id = ".session()->userData['buyerId']." ) AS cart";
    $query['where'] = " AND product_spq.product_idx = product.id 
                        AND cart.prd_id = product.id";
    $query['orderby'] = ", cart.idx ASC";
    $query['limit'] = NULL;

    if ( !empty($params) ) {
      if ( !empty($params['select']) ) $query['select'] .= $params['select'];
      if ( !empty($params['from']) ) $query['from'] .= $params['from'];
      if ( !empty($params['where']) ) $query['where'] .= $params['where'];
      if ( !empty($params['orderby']) ) $query['orderby'] .= $params['orderby'];
      if ( !empty($params['limit']) ) $query['limit'] .= $params['limit'];
    } else array_merge($query, $params);

    $query['select'] .= ", ".$this->calcRetailPrice().' AS retail_price, '
                      .$this->calcSupplyPrice().' AS product_price, '
                      .' ('.$this->calcSupplyPrice().' * order_qty) AS order_price';

    $cartList = $this->product->getProductQuery($query);
    return $cartList;
  }

  public function calcRetailPrice() {
    $select = null;
    $exchangeRate = session()->currency['exchangeRate'];
    $basedExchangeRate = session()->currency['basedExchangeRate'];

    if ( !empty($exchangeRate) && ($exchangeRate != $basedExchangeRate) ) {
      $basedExchangeRate = $exchangeRate;
    }
    
    $select = "ROUND((product_price.retail_price / {$basedExchangeRate}), ".session()->currency['currencyFloat'].")";
   
    return $select;
  }

  public function calcSupplyPrice() {
    $select = null;
    $exchangeRate = session()->currency['exchangeRate'];
    $basedExchangeRate = session()->currency['basedExchangeRate'];

    if ( !empty($exchangeRate) && ($exchangeRate != $basedExchangeRate) ) { // 환율 우대 받은 값이 있을 때 값이 다르면 환율 적용된 값을 최우선으로 처리
      $basedExchangeRate = $exchangeRate;
    }
    $select = "ROUND((supply_price.price / {$basedExchangeRate}), ".session()->currency['currencyFloat'].")";
    return $select;
  }

  public function calcSupplyPriceCompare($condition = NULL) {
    $select = null;
    $exchangeRate = session()->currency['exchangeRate'];
    $basedExchangeRate = session()->currency['basedExchangeRate'];

    if ( !empty($exchangeRate) && ($exchangeRate != $basedExchangeRate) ) { // 환율 우대 받은 값이 있을 때 값이 다르면 환율 적용된 값을 최우선으로 처리
      $basedExchangeRate = $exchangeRate;
    }
    $select = "ROUND((supply_price_compare.price / {$basedExchangeRate}), ".session()->currency['currencyFloat'].")";
    return $select;
  }

  public function getCartTotalPrice( $where = array(), $exchangeRate = 1 ) {
    $cartSubTotal = NULL;
    $whereCondition = count($where) > 0 ? $where : array();    

    if ( $exchangeRate > 1 ) : // 환율 혹은 달러에서 한화로 변경할 경우, 한화의 환율을 적용
      $this->cart
          ->select("(SUM({$this->calcSupplyPrice()} * `cart`.`order_qty`) * {$exchangeRate}) AS `order_price_total`")
          ->select("ROUND((SUM({$this->calcSupplyPrice()} * `cart`.`order_qty`) * {$exchangeRate}), 0) AS order_subTotal");
    else : 
      $this->cart
        ->select("SUM({$this->calcSupplyPrice()} * `cart`.`order_qty`) AS `order_price_total`")
        ->select("SUM({$this->calcSupplyPrice()} * `cart`.`order_qty`) AS `order_subTotal`");
    endif;

    $this->cart
        // ->select("cart.apply_discount AS applyDiscount")
        ->joins()    
        ->joinsDefaultWhere()
        ->where('cart.buyer_id', session()->userData['buyerId'])
        ->where('supply_price.margin_level = cart.prd_section')
        ->where($whereCondition)
        ->groupBy('cart.buyer_id');

    $cartSubTotal = $this->cart->first();

    return $cartSubTotal;
  }

  public function checkMinimumAmount() {
    $return = false;
    if ( !empty($this->getCartTotalPrice()) ) {
      if ( $this->getCartTotalPrice()['order_price_total'] >= $this->basedMinimumOrderVal ) {
        $return = true;
      }
    } 
    return $return;
  }

  public function initialCartList() {
    $cartList = $this->cart->where(['updated_at <' => $this->checkDate])->findAll();
    if ( !empty($cartList) ) {
      $this->removeCart(['updated_at <' => $this->checkDate]);
      unset($cartList);
    }
  }

  public function removeCart($where = []) {
    if ( empty($where) ) {
      session()->setFlashdata('errors', 'remove where empty'); 
      return;
    }
    
    $this->cart->where($where)->delete();
    if ( $this->cart->affectedRows() ) {
      session()->setFlashdata('errors', 'cart remove success');
    } else {
      session()->setFlashdata('errors', 'cart removal error');
    }
  }
}