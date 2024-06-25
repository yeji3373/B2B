<?php
namespace App\Models;

use CodeIgniter\Model;

class CurrencyRateModel extends Model {
  protected $table = 'currency_rate';
  protected $primaryKey = 'cRate_idx';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes = false;

  protected $allowedFields = [];
  
  protected $useTimestamps = true;
  protected $createdField  = '';
	protected $updatedField  = '';
  protected $dateFormat  	 = '';

  protected $default = ['currency_rate.available' => 1];

  public function currency_rate() {
    return $this->where($this->default);
  }
}