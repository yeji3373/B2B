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
            <div class='product-info-item spq'>
              <table class='border border-secondary w-200px'>
                <tr class='border border-secondary'>
                  <td class='border border-secondary w-10'></td>
                  <td class='w-25'>수량</td>
                  <td class='border border-secondary w-25'>가격</td>
                  <td class='w-25'>비고</td>
                </tr>
                <tr class='border border-secondary'>
                  <td class='border border-secondary w-10'>최초</td>
                  <td class='w-25'><?=$product['prd_order_qty']?></td>
                  <td class='border border-secondary w-25'><?=$product['prd_price']?></td>
                  <td class='w-25'></td>
                </tr>
                <tr class='border border-secondary'>
                  <td rowspan='2' class='border border-secondary'>재고</td>
                  <td><?=$product['prd_change_qty']?></td>
                  <td class='border border-secondary'><?=$product['prd_change_price']?></td>
                  <td><?=$product['detail_desc']?></td>
                </tr>
                <tr class='border border-secondary'>
                  <td colspan='3' class='detail_id_check' data-detail-id='<?=$product['detail_id']?>'>
                  <div class='name-group'>
                    <?php foreach($orderRequirement AS $j => $require) :?>
                      <?php if (($product['detail_id'] == $require['order_detail_id']) && (!empty($require['requirement_reply']))) : ?>
                        <span class='product_name text-uppercase fw-bold'>※ <?=$require['requirement_en']?></span> :
                        <span class='product_name text-uppercase fw-bold'><?=$require['requirement_reply']?></span>
                        <?php if(($product['prd_qty_changed'] == 1) && ($require['requirement_id'] == 2)) :?>
                          <span>수량 변경, 리드타임도 있음.</span>
                          <form>
                            <label>
                              <input type='radio' name='detail[<?=$i?>][order_option]' value='order_all'>
                              다 주문하기
                            </label>
                            <label>
                              <input type='radio' name='detail[<?=$i?>][order_option]' value='order_now'>지금재고만주문하기
                            </label>
                            <label>
                              <input type='radio' name='detail[<?=$i?>][order_option]' value='cancel'>취소하기
                            </label>
                            <button class='btn btn-primary btn-small'>저장</button>
                          </form>
                        <?php elseif(($product['prd_qty_changed'] == 0) && ($require['requirement_id'] == 2)) :?>
                          <span>수량은 변경 x, 리드타임만 있음. (= 전량주문해야 한다는 뜻)</span>
                          <form>
                            <label>
                              <input type='radio' name='detail[<?=$i?>][order_option]' value='order_all'>다 주문하기
                            </label>
                            <label>
                              <input type='radio' name='detail[<?=$i?>][order_option]' value='cancel'>취소하기
                            </label>
                            <button class='btn btn-primary btn-small'>저장</button>
                          </form>
                        <?php endif ?>
                      <?php endif ?>
                    <?php endforeach ?>
                  </div>
                    <span>짜바리</span>
                  </td>
                </tr>
                <tr class='border border-secondary'>
                  <td class='border border-secondary'>포장</td>
                  <td><?=$product['prd_change_qty']?></td>
                  <td class='border border-secondary'><?=$product['prd_change_price']?></td>
                  <td></td>
                </tr>
              </table>
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