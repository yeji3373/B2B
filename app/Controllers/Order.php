<?php
namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\OrderModel;
use App\Models\BrandModel;
use App\Models\BuyerModel;
use App\Models\CurrencyModel;
use App\Models\CartModel;
use App\Models\MarginModel;
use App\Models\RegionModel;
use App\Models\CountryModel;
use App\Models\BuyerAddressModel;
use App\Models\PaymentMethodModel;
use App\Models\ProductPriceModel;
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
  protected $currency;

  protected $data;
  protected $searchData;

  public function __construct() {
    helper('date');
    $this->products = new ProductModel();
    $this->brands = new BrandModel();
    $this->buyers = new BuyerModel();
    $this->currency = new CurrencyModel();
    $this->margin = new MarginModel();
    $this->users = new UserModel();
    $this->productPrice = new ProductPriceModel();
    $this->productSpq = new ProductSpqModel();
    $this->product = new ProductPriceModel();
    $this->address = new BuyerAddressModel();
    $this->stocks = new StockDetailModel();
    $this->buyerCurrency = new BuyerCurrencyModel();
    $this->cart = new CartModel();
    
    $this->CartController = new CartController();


    $this->data['header'] = ['css' => ['/address.css', '/order.css'
                                      , '/inventory.css', '/stock.css'],
                              'js' => ['/address.js', '/product.js', '/inventory.js', '/stock.js']];
  }

  public function __output() {}

  public function index() {
    $this->CartController->initialCartList(); // 카트 초기화
    $this->brandList();
    $this->cartList();
    $this->productList();
    $this->cartTotalPrice();
    // $this->applyDiscountCart();
    $this->basicLayout('product/list', $this->data);
  }

  public function brandList() {
    $page = null;
    $brandGroup = 'brand';

    $brands = $this->brands
                  ->where('available', 1)
                  ->orderBy('own_brand DESC, brand_name ASC, brand_id ASC')
                  ->findAll();
    $this->data['brands'] = $brands;
  }
  
  public function productList() {
    // var_dump(session()->userData);
    $params = $this->request->getPost();
    $total = 0;
    $request_unit = false;
    $where = null;

    $offset = 15;
    $start = empty($params['page']) ? 0 : ((($params['page'] - 1) * 1) * $offset);

    if ( !empty($params) ) {
      if ( isset($params['page']) ) unset($params['page']);
      if ( isset($params['brand_id']) && empty($params['brand_id']) ) unset($params['brand_id']);
      if ( isset($params['request_unit']) ) {
        if ( !empty($params['request_unit']) )  $request_unit = true;
        unset($params['request_unit']);
      }
    }

    $query['select'] = ','.$this->CartController->calcRetailPrice().' AS retail_price, '
                    .$this->CartController->calcSupplyPrice().' AS product_price';
    $query['where'] = $params;
    $query['limit'] = " limit $start, $offset";
    $products = $this->getProduct($query);

    $this->data['products'] = $products;

    if ( $this->request->isAJAX() ) {
      // if ( $request_unit == true ) {
        return view('/layout/includes/productItem', $this->data);
      // } else return view('/layout/includes/product', $this->data);
    } else {
      // echo $this->products->getLastQuery(); 
      return $this->data;
    }
  }

  public function cartList() {
    $data = $this->request->getPost();
    $code = 500;
    $msg = '';
    $where = NULL;

    if ( !empty(session()->userData) ) {
      if ( isset($data['cart_id']) && !empty($data['cart_id']) ) {
        $where .= " AND cart.idx = ".$data['cart_id'];
      }
      $query['where'] = $where;
      $cartList = $this->CartController->getCartList($query);
    }
    // // $this->data['cartMinimize'] = false; // cart data 최소화해서 보여줌 여부. default false. false: 전체 다 보여주기;
    $this->data['carts'] = $cartList;
    // var_dump($cartList);
    if ( $this->request->isAJAX() ) {
      $this->data['params'] = $data;
      return view('/layout/includes/Cart', $this->data);
    } else return $this->data;
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

  public function editCartList() {
    $msg = '';
    $data = $this->request->getVar();
    // var_dump($data);
    $where = [];
    if ( !isset($data['cart_idx']) ) {
      $where = ['buyer_id' => session()->userData['buyerId']];
      if ( !empty($data['prd_id']) ) $where = array_merge($where, ['prd_id' => $data['prd_id']]);
    } else $where = ['idx' => $data['cart_idx']];

    $cart = $this->cart->where($where)->first();    
    if ( !empty($cart) ) {
      $this->cart->where($where);
      if ( empty($data['oper']) ) {
        $prdTotalPrice = ($data['product_price'] * $cart['order_qty']);
        $this->cart
            ->set(['order_qty' => $data['order_qty']])
            ->update();
        
        if ( $this->cart->affectedRows() ) {
          $prdTotalPrice = ($data['product_price'] * $data['order_qty']);
          $code = 200;
          $msg = number_format($prdTotalPrice, session()->currency['currencyFloat']);
        } else {
          $code = 500; 
          $msg = lang('Order.unknownError', [ 'error' => 'update' ]);
        }
      } else {
        if ( $data['oper'] == 'del' ) {
          $this->cart->delete();
          if ( $this->cart->affectedRows() ) {
            if ( isset($data['stock_req_parent']) && isset($data['case']) ) {
              $this->cart->where(['idx' => $data['stock_req_parent']]);
              if ( $data['case'] == 1 ) {
                $this->cart->set(['stock_req_parent' => NULL])->update();
              } else if ( $data['case'] == 0 ) {
                $this->cart->delete();
              }

              if ( $this->cart->affectedRows() ) {
                $code = 200;
                // // $msg = $this->cart->getLastQuery();
                // $this->applyDiscountCart();
              } else {
                $code = 500;
                // $msg = $this->cart->getLastQuery();
              }
            } else {
              $code = 200;
              // $this->applyDiscountCart();
            }
          } else {
            $code = 500; 
            // $msg = $data;
          }
        }
      }
    } else {
      $code = 500;
      $msg = $this->cart->error()['message'].' is null '.json_encode($data);
    }

    if ( $this->request->isAJAX() ) {
      return json_encode(['Code' => $code, 'Msg' => $msg]);
    }
  }

  public function addCartList() {
    $code = 500;
    $msg = '';
    $data = $this->request->getPost();

    if ( !empty(session()->userData['buyerId']) ) {
      $data['order_qty'] = 10;
      // $data['prd_section'] = (!empty($this->getBuyerInfo()) ? $this->getBuyerInfo()['margin_level'] : 2);
      $data['prd_section'] = session()->userData['buyerMargin'];
      $data['buyer_id'] = session()->userData['buyerId'];
      if ( !empty($data['cart_idx']) && isset($data['cart_idx']) ) {
        $data['idx'] = $data['cart_idx'];
        unset($data['cart_idx']);
      }
      if ( !empty($data['prd_id']) ) {
        $productPrice = $this->productPrice
                            ->where(['product_idx' => $data['prd_id']
                                  , 'available' => 1])
                            ->first();
        if ( !empty($productPrice) ) {
          $data['product_price_idx'] = $productPrice['idx'];
        }

        $productSpq = $this->productSpq
                            ->where(['product_idx' => $data['prd_id']
                                  , 'available' => 1])
                            ->first();
        if ( !empty($productSpq) ) {
          $data['order_qty'] = !empty($productSpq['spq_inBox']) ? $productSpq['spq_inBox'] : 10;
        }
      }

      $cart = $this->cart
                ->where(['buyer_id' => $data['buyer_id']
                        , 'prd_id' => $data['prd_id']])
                ->first();
      if ( empty($cart) ) {
        if ( $this->cart->save($data) ) {
          $code = 200;
          $msg = $this->cart->getInsertID();
          $aaaa = $productSpq;
        } else {
          $code = 500;
          $msg = $this->cart->error();
        }
      } else {
        $code = 500;
        $msg = lang('Order.alreadyExists');
      }
    } else {
      $code = 401;
      $msg = '로그인 후 재 진행해주세요';
    }

    if ( $this->request->isAJAX() ) {
      return json_encode(['Code' => $code, 'Msg' => $msg]);
    }
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
    //     return json_encode(['error' => lang('Order.orderMinCheck', [$this->CartController->basedMinimumOrderVal])]);
    //   } 
    //   return redirect()->to(site_url('/order'))->with('error', lang('Order.orderMinCheck', [$this->CartController->basedMinimumOrderVal]));
    // }    

    $this->data['prevAddrList'] = $this->address->where('buyer_id', session()->userData['buyerId'])->findAll();
    $this->data['regions'] = $country->findAll();
    $this->data['buyer'] = $this->getBuyerInfo();
    $this->data['payments'] = $payments->where('available', 1)->find();
    $this->data['itus'] = $this->getItus()->findAll();
    $this->data['currencies'] = $this->currency->currencyJoin()->where('currency.default_currency', 1)->find();
    $this->data['orderDetails'] = $this->products
                                      ->productOrderJoin(
                                        ['orders_detail.order_id'=> 1
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
    
    $products = $this->products->getProductQuery($query);    
    return $products;  
  }

  public function getUserIdx() {
    $userIdx = $this->users->getUserIndex(session()->userData['id']);
    return $userIdx;
  }

  public function getBuyerInfo() {
    $buyer = $this->buyers->where('id', session()->userData['buyerId'])->first();
    return $buyer;
  }

  // public function getProductPrice($id) {
  //   $product = $this->productPrice->where(['product_idx' => $id, 'available' => 1])->first();
  //   return $product;
  // }
}