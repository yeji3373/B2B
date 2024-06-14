<div class="py-5 px-2 text-center empty <?=empty($products) ? '' : "d-none"?>">
  <?=lang('Lang.noResultSearch')?>
</div>

<?php 
if ( !empty($products) ) : 
  foreach($products as $product) : 
    echo view('layout/includes/productItem', ['product' => $product]);
  endforeach; 
endif;
?>