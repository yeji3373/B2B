<?php namespace Auth\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
	protected $table      = 'users';
	protected $primaryKey = 'idx';

	protected $returnType = 'array';
  protected $useAutoIncrement = true;
	protected $useSoftDeletes = false;

	// this happens first, model removes all other fields from input data
	protected $allowedFields = [
		'buyer_id', 'email', 'name', 'password', 'active', 'reset_hash', 'reset_expires'
	];

	protected $useTimestamps = true;
	protected $createdField  = 'created_at';
	protected $updatedField  = 'updated_at';
	protected $dateFormat  	 = 'datetime';

	protected $validationRules = [];

	// we need different rules for registration, account update, etc
	protected $dynamicRules = [
		'registration' => [
			'name'              => 'trim|required|min_length[2]',
			'email'             => 'trim|required|valid_email|is_unique[users.email]',
			// 'password'			    => 'trim|required|min_length[5]|max_length[20]|regex_match[/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{5,20}$/]',
      // 'password'			    => 'trim|required|min_length[5]|max_length[20]|regex_match[/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{5,20}$/]',
			'password_confirm'	=> 'trim|matches[password]'
		],
		'updateAccount' => [
			'id'      => 'trim|required|is_natural_no_zero',
			'name'	  => 'trim|required|min_length[2]'
		],
		'changeEmail' => [
			'id'			      => 'trim|required|is_natural_no_zero',
			'new_email'		  => 'trim|required|valid_email|is_unique[users.email]',
			'activate_hash'	=> 'trim|required'
		]
	];

	protected $validationMessages = [
    // 'registration' = [
    //   'password_confirm' => 
    // ]
  ];

	protected $skipValidation = false;

	// this runs after field validation
	protected $beforeInsert = ['hashPassword'];
	protected $beforeUpdate = ['hashPassword'];


  //--------------------------------------------------------------------

  /**
  * Retrieves validation rule
  */
	public function getRule(string $rule)	{
		return $this->dynamicRules[$rule];
	}

  //--------------------------------------------------------------------

  /**
  * Hashes the password after field validation and before insert/update
  */
	protected function hashPassword(array $data) {
		if (! isset($data['data']['password'])) return $data;

		$data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
		// unset($data['data']['password']);
		unset($data['data']['password_confirm']);

		return $data;
	}

  //--------------------------------------------------------------------
  
  /* 
  * getUser index
  */
  public function getUserIndex($data) {
    if ( !isset($data) ) return null;

    $userIdx = $this->select('idx')->where('email', $data)->first();
    return $userIdx['idx'];
  }
}