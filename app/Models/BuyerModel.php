<?php
namespace App\Models;

use CodeIgniter\Model;

class BuyerModel extends Model {
  protected $table = 'buyers';
  protected $primaryKey = 'id';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    'name', 'business_number', 'region_ids', 'countries_ids', 
    'address', 'city', 'country_id', 'zipcode',
    'phone', 'certificate_business'
  ];
  
  protected $useTimestamps = true;
  protected $createdField  = 'created_at';
	protected $updatedField  = 'updated_at';
  protected $dateFormat  	 = 'datetime';

  protected $default = ["buyers.available" => 1];

  public function buyers() {
    return $this->where($this->default);
  }

  public function buyerJoin() {
    if ( !empty(session()->userData['buyerId']) ) {
      $this->default[$this->table.'.id'] = session()->userData['buyerId'];
    }
    return $this
            ->select("{$this->table}.*, buyers_currency.cRate_idx, buyers_currency.available AS buyerCurrency_available")
            ->join('buyers_currency', 'buyers_currency.buyer_id = '.$this->table.'.id', 'left outer')
            // ->join('currency', 'currency.idx = buyer_currency.currecy_idx')
            // ->join('currency_code', 'currency_code.idx = currency.currency_code_idx');
            ->where($this->default);
  }
}