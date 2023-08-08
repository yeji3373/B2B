<?php
namespace App\Models;

use CodeIgniter\Model;

class RequirementRequestModel extends Model {
  protected $table = 'requirement_request';
  protected $primaryKey = 'idx';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    'order_id', 'order_detail_id', 'requirement_id', 'requirement_detail'
  ];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updateField = 'updated_at';
  protected $dateFormat = 'datetime';
}