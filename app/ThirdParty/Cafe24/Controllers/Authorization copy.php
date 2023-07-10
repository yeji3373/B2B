<?php
namespace Cafe24\Controllers;

use CodeIgniter\Controller;
use Cafe24\Models\Cafe24Model;

class Authorization extends Controller {
  public $needToken = false;
  public $needCode = false;
  public $grantType = 'authorization_code';
  public $tokenType = 'code';

  public function __construct() {
    helper('date');
    $this->curl = \Config\Services::curlrequest();
    $this->config = config('Cafe24');
    $this->cafe24Model = new Cafe24Model;

    $this->cafe24 = $this->cafe24Model->first();
    if ( !empty($this->cafe24) ) {
      $this->config->access_token = $this->cafe24['access_token'];
      $this->config->access_token_expires_at = $this->cafe24['access_token_expires_at'];
      $this->config->refresh_token = $this->cafe24['refresh_token'];
      $this->config->refresh_token_expires_at = $this->cafe24['refresh_token_expires_at'];
    }
  }

  public function index() {
    // header("Access-Control-Allow-Origin:*");
    // return json_encode($this->cafe24);
  }

  public function access_code_request() {
    if ( !empty($this->cafe24) ) {
      $this->cafe24Model->where('id', $this->cafe24['id'])->delete();
    }
    $query = ['response_type' => 'code',
              'client_id'     => $this->config->client_id,
              'state'         => $this->config->state,
              'redirect_uri'  => $this->config->redirect_uri,
              'scope'         => $this->config->scope];
    
    return redirect()->to($this->config->base_url.'/api/v2/oauth/authorize?'.http_build_query($query));
  }

  public function auth_request() {
    if ( empty($this->request->getGet('code')) ) {
      print_r($this->config);
      echo '<br/>'.strtotime($this->config->access_token_expires_at).'<br/>';
      echo strtotime('NOW').'<br/>';
      print_r($this->cafe24);
      
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

    if ( $this->needCode ) $this->access_code_request();
    if ( $this->needToken ) {
      $response = $this->curl->post(
                            $this->config->base_url.'/api/v2/oauth/token',
                            [ 'auth'          =>  [$this->config->client_id, $this->config->client_secret],
                              'form_params'   =>  [ 'grant_type'      => $this->grantType,
                                                    'redirect_uri'    => $this->config->redirect_uri,
                                                    $this->tokenType  => $this->token]]);

      if ( $response->getStatusCode() === 200 ) {
        if ( strpos($response->header('content-type'), 'application/json') !== false ) {
          $body = json_decode($response->getBody());
        }

        if ( !empty($this->cafe24) ) $this->cafe24Model->where('id', $this->cafe24['id'])->delete();
        if ( $this->cafe24Model->save([ 'access_token' => $body->access_token,
                                        'access_token_expires_at' => $body->expires_at,
                                        'refresh_token' => $body->refresh_token,
                                        'refresh_token_expires_at' => $body->refresh_token_expires_at]) ) {
          return redirect()->to('/cafe24/carries');
        }
      }
    }
  }

  public function getCarriers() {
    header("Access-Control-Allow-Origin: *");
    // header('Content-Type: application/json; charset=utf-8');
    try {
    $response = $this->curl->get(
                        $this->config->base_url.'/api/v2/admin/carriers',
                        [
                          'debug'   => TRUE,
                          // 'auth'    => [$this->config->client_id, $this->config->client_secret],
                          'headers' => ['Content-Type'  => 'application/json',
                                        'Authorization' => 'Bearer '.$this->config->access_token,
                                        'Access-Control-Allow-Origin' => 'https://beautynetkr.cafe24.com'],
                          'query'   => ['shop_no' => $this->config->shop_no ]
                        ]
    );
    } catch ( \Exception $e) {
      // print_r($e);
      return redirect()->to('/cafe24/authorization');
    }
    
    $countries = [];
    if ( $response->getStatusCode() === 200 ) {
      // if ( strpos($response->header('content-type'), 'application/json') !== false ) {
      //   $body = json_decode($response->getBody());
      if ( strpos($response->header('content-type'), 'application/json') !== false ) {
        $body = json_decode($response->getBody());
      } else $body = $response->getBody();

      // print_r($body->carriers);

      // // return print_r(json_encode($body->carriers));
      foreach($body->carriers AS $i => $carriers ) :
        if ( !empty($carriers->shipping_fee_setting_detail->shipping_fee_setting_oversea) ) {
          if ( empty($countries) ) {
            $countries = $carriers->shipping_fee_setting_detail->shipping_fee_setting_oversea->shipping_country_list;
          } else {
            foreach($carriers->shipping_fee_setting_detail->shipping_fee_setting_oversea->shipping_country_list AS $country ) :
              // if ( array_search($country->country_code, array_column($countries, 'country_code')) == null ) {
              //   echo "?".array_search($country->country_code, array_column($countries, 'country_code'))."<br/>";
              //   array_push($countries, $country);
              //   print_r($country->country_code);
              //   echo '<br/>';
              // }
              if ( !in_array($country->country_code, array_column($countries, 'country_code')) ) {
                array_push($countries, $country);
              }              
            endforeach;
          }
        }
        // array_push($countries, $carriers->shipping_fee_setting_detail->shipping_fee_setting_oversea->shipping_country_list);
        // echo array_search($carriers->shipping_fee_setting_detail->shipping_fee_setting_oversea->shipping_country_list['country_code'], $countries);
        // print_r($carriers->shipping_fee_setting_detail->shipping_fee_setting_oversea->shipping_country_list);
        // echo '<Br/>';
        // print_r($carriers->shipping_fee_setting_detail);
        // echo "<br/>";
        // echo $carriers->shop_no.'<Br/>';
        // print_r($carriers);
        // echo '<br/>';
        // echo "<br/>";
      endforeach;
      // // // // return var_dump($body);
      // // echo "<br/>";
      // print_r($countries);
      // echo "<br/>";
      // // // echo array_search('BR', array_column($countries, 'country_code'));
      // // echo '<br/>';
      print_r(array_count_values(array_column($countries, 'country_code')));
      // echo "<br/>";
      // // print_r(array_column($countries, 'country_code'));
      // // echo "<br/>";
    }
  }
}