<?php

return [
  'brand'               => 'Brand',
  'brands'              => 'Brands',
  'brandSearch'         => 'Search for brands',
  'allBrands'           => 'All brands',

  'product'             => 'Product',
  'products'            => 'Products',
  'selectOps'           => 'Select search option',
  'barcode'             => 'Barcode',
  'productName'         => 'Product Name',
  
  'searchKeyword'       => 'Search for keyword',
  'noResultSearch'      => 'No Results Found',
  
  'productBarcode'      => 'Barcode',
  'productWeight'       => 'Weight',
  'productType'         => 'Type',
  'sample'              => 'Sample',
  'container'           => '용기제품',
  'bundle'              => '묶음제품',
  'bundleSpec'          => '묶음구성',
  'onsale'              => 'On Sale',
  'soldout'             => 'Sold out',
  'set'                 => '세트구성',
  'compo'               => 'Compo.',
  'composition'         => 'Composition',

  'noStock'             => 'Sold out',
  
  'order'               => 'Order',

  'stockReqPrd'         => 'Request an order',
  'nonRefund'           => 'Non-refundable',
  'nonRefundMsg'        => 'Refund is not possible for {target} products',
  
  'selectBtn'           => 'Select',
  'unselectBtn'         => 'Unselect',
  'stockRequestBtn'     => 'Request an order',
  'stockReqCancelBtn'   => 'Cancel order request',
  'checkout'            => 'Checkout',
  'changeQtyBtn'        => 'Change Qty',

  'previouslyAddrss'    => 'Registered shipping address',
  'addNewAddress'       => 'Add a new address',
  'orderList'           => 'Products in orders',

  /* msg */
  'alreadyRegistered'   => 'The product is already selected. Change the quantity',
  'alreadyExists'       => '이미 존재함',
  'unknownError'        => 'The error occurred {error}',
  // 'orderMinCheck'   => '최소 금액은 {minCount}입니다.',
  'orderMinCheck'       => 'Select products over ${0, number}',
  'addrOperate'         => '{type} {result}',
  'off'                 => '{0, number}% off',
  /* msg */

  'isEmpty'             => 'Is empty',
  'edit'                => 'Edit',
  'del'                 => 'Del',
  'delete'              => 'Delete',
  'confirm'             => 'Confirm',
  'draft'               => 'Draft', // 임시저장

  'productPrice'        => 'Unit price',
  'qty'                 => 'Qty',
  'quantity'            => 'Quantity',
  
  /* orders */
  'orders'              => [
    'orderDate'           => 'Order date',  // 주문날짜
    'orderNumber'         => 'Order number',  // 주문번호
    'paymentType'         => 'Payment type',  // 결제 수단
    'inventoryChecking'   => 'Inventory request under review',  // 재고요청 확인 중
    'orderChecking'       => 'Order under review',  // 주문 확인 중
    'currency'            => 'Currency',  // 결제 통화
    'ExportCheck'         => 'Export declaration',
    'zeroTax'             => 'Zero tax',
    'taxation'            => 'Taxation',
    'includeVat'          => 'VAT',
    'orderQty'            => 'Qty',
    'orderPrice'          => 'Price',
    'totalAmount'         => 'Total amount',  // 결제금액
    'productSubTotal'     => 'Subtotal',  
    'shippingFee'         => 'Shipping cost', // 배송비
    'totalDiscount'       => 'Total discount',  // 할인금액
    'paypalInvoice'       => 'Paypal invoice',
    'invoice'             => 'Invoice',
    'ordermore'           => 'order more',
    // 'pi'                  => 'Proforma Invoice',
    'pi'                  => 'PI',
    'ci'                  => 'CI',
    'pl'                  => 'PL',
    'view'                => 'View',
    'amount'              => 'Amount',
    'address'             => 'Address',
    'payment'            => [
      'receipts'            => 'receipts',
      'receipt'             => 'receipt',
      'piTitle'             => 'Payment {0, number}', // 1차 결제정보
      'status'              => 'status',  // 결제 현황
      'toBePaid'            => 'Amount to be paid', // 결제 요청 금액
      'remainBalance'       => 'Remaining balance', // 결제 후 잔액
    ],
    'detail'                => [
      'orderStatus'         => 'Order status',
      'orderCanceled'       => 'Order canceled',
      'cancelReason'        => 'Cancellation reason',
      'paymentConfirm'      => 'Payment confirmed',
      'requestPrice'        => 'Request price', //재고 요청 금액
      'initialQty'          => 'Initial order quantity', //최초 주문수량
      'securedQty'          => 'Secured inventory quantity', //재고 확보 된 수량
      'fixedQty'            => 'Fixed order quantity',
      'finalQty'            => 'Final order quantity', //최종 주문 수량
      'initialAmount'       => 'Initial order amount', //최초 주문 금액
      'fixedAmount'         => 'Fixed order amount',
      'finalAmount'         => 'Final order amount', //최종 주문 금액
      'fixedProductPrice'   => 'Fixed product price' // 확정된 제품 가격
    ],
    'invoice'               => [
      'excludedOrder'       => 'Excluded from Order',
      'exclued'             => 'Excluded',
      'cancellation'        => 'cancellation',
    ],
  ],
  /* orders */

  /* checkout */
  'consignee'           => 'Recipient\'s name', // 수령자 이름
  'paymentMethod'       => 'Payment method',
  'orderProduct'        => 'Order Product List',
  'checkout'            => [
    'checkout'          => 'Checkout',
    'error'             => [
      'paymentExcluded' =>  '{payment} payment excluded when paying {currency}',
    ],
  ],
  /* checkout */

  /* 재고요청 선택사항 DB */
  'inventoryRequest'    => [
    'inventoryCheckRq'    => 'Inventory check request',
    'checkAlert'          => 'Would you like to cancel the inventory check request?',
    'requestCheck'        => 'Details when requesting inventory',
    'requestCheckSelect'  => 'Select request details',
    'balanceReq'          => '잔금요청',
    'balanceCheckComplete'=> '잔금입금 확인 완료',
    'otherAddRequirment'  => '그 외 요청사항',
    'additional'          => 'Additional',
  ],
  /* 재고요청 선택사항 DB */

  /* 공통 */
  'remark'                => 'remark', //비고
  'msg'                   => [
    'statusChooseReCheck' => 'No options have been selected. Would you like to select an option?', // 체크가 완료되지 않았습니다. 체크를 다시 진행하시겠습니까?
  ],
  /* 공통 */
];