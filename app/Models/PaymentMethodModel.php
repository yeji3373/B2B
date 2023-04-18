<?php
namespace App\Models;

use CodeIgniter\Model;

class PaymentMethodModel extends Model {
  protected $table = 'payment_method';
  protected $primaryKey = 'id';
  protected $useSoftDeletes = false;

  protected $allowedFields = [];

  protected $useTimestamps = true;
  protected $createdField = '';
}