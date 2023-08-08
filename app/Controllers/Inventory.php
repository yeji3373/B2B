<?php
namespace App\Controllers;

use App\Models\CurrencyModel;
use App\Models\CartModel;
use App\Models\BuyerModel;
use App\Models\OrderModel;
use App\Models\OrderDetailModel;
use App\Models\OrdersStatusModel;
use App\Models\RegionModel;
use App\Models\CountryModel;
use App\Models\RequirementModel;
use App\Models\BuyerAddressModel;
use App\Models\RequirementRequestModel;
use App\Models\DeliveryModel;
use App\Models\PackagingModel;
use App\Models\PackagingDetailModel;
use App\Models\MarginModel;

use App\Controllers\CartController;
use App\Controllers\AddressController;
use App\Controllers\Order;


class Inventory extends BaseController {
  public $orderNumber;

  public function __construct() {
    $this->address = new BuyerAddressModel();
    $this->order = new OrderModel();
    $this->orderDetail = new OrderDetailModel();
    $this->orderStatus = new OrdersStatusModel();
    $this->cart = new CartModel();
    $this->buyer = new BuyerModel();
    $this->currency = new CurrencyModel();
    $this->requirmentRequest = new RequirementRequestModel();
    $this->margin = new MarginModel();

    $this->delivery = new DeliveryModel();
    $this->packaging = new PackagingModel();
    $this->packagingDetail = new PackagingDetailModel();

    $this->CartController = new CartController();
    $this->AddressController = new AddressController();
    $this->OrderController = new Order();
  }

  public function index() {
    // // $cart = $this->CartController->getCartList()->findAll();
    // // return print_r($cart);
    // return print_r($this->margin->where('margin_level', $));
    return print_r(session()->currency);
  }

  public function requestInventoryCheck() {
    $country = new CountryModel();
    $requirement = new RequirementModel();

    if ( $this->CartController->checkMinimumAmount() === false ) {
      if ( $this->request->isAJAX()) {
        return json_encode(['error' => lang('Order.orderMinCheck', [$this->CartController->basedMinimumOrderVal])]);
      } 
      return redirect()->to(site_url('/order'))->with('error', lang('Order.orderMinCheck', [$this->CartController->basedMinimumOrderVal]));
    }

    $this->data['prevAddrList'] = $this->address->where('buyer_id', session()->userData['buyerId'])->orderBy('idx DESC')->findAll(0, 1);
    $this->data['regions'] = $country->findAll();
    $this->data['itus'] = $this->OrderController->getItus()->findAll();
    $this->data['requirements'] = $requirement->where('display', 1)->findAll();
    // $this->cartList();
    
    return view('order/InventoryCheck', $this->data);
  }

  public function requestInventory() {
    $data = $this->request->getVar();
    // print_r($data['requirement']);
    $request = [];
    
    $cartList = $this->cart->where('buyer_id', session()->userData['buyerId'])->findAll();

    if ( !empty($cartList)) {
      $currency = $this->currency->currencyJoin()
                      ->select('currency.*')
                      ->select('currency_rate.cRate_idx AS currency_rate_idx
                              , currency_rate.exchange_rate')
                      ->where(['currency_rate.default_set' => 1])->first();
      if ( !empty($currency) ) {
        if ( $currency['exchange_rate'] != session()->currency['basedExchangeRate'] ) {
          return redirect()->to('/logout');
        }
      }

      if ( $data['address_operate'] == true ) {
        $addressId = $this->AddressController->addressConduct();
      } else {
        $addressId = $data['address_id'];
      }

      if ( !empty($addressId) ) {
        $request['buyer_id'] = session()->userData['buyerId'];
        $request['order_number'] = date('Ymd', time()).sprintf('%04d', ($this->makeOrderNumber() + 1));
        $request['request_amount'] = $data['request-total-price'];
        $request['currency_rate_idx'] = $currency['currency_rate_idx'];
        $request['currency_code'] = $currency['currency_code'];
        $request['address_id']  = $addressId;
        
        // if ( $cartList['temp_order_number'] == $request['order_number'] ) {
        //   $this->order->where(['order_numeber'=> $request['order_numeber']
        //                       , 'buyer_id' => $request['buyer_id']
        //                       , 'available' => 0 ])->first();
          
        // } else {
        //   $this->cart
        //       ->where('buyer_id', session()->userData['buyerId'])
        //       ->set(['request_order_date' => 'NOW()'
        //             , 'temp_order_number' => $request['order_number']])
        //       ->update();
        // }

        // if ( $this->cart->affectedRows() ) {
          if ( $this->order->save($request) ) {
            $orderId = $this->order->getInsertID();
            $this->orderNumber = $request['order_number'];

            $orderDetailFailed = $this->setOrderDetail($orderId);

            if ( empty($orderDetailFailed) ) {  // detail 입력중 오류 없음
              if ( $this->delivery->save(['order_id' => $orderId]) ) {
                if ( $this->packaging->save(['order_id' => $orderId]) ) {
                  if ( empty($this->packagingDetail->where(['packaging_id'=> $this->packaging->getInsertID(), 'status_id' => 1])->find() ) ) {
                    if ( $this->packagingDetail->save(['packaging_id' => $this->packaging->getInsertID()
                                                  , 'status_id' => 1
                                                  , 'in_progress' => 1]) ) :
                    else :
                      //  packagingDetail save error
                      return redirect()->to(site_url('orders'))->with('error', '처리중 오류 발생');
                    endif;
                  } else {
                    // 이미 있음...
                  }
                } else {
                  //  packaging save error
                  return redirect()->to(site_url('orders'))->with('error', '처리중 오류 발생');
                }
              } else {
                // delivery save error
                return redirect()->to(site_url('orders'))->with('error', '처리중 오류 발생');
              }

              // $orderStatus = $this->orderStatus->where(['available' => 1, 'default' => 1])->first();
              // if ( !empty($orderStatus) ) $orderStatusId = $orderStatus['status_id'];
              // else $orderStatusId = 1;

              // if ( !empty($orderStatus) && !empty($orderStatusId) ) {
                if ( $this->order->save(['id'=> $orderId, 'available'=> 1]) ) {  
                } else {
                  // order 활성화 중에 오류 발생
                  return redirect()->to(site_url('orders'))->with('error', '처리중 오류 발생');
                }
              // } else {
              //   // orderStatus가 없어서 order 활성화 안됨
              //   return redirect()->to(site_url('orders'))->with('error', '처리중 오류 발생');
              // }
            } else {
              // detail 입력중에 오류 발생
              return redirect()->to(site_url('orders'))->with('error', '처리중 오류 발생');
            }

            return redirect()->to(site_url('orders'));
          } else {
            return redirect()->to(site_url('orders'))->with('error', '처리중 오류 발생');
          }
        // }
      } else {
        return json_encode(['code' => 500, 'Msg' => 'address 등록 오류']);
      }
    } else {
      return redirect()->to('/order')->widthInput()->with('error', 'Cart is empty');;
    }
  }

  public function setOrderDetail($orderId) {
    $margin_level = 2;
    $data = $this->request->getVar();
    $failed = [];

    if ( empty($orderId) ) return session()->setFlashdata('error', '재고요청서 작성 중 오류가 발생됐습니다.');

    $buyer = $this->buyer->where(['id'=> session()->userData['buyerId'], 'available'=> 1])->first();
    if ( !empty($buyer) ) $margin_level = $buyer['margin_level'];
    else return session()->setFlashdata('error', 'buyer 정보가 일치하지 않음');

    $cartList = $this->cart
                  ->select('cart.buyer_id, 
                          cart.prd_id,
                          cart.brand_id,
                          cart.product_price_idx AS prd_price_id,
                          cart.order_qty AS prd_order_qty,
                          cart.margin_section_id,
                          supply_price.price AS prd_price')
                  ->join('supply_price', 'supply_price.product_price_idx = cart.product_price_idx')
                  ->where(['cart.buyer_id'=> session()->userData['buyerId']
                          , 'supply_price.margin_level' => $margin_level])->findAll();

    if ( !empty($cartList) ) :
      $success = 0;
      foreach( $cartList AS $i => $cart ) :
        $margin = $this->margin->margin()->where(['margin_rate.brand_id' => $cart['brand_id'], 'margin_rate.idx' => $cart['margin_section_id']])->first();
        $prd_price = ROUND(($cart['prd_price'] / session()->currency['basedExchangeRate']), session()->currency['currencyFloat']);

        if ( !empty($margin) ) {
          if ( $this->orderDetail->save( array_merge($cart, [ 'order_id' => $orderId, 'prd_price' => $prd_price, 'margin_rate_id' => $margin['margin_rate_id']]) ) ) {
            // 성공했을 때 order 활성화
            if ( $i == (count($cartList) - 1) ) $this->CartController->removeCart(['buyer_id' => session()->userData['buyerId']]);

            if ( !empty($data['requirement']) ) {
              foreach($data['requirement'] AS $requirement ){
                $this->requirmentRequest->save(array_merge($requirement, ['order_id' => $orderId, 'order_detail_id'=> $this->orderDetail->getInsertID()]));
              }
            }
            $success++;
          } else {
            $success--;
            // 실패했을 때 order 비활성화 유지
            array_push($failed, ['order_id'=> $orderId
                                , 'cart_idx' => $cart['idx']
                                , 'buyer_id' => session()->userData['buyerId']
                                , 'prd_id' => $cart['prd_id']]);
          }
        }
      endforeach;
    endif;

    return $failed;
  }

  // public function setOrderStatus() {
  //   $orderStatus = $this->orderStatus->where(['available' => 1, 'default' => 1])->first();
  //   if ( !empty($orderStatus) ) $orderStatusId = $orderStatus['status_id'];
  //   else $orderStatusId = 1;

  //   return $orderStatusId;
  // }

  public function makeOrderNumber() {
    // $cnt = $this->order->where(['DATE(`created_at`)' => 'DATE(NOW())', 'available' => 1])->countAllResults();
    $cnt = $this->order->where(['DATE(`created_at`)' => date('Y-m-d', time())])->countAllResults();
    return $cnt;
  }
}