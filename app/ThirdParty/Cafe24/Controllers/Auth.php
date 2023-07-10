<?php
namespace Cafe24\Controllers;

use CodeIgniter\Controller;
use Cafe24\Models\Cafe24Model;

class Auth extends Controller {
  public function __construct() {
    $this->curl = service('curlrequest');
    $this->config = config('Cafe24');
  }
  
  public function index() {
    $query = ['response_type' => 'code',
              'client_id'     => $this->config->client_id,
              'state'         => $this->config->state,
              'redirect_uri'  => $this->config->redirect_uri,
              'scope'         => $this->config->scope];
    return redirect()->to($this->config->base_url.'/api/v2/oauth/authorize?'.http_build_query($query));
  }
}