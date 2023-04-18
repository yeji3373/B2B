<?php
namespace App\Models;

use CodeIgniter\Model;

class BuyerCurrencyModel extends Model {
  protected $table = 'buyers_currency';
  protected $primaryKey = 'id';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    'buyer_id', 'currency_id'
  ];
  
  protected $useTimestamps = true;
  protected $createdField  = 'created_at';
	protected $updatedField  = 'updated_at';
  protected $dateFormat  	 = 'datetime';
}