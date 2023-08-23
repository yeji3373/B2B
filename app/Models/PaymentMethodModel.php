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

  // public function paymentDeposit() {
  //   $this->select("{$this->table}.*")
  //       ->join('deposit', 'deposit.payment_id = payment_method.id AND deposit.available = 1', 'LEFT OUTER')
  //       ->where('available', 1);
  //   return $this;
  // }
}