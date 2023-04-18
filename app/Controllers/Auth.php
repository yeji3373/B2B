<?php
namespace App\Controllers;

use Config\Email;
use Config\Services;
use App\Models\UserModel;

class Auth extends BaseController {
  //https://oops4u.tistory.com/1571
  public function __construct() { // class 호출 시 처음에 자동으로 호출하는 함수(생성자)
    $this->users = new UserModel();
  }

  public function __remap() { 
    // 함수 호출 재매핑
  }

  public function __output() {
    // 웹브라우저로 마지막 렌더링된 데이터 출력하는 view와 output class가 있음
    // 마지막 데이터가 웹브라우저로 보내지기 전에 처리해야 할 것이있다면 __output()함수를 추가
    // __output()함수가 있으면 마지막 데이터가 보여지기 전에 항상 호출 됨
  }

  public function index() {
    echo "session <br/>";
    // print_r ($this->session);
    // print_r (session());
    if ( !session()->get('isLoggedIn')) {
      echo "aaaaaaaaaaaaaaaa";
      echo view('auth/login');
    }
    // $this->template('auth/login', []);
  }

  public function login() {
    $userID = $this->request->getVar('managerID');
    $password = $this->request->getVar('password');

    $user = $this->users->where('user_identifier', $userID)->first();
    
    if ( $user ) {
      $password_check = $user['user_pw'];
      $verify_pass = password_verify($password, $password_check);

      if ( $verify_pass ) {
        // $role = $this->roleManager->roleID($manager['MANAGER_NO']);
        // $menus = $this->roleMenu->getRoleMenuAll($role);
      
        $auth = [
          'mId'         => $user['user_identifier'],
          // 'cNo'         => $user['COMPANY_NO'],
          // 'menus'       => $menus,
          'isLoggedIn'  => TRUE
        ];
        session()->set($auth);

        return redirect()->to(site_url('home'));
        // return view('home');
        // return redirect()->to(site_url('login'));
      } else {
        echo "<script>alert('아이디 혹은 비밀번호가 일치하지 않음.');"
              ."window.location.href='".(site_url('auth'))."'
              </script>";
      }
    } else {
      // return redirect()->to(site_url('/'));
      return redirect()->to(site_url('auth'));
    } 
  }

  public function logout() {
    // session()->destroy();
    // return redirect()->to(site_url('auth'));
    // $this->session->remove(['isLoggedIn', 'userData']);
    session()->remove(['isLoggedIn', 'userData']);
    return redirect()->to(site_url('auth'));
  }

  public function withdrawal() {
    // $this->manager->where('MANAGER_ID', $session->get())
  }
}