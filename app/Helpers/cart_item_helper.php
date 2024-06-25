<?php
if ( !function_exists('cart_list') ) {
  function cart_list($data) {
    $items = "";
    foreach($data AS $cart) :
      $items .= cart_item($cart);
    endforeach;

    return json_encode($items);
  }
}

if ( !function_exists('cart_item') ) {
  function cart_item($cart) {
    $item = '';
    
    $qtyInputReadOnly = false;
    if ( isset($cart['spq_criteria']) && !is_null($cart['spq_criteria']) ) { 
      $qtyInputReadOnly = true;
    }
    
    $item = 
    "<div class='d-grid py-1 px-2 list-group-item position-relative w-100 slideUp'>
      <div class='d-flex flex-column product-item-info'>
        <div class='d-flex flex-row align-items-baseline align-self-baseline me-2'>".
          img(esc($cart['img_url']), false, ['class' => 'thumbnail align-self-start']).
          "<div class='d-flex flex-column ms-1'>
            <div class='name-group'>
              <span class='brand_name'>".stripslashes($cart['brand_name'])."</span>
              <span class='product_name'>".stripslashes($cart['name'].' '.$cart['spec'])."</span>
            </div>";
            if ( !empty($cart['type']) ) :
            $item.=
            "<div class='type font-size-7'>
              <label class='item-name'>".lang('Lang.productType')." : </label>
              <span>";
              if ( strpos($cart['type'], '#') === false ) :
                $item .= "#";
              endif;
              $item .= $cart['type'].
              "</span>
            </div>";
            endif;
            if ( $cart['barcode'] > 0 ) :
            $item .=
            "<div class='barcode font-size-7'>
              <label class='item-name'>Barcode : </label>
              <span>".$cart['barcode']."</span>
            </div>";
            endif;
          $item .=
          "</div>
        </div>
        <div class='flex-column product-item-detail-info '>
          <div class='d-flex flex-column product-info'>";
            //spec
            if ( !empty($cart['spec']) ) : 
            $item .= 
            "<div class='product-info-item spec'>
              <label class='item-name'>Spec</label>
              <span>".
                $cart['spec'];
                if ( !empty($cart['spec2']) ) :
                  $item .= "/<span>".$cart['spec2']."</span>";
                endif;
              $item .=
              "</span>
            </div> <!-- product-info-item spec 닫음 -->";
            endif; 
            // spec
            
            // detail
            if ( $cart['container'] == 0 && $cart['spec_detail'] > 0 ) :
            $item.=
            "<div class='product-info-item spec-detail'>
              <label class='item-name'>".lang('Lang.bundleSpec')."</label>
              <span>".$cart['spec_detail']."&#215;".$cart['spec_pcs']."pcs</span>
            </div>";
            endif; 
            // detail

            // shipping weight
            if ( !empty($cart['shipping_weight']) ) :
            $item .=
            "<div class='product-info-item ship-weight'>
              <label class='item-name'>".lang('Lang.productWeight')."</label>
              <span>".number_format($cart['shipping_weight'])."g</span>
            </div>";
            endif;
            // shipping weight

            // retail price
            $item .=
            "<div class='product-info-item retail-price'>
              <label class='item-name'>Retail price</label>
                <span>"
                  .session()->currency['currencySign']
                  .number_format($cart['retail_price'], session()->currency['currencyFloat']).
                "</span>
            </div>";
            // retail price

            // unit price
            $item .= 
            "<div class='product-info-item unit-price'>
              <label class='item-name'>Unit price</label>
              <div>
                <span>".
                  session()->currency['currencySign'].
                  number_format($cart['applied_price'], session()->currency['currencyFloat']).
                "</span>
              </div>
            </div>";
            // unit price

            // moq
            $item .=
            "<div class='product-info-item moq'>
              <label class='item-name'>MOQ</label>
              <span>".number_format($cart['moq'])."</span>
            </div>";
            // moq

            // spq
            $item .=
            "<div class='product-info-item spq'>
              <label class='item-name'>SPQ</label>
              <div class='d-flex flex-row flex-wrap border border-secondary w-50'>
                <div class='d-flex flex-column border-end border-secondary w-50'>
                  <label class='fw-lighter border-bottom border-secondary text-center w-100'>In box </label>
                  <span class='d-inline-block text-center w-100'>".(empty($cart['spq_inBox']) ? '-' : number_format($cart['spq_inBox']))."</span>
                </div>
                <div class='d-flex flex-column w-50'>
                  <label class='fw-lighter border-bottom border-secondary text-center w-100'>Out box</label>
                  <span class='d-inline-block text-center w-100'>".(empty($cart['spq_outBox']) ? '-' : number_format($cart['spq_outBox']))."</span>
                </div>
              </div>
            </div>";
            // spq

          $item .=
          "</div> <!-- product-info 닫음 -->
        </div> <!-- flex-column product-item-detail-info 닫음 -->
      </div> <!-- product-item-info 닫음 -->
      <div class='d-flex flex-column justify-content-end cart-qty-request'>
        <form accept-charset='utf-8' method='post' class='cart-qty-form'>
          <input type='hidden' name='idx' value='".$cart['idx']."'>
          <input type='hidden' name='dataType' value='delete'>
          <div class='btn btn-close border-0 end-0 position-absolute top-0 bsk-del-btn'></div>
          <div class='cart-qty-group'>
            <div class='d-flex flex-row justify-content-center align-items-center flex-nowrap border mx-auto mb-2 w-100 qty-group'>
              <div class='w-25 h-100 p-0 fw-bold text-center shadow-none decrease-btn' data-calc='1'>-</div>
              <input type='text' value='".$cart['order_qty']."' class='w-50 border-0 border-start border-end rounded-0 qty-spq text-center'".($qtyInputReadOnly ? 'readonly': '').">
              <div class='w-25 h-100 p-0 fw-bold text-center shadow-none increase-btn' data-calc='0'>+</div>
            </div>";
            if ( !$qtyInputReadOnly ) :
            $item .=
            "<div class='p-1 mb-1 text-end btn btn-dark qty-change-btn'>".lang('Lang.changeQtyBtn')."</div>";
            endif;
          $item .=
          "</div>
          <div class='text-end price-group'>
            <div>".
              session()->currency['currencySign'].
              "<span class='prd-item-total-price'>".
              number_format(($cart['applied_price'] * $cart['order_qty']), session()->currency['currencyFloat'])."</span>
            </div>
          </div>
        </form>
      </div>
      <div class='w-100 grid-column-span-2 p-0 m-0 d-flex justify-content-center position-absolute bottom-0'>
        <span class='btn btn-sm btn-secondary badge rounded-0 py-0 px-2 font-size-6 position-absolute bottom-0 more-btn view-more'>View More</span>
      </div>
    </div>";
           
    return $item;
  }
}
 
