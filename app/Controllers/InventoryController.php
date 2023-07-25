<?php
namespace App\Controllers;

use App\Controllers\AddressController;

class InventoryController extends BaseController {

  public function __construct() {
    $this->AddressController = new AddressController();
  }

  
}