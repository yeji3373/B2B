<?php
namespace Auth\Config;

use CodeIgniter\Config\BaseConfig;

class Auth extends BaseConfig {
//--------------------------------------------------------------------
// Views used by Auth Controllers
//--------------------------------------------------------------------

  public $views = [
    'login' => 'Auth\Views\login',
    'register' => 'Auth\Views\register',
    'forgot-password' => 'Auth\Views\forgot',
    'reset-password' => 'Auth\Views\reset',
    'account' => 'Auth\Views\account',
    'delete' => 'Auth\Views\delete'
  ];

  // Layout for the views to extend
  public $viewLayout = 'Auth\Views\layout';
}