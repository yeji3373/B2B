<?php
namespace Paypal\Config;

use CodeIgniter\Config\BaseConfig;
use Config\Services;

class Paypal extends BaseConfig {
  public $sandbox = false; // TRUE:test계정 사용하기 FALSE:LIVE 계정 사용하기

  protected $sandboxURL = 'https://sandbox.paypal.com';
  protected $liveURL = 'https://api.paypal.com';

  protected $sandBoxBaseUrl = 'https://api-m.sandbox.paypal.com';
  protected $liveBaseUrl = 'https://api-m.paypal.com';
 
  /* v2plus1v@hotmail.com 계정일 때 */
  protected $sandBoxEmail = 'sb-amddg11313806@business.example.com';
  protected $sandBoxClientId = 'AVJV4jd9LkeLypYumyAPrbl3DAYOOdFQws0tVBjHB9DKSLoGETmDFa6B0c4BIGok8_Q211dnDMz-yctu';
  protected $sandBoxClientSecret = 'EBujP4D-WeYXLQLVIPARWwaNT73MWIFb0QUjwTVcjtqOTFOfBCsnWGrWb3Oa122jzdWGmO3eq_tgHw4x';

  protected $liveEmail = 'v2plus1v@hotmail.com';
  protected $liveClientId = 'AfWhStgsSYVOBBJtWRcJ2CNjSn7uVwGW-bditn4ZL4KXxqJJaemgdzpjK1ckk-DM7eEh5cE1jgEX5GT1';
  protected $liveClientSecret = 'ELMz-SDSkIAXEX8q2t1qmfV8fLxsfgE6aXEtsJ6e_it2qEzPw8SQ_xYv-TsGlZv9kSTPknrGZAu7sPKL';
  /* v2plus1v@hotmail.com 계정일 때 */

  protected $needNewToken = true;
  public $accessToken;
  public $accessTokenExpiry;

  protected $lastError;

  protected $buttonPath;
  protected $submitBtn;

  public $clientID;
  public $clientScret;
  public $baseUrl;

  public $paypalUrl = [
    'token'   => '/v1/oauth2/token',
    'invoice' => '/v2/invoicing/invoices',
  ];  
  public $invoiceViewer;

  public function __construct() {
    $this->curl = service('curlrequest');

    if ( $this->sandbox ) {
      $this->clientID = $this->sandBoxClientId;
      $this->clientScret = $this->sandBoxClientSecret;
      $this->baseUrl = $this->sandBoxBaseUrl;
      $this->invoicerEmail = $this->sandBoxEmail;
      $this->invoiceViewer = $this->sandboxURL.'/invoice/p/';
    } else {
      $this->clientID = $this->liveClientId;
      $this->clientScret = $this->liveClientSecret;
      $this->baseUrl = $this->liveBaseUrl;
      $this->invoicerEmail = $this->liveEmail;
      $this->invoiceViewer = $this->liveURL.'/invoice/p/';
    }

    if ( !empty($this->accessToken) || !empty($this->accessTokenExpiry) ) {
      if ( $this->accessTokenExpiry >= time() ) {
        $this->needNewToken = false;
      } else $this->needNewToken = true;
    } 

    if ( $this->needNewToken ) $this->getOauth();
  }

  protected function getOauth() {
    $oauth = $this->curl->post(
                $this->baseUrl.$this->paypalUrl['token'],
                [
                  'auth'        => [$this->clientID, $this->clientScret],
                  'debug'       => true,
                  'headers'     => ['Content-Type' => 'application/x-www-form-urlencoded'],
                  'form_params' => ['grant_type' => 'client_credentials']
                ]
              );

    if ( $oauth->getStatusCode() == 200 ) : 
      $this->needNewToken = false;

      $body = json_decode($oauth->getBody(), true);
      $this->accessToken = $body['access_token'];
      $this->accessTokenExpiry = time() + $body['expires_in'];
    endif;
  }
}