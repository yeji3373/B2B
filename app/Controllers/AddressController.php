<?php
namespace App\Controllers;

use Auth\Models\UserModel;
use App\Models\BuyerAddressModel;

class AddressController extends BaseController {
  public $addressId = NULL;
  
  public function __construct() {
    $this->address = new BuyerAddressModel();
  }

  public function index() {
    $this->addressConduct();
  }

  public function getAddress() {

  }

  public function addressConduct($data = []) {
    if ( is_null($this->request) ) {
      if ( !empty($data) ) $req = $data;
      else return NULL;
    } else $req = $this->request->getVar('address');

    $req['buyer_id'] = session()->userData['buyerId'];

    if ( $this->address->save($req) ) {
      $this->addressId = $this->address->getInsertID();
    } 
    return $this->addressId;
  }

  public function addressOperate() {
    $code = 200;
    $msg = '';
    $type = '';
    $data = $this->request->getVar('address');

    $address = $this->address->where(['buyer_id' => session()->userData['buyerId'], 'idx' => $data['idx']]);

    if ( !empty($address) ) :
      if ( $data['oper'] == 'del') {
        $type = 'Deleted';
        $this->address->where($address)->delete();
      } else if ($data['oper'] == 'edit') {
        unset($data['idx']);
        $type = 'Edit';
        $this->address
              ->set($data)
              ->update();
      }

      if ( $this->address->affectedRows() ) {
        $code = 200;
        $msg = lang('Lang.addrOperate', ['type' => $type, 'result' => 'success']);
      } else {
        $code = 500;
        $msg = lang('Lang.addrOperate', ['type' => $type, 'result' => 'error']);
      }
    else :
      $code = 500;
      $msg = lang('Lang.addrOperate', ['type' => $type, 'result' => 'error']);
    endif;

    // if ( $this->request->isAJAX() ) {
    return json_encode(['code' => $code, 'Msg' => $msg]);
    // }
  }
}