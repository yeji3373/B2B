<?php if( empty($products) ) : ?>
  <div class='py-5 px-2 text-center'>
    <?=lang('Lang.noResultSearch')?>
  </div>  
<?php else : ?>
  <div class='product-list'>
  <?=view('/layout/includes/productItem', ['product' => $products]) ?>
  </div>
<?php endif; ?>