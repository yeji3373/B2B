<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Thirdparty\Auth;

class MyFilter implements FilterInterface {
  protected $auth;

  public function __construct() {
    $auth = \Config\Service::auth();
  }

  public function before(RequestInterface $request, $arguments = null) {
    // if (!session()->get('isLoggedIn')) {
    //   // // return redirect()->to('/login');
    //   return redirect()->to(site_url('auth'));
    // // } else {
    // //   echo session()->get('isLoggedIn');
    // // //   // echo "aaaaaaaaaa<br/>";
    // // //   // // print_r($request);
    // //   // return redirect()->to(site_url('/'));
    // }
  }

  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
    // Do something here
  }
}