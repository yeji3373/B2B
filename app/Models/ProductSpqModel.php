<?php
namespace App\Models;
use CodeIgniter\Model;

class ProductSpqModel extends Model {
  protected $table = 'product_spq';
  protected $primaryKey = 'id';
  protected $useSoftDeletes = false;

  protected $allowedFields = [];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updatedField = 'updated_at';
  protected $dateFormat = "datetime";
}