<?php
namespace Cafe24\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Cors implements FilterInterface {
  public function before(RequestInterface $request, $arguments = null) {
  }

  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
    helper('Cafe24');
    setHeaders();
  }
}