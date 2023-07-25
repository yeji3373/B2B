<main class='my-1 mx-auto w-auto products'>
  <section>
    <div class='m-auto d-flex position-relative w-90'>
      <div class='border border-dark brand-section me-2 rounded w-14'>
        <div class='p-2 mb-0'>
          <label class='mb-1'><?=lang('Order.brands')?></label><br/>
          <div class='position-relative'>
            <input type='text' 
                  class='form-control m-auto w-100 brand-keyword-search dropdown-toggle' 
                  data-bs-target='.brand-keyword-search-result' 
                  data-bs-toggle='dropdown' 
                  aria-expanded='false'
                  placeholder='<?=lang('Order.brandSearch')?>' />
            <ul class='brand-keyword-search-result dropdown-menu position-absolute start-0 w-100 mt-1 mb-0 py-1 dropdown-menu-dark'>
              <li class='dropdown-header'><?=lang('Order.brandSearch')?></li>
            </ul>
          </div>
        </div>
        <div class='w-100 h-91 overflow-auto'>
          <ul class='list-group list-group-flush border-top border-dark brand-list-group'>
            <li class='list-group-item brand-item <?=(!isset($_GET['brand_name']) || empty($_GET['brand_name'])) ? 'active' : ''?>' data-name=''><span><?=lang('Order.allBrands')?></span></li>
            <?php foreach($brands as $brand) : 
              echo "<li class='list-group-item brand-item ". (((isset($_GET['brand_name']) && !empty($_GET['brand_name'])) && $_GET['brand_name'] == $brand['brand_name']) ? 'active' : '')."' 
                        data-id='".$brand['brand_id']."' data-name='".$brand['brand_name']."'>".
                      "<div class='d-flex flex-column'>".
                        "<div>".htmlspecialchars(stripslashes($brand['brand_name']))."</div>";
                      "</div>".
                    "</li>";
              endforeach?>
          </ul>
        </div>
      </div>
      <div class='border border-dark product-section me-2 rounded w-40'>
        <div class='px-2 py-1 mb-0 h-5rm'>
          <label class='mb-1'><?=lang('Order.products')?></label>
          <div class='input-group w-100'>
            <select class='form-select w-auto flex-grow-0 productSearchOpts'>
              <option value><?=lang('Order.selectOps')?></option>
              <option value='barcode' <?=(isset($_GET['barcode']) && $_GET['barcode'] != '') ? 'selected' : ''?> ><?=lang('Order.barcode')?></option>
              <option value='name' <?=(isset($_GET['name']) && $_GET['name'] != '') ? 'selected' : ''?>><?=lang('Order.productName')?></option>
            </select>
            <input type='text' 
                  class='form-control col-7' 
                  id='productSearch' 
                  placeholder='<?=lang('Order.searchKeyword')?>' 
                  value='<?php
                          if ( isset($_GET['barcode']) && !empty($_GET['barcode'] ) ) :
                            echo $_GET['barcode'];
                          elseif ( isset($_GET['name']) && !empty($_GET['name'] ) ) : 
                            echo $_GET['name'];
                          else : 
                            echo "";
                          endif;
                        ?>'>
            <input class='btn btn-primary shadow-none search-btn' type='button' value='Search'>
          </div>
        </div>
        <div class='product-search-result border-top border-dark'>
          <?=view('layout/includes/product');?>
        </div>
      </div>
      <div class='border border-dark rounded product-invoice-section w-45'>
        <div class='p-2 border-bottom'>
          Products in cart
        </div>
        <div class='overflow-auto border-bottom border-dark product-selected'>
          <?=view('/layout/includes/Cart') ?>
        </div>
        <div class='px-3 py-2 d-flex flex-column product-total-price'>
          <?php if ( isset($cartSubTotal) ) : ?>
          <div class='w-100 d-flex flex-column product-price-detail'>
            <!-- <div class='d-flex flex-row justify-content-between align-items-baseline'>
              <label>total</label>
              <div>
                <span><?//=session()->currency['currencySign']?></span>
                <span class='total-price'><?//=$cartSubTotal['order_price_total'];?></span>
              </div>
            </div>
            <div class='d-flex flex-row justify-content-between align-items-baseline'>
              <label>discount</label>
              <div>
                <span><?//=session()->currency['currencySign']?></span>
                <span class='discount-price'><?//=$cartSubTotal['order_discount_total']?></span>
              </div>
            </div>
          </div> -->
          <div class='product-price w-100 d-flex flex-row justify-content-between align-items-baseline'>
            <label>subtotal</label>
            <div class='fw-bold font-size-large-large d-flex'>
              <span><?=session()->currency['currencySign']?></span>
              <span class='sub-total-price'>
                <?php 
                  echo $cartSubTotal['order_subTotal']
                ?>
              </span>
            </div>
          </div>
          <div class='w-100 text-end'>
            <button class='btn btn-primary inventory_check_request-btn' data-bs-target='.pre-order' aria-confirm='재고체크 확인 요청을 취소하겠습니까?'>
              <?=lang('Order.inventoryCheckRq')?>
            </button>
          </div>
          <?php endif ?>
        </div>
      </div>
    </div>
  </section>
  <div class='pre-order'></div>
  <?php if ( session()->has('error') ) : ?>
  <script>
    alert('<?=session('error')?>');
  </script>
  <?php endif ?>
  
  <!-- <div class='stock_modal'></div> -->
</main>