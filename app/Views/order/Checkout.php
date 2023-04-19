<main id='checkout-main'>
  <form method='POST' id='checkout-form' action='<?=site_url('/checkout')?>' accept-charset='UTF-8'>
    <section class='d-flex flex-row flex-wrap p-5 '>
      <section class='w-60 px-2'>
        <div class='d-flex flex-column w-100'>
          <?=view('/layout/includes/AddressForm')?>
          <div class='py-2 px-4 mt-2 border rounded'>
            <div class='d-flex flex-row flex-wrap'>
              <label class='title'><?=lang('Order.currency')?></label>
              <div class='w-75 radio-group'>
                <?php foreach($currencies as $currency) : ?>
                <label class='checkout-currency' for='checkout-currency-<?=strtolower($currency['currency_code'])?>'>
                  <input type='radio'
                      name='checkout-currency' 
                        id='checkout-currency-<?=strtolower($currency['currency_code'])?>'
                        data-rate='<?=($currency['exchange_rate'] / 100)?>'
                        data-id='<?=$currency['idx']?>'
                        data-rId='<?=$currency['cRate_idx']?>'
                        data-exchange='<?=$currency['addCalc']?>'
                        data-code='<?=$currency['currency_code']?>'
                        <?=$currency['currency_code'] == 'USD' ? 'checked': ''?>
                        value='<?=$currency['cRate_idx']?>'>
                    <?=$currency['currency_code']?>
                </label>
                <?php endforeach ?>
              </div>
            </div>
          </div>
          <?php if ( !empty($buyer) && !empty($buyer['tax_check']) ) : ?>
          <div class='py-2 px-4 mt-2 border rounded currency-kr-tax-choice'>
            <div class='d-flex flex-row flex-wrap'>
              <label class='title'>수출신고</label>
              <div class='w-75 radio-group'>
                <label for='checkout-export-tax-0'>
                  <input type='radio' name='taxation' id='checkout-export-tax-0' value='1' checked> 영세
                </label>
                <label for='checkout-export-tax-vat'>
                  <input type='radio' name='taxation' id='checkout-export-tax-vat' value='2'> 과세
                </label>
              </div>
            </div>
          </div>
          <?php else : ?>
          <input type='hidden' name='taxation' value='0'>
          <?php endif; ?>
          <div class='py-2 px-4 mt-2 border rounded'> 
            <div class='d-flex flex-row flex-wrap'>
              <label class='title'><?=lang('Order.paymentMethod')?></label>
              <div class='w-75 radio-group'>
                <?php if ( !empty($payments) ) : 
                  foreach ($payments as $payment) : ?>
                <label for='payment-<?=$payment['payment_val']?>'>
                  <input type='radio' name='payment_id' 
                    id='payment-<?=$payment['payment_val']?>' 
                    value='<?=$payment['id']?>' 
                    <?=$payment['id'] == 3 ? 'disabled': '' ?>
                    <?=$payment['id'] == 4 ? 'disabled': '' ?>
                    required>
                  <?=$payment['payment']?>
                </label>
                <?php endforeach; 
                  endif; ?>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section class='w-40 px-2 cart-list-section'>
        <div class>
          <h6><?=lang('Order.orderProduct')?><?=isset($carts) ? '('.count($carts).')' : ''?></h6>
          <?php if (empty($carts)) : ?>
            <div class='isEmpty'>
              <?=lang('Order.isEmpty')?>
            </div>
          <?php else : ?>
            <div class='accordion cart-list' id='accordionExample'>
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
                              <!-- <label>spec</label> -->
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
                    data-bs-parent='#accordionExample'>
                  <div class='accordion-body'>
                    Barcode : <?=$cart['barcode']?><br/>
                    Qty : <?=$cart['order_qty']?><br/>
                    Unit Pirce : <?=$cart['prd_price']?><Br/>
                    Discount Price : 
                    <?php if ( $cart['apply_discount'] == 1 ) :
                      // echo lang('Order.off', [((1 - $cart['dis_rate']) * 100)])."<br/>";
                      echo session()->currency['currencySign'].number_format($cart['dis_prd_price'], session()->currency['currencyFloat']).'<br/>';
                    else :
                      echo session()->currency['currencySign']."0<br/>";
                    endif; ?>
                    Total Price : <?=$cart['order_price']?><br/>
                    <!-- Grand Total Price : <?//=$cart['order_discount_price']?> -->
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
        <div class='mt-5 p-0 border position-sticky rounded w-60 m-0 ms-auto cart-total-price'>
          <div class='w-100 p-2 border-bottom box-header'>
            Total (<input type='text' class='currency-unit fw-bold' name='currency_code' value='<?=session()->currency['currencyUnit']?>' readonly/>)
          </div>
          <div class='box-body pt-2 pb-4 px-4'>
            <div class='w-100'>
              <input type='hidden' name='order-total-price' value='<?=$cartSubTotal['order_price_total']?>'>
              <input type='hidden' name='order-discount-price' value='<?=$cartSubTotal['order_discount_total']?>'>
              <input type='hidden' name='order-subtotal-price' value='<?=$cartSubTotal['order_subTotal']?>'>
              <p class='text-end'>
                <label>Total</label>
                <span class='currency-code text-end order-total-price'><?=number_format($cartSubTotal['order_price_total'], session()->currency['currencyFloat'])?></span>
              </p>
              <p class='text-end'>
                <label>Discount</label>
                <span class='currency-code order-discount-price'><?=number_format($cartSubTotal['order_discount_total'], session()->currency['currencyFloat'])?></span>
              </p>
              <p class='text-end order-subtotal'>
                <label>Subtotal</label>
                <span class='currency-code order-subtotal-price'><?=number_format($cartSubTotal['order_subTotal'], session()->currency['currencyFloat'])?></span>
              </p>
            </div>
            <div class='w-100 text-end'>
              <button class='btn border-0 w-100 checkout-btn m-auto'><?=lang('Order.orderBtn')?></button>
            </div>
          </div>
        </div>
      </section>
    </section>
  </form>  
</main>