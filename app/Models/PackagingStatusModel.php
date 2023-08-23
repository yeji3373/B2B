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


  public function getNextIdx($idx) {
    $this->select('(SELECT idx
                  FROM packaging_status AS B
                  WHERE packaging_status.order_by > B.order_by
                  ORDER BY B.order_by ASC
                  LIMIT 1) AS nextIdx')
        ->where('idx', $idx)
        ->first();
    return $this;
  }
}