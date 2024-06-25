<?php
namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\OrderModel;
use App\Models\BuyerModel;
use App\Models\CurrencyModel;
use App\Models\CartModel;
use App\Models\CartStatusModel;
use App\Models\MarginModel;
use App\Models\RegionModel;
use App\Models\CountryModel;
use App\Models\BuyerAddressModel;
use App\Models\PaymentMethodModel;
use App\Models\ProductPriceModel;
use App\Models\SupplyPriceModel;
use App\Models\ProductSpqModel;
use App\Models\StockDetailModel;
use App\Models\BuyerCurrencyModel;

use App\Controllers\CartController;

use Auth\Models\UserModel;

use CodeIgniter\I18n\Time;

class Order extends BaseController
{
  protected $brands;
  protected $buyers;
  // protected $currency;

  protected $data;
  protected $searchData;

  public function __construct() {
    helper(['brand', 'auth', 'product']);

    current_user();
    
    $this->product = new ProductModel();
    $this->buyers = new BuyerModel();
    $this->currency = new CurrencyModel();
    $this->margin = new MarginModel();
    $this->users = new UserModel();
    $this->productPrice = new ProductPriceModel();
    $this->productSupplyPrice = new SupplyPriceModel();
    $this->productSpq = new ProductSpqModel();
    $this->address = new BuyerAddressModel();
    $this->stocks = new StockDetailModel();
    $this->buyerCurrency = new BuyerCurrencyModel();
    $this->cart = new CartModel();
    $this->cartStatus = new CartStatusModel();
    
    $this->CartController = new CartController();

    $this->data['header'] = ['css' => ['/address.css', '/order.css'
                                      , '/inventory.css', '/stock.css'],
                              'js' => ['/address.js', '/product.js', '/inventory.js', '/stock.js']];
  }

  public function __output() {}

  public function index() {
    $this->CartController->initialCartList(); // 카트 초기화
    $this->data['brands'] = brands();
    // $this->cartList();
    // $this->productList();
    // // $this->cartTotalPrice();
    $this->basicLayout('product/list', $this->data);
  }

  public function productList() {
    helper('product_item');
    $params = $this->request->getVar();
    return product_item($params['products']);
  }
  
  public function cartTotalPrice() {
    $totalPrice = $this->CartController->getCartTotalPrice();

    if ( !empty($totalPrice) ) {
      if ( $totalPrice['order_price_total'] >= $this->CartController->basedMinimumOrderVal ) {
        $totalPrice['disable'] = false;
        $totalPrice['msg'] = '';
      } else {
        $totalPrice['disable'] = true;
        $totalPrice['msg'] = '최소 결제금액은 '.$this->CartController->basedMinimumOrderVal.'이상';
      }

      // $this->applyDiscountCart(); // 상황 지켜본후 다시 활성화
    } else {
      $totalPrice['order_subTotal'] = 0;
      $totalPrice['order_price_total'] = 0;
      $totalPrice['order_discount_total'] = 0;

      $totalPrice['disable'] = true;
    }

    // print_r($totalPrice);

    if ( $this->request->isAJAX() ) {
      return json_encode($totalPrice);
    } else return $this->data['cartSubTotal'] = $totalPrice;
  }
  
  // public function applyDiscountCart(Int $totalPrice = null) {
  //   if ( $totalPrice == NULL ) $invoiceTotal = $this->CartController->getCartTotalPrice();
  //   else $invoiceTotal['order_price_total'] = $totalPrice;

  //   if ( !empty($invoiceTotal['order_price_total']) ) {
  //     $this->cart->where('buyer_id', session()->userData['buyerId']);
  //     if ( $invoiceTotal['order_price_total'] >= $this->CartController->basedDiscountVal ) {
  //       $carts = $this->cart
  //                     // ->where('user_id', $userId)
  //                     ->where('apply_discount', 0)
  //                     ->where('prd_section !=', 1)
  //                     ->findAll();
  //       if ( !empty($carts) ) {
  //         $this->cart->where(['buyer_id'=> session()->userData['buyerId'],
  //                               'apply_discount'=> 0,
  //                               'prd_section !=' => 1])
  //                       ->set(['apply_discount'=> 1])->update();

  //         // if ( $this->cart->affectedRows() ) {
  //         //   $session()->set('applyDiscount', true);
  //         // }
  //       }
  //     } else {
  //       // $carts = $this->cart->where(['user_id' => $userId, 'prd_section !=' => 'dis_section', 'apply_discount' => 1])->findAll();
  //       $carts = $this->cart->where(['prd_section !=' => 'dis_section', 'apply_discount' => 1])->findAll();

  //       if ( !empty($carts) ) {
  //         $this->cart
  //               ->where(['buyer_id' => session()->userData['buyerId'], 
  //                       'prd_section !=' => 'dis_section', 
  //                       'apply_discount' => 1])
  //               ->set('apply_discount', 0)
  //               ->update();
  //       }
  //     }
  //   }
  // }

  public function orderForm() {
    $country = new CountryModel();
    $payments = new PaymentMethodModel();
    $orderModel = new OrderModel();
    $where = $this->request->getVar();

    $orderWhere = []; 
    if ( isset($where['order']) && !empty($where['order']) ) {
      $orderWhere = $where['order'];
    } else {
      return ['error' => $orderWhere];
    }
    // if ( !empty($this->request->getPost('margin_level')) ) {
    //   $where['supply_price.margin_level'] = $this->request->getPost('margin_level');
    // } else $where['supply_price.margin_level'] = 2;

    // if ( $this->CartController->checkMinimumAmount() === false ) {
    //   if ( $this->request->isAJAX()) {
    //     return json_encode(['error' => lang('Lang.orderMinCheck', [$this->CartController->basedMinimumOrderVal])]);
    //   } 
    //   return redirect()->to(site_url('/order'))->with('error', lang('Lang.orderMinCheck', [$this->CartController->basedMinimumOrderVal]));
    // }    

    $this->data['prevAddrList'] = $this->address->where('buyer_id', session()->userData['buyerId'])->findAll();
    $this->data['regions'] = $country->findAll();
    $this->data['buyer'] = $this->getBuyerInfo();
    $this->data['payments'] = $payments->where('available', 1)->find();
    $this->data['itus'] = $this->getItus()->findAll();
    $this->data['currencies'] = $this->currency->currencyJoin()->where('currency.default_currency', 1)->find();
    $this->data['orderDetails'] = $this->product
                                      ->productOrderJoin(
                                        ['orders_detail.order_id'=> $where['order']['id']
                                        , 'orders_detail.order_excepted' => 0])
                                      ->findAll();
    $this->data['subTotal'] = $orderModel->where($orderWhere)->first();
    // $this->cartList();

    // print_r($this->data);
    
    if ( $this->request->isAJAX() ) { 
      return view('order/Checkout', $this->data);
    }
    $this->basicLayout('order/Checkout', $this->data);
  }

  public function getItus() {
    $country = new CountryModel();

    return $country->select('id, country_no')->orderBy('country_no ASC', 'country_no_sub ASC')->groupBy('country_no');
  }

  public function checkoutTotalPrice() {
    $data = $this->request->getVar();
    
    if ( $data['exchange'] == 1 ) {
      $currency = $this->currency->currencyJoin()->where('cRate_idx', $data['rId'])->first();
      $exchange_rate = $currency['exchange_rate'];
    } else $exchange_rate = 1;
    unset($data['exchange']);
    unset($data['rId']);

    $totalPrice = $this->CartController->getCartTotalPrice($data, $exchange_rate);

    if ( !empty($currency) && !empty($totalPrice) ) {
      $totalPrice = array_merge($totalPrice,
                            [ 'currency_code' => $currency['currency_code'], 
                              'currency_float' => $currency['currency_float'], 
                              'currency_sign' => $currency['currency_sign'],
                              'exchange_rate' => $currency['exchange_rate'],
                            ]);
    }

    if ( empty($totalPrice) ) {
      $totalPrice['msg'] = "exchange_rate ".$exchange_rate." data ".json_encode($data);
    }

    return json_encode($totalPrice);
    // return json_encode($currency);
  }

  public function getProduct($params) {
    helper('querystring');
    $whereCondition = NULL;
    $query = [];

    if ( isset($params['select']) ) $query['select'] = $params['select'];
    if ( isset($params['from']) ) $query['from'] = $params['from'];
    if ( isset($params['where']) && !empty($params['where']) ) {
      $whereCondition = product_query_return($params['where']);
    } else $whereCondition = product_query_return($params);
    if ( isset($params['limit']) ) $query['limit'] = $params['limit'];

    if ( !empty($whereCondition) ) {
      $query['where'] = " AND ".join(" AND ", $whereCondition);
    }
    
    $products = $this->product->getProductQuery($query);    
    return $products;  
  }

  public function getBuyerInfo() {
    if ( empty(session()->userData['buyerId']) ) return null;
    $buyer = $this->buyers->where('id', session()->userData['buyerId'])->first();
    return $buyer;
  }

  // public function getProductPrice($id) {
  //   $product = $this->productPrice->where(['product_idx' => $id, 'available' => 1])->first();
  //   return $product;
  // }
}