<main class='my-1 mx-auto w-auto products'>
  <section>
    <form id='product-form'>
      <input type='hidden' name='brand_id' data-default-value>
      <input type='hidden' name='request_unit' value='0' data-default-value='0'>
      <input type='hidden' name='offset' value='1' data-default-value='1'>
      <input type='hidden' name='totalpage'>
    </form>
    <div class='m-auto d-flex position-relative w-90'>
      <div class='border border-dark brand-section me-2 rounded w-14'>
        <div class='p-2 mb-0'>
          <label class='mb-1'><?=lang('Lang.brands')?></label><br/>
          <div class='position-relative'>
            <input type='text' 
                  class='form-control m-auto w-100 brand-keyword-search dropdown-toggle' 
                  data-bs-target='.brand-keyword-search-result' 
                  data-bs-toggle='dropdown' 
                  aria-expanded='false'
                  placeholder='<?=lang('Lang.brandSearch')?>' />
            <ul class='brand-keyword-search-result dropdown-menu position-absolute start-0 w-100 mt-1 mb-0 py-1 dropdown-menu-dark'>
              <li class='dropdown-header'><?=lang('Lang.brandSearch')?></li>
            </ul>
          </div>
        </div>
        <div class='w-100 brands-list overflow-auto'>
          <ul class='list-group list-group-flush border-top border-dark brand-list-group'>
            <li class='list-group-item brand-item active' data-name=''><span><?=lang('Lang.allBrands')?></span></li>
            <?php foreach($brands as $brand) : 
              echo "<li class='list-group-item brand-item'
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
        <div class='px-2 py-1 mb-0 h-5rem'>
          <label class='mb-1'><?=lang('Lang.products')?></label>
          <div class='input-group w-100'>
            <select class='form-select w-auto flex-grow-0 productSearchOpts'>
              <option value><?=lang('Lang.selectOps')?></option>
              <option value='barcode' <?=(isset($_GET['barcode']) && $_GET['barcode'] != '') ? 'selected' : ''?> ><?=lang('Lang.barcode')?></option>
              <option value='name' <?=(isset($_GET['name']) && $_GET['name'] != '') ? 'selected' : ''?>><?=lang('Lang.productName')?></option>
            </select>
            <input type='text' 
                  class='form-control col-7' 
                  id='productSearch' 
                  placeholder='<?=lang('Lang.searchKeyword')?>' 
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
        <div class='product-search-result border-top border-dark overflow-auto'>
          <div class='product-list'>
            <?=view('layout/includes/product');?>
          </div>
        </div>
      </div>
      <div class='border border-dark rounded product-invoice-section w-45'>
        <div class='p-2 border-bottom'>
          Products in cart
        </div>
        <div class='overflow-auto border-bottom border-dark product-selected cart-in-product'>
          <?=view('layout/includes/Cart') ?>
        </div>
        <div class='px-3 py-2 d-flex flex-column product-total-price'>
          <!-- <div class='w-100 d-flex flex-column product-price-detail'>
            <div class='d-flex flex-row justify-content-between align-items-baseline'>
              <label>total</label>
              <div>
                <span><?//=session()->currency['currencySign']?></span>
                <span class='total-price'>0</span>
              </div>
            </div>
            <div class='d-flex flex-row justify-content-between align-items-baseline'>
              <label>discount</label>
              <div>
                <span><?//=session()->currency['currencySign']?></span>
                <span class='discount-price'>0</span>
              </div>
            </div>
          </div> -->
          <div class='product-price w-100 d-flex flex-row justify-content-between align-items-baseline'>
            <label>subtotal</label>
            <div class='fw-bold font-size-large-large d-flex'>
              <span><?=session()->currency['currencySign']?></span>
              <span class='sub-total-price'>0</span>
            </div>
          </div>
          <div class='w-100 text-end'>
            <button class='btn btn-primary inventory_check_request-btn' 
                  data-bs-target='.pre-order' 
                  aria-confirm='<?=lang('Lang.inventoryRequest.checkAlert')?>'>
              <?=lang('Lang.inventoryRequest.inventoryCheckRq')?>
            </button>
          </div>
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