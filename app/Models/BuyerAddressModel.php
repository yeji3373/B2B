<?php
namespace App\Models;
use CodeIgniter\Model;

class BuyerAddressModel extends Model {
  protected $table = 'buyers_address';
  protected $primaryKey = 'idx';
  protected $useSoftDeletes = true;
  
  protected $allowedFields = [
    'buyer_id', 'consignee', 'region_id', 'country_code', 'region', 'streetAddr1', 'streetAddr2',
    'city', 'zipcode', 'phone_code', 'phone'
  ];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updatedField = 'updated_at';
  protected $deletedField = 'deleted_at';
  protected $dateFormat = "datetime";
}