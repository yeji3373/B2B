<?php

use Config\Servies;

if ( !function_exists('invoice_detail') || !empty($userData) ) {
  function invoice_detail($userData, $currencyUnit = 'USD') {
    $config = config('Paypal');
    
    if ( !empty($userData['currency_code']) ) $currencyUnit = $userData['currency_code'];

    $default = [
      'detail'   => [
        'invoice_number'  => $userData['buyerName'].'_'.date('YmdHms', time()),
        'invoice_date'    => date('Y-m-d'),
        'payment_term'    => ['term_type' => 'NO_DUE_DATE'],
        'currency_code'   => $currencyUnit
      ],
      'invoicer'  => [
        'name'    => [
          'given_name'  => 'BeautynetKorea Co.,'
        ],
        'address' => [
          'address_line_1'    => '21, Janggogae-ro 231beonan-gil, Seo-gu',
          'address_line_2'    => 'Beautynetkorea Bldg',
          'admin_area_2'      => 'Incheon',
          'admin_area_1'      => 'Korea',
          'postal_code'       => '22827',
          'country_code'      => 'KR'
        ],
        'email_address' => $config->invoicerEmail,
        'phones'    => [
          [
            'country_code'      => '082',
            'national_number'  => '7048005454',
            'extension_number'  => '202',
            'phone_type'        => 'MOBILE'
          ]
        ],
        // "website" => "www.beautynetkorea.com",
        // "tax_id" => "XX-XXXXXXXX",
        'logo_url'  => 'https://pics.paypal.com/00/s/MjUwWDEwMDBYUE5H/p/N2VhODRjZDUtOTU3Yy00YWE1LTk0MmQtMWRkNjgxOTA1NDAy/image_109.png'
      ],
      'primary_recipients'  => [
        [
          'billing_info'  => [
            'name'      => ['given_name' => $userData['buyerName'].'('.$userData['id'].')'],
            // 'address'   => [
            //   'address_line_1'  => $userData['streetAddr1'].$userData['streetAddr2'],
            //   // 'admin_area_2'    => 'Anytown',
            //   // 'admin_area_1'    => 'CA',
            //   'postal_code'     => '',
            //   'country_code'    => 'US'
            // ], 
            'email_address'   => $userData['email'],
            'phones'   => [
              [
                'country_code'    => $userData['phone_code'],
                'national_number' => $userData['phone'],
                'phone_type'      => 'MOBILE'
              ]
            ], 
            'additional_info_value' => 'add-info'
          ], 
          'shipping_info' => [
            'name'  => [
              'given_name' => $userData['consignee']
            ],
            'address' => [
              'address_line_1'  => $userData['streetAddr1'],
              'address_line_2'  => $userData['streetAddr2'],
              'postal_code'     => $userData['zipcode'],
              'country_code'    => $userData['country_code']
            ]
          ]
        ]
      ],
      'items' => [
        [
          'name'  => 'cosmetic',
          'quantity'  => '1',
          'unit_amount' => [
            'currency_code' => $currencyUnit,
            // 'value'         => ($userData['order-subtotal-price'] * $userData['depositRate'])
            'value'         => ($userData['order-subtotal-price'] * $userData['depositRate'])
          ],
          'unit_of_measure' => 'QUANTITY'
        ]
      ],
      'configuration' => [
        'partical_payment'    => [
          'allow_partial_payment'  => false,
        ],
        'allow_tip' => false,
        'tax_calculated_after_discount' => true,
        'tax_inclusive' => false
      ],
      // 'amount' => [
      //   'breakdown' => [
      //     'shipping' => [
      //       'amount' => [
      //         'currency_code' => $currencyUnit,
      //         'value' => $userData['shippingFee']
      //       ]
      //     ]
      //   ]
      // ]
    ];

    return json_encode($default);
  }
}