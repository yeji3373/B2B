<?php
namespace App\Controllers;

use App\Models\ProductModel;
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
use App\Models\StockDetailModel;
use App\Models\BuyerCurrencyModel;

use Auth\Models\UserModel;

use App\Controllers\CartController;
// use App\Controllers\Api;

use CodeIgniter\I18n\Time;

class Order extends BaseController
{
  protected $brands;
  protected $buyers;
  protected $currency;

  protected $data;
  protected $searchData;
  // protected $tax = 1.1;

  public function __construct() {
    helper('date');
    $this->products = new ProductModel();
    $this->brands = new BrandModel();
    $this->buyers = new BuyerModel();
    $this->currency = new CurrencyModel();
    $this->margin = new MarginModel();
    $this->users = new UserModel();
    $this->productPrice = new ProductPriceModel();
    $this->address = new BuyerAddressModel();
    $this->stocks = new StockDetailModel();
    $this->buyerCurrency = new BuyerCurrencyModel();
    $this->cart = new CartModel();
    
    $this->CartController = new CartController();


    $this->data['header'] = ['css' => ['/address.scss', '/order.scss', '/stock.scss'],
                              'js' => ['/product.js', '/stock.js']];
  }

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
    // $this->searchData = $this->request->getGet();
    $page = null;
    $brandGroup = 'brand';

    // // if ( isset($this->searchData['page_brand']) && !empty($this->searchData['page_brand']) ) $page = $this->searchData['page_brand'];
    // if ( isset($this->searchData['brand_id']) ) {
    //   $this->brands->where('brand_id', $this->searchData['brand_id']);
    // }
    $brands = $this->brands->where('available', '1')->paginate(20, $brandGroup, $page);
    $this->data['brandPager'] = $this->brands->pager;
    $this->data['brandGroup'] = $brandGroup;
    $this->data['brands'] = $brands;
  }
  
  public function productList() {
    $params = $this->request->getVar();
    $page = null;
    $pageGroup = 'prd';
    $total = 0; 
    $buyer = $this->getBuyerInfo();

    // if ( isset($this->searchData['page']) && !empty($this->searchData['page']) ) $page = $this->searchData['page'];
    // if ( $this->request->getGet('brand_id') ) {
    //   $this->product->where('brand_id', $this->request->getGet('brand_id'));
    // }

    $products = $this->getProduct($params)
                    ->select('cart.idx AS cart_idx')
                    ->join('( SELECT * FROM cart WHERE buyer_id = "'.session()->userData['buyerId'].'" GROUP BY prd_id) AS cart'
                          , 'cart.prd_id = product.id'
                          , 'left outer')
                    ->where('margin.margin_level', $buyer['margin_level'])
                    ->where('supply_price.margin_level', $buyer['margin_level'])
                    ->orderBy('product.id')
                    ->paginate(null, $pageGroup, $page);

    // echo $this->products->getLastQuery();
    // echo "<br/><br/>";

    $this->data['products'] = $products;
    $this->data['productPager'] = $this->products->pager;
    $this->data['search'] =  $this->request->getPost();
    $this->data['pageGroup'] = $pageGroup;
    // $this->data['tax'] = $this->tax;
    $this->data['total'] = $total;

    if ( $this->request->isAJAX() ) {
      return view('/layout/includes/product', $this->data);
    } else return $this->data;
  }

  public function cartList() {
    $this->searchData = $this->request->getPost();
    
    if ( !empty(session()->userData) ) {
      $userIdx = $this->getUserIdx();
      $cartList = $this->CartController
                        ->getCartList()
                        ->where('cart.buyer_id', session()->userData['buyerId'])
                        ->where('supply_price.margin_level = cart.prd_section')
                        // ->where('cart.updated_at >=', $this->CartController->checkDate)
                        ->orderBy('cart.prd_id ASC, cart.idx ASC')
                        ->findAll();
    }
    // // $this->data['cartMinimize'] = false; // cart data 최소화해서 보여줌 여부. default false. false: 전체 다 보여주기;

    $this->data['carts'] = $cartList;
    
    // echo $this->products->getLastQuery();
    if ( $this->request->isAJAX() ) {
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
    // print_r($data);
    $where = [];
    if ( !isset($data['cart_idx']) ) {
      $where = ['buyer_id' => session()->userData['buyerId']];
      if ( !empty($data['prd_id']) ) $where = array_merge($where, ['prd_id' => $data['prd_id']]);
    } else $where = ['idx' => $data['cart_idx']];

    $cart = $this->cart->where($where)->first();
    
    if ( !empty($cart) ) {
      // echo "있음<br/>";
      $this->cart->where($where);
      if ( empty($data['oper']) ) {
        $this->cart
            ->set(['order_qty' => $data['order_qty'],
                  'order_price' => $data['order_price']])
            ->update();
        
        if ( $this->cart->affectedRows() ) {
          // $this->applyDiscountCart();
          $code = 200;
          // $msg = $this->cart->getLastQuery();
        } else {
          $code = 500; 
          $msg = lang('Order.unknownError', [ 'error' => 'update' ]);
          // $msg = $this->cart->getLastQuery();
        }
        // echo $msg;
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
                // $msg = $this->cart->getLastQuery();
                $this->applyDiscountCart();
              } else {
                $code = 500;
                // $msg = $this->cart->getLastQuery();
              }
            } else {
              $code = 200;
              $this->applyDiscountCart();
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
    
    if ( !empty(session()->userData['buyerId']) ) { // common.js에 ajax 호출할때마다 로그인 체크 진행 후로 처리 한 후에 if조건 없애기
      $data['prd_section'] = (!empty($this->getBuyerInfo()) ? $this->getBuyerInfo()['margin_level'] : 2);
      $data['buyer_id'] = session()->userData['buyerId'];

      if ( empty($data['brand_id']) && !empty($data['brd_id']) ) {
        $data['brand_id'] = $data['brd_id'];
        unset($data['brd_id']);
      }

      // $cartWhere = ['buyer_id' => session()->userData['buyerId']];
      $cartWhere = ['buyer_id' => $data['buyer_id']];
      if ( !empty($data['prd_id']) ) {
        $cartWhere = array_merge($cartWhere, ['prd_id' => $data['prd_id']]);
      }
      if ( !empty($data['stock_req']) && isset($data['stock_req']) ) {
        $cartWhere = array_merge($cartWhere, ['stock_req' => $data['stock_req']]);
      }
      if ( !empty($data['cart_idx']) && isset($data['cart_idx']) ) {
        $cartWhere = array_merge($cartWhere, ['idx' => $data['cart_idx']]);
        $data['stock_req_parent'] = $data['cart_idx'];
      }
      if ( !empty($data['prd_id']) ) {
        $productPrice = $this->productPrice
                            ->where(['product_idx' => $data['prd_id']
                                  , 'available' => 1])
                            ->first();
        if ( !empty($productPrice) ) {
          $data['product_price_idx'] = $productPrice['idx'];
        }
      }
      
      $marginRate = $this->margin->margin()
                        // ->select('brand_opts.supply_rate_based, brand_opts.available AS brand_opt_available')
                        // ->select("IF ( product_price.not_calculating_margin = 0
                        //               , CONVERT((lead(margin_rate.margin_rate) OVER(PARTITION BY margin_rate.brand_id ORDER BY margin.margin_level ASC) - margin_rate.margin_rate), FLOAT)
                        //               , ROUND(((lead(supply_price.price) OVER(PARTITION BY supply_price.product_price_idx ORDER BY supply_price.margin_idx ASC) - supply_price.price) / ".session()->currency['basedExchangeRate']."), 2)
                        //         ) AS apply_rate")
                        // ->join('brand', 'brand.brand_id = margin_rate.brand_id')
                        // ->join('product', 'product.brand_id = brand.brand_id')
                        // ->join('product_price', 'product_price.product_idx = product.id')
                        // ->join('supply_price', 'supply_price.product_price_idx = product_price.idx AND supply_price.margin_idx = margin.idx AND supply_price.available = 1')
                        // ->join('brand_opts', 'brand_opts.brand_id = brand.brand_id', 'left outer')
                        ->where('margin_rate.brand_id', $data['brand_id'])
                        ->where('margin.margin_level < ', $data['prd_section'])
                        ->orderBy('margin_rate.brand_id ASC, margin.margin_level ASC')
                        ->first();
      // if ( $data['prd_section'] > 1 ) {
      //   $marginRate = $marginRate->limit(($data['prd_section'] - 2), 1);
      // }
      // $marginRate = $marginRate->first();
      
      if ( !empty($marginRate) ) {
        $data['dis_section_margin_rate_id'] = $marginRate['margin_rate_id'];
        $data['dis_section'] = $marginRate['margin_level'];
        // $data['dis_rate'] = $marginRate['margin_rate'];
        // $data['dis_rate'] = $marginRate['apply_rate'];
        // $data['dis_prd_price'] = ($data['prd_price'] * (1 - $marginRate['margin_rate']));
        // $data['order_price'] = ($data['prd_price'] * $data['order_qty']);
        // $data['dis_price'] = $data['dis_prd_price'] * $data['order_qty'];
      } else {
        $data['dis_section'] = NULL;
        // $data['dis_rate'] = 0;
      }
      unset($data['margin_section']);
      unset($data['bskAction']);
      
      $cart = $this->cart->where($cartWhere)->first();
      if ( empty($cart) ) {
        if ( $this->cart->save($data) ) {
          $code = 200;
          // $msg = 'success';
          $msg = $this->cart->getInsertID();
        } else {
          $code = 500;
          $msg = $this->cart->error();
        }
      } else {
        $code = 500;
        $msg = lang('Order.alreadyExists');
      }

      $this->applyDiscountCart();

      if ( $this->request->isAJAX() ) {
        return json_encode(['Code' => $code, 'Msg' => $msg]);
      }
    }
  }

  public function applyDiscountCart(Int $totalPrice = null) {
    if ( $totalPrice == NULL ) $invoiceTotal = $this->CartController->getCartTotalPrice();
    else $invoiceTotal['order_price_total'] = $totalPrice;

    if ( !empty($invoiceTotal['order_price_total']) ) {
      $this->cart->where('buyer_id', session()->userData['buyerId']);
      if ( $invoiceTotal['order_price_total'] >= $this->CartController->basedDiscountVal ) {
        $carts = $this->cart
                      // ->where('user_id', $userId)
                      ->where('apply_discount', 0)
                      ->where('prd_section !=', 1)
                      ->findAll();
        if ( !empty($carts) ) {
          $this->cart->where(['buyer_id'=> session()->userData['buyerId'],
                                'apply_discount'=> 0,
                                'prd_section !=' => 1])
                        ->set(['apply_discount'=> 1])->update();

          // if ( $this->cart->affectedRows() ) {
          //   $session()->set('applyDiscount', true);
          // }
        }
      } else {
        // $carts = $this->cart->where(['user_id' => $userId, 'prd_section !=' => 'dis_section', 'apply_discount' => 1])->findAll();
        $carts = $this->cart->where(['prd_section !=' => 'dis_section', 'apply_discount' => 1])->findAll();

        if ( !empty($carts) ) {
          $this->cart
                ->where(['buyer_id' => session()->userData['buyerId'], 
                        'prd_section !=' => 'dis_section', 
                        'apply_discount' => 1])
                ->set('apply_discount', 0)
                ->update();
        }
      }
    }
  }

  public function requestInventoryCheck() {
    $country = new CountryModel();

    $this->data['prevAddrList'] = $this->address->where('buyer_id', session()->userData['buyerId'])->orderBy('idx DESC')->findAll(0, 1);
    $this->data['regions'] = $country->findAll();
    $this->data['itus'] = $this->getItus()->findAll();
    $this->cartList();
    
    return view('order/InventoryCheck', $this->data);
  }

  public function orderForm() {
    $country = new CountryModel();
    $payments = new PaymentMethodModel();
    
    $where = [];
    // if ( !empty($this->request->getPost('margin_level')) ) {
    //   $where['supply_price.margin_level'] = $this->request->getPost('margin_level');
    // } else $where['supply_price.margin_level'] = 2;

    $cartTotal = $this->CartController->getCartTotalPrice($where);

    if ( $cartTotal['order_price_total'] < $this->CartController->basedMinimumOrderVal ) {
      if ( $this->request->isAJAX()) {
        return json_encode(['error' => lang('Order.orderMinCheck', [$this->CartController->basedMinimumOrderVal])]);
      } 
      return redirect()->to(site_url('/order'))->with('error', lang('Order.orderMinCheck', [$this->CartController->basedMinimumOrderVal]));
      // return;
    }

    $this->data['prevAddrList'] = $this->address->where('buyer_id', session()->userData['buyerId'])->findAll();
    $this->data['regions'] = $country->findAll();
    $this->data['buyer'] = $this->getBuyerInfo();
    $this->data['payments'] = $payments->where('available', 1)->find();
    // $a = $country->select('id, country_no')->orderBy('country_no ASC', 'country_no_sub ASC')->groupBy('country_no')->findAll();
    $this->data['itus'] = $this->getItus()->findAll();
    $this->data['currencies'] = $this->currency->currencyJoin()->where('default_set', 1)->find();
    $this->data['cartSubTotal'] = $cartTotal;
    $this->cartList();

    // print_r($this->data);
    
    if ( $this->request->isAJAX() ) { 
      return view('order/Checkout', $this->data);
    } else $this->basicLayout('order/Checkout', $this->data);
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
    // $this->searchData = $this->request->getPost(); 
    // $whereCondition = product_query_return($this->searchData);

    if ( isset($params['brand_name']) ) {
      $this->products->where('brand.brand_name', $params['brand_name']);
    }

    $whereCondition = product_query_return($params);
    
    $products = $this->products->productJoin()
              ->select($this->CartController->calcRetailPrice().' AS retail_price')
              ->select($this->CartController->calcSupplyPrice().' AS product_price')
              ->join('product_opts', 'product_opts.prd_id = product.id', 'left outer');

    if ( !empty($whereCondition) ) $products->where(join(" AND ", $whereCondition));
    return $products;  
  }

  // public function getCartList() {
  //   $cartList = $this->cart->cartJoin()
  //                         ->select('cart.margin_section_id, cart.dis_section_margin_rate_id')
  //                         ->select($this->calcRetailPrice().' AS retail_price')
  //                         ->select($this->calcSupplyPrice().' AS prd_price')
  //                         ->select("( {$this->calcSupplyPrice()} * cart.order_qty ) AS order_price")
  //                         // ->select("IF ( `cart`.`apply_discount` = 1, (({$this->calcSupplyPrice()} * `cart`.`dis_rate`) * `cart`.`order_qty`), 0 ) AS `order_discount_price`")
  //                         ->select(" IF( cart.apply_discount = 1, ({$this->calcSupplyPriceCompare()} * cart.order_qty), 0) AS order_discount_price")
  //                         // ->select("IF ( `cart`.`apply_discount` = 1, ({$this->calcSupplyPrice()} * (1 - `cart`.`dis_rate`)), 0 ) AS `dis_prd_price`")
  //                         ->select(" IF( cart.apply_discount = 1, ({$this->calcSupplyPrice()} - {$this->calcSupplyPriceCompare()}), 0) AS dis_prd_price")
  //                         ->where('cart.buyer_id', session()->userData['buyerId'])
  //                         ->where('supply_price.margin_level = cart.prd_section')
  //                         ->where('cart.updated_at >=', new Time('-7 days'))
  //                         ->orderBy('cart.prd_id ASC, cart.idx ASC');    
  //   return $cartList;
  // }

  // public function calcRetailPrice() {
  //   $select = null;
  //   $exchangeRate = session()->currency['exchangeRate'];
  //   $basedExchangeRate = session()->currency['basedExchangeRate'];

  //   if ( !empty($exchangeRate) && ($exchangeRate != $basedExchangeRate) ) {
  //     $basedExchangeRate = $exchangeRate;
  //   }
    
  //   $select = "ROUND((product_price.retail_price / {$basedExchangeRate}), ".session()->currency['currencyFloat'].")";
   
  //   return $select;
  // }

  // public function calcSupplyPrice() {
  //   $select = null;
  //   $exchangeRate = session()->currency['exchangeRate'];
  //   $basedExchangeRate = session()->currency['basedExchangeRate'];

  //   if ( !empty($exchangeRate) && ($exchangeRate != $basedExchangeRate) ) { // 환율 우대 받은 값이 있을 때 값이 다르면 환율 적용된 값을 최우선으로 처리
  //     $basedExchangeRate = $exchangeRate;
  //   }
  //   $select = "ROUND((supply_price.price / {$basedExchangeRate}), ".session()->currency['currencyFloat'].")";
  //   return $select;
  // }

  // public function calcSupplyPriceCompare($condition = NULL) {
  //   $select = null;
  //   $exchangeRate = session()->currency['exchangeRate'];
  //   $basedExchangeRate = session()->currency['basedExchangeRate'];

  //   if ( !empty($exchangeRate) && ($exchangeRate != $basedExchangeRate) ) { // 환율 우대 받은 값이 있을 때 값이 다르면 환율 적용된 값을 최우선으로 처리
  //     $basedExchangeRate = $exchangeRate;
  //   }
  //   $select = "ROUND((supply_price_compare.price / {$basedExchangeRate}), ".session()->currency['currencyFloat'].")";
  //   return $select;
  // }

  // public function getCartTotalPrice( $where = array(), $exchange = 1 ) {
  //   $whereCondition = array();
  //   $cart = $this->cart->cartJoin();
  //   if ( count($where) > 0 ) $whereCondition = $where;
  //   if ( $exchange > 1 ) :
  //     $cart->select("(SUM({$this->calcSupplyPrice()} * `cart`.`order_qty`) * {$exchange}) AS `order_price_total`")
  //         ->select("IF ( `cart`.`apply_discount` = 1, 
  //                         ROUND((SUM(({$this->calcSupplyPrice()} - {$this->calcSupplyPriceCompare()}) * `cart`.`order_qty`) * {$exchange}), 0),
  //                         0 
  //                       ) AS `order_discount_total`")
  //         ->select("IF ( `cart`.`apply_discount` = 1, 
  //                         ROUND((SUM({$this->calcSupplyPriceCompare()} * `cart`.`order_qty`) * {$exchange}), 0),
  //                         ROUND((SUM({$this->calcSupplyPrice()} * `cart`.`order_qty`) * {$exchange}), 0)
  //                       ) AS order_subTotal");
  //   else : 
  //     $cart->select("SUM({$this->calcSupplyPrice()} * `cart`.`order_qty`) AS `order_price_total`")
  //     ->select("IF ( `cart`.`apply_discount` = 1, 
  //                     SUM(({$this->calcSupplyPrice()} - {$this->calcSupplyPriceCompare()}) * `cart`.`order_qty`),
  //                     0 
  //                   ) AS `order_discount_total`")
  //     ->select("IF ( `cart`.`apply_discount` = 1, 
  //                     SUM({$this->calcSupplyPriceCompare()} * `cart`.`order_qty`),
  //                     SUM({$this->calcSupplyPrice()} * `cart`.`order_qty`)
  //                   ) AS `order_subTotal`");
  //   endif;

  //   $cart->select('cart.apply_discount AS applyDiscount')
  //         ->where('cart.buyer_id', session()->userData['buyerId'])
  //         ->where('supply_price.margin_level = cart.prd_section')
  //         ->where($whereCondition)
  //         ->groupBy('cart.buyer_id');

  //   $cartSubTotal = $cart->first();

  //   if ( empty($cartSubTotal) ) {
  //     return NULL;
  //   } else return $cartSubTotal;
  // }

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