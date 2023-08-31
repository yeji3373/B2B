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

  public function getStocks() {
    $stocks = $this->db
                ->query("SELECT stocks.id AS stocks_id
                                , stocks.prd_id
                                , stocks.order_base
                                , stocks_detail.supplied_qty
                                , stocks_detail.supplied_qty AS available_stock
                        FROM stocks
                              , ( SELECT id, stocks_id, SUM(supplied_qty) AS supplied_qty FROM stocks_detail WHERE available = 1 GROUP BY stocks_id ) AS stocks_detail
                        WHERE stocks.id = stocks_detail.stocks_id")
                ->getResultArray();
    return $stocks;
  }
}