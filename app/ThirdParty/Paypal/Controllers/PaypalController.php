<?php
namespace Paypal\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class PaypalController extends Controller
{
  protected $config;

  protected $currencyCode;

  protected $default;

  protected $orderInfo;

  protected $isPaypal = false;
  protected $orderNumber;
  protected $payment = 500;

  protected $invoiceId;
  protected $invoiceNumber;

  public $result;

  public function __construct() {
    // helper('date');
    // helper('merge');
    helper('paypal');
    
    $this->curl = service('curlrequest');
    $this->config = config('Paypal');

    $this->invoiceUrl = $this->config->paypalUrl['invoice'];

    $this->header = [
      'Authorization: Bearer '.$this->config->accessToken,
      'Content-Type: application/json'
    ];
    $this->authorization();
  }

  public function authorization() {
    if ( $this->config->accessTokenExpiry >= time() ) {
      if ( empty($this->config->accessToken) ) {
        // echo "empty ";
        $this->config->getOauth();
      }
    }
  }

  public function paypal($req) {
    $this->orderInfo = $req;

    if ( $this->config->accessTokenExpiry >= time() ) {
      if ( empty($this->config->accessToken) ) {
        // echo "empty ";
        $this->config->getOauth();
      }
    }
    $this->makeInvoice();
  }

  protected function makeInvoice() {
    $header = array_merge($this->header, ['Prefer: return=representation']);
    $invoiceData = invoice_detail($this->orderInfo);
    
    $generate = $this->curlRequest(
      $this->config->baseUrl.$this->invoiceUrl,
      $header,
      $invoiceData,
      'POST'
    );
    // echo "<br/><br/>";
    // var_dump($generate);

    if ( $generate['code'] == 201 ) {   // successful request returns code 
      $this->invoiceId = $generate['data']['id'];
      $this->invoiceNumber = $generate['data']['detail']['invoice_number'];
      $this->sendInvoice();
    } else if ( $generate['code'] == 200 ) {
      $this->invoiceId = $generate['data']['id'];
      $this->invoiceNumber = $generate['data']['detail']['invoice_number'];
      $this->sendInvoice();
    } else {
      // print_r($generate);
      $this->result['error'] = 'invoice make error '.$generate['data']['name'].' : '.json_encode($generate['data']['details']);
      $this->result['code'] = $generate['code'];
      
      return $this->result;
    }
  }

  protected function sendInvoice() {
    $send = $this->curlRequest(
      $this->config->baseUrl.$this->invoiceUrl.'/'.$this->invoiceId.'/send',
      $this->header,
      '{"send_to_invoicer": true}',
      'POST'
    );
    // echo "<br/><br/>";
    // print_r($send);
    
    // successful code 202 : 인보이스 발행 날짜가 미래인 경우
    if ( $send['code'] == 200 || $send['code'] == 201 || $send['code'] == 202 ) { 
      $this->result['payment_url'] = $send['data']['href'];
      $this->result['payment_invoice_id'] = $this->invoiceId;
      $this->result['payment_invoice_number'] = $this->invoiceNumber;
      $this->result['data'] = $send;
    } else {
      $this->result['error'] = 'invoice send error '.$send['data']['name'].' : '.json_encode($send['data']['details']);
    }    
    $this->result['code'] = $send['code'];
    
    return $this->result;
  }

  public function showInvoiceDetail($invoiceId) {
    $detail = $this->curlRequest(
      $this->config->baseUrl.$this->invoiceUrl.'/'.$invoiceId,
      $this->header,
      []
    );

    if ( $detail['code'] == 200 || $detail['code'] == 201 ) {
      $this->result = [
        'data'  => $detail['data'],
        'code'  => $detail['code']
      ];
    } else $this->result = ['code' => $detail['code'], 'data' => $detail['data']['name'].' : '.json_encode($detail['data']['details'])];
    return $this->result;
  }

  public function test() { return "aaaaaaaaaaaaaaaaaaaa"; }

  // protected function curlRequest($url, array $header, $params, $config, $method = 'GET') {
  public static function curlRequest($url, array $header = [], $params, $method = 'GET') {
    $ch = curl_init();

    if ( isset($header['auth'])  ) {
      $config['auth'] = $header['auth'];
      unset($header['auth']);
    }

    if ( isset($header['form_params']) ) {
      $config['form_params'] = $header['form_params'];
      unset($header['form_params']);
    }

    curl_setopt($ch, CURLOPT_POST, $method === 'POST');
    if ($method === 'POST') {
      if ( !empty($params) ) curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    } else if ($method === 'GET') {
      // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      if ( !empty($params) && is_array($params) ) {
        $url .= '?'.http_build_query($params);
      }
    }

    if ( !empty($config['auth']) ) {
      curl_setopt($ch, CURLOPT_USERPWD, $config['auth'][0]);

      if ( !empty($config['auth'][1]) && strtolower($config['auth'][1]) == 'digest') {
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
      } else curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    }

    if ( !empty($config['form_params']) && is_array($config['form_params']) ) {
      $postFields = http_build_query($config['form_params']);

      curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, true);
    // // Disable @file uploads in post data.
    // $curlOptions[CURLOPT_SAFE_UPLOAD] = true;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);

    curl_close($ch);

    return [
      'code'    => $http_code,
      'data'    => json_decode($body, true),
      'header'  => $header
    ];
  }
}