<div class='d-flex flex-column flex-wrap accordion' id='address-accordion'>
  <?php if ( isset($prevAddrList) && !empty($prevAddrList) ) : ?>
  <!-- 등록되어있는 주소 -->
  <div class='w-100 d-flex flex-column accordion-item prev-addr'>
    <div class='w-100 accordion-header' id='address-prev-head'>
      <div class='accordion-button'
          data-bs-toggle='collapse' data-bs-target='#address-prev-body'
          aria-expanded='true' aria-controls='address-prev-body'>
        <?=lang('Order.previouslyAddrss')?>
      </div>
    </div>
    <div id='address-prev-body' class='accordion-collapse collapse show' aria-labelledby='address-prev-head' data-bs-parent='#address-accordion'>
      <div class='accordion-body d-flex flex-column'>
      <?php foreach ($prevAddrList as $address) : ?>
        <!-- <?//php print_r($address) ?> -->
        <div class='border rounded py-1 px-3 bg-opacity-10 bg-secondary prev-addr-sel' data-id='<?=$address['idx']?>'>
          <div class='d-flex flex-row justify-content-between'>
            <span class='consignee'><?=$address['consignee']?></span>
            <div>
              <div class='btn btn-outline-primary btn-sm py-1 prev-addr-edit' data-id='<?=$address['idx']?>'><?=lang('Order.edit')?></div>
              <div class='btn btn-outline-primary btn-sm py-1 prev-addr-del' data-id='<?=$address['idx']?>'><?=lang('Order.del')?></div>
            </div>
          </div>
          <div>
            <span class='region' data-id='<?=$address['region_id']?>' data-ccode='<?=$address['country_code']?>'><?=$address['region']?></span> / <span class='city'><?=$address['city']?></span>
          </div>
          <div>
            <span class='streetAddr1'><?=$address['streetAddr1']?></span>
            <span class='streetAddr2'><?=$address['streetAddr2']?></span>
          </div>
          <?php if ( isset($address['zipcode']) ) : ?>
          <div class='zipcode'><?=$address['zipcode']?></div>
          <?php endif ?>
          <div class='d-flex flex-row'>
            <span class='phone_code'><?=$address['phone_code']?></span>
            <span class='phone'><?=$address['phone']?></span>
          </div>
          <!-- <div class='d-flex flex-row mt-2'>
              <div class='btn btn-outline-primary'>수정</div>
              <div class='btn btn-primary ms-3'>선택</div>
          </div> -->
        </div>
      <?php endforeach ?>
      </div>
    </div>
  </div>
  <?php endif ?>
  <div class='w-100 d-flex flex-column accordion-item new-addr'>
    <input type='hidden' name='address_id'>
    <div class='w-100 accordion-header' id='address-new-head'>
      <div class='accordion-button <?=!empty($prevAddrList) ? 'collapsed' : ''?>'
        data-bs-toggle='collapse' data-bs-target='#address-new-body'
        aria-expanded='<?=empty($prevAddrList) ? true : false?>' aria-controls='address-new-body'>
        <?=lang('Order.addNewAddress')?>
      </div>
    </div>
    <div id='address-new-body' 
        class='accordion-collapse collapse <?=empty($prevAddrList) ? 'show' : ''?>'
        aria-labelledby='address-new-head' data-bs-parent='#address-accordion'>
      <div class='accordion-body address-new-form'>
        <div>
          <label><?=lang('Order.consignee')?></label>
          <!-- <input type='text' name='consignee' value='<?//=isset($prevAddrList) ? $prevAddrList[0]['consignee'] : ''?>' required> -->
          <input type='text' name='consignee' value required>
        </div>
        <div>
          <label>Country/Region</label>
          <div class='position-relative w-50'>
            <input type='text' name='region' class='regionSelected dropdown-toggle w-100' 
                data-bs-target='.region-list' data-bs-toggle='dropdown' aria-expanded='false' required/>
            <input type='hidden' name='region_id'>
            <input type='hidden' name='country_code'>
            <ul class='region-list dropdown-menu w-100'>
            <?php if ( !empty($regions) ) : 
              foreach($regions as $region) : ?>
              <li class='dropdown-item' value='<?=$region['id']?>' data-cNo='<?=$region['country_no']?>' data-ccode='<?=$region['country_code']?>'><?=$region['name_en']?></li>
            <?php endforeach; 
            endif;?>
            </ul>
          </div>
        </div>
        <div>
          <label>Street address</label>
          <div class='w-75'>
            <input type='text' class='w-100 mb-2' name='streetAddr1' required>
            <input type='text' class='w-100' name='streetAddr2'>
          </div>
        </div>
        <div>
          <label>City</label>
          <input type='text' name='city' required>
        </div>
        <!-- <div>
          <label>State/Province/Region</label>
          <input type='text' name='' value>
        </div> -->
        <div>
          <label>Zip/Postal code</label>
          <input type='text' name='zipcode' maxlength='10'>
        </div>
        <div>
          <label>Phone number</label>
          <div class='d-flex flex-direction-row align-items-center'>
            +<select class='col-1 me-2 w-auto' name='phone_code' required>
              <option><?=lang('Order.selectBtn')?></option>
              <?php if ( !empty($itus) ) :
              foreach ($itus as $itu) : ?>
                <option value='<?=$itu['country_no']?>'>
                  <?=$itu['country_no']?>
                </option>
              <?php endforeach; 
              endif;?>
            </select>
            <input type='text' name='phone' pattern='[0-9]+' maxlength='11' required>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>