<?php
namespace App\Controllers;

use Auth\Models\UserModel;
use App\Models\BuyerAddressModel;
use App\Models\OrderModel;
use App\Models\OrderDetailModel;
use App\Models\OrdersReceiptModel;
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
  // protected $default;

  // protected $orderInfo;

  // protected $orderNumber;
  // protected $payment = 400;

  public function __construct() {
    $this->curl = service('curlrequest');
    
    $this->address = new BuyerAddressModel();
    $this->order = new OrderModel();
    $this->orderDetail = new OrderDetailModel();
    $this->receipt = new OrdersReceiptModel();
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
    $currencyModel = new CurrencyModel();
    $req = $this->request->getVar();
    $addressReq = empty($req['address']) ? [] : $req['address'];
    $returnUrl = base_url('orders');
    $order_amount = 0;
    $isPaypal = false;
    $currentPackaging = [];

    $getPaymethod = $this->paymentMethod->where(['id'=> $req['payment_id'], 'available' => 1])->first();
    if ( !empty($getPaymethod) ) {
      if ( $getPaymethod['payment_val'] == 'paypal' ) {
        $isPaypal = true;
      }
    } else {
      return redirect()->to($returnUrl)->with('error', 'not vaild payment');
    }

    $currentPackaging = $this->packaging
                            ->getAllPackagingStatus(['packaging.order_id' => $req['order_id'], 'packaging_detail.complete' => 0])
                            ->select('packaging.*')
                            ->select('packaging_status.order_by, packaging_status.status_name
                                      , packaging_status.status_name_en, packaging_status.available
                                      , packaging_status.payment_request')
                            ->select('packaging_detail.idx AS detail_id, packaging_detail.packaging_id
                                      , packaging_detail.status_id, packaging_detail.in_progress, packaging_detail.complete')
                            ->first();
    if ( empty($currentPackaging) ) {
      return redirect()->to($returnUrl)->with('error', 'packaging status error');
    } else {
      if ( empty($currentPackaging['payment_request']) ) {
        return redirect()->to($returnUrl)->with('error', '결제요청 단계가 아니라 결제 진행이 불가합니다.');
      }      
    }

    $getOrder = $this->order
                    ->select('orders.*')
                    ->select('buyers.name AS buyerName, buyers.phone AS phone')
                    ->join('buyers', 'buyers.id = orders.buyer_id')
                    ->where('orders.id', $req['order_id'])
                    ->where('orders.available', 1)
                    ->first();
    if ( !empty($getOrder) ) {
      $req['order'] = $getOrder;
      $req['order']['payment_id'] = $req['payment_id'];
      $req['isPaypal'] = $isPaypal;
      $returnUrl = $returnUrl.'?order_number='.$getOrder['order_number'];

      $currency = $currencyModel->where('idx', $req['checkout-currency'])->first();
      if ( empty($currency) ) {
        return redirect()->to($returnUrl)->with('error', '해당하는 화폐단위가 없음');
      } else {
        if ( $isPaypal ) {
          if ( !empty($currency['paypal_excluded']) ) {
            return redirect()->to($returnUrl)->with('error', lang('Lang.checkout.error.paymentExcluded', ['payment' => $getPaymethod['payment'], 'currency' => $currency['currency_code']]));
          }
        }
      }

      if ( !empty($getOrder['complete_payment']) ) return reirect()->to($returnUrl)->with('error', '이미 완료된 주문');

      if ( !empty($addressReq) ) {
        if ( !empty($addressReq['address_operate']) ) {
          if ( is_null($this->addressController->addressConduct($addressReq)) ) {
            return redirect()->to($returnUrl)->with('error', '주소 처리 오류');
          } else {
            $addressId = $this->addressController->addressConduct($addressReq);
          }
        } else $addressId = $addressReq['idx'];
        $req['order']['address_id'] = $addressId;
        unset($req['address']);
        $req['address'] = $this->address->where('idx', $addressId)->first();
      }

      $getOrderAmount = $this->orderDetail
                            ->select("ROUND(SUM(prd_fixed_qty * prd_change_price), ".session()->currency['currencyFloat'].") AS order_amount")
                            ->where(['order_id' => $getOrder['id'], 'order_excepted' => 0])
                            ->first();

      if ( !empty($getOrderAmount) ) {
        $invoiceResult['code'] = 500;
        $order_amount = $getOrderAmount['order_amount'];
        $req['order_amount'] = $getOrderAmount['order_amount'];
        $req['subtotal'] = ROUND(($order_amount * session()->userData['depositRate']), session()->currency['currencyFloat']);
        
        // if ( $req['checkout-currency'] != $getOrder['currency_rate_idx'] ) {
        //   // 화폐단위가 다르면 가격을 재계산해서 체크할 수있게 처리할것!!!  
        // }
        if ( $getOrder['fixed_amount'] < $order_amount && $getOrder['fixed_amount'] > $order_amount) {
          return redirect()->to($returnUrl)->with('error', '주문 총액이 일치하지 않음');
        }
        
        $getReceipt = $this->receipt->where(['order_id'=> $getOrder['id'], 'receipt_type'=> 1])->first();
        if ( empty($getReceipt) ) {
          $invoiceResult = $this->requestOrders($req);
        } else {
          $req['receipt'] = $getReceipt;
          $req['order']['receipt_id'] = $getReceipt['receipt_id'];
          if ( $getReceipt['payment_status'] == -1 ) {
            if ( $isPaypal ) {
              $invoiceResult = $this->paypalSendInvoice($getReceipt, $req['order']);
              if ( $invoiceResult['code'] == 404 ) {
                // var_dump($invoiceResult);
                // return;
                $invoiceResult = $this->requestOrders($req);
              }
            } else {
              if ( !empty($getReceipt['payment_invoice_id'] ) ) {
                $invoiceResult = $this->requestOrders($req);
              }
            }
          } else {
            return redirect()->to($returnUrl)->with('error', 'already updated');
          }
        }
      } else return redirect()->to(site_url('orders'))->with('error', '주문된 상품 정보가 없음');
    } else return redirect()->to(site_url('orders'))->with('error', '해당하는 주문 내역이 없음');

    
    if ( !empty($invoiceResult) ) {
      if ( $invoiceResult['code'] == 200 ) { // 성공
        if ( $this->order->save(['id'=> $req['order']['id']
                                , 'payment_id' => $req['order']['payment_id']
                                , 'order_amount' => $order_amount
                                , 'currency_code' => $req['currency_code']
                                , 'currency_rate_idx' => $req['checkout-currency']]) ) {
          if ( !empty($currentPackaging) ) {
            $getPackagingStatus = $this->packagingStatus
                                      ->where("order_by", "(  SELECT order_by FROM packaging_status 
                                                              WHERE idx = {$currentPackaging['status_id']} AND available = 1  )")
                                      ->orderBy('order_by ASC')
                                      ->first();
            if ( !empty($getPackagingStatus) ) {
              if ( empty($currentPackaging['complete']) ) {
                if ( !$this->packagingDetail->save(['idx' => $currentPackaging['detail_id'], 'complete' => 1]) ) {
                  return redirect()->to($returnUrl)->with('error', 'packaging status error');
                }
              }

              if ( !$this->packagingDetail->save(['packaging_id'   => $req['packaging_id']
                                                , 'status_id'     => $getPackagingStatus['idx']]) ) {
                return redirect()->to($returnUrl)->with('error', 'packaging next status error');
              }
            }
          }
        } else {
          session()->setFlashdata('error', 'order update error');
        }
      }
    } else {
      session()->setFlashdata('error', 'checkout error');
    }
    return redirect()->to($returnUrl);
  }

  public function requestOrders($data = []) {
    $result['code'] = 500;
    
    if ( empty($data) ) return $result;
    if ( $data['order_amount'] <= 0 ) return $result;

    if ( $data['isPaypal'] ) {
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
      $invoiceData['subtotal'] = $data['subtotal'];
      $invoiceData['depositRate'] = session()->currency['currencyFloat'];
    
      $makeResult = $this->paypalCraftInvoice($invoiceData, $data['order']);
    } else {  // paypal 결제가 아닐 때
      $receiptData['order_id'] = $data['order']['id'];
      $receiptData['receipt_type'] = 1; // 1차영수증
      $receiptData['rq_percent'] = session()->userData['depositRate'];
      $receiptData['rq_amount'] = $data['subtotal'];
      $receiptData['due_amount'] = $data['order_amount'] - $data['subtotal'];
      $receiptData['display'] = 1; //1차는 무조건 보이게
      $receiptData['payment_invoice_id'] = NULL;
      $receiptData['payment_invoice_number'] = NULL;
      $receiptData['payment_url'] = NULL;
      $receiptData['payment_status'] = 0;
      
      if ( !$this->receipt->save($receiptData) ) {
        // var_dump($this->receipt->getErrorMessage());
        session()->setFlashdata('error', 'receipt insert error');
      } else {
        $result['code'] = 200;
      }
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
          if ( strpos($temp, '-') !== false ) {
            if ( is_numeric(str_replace('-', '', $temp)) ) {
              $idx = (str_replace('-', '', $temp) + 1);
            } else $idx = 1;
          } else $idx = 1;
        } else $idx = 1;
        
        $invoiceData['invoice_number'] = $standardInvoiceNumber."-{$idx}";
        $this->paypalCraftInvoice($invoiceData, $order);
      }
    }
  }

  public function paypalSendInvoice($receipt, $order) {
    $result['code'] = 500;
    $sendResult = $this->paypal->sendInvoice($receipt['payment_invoice_id']);
    if ($sendResult['code'] == 200) {
      if ( $this->receipt->save(['receipt_id' => $receipt['receipt_id']
                            , 'payment_status' => 0
                            , 'payment_url' => $sendResult['payment_url']
                            , 'display' => 1]) ) {
        $result['code'] = $sendResult['code'];
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