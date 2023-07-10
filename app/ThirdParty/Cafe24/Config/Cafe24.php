<?php
namespace Cafe24\Config;

use CodeIgniter\Config\BaseConfig;

class Cafe24 extends BaseConfig {
  public $base_url = 'https://beautynetkr.cafe24api.com';
  public $client_id = '45icFc2YGBpryVwZjZBkdC';
  public $client_secret = 'eO7VKRENWyrMXpER0YfeyG';
  // public $client_id = 'z5YO10eOYaYHzpm27g7hTA';
  // public $client_secret = 'fOUrejmcxkNha1PayF32wG';
  public $service_key = 'iQkSRsqr4k2lHTWVm6iDbjQNY9Rpmi/rJbRcea7xcdE=';
  public $access_code = 'snRDf7StSihMb4vL1KbGqH';
  public $auth_code;
  
  public $state = 'MTIzNDU2Nzg=';
  public $redirect_uri = 'https://koreacosmeticmall.com/cafe24/authorization';
  public $shop_no = 2;
  public $grant_type = 'authorization_code';
  public $access_token;
  public $access_token_expires_at;
  public $refresh_token;
  public $refresh_token_expires_at;
  public $scope = 'mall.read_application,mall.write_application,mall.read_category,mall.write_category,mall.read_product,mall.read_collection,mall.read_order,mall.write_order,mall.read_community,mall.write_community,mall.read_customer,mall.read_notification,mall.write_notification,mall.read_salesreport,mall.read_shipping,mall.write_shipping,mall.read_analytics,mall.read_personal,mall.write_personal';

  public $allow_origin = ['https://beautynetkr.cafe24.com', 
                          'https://beautynetkr.cafe24.com/', 
                          'https://beautynetkorea.com/',
                          'https://beautynetkorea.com'];

  // https://beautynetkr.cafe24api.com/api/v2/oauth/authorize?response_type=code&client_id=45icFc2YGBpryVwZjZBkdC&state=MTIzNDU2Nzg=&redirect_uri=https://koreacosmeticmall.com/cafe24/authorization&scope=mall.read_application,mall.write_application,mall.read_category,mall.write_category,mall.read_product,mall.read_collection,mall.read_order,mall.write_order,mall.read_community,mall.write_community,mall.read_customer,mall.read_notification,mall.write_notification,mall.read_salesreport,mall.read_shipping,mall.write_shipping,mall.read_analytics
}