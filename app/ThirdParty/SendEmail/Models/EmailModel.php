<?php
namespace App\Models;

use CodeIgniter\Model;

class EmailModel extends Model {
  protected $table = 'email_status';
  protected $primaryKey = 'email_status_idx';
  protected $useSoftDeletes = true;

  protected $allowedFields = [
    'email_idx',
    'email_message_idx', 
    'user_idx',
    'manager_idx',
    'validation_key',
    'expiring_date'
  ];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $deletedField = 'deleted_at';
  protected $dateFormat = 'datetime';
}