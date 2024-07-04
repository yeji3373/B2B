<?php
namespace Cafe24\Controllers;

use CodeIgniter\Controller;

class BNK extends Controller {
  public function _remap(...$params) {
    $method = $this->request->getMethod();
    $params = [($params[0] !== 'index' ? $params[0] : false)];
    $this->data = $this->request->getJSON();

    d(apache_response_headers());
    // d(headers_list());
    
    // dd($this->request); // debug하고 die 그래서 dd
    // d($method); // dubug만 그래서 d
    // d($params);
    // debugging http://ci4doc.cikorea.net/testing/debugging.html 참고
    
    if (method_exists($this, $method)) {
        return call_user_func_array([$this, $method], $params);  // 1번방식. 호출 방식으로 함수 호출 예)get, post
        // return call_user_func_array([$this, $params[0]], $params);  // 2번방식. url에 맞게 함수 호출 예) 호출 url이 base_url()/cafe24/bnk/test 일 경우, test호출
    } else {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
  }
  
  public function test() { // 2번방식 호출
    echo 'test';
  }

  public function get() { // 1번방식 호출
    return;
    // return json_encode(['result' => 'get']);
  }

  public function post() { // 1번방식 호출
    echo 'post';
  }
}