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

use App\Controllers\Orders;

class Home extends BaseController {
  protected $data;
  
  public function __construct() {
    $this->order = new OrderModel();
    $this->notice = new NoticeModel();

    $this->ordersController = new Orders();

    $this->data['header'] = [ 'js' => ['https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js'
                                      , 'https://cdn.jsdelivr.net/npm/chart.js']
                            , 'css' => ['https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css', '/main.css']];
  }

  public function index() {
    // if ( session()->isLoggedIn ) {
    //   return call_user_func_array(array($this, 'main'), []);
    // }
    $path = "../www/img/brand_logo/top20";

    $myfiles = array_diff(scandir($path), array('.', '..'));
    $this->data['brandList'] = $myfiles;

    $this->data['policy'] = $this->notice->board(['board.idx' => 2])->first();
    return $this->basicLayout('dash/index', $this->data);
  }

  public function sampleIndex() {
    $path = "../www/img/brand_logo/top20";

    $myfiles = array_diff(scandir($path), array('.', '..'));
    $this->data['brandList'] = $myfiles;

    $this->data['policy'] = $this->notice->board(['board.idx' => 2])->first();
    return $this->basicLayout('dash/sampleIndex', $this->data);
  }

  public function main() {
    if ( session()->isLoggedIn === false ) {
      if ( session()->isLoggedIn ) {
        return call_user_func_array(array($this, 'index'), []);
      }
    }
    $this->data['notices'] = $this->notice->board(['board_type.available' => 1
                                                , 'board.type_idx' => 1
                                                , 'board.display' => 1])
                                          ->orderBy('board.fixed DESC, board.sort ASC, board.idx ASC')
                                          ->findAll(8);
    $this->data['qna'] = $this->notice->board(['board_type.available' => 1
                                              , 'board.type_idx' => 2
                                              , 'board.display' => 1])
                                      ->orderBy('board.fixed DESC, board.sort ASC, board.idx ASC')
                                      ->findAll(8);
    $this->data['statistics'] = $this->ordersController->ordersStatistics();
    return $this->basicLayout('dash/main', $this->data);
  }
}
