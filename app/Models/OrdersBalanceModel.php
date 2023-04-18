<?php
namespace App\Models;

use CodeIgniter\Model;

class OrdersBalanceModel extends Model {
  protected $table = 'orders_balance';
  protected $primaryKey = 'balance_id';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    'method_id', 'order_id', 'buyer_id', 
  ];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updateField = 'updated_at';
  protected $dateFormat = 'datetime';
}