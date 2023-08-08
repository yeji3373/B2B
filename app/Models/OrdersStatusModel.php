<?php
namespace App\Models;

use CodeIgniter\Model;

class OrdersStatusModel extends Model {
  protected $table = 'order_status';
  protected $primaryKey = 'status_id';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes = false;

  protected $allowedFields = [];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updateField = 'updated_at';
  protected $dateFormat = 'datetime';
}