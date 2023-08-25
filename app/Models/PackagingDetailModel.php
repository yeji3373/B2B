<?php
namespace App\Models;

use CodeIgniter\Model;

class PackagingDetailModel extends Model {
  protected $table = 'packaging_detail';
  protected $primaryKey = 'idx';
  protected $useSoftDeletes = true;

  protected $allowedFields = ['packaging_id', 'status_id', 'in_progress', 'complete'];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updatedField = 'updated_at';
  protected $deletedField = 'deleted_at';
  protected $dateFormat = 'datetime';
}