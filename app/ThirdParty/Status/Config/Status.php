<?php
namespace Status\Config;

use CodeIgniter\Config\BaseConfig;

define('IMAGEURL', 'https://beautynetkorea.daouimg.com');

class Status extends BaseConfig {  
  public function imageSrc($url = '', Array $src = []) {
    $imageSrc;
    
    switch($url) {
      case 'brand' :
        $imageSrc = IMAGEURL."/b2b/{$src['brand']}/{$src['name']}";
        break;
      default :
        $imageSrc = IMAGEURL."/b2b/documents/common/no-image.png";
        break;
    }

    return $imageSrc;
  }

  public $deliveryCode = [
    0 => 'Calculating shipping cost', // 택배비 산정중
    100 => '산정완료',
  ];

  public $paymentStatus = [
    0 => 'Waiting',
    100 => 'Paid',
    -100 => 'Cancelled',
    -200 => 'Refunded'
  ];

  public function paymentStatus($i = null) {
    $status = '';
      
    switch($i) {
      case 0 :
        $status = 'Waiting'; // 결제 대기
        break;
      case 100 :
        $status = 'Paid';    // 결제 완료
        break;
      case -100: 
        $status = 'Cancelled';  // 결제 취소
        break;
      case -200 :
        $status ='Refunded';  // 환불
        break;
      default:
        $status = 'error';
        break;
    }

    return $status;
  }
}