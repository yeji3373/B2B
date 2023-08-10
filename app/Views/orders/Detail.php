<div class='d-flex flex-column list-group'>
  <?php if ( isset($orderDetails) && !empty($orderDetails) ) : ?>
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
            <div class='name-group'>
              <span class='product_name text-uppercase fw-bold'>qty : <?=$product['prd_order_qty']?></span>
              <span class='product_name text-uppercase fw-bold'>price : <?=$product['prd_price']?></span>
            </div>
            <div class='name-group'>
              <?php foreach($orderRequirement AS $j => $require) :?>
                <?php if (($product['detail_id'] == $require['order_detail_id']) && (!empty($require['requirement_reply']))) : ?>
                  <span class='product_name text-uppercase fw-bold'>※ <?=$require['requirement_en']?></span> :
                  <span class='product_name text-uppercase fw-bold'><?=$require['requirement_reply']?></span>
                <?php endif ?>
              <?php endforeach ?>
            </div>
          <?php endif;?>
        </div>
      </div>
    </div>
    <?php endforeach ?>
  <?php else : ?>
    <div><?=lang('Order.isEmpty')?></div>
  <?php endif ?>
</div>