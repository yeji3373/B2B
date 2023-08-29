<?php
namespace App\Models;

use CodeIgniter\Model;

class RequirementRequestModel extends Model {
  protected $table = 'requirement_request';
  protected $primaryKey = 'idx';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    'order_id', 'order_detail_id', 'requirement_id', 'requirement_detail'
  ];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updateField = 'updated_at';
  protected $dateFormat = 'datetime';

  function test() {
    $result = $this->where('order_id', 1)->findAll();

    foreach($result as $a ) {
      if ( !empty($a['requirement_option_ids']) ) :
        $ids = explode(",", $a['requirement_option_ids']);
        $temp = NULL;
        foreach($ids AS $i => $id) :
          if ( $i > 0 ) :
            $temp .= ", ".$id;
          else :
            $temp .= $id;
          endif;          
        endforeach;

        echo $temp;

        $options = $this->db->query(
                  "SELECT *
                  FROM requirement_option
                  WHERE idx IN ({$temp})")
                  ->getResultArray();
      endif;
    }

    return $options;
  }
}