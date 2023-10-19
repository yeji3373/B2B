<?php
namespace App\Models;

use CodeIgniter\Model;

class RequirementOptionModel extends Model {
  protected $table = 'requirement_option';
  protected $primaryKey = 'idx';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes = false;

  protected $allowedFields = [];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updateField = '';
  protected $dateFormat = 'datetime';
}