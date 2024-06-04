<?php
namespace App\Models;
use CodeIgniter\Model;

class ProductPriceModel extends Model {
  protected $table = 'product_price';
  protected $primaryKey = 'idx';
  protected $useSoftDeletes = false;

  protected $allowedFields = [];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updatedField = 'updated_at';
  protected $dateFormat = "datetime";

  protected $default = [ 'available' => 1];


  public function get_product_price($sql = array()) {
    $select = '';
    $where = $this->default;
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