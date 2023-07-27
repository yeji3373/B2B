<main id='inventoryCheck-main'>
  <form method='post' action='<?=site_url('/address')?>' accept-charset='UTF-8'>
    <!-- <div class='inventory-header'>
      <div class='inventory-title'>재고요청 확인</div>
      <button class='btn-close'></button>
    </div> -->
    <div class='inventory-content'>
      <div class='d-flex flex-column w-100'>
        <?=view('/layout/includes/AddressForm')?>
      </div>
      <div class='mt-3 w-100'>
        <h7>요청사항</h7>
        <div>
          <
        <textarea></textarea>
        </div>
      </div>
      <!-- <div class='mt-3'>
        <h7>재고요청 내역</h7>
        <?php if (!empty($carts)) : ?>
          <div class='accordion cart-list' id='InventoryCheckList'>
          <?php foreach ( $carts as $key => $cart ) : ?>
            <?php $ids = ++$key ?>
            <div class='accordion-item'>
              <div class='accrodion-header' id='heading<?=$ids?>'>
                <div class="accordion-button collapsed d-flex flex-row flex-wrap px-3 py-2 <?=$cart['onlyZeroTax'] == 1 ? 'only-zero-tax' : ''?>" 
                      data-bs-toggle="collapse" 
                      data-bs-target="#collapse<?=$ids?>"
                      aria-expanded="true" 
                      aria-controls="<?=$ids?>">
                  <div class='d-flex flex-row flex-wrap w-80'>
                    <div class='pe-1'>
                      <?=img(esc($cart['img_url']), false, ['class'=>'thumbnail']);?>
                    </div>
                    <div class='d-flex flex-column flex-wrap'>
                      <div class='d-flex flex-row'>
                        <div class='m-0 product-names'>
                          <span class='brand-name'><?=$cart['brand_name']?></span>
                          <span class='product-name'><?=$cart['name_en']?></span>
                        </div>
                        <span class='product-spec ms-1'>
                          <?=$cart['spec']?>
                        </span>
                      </div>
                      <p class='m-0 product-opts'>
                        <?php if ( !empty($cart['type_en']) ) : ?>
                        <span class='product-type fw-bold'><?=$cart['type_en']?></span>
                        <?php endif ?>
                      </p>
                    </div>
                  </div>
                  <div class='w-14 text-end'>
                    <p class='m-0 p-0'>
                      <label>Qty</label>
                      <span><?=number_format($cart['order_qty'])?></span>
                    </p>
                    <div class='w-100'>
                      <?php if ( $cart['apply_discount'] == 1 ) : ?>
                      <span class='currency-code'><?=number_format($cart['order_discount_price'], session()->currency['currencyFloat'])?></span>
                      <?php else : ?>
                      <span class='currency-code'><?=number_format($cart['order_price'], session()->currency['currencyFloat'])?></span>
                      <?php endif ?>
                    </div>
                  </div>
                </div>
              </div>
              <div id='collapse<?=$ids?>' 
                  class='accordion-collapse collapse'
                  aria-labelledby='heading<?=$ids?>'
                  data-bs-parent='#InventoryCheckList'>
                <div class='accordion-body'>
                  Barcode : <?=$cart['barcode']?><br/>
                  Qty : <?=$cart['order_qty']?><br/>
                  Unit Pirce : <?=session()->currency['currencySign'].$cart['prd_price']?><Br/>
                  Discount Price : 
                  <?php if ( $cart['apply_discount'] == 1 ) :
                    echo session()->currency['currencySign'].number_format($cart['dis_prd_price'], session()->currency['currencyFloat']).'<br/>';
                  else :
                    echo session()->currency['currencySign']."0<br/>";
                  endif; ?>
                  Total Price : <?=session()->currency['currencySign'].$cart['order_price']?><br/>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div> -->
    </div>
    <div class='inventory-footer'>
      <input type='submit' class='btn btn-primary' value='Submit'>
    </div>
  </form>
</main>