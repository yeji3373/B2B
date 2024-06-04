<?php
use App\Models\BuyerModel;

if ( ! function_exists('buyers') ) {
  function buyers() {
    $buyer = array();
    $buyerModel = new BuyerModel();

    // $buyer = $buyerModel->where();
    return $buyer;
  }
}