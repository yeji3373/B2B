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
            <span class=''>
              <?=$product['spec'].($product['box'] == 1 || $product['spec_pcs'] > 0 ? "/pc" : "")?>
            </span>
            <?php if ( !empty($product['type_en']) ) : ?>
              <span class='fw-bold'><?="#".$product['type_en']?></span>
            <?php endif ?>
          </div>
          <?php if ($product['order_excepted'] == 1) : ?> 
          <div>
            <span>주문 제외 상품</span>
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