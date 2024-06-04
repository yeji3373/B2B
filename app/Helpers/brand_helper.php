<?php
use Config\Services;
use App\Models\BrandModel;

if ( !function_exists('brands') ) {
  function brands(Array $sql = array()) {
    $brandModel = new BrandModel();

    $brands = $brandModel->getBrand($sql)->findAll();
    return $brands;
  }

  function brand(Array $sql = array()) {
    $brandModel = new BrandModel();

    $brand = $brandModel->getBrand($sql)->first();
    return $brand;
  }
}
