<?php
namespace SendEmail\Models;

use CodeIgniter\Model;

class EmailStatusModel extends Model {
  protected $table = 'email_status';
  protected $primaryKey = 'email_status_idx';
  protected $useSoftDeletes = true;

  protected $allowedFields = [
    'email_idx',
    'email_message_idx', 
    'user_idx',
    'manager_idx',
    'activate_hash',
    'expiring_date',
    'active'
  ];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updatedField = 'updated_at';
  protected $deletedField = 'deleted_at';
  protected $dateFormat = 'datetime';
}