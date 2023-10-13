<div class='d-flex flex-column list-group'>
  <?php if ( isset($orderDetails) && !empty($orderDetails) ) : ?>
    <?php 
    foreach($orderDetails AS $i => $product) : 
    ?>
    <div class='d-flex flex-column border <?=($i > 0 ? 'border-top-0': '')?> justify-content-between p-0 list-group-item'>
      <div class='d-flex flex-row align-items-center product-item mx-0 my-auto pt-2 pb-1 px-2'>
        <?=img(esc($product['img_url']), false, ['class' => 'thumbnail me-2 align-self-baseline']);?>
        <div class='d-flex flex-column'>
          <div class='name-group'>
            <span class='brand_name bracket text-uppercase'><?=$product['brand_name']?></span>
            <span class='product_name text-uppercase'><?=$product['name_en']?></span>
            <?=$product['spec'].($product['box'] == 1 || $product['spec_pcs'] > 0 ? "/pc" : "")?>
            </span>
          </div>
          <?php if ( !empty($product['type_en']) ) : ?>
          <div>
            <label class='w-25'><?=lang('Lang.productType')?></label>
            <span class='fw-bold'>
              <?php if ( str_contains($product['type_en'], "#") ) :
                echo $product['type_en']; 
              else :
                echo "#".$product['type_en']; 
              endif; ?>
            </span>
          </div>
          <?php endif ?>
          <div>
            <label class='w-25'><?=lang('Lang.productBarcode')?></label>
            <span><?=empty($product['barcode']) ? '' : $product['barcode']?></span>
          </div>
          <?php if ( !empty($product['shipping_weight']) ) : ?>
          <div>
            <label class='w-25'><?=lang('Lang.productWeight')?></label>
            <span><?=number_format($product['shipping_weight'])?>g</span>
          </div>
          <?php endif; ?>
          <div>
            <label class='w-25'><?=lang('Lang.productPrice')?></label>
            <span><?=session()->currency['currencySign'].number_format($product['prd_price'], session()->currency['currencyFloat']);?></span>
          </div>
          <div>
            <label class='w-25'><?=lang('Lang.qty')?></label>
            <span><?=number_format($product['prd_order_qty'])?>ea</span>
          </div>
          <div>
            <label class='w-25'><?=lang('Lang.orders.detail.initialAmount')?></label>
            <span><?=session()->currency['currencySign'].number_format(($product['prd_order_qty'] * $product['prd_price']), session()->currency['currencyFloat'])?></span>
          </div>
        </div>
      </div>
      <?php if ( !empty($nowPackStatus) && !empty($nowPackStatus['requirement_option_check']) ) : ?>
      <div class='d-flex flex-column bg-light pt-1 pb-2 px-2'>      
        <?php if ( empty($product['order_excepted']) ) : ?>
        <div class='d-flex flex-row'>
          <label class='w-30'><?=lang('Lang.orders.detail.fixedProductPrice')?></label>
          <div class='w-70 d-flex flex-row align-items-lg-end'>
            <?php
            $orderPrice = 0;
            $prd_price = $product['prd_price'];
  
            if ( $nowPackStatus['pay_step'] > 1 ) {
              if ( !empty($product['prd_change_price']) ) $prd_price = $product['prd_change_price'];
            }

            echo session()->currency['currencySign'].number_format($prd_price, session()->currency['currencyFloat']);
            ?>
          </div>
        </div>
        <div class='d-flex flex-row'>
          <label class='w-30'>
            <?php
              if ( $nowPackStatus['pay_step'] == 2 ) echo lang('Lang.orders.detail.fixedQty');
              else if ( $nowPackStatus['pay_step'] >= 3 ) echo lang('Lang.orders.detail.finalQty');
              else echo lang("Lang.orders.detail.securedQty");
            ?>
          </label>
          <div class='w-70 d-flex flex-row align-items-end'>
            <?php
              $prd_qty = $product['prd_order_qty'];
              if ( $nowPackStatus['pay_step'] == 1 ) {
                if ( !empty($product['prd_change_qty']) ) $prd_qty = $product['prd_change_qty'];
              } else if ( $nowPackStatus['pay_step'] == 2 ) {
                if ( !empty($product['prd_fixed_qty']) ) $prd_qty = $product['prd_fixed_qty'];
              } else if ( $nowPackStatus['pay_step'] == 3 ) {
                if ( !empty($product['prd_final_qty']) ) $prd_qty = $product['prd_final_qty'];
              }
              echo $prd_qty."ea";
            ?>
          </div>            
        </div>
        <div class='d-flex flex-row'>
          <?php
            $orderPrice = ($prd_price * $prd_qty);
          ?>
          <label class='w-30'>
            <?php if ( $nowPackStatus['pay_step'] >= 3 ) { 
              echo lang('Lang.orders.detail.finalAmount');
            } else {
              echo lang('Lang.orders.detail.fixedAmount');
            } ?>
          </label>
          <div class='w-70 d-flex flex-row align-items-lg-end'>
            <span><?=session()->currency['currencySign'].number_format($orderPrice, session()->currency['currencyFloat'])?></span>
          </div>
        </div>
        <?php if ( !empty($orderRequirement)) :
          $rIdx = 0;
          foreach($orderRequirement AS $r) :
            if($product['detail_id'] == $r['order_detail_id']) :
              if(isset($r['options'])) : ?>
                <fieldset class='border <?=$rIdx > 0 ? 'mt-3' : 'mt-2'?> pb-2 position-relative pt-3 px-2'>
                <legend class='position-absolute'><?=$r['requirement_en']?></legend>
                  <form class='requirmentOptForm'>
                    <input type='hidden' name='requirement[<?=$i?>][<?=$rIdx?>][idx]' value="<?=$r['idx']?>">
                    <input type='hidden' name='requirement[<?=$i?>][<?=$rIdx?>][order_detail_id]' value="<?=$r['order_detail_id']?>">
                    <input type='hidden' name='requirement[<?=$i?>][<?=$rIdx?>][requirement_id]' value="<?=$r['requirement_id']?>">
                    
                    <?php if(!empty($r['requirement_reply'])) : ?>
                    <div class='d-flex flex-row mb-2'>
                      <label class='pe-2'>Answers to requirements :</label>
                      <span><?=$r['requirement_reply']?></span>
                    </div>
                    <?php endif; ?>

                    <div class='d-flex flex-column'>
                      <div class='d-flex flex-column'>
                        <?php foreach($r['options'] AS $o) : 
                          $checked = NULL;
                          $bold = NULL;
                          $disabled = NULL;

                          if ( $o['idx'] == $r['requirement_selected_option_id'] ) {
                            $checked = " checked";
                            $bold = 'fw-bold';
                          }
                          if ( !empty($nowPackStatus['requirement_option_disabled']) ) $disabled = " disabled";
                        ?>
                        <label class='ms-1 pb-1 d-flex flex-row align-items-center <?=$bold?>'>
                          <input type='radio' name='requirement[<?=$i?>][<?=$rIdx?>][requirement_selected_option_id]' value='<?=$o['idx']?>'
                            <?php 
                              echo $checked;
                              echo $disabled;
                            ?>>
                          <div class='ps-1'><?=$o['option_name_en']?></div>
                        </label>
                        <?php endforeach; ?>
                      </div>
                      <button class='btn btn-sm btn-secondary confirmbtn px-2 py-0 align-self-center'
                        <?=$disabled?>>
                        <?=lang('Lang.draft')?>
                      </button>
                    </div>
                  </form>
                </fieldset>
              <?php else: 
                if(!empty($r['requirement_reply'])) : ?>
                <fieldset class='border <?=$rIdx > 0 ? 'mt-3' : 'mt-2'?> pb-2 position-relative pt-3 px-2'>
                  <legend class='position-absolute'><?=$r['requirement_en']?></legend>
                  <div class='d-flex flex-row my-2'>
                    <label class='pe-2'>Answers to requirements :</label>
                    <span><?=$r['requirement_reply']?></span>
                  </div>
                </fieldset>
                <?php endif;  
              $rIdx++;
              endif;
            endif;
          endforeach;
        endif ;?>
        <?php else: ?>
        <div class='d-flex flex-row'>
          <label class='w-30'><?=lang('Lang.orders.detail.orderStatus')?></label>
          <span><?=lang('Lang.orders.detail.orderCanceled')?></span>
        </div>
        <?php endif;?>
        <?php if ( !empty($product['detail_desc'])) : ?>
          <div class='d-flex flex-row'>
            <label class='w-30'><?=lang('Lang.orders.detail.cancelReason')?></label>
            <div class='w-70'><?=$product['detail_desc']?></div>
          </div>
        <?php endif; ?>
      </div>
      <?php endif;?>
    </div>
    <?php endforeach ?>
  <?php else : ?>
    <div><?=lang('Lang.isEmpty')?></div>
  <?php endif ?>
</div>