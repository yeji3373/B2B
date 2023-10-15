<div class='d-flex flex-column list-group'>
  <?php if ( isset($orderDetails) && !empty($orderDetails) ) : ?>
    <?php 
    foreach($orderDetails AS $i => $product) : 
    ?>
    <div class='d-flex flex-column border border-secondary <?=($i > 0 ? 'border-top-0': '')?> rounded-0 justify-content-between p-0 list-group-item'>
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
      <?php if ( !empty($nowPackStatus) && !empty($nowPackStatus['requirement_option_check']) ) : 
        $orderPrice = 0;
        $prd_price = $product['prd_price'];
        $prd_qty = $product['prd_order_qty'];
        $canceled = FALSE;
    
        if ( $nowPackStatus['pay_step'] > 1 ) {
          if ( !empty($product['prd_change_price']) ) $prd_price = $product['prd_change_price'];
        }
        
        if ( empty($product['order_excepted']) ) {
          if ( $nowPackStatus['pay_step'] == 1 ) {
            if ( !empty($product['prd_change_qty']) ) $prd_qty = $product['prd_change_qty'];
          } else if ( $nowPackStatus['pay_step'] == 2 ) {
            if ( !empty($product['prd_fixed_qty']) ) $prd_qty = $product['prd_fixed_qty'];
          } else if ( $nowPackStatus['pay_step'] == 3 ) {
            if ( !empty($product['prd_final_qty']) ) $prd_qty = $product['prd_final_qty'];
          }
        } else {
          $canceled = TRUE;
          $prd_qty = 0;
        }

        $orderPrice = ($prd_price * $prd_qty);
      ?>
      <div class='d-flex flex-column pt-1 pb-2 px-2'>
        <table class='border border-secondary border-opacity-50'>
          <thead>
            <tr>
              <td class='px-1 border-secondary border-opacity-50 border-end border-bottom fw-bold bg-opacity-50 text-bg-secondary text-center'>
                <?=lang('Lang.orders.detail.fixedProductPrice')?>
              </td>
              <td class='px-1 border-secondary border-opacity-50 border-end border-bottom fw-bold bg-opacity-50 text-bg-secondary text-center'>
                <?php
                  if ( $nowPackStatus['pay_step'] == 2 ) echo lang('Lang.orders.detail.fixedQty');
                  else if ( $nowPackStatus['pay_step'] >= 3 ) echo lang('Lang.orders.detail.finalQty');
                  else echo lang("Lang.orders.detail.securedQty");
                ?>
              </td>
              <td class='px-1 border-secondary border-opacity-50 <?=$canceled ? 'border-end' : ''?> border-bottom fw-bold bg-opacity-50 text-bg-secondary text-center'>
                <?php if ( $nowPackStatus['pay_step'] >= 3 ) { 
                    echo lang('Lang.orders.detail.finalAmount');
                  } else {
                    echo lang('Lang.orders.detail.fixedAmount');
                  }
                ?>
              </td>
              <?php if ( $canceled ) : ?>
                <td class='px-1 border-secondary border-opacity-50 border-bottom fw-bold bg-opacity-50 text-bg-secondary text-center'>
                <?=lang('Lang.orders.detail.orderStatus')?>
              </td>
              <?php endif; ?>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class='px-1 border-secondary border-opacity-50 border-end text-end'>
                <?=session()->currency['currencySign'].number_format($prd_price, session()->currency['currencyFloat']);?>
              </td>
              <td class='px-1 border-secondary border-end text-end'>
                <?=$prd_qty."ea";?>
              </td>
              <td class='px-1 border-secondary border-opacity-50 <?=$canceled ? 'border-end' : ''?> text-end'>
                <?=session()->currency['currencySign'].number_format($orderPrice, session()->currency['currencyFloat'])?>
              </td>
              <?php if ( $canceled ) : ?>
                <td class='px-1 border-secondary border-opacity-50 text-end'>
                <?=lang('Lang.orders.detail.orderCanceled')?>
              </td>
              <?php endif; ?>
            </tr>
          </tbody>
        </table>
        <?php if ( empty($product['order_excepted']) ) : ?>
          <?php if ( !empty($orderRequirement)) :
            $rIdx = 0;
            foreach($orderRequirement AS $r) :
              if($product['detail_id'] == $r['order_detail_id']) :
                if(isset($r['options'])) : ?>
                  <fieldset class='border border-secondary border-opacity-50 mt-4 pb-2 position-relative pt-3 px-2'>
                  <legend class='position-absolute'><?=$r['requirement_en']?></legend>
                    <form class='requirmentOptForm'>
                      <input type='hidden' name='requirement[<?=$i?>][<?=$rIdx?>][idx]' data-name='idx' data-type='1' value="<?=$r['idx']?>">
                      <input type='hidden' name='requirement[<?=$i?>][<?=$rIdx?>][order_detail_id]' data-name='order_detail_id' data-type='1' value="<?=$r['order_detail_id']?>">
                      <input type='hidden' name='requirement[<?=$i?>][<?=$rIdx?>][requirement_id]' data-name='requirement_id' data-type='1' value="<?=$r['requirement_id']?>">
                      
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
                            <input type='radio' name='requirement[<?=$i?>][<?=$rIdx?>][requirement_selected_option_id]' data-name='requirement_selected_option_id' data-type='1' value='<?=$o['idx']?>'
                              <?php 
                                echo $checked;
                                echo $disabled;
                              ?>>
                            <div class='ps-1'><?=$o['option_name_en']?></div>
                          </label>
                          <?php endforeach; ?>
                        </div>
                        <button class='btn btn-sm btn-secondary confirmbtn px-2 py-0 align-self-center shadow-none'
                          <?=$disabled?>>
                          <?=lang('Lang.draft')?>
                        </button>
                      </div>
                    </form>
                  </fieldset>
                <?php $rIdx++;
                else: 
                  if(!empty($r['requirement_reply'])) : ?>
                  <fieldset class='border mt-4 pb-2 position-relative pt-3 px-2'>
                    <legend class='position-absolute'><?=$r['requirement_en']?></legend>
                    <div class='d-flex flex-row my-2'>
                      <label class='pe-2'>Answers to requirements :</label>
                      <span><?=$r['requirement_reply']?></span>
                    </div>
                  </fieldset>
                  <?php endif;  
                // $rIdx++;
                endif;
              endif;
            endforeach;
          endif ;?>
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