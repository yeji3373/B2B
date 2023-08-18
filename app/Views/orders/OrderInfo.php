<!-- <div class='w-100 d-flex flex-column flex-wrap'> -->
<?php if ( isset($order) && !empty($order) ) : ?>
<div class='w-100 fw-bold d-flex flex-row flex-wrap py-2'>
  <div class='me-3'>
    <label><?=lang('Order.orderDate')?></label>
    <span><?=date('Y-m-d', strtotime($order['created_at']))?></span>
  </div>
  <div>
    <label><?=lang('Order.orderNumber')?></label>
    <span><?=$order['order_number']?></span>
  </div>
</div>
<div class='d-flex flex-column flex-wrap p-0 product-payment-info-section'>
  <div class='info-sec p-0'>
    <label class='py-2 ps-2'><?=lang('Order.paymentType')?></label>
    <?php if ( !empty($order['payment_id']) && !empty($order['complete_payment']) ) : ?>
      <span class='py-2 pm-2'><?=$paymentMethod['payment']?></span>
      <?php if ( $paymentMethod['show_info'] == 1 ) : ?>
      <div class='w-100 p-2 bg-opacity-10 bg-secondary sub-sec'>
        <!-- htmlspecialchars -->
        <span><?=$paymentMethod['payment_info']?></span>
      </div>
      <?php endif; ?>
    <?php else : ?>
      <?php if ( !empty($nowPackStatus) ) : ?>
        <?php if ( !empty($nowPackStatus['payment_request']) ) : ?>
          <form>
          <input type='hidden' name='order[id]' value='<?=$order['id']?>'>
          <?php if ( empty($order['order_check'] )) : ?>
          <button class='btn btn-sm btn-dark my-1 order-check'>결제확정</button>
          <?php else : ?>
            <button class='btn btn-sm btn-dark my-1 order-request'>결제하기</button>
          <?php endif; ?>
          </form>
        <?php else : ?>
          <span class='py-2 pm-2'><?=lang('Order.inventoryChecking')?>
        <?php endif; ?>
      <?php else : ?>
        <!-- 현재 상태값이 없음 -->
      <?php endif; ?>
    <?php endif; ?>
  </div>
    <div class='info-sec p-0'>
    <label class='py-2 ps-2'><?=lang('Order.currency')?></label>
    <span class='py-2 pm-2'><?=$order['currency_code']?></span>
  </div>
  <div class='info-sec p-0'>
    <?php if ( empty($order['order_amount'])) : ?>
    <label class='py-2 ps-2'><?=lang('Order.amount')?></label>
    <span class='py-2 pm-2'><?=$order['currency_sign'].number_format($order['request_amount'], $order['currency_float'])?></span>
    <!-- 재고요청 완료 후, 결제하기 버튼 보여지기. -->
    <?php else: ?>
    <div class='w-100 py-2 px-1 bg-opacity-10 bg-secondary sub-sec'>
      <label class='py-2 ps-2'><?=lang('Order.amount')?></label>
        <!-- 재고 요청 완료 후 값들 -->
    </div>
    <?php endif; ?>
  </div>
  <!-- 재고요청 당시 등록한 주소 & 주소 편집 추가
  재고요청 완료 후, 배송정보 -->
</div>
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
              <?=( $status['in_progress'] == 1 ) 
                || ( (empty($status['in_progress']) && empty($status['complete']) && $i == 0) ) ? 'ing': ''?>
              '>
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