<?php
namespace App\Controllers;

use App\Models\CurrencyModel;
use App\Models\CartModel;
use App\Models\CartStatusModel;
use App\Models\BuyerModel;
use App\Models\OrderModel;
use App\Models\OrderDetailModel;
use App\Models\OrdersStatusModel;
use App\Models\RegionModel;
use App\Models\CountryModel;
use App\Models\RequirementModel;
use App\Models\BuyerAddressModel;
use App\Models\RequirementOptionModel;
use App\Models\RequirementRequestModel;
// use App\Models\DeliveryModel;
use App\Models\PackagingModel;
use App\Models\PackagingDetailModel;
use App\Models\MarginModel;

use App\Controllers\CartController;
use App\Controllers\AddressController;
use App\Controllers\Order;


class Inventory extends BaseController {
  public $orderNumber;

  public function __construct() {
    helper(['auth', 'product']);
    current_user();
    $this->address = new BuyerAddressModel();
    $this->order = new OrderModel();
    $this->orderDetail = new OrderDetailModel();
    $this->orderStatus = new OrdersStatusModel();
    $this->cart = new CartModel();
    $this->cartStatus = new CartStatusModel();
    $this->buyer = new BuyerModel();
    $this->currency = new CurrencyModel();
    $this->requirmentRequest = new RequirementRequestModel();
    $this->requirementOption = new RequirementOptionModel();
    $this->margin = new MarginModel();

    // $this->delivery = new DeliveryModel();
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

    $this->data['prevAddrList'] = $this->address->where('buyer_id', session()->userData['buyerId'])->orderBy('idx DESC')->findAll(0, 1);
    $this->data['regions'] = $country->findAll();
    $this->data['itus'] = $this->OrderController->getItus()->findAll();
    $this->data['requirements'] = $requirement->where('display', 1)->findAll();

    if ( $this->request->isAJAX() ) {
      $result['view'] = view('order/InventoryCheck', $this->data);
      echo json_encode($result);
    }
    return;
  }

  public function requestInventory() {
    $result = [];
    $data = $this->request->getPost();
    // d($data);

    // $getCarts = $this->cart->select('idx')->where(['cart.buyer_id' => session()->userData['buyerId']])->findAll();
    $getCarts = get_cart(['callType' => 1, ['where' => ['cart.buyer_id' => session()->userData['buyerId']]]]);
    
    if ( !empty($getCarts) ) {
      $ordersData = [];
      $cartTotalPrice = get_cart_total();

      if ( $cartTotalPrice == 0 ) {
        $result['error']['message'] = 'cart is empty';
      } else {
        if ( empty($data['address']['address_operate']) ) {
          if ( empty($data['address']['idx']) ) {
            $result['error']['message'] = 'address info is empty';
            $this->response->setStatusCode(400, $result['error']['message']);
          }
        } else {
          $data['address']['idx'] = $this->AddressController->addressConduct($data['address']);
        }
        d($data);

        $ordersData['buyer_id'] = session()->userData['buyerId'];
        $ordersData['user_id'] = session()->userData['id'];
        $ordersData['order_number'] = date('Ymd', time()).sprintf('%04d', (self::makeOrderNumber() + 1));
        $ordersData['request_amount'] = $cartTotalPrice;
        $ordersData['inventory_fixed_amount'] = $cartTotalPrice;
        $ordersData['currency_rate_idx'] = session()->currency['currencyId'];
        $ordersData['currency_code'] = session()->currency['currencyUnit'];
        $ordersData['address_id'] = $data['address']['idx'];
        $ordersData['available'] = 1;
        
        $this->order->transBegin();
        if ( $this->order->save($ordersData) ) {
          $detailResult = (self::setOrderDetail($this->order->getInsertID()));
          
          if ( $detailResult === false ) {
            $result['error']['message'] = 'order detail insert error';
            $this->order->transRollback();
          } else {
            // $this->packaging->transBegin();
            if ( !$this->packaging->save(['order_id' => $this->order->getInsertID()]) ) {
              $result['error']['message'] = 'packaging insert error';
              $this->order->transRollback();
            } else {
              d($this->order->getInsertID());
              d($this->packaging->getInsertID());
              // $this->packagingDetail->transBegin();
              if ( !$this->packagingDetail->save(['packaging_id' => $this->packaging->getInsertID(), 'status_id' => 1, 'in_progress' => 1]) ) {
                $result['error']['message'] = 'packaging detail insert error';
                // $this->packaging->transRollback();
                // $this->packagingDetail->transRollback();
                $this->order->transRollback();
                $this->response->setStatusCode(400, $result['error']['message']);
                return redirect()->to('/order')->with('error', $result['error']['message']);
              } else {
                foreach($getCarts AS $cart) {
                  if ( !$this->cart->delete(['idx' => $cart['idx']]) ) {
                    $this->order->transRollback();
                    $result['error']['message'] = 'cart delete error';
                    $this->response->setStatusCode(400, $result['error']['message']);
                    return redirect()->to('/order')->with('error', $result['error']['message']);
                  } else {
                    if ( !$this->cartStatus->delete(['cart_idx' => $cart['idx']]) ) {
                      $this->order->transRollback();
                      $result['error']['message'] = 'cart status delete error';
                      $this->response->setStatusCode(400, $result['error']['message']);
                      return redirect()->to('/order')->with('error', $result['error']['message']);
                    }
                  }
                }
                $this->order->transCommit();
                // $this->packaging->transCommit();
                // $this->packagingDetail->transCommit();
              }
            }
          }
        } else {
          $result['error']['message'] = 'order list error';
          $this->order->transRollback();
        }
      }
    } else {
      $result['error']['message'] = 'cart is empty';
    }

    if ( !empty($result['error']) )  {
      $this->response->setStatusCode(400, $result['error']['message']);
      return redirect()->to('/order')->with('error', $result['error']['message']);
    }
    return redirect()->to('/orders');
  }

  public function setOrderDetail($orderId) {
    $getCarts = get_cart(['callType' => 1, 'where' => ['cart.buyer_id' => session()->userData['buyerId']]]);
    if ( !empty($getCarts) ) {
      $this->orderDetail->transBegin();
      foreach($getCarts AS $cart) {
        $orderDetailData['order_id'] = $orderId;
        $orderDetailData['prd_id'] = $cart['prd_id'];
        $orderDetailData['prd_order_qty'] = $cart['order_qty'];
        $orderDetailData['prd_price_id'] = $cart['product_price_idx'];
        $orderDetailData['prd_supply_price_id'] = $cart['supply_price_idx'];
        $orderDetailData['prd_price'] = $cart['applied_price'];
        $orderDetailData['margin_rate_id'] = $cart['margin_rate_id'];

        if ( !$this->orderDetail->save($orderDetailData) ) {
          $this->orderDetail->transRollback();
          return false;
        }
      }
      $this->orderDetail->transCommit();
      return true;
    } else {
      $this->resonse->setStatusCode(400, 'cart is empty');
      return false;
    }
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