<div class='d-flex flex-column list-group'>
  <?php if ( isset($orderDetails) && !empty($orderDetails) ) : ?>
    <?php 
    foreach($orderDetails AS $i => $product) : 
    ?>
    <div class='d-flex flex-column border <?=($i > 0 ? 'border-top-0': '')?> justify-content-between p-0 list-group-item'>
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
            <label>Type : </label>
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
          <label class='w-20'><?=lang('Lang.productWeight')?></label>
          <span><?=empty($product['shipping_weight']) ? '-' : number_format($product['shipping_weight']).'g';?></span>
        </div>
        <div class='d-flex flex-row'>
          <label class='w-20'><?=lang('Lang.productPrice')?></label>
          <div class='w-80 d-flex flex-row align-items-lg-end'>
            <?php
            $priceClass = '';
            $changedClass = '';
            
            if ( !empty($nowPackStatus) && !empty($nowPackStatus['requirement_option_check']) ) : 
              if ( !empty($product['prd_change_price']) ) :
                $priceClass = 'pe-2';
                $changedClass = 'fw-bold font-size-9';
              else : 
                $priceClass = 'fw-bold font-size-9';
              endif; 
            endif;

            echo "<span class='".$priceClass."'>";
            echo session()->currency['currencySign'].number_format($product['prd_price'], session()->currency['currencyFloat']);
            echo "</span>";
            
            if ( !empty($nowPackStatus) && !empty($nowPackStatus['requirement_option_check']) && !empty($product['prd_change_price'])) : 
              echo "<span class='".$changedClass."'>";
              echo session()->currency['currencySign'].number_format($product['prd_change_price'], session()->currency['currencyFloat']);
              echo "</span>";
            endif;
            ?>
          </div>
        </div>
        <div class='d-flex flex-row'>
          <label class='w-20'><?=lang('Lang.qty')?></label>
          <div class='w-80 d-flex flex-row align-items-lg-end'>
          <?php
            $priceClass = '';
            $changePriceClass = '';
            $fixedPriceClass = '';

            if ( !empty($nowPackStatus) && !empty($nowPackStatus['requirement_option_check']) ) {
              if ( !empty($product['prd_change_qty']) ) {
                $priceClass = 'pe-1';
                if ( !empty($order) && !empty($order['order_fixed']) ) {
                  if ( !empty($product['prd_fixed_qty']) ) {
                    $changePriceClass = 'pe-1';
                    $fixedPriceClass = 'fw-bold font-size-9';
                  } else {
                    $changePriceClass = 'fw-bold font-size-9';
                  }
                } else {
                  if ( !empty($product['prd_change_qty']) ) {
                    $changePriceClass = 'fw-bold font-size-9';
                  }
                }
              } else $priceClass = 'fw-bold font-size-9';
            }
            
            if ( !empty($product['prd_order_qty']) ) { 
              echo "<div>
                      <span class='{$priceClass}'>".number_format($product['prd_order_qty'])."ea</span>
                    </div>";
            }

            if ( !empty($nowPackStatus) && !empty($nowPackStatus['requirement_option_check']) ) {
              if ( !empty($order) && !empty($order['order_fixed']) ) {
                if( !empty($product['prd_fixed_qty']) ) {
                  echo "<div>
                          <label>".lang("Lang.orders.detail.finalQty")." : </label>
                          <span class='{$fixedPriceClass}'>".number_format($product['prd_fixed_qty'])."ea</span>
                        </div>";
                }
              } else {
                if ( !empty($product['prd_change_qty']) ) {
                  echo "<div>
                          <label>".lang('Lang.orders.detail.securedQty')." : </label>
                          <span class='{$changePriceClass}'>".number_format($product['prd_change_qty'])."ea</span>
                        </div>";
                }
              }
            }
          ?>
          </div>
            
        </div>
        <div class='d-flex flex-row'>
          <?php
          $orderPrice = 0;
          $prd_price = $product['prd_price'];
          $prd_qty = $product['prd_order_qty'];

          if ( empty($nowPackStatus) && empty($nowPackStatus['requirement_option_check']) ) {
            if ( !empty($product['prd_change_price']) ) $prd_price = $product['prd_change_price'];

            if ( !empty($product['prd_change_qty']) ) {
              $prd_qty = $product['prd_change_qty'];
              
              if ( !empty($order) && !empty($order['order_fixed']) ) {
                if ( !empty($product['prd_fixed_qty']) ) {
                  $prd_qty = $product['prd_fixed_qty'];
                }
              }
            }
            $orderPrice = ($prd_price * $prd_qty);
          }
          ?>
          <label class='w-20'><?=lang('Lang.orders.totalAmount')?></label>
          <div class='w-80 d-flex flex-row align-items-lg-end'>
            <span><?=session()->currency['currencySign'].number_format($orderPrice, session()->currency['currencyFloat'])?></span>
            <?php 
            // if(!empty($product['prd_fixed_qty'])) :
            //   echo "<span>";
            //   echo session()->currency['currencySign'].number_format(($product['prd_fixed_qty'] * $product['prd_price']), session()->currency['currencyFloat']);
            //   echo "</span>";
            // else :
            //   if (!empty($product['prd_qty_changed']) || !empty($product['prd_price_changed'])) :
            //     echo "<span>";
            //     echo session()->currency['currencySign'].number_format($orderPrice, session()->currency['currencyFloat']);
            //     echo "</span>";
            //   else :
            //     echo "<span>";
            //     echo  session()->currency['currencySign'].number_format(($product['prd_order_qty'] * $product['prd_price']), session()->currency['currencyFloat']);
            //     echo "</span>";
            //   endif;
            // endif;
            ?>
          </div>
        </div>
        <?php 
          if ( !empty($nowPackStatus) && !empty($nowPackStatus['requirement_option_check']) ) : 
            if ( !empty($orderRequirement)) :
              foreach($orderRequirement AS $i => $r) :
                if($product['detail_id'] == $r['order_detail_id']) :
                  if(isset($r['options'])) : ?>
                  <fieldset class='border <?=$i > 0 ? 'mt-3' : 'mt-2'?> pb-2 position-relative pt-3 px-2'>
                    <legend class='position-absolute'><?=$r['requirement_en']?></legend>
                    <form class='requirmentOptForm'>
                      <input type='hidden' name='idx' value="<?=$r['idx']?>">
                      <input type='hidden' name='detail_id' value="<?=$r['order_detail_id']?>">
                      <input type='hidden' name='requirement_id' value="<?=$r['requirement_id']?>">
                      
                      <?php if(!empty($r['requirement_reply'])) : ?>
                        <div class='d-flex flex-row mb-2'>
                          <label class='pe-2'>Answers to requirements :</label>
                          <span><?=$r['requirement_reply']?></span>
                        </div>
                      <?php endif; ?> 
                      <div class='d-flex flex-column'>
                        <div class='d-flex flex-column'>
                        <?php foreach($r['options'] AS $o) : ?>
                          <label class='ms-1 pb-1 d-flex flex-row align-items-center'>
                            <input type='radio' name='requirement_selected_option_id' value='<?=$o['idx']?>'
                              <?php 
                                if($o['idx'] == $r['requirement_selected_option_id']) echo "checked"; 
                                if( !empty($nowPackStatus['requirement_option_disabled']) ) echo "disabled";
                              ?>
                            >
                            <div class='ps-1'><?=$o['option_name_en']?></div>
                          </label>
                        <?php endforeach; ?>
                        </div>
                        <button class='btn btn-sm btn-secondary confirmbtn px-2 py-0 align-self-center'
                        <?php 
                          if($nowPackStatus['requirement_option_disabled'] ) echo " disabled";
                          ?>
                        ><?=lang('Lang.draft')?></button>
                      </div>
                    </form>
                  </fieldset>
            <?php endif;
                endif;
              endforeach;
            endif ;
          endif;?>
      <?php else: ?>
        <div class='d-flex flex-row'>
          <label class='w-20'><?=lang('Lang.orders.detail.orderStatus')?></label>
          <span><?=lang('Lang.orders.detail.orderCanceled')?></span>
        </div>
        <?php if ( !empty($product['detail_desc'])) : ?>
        <div class='d-flex flex-row'>
          <label class='w-20'><?=lang('Lang.orders.detail.cancelReason')?></label>
          <div class='w-80'><?=$product['detail_desc']?></div>
        </div>
        <?php endif;?>
      <?php endif;?>
      </div>
    </div>
    <?php endforeach ?>
  <?php else : ?>
    <div><?=lang('Lang.isEmpty')?></div>
  <?php endif ?>
</div>