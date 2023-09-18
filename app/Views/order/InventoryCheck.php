<main id='inventoryCheck-main'>
  <form method='POST' action='<?=site_url('/inventory/request')?>' accept-charset='UTF-8'>
  <?=csrf_field() ?>
  <input type='hidden' name='request-total-price'>
    <!-- <div class='inventory-header'>
      <div class='inventory-title'>재고요청 확인</div>
      <button class='btn-close'></button>
    </div> -->
    <div class='inventory-content'>
      <div class='d-flex flex-column w-100'>
        <?=view('/layout/includes/AddressForm')?>
      </div>
      <div class='mt-3 w-100'>
        <h7><?=lang('Lang.inventoryRequest.requestCheck')?></h7>
        <div class='requirements accordion border border-1 rounded-1'>
          <div class='accordion-body d-flex flex-column py-2'>
            <div class='w-100 d-flex'>
              <!-- <label class='w-10 pe-1'>선택</label> -->
              <div class='w-100 d-flex flex-column ps-1'>
                <select class='mb-4 w-100' aria-target='.requirements-group'>
                  <option aria-appended='true'><?=lang('Lang.inventoryRequest.requestCheckSelect')?></option>
                  <?php if (!empty($requirements)) :
                    foreach ($requirements AS $requirement): ?>
                    <option value='<?=$requirement['idx']?>' data-placeholder='<?=$requirement['placeholder']?>'><?=$requirement['requirement_en']?></option>
                  <?php endforeach;
                    endif; ?>
                </select>
                <div class='w-100 requirements-group d-flex flex-wrap'></div>
              </div>
            </div>
          </div>
      </div>
    </div>
    <div class='inventory-footer mt-3 d-flex justify-content-end'>
      <input type='submit' class='btn btn-primary' value='Submit'>
    </div>
  </form>
</main>