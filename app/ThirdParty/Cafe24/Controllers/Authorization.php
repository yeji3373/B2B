<?php
namespace Cafe24\Controllers;

use CodeIgniter\Controller;
use Cafe24\Models\Cafe24Model;

class Authorization extends Controller {
// class Authorization extends Cafe24InitController {
  public $needToken = false;
  public $needCode = false;
  public $grantType = 'authorization_code';
  public $tokenType = 'code';
  public $token;
  
  public $carriers;
  public $countries;

  public function __construct() {
    helper('date');
    $this->curl = \Config\Services::curlrequest();
    $this->config = config('Cafe24');
    $this->cafe24Model = new Cafe24Model;
    $this->uri = service('uri');

    $this->cafe24 = $this->cafe24Model->first();
    if ( !empty($this->cafe24) ) {
      $this->config->access_token = $this->cafe24['access_token'];
      $this->config->access_token_expires_at = $this->cafe24['access_token_expires_at'];
      $this->config->refresh_token = $this->cafe24['refresh_token'];
      $this->config->refresh_token_expires_at = $this->cafe24['refresh_token_expires_at'];
    }
  }

  public function access_code_request() {
    // if ( !empty($this->cafe24) ) {
    //   $this->cafe24Model->where('id', $this->cafe24['id'])->delete();
    // }
    
    $query = [ 'response_type' => 'code',
               'client_id'     => $this->config->client_id,
               'state'         => $this->config->state,
               'redirect_uri'  => $this->config->redirect_uri,
               'scope'         => $this->config->scope ];

    if ( !empty($this->request->getVar('return_uri')) ) $query['return_uri'] = $this->request->getVar('return_uri');
    if ( !empty($this->cafe24) ) $query['id'] = $this->cafe24['id'];

    return redirect()->to($this->config->base_url.'/api/v2/oauth/authorize?'.http_build_query($query));
  }

  public function auth_request() {
    print_r($this->request->getVar());
    if ( empty($this->request->getGet('code')) ) {
      print_r($this->config);
      echo '<br/>'.strtotime($this->config->access_token_expires_at).'<br/>';
      echo strtotime('NOW').'<br/>';
      print_r($this->cafe24);
      // echo "<br/><br/>";
      // // print_r(urldecode($this->uri->getQuery()));
      // echo "<br/><br/>";
      if ( empty($this->cafe24) ) return redirect()->to('/cafe24/accesscode?'.urldecode($this->uri->getQuery()));
      
      if ( strtotime($this->config->access_token_expires_at) < strtotime('NOW') ) {
        if ( strtotime($this->config->refresh_token_expires_at) >= strtotime('NOW') ) {
          $this->grantType = 'refresh_token';
          $this->tokenType = 'refresh_token';
          $this->token = $this->config->refresh_token;
          $this->needToken = TRUE;
        }
      } else {
        $this->needCode = TRUE;
      }
    } else {
      $this->tokenType = 'code';
      $this->token = $this->request->getGet('code');
      $this->needToken = TRUE;
    }

    echo $this->grantType.'<br/>';
    echo $this->tokenType.'<br/>';
    echo $this->token.'<br/>';
    if ( $this->needCode ) return redirect()->to('/cafe24/accesscode');
    if ( $this->needToken ) {
      try {
        $response = $this->curl->post(
                              $this->config->base_url.'/api/v2/oauth/token',
                              [ 
                                'headers'       =>  [ 'Content-Type'  => 'x-www-form-urlencoded'],
                                'auth'          =>  [ $this->config->client_id, $this->config->client_secret ],
                                'debug'         =>  true,
                                'form_params'   =>  [ 'grant_type'      => $this->grantType,
                                                      $this->tokenType  => $this->token,
                                                      'redirect_uri'    => $this->config->redirect_uri]
                              ]);
        if ( $response->getStatusCode() === 200 ) {
          if ( strpos($response->header('content-type'), 'application/json') !== false ) {
            $body = json_decode($response->getBody());
          }

          // if ( !empty($this->cafe24) ) $this->cafe24Model->where('id', $this->cafe24['id'])->delete();
          if ( $this->cafe24Model->save([ 'access_token'              => $body->access_token,
                                          'access_token_expires_at'   => $body->expires_at,
                                          'refresh_token'             => $body->refresh_token,
                                          'refresh_token_expires_at'  => $body->refresh_token_expires_at,
                                          'id'                        => $this->cafe24['id']]) ) {
            return redirect()->to('/cafe24/carries');
          }
        return print_r($response);
        $this->needToken = FALSE;
        } else return redirect()->to('/cafe24/accesscode');
      } catch( \Exeption $e ) {
        echo "error<br/>";
        print_r($e->getMessage());
        echo "<br/>";
        // return redirect()->to('/cafe24/accesscode');
      }
    }
  }
}