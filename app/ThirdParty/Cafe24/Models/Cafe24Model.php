<?php
namespace Cafe24\Models;

use CodeIgniter\Model;

class Cafe24Model extends Model {
  protected $table      = 'cafe24';
	protected $primaryKey = 'id';

  protected $useAutoIncrement = true;
	protected $useSoftDeletes = false;

	protected $allowedFields = [
    'access_code',
		'access_token', 'access_token_expires_at', 
    'refresh_token', 'refresh_token_expires_at'
	];

  protected $useTimestamps = true;
  protected $createdField  = '';
	protected $updatedField  = 'updated_at';
  protected $deletedField = 'deleted_at';
	protected $dateFormat  	 = 'datetime';

}