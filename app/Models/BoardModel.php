<?php
namespace App\Models;

use CodeIgniter\Model;

class BoardModel extends Model {
    protected $table = 'board';
    protected $primaryKey = 'idx';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $dateFormat    = 'datetime';

    public function readBoard($type_idx, $idx) {
        return $this->db->query(
            "SELECT board.*
                    , board_type.name_kr AS type_name_kr
                    , board_type.name_en AS type_name_en
                    , board_type.available AS type_available
            FROM board
            LEFT JOIN board_type ON board.type_idx = board_type.idx
            WHERE board.type_idx = ? AND board.idx = ?"
            , array($type_idx, $idx)
        );
    }
}
?>