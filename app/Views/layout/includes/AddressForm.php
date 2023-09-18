<div class='d-flex flex-column flex-wrap accordion' id='address-accordion'>
  <!-- 등록되어있는 주소 -->
  <?php if ( isset($prevAddrList) && !empty($prevAddrList) ) : ?>
  <div class='w-100 d-flex flex-column accordion-item prev-addr overflow-auto'>
    <div class='w-100 accordion-header' id='address-prev-head'>
      <div class='accordion-button'
          data-bs-toggle='collapse' data-bs-target='#address-prev-body'
          aria-expanded='true' aria-controls='address-prev-body'>
        <?=lang('Lang.previouslyAddrss')?>
      </div>
    </div>
    <div id='address-prev-body' class='accordion-collapse collapse show' aria-labelledby='address-prev-head' data-bs-parent='#address-accordion'>
      <div class='accordion-body d-flex flex-column'>
      <?php foreach ($prevAddrList as $address) : ?>
        <div class='w-100 registed-address'>
          <div class='d-flex flex-row justify-content-between align-items-center mb-1'>
            <span><?=$address['consignee']?></span>
            <div>
              <div class='btn btn-outline-primary btn-sm py-1 prev-addr-edit' data-id='<?=$address['idx']?>'><?=lang('Lang.edit')?></div>
              <div class='btn btn-outline-primary btn-sm py-1 prev-addr-del' data-id='<?=$address['idx']?>'><?=lang('Lang.del')?></div>
            </div>
          </div>
          <div class='d-flex flex-column border rounded py-1 px-3 bg-opacity-10 bg-secondary prev-addr-sel' data-id='<?=$address['idx']?>'>
            <span class='consignee d-none'><?=$address['consignee']?></span>
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
          </div>
        </div>
      <?php endforeach ?>
      </div>
    </div>
  </div>
  <?php endif ?>
  <div class='w-100 d-flex flex-column accordion-item new-addr'>
    <input type='hidden' name='address[idx]'>
    <input type='hidden' name='address[address_operate]' value='0' >    
    <div class='w-100 accordion-header' id='address-new-head'>
      <div class='accordion-button <?=!empty($prevAddrList) ? 'collapsed' : ''?>'
        data-bs-toggle='collapse' data-bs-target='#address-new-body'
        aria-expanded='<?=empty($prevAddrList) ? true : false?>' aria-controls='address-new-body'>
        <?=lang('Lang.addNewAddress')?>
      </div>
    </div>
    <div id='address-new-body' 
        class='accordion-collapse collapse <?=empty($prevAddrList) ? 'show' : ''?>'
        aria-labelledby='address-new-head' data-bs-parent='#address-accordion'>
      <div class='accordion-body address-new-form'>
        <div class='body-item'>
          <label><?=lang('Lang.consignee')?></label>
          <!-- <input type='text' name='address[consignee' value='<?//=isset($prevAddrList) ? $prevAddrList[0]['consignee'] : ''?>' required> -->
          <input type='text' name='address[consignee]' value aria-required='true' <?=empty($prevAddrList) ? 'required' : ''?>>
        </div>
        <div class='body-item'>
          <label>Country/Region</label>
          <div class='position-relative w-50'>
            <input type='text' name='address[region]' class='regionSelected dropdown-toggle w-100' 
                data-bs-target='.region-list' data-bs-toggle='dropdown' aria-expanded='false' aria-required='true' <?=empty($prevAddrList) ? 'required' : ''?>/>
            <input type='hidden' name='address[region_id]'>
            <input type='hidden' name='address[country_code]'>
            <ul class='region-list dropdown-menu w-100'>
            <?php if ( !empty($regions) ) : 
              foreach($regions as $region) : ?>
              <li class='dropdown-item' value='<?=$region['id']?>' data-cNo='<?=$region['country_no']?>' data-ccode='<?=$region['country_code']?>'><?=$region['name_en']?></li>
            <?php endforeach; 
            endif;?>
            </ul>
          </div>
        </div>
        <div class='body-item'>
          <label>Street address</label>
          <div class='w-75'>
            <input type='text' class='w-100 mb-2' name='address[streetAddr1]' aria-required='true' <?=empty($prevAddrList) ? 'required' : ''?>>
            <input type='text' class='w-100' name='address[streetAddr2]'>
          </div>
        </div>
        <div class='body-item'>
          <label>City</label>
          <input type='text' name='address[city]' aria-required='true' <?=empty($prevAddrList) ? 'required' : ''?>>
        </div>
        <!-- <div class='body-item'>
          <label>State/Province/Region</label>
          <input type='text' name='address[' value>
        </div> -->
        <div class='body-item'>
          <label>Zip/Postal code</label>
          <input type='text' name='address[zipcode]' maxlength='10'>
        </div>
        <div class='body-item border-0'>
          <label>Phone number</label>
          <div class='d-flex flex-direction-row align-items-center'>
            +<select class='col-1 me-2 w-auto' name='address[phone_code]' <?=empty($prevAddrList) ? 'required' : ''?>>
              <option><?=lang('Lang.selectBtn')?></option>
              <?php if ( !empty($itus) ) :
              foreach ($itus as $itu) : ?>
                <option value='<?=$itu['country_no']?>'>
                  <?=$itu['country_no']?>
                </option>
              <?php endforeach; 
              endif;?>
            </select>
            <input type='text' name='address[phone]' pattern='[0-9]+' maxlength='11' aria-required='true' <?=empty($prevAddrList) ? 'required' : ''?>>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>