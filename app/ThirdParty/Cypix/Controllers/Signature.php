<?php
namespace Cypix\Controllers;

use CodeIgniter\Controller;
// use Config\Services;
use Paypal\Controllers\PaypalController;
// use Cypex\Config\Cypex;

class Signature extends Controller
{
  protected $signature;

  public function __construct() {
    helper('date');
    $this->curl = \Config\Services::curlrequest();
    $this->config = config('Cypix');
    // $this->body = [ "amount" => "120.00",
    //                 "currency" => "RUB",
    //                 "description" => "Order payment",
    //                 // "returnUrl" => base_url()."/cypixReturn",
    //                 "method" => "card",
    //                 "orderId" => "123456",
    //                 "customer" => [
    //                   "userId" => '',
    //                   "email" => 'jmyho59@gmail.com',
    //                   "ip" => "127.0.0.1", 
    //                   "countryCode" => "RU", 
    //                 ],
    //                 "creditCard" => [
    //                   "pan" => "1234123412341234", 
    //                   "cardHolder" => "CARD HOLDER", 
    //                   "expMonth" => 11, 
    //                   "expYear" => 2021, 
    //                   "cvc" => 123, 
    //                   "recurrentPaymentEnabled" => true
    //                 ],
    //                 "mc"  => [
    //                   "msisdn" => "",

    //                 ],
    //                 // "browserInfo" => [
    //                 //   "userAgent" => ,
    //                 //   "acceptHeader" => ,
    //                 //   "colorDepth" =>,
    //                 //   "language" =>,
    //                 //   "timezone" =>,
    //                 //   "javaEnabled" => ,
    //                 //   "screenHeight" =>,
    //                 //   "screenWidth" => ,
    //                 //   "windowWidth" => ,
    //                 //   "windowHeight" =>
    //                 // ]
    //               ];
    $this->body = [
        "amount" => "120.00",
        "currency" => "RUB",
        "description" => "Order payment",
        "returnUrl" => "http://example.com/return",
        "method" => "card",
        "orderId" => "123456",
        "customer" => [
          "userId" => 1111111,
          "ip" => '61.255.102.98',
          'email' => "jmyho59@gmail.com",
          'countryCode' => 'RU'
        ]
    ];
    $this->getSignature();
  }

  public function getSignature() {
    $salt = $this->config->salt;
    $secret = $this->config->secret;
    // $body = [ "amount" => 20,
    //                 "returnUrl" => "https://example.com/",
    //                 "description" => "TEST",
    //                 "method" => "card",
    //                 "orderId" => 123456  ];

    
    $signatureRaw = hash_hmac('sha1', json_encode($this->body). $salt, $secret, true);
    $this->signature = base64_encode($signatureRaw);

    // return $this->signature;
  }

  public function transaction() {
    $header = [
      'Content-Type' => 'application/json',
      'X-Signature' => $this->signature,
      'X-Serviceapikey' => $this->config->apiKey,
      'X-Salt' => $this->config->salt
      // // 'x-sdk-date: 20191115T033655Z',
      // 'Authorization: SDK-HMAC-SHA256 Access='.$this->config->salt.', SignedHeaders=content-type;host;x-sdk-date, Signature='.$this->signature
      // // Authorization: SDK-HMAC-SHA256 Access=QTWAOYTTINDUT2QVKYUC, SignedHeaders=content-type;host;x-sdk-date, Signature=7be6668032f70418fcc22abc52071e57aff61b84a1d2381bb430d6870f4f6ebe
    ];

    // echo strtotime('NOW');

    // $this->curl->head($header);
    // print_r($this->curl);
    $response = $this->curl->request('POST',
                    $this->config->cypixApiUrl."/gateway/form", 
                    [ 
                      'headers' => $header,
                      'form_params' => $this->body,
                      'debug' => true
                    ]);
  }

  public function cypixReturn() {
    echo "return";
  }
}