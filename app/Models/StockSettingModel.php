<?php
namespace App\Models;
use CodeIgniter\Model;

class StockSettingModel extends Model {
  protected $table = 'stock_settings';
  protected $primaryKey = 'id';

  protected $useSoftDeletes = false;

  protected $allowedFields = [];

  protected $useTimestamps = true;
  protected $createdField = '';
  protected $updatedField = '';
  protected $deletedField = '';
  protected $dateFormat = "datetime";
}