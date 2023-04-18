<?php
namespace App\Models;

use CodeIgniter\Model;

class NoticeModel extends Model {
  protected $table = 'notice';
  protected $primaryKey = 'idx';
  protected $useSoftDeletes = false;

  protected $useTimestamps = true;
  protected $createdField = 'create_at';
  protected $updatedField = 'update_at';
  protected $dateFormat = 'datetime';
}