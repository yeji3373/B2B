<?php
namespace Cafe24\Models;

use CodeIgniter\Model;

class Cafe24IpModel extends Model {
  protected $table      = 'cafe24_available_ip';
	protected $primaryKey = 'idx';

  protected $useAutoIncrement = true;
	protected $useSoftDeletes = false;

	protected $allowedFields = [
    'ip',
		'ip_nation', 'own_ip', 'corp_name', 'updated_at'
	];

  protected $useTimestamps = true;
  protected $createdField  = '';
	protected $updatedField  = 'updated_at';
	protected $dateFormat  	 = 'datetime';

}