<?php
namespace App\Models;

use CodeIgniter\Model;

class OrderDetailModel extends Model {
  protected $table = 'orders_detail';
  protected $primaryKey = 'id';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    'order_id', 'prd_id', 'stock_req', 'stock_req_qty',
    'prd_order_qty', 'prd_price_changed', 
    'prd_price_id', 'prd_supply_price_id',
    'prd_price', 'prd_discount', 'margin_rate_id'
  ];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updateField = 'updated_at';
  protected $dateFormat = 'datetime';
}