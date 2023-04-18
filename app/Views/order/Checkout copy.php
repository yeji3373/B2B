<main id='checkout-main'>
  <form method='POST' action='<?=site_url('/paypal')?>' accept-charset='UTF-8'>
    <section class='d-flex flex-row flex-wrap p-5'>
    <!-- <section class='w-100'></section> -->
      <section class='w-60 px-2 py-3'>
        <div class='d-flex flex-column w-100'>
          <?=view('/layout/includes/AddressForm')?>
          <div class='py-2 px-4 mt-2 border rounded'> 
            <!-- <div class='d-flex flex-row flex-wrap mb-2'>
              <label class='title'>Type</label>
              <div class='w-75'>
                <label class='w-20' for='type-fob'>
                  <input class='me-2' type='radio' name='tradeConditions' id='type-fob' value='fob'>FOB
                </label>
                <label class='w-20' for='type-ex'>
                  <input class='me-2' type='radio' name='tradeConditions' id='type-ex' value='ex'>EXW
                </label>
              </div>
            </div> -->
            <!-- <div class='d-flex flex-row flex-wrap mb-2'>
              <label class='title'>Export declaration</label>
              <div class='w-75'>
                <label class='w-20' for='export-declare-y'>
                  <input class='me-2' type='radio' name='exportConditions' id='export-declare-y' value='yes'>YES
                </label>
                <label class='w-20' for='export-declare-n'>
                  <input class='me-2' type='radio' name='exportConditions' id='export-declare-n' value='no'>NO
                </label>
              </div>
            </div> -->
            <div class='d-flex flex-row flex-wrap'>
              <label class='title'>Payment method</label>
              <div class='w-75'>
                <?php if ( !empty($payments) ) : 
                  foreach ($payments as $payment) : ?>
                <label class='w-20' for='payment-<?=$payment['payment']?>'>
                  <input type='radio' class='me-2' name='payment_id' id='payment-<?=$payment['payment']?>' value='<?=$payment['id']?>'><?=$payment['payment']?>
                </label>
                <?php endforeach; 
                  endif; ?>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section class='w-40 px-2 py-3 cart-list-section'>
        <div class>
          <h6>Products in cart <?=isset($carts) ? '('.count($carts).')' : ''?></h6>
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
                  <div class="accordion-button collapsed d-flex flex-row flex-wrap px-3 py-2" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#collapse<?=$ids?>"
                        aria-expanded="true" 
                        aria-controls="<?=$ids?>">
                    <div class='d-flex flex-row flex-wrap w-85'>
                      <div class='pe-1'>
                        <img class='thumbnail' src='<?=esc($cart['img_url'])?>' />
                      </div>
                      <div class='d-flex flex-column flex-wrap'>
                        <p class='m-0'>
                          <span class='brand-name'><?=$cart['brand_name']?></span>
                          <span class='product-name'><?=$cart['name_en']?></span>
                        </p>
                        <p class='m-0 product-opts'>
                          <?php if ( !empty($cart['type_en']) ) : ?>
                          <span class='product-type fw-bold'><?=$cart['type_en']?></span>
                          <?php endif ?>
                          <span class='product-spec'>
                            <!-- <label>spec</label> -->
                            <?=$cart['spec']?>
                          </span>
                        </p>
                      </div>
                    </div>
                    <div>
                      <?php if ( $cart['apply_discount'] == 1 ) : ?>
                      <span class='currency-code'><?=number_format($cart['order_discount_price'], session()->currency['currencyFloat'])?></span>
                      <?php else : ?>
                      <span class='currency-code'><?=number_format($cart['order_price'], session()->currency['currencyFloat'])?></span>
                      <?php endif ?>
                    </div>
                  </div>
                </div>
                <div id='collapse<?=$ids?>' 
                    class='accordion-collapse collapse'
                    aria-labelledby='heading<?=$ids?>'
                    data-bs-parent='#accordionExample'>
                  <div class='accordion-body'>
                    <?php if ( $cart['apply_discount'] == 1 ) : ?>
                      <?=(1 - $cart['dis_rate']) * 100?>% 할인적용.
                    <?php endif ?>
                    barcode : <?=$cart['barcode']?>
                    주문수량 : <?=$cart['order_qty']?>
                    제품 1개당 가격 : <?=$cart['prd_price']?>
                    할인전 가격 : <?=$cart['order_price']?>
                    할인된 가격 : <?=$cart['order_discount_price']?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
        <div class='mt-5 p-0 border position-sticky rounded w-50 m-0 ms-auto cart-total-price'>
          <div class='w-100 p-2 border-bottom box-header'>
            Total (<?=session()->currency['currencyUnit']?>)
          </div>
          <div class='box-body pt-2 pb-4 px-4'>
            <div class='w-100'>
              <p class='text-end'>
                <!-- <label>주문금액</label> -->
                <span class='currency-code text-end'><?=number_format($cartSubTotal['order_price_total'], session()->currency['currencyFloat'])?></span>
              </p>
              <?php if ($cartSubTotal['applyDiscount'] == 1 ) : ?>
              <p class='text-end'>
                <label>Discount</label>
                <span class='currency-code order-discount-price'><?=number_format($cartSubTotal['order_discount_total'], session()->currency['currencyFloat'])?></span>
              </p>
              <p class='text-end order-subtotal'>
                <label>Subtotal</label>
                <span class='currency-code order-subtotal-price'><?=number_format($cartSubTotal['order_subTotal'], session()->currency['currencyFloat'])?></span>
              </p>
              <?php else : ?>
              <p class='text-end order-subtotal'>
                <label>Subtotal</label>
                <span class='currency-code order-subtotal-price'><?=number_format($cartSubTotal['order_subTotal'], session()->currency['currencyFloat'])?></span>
              </p>
              <?php endif ?>
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