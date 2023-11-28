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
use App\Models\BrandModel;

use App\Controllers\Orders;

class Home extends BaseController {
  protected $data;
  protected $path = "../www/img/brand_logo";
  
  public function __construct() {
    $this->order = new OrderModel();
    $this->notice = new NoticeModel();
    $this->brand = new BrandModel();

    $this->ordersController = new Orders();

    $this->data['header'] = [ 'js'  => ['https://cdn.jsdelivr.net/npm/chart.js'
                                       , '/main.js']
                            , 'css' => ['https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css', '/main.css']];
  }

  public function index() {
    $this->data['isIndex'] = true;
    // if ( session()->isLoggedIn ) {
    //   return call_user_func_array(array($this, 'main'), []);
    // }
    array_push($this->data['header']['css'], '/home.css');
    $path = $this->path."/other_brand_logo/";

    $myfiles = explode(',', (implode(',' , array_diff(scandir($path), array('.', '..')))));
    $brands = $this->brand
                ->select("brand_name, LOWER(REGEXP_REPLACE(brand_name, '[^[:alnum:]]+', '')) AS name, sort_main")
                ->where(['own_brand' => 0, 'sort_main != ' => NULL])
                ->orderBy('sort_main', 'ASC')
                ->findAll();
    $topBrands = array();
    
    foreach( $brands AS $key => $brand ) :
      foreach( $myfiles AS $myfile ) :
        if ( strtolower(preg_replace('/.jpg|.png|.gif|[^[:alnum:]]+/', '', $myfile)) == $brand['name']) :
          if ( !is_null($brand['sort_main']) ) array_push($topBrands, array_merge($brand, ['logo' => $myfile]) );
        endif;
      endforeach;
    endforeach;
    // var_dump($topBrands);
    $this->data['brandList'] = $topBrands;
    $this->data['policy'] = $this->notice->board(['board.idx' => 2])->first();
    return $this->basicLayout('dash/index', $this->data);
  }

  // public function sampleIndex() {
  //   $path = $path = $this->path."/top20";

  //   $myfiles = array_diff(scandir($path), array('.', '..'));
  //   $this->data['brandList'] = $myfiles;

  //   $this->data['policy'] = $this->notice->board(['board.idx' => 2])->first();
  //   return $this->basicLayout('dash/sampleIndex', $this->data);
  // }

  public function subscribe() {
    $parameter = $this->request->getVar();
    
    if($this->request->isAJAX()){
      if(!empty($parameter)){
        $params = ['type_idx' => 3 , 
                  'title' => $parameter['full-name'] ,
                  'contents' => $parameter['email-address'] ,
                  'display' => 0];
        if($this->notice->save($params)){
          return json_encode(['code' => 200, 'msg' => "Your subscription has been successfully completed."]);
        };
      }
    }
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

  public function manual() {
    array_push($this->data['header']['css'], '/manual.css');
    return $this->basicLayout('manual/main', $this->data);
  }
}
