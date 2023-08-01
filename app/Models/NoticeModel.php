<?php
namespace App\Models;

use CodeIgniter\Model;

class NoticeModel extends Model {
  protected $table = 'board';
  protected $primaryKey = 'idx';
  protected $useSoftDeletes = false;

  protected $useTimestamps = true;
  protected $createdField = 'create_at';
  protected $updatedField = 'update_at';
  protected $dateFormat = 'datetime';

  public function board($where = array()) {
    $this->select('board.*
                  , board_type.name_kr AS type_name_kr
                  , board_type.name_en AS type_name_en
                  , board_type.available AS type_available')
          ->join('board_type', 'board.type_idx = board_type.idx')
          ->where($where);

    return $this;
  }
}