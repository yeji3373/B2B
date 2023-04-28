<!-- <div class='w-100 d-flex flex-column flex-wrap'> -->
<?php if ( isset($order) && !empty($order) ) : ?>
  <?php //print_r($order) ?>
  <div class='w-100 fw-bold d-flex flex-row flex-wrap py-2'>
    <div class='me-3'>
      <label><?=lang('Order.orderDate')?></label>
      <span><?=$order['orderDate']?></span>
    </div>
    <div>
      <label><?=lang('Order.orderNumber')?></label>
      <span><?=$order['order_number']?></span>
    </div>
  </div>
  <div class='d-flex flex-column flex-wrap p-0 product-payment-info-section'>
    <div class='info-sec p-0'>
      <label class='py-2 ps-2'><?=lang('Order.paymentType')?></label>
      <span class='py-2 pm-2'><?=$order['payment']?></span>
      <?php if ( $order['show_info'] == 1 ) : ?>
      <div class='w-100 p-2 bg-opacity-10 bg-secondary sub-sec'>
        <!-- htmlspecialchars -->
        <span><?=$order['payment_info']?></span>
      </div>
      <?php endif ?>
    </div>
    <div class='info-sec p-0'>
      <label class='py-2 ps-2'><?=lang('Order.currency')?></label>
      <span class='py-2 pm-2'><?=$order['currency_code']?></span>
    </div>
    <div class='info-sec p-0'>
      <label class='py-2 ps-2'><?=lang('Order.amount')?></label>
      <div class='w-100 py-2 px-1 bg-opacity-10 bg-secondary sub-sec'>
        <table class='w-80'>
          <thead>
            <tr>
              <td class='w-25 p-1 text-center border border-dark border-end'><?=lang('Order.productSubTotal')?></td>
              <td class='w-25 p-1 text-center border border-dark border-end'><?=lang('Order.totalDiscount')?></td>
              <td class='w-25 p-1 text-center border border-dark border-end'><?=lang('Order.shippingFee')?></td>
              <td class='w-25 p-1 text-center border border-dark border-end'><?=lang('Order.totalAmount')?></td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class='text-end p-1 border border-dark border-end'>
                <?=$order['currency_sign'].number_format($order['order_amount'], $order['currency_float'])?>
              </td>
              <td class='text-end p-1 border border-dark border-end'>
                <?=$order['currency_sign'].number_format($order['discount_amount'], $order['currency_float'])?>
              </td>
              <td class='text-end p-1 border border-dark border-end'>
                <?php if ( !empty($shippinCost) ) : 
                  echo $order['currency_sign'].number_format($shippinCost['shipping_total_cost'], $order['currency_float']);
                else : 
                  echo "-";
                endif ?>
              </td>
              <td class='text-end p-1 border border-dark border-end'>
                <?=$order['currency_sign'].number_format(($order['subtotal_amount'] + $order['delivery_price']), $order['currency_float'])?>
                <?=$order['taxation'] == 2 ? '('.lang('Order.includeVat').')' : '' ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class='info-sec p-0'>
      <label class='py-2 ps-2'>결제내역 / <?=lang('Order.pi')?></label>
      <div class='w-100 py-2 px-1 bg-opacity-10 bg-secondary sub-sec'> 
        <table class='border border-dark w-100 sub-inner-sec'>
          <colgroup>
            <col style='width: 5%'></col>
            <col style='width: 12%'></col>
            <col style='width: 12%'></col>
            <col style='width: 12%'></col>
            <col style='width: 8%'></col>
            <col style='width: 20%'></col>
          </colgroup>
          <thead class='border border-bottom border-dark'>
            <tr>
              <td scope='col' class='border border-end border-dark text-center p-1'>No</td>
              <td scope='col' class='border border-end border-dark text-center p-1'><?=lang('Order.toBePaid')?></td>
              <td scope='col' class='border border-end border-dark text-center p-1'><?=lang('Order.shippingFee')?></td>
              <td scope='col' class='border border-end border-dark text-center p-1'><?=lang('Order.remainBalance')?></td>
              <td scope='col' class='border border-end border-dark text-center p-1'><?=lang('Order.paymentStatus')?></td>
              <td scope='col' class='border border-dark text-center p-1'><?=lang('Order.invoice')?></td>
            </tr>
          </thead>
          <tbody>
          <?php if ( !empty($receipts) ) : 
            foreach ( $receipts as $receipt ) : ?>
            <tr>
              <td class='border border-end border-dark text-center p-1'>
                <?=$receipt['receipt_type']?>
              </td>
              <td class='border border-end border-dark text-end p-1'>
                <?=$order['currency_sign'].number_format($receipt['rq_amount'], $order['currency_float'])?>
              </td>
              <td class='border border-end border-dark text-end p-1'>
                <?=$order['currency_sign'] . number_format($receipt['delivery_price'], $order['currency_float'])?>
              </td>
              <td class='border border-end border-dark text-end p-1'>
                <?=$order['currency_sign'].number_format($receipt['due_amount'], $order['currency_float'])?>
              </td>
              <td class='border border-end border-dark text-center p-1'>
                <?=!empty($receipt['payment_status_msg']) ? $receipt['payment_status_msg'] : ''?>
              </td>
              <td class='border border-dark text-center px-1'>
                <div class='d-flex flex-row justify-content-center'>
                <?php if ( $order['has_payment_url'] ) : ?>
                  <a class='btn btn-secondary btn-sm px-1 me-1' href='<?=$receipt['payment_url']?>' target='_blank'>
                    <?=lang('Order.paypalInvoice')?>
                  </a>
                <?php endif;?>                  
                <?php if ( $receipt['payment_status'] >= 0 ) : ?>
                <form method='post'>
                <div class='d-flex flex-row justify-content-center'>
                  <input type='hidden' name='receipt_type' value='<?=$receipt['receipt_type']?>'>
                  <input type='hidden' name='order_number' value='<?=$order['order_number']?>'>
                  <div class='btn btn-dark btn-sm px-1 pi-view'><?=lang('Order.pi')?> <?=lang('Order.view')?></div>
                </div>
                </form>
                <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach;
          endif ?>
          </tbody>
        </table>
      </div>
    </div>
    <!-- CI, PL -->
    <div class='info-sec p-0 d-flex flex-row align-items-center'>
      <label class='py-2 ps-2'><?=lang('Order.invoice')?></label>
      <div class='p-2 w-80 d-flex flex-row'>
        <?php if ( !empty($packaging) && $packaging['showed'] == 1) : ?>
        <form method='post'>
        <input type='hidden' name='order_number' value='<?=$order['order_number']?>'>
        <div class='btn btn-sm btn-dark me-1'><?=lang('Order.ci')?> <?=lang('Order.view')?></div>
        <div class='btn btn-sm btn-dark'><?=lang('Order.pl')?> <?=lang('Order.view')?></div>
        </form>
        <?php else : ?>
        <span>-</span>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <!-- 배송지 -->
  <div class='p-0 delivery-address-info-section'>
    <div class='info-sec p-0'>
      <label class='py-2 ps-2'>Shipping Information</label>
      <div class='w-100 sub-sec p-2'>
        <table class='w-100'>
          <tbody>
            <tr>
              <th class='w-15 align-baseline'>Consignee</th>
              <td class='py-1'><?=$order['consignee']?></td>
            </tr>
            <tr>
              <th class='align-baseline'>Address</th>
              <td class='py-1'>
                <p class='mb-1'><?=$order['region']?></p>
                <p><?=$order['streetAddr1']?></p>
                <p><?=$order['streetAddr2']?></p>
                <p><?=$order['city']?></p>
              </td>
            </tr>
            <tr>
              <th class='align-baseline'>Postal code</th>
              <td class='py-1'><?=$order['zipcode']?></td>
            </tr>
            <tr>
              <th class='align-baseline'>Phone</th>
              <td class='py-1 plus-sign'><?=$order['phone_code'].' '.preg_replace('/([0-9]{2})([0-9]{4})([0-9]{4})/', '$1-$2-$3', $order['phone'])?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class='p-0 packaging-info-section'>
    <div class='info-sec p-0'>
      <label class='py-2 ps-2'>Packaging Status</label>
      <div class='w-100 sub-sec p-2'>
        <div class='w-100 d-flex flex-row flex-wrap'>
          <?php if ( !empty($packagingStatus) ) : 
            foreach ( $packagingStatus AS $i => $status ) : ?>
            <div class='packagin-status 
              <?=( $status['in_progress'] == 1 ) 
                || ( (empty($status['in_progress']) && empty($status['complete']) && $i == 0) ) ? 'ing': ''?>
              '>
              <?=$status['status_name']?>
            </div>
          <?php endforeach;
          endif; ?>
        </div>
      </div>
    </div>
  </div>
<?php else : ?>
  <div><?=lang('Order.isEmpty')?></div>
<?php endif ?>
<!-- </div> -->