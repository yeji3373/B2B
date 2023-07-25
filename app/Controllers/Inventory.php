<?php
namespace App\Controllers;

use App\Models\RegionModel;
use App\Models\CountryModel;
use App\Models\RequirementModel;
use App\Models\BuyerAddressModel;
// use App\Models\BuyerCurrencyModel;

use App\Controllers\CartController;
use App\Controllers\AddressController;
use App\Controllers\Order;

class Inventory extends BaseController {
  public function __construct() {
    $this->address = new BuyerAddressModel();

    $this->CartController = new CartController();
    $this->AddressController = new AddressController();
    $this->OrderController = new Order();
  }

  public function requestInventoryCheck() {
    $country = new CountryModel();
    $requirement = new RequirementModel();

    if ( $this->CartController->checkMinimumAmount() === false ) {
      if ( $this->request->isAJAX()) {
        return json_encode(['error' => lang('Order.orderMinCheck', [$this->CartController->basedMinimumOrderVal])]);
      } 
      return redirect()->to(site_url('/order'))->with('error', lang('Order.orderMinCheck', [$this->CartController->basedMinimumOrderVal]));
    }

    $this->data['prevAddrList'] = $this->address->where('buyer_id', session()->userData['buyerId'])->orderBy('idx DESC')->findAll(0, 1);
    $this->data['regions'] = $country->findAll();
    $this->data['itus'] = $this->OrderController->getItus()->findAll();
    $this->data['requirements'] = $requirement->where('display', 1)->findAll();
    // $this->cartList();
    
    return view('order/InventoryCheck', $this->data);
  }

  public function requestInventory() {
    // if ( )
    $this->AddressController->addressConduct();
  }
}