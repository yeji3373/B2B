<?php if( empty($products) ) : ?>
  <div class='py-5 px-2 text-center'>
    <?=lang('Order.noResultSearch')?>
  </div>  
<?php else : ?>
  <div>
  <?php foreach($products as $product) : ?>
    <div class='d-flex flex-row border border-top-0 justify-content-between py-1 px-2 list-group-item'>
      <!-- <div class='d-flex flex-row align-items-center product-item'> -->
      <div class='d-flex flex-row align-items-start product-item mx-0 my-auto'>
        <?=img(esc($product['img_url']), false, ['class'=>'thumbnail me-2']);?>
        <div class='d-flex flex-column name-type-group'>
          <div class='name-group'>
            <span class='brand_name'><?=$product['brand_name']?></span>
            <span class='product_name'><?=$product['name_en']?></span>
            <span class=''>
              <?php
                echo trim($product['spec']);
                // ($product['container'] == 1 && $product['spec_detail']  > 0 ? "<span class='font-size-small'>(".$product['spec_detail']."&#215;".$product['spec_pcs']."pcs)</span>" : "").
                if ( $product['box'] == 1 ) {
                  if ( $product['container'] == 0 ) {
                    if ( empty($product['spec_pcs']) ) echo "/pc";
                    // else if ( !empty($product['spec_detail']))
                  }
                  echo "<span class='parenthesis font-monospace font-size-middle ms-1'>".$product['in_the_box']."pcs/Box</span>";
                }

                // echo ($product['box'] == 2 ? "<span class='parenthesis font-monospace font-size-middle ms-1'>".$product['in_the_box']."pcs</span>" : "");
                if ( $product['box'] == 2 ) {
                  if ( $product['container'] == 0 ) {
                    if ( !empty($product['spec_detail']) ) {
                      $box_component_spec = explode('/', $product['spec_detail']);
                      $box_component_pcs = explode('/', $product['spec_pcs']);
                      $box_component = explode('/', $product['contents_of_box']);
                      // echo 'aaaa '.(!empty($box_component));
                      $temp = '';

                      if ( $product['in_the_box'] == count($box_component_spec) ) {
                        echo "<span class='parenthesis font-monospace font-size-middle ms-1'>";

                        if ( $product['in_the_box'] == count($box_component_pcs) ) {
                          if ( $product['in_the_box'] == count($box_component) ) {
                            for($j = 0; $j < count($box_component); $j++ ) {
                              $temp.=$box_component[$j].' ';
                            }  
                          }
                          for($i = 0; $i < count($box_component_spec); $i++ ) {
                            if ( $i > 0 ) $temp.='/';
                            $temp.=$box_component_spec[$i].'&#215;'.$box_component_pcs[$i].($box_component_pcs[$i] > 1 ? 'pcs' : 'pc');
                          }
                        } else $temp.= $product['in_the_box']."pcs";
                        echo $temp;
                        echo "</span>";
                      } else {
                        if ( !empty($product['type_en']) ) {
                          if ( $product['in_the_box'] == count($box_component_spec) ) {
                            if ( empty($product['spec']) ) {
                              $temp.=$product['type_en'].'&#215;'.$box_component_spec[0];
                            }

                            for($i = 1; $i < count($box_component_spec); $i++) {
                              if ( empty($product['contents_of_box'])) {
                                $temp.=$product['contents_of_box'].'&#215;'.$box_component_spec[$i];
                              }
                            }
                          }
                        }
                      }
                    } else {
                      if ( $product['in_the_box'] > 0 ) {
                        echo "<span class='parenthesis font-monospace font-size-middle'>";
                        echo '&#215'.$product['in_the_box'].($product['in_the_box'] > 1 ? 'pcs' : 'pc').'</span>';
                      }
                    }
                  }
                }

                if ( $product['container'] == 1 ) {
                  if ( $product['box'] == 0 ) {
                    echo "<span class='parenthesis font-monospace font-size-middle'>";
                    echo $product['spec_detail'];
                    echo ($product['spec_pcs'] >= 1 ? '&#215;'.$product['spec_pcs'].'p' : '');
                    echo '</span>';
                  }
                }
              ?>
            </span>
            <!-- <?php
              // echo ($product['box'] == 1 ? "<span>".$product['in_the_box']."pcs/Box</span>" : "");
              // echo ($product['box'] == 2 ? "<span>".$product['in_the_box']."pcs</span>" : "");
            ?> -->
          </div>
          <!-- <div class='font-size-middle'>
            <?//=($product['barcode'] <= 0 ? '' : 'Barcode: '.$product['barcode'])?>
          </div> -->
          <div class='font-size-middle'>
            <?php
              // ($product['shipping_weight'] != "" ? "<span class='me-1'>".lang('Order.productWeight').": ".number_format($product['shipping_weight'])."g</span>" : "" ).
              if ($product['type_en'] != "" ) {
                echo "<span class='fw-bold type-en text-capitalize'>".lang('Order.productType')." : #".$product['type_en']."</span>";
              }

              if ( $product['package'] == 1 ) {
                echo "<span class='fw-bold text-capitalize'>".lang('Order.set')." : ".$product['package_detail']."</span>";
              }
            ?>            
          </div>
          <div class='tag-group d-flex flex-row flex-nowrap'>
            <?php
              if ( !empty($product['available_stock']) && $product['available_stock'] > 0 ) {
                echo "<span class='tag-item on-sale'>".lang('Order.onsale')."</span>";
              } else echo "<span class='tag-item sold-out'>".lang('Order.soldout')."</span>";
              // if ( $product['container'] == 0 && $product['spec_pcs'] > 0 ) {
              //   echo "<span class='tag-item bundle-item'>".lang('Order.bundle')."</span>";
              // }

              if ( $product['taxation'] == 1 ) {
                echo "<span class='tag-item taxation'>zero tax</span>";
              }
              // echo ($product['box'] == 1 ? "<span class='tag-item inTheBox'>".$product['in_the_box']."pcs/Box</span>" : "");
              if ($product['sample'] == 1) {
                echo "<span class='tag-item sample'>".lang('Order.sample')."</span>";
                // if ( $product['container'] == 1 ) {
                //   echo "<span class='fw-bold'>".lang('Order.container')."</span>";
                // }
              }
            ?>
          </div>
          <?php if ($product['available_stock'] <= 0 ) : ?>
          <span class='text-danger font-size-middle'><?=lang('Order.nonRefundMsg', ['target'=> '[Request an order]'])?></span>
          <?php endif; ?>
        </div>        
      </div>
      <div class='d-flex flex-column justify-content-between align-items-end'>
        <div class='font-size-large'>
          <?php 
            echo session()->currency['currencySign'];
            echo number_format($product['product_price'], session()->currency['currencyFloat']);
          ?>
        </div>
        <!-- <div>SPQ <?//=$product['spq']?></div> -->
        <div class='d-flex flex-column align-items-end'>
          <form method='post'>
            <input type='hidden' name='brand_id' value='<?=$product['brand_id']?>'>
            <input type='hidden' name='prd_id' value='<?=$product['id']?>'>
            <input type='hidden' name='prd_price' value='<?=$product['product_price']?>'>
            <input type='hidden' name='margin_section_id' value='<?=$product['margin_rate_id']?>'>
            <input type='hidden' name='margin_section' value='<?=$product['margin_level']?>'>
            <input type='hidden' name='onlyZeroTax' value='<?=$product['taxation']?>'>
            <input type='hidden' name='bskAction' value='add'>
          <?php if ( $product['available_stock'] > 0 ) : ?>
            <input type='hidden' name='order_qty' value='<?=$product['moq']?>'>
          </form>
            <?php if ($product['cart_idx'] == null ) : ?>
            <?php echo "<button class='btn btn-sm order-req' data-prd-id='".$product['id']."' data-btn='".lang('Order.unselectBtn')."'>".lang('Order.selectBtn')."</button>"; ?>
            <?php else : ?>
            <input type='hidden' class='cart_idx' value='<?=$product['cart_idx']?>'>
            <button class='btn btn-sm bsk-del-btn' data-class='order-req' data-btn='<?=lang('Order.selectBtn')?>'><?=lang('Order.unselectBtn')?></button>
            <?php endif; ?>
          <?php else : ?>
            <input type='hidden' name='order_qty' value='10'>
            <input type='hidden' name='stock_req' value='1'>
          </form>
            <?php if ( $product['cart_idx'] == null ) : ?>
            <button class='btn btn-sm stock-order-req' data-prd-id="<?=$product['id']?>" data-btn='<?=lang('Order.stockReqCancelBtn')?>'><?=lang('Order.stockRequestBtn')?></button>
            <?php else : ?>
            <input type='hidden' class='cart_idx' value='<?=$product['cart_idx']?>'>
            <button class='btn btn-sm stock-order-req bsk-del-btn' data-class='stock-order-req' data-btn='<?=lang('Order.stockRequestBtn')?>'><?=lang('Order.stockReqCancelBtn')?></button>
            <?php endif ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  </div>
  <?php echo $pager->links($pageGroup, 'pager'); ?>
<?php endif; ?>