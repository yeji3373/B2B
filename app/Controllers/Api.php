<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Config\Services;
use App\Models\CountryModel;
use App\Models\BrandModel;
use App\Models\ProductModel;
use App\Models\BuyerModel;
// use CodeIgniter\Database\RawSql;

class Api extends ResourceController {
  use ResponseTrait;
  protected $data;
  protected $format = 'json';

  public function __construct() {
    helper('merge');
    helper('auth');
  }

  public function __remap(...$params) {
    $method = $this->request->getMethod();
    $params = [($params[0] !== 'index' ? $params[0] : false)];
    $this->data = $this->request->getJSON();

    if (method_exists($this, $method)) {
        return call_user_func_array([$this, $method], $params);
    } else {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
  }

  // public function index()
  // {
  //     return view('welcome_message');
  //     // return view('dash/main');
  // }

  // public function getProduct() {
  //   current_user();
  // }

  public function getCountry() {
    $countries = new CountryModel();
    $where = [
      'region_id'   => $this->request->getVar('region_id'),
      'available'   => 1
    ];
    
    $this->data = $countries->where($where)->findAll();
    return $this->respond($this->data);
  }

  public function getLikeBrandName() {
    $brands = new BrandModel();
    $default = [
      'available'   => '1'
    ];

    $this->data = $brands
                  ->where($default)
                  ->like('brand_name', $this->request->getVar('brand_name'))
                  ->get()
                  ->getResultArray();
    return $this->respond($this->data);
  }

  public function getBrands() {
    $brands = new BrandModel();
    $default = [
      'available'   => '1'
    ];
    $orderBy = '';
    $where = array_merge_return($default, $this->request->getVar());

    $this->data = $brands
                  ->where($where)
                  ->orderby($orderBy)
                  ->get()
                  ->getResultArray();
    return $this->respond($this->data);
  }

  // public function getBuyers() {
  //   $buyers = new BuyerModel();

  //   $buyers->where('available', 1);
    
  //   if ( !empty($this->request->getVar('buyerId')) ) {
  //     $buyers->where(['id' => $this->request->getVar('buyerId')]);
  //   }

  //   $this->data = $buyers->first();
  //   return $this->data;
  // }

  function productSelect() {
    $products = new ProductModel();
    $this->data = $products->selects()->select(['name_en', 'name'])->findAll();
    echo $products->getLastQuery(); 
    echo "<br/><br/>";
    return $this->respond($this->data);
  }
}
