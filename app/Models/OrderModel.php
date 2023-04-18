<?php
namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model {
  protected $table = 'orders';
  protected $primaryKey = 'id';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    'buyer_id', 'order_number', 'order_amount', 'discount_amount', 
    'subtotal_amount', 'currency_rate_idx', 'calc_currency_rate_id', 'currency_code',
    'taxation', 'payment_id', 'address_id'
  ];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updateField = 'updated_at';
  protected $dateFormat = 'datetime';
}