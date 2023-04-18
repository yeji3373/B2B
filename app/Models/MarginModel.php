<?php
namespace App\Models;

use CodeIgniter\Model;

class MarginModel extends Model {
  protected $table = 'margin_rate';
  protected $primaryKey = 'idx';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes = false;

  protected $allowedFields = [];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updateField = 'updated_at';
  protected $dateFormat = 'datetime';

  public function margin() {
    $this->select("{$this->table}.idx AS margin_rate_id, {$this->table}.brand_id, {$this->table}.available AS margin_rate_available")
        ->select("margin.idx AS idx, margin.margin_level")
        ->select("margin.margin_section, {$this->table}.margin_rate, margin.available")
        ->join("margin", "margin.idx = margin_rate.margin_idx")
        ->where("{$this->table}.available", 1);

    return $this;
  }
}
// // SELECT idx, brand_id, margin_idx, margin_rate, CONVERT((lead(margin_rate, 1) over(PARTITION BY brand_id ORDER BY margin_idx ASC) - margin_rate ), FLOAT) AS a FROM `margin_rate`
// // -- WHERE margin_idx < 2
// // ORDER BY brand_id ASC, margin_idx ASC

// SELECT margin_rate.idx AS margin_rate_id, margin_rate.brand_id, margin_rate.available AS margin_rate_available, margin_rate.margin_rate
// 		, margin.idx AS idx, margin.margin_level, margin.margin_section, margin.available
//         , brand_opts.supply_rate_based, brand_opts.available AS brand_opt_available
// 		, IF ( product_price.not_calculating_margin = 0
//             , CONVERT((lead(margin_rate.margin_rate) OVER(PARTITION BY margin_rate.brand_id ORDER BY margin.margin_level ASC) - margin_rate.margin_rate), FLOAT)
//             , 
//       ) AS diff
// FROM margin_rate
// 	JOIN margin ON margin.idx = margin_rate.margin_idx
//     JOIN brand ON brand.brand_id = margin_rate.brand_id
//     LEFT OUTER JOIN brand_opts ON brand_opts.brand_id = brand.brand_id
//     JOIN product ON product.brand_id = brand.brand_id
//     JOIN product_price ON product_price.product_idx = product.idx
//     JOIN supply_price ON supply_price.product_idx = product_price.idx
// WHERE margin_rate.available = 1
// 	AND margin_rate.brand_id = 7
//     -- AND margin.margin_level < 2
// ORDER BY margin_rate.brand_id ASC,  margin.margin_level ASC