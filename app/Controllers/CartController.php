<?php
namespace App\Controllers;

use App\Models\CartModel;

use CodeIgniter\I18n\Time;

class CartController extends BaseController {
  protected $tax = 1.1;
  protected $products;
  protected $data;
  
  public $basedDiscountVal = 7000; // B구간일 때 A구간 변경되는 금액
  public $basedMinimumOrderVal = 44;
  public $checkDate;

  public function __construct() {
    helper('date');
    $this->cart = new CartModel();
    $this->checkDate = date_format(new Time('-7 days'), 'Y-m-d 23:59:59');
  }

  public function getCartList() {
    $cartList = $this->cart
                    ->cartJoin()
                    ->select('cart.margin_section_id, cart.dis_section_margin_rate_id')
                    ->select($this->calcRetailPrice().' AS retail_price')
                    ->select($this->calcSupplyPrice().' AS prd_price')
                    ->select("( {$this->calcSupplyPrice()} * cart.order_qty ) AS order_price")
                    ->select(" IF( cart.apply_discount = 1, ({$this->calcSupplyPriceCompare()} * cart.order_qty), 0) AS order_discount_price")
                    ->select(" IF( cart.apply_discount = 1, ({$this->calcSupplyPrice()} - {$this->calcSupplyPriceCompare()}), 0) AS dis_prd_price");
  
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

  public function getCartTotalPrice( $where = array(), $exchange = 1 ) {
    $whereCondition = array();
    $cart = $this->cart->cartJoin();
    if ( count($where) > 0 ) $whereCondition = $where;
    if ( $exchange > 1 ) :
      $cart->select("(SUM({$this->calcSupplyPrice()} * `cart`.`order_qty`) * {$exchange}) AS `order_price_total`")
          ->select("IF ( `cart`.`apply_discount` = 1, 
                          ROUND((SUM(({$this->calcSupplyPrice()} - {$this->calcSupplyPriceCompare()}) * `cart`.`order_qty`) * {$exchange}), 0),
                          0 
                        ) AS `order_discount_total`")
          ->select("IF ( `cart`.`apply_discount` = 1, 
                          ROUND((SUM({$this->calcSupplyPriceCompare()} * `cart`.`order_qty`) * {$exchange}), 0),
                          ROUND((SUM({$this->calcSupplyPrice()} * `cart`.`order_qty`) * {$exchange}), 0)
                        ) AS order_subTotal");
    else : 
      $cart->select("SUM({$this->calcSupplyPrice()} * `cart`.`order_qty`) AS `order_price_total`")
      ->select("IF ( `cart`.`apply_discount` = 1, 
                      SUM(({$this->calcSupplyPrice()} - {$this->calcSupplyPriceCompare()}) * `cart`.`order_qty`),
                      0 
                    ) AS `order_discount_total`")
      ->select("IF ( `cart`.`apply_discount` = 1, 
                      SUM({$this->calcSupplyPriceCompare()} * `cart`.`order_qty`),
                      SUM({$this->calcSupplyPrice()} * `cart`.`order_qty`)
                    ) AS `order_subTotal`");
    endif;

    $cart->select('cart.apply_discount AS applyDiscount')
          ->where('cart.buyer_id', session()->userData['buyerId'])
          ->where('supply_price.margin_level = cart.prd_section')
          ->where($whereCondition)
          ->groupBy('cart.buyer_id');

    $cartSubTotal = $cart->first();

    if ( empty($cartSubTotal) ) {
      return NULL;
    } else return $cartSubTotal;
  }

  public function initialCartList() {
    $cartList = $this->cart->where(['updated_at <' => $this->checkDate])->findAll();
    if ( !empty($cartList) ) {
      $this->removeCart(['updated_at <' => $this->checkDate]);
      unset($cartList);
    }

    // $cartList = $this->cart
    //               ->cartJoin()
    //               ->where(['cart.buyer_id' => session()->userData['buyerId']])
    //               ->findAll();
    // if ( !empty($cartList) ) {
    //   print_r($cartList);
    // }

    if ( !empty($this->getCartTotalPrice()) ) {
      if ( $this->getCartTotalPrice()['order_price_total'] < $this->basedDiscountVal ) {
        $this->cart
            ->set(['apply_discount' => 0])
            ->where(['buyer_id' => session()->userData['buyerId']])
            ->update();
      } else {
        $this->cart
            ->set(['apply_discount' => 1])
            ->where(['buyer_id' => session()->userData['buyerId']])
            ->update();
      }
    }

    // // $cartList = $this->cart->where(['buyer_id' => session()->userData['buyerId'], 'product_price_changed' => 1])->findAll();
    // // if ( !empty($cartList )) {
    // //   $this->cart->set(['product_price_changed' => 0])
    // //               ->where(['buyer_id' => session()->userData['buyerId'], 'product_price_changed' => 1])
    // //               ->update();
    // //   session()->setFlashdata('changed', 'product price');
    // //   unset($cartList);
    // // }

    // // // if ( session()->currency['preferentialRate'] === true ) {
    // // //   $buyerCurrency = $this->buyerCurrency->where(['buyer_id'=> session()->userData['buyerId']])->findAll();
    // // // // 관리자에서 우대 환율 변경할 경우, 해당하는 바이어의 카트 내역 확인 후 금액 변경하기.
    // // // }

    // $cartList = $this->cart->where(['buyer_id' => session()->userData['buyerId']])->findAll();
    // if (!empty($cartList) ) {
    //   // foreach($cartList AS $cart) {
    //   //   $cart['prd_id']
    //   // }
    // }
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