<?php
namespace App\Models;

use CodeIgniter\Model;

class CartStatusModel extends Model {
  protected $table = "cart_status";
  protected $primaryKey = "cart_status_idx";
  protected $useAutoIncrement = true;
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    "cart_idx", 
    "product_idx", 
    "brand_id", "brand_name",
    "exchange_rate",
    "product_price_idx", "retail_price", "supply_price",
    "supply_price_idx", "price", "applied_price",
    "spq_idx", "moq", 'spq_criteria', 'spq', 'spq_inBox', 
    'spq_outBox', 'calc_code', 'calc_unit'
  ];

  protected $useTimestamps = true;
  protected $createdField = "created_at";
  protected $updateField = "updated_at";
  protected $dateFormat = "datetime";

}