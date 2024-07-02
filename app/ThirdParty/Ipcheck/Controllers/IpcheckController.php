<?php
namespace Ipcheck\Controllers;

use CodeIgniter\Controller;

class IpcheckController extends Controller {

	protected $key = 'PvE6F2vw1kVEUTJYtbx6';

	public function __construct() {
		$this->curl = \Config\Services::curlrequest();
	}

	public function ipLookup($ip = '') {
		
		if(empty($ip)){
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		$response = $this->curl->get(
			'https://extreme-ip-lookup.com/json/'.$ip.'?key='.$this->key);
		
		// var_dump('https://extreme-ip-lookup.com/json/'.$ip.'?key='.$this->key);
		// 이 $response 는 무조건 json 타입을 준다. 이게 php 에선 object 자료형이고.
		// $response->body 는 array로 형변환 가능
		// statusCode 는 무조건 200 을 return 한다.
		$result = [];

		$result = json_decode($response->getBody());

		$result->statusCode = $response->getStatusCode();

		// var_dump((array) $result);

		return (array) $result;
	}
}