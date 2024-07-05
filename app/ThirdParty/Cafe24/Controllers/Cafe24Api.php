<?php
namespace Cafe24\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Ipcheck\Controllers\IpcheckController;
use Cafe24\Models\Cafe24IpModel;

class Cafe24Api extends ResourceController {
	use ResponseTrait;
	protected $format = 'json';

	public function __construct() {
		$this->ipcheck = new IpcheckController();
		$this->cafe24Ip = new Cafe24IpModel();
    $this->result = [];
	}

	public function index() {

	}

	public function getIp() {
    // $.ajax({
    //   method: 'get',
    //   headers: {'Access-Control-Allow-Origin': 'https://www.beautynetkorea.com'}, // headers 반드시 기억하기
    //   url: 'https://koreacosmeticmall.com/cafe24/get_ip',
    //   dataType: 'json',
    //   async: false,
    //   success: function(data) {
    //       console.log('data', data);
    //   }
    // });
    // 호출할 때 반드시 headers 기억하기

		$thisIP = $_SERVER['REMOTE_ADDR'];

		// 접속 불가 국가 목록
		$bannedCountries = ['KR', 'JP'];

		// 해당 ip의 접속 허용 여부 (false 접속 불가 / true 접속 가능)
		$this->result['flag'] = false;
		// 자사 ip 여부
		$this->result['bnk_ip'] = false;

		$ipLookup = $this->ipcheck->ipLookup('5.181.235.154');

		if($ipLookup['statusCode'] == 200){
			// ipLookup 시 국가코드가 안나오면 fail
			if(!empty($ipLookup['countryCode'])){
				$this->result = array_merge($this->result, $ipLookup);
				
				if(in_array($this->result['countryCode'], $bannedCountries)){
					// 접속 불가 국가지만 예외적으로 접속 허용하는 ip
					$available_ip = $this->cafe24Ip->where('ip', '5.181.235.154')->first();
					if(!empty($available_ip)){
						$this->result['flag'] = true;
						if($available_ip['own_ip']){
							$this->result['bnk_ip'] = true;
						}
					}
				}else{
					$this->result['flag'] = true;
				}
			}else{
				$this->result['status'] = 'fail';
				$this->result['statusCode'] = 500;
			}
		}
    // $result['header'] = json_encode($request->getHeader('Origin'));
    return $this->respond($this->result);
	}
}