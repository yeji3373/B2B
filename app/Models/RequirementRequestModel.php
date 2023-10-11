<?php
namespace App\Models;

use CodeIgniter\Model;

class RequirementRequestModel extends Model {
  protected $table = 'requirement_request';
  protected $primaryKey = 'idx';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    'idx', 'order_id', 'order_detail_id', 'requirement_id',
    'requirement_detail', 'requirement_selected_option_id'
  ];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updateField = 'updated_at';
  protected $dateFormat = 'datetime';

  function getRequirementOptions($orderId = NULL) {
    $result = $this
              ->select('requirement_request.idx')
              ->select('requirement_request.requirement_detail')
              ->select('requirement_request.order_detail_id')
              ->select('requirement_request.order_id')
              ->select('requirement_request.requirement_reply')
              ->select('requirement.requirement_en')
              ->select('requirement_request.requirement_id')
              ->select('requirement_request.requirement_option_ids')
              ->select('requirement_request.requirement_selected_option_id')
              ->join('requirement', 'requirement.idx = requirement_request.requirement_id')
              ->where('order_id', $orderId)
              ->orderby('requirement_request.order_detail_id')
              ->findAll();

    foreach($result as $j => $a ) {
      if ( !empty($a['requirement_option_ids']) ) :
        $ids = (string)$a['requirement_option_ids'];
        // $ids = explode(",", $a['requirement_option_ids']);
        // $temp = NULL;
        // foreach($ids AS $i => $id) :
        //   if ( $i > 0 ) :
        //     $temp .= ", ".$id;
        //   else :
        //     $temp = $id;
        //   endif;          
        // endforeach;
        // var_dump($ids);
        $options = $this->db->query(
                  "SELECT requirement_option.idx,
                          requirement_option.requirement_idx,
                          option_name,
                          option_name_en
                  FROM requirement_option
                  WHERE requirement_option.idx IN ({$ids})")
                  ->getResultArray();
        
        $result[$j]['options'] = $options;
      endif;
    }
    return $result;
  }

  public function requirementRequest($orderId = NULL) {
    return $this->db->query(
      "SELECT requirement_request.requirement_detail,
              requirement_request.order_detail_id,
              requirement_request.order_id,
              requirement_request.requirement_reply,
              requirement.requirement_en,
              requirement_request.requirement_id,
              requirement_option_ids
      FROM requirement_request
      JOIN requirement ON requirement.idx = requirement_request.requirement_id
      WHERE requirement_request.order_id = {$orderId}
      ORDER BY requirement_request.order_detail_id"
    );
  }
}