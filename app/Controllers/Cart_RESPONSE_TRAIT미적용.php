<?php
namespace App\Controllers;

use App\Models\CartModel;
use App\Models\ProductModel;
use App\Models\StockModel;

use CodeIgniter\I18n\Time;

class Cart extends BaseController {
  protected $tax = 1.1;
  protected $products;
  protected $data;
  
  public $basedDiscountVal = 7000; // B구간일 때 A구간 변경되는 금액
  public $basedMinimumOrderVal = 1000;
  public $checkDate;

  public function __construct() {
    helper(['product', 'auth']);
    current_user();
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

  public function addCartList($data = array()) {
    if ( empty($data) ) $data = $this->request->getPost();
    if ( empty($data['prd_id']) ) return ['product' => 'Invalid data'];
    
    $margin_level = session()->userData['buyerMargin'];
    
    if ( isset($data['idx']) ) {
      self::editCartList();
      return;
    }
    $prdSpq = get_spq(['where' => ['product_idx' => $data['prd_id']]]);
    $product_price = [ 'select' => productPriceDefaultSelect()
                      , 'where' => ['product_idx' => $data['prd_id']] ];
    $supply_price = [ 'select' => supplyPriceDefaultSelect()
                      , 'where' => [  'product_idx' => $data['prd_id'] ]
                      , 'orderby' => $margin_level > 1 ? 'margin_level desc' : '' ];
    $product = get_product(['where' => ['id' => $data['prd_id']]]);
    $combineProductInfo = array_merge($product, combine_price_info($product_price, $supply_price));

    if ( !empty($combineProductInfo) ) {
      $combineProductInfo['exchange_rate'] = session()->currency['exchangeRate'];
    //   $brand = brand([ 'select' => 'brand_id, brand_name'
    //                   , 'where' => ['brand_id' => $data['brand_id']]]);
    //   // var_dump($brand);
    //   $combineProductInfo = array_merge($combineProductInfo, $prdSpq, $brand);

    //   if ( $this->cart->save(['buyer_id'  => session()->userData['buyerId']
    //                         , 'prd_id'  => $data['prd_id']] ) ) {
                      
    //     $combineProductInfo['cart_idx'] = $this->cart->getInsertID();
    //     $combineProductInfo['order_qty'] = $combineProductInfo['moq'];
    //     $combineProductInfo['applied_price'] = round(($combineProductInfo['price'] /session()->currency['exchangeRate']), 2);

    //     if ( $this->cartStatus->save($combineProductInfo) ) {
    //       $code = 200;
    //       $msg = $combineProductInfo;
    //       $data = get_carts();
    //     } else $msg = '입력 안됨';
    //   } else $msg = '입력 안됨...';
    } else return '해당하는 제품의 가격 정보가 없습니다.';
    
    // return json_encode(['Code' => $code, 'Msg' => $msg, 'data' => $resultData]);
  }

  public function editCartList($data = array()) {
    if ( empty($data) ) $data = $this->request->getVar();
    $where = [];
    $result = [];
    
    if ( !isset($data['idx']) ) {
      $where = ['buyer_id' => session()->userData['buyerId']];
      if ( !empty($data['prd_id']) ) $where = array_merge($where, ['prd_id' => $data['prd_id']]);
    } else $where = ['idx' => $data['idx']];

    $cart = get_cart(['where' => $where]);
    if ( !empty($cart) ) {
      $dataOperator = $data['dataType'];
      unset($data['dataType']);
      
      $result = ['cartList' => $cart];

      if ( $dataOperator == 'update' ) {        
        if ( isset($data['order_qty'] ) ) $data['order_qty'] = $cart['order_qty'] + $data['order_qty'];
        else $data['order_qty'] = $cart['order_qty'];

        $data['req_price'] = round(($cart['applied_price'] * $data['order_qty']), 2);

        // if ( $this->cart->save($data) ) {
        //   $result['update'] = 'success';
        // } else {
        //   $this->response->setStatusCode(404);
        //   $result['update'] = $this->cart->error()['message'];
        // }
      } else {
        $deleteResult = 'success';
        $this->cart->delete(['idx' => $data['idx']]);
        if ( !$this->cart->affectedRows() ) {
          // $this->response->setStatusCode(404);
          $deleteResult = $this->cart->error()['message'];
          $this->fail($deleteResult, 400);
        } 
        $result['delete'] = $deleteResult;
      }

      if ( $this->request->isAJAX() ) {
        // return json_encode($result);
        return $this->response->setJSON($result); // 하지만 안됨...ㅠㅠㅠㅠㅠㅠ
        // return $this->respond($result); // use ResponseTrait; 일때만 가능
        // return $this->response->setStatusCode(404);
      }
      return $result;
    //   $this->cart->where($where);
    //   if ( empty($data['oper']) ) {
    //     $prdTotalPrice = ($data['product_price'] * $cart['order_qty']);
    //     $this->cart
    //         ->set(['order_qty' => $data['order_qty']])
    //         ->update();
        
    //     if ( $this->cart->affectedRows() ) {
    //       $prdTotalPrice = ($data['product_price'] * $data['order_qty']);
    //       $code = 200;
    //       $msg = number_format($prdTotalPrice, session()->currency['currencyFloat']);
    //     } else {
    //       $code = 500; 
    //       $msg = lang('Lang.unknownError', [ 'error' => 'update' ]);
    //     }
    //   } else {
    //     if ( $data['oper'] == 'del' ) {
    //       $this->cart->delete();
    //       if ( $this->cart->affectedRows() ) {
    //         if ( isset($data['stock_req_parent']) && isset($data['case']) ) {
    //           $this->cart->where(['idx' => $data['stock_req_parent']]);
    //           if ( $data['case'] == 1 ) {
    //             $this->cart->set(['stock_req_parent' => NULL])->update();
    //           } else if ( $data['case'] == 0 ) {
    //             $this->cart->delete();
    //           }

    //           if ( $this->cart->affectedRows() ) {
    //             $code = 200;
    //             // // $msg = $this->cart->getLastQuery();
    //             // $this->applyDiscountCart();
    //           } else {
    //             $code = 500;
    //             // $msg = $this->cart->getLastQuery();
    //           }
    //         } else {
    //           $code = 200;
    //           // $this->applyDiscountCart();
    //         }
    //       } else {
    //         $code = 500; 
    //         // $msg = $data;
    //       }
    //     }
    //   }
    // } else {
    //   $code = 500;
    //   $msg = $this->cart->error()['message'].' is null '.json_encode($data);
    // }

    // if ( $this->request->isAJAX() ) {
    //   return json_encode(['Code' => $code, 'Msg' => $msg]);
    }
  }
}