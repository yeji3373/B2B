<?php
namespace App\Controllers;

// use App\Models\ProductModel;
// use App\Models\BrandModel;
// use App\Models\BuyerModel;
// use App\Models\CurrencyModel;
// use App\Models\CartModel;
// use App\Models\MarginModel;
// use App\Models\RegionModel;
// use App\Models\CountryModel;
// use App\Models\BuyerAddressModel;
// use App\Models\PaymentMethodModel;
// use App\Models\ProductPriceModel;
// use App\Models\StockDetailModel;
use App\Models\OrderModel;
use App\Models\NoticeModel;

class Home extends BaseController {
  protected $data;
  
  public function __construct() {
    $this->order = new OrderModel();
    $this->notice = new NoticeModel();

    $this->data['header'] = [ 'js' => ['https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js']
                            , 'css' => ['https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css']];
  }

  public function index() {
    return $this->basicLayout('dash/index');
  }

  // public function dashboard() {
  //   // echo $this->request->getLocale();
  //   $this->data = $this->dashboardData();
  //   return $this->basicLayout('dash/main', $this->data);
  // }

  // public function dashboardData() {
  //   $this->data['order'] = $this->getOrderStatus();
  //   $this->data['notices'] = $this->notice->limit(5)->findAll();

  //   return $this->data;
  // }

  // public function getOrderStatus() {
  //   $orders = $this->order
  //                   ->select('created_at, DATE_FORMAT(created_at, \'%Y-%m-%d\') AS created_at_co, AVG(order_amount) AS order_amount, SUM(subtotal_amount) AS subtotal_amount')
  //                   ->where('buyer_id', session()->userData['buyerId'])
  //                   ->where('created_at BETWEEN DATE_ADD(NOW(), INTERVAL -1 WEEK) AND NOW()')
  //                   ->groupBy('created_at_co')
  //                   ->find();
  //   return $orders;
  // }
}
