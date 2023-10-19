<!-- <div class='w-100 d-flex flex-column flex-wrap'> -->
<?php if ( isset($order) && !empty($order) ) : ?>
<div class='w-100 fw-bold d-flex flex-row flex-wrap py-2'>
  <div class='me-3'>
    <label><?=lang('Lang.orders.orderDate')?></label>
    <span><?=date('Y-m-d', strtotime($order['created_at']))?></span>
  </div>
  <div>
    <label><?=lang('Lang.orders.orderNumber')?></label>
    <span><?=$order['order_number']?></span>
  </div>
</div>
<div class='d-flex flex-column flex-wrap p-0 product-payment-info-section'>
  <div class='info-sec p-0'>
    <label class='py-2 ps-2'><?=lang('Lang.orders.paymentType')?></label>
    <?php if ( !empty($paymentMethod) ) : ?>
      <span class='py-2 pm-2'><?=$paymentMethod['payment']?></span>
      <?php if ( $paymentMethod['show_info'] == 1 ) : ?>
      <div class='w-100 p-2 bg-opacity-10 bg-secondary sub-sec'>
        <!-- htmlspecialchars -->
        <span><?=$paymentMethod['payment_info']?></span>
      </div>
      <?php endif; ?>
    <?php else : ?>
      <?php if ( !empty($nowPackStatus) ) : ?>
        <form class='align-self-center m-0'>
        <input type='hidden' name='order[id]' value='<?=$order['id']?>'>
        <?php if($nowPackStatus['requirement_option_check']) : 
                if(is_null($nowPackStatus['requirement_option_disabled'])) : ?>
                  <button class='btn btn-sm btn-dark my-1 order-check' data-confirm-msg='<?=lang('Lang.msg.statusChooseReCheck')?>'><?=lang('Lang.orders.detail.paymentConfirm')?></button>
          <?php else :
                  if (($nowPackStatus['payment_request'])) : ?>
                    <button class='btn btn-sm btn-dark my-1 order-request'><?=lang('Lang.checkout.checkout')?></button>
            <?php else : ?>
                    <span class='py-2 pm-2'><?=lang('Lang.orders.orderChecking')?>
            <?php endif; 
                endif; ?>
        </form>
        <?php else : ?>
                <span class='py-2 pm-2'><?=lang('Lang.orders.inventoryChecking')?>
        <?php endif; ?>
      <?php else : ?>
        <!-- 현재 상태값이 없음 -->
      <?php endif; ?>
    <?php endif; ?>
  </div>
    <div class='info-sec p-0'>
    <label class='py-2 ps-2'><?=lang('Lang.orders.currency')?></label>
    <span class='py-2 pm-2'><?=$order['currency_code']?></span>
  </div>
  <div class='info-sec p-0'>
    <?php if (!empty($nowPackStatus['pay_step']) ) : 
      $amount = $order['request_amount'];
      if ( $nowPackStatus['pay_step'] == 2 ) $amount = $order['inventory_fixed_amount'];
      else if ( $nowPackStatus['pay_step'] == 3 ) $amount = $order['fixed_amount'];
      else if ( $nowPackStatus['pay_step'] == 4 ) $amount = $order['decide_amount'];
    ?>
    <label class='py-2 ps-2'><?=lang('Lang.orders.amount')?></label>
    <span class='py-2 pm-2'><?=$order['currency_sign'].number_format($amount, $order['currency_float'])?></span>
    <!-- 재고요청 완료 후, 결제하기 버튼 보여지기. -->
    <?php else: ?>
    <div class='w-100 py-1 px-1 bg-light sub-sec fw-bold'>
      <label class='py-2 ps-2'><?=lang('Lang.orders.amount')?></label>
      <span class='py-2 pm-2 fs-6'><?=$order['currency_sign'].number_format($order['request_amount'], $order['currency_float'])?></span>
    </div>
    <?php endif; ?>
  </div>
  <!-- 재고요청 당시 등록한 주소 & 주소 편집 추가
  재고요청 완료 후, 배송정보 -->
</div>
<?php if ( !empty($receipts) ) : ?>
<div class='p-0 receipt-info-section'>
  <div class='info-sec p-0 d-flex flex-column'>
    <div class='py-2 ps-2 border-bottom text-capitalize w-100'><?=lang('Lang.orders.payment.receipts')?></div>
    <div class='w-100 sub-sec p-2'>
      <div class='d-flex flex-column border border-1'>
        <div class='w-100 head d-grid text-center text-capitalize'>
          <div><?=lang('Lang.orders.payment.receipt')?></div>
          <div><?=lang('Lang.orders.payment.status')?></div>
          <div><?=lang('Lang.orders.payment.toBePaid')?></div>
          <div><?=lang('Lang.orders.payment.remainBalance')?></div>
          <div><?=lang('Lang.orders.shippingFee')?></div>
          <div><?=lang('Lang.remark')?></div>
        </div>
      <?php foreach ($receipts as $key => $receipt) {?>
        <div class='w-100 d-grid border-top'>
          <div class='p-1 border-end text-center'>
            <?php if ( $receipt['receipt_type'] == 1 ) : 
              echo esc($receipt['receipt_type']).'st '.lang('Lang.orders.payment.receipt');
            elseif ( $receipt['receipt_type'] == 2 ) : 
              echo esc($receipt['receipt_type']).'nd '.lang('Lang.orders.payment.receipt');
            elseif ( $receipt['receipt_type'] == 3 ) : 
              echo esc($receipt['receipt_type']).'rd' .lang('Lang.orders.payment.receipt');
            else : 
              echo esc($receipt['receipt_type']).'th '.lang('Lang.orders.payment.receipt');
            endif; ?>
          </div>
          <div class='p-1 border-end text-center'>
            <?=esc($receipt['payment_status_msg'])?>
          </div>
          <div class='border-end p-1 text-end'>
            <?=$order['currency_sign'].number_format($receipt['rq_amount'], $order['currency_float'])?>
          </div>
          <div class='border-end p-1 text-end'>
            <?=$order['currency_sign'].number_format($receipt['due_amount'], $order['currency_float'])?>
          </div>
          <div class='border-end p-1 text-end'>
            <?php if ( !empty($receipt['delivery_id']) ) : ?>
              <?php 
                if ( !empty($receipt['delivery_price']) ) :
                  echo $order['currency_sign'].number_format($receipt['delivery_price'], $order['currency_float']);
                else: 
                  echo "정산전";
                endif;
              else: 
                echo "-";
            endif;?>
          </div>
          <div class='p-1 text-center d-flex flex-column'>
            <?php if ( !empty($receipt['payment_invoice_id']) ) : ?>
              <div>
              <?php 
                if ( !empty($receipt['payment_refund_id']) ) :
                  echo esc($receipt['payment_refund_id']);
                else: 
                  echo "<a href='".$receipt['payment_url']."' target='_blank'>".$receipt['payment_invoice_id']."</a>";
                endif;
                echo "</div>";
            endif;?>
            <?php if ( $receipt['payment_status'] >= 0 ) : ?>
              <form method='post'>
                <input type='hidden' name='receipt_type' value='<?=$receipt['receipt_type']?>'>
                <input type='hidden' name='order_number' value='<?=$order['order_number']?>'>
                <input type='hidden' name='just_data' value='1'>
                <div class='btn btn-dark btn-sm px-1 pi-view'><?=lang('Lang.orders.pi')?> <?=lang('Lang.orders.view')?></div>
              </form>
            <?php endif; ?>
          </div>
        </div>
      <?php } ?>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
<div class='p-0 packaging-info-section'>
  <div class='info-sec p-0'>
    <div class='py-2 ps-2 border-bottom w-100'>Adress</div>
    <div class='w-100 sub-sec p-2 pt-0'>
      <span class='consignee d-none'><?=$order['consignee']?></span>
      <div>
        <span class='region' data-id='<?=$order['region']?>' data-ccode='<?=$order['country_code']?>'><?=$order['region']?></span> / <span class='city'><?=$order['city']?></span>
      </div>
      <div>
        <span class='streetAddr1'><?=$order['streetAddr1']?></span>
        <span class='streetAddr2'><?=$order['streetAddr2']?></span>
      </div>
      <div class='d-flex flex-row'>
        <?php if ( isset($order['zipcode']) ) : ?>
        <div class='zipcode me-2'><?=$order['zipcode']?></div>
        <?php endif ?>
        <div class='d-flex flex-row'>
          <span class='phone_code'><?=$order['phone_code']?></span>
          <span class='phone'><?=$order['phone']?></span>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- <div class='p-0'>
  <div class='info-sec p-0'>
    invoice 보기
  </div>
</div> -->
<div class='p-0 packaging-info-section'>
    <div class='info-sec p-0'>
      <div class='py-2 ps-2 border-bottom w-100'>Packaging Status</div>
      <div class='w-100 sub-sec p-2'>
        <div class='w-100 d-flex flex-row flex-wrap'>
          <?php if ( !empty($packagingStatus) ) : 
            foreach ( $packagingStatus AS $i => $status ) : ?>
            <div class='packagin-status 
              <?php 
              if ( !empty($nowPackStatus) ) :
                if ( $status['order_by'] <= $nowPackStatus['order_by'] ) echo 'ing';
              endif;
              ?>'>
              <div class='circle'></div>
              <div class='status-name text-center'><?=$status['status_name_en']?></div>
            </div>
          <?php endforeach;
          endif; ?>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>