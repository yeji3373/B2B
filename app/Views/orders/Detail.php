<div class='d-flex flex-column list-group'>
  <?php if ( isset($orderDetails) && !empty($orderDetails) ) : ?>
    <?php $orderTotal = 0?>
    <?php foreach($orderDetails AS $i => $product) : ?>
    <div class='d-flex flex-column border <?=($i > 0 ? 'border-top-0': '')?> justify-content-between py-1 px-2 list-group-item <?=$product['prd_discount'] > 0 ? "apply-discount": ""?>'>
      <div class='d-flex flex-row align-items-start product-item mx-0 my-auto'>
        <?=img(esc($product['img_url']), false, ['class' => 'thumbnail me-2']);?>
        <div class='d-flex flex-column'>
          <div class='name-group'>
            <span class='brand_name bracket text-uppercase'><?=$product['brand_name']?></span>
            <span class='product_name text-uppercase'><?=$product['name_en']?></span>
            <?=$product['spec'].($product['box'] == 1 || $product['spec_pcs'] > 0 ? "/pc" : "")?>
            </span>
            <!-- 상세타입이 있는지 여부 -->
            <?php if ( !empty($product['type_en']) ) : ?>
              <span class='fw-bold'><?="#".$product['type_en']?></span>
            <?php endif ?>
            <span class=''>
          </div>
          <?php if ($product['order_excepted'] == 1) : ?> 
          <div> 
            <span>주문 취소</span>
            <?php if (!empty($product['detail_desc'])) : ?>
            <div>
              <span class='fw-bold'>※ <?=$product['detail_desc']?></span>
            </div>
            <?php endif ?>
          </div>
          <?php else : ?>
            <!-- <div class='name-group'>
              <span class='product_name text-uppercase fw-bold'>qty : <?=$product['prd_order_qty']?></span>
              <span class='product_name text-uppercase fw-bold'>price : <?=$product['prd_price']?></span>
              <?php $total = number_format(($product['prd_order_qty'] * $product['prd_price']), 2)?>
              <?php $orderTotal += $total?> 
              <span class='product_name text-uppercase fw-bold'>total : <?=$total?></span>
            </div> 
            <div class='name-group'>
              <?php foreach($orderRequirement AS $j => $require) :?>
                <?php if (($product['detail_id'] == $require['order_detail_id']) && (!empty($require['requirement_reply']))) : ?>
                  <span class='product_name text-uppercase fw-bold'>※ <?=$require['requirement_en']?></span> :
                  <span class='product_name text-uppercase fw-bold'><?=$require['requirement_reply']?></span>
                <?php endif ?>
              <?php endforeach ?>
            </div> -->
            <div class='product-info-item spq w-450px'>
              <div class='d-flex flex-column border border-1 border-dark mb-2'>
                <div class='w-100 d-flex flex-row border-bottom border-dark text-center'>
                  <div class='w-25 border-end border-dark'></div>
                  <div class='w-25 border-end border-dark'>수량</div>
                  <div class='w-25 border-end border-dark'>가격</div>
                  <div class='w-25'>비교</div>
                </div>
                <div class='w-100 d-flex flex-column'>
                  <div class='w-100 d-flex flex-row border-bottom border-dark'>
                    <div class='w-25 border-end border-dark text-center'>최초</div>
                    <div class='w-25 border-end border-dark text-center'><?=$product['prd_order_qty']?></div>
                    <div class='w-25 border-end border-dark text-center'><?=$product['prd_price']?></div>
                    <div class='w-25 text-center'>-</div>
                  </div>
                  <div class='w-100 d-flex flex-row border-bottom border-dark'>
                    <div class='w-25 border-end border-dark text-center'>재고</div>
                    <div class='w-75 d-flex flex-column'>
                      <div class='w-100 d-grid grid-half2 text-end'>
                        <div class='border-end border-dark text-center'><?=$product['prd_change_qty']?></div>
                        <div class='border-end border-dark text-center'><?=$product['prd_change_price']?></div>
                        <div class='text-center'><?=!empty($product['detail_desc']) ? $product['detail_desc'] : '-' ?></div>
                      </div>
                      <?php foreach($orderRequirement AS $j => $require) :?>
                      <?php if (($product['detail_id'] == $require['order_detail_id']) 
                      && (!empty($require['requirement_reply']))) : ?>
                      <div class='w-100 border-top border-dark p-1'>
                      <span class='product_name text-uppercase fw-bold'>※ <?=$require['requirement_en']?></span> :
                        <span class='product_name text-uppercase fw-bold'><?=$require['requirement_reply']?></span>
                        <?php if(($product['prd_qty_changed'] == 1) && ($require['requirement_id'] == 2) && ($nowPackStatus['order_by'] == 5)) :?>
                          <span>수량 변경, 리드타임도 있음.</span>
                          <form name='optionForm' detail_id_check=<?=$i?>>
                            <label>
                              <input type='radio' name='detail[<?=$i?>][order_option]' value='order_all'>다 주문하기
                            </label>
                            <label>
                              <input type='radio' name='detail[<?=$i?>][order_option]' value='order_now'>지금재고만주문하기
                            </label>
                            <label>
                              <input type='radio' name='detail[<?=$i?>][order_option]' value='cancel'>취소하기
                            </label>
                            <button class='btn btn-primary btn-small'>저장</button>
                          </form>
                        <?php elseif(($product['prd_qty_changed'] == 0) && ($require['requirement_id'] == 2) && ($nowPackStatus['order_by'] == 5)) :?>
                          <span>수량은 변경 x, 리드타임만 있음. (= 전량주문해야 한다는 뜻)</span>
                          <form name='optionForm2'>
                            <label>
                              <input type='radio' name='detail[<?=$i?>][order_option]' value='order_all'>다 주문하기
                            </label>
                            <label>
                              <input type='radio' name='detail[<?=$i?>][order_option]' value='cancel'>취소하기
                            </label>
                            <button class='btn btn-primary btn-small'>저장</button>
                          </form>
                        <?php endif ?>
                      </div>
                      <?php endif;
                      endforeach; ?>
                    </div>
                  </div>
                  <div class='w-100 d-flex flex-row'>
                    <div class='w-25 border-end border-dark text-center'>포장</div>
                    <div class='w-25 border-end border-dark text-center'><?=$product['prd_change_qty']?></div>
                    <div class='w-25 border-end border-dark text-center'><?=$product['prd_change_price']?></div>
                    <div class='w-25 text-center'>-</div>
                  </div>
                </div>
              </div>
            </div>
            <div class='name-group'>
              <?php if(!empty($product['detail_desc'])) : ?>
                <span class='product_name text-uppercase'>기타 : <?=$product['detail_desc']?></span>
              <?php else :?>
              <?php endif?>
            </div>
          <?php endif?>
        </div>
      </div>
    </div>
    <?php endforeach ?>
    <span></span>
    <span class='order_total'><?=$product['currency_sign'].number_format($orderTotal, $product['currency_float'])?></span>
    <!-- <button class='btn btn-primary inventory_check_request-btn' data-bs-target='.pre-order' aria-confirm='재고체크 확인 요청을 취소하겠습니까?'>
      <?=lang('Order.ordermore')?>
    </button> -->
  <?php else : ?>
    <div><?=lang('Order.isEmpty')?></div>
  <?php endif ?>
</div>
<div class='pre-order'></div>