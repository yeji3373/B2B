<?php
namespace App\Models;
use CodeIgniter\Model;

class ProductModel extends Model {
  protected $table = 'product';
  protected $primaryKey = 'id';
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    // 'brand_id', 'category_ids', 'barcode', 'name', 'name_en', 'type', 'type_en',
    // 'package', 'package_pcs', 'spec', 'spec_detail', 'sales_channel', 'unit_weight',
    // 'shipping_weight'
  ];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updatedField = 'updated_at';
  protected $dateFormat = "datetime";

  protected $default = [
    'discontinued' => 0,
    'display'      => 1,
  ];


  function products() {
    $this->where($this->default)->orderBy('id', 'ASC');
    return $this;
  }

  public function getProductQuery($getQuery = [], $mrginLevel = 2) {
    $query = NULL;
    $limitquery = NULL;
    $selectquery = "SELECT product.id, product.category_ids, product.barcode
          , product.hs_code, product.sample, product.img_url
          , product.name_en, product.type_en, product.edition_en
          , product.box, product.in_the_box, product.contents_of_box
          , product.contents_type_of_box, product.spec, product.spec2
          , product.container, product.spec_detail, product.spec_pcs
          , product.package, product.package_detail, product.etc
          , product.sales_channel, product.shipping_weight
          , product.discontinued, product.display, product.renewal
          , brand.brand_id, brand.brand_name, brand.brand_logo_src
          , brand.taxation AS brand_tax, brand.own_brand
          , brand.excluded_countries";
    $fromquery = " FROM {$this->table}
                  , ( SELECT * FROM brand ) AS brand
                  , ( SELECT * FROM product_price ) AS product_price
                  , ( SELECT * FROM supply_price WHERE margin_level = $mrginLevel) AS supply_price";
    $wherequery = " WHERE discontinued = 0 
                      AND display = 1 
                      AND brand.brand_id = product.brand_id
                      AND product.id = product_price.product_idx
                      AND product_price.idx = supply_price.product_price_idx
                      AND product_price.available = 1 
                      AND brand.available = 1";
    $orderbyquery = " ORDER BY brand.brand_id ASC, product.id ASC";

    if ( !empty($getQuery['select'])) $selectquery .= $getQuery['select'];
    if ( !empty($getQuery['from'])) $fromquery .= $getQuery['from'];
    if ( !empty($getQuery['where'])) $wherequery .= $getQuery['where'];
    if ( !empty($getQuery['orderby'])) $orderbyquery .= $getQuery['orderby'];
    if ( !empty($getQuery['limit'])) $limitquery = $getQuery['limit'];
   
    $query = $selectquery.
              $fromquery.
              $wherequery.
              $orderbyquery.
              $limitquery;

    $products = $this->db->query($query)->getResultArray();
    return $products;
  }

  function productJoin() {
    // $this->default['stocks.available'] = 1;
    $this->default['product_price.available'] = 1;
    // $this->default['margin.available'] = 1;
    $this->default['brand.available'] = 1;
    // $this->default['product_spq.available'] = 1;

    $this->select('product.*')
        ->select('brand.brand_id, brand.brand_name')
        ->select('brand.lead_time_min, brand.lead_time_max, brand.brand_logo_src')
        ->select('brand.taxation AS brand_tax, brand.own_brand')
        ->select('brand.excluded_countries', 'brand.supply_rate_based', 'brand.supply_rate_by_brand')
        // ->select('margin_rate.idx AS margin_rate_id, margin_rate.margin_idx, margin_rate.margin_rate')
        // ->select('margin.margin_level, margin.margin_section')
        // // ->select('product_price.retail_price, product_price.price, product_price.taxation')
        // ->select('product_price.retail_price, product_price.taxation')
        ->select('product_price.taxation')
        ->select('product_spq.moq, product_spq.spq, product_spq.spq_inBox, product_spq.spq_outBox')
        ->select('product_spq.calc_code, product_spq.calc_unit')
        // ->select('( stocks_detail.supplied_qty - stocks_detail.stock_basis - IFNULL(stocks_detail.req_qty, 0) ) AS available_stock')
        ->join('brand', 'brand.brand_id = '.$this->table.'.brand_id')
        // ->join('margin_rate', 'margin_rate.brand_id = '.$this->table.'.brand_id')
        // ->join('margin', 'margin_rate.margin_idx = margin.idx')
        ->join('product_price', 'product_price.product_idx = '.$this->table.'.id')
        ->join('supply_price', 'supply_price.product_idx = '.$this->table.'.id AND supply_price.available = 1', 'left outer')
        ->join('product_spq', 'product_spq.product_idx = '.$this->table.'.id', 'left outer')
        // ->join('stocks', 'stocks.prd_id = '.$this->table.'.id', 'left outer')
        // ->join('( SELECT stocks_id
        //                 , SUM( stocks_detail.supplied_qty ) AS supplied_qty
        //                 , ( SELECT SUM( stocks_req.req_qty )
        //                     FROM stocks_req 
        //                     WHERE stock_id IN (GROUP_CONCAT(stocks_detail.id)) 
        //                     GROUP BY stocks_req.stock_id ) AS req_qty
        //                 , ( SELECT out_of_stock_basis FROM stock_settings WHERE available = 1 ) AS stock_basis
        //           FROM stocks_detail
        //           WHERE stocks_detail.available = 1
        //           GROUP BY stocks_detail.stocks_id
        //         ) AS stocks_detail', 'stocks.id = stocks_detail.stocks_id', 'left outer')
        ->where($this->default);

    return $this;
  }

  function productOrderJoin($where = []) {
    $this->select('product.*
                  , brand.brand_name
                  , orders_detail.id AS order_detail_id
                  , orders_detail.order_id
                  , orders_detail.order_excepted
                  , orders_detail.changed_manager
                  , orders_detail.prd_order_qty
                  , orders_detail.prd_change_qty
                  , orders_detail.prd_qty_changed
                  , orders_detail.prd_price
                  , orders_detail.prd_change_price
                  , orders_detail.prd_price_changed
                  , orders_detail.margin_rate_id')
    ->join('orders_detail', 'orders_detail.prd_id = product.id')
    ->join('brand', 'brand.brand_id = product.brand_id')
    ->where($where);

    return $this;
  }
}