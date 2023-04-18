<?php
namespace App\Models;

use CodeIgniter\Model;

class OrdersReceiptModel extends Model {
  protected $table = 'orders_receipt';
  protected $primaryKey = 'receipt_id';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    'order_id', 'payment_url', 'receipt_type', 'payment_invoice_id', 'payment_invoice_number',
    'payment_status', 'rq_percent', 'rq_amount', 'due_amount', 'display'
  ];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updateField = 'updated_at';
  protected $dateFormat = 'datetime';
}