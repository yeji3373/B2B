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
        <div class='w-100 d-flex flex-row justify-content-between align-baseline'>
        <h7><?=lang('Lang.inventoryRequest.requestCheck')?></h7>
        <span class='btn btn-sm btn-light btn-outline-secondary shadow-none requirment-additional'><?=lang('Lang.inventoryRequest.additional')?></span>
        </div>
        <div class='requirements accordion border border-1 rounded-1'>
          <div class='accordion-body requirements-group d-flex flex-column py-2'>
          <?php 
          if (!empty($requirements)) :
            foreach ($requirements AS $i => $requirement) :
              if ( !empty($requirement['default']) ) :
          ?>
            <div class='w-100 d-flex flex-column requirement-item'>
              <label class='w-100 mb-1'><?=$requirement['requirement_en']?></label>
              <input type='hidden' name='requirement[<?=$i?>][requirement_id]' value='<?=$requirement['idx']?>'/>
              <textarea name='requirement[<?=$i?>][requirement_detail]' placeholder='<?=$requirement['placeholder']?>'></textarea>
            </div>
          <?php else : ?>
            <div class='w-100 d-flex flex-column requirement-item d-none'>
              <label class='w-100 mb-1'><?=$requirement['requirement_en']?></label>
              <input type='hidden' name='requirement[<?=$i?>][requirement_id]' value='<?=$requirement['idx']?>' disabled/>
              <textarea name='requirement[<?=$i?>][requirement_detail]' placeholder='<?=$requirement['placeholder']?>' disabled></textarea>
            </div>
          <?php
              endif;
            endforeach;
          endif; 
          ?>
          </div>
      </div>
    </div>
    <div class='inventory-footer mt-3 d-flex justify-content-end'>
      <input type='submit' class='btn btn-primary shadow-none' value='Submit'>
    </div>
  </form>
</main>