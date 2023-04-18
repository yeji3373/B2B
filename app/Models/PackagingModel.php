<?php
namespace App\Models;

use CodeIgniter\Model;

class PackagingModel extends Model {
  protected $table = 'packaging';
  protected $primaryKey = 'idx';
  protected $useSoftDeletes = false;

  protected $allowedFields = ['order_id'];

  protected $useTimestamps = true;
  protected $createdField = '';
  protected $dateFormat = 'datetime';
}