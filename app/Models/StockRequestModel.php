<?php
namespace App\Models;
use CodeIgniter\Model;

class StockRequestModel extends Model {
  protected $table = 'stocks_req';
  protected $primaryKey = 'req_id';
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    'prd_id', 'order_id', 'req_qty', 'prd_id', 
    'stocks_id', 'stock_id', 'stock_type', 'done'
  ];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updatedField = 'updated_at';
  protected $dateFormat = "datetime";
}