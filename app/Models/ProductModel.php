<?php
namespace App\Models;
use CodeIgniter\Model;

class ProductModel extends Model {
  protected $table = 'product';
  protected $primaryKey = 'id';
  protected $useSoftDeletes = false;

  protected $allowedFields = [];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updatedField = 'updated_at';
  protected $dateFormat = "datetime";

  protected $default = [
    'discontinued' => 0,
    'display'      => 1,
  ];


  function products($sql = array()) {
    $select = '';
    $where = $this->default;
    $orderBy = 'id ASC';

    if ( !empty($sql) ) {
      if ( !empty($sql['select']) ) $select = $sql['select'];
      if ( !empty($sql['where']) ) $where = array_merge($where, $sql['where']);
      if ( !empty($sql['orderby']) ) $orderBy = $sql['orderby'];
      if ( !empty($sql['orderBy']) ) $orderBy = $sql['orderBy'];
    }
    $this->select($select)->where($where)->orderBy($orderBy);
    return $this;
  }

  function combine_product_brand($sql = array()) {
    self::products($sql);
    return $this;
  }

  public function getProductQuery($getQuery = [], $mrginLevel = 2) {
    $query = NULL;
    $limitquery = NULL;

    $range = 'MAX';
    if ( $mrginLevel < 2 ) { $range = "MIN"; } 
    
    $selectquery = "SELECT {$this->table}.id, {$this->table}.category_ids, {$this->table}.barcode
                          , {$this->table}.hs_code, {$this->table}.sample, {$this->table}.img_url
                          , {$this->table}.name_en, {$this->table}.type_en, {$this->table}.edition_en
                          , {$this->table}.box, {$this->table}.in_the_box, {$this->table}.contents_of_box
                          , {$this->table}.contents_type_of_box, {$this->table}.spec, {$this->table}.spec2
                          , {$this->table}.container, {$this->table}.spec_detail, {$this->table}.spec_pcs
                          , {$this->table}.package, {$this->table}.package_detail, {$this->table}.etc
                          , {$this->table}.sales_channel, {$this->table}.shipping_weight
                          , {$this->table}.discontinued, {$this->table}.display, {$this->table}.renewal
                          , brand.brand_id, brand.brand_name, brand.brand_logo_src
                          , brand.taxation AS brand_tax, brand.own_brand
                          , brand.excluded_countries";
    $fromquery = " FROM {$this->table} ";
    $joinQuery = "  STRAIGHT_JOIN product_price ON {$this->table}.id = product_price.product_idx
                    STRAIGHT_JOIN supply_price ON product_price.idx = supply_price.product_price_idx
                    STRAIGHT_JOIN brand ON brand.brand_id = {$this->table}.brand_id
                    STRAIGHT_JOIN ( SELECT margin_rate.*, margin.margin_level
                        FROM margin_rate
                        STRAIGHT_JOIN margin ON margin_rate.margin_idx = margin.idx
                        STRAIGHT_JOIN (
                              SELECT A.brand_id, {$range}(A.margin_level) AS margin_level
                              FROM ( 	
                                    SELECT margin.idx AS margin_idx, margin.margin_level, margin.margin_section, margin.available AS margin_avaiable
                                        , margin_rate.idx AS margin_rate_idx, margin_rate.brand_id, margin_rate.margin_rate, margin_rate.available
                                    FROM margin
                                    STRAIGHT_JOIN margin_rate ON margin.idx = margin_rate.margin_idx 
                                    WHERE margin_rate.available = 1
                                    ORDER BY margin_rate.brand_id ASC, margin.margin_level ASC
                                  ) AS A
                              GROUP BY A.brand_id
                          ) AS B ON B.brand_id = margin_rate.brand_id AND B.margin_level = margin.margin_level
                      ) AS C ON C.brand_id = {$this->table}.brand_id AND C.margin_idx = supply_price.margin_idx";
    $wherequery = " WHERE discontinued = 0 
                      AND display = 1
                      AND product_price.available = 1 
                      AND brand.available = 1";
    $orderbyquery = " ORDER BY brand.brand_id ASC, {$this->table}.id ASC";

    if ( !empty($getQuery['select'])) $selectquery .= $getQuery['select'];
    if ( !empty($getQuery['from'])) $fromquery .= $getQuery['from'];
    if ( !empty($getQuery['join'])) $joinQuery .= $getQuery['join'];
    if ( !empty($getQuery['where'])) $wherequery .= $getQuery['where'];
    if ( !empty($getQuery['orderby'])) $orderbyquery .= $getQuery['orderby'];
    if ( !empty($getQuery['limit'])) $limitquery = $getQuery['limit'];
   
    $query = $selectquery.
              $fromquery.
              $joinQuery.
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

    $this->select('{$this->table}.*')
        ->select('brand.brand_id, brand.brand_name')
        ->select('brand.lead_time_min, brand.lead_time_max, brand.brand_logo_src')
        ->select('brand.taxation AS brand_tax, brand.own_brand')
        ->select('brand.excluded_countries', 'brand.supply_rate_based', 'brand.supply_rate_by_brand')
        ->select('product_price.taxation')
        ->select('product_spq.moq, product_spq.spq_criteria, product_spq.spq_inBox, product_spq.spq_outBox')
        ->select('product_spq.calc_code, product_spq.calc_unit')
        ->join('brand', 'brand.brand_id = '.$this->table.'.brand_id', 'STRAIGHT')
        ->join('product_price', 'product_price.product_idx = '.$this->table.'.id', 'STRAIGHT')
        ->join('supply_price', 'supply_price.product_idx = '.$this->table.'.id AND supply_price.available = 1', 'left outer')
        ->join('product_spq', 'product_spq.product_idx = '.$this->table.'.id', 'left outer')
        ->where($this->default);

    return $this;
  }

  function productOrderJoin($where = []) {
    $this->select('{$this->table}.*
                  , brand.brand_name
                  , orders_detail.id AS order_detail_id
                  , orders_detail.order_id
                  , orders_detail.order_excepted
                  , orders_detail.changed_manager
                  , orders_detail.prd_order_qty
                  , orders_detail.prd_change_qty
                  , orders_detail.prd_fixed_qty
                  , orders_detail.prd_price
                  , orders_detail.prd_change_price
                  , orders_detail.margin_rate_id')
    ->join('orders_detail', 'orders_detail.prd_id = {$this->table}.id')
    ->join('brand', 'brand.brand_id = {$this->table}.brand_id')
    ->where($where);

    return $this;
  }
}