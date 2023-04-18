<?php
namespace App\Models;

use CodeIgniter\Model;

class PackagingDetailModel extends Model {
  protected $table = 'packaging_detail';
  protected $primaryKey = 'idx';
  protected $useSoftDeletes = false;

  protected $allowedFields = ['packaging_id', 'status_id', 'in_progress'];

  protected $useTimestamps = true;
  protected $createdField = '';
  protected $dateFormat = 'datetime';
}