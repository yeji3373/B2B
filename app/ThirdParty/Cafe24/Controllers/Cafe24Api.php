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

		// 접속 불가 국가 목록
		$bannedCountries = ['KR', 'JP'];

		// 해당 ip의 접속 허용 여부 (false 접속 불가 / true 접속 가능)
		$result['flag'] = false;

		$ipLookup = $this->ipcheck->ipLookup($thisIP);

		if($ipLookup['statusCode'] == 200){
			// ipLookup 시 국가코드가 안나오면 fail
			if(!empty($ipLookup['countryCode'])){
				$result = array_merge($result, $ipLookup);
				
				if(in_array($result['countryCode'], $bannedCountries)){
					// 접속 불가 국가지만 예외적으로 접속 허용하는 ip
					$available_ip = $this->cafe24Ip->where('ip', $thisIP)->first();
					if(!empty($available_ip)){
						$result['flag'] = true;
					}
				}else{
					$result['flag'] = true;
				}
			}else{
				$result['status'] = 'fail';
				$result['statusCode'] = 500;
			}
		}

		return $this->respond($result);
	}
}