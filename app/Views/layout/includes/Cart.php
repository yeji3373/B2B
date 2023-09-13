<?php if (!empty($carts) && isset($carts)) :
  foreach($carts as $cart) : ?>
  <div class='d-grid py-1 px-2 list-group-item w-100 slideUp'>
    <div class='d-flex flex-column product-item-info'>
      <div class='d-flex flex-row align-items-baseline align-self-baseline me-2'>
        <!-- <input type='checkbox' class='bsk-check' value='<?=$cart['chkd']?>' <?=($cart['chkd'] ? 'checked' : '')?>> -->
        <?=img(esc($cart['img_url']), false, ['class' => 'thumbnail align-self-start'])?>
        <div class='d-flex flex-column ms-1'>
          <div class='name-group'>
            <span class='brand_name'><?=stripslashes($cart['brand_name'])?></span>
            <span class='product_name'><?=stripslashes($cart['name_en'].' '.$cart['spec'])?></span>
          </div>          
          <?php if ( $cart['type_en'] != "" ) : ?>
          <div class='type'>
            <label class='item-name'><?=lang('Order.productType')?> : </label>
            <span><?="#".$cart['type_en']?></span>
          </div>
          <?php endif ?>

          <?php if ( $cart['barcode'] > 0 ) : ?>
          <div class='barcode'>
            <label class='item-name'>Barcode : </label>
            <span><?=$cart['barcode']?></span>
          </div>
          <?php endif ?>
        </div>
      </div>
      <div class='flex-column product-item-detail-info'>
        <div class='d-flex flex-column product-info'>
          <div class='product-info-item spec'>
            <label class='item-name'>Spec</label>
            <span>
              <?php
                echo $cart['spec'];
                if ( !empty($cart['spec2']) ) {
                  echo "<span>/".$cart['spec2']."</span>";
                }
                if ( $cart['box'] == 1 ) :
                  echo "/pc";
                  echo "<span class='fw-bold'> (".$cart['spec']."&#215;".$cart['in_the_box']."pcs)</span>";
                endif;
                echo $cart['container'] == 1 && $cart['spec_detail']  > 0 ? 
                      "<span class>".
                        " (".$cart['spec_detail']."&#215;".$cart['spec_pcs']."pcs)".
                      "</span>" : 
                      "";
              ?>
            </span>
          </div>
          
          <?php if ( $cart['container'] == 0 && $cart['spec_detail'] > 0 ) : ?>
          <div class='product-info-item spec-detail'>
            <label class='item-name'><?=lang('Order.bundleSpec')?></label>
            <span><?=$cart['spec_detail']."&#215;".$cart['spec_pcs']."pcs"?></span>
          </div>
          <?php endif ?>

          <div class='product-info-item ship-weight'>
            <label class='item-name'><?=lang('Order.productWeight')?></label>
            <span>
              <?=$cart['shipping_weight'] != 0 ? number_format($cart['shipping_weight'])."g" : "-";?>
            </span>
          </div>

          <div class='product-info-item retail-price'>
            <label class='item-name'>Retail price</label>
            <span>
              <?=session()->currency['currencySign'].number_format($cart['retail_price'], session()->currency['currencyFloat'])?>
            </span>
          </div>

          <div class='product-info-item unit-price'>
            <label class='item-name'>Unit price</label>
            <div>
              <span>
                <?php 
                  echo session()->currency['currencySign'];
                  echo number_format($cart['product_price'], session()->currency['currencyFloat']);
                ?>
              </span>
            </div>
          </div>
          
          <div class='product-info-item spq'>
            <label class='item-name'>SPQ</label>
            <div class='d-flex flex-row flex-wrap border border-secondary w-50'>
              <div class='d-flex flex-column border-end border-secondary w-50'>
                <label class='fw-lighter border-bottom border-secondary text-center w-100'>In box </label>
                <span class='d-inline-block text-center w-100'><?=empty($cart['spq_inBox']) ? '-' : number_format($cart['spq_inBox'])?></span>
              </div>
              <div class='d-flex flex-column w-50'>
                <label class='fw-lighter border-bottom border-secondary text-center w-100'>Out box</label>
                <span class='d-inline-block text-center w-100'><?=empty($cart['spq_outBox']) ? '-' : number_format($cart['spq_outBox'])?></span>
              </div>
            </div>
          </div>
          <div class='product-info-item moq'>
            <label class='item-name'>MOQ</label>
            <span><?=number_format($cart['moq'])?></span>
          </div>
          
          <div class='product-info-item stock'>
            <label class='item-name'>Stock</label>
            <span>
              <?php if ( !empty($cart['available_stock']) && ($cart['available_stock'] > 0 && empty($cart['stock_req']))) : ?>
              <?=number_format($cart['available_stock'])?>
              <?php else:
                // echo lang('Order.noStock'); 
              echo "-"; ?>
              <?php endif ?>
            </span>
          </div>

        </div>
      </div>
    </div>
    <div class='d-flex flex-column justify-content-end position-relative cart-qty-request'>
      <form accept-charset='utf-8' method='post' class='cart-qty-form'>
        <?=csrf_field() ?>
        <input type='hidden' name='currency-chk' value='<?=session()->currency['exchangeRate']?>'>
        <input type='hidden' name='prd_id' value='<?=$cart['id']?>'>
        <input type='hidden' name='brd_id' value='<?=$cart['brand_id']?>'>
        <input type='hidden' name='cart_idx' value='<?=$cart['cart_idx']?>'>
        <input type='hidden' name='prd_price' value='<?=$cart['product_price']?>'>
        <input type='hidden' name='prd-total-price' value='<?=$cart['order_price']?>'>
        <input type='hidden' name='op-code' value='<?=$cart['calc_code']?>'>
        <input type='hidden' name='op-val' value='<?=$cart['calc_unit']?>'>
        <input type='hidden' name='order_qty' value='<?=$cart['moq']?>'> <!-- qty stand value -->
        <!-- <input type='hidden' name='qty-maximum-val' value='<?//=!empty($cart['stock_req']) ? $cart['available_stock'] : ''?>'> -->

        <div class='btn btn-close border-0 end-0 position-absolute top-0 bsk-del-btn'></div>
        <div class='cart-qty-group'>
          <div class='d-flex flex-row justify-content-center align-items-center flex-nowrap border mx-auto mb-2 w-100 qty-group'>          
            <div class='w-25 h-100 p-0 fw-bold text-center shadow-none decrease-btn' data-calc='-'>-</div>
            <input type='text' value='<?=$cart['order_qty']?>' class='w-50 border-0 border-start border-end rounded-0 qty-spq'>
            <div class='w-25 h-100 p-0 fw-bold text-center shadow-none increase-btn' data-calc='+'>+</div>
          </div>
          <div class='p-1 mb-1 text-end btn /*btn-link*/ btn-dark qty-change-btn'><?=lang('Order.changeQtyBtn')?></div>
        </div>
        <div class='text-end price-group'>
          <div>
            <?php echo session()->currency['currencySign']?>
            <span class='prd-item-total-price'><?=number_format($cart['order_price'], session()->currency['currencyFloat'])?></span>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php endforeach; ?>
<?php endif; ?>