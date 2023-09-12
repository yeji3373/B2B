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
          <label class='w-20'><?=lang('Order.productWeight')?></label>
          <span><?=empty($product['shipping_weight']) ? '-' : number_format($product['shipping_weight']).'g';?></span>
        </div>
        <div class='d-flex flex-row'>
          <label class='w-20'>Unit price</label>
          <div class='w-80 d-flex flex-row align-items-lg-end'>
            <?php
            $priceClass = '';
            $changedClass = '';
            if ( !empty($product['prd_price_changed']) ) :
              $priceClass = 'text-decoration-line-through pe-2';
              $changedClass = 'fw-bold font-size-9';
            endif;
            if ( empty($product['prd_price_changed']) ) {
              echo "<span class='".$priceClass."'>";
              echo session()->currency['currencySign'].number_format($product['prd_price'], session()->currency['currencyFloat']);
              echo "</span>";
            } else {
              echo "<span class='".$changedClass."'>";
              echo session()->currency['currencySign'].number_format($product['prd_change_price'], session()->currency['currencyFloat']);
              echo "</span>";
            }
            ?>
          </div>
        </div>
        <div class='d-flex flex-row'>
          <label class='w-20'>Request Qty</label>
          <div class='w-80 d-flex flex-row align-items-lg-end'>
          <?php
            $seletedOptions = [];
            if ( !empty($product['prd_order_qty']) ) {
              echo "<span>";
              echo number_format($product['prd_order_qty'])."EA &nbsp;";
              echo "</span>";
            }
            if ( !empty($product['prd_change_qty']) ) {
              echo "<span> &nbsp:&nbsp:&nbsp ";
              echo number_format($product['prd_change_qty'])."EA &nbsp;";
              echo "</span>";
            }
            if(!empty($product['prd_fixed_qty'])) {
              echo "<span> &nbsp:&nbsp:&nbsp ";
              echo number_format($product['prd_fixed_qty'])."EA &nbsp;";
              echo "</span>";
            }
          ?>
          </div>
            
        </div>
        <div class='d-flex flex-row'>
          <?php
          $orderPrice = 0;
          if ( !empty($product['prd_qty_changed']) ) :
            if ( !empty($product['prd_price_changed'])) :
              $orderPrice = ($product['prd_change_qty'] * $product['prd_change_price']);
            else:
              $orderPrice = ($product['prd_change_qty'] * $product['prd_price']);
            endif;
          else :
            if ( !empty($product['prd_price_changed']) ) :
              $orderPrice = ($product['prd_order_qty'] * $product['prd_change_price']);
            else :
              $orderPrice = ($product['prd_order_qty'] * $product['prd_price']);
            endif;
          endif;
          ?>
          <label class='w-20'>Order Price</label>
          <div class='w-80 d-flex flex-row align-items-lg-end'>
            <?php 
            if ( ($product['prd_order_qty'] * $product['prd_price']) != $orderPrice ) : 
              echo "<span>";
              echo  session()->currency['currencySign'].number_format(($product['prd_order_qty'] * $product['prd_price']), session()->currency['currencyFloat'])."&nbsp;";
              echo "</span>";
              if (!empty($product['prd_change_qty'])) :
                echo "<span>";
                echo  " &nbsp:&nbsp:&nbsp ".session()->currency['currencySign'].number_format($orderPrice, session()->currency['currencyFloat'])."&nbsp;";
                echo "</span>";
              endif;
              if(!empty($product['prd_fixed_qty'])) :
                echo "<span>";
                echo " &nbsp:&nbsp:&nbsp ".session()->currency['currencySign'].number_format(($product['prd_fixed_qty'] * $product['prd_price']), session()->currency['currencyFloat']);
                echo "</span>";
              endif;
            else : 
              echo "<span>";
              echo  session()->currency['currencySign'].number_format(($product['prd_order_qty'] * $product['prd_price']), session()->currency['currencyFloat']);
              echo "</span>";
            endif;            
            ?>
          </div>
        </div>
        <?php 
          if ( !empty($nowPackStatus) && $nowPackStatus['requirement_option_check'] == 1 ) : 
          foreach($orderRequirement AS $i => $r) :
            if($product['detail_id'] == $r['order_detail_id']) :
              if(isset($r['options'])) :
                  echo "<fieldset class='border ".($i > 0 ? 'mt-3' : 'mt-2')." pb-2 position-relative pt-3 px-2'>
                        <legend class='position-absolute'>".$r['requirement_en']."</legend>
                        <form id='requirmentOptForm'>
                          <input type='hidden' name='idx' value=".$r['idx'].">
                          <input type='hidden' name='detail_id' value=".$r['order_detail_id'].">
                          <input type='hidden' name='requirement_id' value=".$r['requirement_id'].">";
                  if(!empty($r['requirement_reply'])) :
                    echo "<div class='d-flex flex-row mb-2'>
                          <label class='pe-2'>Answers to requirements :</label>
                          <span>".$r['requirement_reply']."</span>
                        </div>";
                  endif;
                  echo "<div class='d-flex flex-column'>";
                  echo "<div class='d-flex flex-column'>";
                  $optionName = $r['requirement_id'] == 1 ? "expirationOption" : "leadtimeOption";
                  foreach($r['options'] AS $o) :
                    echo "<label class='margin-right-1 pb-1 d-flex flex-row align-items-center'>";
                    echo "<input type='radio' name='".$optionName."' value='{$o['idx']}'";
                    if($o['idx'] == $r['requirement_selected_option_id']){
                      echo " checked";
                    }
                    if($nowPackStatus['requirement_option_disabled'] == 1){
                      echo " disabled";
                    }
                    echo ">";
                    echo "<div class='ps-1'>{$o['option_name_en']}</div>";
                    echo "</label>";
                  endforeach;                  
                  echo "</div>";
                  echo "<button class='btn btn-outline-primary btn-sm confirmbtn w-10 p-1 align-self-center'";
                  if($nowPackStatus['requirement_option_disabled'] == 1){
                    echo " disabled";
                  }
                  echo ">confirm</button>
                    </div>
                  </form>
                  </fieldset>";
              endif;
            endif;
          endforeach;
          endif;?>
      <?php else: ?>
        <div class='d-flex flex-row'>
          <label class='w-20'><?=lang('Order.orders.detail.orderStatus')?></label>
          <span><?=lang('Order.orders.detail.orderCanceled')?></span>
        </div>
        <?php if ( !empty($product['detail_desc'])) : ?>
        <div class='d-flex flex-row'>
          <label class='w-20'><?=lang('Order.orders.detail.cancelReason')?></label>
          <div class='w-80'><?=$product['detail_desc']?></div>
        </div>
        <?php endif;?>
      <?php endif;?>
      </div>
    </div>
    <?php endforeach ?>
  <?php else : ?>
    <div><?=lang('Order.isEmpty')?></div>
  <?php endif ?>
</div>