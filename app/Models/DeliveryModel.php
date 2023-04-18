<?php
namespace App\Models;

use CodeIgniter\Model;

class DeliveryModel extends Model {
  protected $table = 'delivery';
  protected $primaryKey = 'id';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    'order_id', 'shipment_id', 'payment_id', 'delivery_status', 
    'delivery_status_en', 'delivery_code'
  ];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updateField = 'updated_at';
  protected $dateFormat = 'datetime';
}