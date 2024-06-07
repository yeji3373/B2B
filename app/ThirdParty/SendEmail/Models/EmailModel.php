<?php
namespace SendEmail\Models;

use CodeIgniter\Model;

class EmailModel extends Model {
  protected $table = 'email';
  protected $primaryKey = 'idx';
  protected $useSoftDeletes = false;

  protected $allowedFields = [];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $dateFormat = 'datetime';
}