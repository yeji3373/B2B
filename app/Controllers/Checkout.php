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
use App\Models\CurrencyModel;

use Paypal\Controllers\PaypalController;

use App\Controllers\CartController;
use App\Controllers\AddressController;

class Checkout extends BaseController {
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
    // var_dump($req);
    // var_dump($addressReq);
    // return;
    $getOrder = $this->order
                    ->select('orders.*')
                    ->select('buyers.name AS buyerName, buyers.phone AS phone')
                    ->join('buyers', 'buyers.id = orders.buyer_id')
                    ->where('orders.id', $req['order_id'])
                    ->first();
    if ( !empty($getOrder) ) {
      // $invoiceIndex = $this->order->where(['buyer_id' => $getOrder['buyer_id'], 'DATE(created_at)' => DATE('Y-m-d', strtotime($getOrder['created_at']))])->countAllResults();
      $getOrderDetails = $this->orderDetail->where(['order_id' => $getOrder['id'], 'order_excepted' => 0])->findAll();

      if ( !empty($getOrderDetails) ) {
        $order_amount = 0;
        foreach($getOrderDetails AS $detail ) :
          if ( !empty($detail['order_excepted']) ) {
            $order_amount += ($detail['prd_fixed_qty'] * $detail['prd_change_price']);  
          }
        endforeach;

        $this->orderNumber = $getOrder['order_number'];
        $req['order'] = $getOrder;
        $req['order']['payment_id'] = $req['payment_id'];
        $req['order']['order_amount'] = $order_amount;

        $receiptCnt = $this->receipt->where(['order_id'=> $getOrder['id'], 'receipt_type'=> 1])->first();
        if ( $getOrder['complete_payment'] == 1 || !empty($receiptCnt) ) {
          if ( $receiptCnt['payment_status'] != -1 ) {
            return redirect()->to(site_url('orders').'?order_number='.$this->orderNumber); //->with('error', '이미 처리 끝');
          }
        } else {
          $currencyModel = new CurrencyModel();
          $currency = $currencyModel->where('idx', $req['checkout-currency'])->first();
          if ( !empty($currency) ) {
            var_dump($currency);
            if ( $currency['currency_code'] != $getOrder['currency_code'] ) {
              // 다름 체크......해야하나?
              $req['order']['currency_code'] = $currency['currency_code'];
            }
          } else {
            session()->setFlashdata('error', '해당하는 화폐단위가 없음');
            return $result;
          }
        } 
      } else return redirect()->to(site_url('orders'))->with('error', '주문된 상품 정보가 없음');
    } else return redirect()->to(site_url('orders'))->with('error', '해당하는 주문 내역이 없음');
    
    $getPaymethod = $this->paymentMethod->where(['id'=> $req['payment_id'], 'available' => 1])->first();
    if ( !empty($getPaymethod) ) {
      if ( $getPaymethod['payment_val'] == 'paypal' ) {
        $isPaypal = true;
      }
    } else {
      return redirect()->to(site_url('orders').'?order_number='.$this->orderNumber)->with('error', 'not vaild payment');
    }

    if ( !empty($addressReq) ) {
      if ( !empty($addressReq['address_operate']) ) {
        if ( is_null($this->addressController->addressConduct($addressReq)) ) {
          return redirect()->to(site_url('orders').'?order_number='.$this->orderNumber)->with('error', '주소 처리 오류');
        } else {
          $addressId = $this->addressController->addressConduct($addressReq);
        }
      } else $addressId = $addressReq['idx'];
      $req['order']['address_id'] = $addressId;
      unset($req['address']);
      $req['address'] = $this->address->where('idx', $addressId)->first();
    }

    $currentPackaging = $this->packaging
                            ->getAllPackagingStatus(['packaging.order_id' => $getOrder['id'], 'packaging_detail.complete' => 0])
                            ->select('packaging.*')
                            ->select('packaging_status.order_by, packaging_status.status_name
                                      , packaging_status.status_name_en, packaging_status.available
                                      , packaging_status.payment_request')
                            ->select('packaging_detail.idx AS detail_id, packaging_detail.packaging_id
                                      , packaging_detail.status_id, packaging_detail.in_progress, packaging_detail.complete')
                            ->first();
    if ( !empty($currentPackaging) ) {
      $req['currentPackaging'] = $currentPackaging;
    } else return redirect()->to(site_url('orders').'?order_number='.$this->orderNumber)->with('error', 'packaging status error');
    
    $getReceipt = $this->receipt->where(['order_id' => $req['order_id'], 'receipt_type' => 1])->first();
    if ( !empty($getReceipt) ) {
      $req['order']['receipt_id'] = $getReceipt['receipt_id'];
      if ( $getReceipt['payment_status'] == -1 ) {
        if ( $isPaypal ) {
          $invoiceResult = $this->paypalSendInvoice($getReceipt['receipt_id'], $req['order']);
          if ( $invoiceResult['code'] == 404 ) {
            $invoiceResult = $this->requestOrders($req);
          }
        }
        else {
          if ( !empty($getReceipt['payment_invoice_id'] ) ) {
            $invoiceResult = $this->requestOrders($req);
          }
        }
      } else {
        // 이미 있음
        return redirect()->to(site_url('orders').'?order_number='.$this->orderNumber);
      }
    } else {
      $invoiceResult = $this->requestOrders($req);
    }

    if ( $invoiceResult['code'] == 200 ) { // 성공
      if ( !empty($currentPackaging) ) {
        $nextStatus = $this->packagingStatus->getNextIdx($currentPackaging['status_id']);
        
        if ( empty($nextStatus) ) {
          session()->setFlashdata('error', 'next status empty');
        } else {
          if ( !$this->packagingDetail->save(['packaging_id' => $currentPackaging['packaging_id']
                                              , 'status_id' => $nextStatus->nextIdx]) ) {
            session()->setFlashdata('error', 'next status error');
          }
        }
      }
    } 
    // return redirect()->to(site_url('orders').'?order_number='.$this->orderNumber);
  }

  public function requestOrders($data = []) {
    $result['code'] = 500;
    if ( empty($data) ) return $result;

    if ( !empty($data['currentPackaging']) && !empty($data['currentPackaging']['payment_request']) ) {
      $getPaymethod = $this->paymentMethod->where(['id'=> $data['payment_id'], 'available' => 1])->first();

      if ( !empty($getPaymethod) ) {
        $subtotal = ($data['order']['order_amount'] * session()->userData['depositRate']);
        
        if ( $getPaymethod['payment_val'] == 'paypal' ) {
          $phone = explode("-", $data['order']['phone']);

          $invoiceData['invoice_number'] = $data['order']['buyerName']."_".date('ymd', strtotime($data['order']['created_at']));
          $invoiceData['buyerName'] = $data['order']['buyerName'];
          $invoiceData['email'] = session()->userData['email'];
          $invoiceData['currency_code'] = $data['order']['currency_code'];
          $invoiceData['phone_code'] = empty($phone[0]) ? $data['address']['phone_code'] : $phone[0];
          $invoiceData['phone'] = empty($phone[1]) ? $data['address']['phone'] : $phone[1];
          $invoiceData['consignee'] = $data['address']['consignee'];
          $invoiceData['streetAddr1'] = $data['address']['streetAddr1'];
          $invoiceData['streetAddr2'] = $data['address']['streetAddr2'];
          $invoiceData['zipcode'] = $data['address']['zipcode'];
          $invoiceData['country_code'] = $data['address']['country_code'];
          $invoiceData['subtotal'] = $subtotal;
          $invoiceData['depositRate'] = session()->currency['currencyFloat'];

          $makeResult = $this->paypalCraftInvoice($invoiceData, $data['order']);
        } else {  // paypal 결제가 아닐 때
          $receiptData['order_id'] = $data['order']['id'];
          $receiptData['receipt_type'] = 1; // 1차영수증
          $receiptData['rq_percent'] = session()->userData['depositRate'];
          $receiptData['rq_amount'] = $subtotal;
          $receiptData['due_amount'] = $data['order']['order_amount'] - $subtotal;
          $receiptData['display'] = 1; //1차는 무조건 보이게
          $receiptData['payment_invoice_id'] = NULL;
          $receiptData['payment_invoice_number'] = NULL;
          $receiptData['payment_url'] = NULL;

          if ( $this->receipt->save($receiptData) ) {
            if ( $this->order->save(['id'=> $data['order']['id']
                                    , 'payment_id' => $data['order']['payment_id']
                                    , 'order_amount' => $data['order']['order_amount']]) ) {
              $result['code'] = 200;
            } else {
              session()->setFlashdata('errror', 'payment info update error');
            }
          } else {
            session()->setFlashdata('error', 'receipt insert error');
          }
        }
      } else {
        session()->setFlashdata('error', 'payment method info empty');
      }
    } else {
      session()->setFlashdata('error', 'packaging error');
    }
    return $result;
  }

  public function paypalCraftInvoice($invoiceData, $order) {
    $result['code'] = 500;
    if ( empty($invoiceData) || empty($order) ) return $result;
    $makeResult = $this->paypal->makeInvoice($invoiceData);
    
    if ($makeResult['code'] == 200 || $makeResult['code'] == 201) {
      if ( !empty($order['receipt_id']) ) $receiptData['receipt_id'] = $order['receipt_id'];
      $receiptData['payment_invoice_id'] = $makeResult['payment_invoice_id'];
      $receiptData['payment_invoice_number'] = $makeResult['payment_invoice_number'];
      $receiptData['order_id'] = $order['id'];
      $receiptData['receipt_type'] =  1; // 1차영수증
      $receiptData['rq_percent'] = session()->userData['depositRate'];
      $receiptData['rq_amount'] = $invoiceData['subtotal'];
      $receiptData['due_amount'] = $order['order_amount'] - $invoiceData['subtotal'];
      $receiptData['payment_status'] = -1; // makeInvoice 에서는 -1 상태

      // order_receipt 에 insert
      if ( $this->receipt->save($receiptData) ) {
        $receipt_id = $this->receipt->getInsertID();

        $this->paypalSendInvoice($receipt_id, $order);
      } else {
        session()->setFlashdata('error', 'receipt insert error');
        return $result;
      }
    } else {
      $idx = 0;
      // $result['code'] = $makeResult['code'];
      // var_dump($makeResult['data']['data']['details']);
      // echo "<br><br>";
      if ( $makeResult['data']['data']['details'][0]['issue'] == 'DUPLICATE_INVOICE_NUMBER' ) {
        $standardInvoiceNumber = $order['buyerName']."_".date('ymd', strtotime($order['created_at']));
        $temp = str_replace($standardInvoiceNumber, '', $makeResult['data']['data']['details'][0]['value']);
        
        if ( !empty($temp) ) {
          if ( strpos($temp, '_') !== false ) {
            if ( is_numeric(str_replace('_', '', $temp)) ) {
              $idx = (str_replace('_', '', $temp) + 1);
            } else {
              // echo "is not numeric<br/>";
            }
          } else {
            $idx = 1;
          }
        } else {
          $idx = 1;
        }
        
        $invoiceData['invoice_number'] = $standardInvoiceNumber."_{$idx}";
        $this->paypalCraftInvoice($invoiceData, $order);
      }
    }
  }

  public function paypalSendInvoice($receiptId, $order) {
    $result['code'] = 500;

    if ( empty($receiptId) || empty($order) ) return $result;

    $receiptCheck = $this->receipt->where(['receipt_id' => $receiptId])->first();
    if ( !empty($receiptCheck) ) {
      if ( $receiptCheck['payment_status'] != -1 ) {
        // 이미처리 된 영수증.
        return $result;
      }
      if ( empty($receiptCheck['payment_invoice_id']) ) {
        // 페이팔이 아님.
        return $result;
      }
    } else {
      $result['error'] = "receipt into is empty";
      return $result;
    }

    $sendResult = $this->paypal->sendInvoice($receiptCheck['payment_invoice_id']);
    if ($sendResult['code'] == 200) {
      if ( $this->receipt->save(['receipt_id' => $receiptId
                            , 'payment_status' => 0
                            , 'payment_url' => $sendResult['payment_url']
                            , 'display' => 1]) ) {
        // orders table update
        if ( $this->order->save(['id'=> $order['id']
                              , 'payment_id' => $order['payment_id']
                              , 'order_amount' => $order['order_amount']]) ) {
          $result['code'] = $sendResult['code'];
        } else {
          session()->setFlashdata('errror', 'payment info update error');
          return $result;
        }
      } else {
        session()->setFlashdata('error', 'payment status update error');
        return $result;
      }
    } else {
      if ( $sendResult['code'] == 404 ) {
        $result['error'] = $sendResult['data'];
        $result['code'] = $sendResult['code'];
        return $result;
      }
      session()->setFlashdata('error', 'send Invoice error ');
      var_dump($sendResult);
      return $result;
    } 
    return $result;
  }
}