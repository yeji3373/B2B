<?php
namespace App\Models;

use CodeIgniter\Model;

class BrandModel extends Model {
  protected $table = 'brand';
  protected $primaryKey = 'brand_id';
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    'brand_name', 'brand_logo_src', 'own_brand', 'excluded_countries', 'available'
  ];

  protected $useTimestamps = true;
  protected $createdField = 'brand_registration_date';
  protected $dateFormat = 'datetime';
}