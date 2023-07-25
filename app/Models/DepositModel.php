<?php
namespace App\Models;

use CodeIgniter\Model;

class DepositModel extends Model {
  protected $table = 'deposit';
  protected $primaryKey = 'id';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    'available'
  ];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updateField = 'updated_at';
  protected $dateFormat = 'datetime';
}