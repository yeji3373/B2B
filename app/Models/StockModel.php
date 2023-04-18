<?php
namespace App\Models;
use CodeIgniter\Model;

class StockModel extends Model {
  protected $table = 'stocks';
  protected $primaryKey = 'id';
  protected $useSoftDeletes = false;

  protected $allowedFields = [];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updatedField = 'updated_at';
  protected $dateFormat = "datetime";


  public function stockJoin() {
    return $this->join("stocks_detail", "stocks_detail.stocks_id = {$this->table}.id", "left outer");
  }

  public function stocksJoin() {
    $this->join("stocks_detail", "stocks_detail.stocks_id = {$this->table}.id", "left outer")
          ->join("stocks_req", "stocks_req.stocks_id = stocks.id AND stocks_req.stock_id = stocks_detail.id", "left outer");

    return $this;
  }
}