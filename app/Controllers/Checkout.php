<?php
namespace App\Controllers;

use Auth\Models\UserModel;
use App\Models\BuyerAddressModel;
use App\Models\OrderModel;
use App\Models\OrderDetailModel;
use App\Models\OrdersReceiptModel;
use App\Models\DeliveryModel;
use App\Models\StockModel;
use App\Models\StockDetailModel;
use App\Models\StockRequestModel;
use App\Models\StockSettingModel;
use App\Models\PackagingModel;
use App\Models\PackagingDetailModel;
use App\Models\PackagingStatusModel;
use App\Models\PaymentMethodModel;

use Paypal\Controllers\PaypalController;

use App\Controllers\CartController;
use App\Controllers\AddressController;

class Checkout extends BaseController 
{
  protected $default;

  protected $orderInfo;

  protected $isPaypal = false;
  protected $orderNumber;
  // protected $payment = 400;

  public function __construct() {
    helper('date');
    $this->curl = service('curlrequest');
    
    $this->address = new BuyerAddressModel();
    $this->order = new OrderModel();
    $this->orderDetail = new OrderDetailModel();
    $this->receipt = new OrdersReceiptModel();
    $this->delivery = new DeliveryModel();
    $this->stocksDetail = new StockDetailModel();
    $this->stocks = new StockModel();
    $this->stockReq = new StockRequestModel();
    $this->stockSet = new StockSettingModel();
    $this->packaging = new PackagingModel();
    $this->packagingDetail = new PackagingDetailModel();
    $this->packagingStatus = new PackagingStatusModel();
    $this->paymentMethod = new PaymentMethodModel();

    $this->paypal = new PaypalController();

    $this->cartController = new CartController();
    $this->addressController = new AddressController();
  }

  public function index() {
    $req = $this->request->getVar();
    $addressReq = empty($req['address']) ? [] : $req['address'];

    $getOrder = $this->order
                    ->select('orders.*')
                    ->select('buyers.name AS buyerName, buyers.phone AS phone')
                    ->join('buyers', 'buyers.id = orders.buyer_id')
                    ->where('orders.id', $req['order_id'])
                    ->first();
    if ( !empty($getOrder) ) {
      $this->orderNumber = $getOrder['order_number'];
      $receiptCnt = $this->receipt->where('order_id', $getOrder['id'])->selectCount('order_id')->find();

      if ( $getOrder['complete_payment'] == 1 && $receiptCnt['order_id'] > 0 ) {
        return redirect()->to(site_url('orders').'?order_number='.$this->orderNumber)->with('error', '이미 처리 끝');
      } else {
        $req['order'] = $getOrder;
        $req['order']['order_amount'] = empty($getOrder['inventory_fixed_amount']) ? 
                                          $getOrder['request_amount'] : 
                                          (empty($getOrder['fixed_amount']) ? $getOrder['inventory_fixed_amount'] : $getOrder['fixed_amount']);
        // $req['order_details'] = $this->orderDetail->where(['order_id' => $getOrder['id'], 'order_excepted' => 0])->findAll();
      } 
    } else return redirect()->to(site_url('orders'))->with('error', '해당하는 주문 내역이 없음');

    if ( session()->has('success') ) {
      if ( !empty($this->orderNumber) ) {
        return redirect()->to(site_url('orders').'?order_number='.$this->orderNumber);
      }
    }

    if ( $req['payment_id'] == 1 ) $this->isPaypal = true;

    if ( !empty($addressReq) ) {
      if ( !empty($addressReq['address_operate']) ) {
        if ( is_null($this->addressController->addressConduct($addressReq)) ) {
          return redirect()->to(site_url('orders').'?order_number='.$this->orderNumber)->with('error', '주소 처리 오류');
        } else {
          $addressId = $this->addressController->addressConduct($addressReq);
        }
      } else $addressId = $addressReq['idx'];
      unset($req['address']);
      $req['address'] = $this->address->where('idx', $addressId)->first();
    }
    
    // if ( $this->requestOrders($req) == 500 ) {
    //   return redirect()->to(site_url('orders').'?order_number='.$this->orderNumber);
    // } else {
    // }
    $this->requestOrders($req);    
    return redirect()->to(site_url('orders').'?order_number='.$this->orderNumber);
  }

  public function requestOrders($data = []) {
    if ( empty($data) ) return ['code' => 500 ];
    $getPaymethod = $this->paymentMethod->where('id', $data['payment_id'])->first();

    if ( !empty($getPaymethod) ) {
      if ( $getPaymethod['payment_val'] == 'paypal' ) {
        $phone = explode("-", $data['order']['phone']);
        $subtotal = ($data['order']['order_amount'] * session()->userData['depositRate']);

        $invoiceData['invoice_number'] = $data['order']['buyerName']."_".$data['order']['order_number'];
        $invoiceData['buyerName'] = $data['order']['buyerName'];
        $invoiceData['id'] = session()->userData['id'];
        $invoiceData['email'] = session()->userData['email'];
        $invoiceData['currency_code'] = $data['order']['currency_code'];
        // $invoiceData['order_details'] = $data['order_details'];
        $invoiceData['phone_code'] = empty($phone[0]) ? $data['address']['phone_code'] : $phone[0];
        $invoiceData['phone'] = empty($phone[1]) ? $data['address']['phone'] : $phone[1];
        $invoiceData['consignee'] = $data['address']['consignee'];
        $invoiceData['streetAddr1'] = $data['address']['streetAddr1'];
        $invoiceData['streetAddr2'] = $data['address']['streetAddr2'];
        $invoiceData['zipcode'] = $data['address']['zipcode'];
        $invoiceData['country_code'] = $data['address']['country_code'];
        $invoiceData['subtotal'] = $subtotal;
        $invoiceData['depositRate'] = session()->currency['currencyFloat'];
        
        $this->paypal->paypal($invoiceData);
        if ($this->paypal->result['code'] == 200 ) {
          $receiptData['payment_url'] = $this->paypal->result['payment_url'];
          $receiptData['payment_invoice_id'] = $this->paypal->result['payment_invoice_id'];
          $receiptData['payment_invoice_number'] = $this->paypal->result['payment_invoice_number'];
        } else 
        session()->setFlaashdata('error', 'paypal invoice error');
        return ['code' => 500 ];
        // return redirect()->to(site_url('orders').'?order_number='.$this->orderNumber)->with('error', 'paypal invoice error');
      }

      $receiptData['order_id'] = $data['order']['id'];
      $receiptData['receipt_type'] = 1;
      $receiptData['rq_percent'] = session()->userData['depositRate'];
      $receiptData['rq_amount'] = $subtotal;
      $receiptData['due_amount'] = $data['order']['order_amount'] - $subtotal;
      $receiptData['display'] = 1; // 1차 영수증은 무조건 보여주기
      
      if ( $this->receipt->save($receiptData) ) {
        // return redirect()->to(site_url('orders').'?order_number='.$this->orderNumber)->with('error', 'success');
        return ['code' => 200 ];
      }
    }
    // var_dump($data);
    // if ( $this->isPaypal ) {

    // }    
  }

  // public function setOrders() {
  //   $req = $this->request->getVar();

  //   // if ( session()->currency['currencyId'] == $req['checkout-currency'] ) {
  //   //   $req['checkout-currency'] = NULL;
  //   // }

  //   $this->orderInfo['buyer_id'] = session()->userData['buyerId'];
  //   $this->orderInfo['order_number'] = date('Ymd', time()).sprintf('%04d', ($this->makeOrderNumber() + 1));
  //   $this->orderInfo['order_amount'] = $req['order-total-price'];
  //   $this->orderInfo['discount_amount'] = $req['order-discount-price'];
  //   $this->orderInfo['subtotal_amount'] = $req['order-subtotal-price'];
  //   $this->orderInfo['currency_rate_idx'] = session()->currency['currencyId'];
  //   $this->orderInfo['calc_currency_rate_id'] = $req['checkout-currency'];
  //   $this->orderInfo['currency_code'] = $req['currency_code'];
  //   $this->orderInfo['taxation'] = !isset($req['taxation']) ? 0 : $req['taxation'];
  //   $this->orderInfo['payment_id'] = $req['payment_id'];

  //   // echo "<br/><br/>";
  //   // print_r($this->orderInfo);
  //   // echo "<br/><br/>";

  //   if ( $this->order->save($this->orderInfo) ) {
  //     $orderId = $this->order->getInsertID();      
  //     $this->delivery->save(['order_id' => $orderId]);
  //     if ( $this->packaging->save(['order_id' => $orderId]) ) {
  //       if ( empty($this->packagingDetail->where(['packaging_id'=> $this->packaging->getInsertID(), 'status_id' => 1])->find() ) ) {
  //         $this->packagingDetail->save(['packaging_id' => $this->packaging->getInsertID()
  //                                       , 'status_id' => 1
  //                                       , 'in_progress' => 1]);
  //       }
  //     }
  //     // unset($this->orderInfo);
  //     $this->orderNumber = $this->orderInfo['order_number'];
  //     $this->orderInfo = array();
  //     $this->orderInfo['order_id'] = $this->order->getInsertID();
  //     $this->orderInfo['subtotal_amount'] = $req['order-subtotal-price'];
  //     $this->setOrdersDetail();
  //   } else {
  //     return redirect()->back()->with('error', 'order Info error');
  //     // session()->setFlashdata('error', 'order info error');
  //   }
  // }

  // public function setOrdersDetail() {
  //   $req = $this->request->getVar();
  //   $carts = $this->getCart()->findAll();
  //   $orderId = $this->orderInfo['order_id'];
  //   $subTotalAmount = $this->orderInfo['subtotal_amount'];
  //   $success = 0;
  //   unset($this->orderInfo['order_id']);
  //   // unset($this->orderInfo['subtotal_amount']);
    
  //   foreach ( $carts as $cart ) : // 변경하기
  //     if ( $cart['apply_discount'] == 1 ) {
  //       // $this->orderInfo['prd_discount'] = ($cart['dis_prd_price'] * $cart['order_qty']);
  //       $this->orderInfo['prd_discount'] = $cart['dis_prd_price'];
  //       $this->orderInfo['margin_rate_id'] = $cart['dis_section_margin_rate_id'];
  //       $this->orderInfo['prd_price_id'] = $cart['supply_price_compare_id']; // supply price id
  //     } else {
  //       $this->orderInfo['prd_discount'] = 0;
  //       $this->orderInfo['margin_rate_id'] = $cart['margin_section_id'];
  //       $this->orderInfo['prd_price_id'] = $cart['supply_price_id']; // supply price id
  //     }

  //     $this->orderInfo['order_id'] = $orderId;
  //     $this->orderInfo['prd_id'] = $cart['prd_id'];
  //     $this->orderInfo['prd_order_qty'] = $cart['order_qty'];      
  //     $this->orderInfo['prd_price'] = $cart['prd_price'];
  //     $this->orderInfo['stock_req'] = $cart['stock_req'];
      
  //     if ( $this->orderDetail->save($this->orderInfo) ) {
  //       if ( $this->orderInfo['stock_req'] == 1 ) $stockReq = true;
  //       else $stockReq = false;
        
  //       $this->setStockDetail($this->orderInfo['prd_id'], $this->orderInfo['prd_order_qty'], $this->orderInfo['order_id'], $stockReq);
  //       $success++;
  //     }
  //   endforeach;
    
  //   if ( count($carts) == $success ) {
  //     if ( $this->isPaypal ) { 
  //       $this->paypal->paypal(array_merge($req, session()->userData));
  //       if ( $this->paypal->result['code'] == 200 ) {
  //         $receiptData['payment_url'] = $this->paypal->result['payment_url'];
  //         $receiptData['payment_invoice_id'] = $this->paypal->result['payment_invoice_id'];
  //         $receiptData['payment_invoice_number'] = $this->paypal->result['payment_invoice_number'];
          
  //         // $receiptData['due_amount'] = ($subTotalAmount * session()->userData['depositRate']);
  //       } else {
  //         // return redirect()->to(site_url('order'))->with('error', 'paypal invoice error');
  //         return redirect()->back()->with('error', 'paypal invoice error');
  //       }
  //     // } else {
  //     //   // $receiptData['due_amount'] = $subTotalAmount; // paypal이 아닐 경우 100 결제
  //     }

  //     $receiptData['order_id'] = $this->orderInfo['order_id'];
  //     $receiptData['receipt_type'] = 1;
  //     $receiptData['rq_percent'] = session()->userData['depositRate'];
  //     $receiptData['rq_amount'] = ($subTotalAmount * session()->userData['depositRate']);
  //     $receiptData['due_amount'] = ($subTotalAmount * session()->userData['depositRate']);
  //     $receiptData['display'] = 1; // 1차 영수증은 무조건 보여주기
      
  //     if ( $this->receipt->save($receiptData) ) {
  //       $this->cartController->removeCart(['buyer_id' => session()->userData['buyerId']]);
  //       session()->setFlashdata('success', 'yyyyyyy');
  //       return redirect()->to(site_url('orders'));
  //     } else {
  //       return redirect()->to(site_url('order'))->with('error', "처리중 오류 발생");
  //     }
  //   } else { 
  //     return redirect()->to(site_url('order'))->with('error', lang('Order.unknownError'));
  //   }
  // }

  // // public function setStockDetail(int $prd_id = null, int $order_qty = 0, int $orderId = NULL, int $pendStock = 1 ) {
  // public function setStockDetail(int $prd_id = null, int $order_qty = 0, int $orderId = NULL, bool $sReq = false ) {
  //   // $sReq = true: 재고요청
  //   // $findStock = [];
  //   // $stocks_id = array();
  //   $disable = false;
  //   $temp_qty = 0;
  //   $remain_qty = 0;
  //   $pendStock = 1;

  //   if ( $prd_id != NULL || $order_qty > 0 || $orderId != NULL ) {
  //     $stockSet = $this->stockSet->where('available', 1)->first();
      
  //     // echo "<br/>prd id : ".$prd_id.' order qty : '.$order_qty.' order id : '.$orderId.'<br/>';
  //     $stocks = $this->stocks
  //                     ->stockJoin()
  //                     ->select('stocks_detail.id, stocks_detail.stocks_id
  //                             , stocks_detail.supplied_qty
  //                             , stocks_detail.available AS available')
  //                     ->select('stocks.prd_id, stocks.available AS stocks_available')
  //                     ->where(['stocks.prd_id' => $prd_id, 'stocks_detail.available' => 1])
  //                     ->orderBy('stocks_detail.id ASC')
  //                     ->findAll();
  //     // print_r($stocks);
  //     // echo "<br/>";
  //     if ( !empty($stocks) ) { // detail에 재고가 있을 경우.
  //       $temp_qty = $order_qty;

  //       foreach ( $stocks AS $stock ) {
  //         if ( $temp_qty > 0 ) {
  //           echo '<br/>temp_qty '.$temp_qty.'<br/>';
  //           $stockReq = $this->stockReq
  //                           ->select('IFNULL(SUM(req_qty), 0) AS req_qty_sum')
  //                           ->where(['stocks_id' => $stock['stocks_id']])
  //                           ->where('stock_id', $stock['id'])
  //                           ->groupBy('stock_id')
  //                           ->first();
  //           // print_r($stockReq);
  //           // echo "<br/>";
  //           if ( empty($stockReq) ) $stockReq['req_qty_sum'] = 0;
  //           // echo "stock supplied qty : {$stock['supplied_qty']}<br/>";
  //           // echo "req_qty_sum : {$stockReq['req_qty_sum']}<br/>";
  //           $remain_stock = $stock['supplied_qty'] - $stockReq['req_qty_sum'];
  //           // echo "remain stocks : $remain_stock<br/>";
  //           if (  $remain_stock < $temp_qty ) {
  //             $this->stockReq->save(['order_id' => $orderId
  //                                   , 'req_qty' => $remain_stock
  //                                   , 'prd_id'  => $prd_id
  //                                   , 'stocks_id' => $stock['stocks_id']
  //                                   , 'stock_id'  => $stock['id']
  //                                   , 'stock_type'  => 1]);
  //             $this->stocksDetail->save(['id'=> $stock['id']
  //                                   , 'available' => 0]);
  //             $temp_qty = $temp_qty - $remain_stock;
  //           } else if ( $remain_stock > $temp_qty ) {
  //             $this->stockReq->save(['order_id' => $orderId
  //                                   , 'req_qty' => $temp_qty
  //                                   , 'prd_id'  => $prd_id
  //                                   , 'stocks_id' => $stock['stocks_id']
  //                                   , 'stock_id'  => $stock['id']
  //                                   , 'stock_type'  => 1]);
  //             $temp_qty = 0;
  //             return;
  //           } else if ( $remain_stock == $temp_qty ) {
  //             $this->stockReq->save(['order_id' => $orderId
  //                                   , 'req_qty' => $temp_qty
  //                                   , 'prd_id'  => $prd_id
  //                                   , 'stocks_id' => $stock['stocks_id']
  //                                   , 'stock_id'  => $stock['id']
  //                                   , 'stock_type'  => 1]);
  //             $this->stocksDetail->save(['id'=> $stock['id']
  //                                       , 'available' => 0]);
  //             $temp_qty = 0;
  //             return;
  //           }
  //         }
  //       }
  //     } else { // detail에 재고가 없을 경우.
  //       echo "유효한 재고가 없음. order request 상품<br/>";
  //       $stock = $this->stocks->where(['prd_id' => $prd_id
  //                                     , 'available' => 1])->first();
  //       if ( !empty($stock) ) {
  //         $this->stockReq->save(['order_id' => $orderId
  //                               , 'req_qty' => $order_qty
  //                               , 'prd_id' => $prd_id
  //                               , 'stocks_id' => $stock['id']
  //                               , 'stock_type' => 2]);
  //       // } else {
  //       //   return session()->setFlashdata('error', 'order request error');
  //       }
  //     }
  //     return;
  //   }
  // }

  // public function getCart() {
  //   $where = [];
  //   // if ( !empty($this->request->getVar('taxation')) ) { // 0:영,과세 1:영세만 2:과세만
  //   //   $where = array_merge($where, ['cart.onlyZeroTax'=> $this->request->getVar('taxation')]);
  //   // }

  //   $carts = $this->cartController->getCartList()
  //                 ->select('cart.prd_id')
  //                 ->select('supply_price.idx AS supply_price_id')
  //                 ->select('supply_price_compare.idx AS supply_price_compare_id')
  //                 ->where('supply_price.margin_level = cart.prd_section')
  //                 ->where($where);
  //   // print_r($carts);
  //   return $carts;
  // }

  // public function makeOrderNumber() {
  //   $cnt = $this->order->where('DATE(`created_at`)', date('Y-m-d', time()))->countAllResults();
  //   return $cnt;
  // }
}
