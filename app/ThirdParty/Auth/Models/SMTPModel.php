<?php namespace Auth\Models;

use CodeIgniter\Model;

class SMTPModel extends Model {
	protected $table 			= 'smtp';
	protected $primaryKey = 'id';
	protected $useAutoIncrement = true;

	protected $allowedFields = ['smtp_host', 'smtp_user', 'smtp_pwd', 'updated_at', 'created_at'];

	protected $useTimestamps = true;
	protected $createdField = 'created_at';
	protected $updatedField = 'updated_at';
	protected $dateFormat 	= 'datetime';
}