<?php
namespace App\Models;

use CodeIgniter\Model;

class BrandModel extends Model {
  protected $table = 'brand';
  protected $primaryKey = 'brand_id';
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    // 'brand_name', 'brand_logo_src', 'own_brand', 'excluded_countries', 'available'
  ];

  protected $useTimestamps = true;
  protected $createdField = 'brand_registration_date';
  protected $dateFormat = 'datetime';


  public function getBrand(Array $sql = array()) {
    $select = '*';
    $where = ['available' => 1];
    $orderby = 'own_brand DESC, brand_name ASC, brand_id ASC';

    if ( !empty($sql) ) {
       if ( !empty($sql['where']) ) $where = array_merge($where, $sql['where']);
       if ( !empty($sql['orderby']) ) $orderby = $sql['orderBy'];
       if ( !empty($sql['select']) ) $select = $sql['select'];
    }

    $this->select($select)->where($where)->orderBy($orderby);
    return $this;
  }
}