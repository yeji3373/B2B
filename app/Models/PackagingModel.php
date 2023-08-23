<?php
namespace App\Models;

use CodeIgniter\Model;

class PackagingModel extends Model {
  protected $table = 'packaging';
  protected $primaryKey = 'idx';
  protected $useSoftDeletes = false;

  protected $allowedFields = ['order_id'];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $dateFormat = 'datetime';

  public function getAllPackagingStatus($where = []) {
    $this->join('packaging_detail', 'packaging_detail.packaging_id = packaging.idx')
        ->join('packaging_status', 'packaging_status.idx = packaging_detail.status_id')
        ->where('packaging_status.available', 1)
        ->where($where)
        ->orderBy('packaging_status.order_by ASC');

    return $this;
  }
}