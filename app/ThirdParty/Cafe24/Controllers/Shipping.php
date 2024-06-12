<?php
namespace Cafe24\Controllers;

use Cafe24\Models\Cafe24Model;

class Shipping extends Cafe24InitController {
  public $carriers;
  public $countries;

  public function __construct() {
    helper('cafe24');
    $this->curl = \Config\Services::curlrequest();
    $this->config = config('Cafe24');
    $this->cafe24Model = new Cafe24Model;
    $this->uri = service('uri');
        
    $this->cafe24 = $this->cafe24Model->first();
    if ( !empty($this->cafe24) ) {
      $this->config->access_token = $this->cafe24['access_token'];
      $this->config->access_token_expires_at = $this->cafe24['access_token_expires_at'];
      $this->config->refresh_token = $this->cafe24['refresh_token'];
      $this->config->refresh_token_expires_at = $this->cafe24['refresh_token_expires_at'];
    }
  }

  public function getCarriers() {
    $this->reqHeaders();
    try {
      $response = $this->curl->get(
                    $this->config->base_url.'/api/v2/admin/carriers',
                    [ 
                      'headers' => ['Content-Type'  => 'application/json',
                                    'Authorization' => 'Bearer '.$this->config->access_token],
                      'query'   => ['shop_no' => $this->config->shop_no ] 
                    ]);
          
      $shippingFeeList = [];
      $carrier = [];

      if ( $response->getStatusCode() === 200 ) {
        if ( strpos($response->header('content-type'), 'application/json') !== false ) {
          $body = json_decode($response->getBody());
        } else $body = $response->getBody();
        // print_r($body);
        // echo "<br/><br/>";

        if ( !empty($body->carriers) ) $this->carriers = $body->carriers;
        else {
          http_response_code(400);
          print(http_response_code());
          return;
        }
        foreach($this->carriers AS $i => $carriers ) :
          if ( $carriers->shipping_fee_setting == 'T' ) {
            if ( empty($this->countries) ) {
              $this->countries = $carriers->shipping_fee_setting_detail->shipping_fee_setting_oversea->shipping_country_list;
            } else {
              foreach($carriers->shipping_fee_setting_detail->shipping_fee_setting_oversea->shipping_country_list AS $country ) :
                if ( !in_array($country->country_code, array_column($this->countries, 'country_code')) ) {
                  array_push($this->countries, $country);
                }
              endforeach;
            }
            // if ( !in_array($carriers->shipping_carrier, array_column($shippingFeeList, 'shipping_carrier')) ) {
            //   $carrier[$i]['carrier_id'] = $carriers->carrier_id;
            //   $carrier[$i]['shipping_carrier'] = $carriers->shipping_carrier;
            //   $carrier[$i]['track_shipment_url'] = $carrier->track_shipment_url;
            //   $carrier[$i]['country_shipping_fee_list'] = $carriers->shipping_fee_setting_detail->shipping_fee_setting_oversea->country_shipping_fee_list;
            // }
          }
        endforeach;

        $shippingFeeList = [];
        $idx = 0;
        foreach($this->carriers AS $i => $c ) {
          // print_r($c);
          if ( $c->shipping_fee_setting == 'T' ) {
            foreach($c->shipping_fee_setting_detail->shipping_fee_setting_oversea->country_shipping_fee_list AS $j => $fee ) {
              $carrierCnt = 0;
              if ( !in_array($fee->country_code, array_column($shippingFeeList, 'country_code')) ) {
                $shippingFeeList[$idx]['country_code'] = $fee->country_code;
                $shippingFeeList[$idx]['country_name'] = $fee->country_name;
                $shippingFeeList[$idx]['carrier'][$carrierCnt]['carrier_id'] = $c->carrier_id;
                $shippingFeeList[$idx]['carrier'][$carrierCnt]['shipping_carrier'] = $c->shipping_carrier;
                $shippingFeeList[$idx]['carrier'][$carrierCnt]['min_shipping_period'] = $c->shipping_fee_setting_detail->min_shipping_period;
                $shippingFeeList[$idx]['carrier'][$carrierCnt]['max_shipping_period'] = $c->shipping_fee_setting_detail->max_shipping_period;
                $shippingFeeList[$idx]['carrier'][$carrierCnt]['optional'][] = [ 'conditional'=> $fee->conditional,
                                                                    'min_value' => $fee->min_value,
                                                                    'max_value' => $fee->max_value,
                                                                    'shipping_fee' => $fee->shipping_fee ];
                // echo $idx."열 : <br/>";
                // print_r($temp[$idx]);
                if ( count($this->countries) > $idx ) $idx++;
              } else {
                $_idx = array_search($fee->country_code, array_column($shippingFeeList, 'country_code'));
                $carrierCnt = count($shippingFeeList[$_idx]['carrier']);

                if ( in_array($c->shipping_carrier, array_column($shippingFeeList[$_idx]['carrier'], 'shipping_carrier'))) {
                  $shippingFeeList[$_idx]['carrier'][$carrierCnt - 1]['optional'][] = ['conditional'=> $fee->conditional,
                                                                            'min_value' => $fee->min_value,
                                                                            'max_value' => $fee->max_value,
                                                                            'shipping_fee' => $fee->shipping_fee ];
                } else {
                  $shippingFeeList[$_idx]['carrier'][$carrierCnt]['carrier_id'] = $c->carrier_id;
                  $shippingFeeList[$_idx]['carrier'][$carrierCnt]['shipping_carrier'] = $c->shipping_carrier;
                  $shippingFeeList[$_idx]['carrier'][$carrierCnt]['min_shipping_period'] = $c->shipping_fee_setting_detail->min_shipping_period;
                  $shippingFeeList[$_idx]['carrier'][$carrierCnt]['max_shipping_period'] = $c->shipping_fee_setting_detail->max_shipping_period;
                  $shippingFeeList[$_idx]['carrier'][$carrierCnt]['optional'][] = [ 'conditional'=> $fee->conditional,
                                                                          'min_value' => $fee->min_value,
                                                                          'max_value' => $fee->max_value,
                                                                          'shipping_fee' => $fee->shipping_fee ];
                }
              }
            }
          }
          // foreach($c['country_shipping_fee_list'] AS $j => $fee) {
          //   $carrierCnt = 0;
          //   if ( !in_array($fee->country_code, array_column($shippingFeeList, 'country_code')) ) {
          //     $shippingFeeList[$idx]['country_code'] = $fee->country_code;
          //     $shippingFeeList[$idx]['country_name'] = $fee->country_name;
          //     $shippingFeeList[$idx]['carrier'][$carrierCnt]['shipping_carrier'] = $c['shipping_carrier'];
          //     $shippingFeeList[$idx]['carrier'][$carrierCnt]['carrier_id'] = $c['carrier_id'];
          //     $shippingFeeList[$idx]['carrier'][$carrierCnt]['optional'][] = [ 'conditional'=> $fee->conditional,
          //                                                         'min_value' => $fee->min_value,
          //                                                         'max_value' => $fee->max_value,
          //                                                         'shipping_fee' => $fee->shipping_fee ];
          //     // echo $idx."열 : <br/>";
          //     // print_r($temp[$idx]);
          //     if ( count($this->countries) > $idx ) $idx++;
          //   } else {
          //     $_idx = array_search($fee->country_code, array_column($shippingFeeList, 'country_code'));
          //     $carrierCnt = count($shippingFeeList[$_idx]['carrier']);

          //     if ( in_array($c['shipping_carrier'], array_column($shippingFeeList[$_idx]['carrier'], 'shipping_carrier'))) {
          //       $shippingFeeList[$_idx]['carrier'][$carrierCnt - 1]['optional'][] = ['conditional'=> $fee->conditional,
          //                                                                 'min_value' => $fee->min_value,
          //                                                                 'max_value' => $fee->max_value,
          //                                                                 'shipping_fee' => $fee->shipping_fee ];
          //     } else {
          //       $shippingFeeList[$_idx]['carrier'][$carrierCnt]['shipping_carrier'] = $c['shipping_carrier'];
          //       $shippingFeeList[$_idx]['carrier'][$carrierCnt]['carrier_id'] = $c['carrier_id'];
          //       $shippingFeeList[$_idx]['carrier'][$carrierCnt]['optional'][] = [ 'conditional'=> $fee->conditional,
          //                                                               'min_value' => $fee->min_value,
          //                                                               'max_value' => $fee->max_value,
          //                                                               'shipping_fee' => $fee->shipping_fee ];
          //     }
          //   }
          // }
        }
        return json_encode($shippingFeeList);
      }
    } catch ( \Exception $e) {
      // echo $e->getMessage()."<br/>";
      // echo http_response_code()."<br/>";
      // print_r($e);
      if ( strpos('The requested URL returned error: 401', $e->getMessage()) >= 0) {
        return redirect()->to(base_url('/cafe24/authorization?return_uri='.$this->uri->getPath()));
      } else return $e->getMessage();
    }
  }
}