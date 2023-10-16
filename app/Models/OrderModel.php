<?php
namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model {
  protected $table = 'orders';
  protected $primaryKey = 'id';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    'buyer_id', 'user_id', 'order_number',
    'request_amount', 'inventory_fixed_amount',
    'fixed_amount', 'order_amount',
    'currency_rate_idx', 'calc_currency_rate_id', 'currency_code',
    'taxation', 'payment_id', 'complete_payment',
    'address_id', 'order_fixed', 'available'
  ];

  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updateField = 'updated_at';
  protected $dateFormat = 'datetime';


  public function orderStatistics() {
    $query = "SELECT getDate.date, 
                    IFNULL(ROUND(getOrder.order_amount, 2), 0) AS subtotal_amount, 
                    IFNULL(ROUND(getOrder.request_amount, 2), 0) AS request_amount
              FROM ( 
                  SELECT DATE_FORMAT(created_at, '%Y-%m-%d') AS created_at_co, CAST(SUM(order_amount) AS DOUBLE) AS order_amount, CAST(SUM(request_amount) AS DOUBLE ) AS request_amount
                  FROM orders
                  WHERE available = 1 AND buyer_id = ".session()->userData['buyerId']."
                GROUP BY DATE_FORMAT(created_at, '%Y-%m-%d')
                ) getOrder
              RIGHT JOIN (
                        SELECT DATE(DATE_ADD(NOW(), INTERVAL -6 DAY)) + INTERVAL (a.a + (5 * b.a)) DAY as date
                        FROM 
                            (SELECT 0 as a UNION SELECT 1 UNION SELECT 2 UNION SELECT 3) as a,
                            (SELECT 0 as a UNION SELECT 1 UNION SELECT 2 UNION SELECT 3) AS b
                        GROUP BY date
              ) getDate ON getOrder.created_at_co = getDate.date
              WHERE getDate.date <= DATE_FORMAT(NOW(), '%Y-%m%-%d')";
    return $this->db->query($query);
  }
}
