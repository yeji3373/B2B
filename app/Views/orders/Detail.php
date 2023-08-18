<div class='d-flex flex-column list-group'>
  <?php if ( isset($orderDetails) && !empty($orderDetails) ) : ?>
    <?php $orderTotal = 0?>
    <?php foreach($orderDetails AS $i => $product) : ?>
    <div class='d-flex flex-column border <?=($i > 0 ? 'border-top-0': '')?> justify-content-between p-0 list-group-item <?=$product['prd_discount'] > 0 ? "apply-discount": ""?>'>
      <div class='d-flex flex-row align-items-center product-item mx-0 my-auto pt-2 pb-1 px-2'>
        <?=img(esc($product['img_url']), false, ['class' => 'thumbnail me-2']);?>
        <div class='d-flex flex-column'>
          <div class='name-group'>
            <span class='brand_name bracket text-uppercase'><?=$product['brand_name']?></span>
            <span class='product_name text-uppercase'><?=$product['name_en']?></span>
            <?=$product['spec'].($product['box'] == 1 || $product['spec_pcs'] > 0 ? "/pc" : "")?>
            </span>
          </div>
          <?php if ( !empty($product['type_en']) ) : ?>
          <div>
            <label class='w-15'>Type : </label>
            <span class='fw-bold'><?="#".$product['type_en']?></span>
          </div>
          <?php endif ?>
          <div>
            <label>Barcode:</label>
            <span><?=empty($product['barcode']) ? '' : $product['barcode']?></span>
          </div>
        </div>
      </div>
      <div class='d-flex flex-column bg-light pt-1 pb-2 px-2'>
      <?php if ( empty($product['order_excepted']) ) : ?>
        <div>
          <label class='w-20'><?=lang('Order.productWeight')?></label>
          <span><?=empty($product['shipping_weight']) ? '-' : number_format($product['shipping_weight']).'g';?></span>
        </div>
        <div>
          <label class='w-20'>Unit price</label>
          <span>
            <?php if ( !empty($product['prd_price']) ) {
              echo session()->currency['currencySign'].number_format($product['prd_price'], session()->currency['currencyFloat']);
            } ?>
          </span>
        </div>
        <div>
          <label class='w-20'>Request Qty</label>
          <span>
            <?php if ( !empty($product['prd_order_qty']) ) {
              echo number_format($product['prd_order_qty']);
            } ?>
          </span>
        </div>
        <?php 
          if ( !empty($nowPackStatus) && $nowPackStatus['order_by'] > 5 ) : 
            if ( !empty($product['requirement_reply']) ) : 
            $requirementDetail = explode("|", $product['requirement_reply']);
            if ( !empty($requirementDetail) ) :
              foreach($requirementDetail AS $rDetail) :
                $requirement = explode(",", $rDetail);

                if ( !empty($requirement[2]) ) :
                echo "<div class='d-flex flex-column'>";
                echo "<div class='f-flex flex-row'>";
                echo "<label class='w-20'>".$requirement[1]."</label>";
                echo "<span>".$requirement[2]."</span>";
                echo "</div>";
                if ( $requirement[0] == 2 ) :
                  echo "<div class='w-100'>";
                  // echo "<input type='radio' name"
                  echo "</div>";
                endif;
                echo "</div>";
                endif;
              endforeach;
            endif;            
            endif;
          endif; ?>
      <?php else: ?>
        <div class='d-flex flex-row'>
          <label class='w-20'>주문상태</label>
          <span>취소됨</span>
        </div>
        <?php if ( !empty($product['detail_desc'])) : ?>
        <div class='d-flex flex-row'>
          <label class='w-20'>취소 사유</label>
          <div class='w-80'><?=$product['detail_desc']?></div>
        </div>
        <?php endif;?>
      <?php endif;?>
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