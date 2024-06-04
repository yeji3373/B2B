<?php
namespace App\Models;
use CodeIgniter\Model;

class SupplyPriceModel extends Model {
  protected $table = 'supply_price';
  protected $primaryKey = 'idx';
  protected $useSoftDeletes = false;

  protected $allowedFields = [];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updatedField = 'updated_at';
  protected $dateFormat = "datetime";

  function get_supply_price($sql = array()) {
    $select = '';
    $where = ['available' => 1];
    $orderBy = '';

    if ( !empty($sql) ) {
      if ( !empty($sql['select']) ) $select = $sql['select'];
      if ( !empty($sql['where']) ) $where = array_merge($where, $sql['where']);
      if ( !empty($sql['orderby']) ) $orderBy = $sql['orderby'];
      if ( !empty($sql['orderBy']) ) $orderBy = $sql['orderBy'];
    }
    
    if ( !empty($orderBy) ) $this->orderBy($orderBy);

    $this->select($select)->where($where);
    return $this;
  }
}