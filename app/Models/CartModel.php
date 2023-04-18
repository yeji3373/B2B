<?php
namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model {
  protected $table = 'cart';
  protected $primaryKey = 'idx';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    'buyer_id', 'brand_id', 'prd_id', 'stock_req', 'stock_req_parent',
    'onlyZeroTax', 'order_qty', 'prd_section','dis_prd_price', 'dis_price', 
    'dis_section', 'dis_rate', 'apply_discount',
    'chkd', 'product_price_idx', 'margin_section_id', 
    'dis_section_margin_rate_id' // 할인 적용될 마진율 id
  ];


  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updateField = 'updated_at';
  protected $dateFormat = 'datetime';

  public function cartJoin() {
    $this->select('cart.idx AS cart_idx,
                  cart.order_qty, cart.prd_section, cart.dis_section,
                  cart.dis_rate, cart.apply_discount, cart.onlyZeroTax,
                  cart.chkd, cart.stock_req, cart.stock_req_parent,
                  cart.updated_at')
        ->select('product.*')
        ->select('brand.brand_name, brand.lead_time_min, brand.lead_time_max,
                  brand.brand_logo_src, brand.own_brand, brand.taxation')
        ->select('product_price.idx AS product_price_idx, product_price.retail_price')
        ->select('supply_price.price')
        ->select('product_spq.moq, product_spq.spq, product_spq.spq_inBox, product_spq.spq_outBox')
        ->select('product_spq.calc_code, product_spq.calc_unit')
        ->select('margin.margin_level, margin.margin_section')
        ->select('margin_rate.margin_rate')
        ->select('currency.currency_code, currency.currency_sign, currency.addCalc, currency.currency_float, currency.default_currency')
        ->select('currency_rate.exchange_rate AS basedExchangRate, currency_rate.default_set')
        ->select('CR.cRate_idx, CR.exchange_rate')
        ->select('buyers_currency.cRate_idx AS buyerCurrencyRateIdx')
        ->select('( stocks_detail.supplied_qty - stocks_detail.stock_basis - IFNULL(stocks_detail.req_qty, 0) ) AS available_stock')
        ->join('buyers', 'buyers.id = cart.buyer_id')
        ->join('product', 'product.id = cart.prd_id')
        ->join('brand', 'brand.brand_id = product.brand_id')
        ->join('product_price', 'product_price.product_idx = product.id')
        ->join('supply_price', 'supply_price.product_price_idx = product_price.idx AND supply_price.available = 1', 'left outer')
        ->join('(SELECT idx, product_price_idx, margin_level, price FROM supply_price) AS supply_price_compare', 'supply_price_compare.product_price_idx = product_price.idx AND supply_price_compare.margin_level = cart.dis_section', 'left outer')
        ->join('product_spq', 'product_spq.product_idx = product.id', 'left outer')
        ->join('margin_rate', 'margin_rate.brand_id = product.brand_id')
        ->join('margin', '(margin.idx = margin_rate.margin_idx AND margin.margin_level = buyers.margin_level)')
        ->join('currency', '(currency.default_currency = 1 AND currency.default_currency = 1)')
        ->join('currency_rate', '(currency_rate.currency_idx = currency.idx AND currency_rate.default_set = 1 )' )
        ->join('buyers_currency', 'buyers_currency.buyer_id = cart.buyer_id', 'LEFT OUTER')
        ->join('currency_rate AS CR', 'CR.cRate_idx = buyers_currency.cRate_idx', 'LEFT OUTER')
        ->join('stocks', 'stocks.prd_id = product.id', 'left outer')
        ->join('( SELECT stocks_id
                        , SUM( stocks_detail.supplied_qty ) AS supplied_qty
                        , ( SELECT SUM( stocks_req.req_qty )
                            FROM stocks_req 
                            WHERE stock_id IN (GROUP_CONCAT(stocks_detail.id)) 
                            GROUP BY stocks_req.stock_id ) AS req_qty
                        , ( SELECT out_of_stock_basis FROM stock_settings WHERE available = 1 ) AS stock_basis
                  FROM stocks_detail
                  WHERE stocks_detail.available = 1
                  GROUP BY stocks_detail.stocks_id
                ) AS stocks_detail', 'stocks.id = stocks_detail.stocks_id', 'left outer')
        ->where('product.discontinued', 0)
        ->where('brand.available', 1)
        ->where('product_price.available', 1)
        ->where('margin.available', 1)
        ->where('currency.available', 1)
        ->where('currency_rate.available', 1);
    return $this;
  }
}