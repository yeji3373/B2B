<?php
namespace App\Models;

use  CodeIgniter\Model;

class UserModel extends Model {
  protected $table = 'users';
  protected $primaryKey = 'idx';
  
  protected $useAutoIncrement = true;

  protected $returnType = 'array';
  // protected $useSoftDeletes = true;
   
  protected $allowedFields = [
    'vendor_id', 'user_identifier', 'user_pw'
  ];

  protected $dynamicRules = [
    'registration'  => [
      'user_name'       => 'required|min_length[2]',
      'user_email'      => 'required|valid_email|is_unique[users.user]'
    ],
    'updateAccount' => [
      'user_identifier' => 'required',
      'user_pw'         => 'required',
    ]
  ];

  public function getUser() {
    // $this->whereIn()
    return $this->findAll();
  }

  /* 
  * 유효성 검사 규칙 찾기
  */
  protected function getRule(string $rule) {
    return $this->dynamicRules[$rule];
  }

  /* 
  * 필드 유효성 검사 후 insert/update 전에 암호화
  */
  protected function hashPassword(array $data) {
    if ( !isset($data['data']['password'])) return $data;

    $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
    unset($data['data']['password']);
    unset($data['data']['password_confirm']);

    return $data;
  }
}