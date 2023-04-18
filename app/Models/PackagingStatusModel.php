<?php
namespace App\Models;

use CodeIgniter\Model;

class PackagingStatusModel extends Model {
  protected $table = 'packaging_status';
  protected $primaryKey = 'idx';
  protected $useSoftDeletes = false;

  protected $allowedFields = [];

  protected $useTimestamps = true;
  protected $createdField = '';
  protected $dateFormat = 'datetime';
}