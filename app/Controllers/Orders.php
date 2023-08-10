<?php
namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderDetailModel;
use App\Models\OrdersReceiptModel;
use App\Models\DeliveryModel;
use App\Models\BuyerModel;
use App\Models\PackagingModel;
use App\Models\PackagingStatusModel;
use App\Models\PaymentMethodModel;
use App\Models\RequirementRequestModel;

use Auth\Models\UserModel;

use Paypal\Controllers\PaypalController;

use Status\Config\Status;

use CodeIgniter\I18n\Time;

class Orders extends BaseController {
  protected $data;

  protected $orderId;
  protected $isPaypal = false;

  public function __construct() {
    helper('date');
    $this->order = new OrderModel();
    $this->oDetail = new OrderDetailModel();
    $this->receipt = new OrdersReceiptModel();
    $this->delivery = new DeliveryModel();
    $this->buyer = new BuyerModel();
    $this->packaging = new PackagingModel();
    $this->packagingStatus = new PackagingStatusModel();
    $this->paymentMethodModel = new PaymentMethodModel();
    $this->requirement = new RequirementRequestModel();
    $this->status = config('Status');

    $this->paypalController = new PaypalController();

    $this->data['header'] = ['css' => ['/orders.css', '/taggroup.css'],
                              'js' => ['https://cdn.jsdelivr.net/npm/chart.js'
                                      , '/orders.js']];

  }

  public function index() {
    
    $this->data['orders'] = $this->getOrderList();

    // if ( empty($this->request->getGet('order_number')) || empty($this->data['order']['order_number'])) {
    if ( empty($this->request->getGet('order_number')) ) {
      $this->data['statistics'] = $this->ordersStatistics();
      $this->data['orderBrand'] = $this->order->select('brand.brand_name, COUNT(brand.brand_id) AS cnt')
                                    ->join('orders_detail', 'orders_detail.order_id = orders.id')
                                    ->join('product', 'product.id = orders_detail.prd_id')
                                    ->join('brand', 'brand.brand_id = product.brand_id')
                                    ->where('orders.buyer_id', session()->userData['buyerId'])
                                    ->where('orders.available', 1)
                                    ->groupby('brand.brand_id')
                                    ->findAll(10);
    } else {
      $this->data['order'] = $this->getOrder();
      $this->data['paymentMethod'] = $this->getPaymentMethod();
      // $this->data['receipts'] = $this->getOrderReceipts();
      $this->data['orderDetails'] = $this->getOrderDetails();
      $this->data['orderRequirement'] = $this->getRequirement();
      // $this->data['shippinCost'] = $this->getTotalShippingCost();
      // $this->data['buyer'] = $this->getBuyer();
      // $this->data['packaging'] = $this->packaging->where('order_id', $this->orderId)->first();
      $this->data['packagingStatus'] = $this->packagingStatus
                                            ->select('packaging_status.*')
                                            ->select('packaging.complete')
                                            ->select('packaging.in_progress')
                                            ->join("( SELECT packaging.idx, packaging.order_id
                                                            , packaging_detail.packaging_id, packaging_detail.status_id
                                                            , packaging_detail.in_progress, packaging_detail.complete
                                                      FROM packaging
                                                      LEFT OUTER JOIN packaging_detail ON packaging.idx = packaging_detail.packaging_id
                                                      WHERE packaging.order_id = {$this->orderId}) AS packaging"
                                                    , "packaging.status_id = packaging_status.idx", "left outer")
                                            ->where(['packaging_status.available' => 1
                                                  , 'packaging_status.display' => 1])
                                            ->orderBy('packaging_status.order_by')
                                            ->findAll();
    
    }

    $this->basicLayout('orders/List', $this->data);
  }

  // public function apiOrderDetail() {
  //   $this->getOrderDetail()->->where('order_number', $this->request->getGet('order_number'))->first();
  // }

  public function ordersStatistics() {
    return $this->order->orderStatistics()->getResultArray();
    
  }

  public function getOrderList() {
    $page = null;

    if ( !empty($this->request->getPost('order_number')) ) {
      $this->order->like('order_number', $this->request->getVar('order_number'), 'both');
    }

    $orders = $this->order
                ->where(['buyer_id'=> session()->userData['buyerId']
                        , 'available' => 1])
                ->orderBy('created_at DESC')
                ->paginate(15, 'default', $page);

    // $this->data['ordersListPage'] = 'ordersListPage';
    $this->data['ordersPager'] = $this->order->pager;
    
    if ( $this->request->isAJAX() ) {
      return json_encode($orders);
    }
    return $orders;
  }

  public function getPaymentMethod() {
    $where = [];
    if ( !empty($this->getOrder()) ) {
      $where['id'] = $this->getOrder()['payment_id'];
    }
    $paymentMethod = $this->paymentMethodModel->where($where)->first();
    return $paymentMethod;
  }

  public function getOrder() {
    $order = $this->order
                ->select('orders.*')
                ->select('currency.currency_sign, currency.currency_float')
                ->join('currency', 'currency.currency_code = orders.currency_code')
                ->where(['order_number' => $this->request->getVar('order_number')
                        ,'buyer_id' => session()->userData['buyerId']])
                ->first();
    // $order = $this->order
    //                 ->select('orders.id, orders.order_number, orders.order_amount, orders.discount_amount')
    //                 ->select('orders.subtotal_amount, orders.order_amount, orders.discount_amount')
    //                 ->select('orders.currency_code, orders.taxation')
    //                 ->select('orders.payment_id, orders.address_id')
    //                 ->select('DATE_FORMAT(orders.created_at, "%Y-%m-%d") AS orderDate')
    //                 ->select('payment_method.payment')
    //                 ->select('payment_method.payment_info, payment_method.bank_name, payment_method.account_no, payment_method.swift_code')
    //                 ->select('payment_method.show_info, payment_method.has_payment_url, payment_method.payment_desc')
    //                 ->select('buyers_address.idx AS address_id, buyers_address.consignee')
    //                 ->select('buyers_address.region, buyers_address.country_code')
    //                 ->select('buyers_address.streetAddr1, buyers_address.streetAddr2')
    //                 ->select('buyers_address.city, buyers_address.zipcode')
    //                 ->select('buyers_address.phone_code, buyers_address.phone')
    //                 ->select('buyers_address.deleted_at')
    //                 ->select('currency.currency_sign, currency.currency_float')
    //                 ->select('delivery.delivery_code, SUM(delivery_price) AS delivery_price')
    //                 ->join('payment_method', 'payment_method.id = orders.payment_id')
    //                 ->join('buyers_address', 'buyers_address.idx = orders.address_id')
    //                 ->join('currency', 'currency.currency_code = orders.currency_code')
    //                 ->join('delivery', 'delivery.order_id = orders.id')
    //                 ->where('orders.order_number', $this->request->getVar('order_number'))
    //                 ->where('orders.buyer_id', session()->userData['buyerId'])
    //                 ->where('payment_method.available', 1)
    //                 ->first();
    
    // echo $this->order->getLastQuery();

    if ( !empty($order) ) {
    //   // if ( $order['payment_id'] == 1) $this->isPaypal = TRUE;
      $this->orderId = $order['id'];
    }

    if ( $this->request->isAJAX() ) {
      return view('/orders/OrderInfo', ['order' => $order]);
    }
    return $order;
  }
  
  public function getOrderDetails() {
    $orderDetails = $this->oDetail
                    ->select('orders_detail.id AS detail_id')
                    ->select('ROUND(IF(orders_detail.prd_price_changed = 1, orders_detail.prd_change_price, orders_detail.prd_price), 2) AS prd_price')
                    ->select('CAST(IF(orders_detail.prd_qty_changed = 1, orders_detail.prd_change_qty, orders_detail.prd_order_qty) AS DOUBLE) AS prd_order_qty')
                    ->select('orders_detail.prd_discount')
                    ->select('orders_detail.order_excepted')
                    // ->select('orders_detail.prd_taxation')
                    ->select('product.brand_id, product.barcode, product.productCode')
                    ->select('product.img_url, product.name_en, product.type_en')
                    ->select('product.sample, product.box, product.in_the_box, product.container')
                    ->select('product.spec, product.spec_detail, product.spec_pcs')
                    ->select('product.package, product.package_detail')
                    ->select('product.shipping_weight')
                    ->select('brand.brand_name')
                    ->select('product_price.retail_price, product_price.taxation')
                    ->select('supply_price.price')
                    ->select('currency.currency_sign, currency.currency_float')
                    ->select('margin.margin_level, margin.margin_section, margin_rate.margin_rate')
                    ->select('IFNULL(currency_rate.exchange_rate, 1) AS exchange_rate')
                    // ->select('requirement_request.requirement_detail')
                    // ->join('orders', 'orders.id = orders_detail.order_id')
                    ->join('product', 'product.id = orders_detail.prd_id')
                    ->join('brand', 'brand.brand_id = product.brand_id')
                    ->join('product_price', 'product_price.idx = orders_detail.prd_price_id')
                    ->join('supply_price', 'supply_price.product_price_idx = product_price.idx', 'left outer')
                    ->join('orders', 'orders.id = orders_detail.order_id', 'left outer')
                    ->join('currency', 'currency.currency_code = orders.currency_code')
                    ->join('margin_rate', 'margin_rate.idx = orders_detail.margin_rate_id')
                    ->join('margin', 'margin.idx = margin_rate.margin_idx')
                    ->join('currency_rate', 'currency_rate.cRate_idx = orders.calc_currency_rate_id', 'left outer')
                    // ->join('requirement_request', 'requirement_request.order_detail_id = orders_detail.id AND orders.id = requirement_request.order_id')
                    ->where('orders_detail.order_id', $this->orderId)
                    ->where(['product.discontinued' => 0, 'product.display' => 1])
                    ->where('product_price.available', 1)
                    ->where('supply_price.margin_level = margin.margin_level')
                    ->findAll();
                    // echo $this->oDetail->getLastQuery();
    return $orderDetails;
  }
  //요구사항
  public function getRequirement() {
    $orderRequirement = $this->requirement
                        ->select('requirement_request.requirement_detail')
                        ->select('requirement_request.order_detail_id')
                        ->select('requirement_request.order_id')
                        ->select('requirement_request.requirement_reply')
                        ->select('requirement.requirement_en')
                        ->join('requirement','requirement.idx = requirement_request.requirement_id')
                        ->where("requirement_request.order_id = {$this->orderId}")
                        ->orderby('requirement_request.order_detail_id')
                        ->findAll();
                      echo $this->requirement->getLastQuery();
    return $orderRequirement;
  }

  public function getOrderReceipts() {
    $addWhere = [];
    // if ( !is_null($this->request->getVar('receipt_id')) ) {
    //   $addWhere['orders_receipt.receipt_id'] = $this->request->getVar('receipt_id');
    // }
    if ( !is_null($this->request->getVar('receipt_type')) ) {
      $addWhere['orders_receipt.receipt_type'] = $this->request->getVar('receipt_type');
    }
    
    $receipts = $this->receipt
                // ->select("orders_receipt.*, CONVERT(IFNULL(delivery.delivery_price, 0), FLOAT) AS delivery_price")
                ->select("orders_receipt.*, CAST(IFNULL(delivery.delivery_price, 0) AS DOUBLE) AS delivery_price")
                ->join("delivery", "delivery.id = orders_receipt.delivery_id", "left outer")
                ->where(['orders_receipt.order_id'=> $this->orderId
                        , 'orders_receipt.display' => 1])
                ->where($addWhere)
                ->findAll();
    // echo $this->receipt->getLastQuery();

    if ( !empty($receipts) ) {
      foreach ($receipts as $i => $receipt) {
        if ( $this->isPaypal ) {
          if ( $receipt['payment_invoice_id'] != NULL && $receipt['payment_status'] == 0 ) {
            $paypal_detail = $this->paypalController->showInvoiceDetail($receipt['payment_invoice_id']);
             
            if ( $receipt['payment_status'] == 0 ) {
              if ( $paypal_detail['data']['due_amount']['value'] == 0 ) {
                $this->receipt
                  ->where(['receipt_id' => $receipt['receipt_id']])
                  ->set(['payment_status' => 100])
                  ->update();
              }

              if ( strtoupper($paypal_detail['data']['status']) == 'CANCELLED' ) {
                echo "<br/><Br/>cencelled<br/><Br/>";
                $this->receipt
                  ->where(['receipt_id' => $receipt['receipt_id']])
                  ->set(['due_amount' => 0, 'payment_status' => -100])
                  ->update();
              }
            } else {
              if ( $paypal_detail['data']['due_amount']['value'] < $receipt['due_amount'] ) {
                $this->receipt
                    ->where(['receipt_id' => $receipt['receipt_id']])
                    ->set(['due_amount' => $paypal_detail['data']['due_amount']['value']])
                    ->update();
              }
            }
          }
        }
        // $receipts[$i]['payment_status_msg'] = self::$paymentStatus[$receipt['payment_status']];
        $receipts[$i]['payment_status_msg'] = $this->status->paymentStatus($receipt['payment_status']);
      }
    }
    return $receipts;
  }

  public function getOrderReceipt() {
    $addWhere = [];
    $where_1 = '';
    if ( !is_null($this->request->getVar('receipt_type')) ) {
      $addWhere = ['orders_receipt.receipt_type <=' => $this->request->getVar('receipt_type')];
      // $addWhere = ['orders_receipt.receipt_type' => $this->request->getVar('receipt_type')];
      // $where_1 = " AND orders_receipt.receipt_type <= {$this->request->getVar('receipt_type')}";
    }

    $receipt = $this->receipt
                      ->select('orders.order_amount')
                      ->select('orders_receipt.receipt_id, orders_receipt.order_id, orders_receipt.receipt_type')
                      ->select('orders_receipt.payment_status, orders_receipt.payment_date')
                      ->select('orders_receipt.refund_date, orders_receipt.payment_invoice_id')
                      ->select('orders_receipt.payment_refund_id, orders_receipt.payment_url')
                      ->select('orders_receipt.rq_percent')
                      ->select("orders_recipt_sum.rq_amount")
                      ->select('(orders.order_amount - orders_recipt_sum.rq_amount) AS due_amount')
                      ->select('orders_receipt.delivery_id, orders_receipt.display')
                      ->select('orders_receipt.created_at')
                      // ->select("SUM(CAST(IFNULL(delivery.delivery_price, 0) AS DOUBLE)) OVER() AS delivery_price")
                      ->select("SUM(CAST(IFNULL(delivery.delivery_price, 0) AS DOUBLE)) AS delivery_price")
                      ->join("orders", "orders.id = orders_receipt.order_id")
                      // ->join("( SELECT order_id, SUM(rq_amount) AS rq_amount 
                      //           FROM orders_receipt 
                      //           WHERE order_id = {$this->orderId} 
                      //             AND payment_status = 100
                      //             $where_1
                      //         ) AS orders_recipt_sum"
                      //       , "orders_recipt_sum.order_id = orders_receipt.order_id", "left outer")
                      ->join("( SELECT order_id, SUM(rq_amount) AS rq_amount 
                            FROM orders_receipt 
                            WHERE order_id = {$this->orderId} 
                              AND payment_status = 100
                          ) AS orders_recipt_sum"
                        , "orders_recipt_sum.order_id = orders_receipt.order_id", "left outer")                            
                      // ->join("delivery", "delivery.id = orders_receipt.delivery_id", "left outer")
                      ->join("(SELECT id, order_id, SUM(delivery_price) AS delivery_price FROM delivery WHERE order_id = {$this->orderId} AND delivery_code = 100) AS delivery"
                            , "delivery.order_id = orders_receipt.order_id AND delivery.id = orders_receipt.delivery_id", "left outer")
                      ->where(['orders_receipt.order_id' => $this->orderId
                              , 'orders_receipt.display' => 1])
                      ->where($addWhere)
                      ->orderBy('orders_receipt.receipt_id DESC')
                      ->first();
    // echo "<br/>";
    // echo "<br/>";
    // echo $this->receipt->getLastQuery();
    // echo "<br/>";
    // echo "<br/>";
    // // print_r($receipt);
    return $receipt;
  }

  public function getTotalShippingCost() {
    $delivery = $this->delivery
                  ->select('*')
                  // ->select('SUM(delivery_price) OVER() AS shipping_total_cost')
                  ->select('SUM(delivery_price) AS shipping_total_cost')
                  ->where(['order_id' => $this->orderId
                          , 'delivery_code >' => 0])
                  ->first();
    // $deliveries = $this->delivery->where(['order_id' => $this->orderId])->findAll();

    // if ( !empty($deliveries) ) :
    //   foreach ( $deliveries as $i => $delivery ) :
    //     // if ($delivery['delivery_code'] < 100 ) {
    //       // $deliveries[$i]['deliveryCode'] = self::$deliveryCode[$delivery['delivery_code']];
    //       $deliveries[$i]['deliveryCode'] = $this->status->deliveryCode[$delivery['delivery_code']];
    //     // }
    //   endforeach;
    // endif;

    return $delivery;
  }

  public function getBuyer() {
    return $this->buyer
                ->select('buyers.*')
                ->select('manager.id AS manager_id, manager.name AS manager_name')
                ->join('manager', 'manager.idx = buyers.manager_id')
                ->where(['buyers.id'=> session()->userData['buyerId'], 'buyers.available' => 1])
                ->first();
  }

  public function getOrderData() {
    // print_r($this->request->getVar());
    // $this->orderId = $this->request->getPost('order_id');
    $data['order'] = $this->getOrder();
    // echo 'orderId '.$this->orderId.'<br/><br/>';
    $data['orderDetails'] = $this->getOrderDetails();
    // $data['orderRequirement'] = $this->getRequirement();
    // $data['receipts'] = $this->getOrderReceipts($this->request->getPost('receiptId'));
    $data['receipt'] = $this->getOrderReceipt();
    $data['buyer'] = $this->getBuyer();

    if ( $this->request->isAJAX() ) {
      // return json_encode(['Code' => 200, 'Msg' => $data]);
      // print_r($data);
      return view('/orders/includes/ProformaInvoice', $data);
    }
    return $data;
  }

  public function htmlToPDF() {
    helper('pi');
    $options = new \Dompdf\Options();
    $options->setIsPhpEnabled(true);
    $options->setIsRemoteEnabled(true);
    $options->setDefaultPaperSize("A4");
    $options->setDefaultPaperOrientation("portrait"); // 'portrait' or 'landscape'
    $options->setFontDir(FCPATH . '/fonts');
    $options->setFontCache(FCPATH . '/fonts');
    // $options->setDefaultFont('NanumGothic');

    $dompdf = new \Dompdf\Dompdf($options);

    $data = $this->getOrderData();
    $this->orderId = $data['order']['id'];
    $data['sign'] = $this->getImage(FCPATH.'img/jmh_sign.png');
    
    $html = makeHtml().
            view('orders/includes/ProformaInvoice', $data).
            "</body></html>";
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream($data['buyer']['name'].'_'.$data['order']['order_number'].'.pdf',
             ['compress' => true, 'Attatchment' => true ]);
  }

  public function getImage(String $url) {
    // $img;
    $path = $url;
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);

    $img = 'data:image/'.$type.';base64,'.base64_encode($data);

    return $img;
  }
}