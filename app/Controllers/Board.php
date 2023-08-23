<?php
namespace App\Controllers;

use App\Models\BoardModel;
use CodeIgniter\I18n\Time;
use App\Models\NoticeModel;

class Board extends BaseController {
    public function __construct() {
        $this->notice = new NoticeModel();
        $this->board = new BoardModel();
    }

    public function index() {

    }

    public function getBoard($type_idx, $idx) {
        // echo $type_idx;
        // echo $idx;
        // print_r ($this->request->uri->getSegments());
        // var_dump($this->request->uri->getSegments());
        // echo '1 '.$this->request->uri->getSegment(0).'<br/></br/>';
        $this->data['board'] = $this->board($type_idx, $idx);
        return $this->basicLayout('/dash/board', $this->data);
    }

    public function board($type_idx, $idx) {
        return $this->board->readBoard($type_idx, $idx)->getResultArray();
    }
}
?>