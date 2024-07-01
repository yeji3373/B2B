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
	}

	public function index() {

	}

	public function requestHeaders() {
		header('Access-Control-Allow-Credentials: TRUE');
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Methods: GET, POST');
	}

	public function getIp() {
		$this->requestHeaders();

		$thisIP = $_SERVER['REMOTE_ADDR'];
		
		$result = []; 
		$ipLookup = $this->ipcheck->ipLookup($thisIP);
		// print_r($ipLookup);
		// ipLookup 에러처리를...
		// 국가 값 있는지 확인 후 오류 처리
		if($ipLookup['statusCode'] == 200){
			if(!empty($ipLookup['countryCode'])){
				$result = ['statusCode' => 200, 'data' => $ipLookup];
			}else{
				$result = ['statusCode' => 500, 'msg' => 'There is no country code'];
			}
		}
		// var_dump($accept);

		$available_ips = $this->cafe24Ip->findAll();
		// print_r($available_ips);
		// foreach($available_ips as $k => $ip){
		// 	// var_dump($ip['idx']);
		// 	$ipLookup = $this->ipcheck->ipLookup($ip['ip']);
		// 	if(!empty($ipLookup['countryCode'])){
		// 		$data = ['idx' => $ip['idx'], 'ip_nation' => $ipLookup['countryCode']];
		// 		$this->cafe24Ip->save($data);
		// 	}
		// }
		$flag = false;
		foreach($available_ips as $k => $ip){
			if($ip['ip'] == $thisIP){
				$flag = true;
			}
		}

		$result['flag'] = $flag;

		return $this->respond($result);
	}
}