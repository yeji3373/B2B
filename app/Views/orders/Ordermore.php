<main id='inventoryCheck-main'>
  <section>
    <form id='search-form'>
      <input type='hidden' name='brand_id'>
      <input type='hidden' name='request_unit'>
    </form>
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
  </section>
</main>