<?php
namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\CartModel;
use App\Models\CartStatusModel;
use App\Models\ProductModel;
use App\Models\StockModel;

use App\Controllers\ApiLoggedIn;

use CodeIgniter\I18n\Time;

class Cart extends BaseController {
  use ResponseTrait;
  protected $tax = 1.1;
  protected $products;
  protected $data;
  
  public $basedDiscountVal = 7000; // B구간일 때 A구간 변경되는 금액
  public $basedMinimumOrderVal = 1000;
  public $checkDate;

  public function __construct() {
    helper(['product', 'auth', 'brand']);
    current_user();
    $this->cart = new CartModel();
    $this->cartStatus = new CartStatusModel();
    $this->product = new ProductModel();
    $this->stocks = new StockModel();
    $this->checkDate = date_format(new Time('-7 days'), 'Y-m-d 23:59:59');
  }

  public function getCarts() {
    // helper('cart_item');
    // $data = $this->request->getPost();
    // $temp = json_decode(json_encode($data), true);
    // var_dump($temp);
    // return cart_list($data['carts']);
    $data = $this->request->getPost();
    $cartSet = ['where'     => ['cart.buyer_id' => session()->userData['buyerId']]];

    $cartList = get_cart($cartSet);

    foreach($cartList AS $i => $cartItem) {
      $cartItem['retail_price'] = round(($cartItem['retail_price'] / $cartItem['exchange_rate']), 2);
      $cartList[$i] = array_merge($cartItem, get_product(['select' => productDefaultSelect(), 'where' => ['id' => $cartItem['prd_id']]]));
    }
    return $cartList;
  }

  public function cartSumPrice() {
    $params = $this->request->getPost();
    $cartTotal = get_cart_total();
    $totalPrice = 0;
    
    if ( !empty($cartTotal) ) $totalPrice = $cartTotal;

    echo json_encode(['totalPrice' => round($totalPrice, 2)]);
    return round($totalPrice, 2);
  }

  public function addCartList($data = array()) {
    if ( empty($data) ) $data = $this->request->getPost();
    if ( empty($data['prd_id']) ) return ['product' => 'Invalid data'];
       
    if ( isset($data['idx']) ) {
      self::editCartList();
      return;
    }

    $margin_level = session()->userData['buyerMargin'];
    $result = [];
    $cartStatusData = [];
    $cartData = [];
    // d($data);

    $product = get_product(['where' => ['id' => $data['prd_id']]]);
    // d($product);
    if ( !empty($product) ) {
      $cartData['buyer_id'] = session()->userData['buyerId'];
      $cartData['prd_id'] = $product['id'];
      $cartStatusData['exchange_rate'] = session()->currency['exchangeRate'];

      $prdSpq = get_spq([ 'where' => ['product_idx' => $data['prd_id']]]);
      if ( !empty($prdSpq) ) {
        // d($prdSpq);
        $cartData['order_qty'] = $prdSpq['moq'];
        $cartStatusData = array_merge($cartStatusData, $prdSpq);
        
        $product_price = get_product_price(
                          [ 'select' => productPriceDefaultSelect()
                            , 'where' => ['product_idx' => $data['prd_id']] ]);
        
        if ( !empty($product_price) ) {
          // d($product_price);
          $cartStatusData = array_merge($cartStatusData, $product_price);

          $supply_price = get_supply_price(
                            [ 'select' => supplyPriceDefaultSelect()
                            , 'where' => [  'product_idx' => $data['prd_id'] ]
                            , 'orderby' => $margin_level > 1 ? 'margin_level desc' : '' ]);

          $getMarginRate = get_margin_rate(['where' => ['brand_id' => $product['brand_id'], 'available' => 1], 'orderby'=> $margin_level > 1 ? 'idx DESC' : '']);

          if ( !empty($getMarginRate) ) {
            $cartStatusData['margin_rate_id'] = $getMarginRate['idx'];
          
            if ( !empty($supply_price) ) {
              // d($supply_price);
              $cartStatusData['supply_price_idx'] = $supply_price['supply_price_idx'];
              $cartStatusData['price'] = $supply_price['price'];
              $cartStatusData['applied_price'] = round(($supply_price['price'] / $cartStatusData['exchange_rate']), 2);            
              $cartData['req_price'] = round(($cartStatusData['applied_price'] * $cartData['order_qty']), 2);

              $brand = brand([ 'select' => 'brand_id, brand_name'
                              , 'where' => ['brand_id' => $product['brand_id']]]);
            
              if ( !empty($brand) ) {
                $cartStatusData['brand_id'] = $brand['brand_id'];
                $cartStatusData['brand_name'] = $brand['brand_name'];

                $this->cart->transBegin();
                if ( !$this->cart->save($cartData) ) {
                  $this->cart->transRollback();
                } else {
                  $cartStatusData['cart_idx'] = $this->cart->getInsertID();
                  $this->cartStatus->transBegin();

                  if ( $this->cartStatus->save($cartStatusData) ) {
                    $this->cart->transCommit();
                    $this->cartStatus->transCommit();
                    $return['insert'] = 'success';
                  } else {
                    $result['error'] = 'cart status insert error';
                    $this->cart->transRollback();
                    $this->cartStatus->transRollback();
                  }
                }
              } else {        
                $result['error'] = '해당하는 브랜드 없음';
              }
            } else {
              $result['error'] = '해당하는 제품의 가격 정보가 없음';
            }
          } else $result['error'] = '해당하는 제품의 마진 정보가 없음';
        } else {
          $result['error'] = '해당하는 제품의 가격 정보가 없음'; // supply price empty
        }
      } else {
        $result['error'] = 'spq 정보 없음';
      }
      // d($cartData);
      // d($cartStatusData);
    } else {
      $result['error'] = '해당하는 제품이 없음';
    }
    
    if ( $this->request->isAJAX() ) {
      if ( isset($result['error']) ) return $this->response->setStatusCode(400, json_encode($result));
      echo json_encode($result);
    }
    return $result;
  }

  public function editCartList($data = array()) {
    if ( empty($data) ) $data = $this->request->getVar();
    $where = [];
    $result = [];
    $dataOperator = empty($data['dataType']) ? '' : $data['dataType'];

    // d($data);
    
    if ( !isset($data['idx']) ) {
      $where = ['cart.buyer_id' => session()->userData['buyerId']];
      if ( !empty($data['prd_id']) ) $where = array_merge($where, ['prd_id' => $data['prd_id']]);
      else {
        $this->response->setStatusCode(400, '해당하는 정보가 없음');
        return "해당하는 정보가 없음";
      }
    } else $where = ['idx' => $data['idx']];

    $cart = get_cart(['where' => $where]);
    // d($cart);

    if ( !empty($cart) ) {
      unset($data['dataType']);
      $result = ['cartList' => $cart];

      if ( $dataOperator == 'update' ) {
        /**
         * $data['operator'] 값이 없거나 0일 경우 증가 
         * $data['operator'] 값이 있을 경우 감소
        */
        $spq = get_spq(['select' => 'moq, calc_unit', 'where' => ['product_idx' => $cart['product_idx']]]);        
        if ( empty($data['order_qty']) ) {
          $data['order_qty'] = $spq['moq'];
          if ( $cart['calc_code'] == 0 ) {
            if ( !empty($cart['moq']) ) $data['order_qty'] = $cart['moq'];
          } else if ( $cart['calc_code'] == 1 ) {
            if ( !empty($cart['calc_unit']) ) $data['order_qty'] = $cart['calc_unit'];
            else $data['order_qty'] = $spq['cart_unit'];
          }
        }

        if ( empty($cart['order_qty'])) $cart['order_qty'] = $spq['moq'];
        if ( isset($data['operator']) ) {
          if ( $data['operator'] == 0 ) {
            $data['order_qty'] = $cart['order_qty'] + $data['order_qty'];
          } else if ( $data['operator'] == 1 ) {
            $data['order_qty'] = $cart['order_qty'] - $data['order_qty'];
            if ( $data['order_qty'] <= 0 ) {
              $data['order_qty'] = $cart['order_qty'];
              $this->response->setStatusCode(400, 'This is the minimum quantity');
              return;
            }
          } else if ( $data['operator'] == 2 ) {
            if ( $data['order_qty'] < $spq['moq'] ) {
              $this->response->setStatusCode(400, "The minimum quantity is {$spq['moq']}");
              return;
            }
          }
        }

        $data['req_price'] = round(($cart['applied_price'] * $data['order_qty']), 2);
        // d($data);
        // return;
        if ( $this->cart->save($data) ) {
          $result['cartList']['req_price'] = $data['req_price'];
          $result['cartList']['order_qty'] = $data['order_qty'];
          $result['update'] = 'success';
        } else {
          $result['update'] = $this->cart->error()['message'];
          $this->response->setStatusCode(400, json_encode($result['update']));
        }
      } else if ( $dataOperator == 'delete' ) {
        // d($data);
        // echo "delete";
        // return;
        $deleteResult = 'success';
        $this->cart->transBegin();
        $this->cart->delete(['idx' => $data['idx']]);
        if ( !$this->cart->affectedRows() ) {
          $this->cart->transRollback();
          $deleteResult = $this->cart->error()['message'];
          // $this->fail($deleteResult, 400);
          $this->response->setStatusCode(400, $result);
        }  else {
          $getCartStatus = $this->cartStatus->where(['cart_idx' => $data['idx']])->first();

          if ( !empty($getCartStatus) ) {
            if ( !$this->cartStatus->delete(['cart_status_idx' => $getCartStatus['cart_status_idx']])) {
              $this->cart->transRollback();
            } else {
              $this->cart->transCommit();
            }
          }
        }
        $result['delete'] = $deleteResult;
      }
      // d($result);

      if ( $this->request->isAJAX() ) {
        echo json_encode($result);
      }
      // var_dump($result);
      // return;
      return redirect()->back();
    }
  }

  public function checkMinimumAmount() {
    $totalPrice = self::cartSumPrice();

    if ( !empty($totalPrice) ) {
      if ( $totalPrice < $this->basedMinimumOrderVal) {
        $this->response->setStatusCode(400, lang('Order.orderMinCheck', [$this->basedMinimumOrderVal]));
        return;
      }
    }
  }

  public function initialCartList() {
    $cartList = $this->cart->where(['updated_at <' => $this->checkDate])->findAll();
    if ( !empty($cartList) ) {
      $this->removeCart(['updated_at <' => $this->checkDate]);
      unset($cartList);
    }
  }
}