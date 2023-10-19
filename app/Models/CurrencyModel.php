<?php
namespace App\Models;

use CodeIgniter\Model;

class CurrencyModel extends Model {
  protected $table = 'currency';
  protected $primaryKey = 'idx';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes = false;

  protected $allowedFields = [];
  
  protected $useTimestamps = true;
  protected $createdField  = '';
	protected $updatedField  = '';
  protected $dateFormat  	 = '';

  protected $default = ['currency.available' => 1];

  public function currencyJoin() {
    return $this
            ->select("{$this->table}.*")
            ->select('currency_rate.cRate_idx
                    , currency_rate.exchange_rate
                    , currency_rate.default_set')
            ->join('currency_rate', 'currency_rate.currency_idx = '.$this->table.'.idx')
            ->where($this->default)
            ->where('currency_rate.available', 1)
            ->where('currency_rate.default_set', 1);
            // ->where('currency_rate.available', 1);
  }
}