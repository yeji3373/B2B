<?php
namespace App\Models;
use CodeIgniter\Model;

class StockDetailModel extends Model {
  protected $table = 'stocks_detail';
  protected $primaryKey = 'id';
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    'sales_qty', 'pending_qty', 'available', 'manager_id'
  ];

  protected $useTimestamps = true;
  protected $createdField = 'supplied_at';
  protected $updatedField = 'updated_at';
  protected $dateFormat = "datetime";
}