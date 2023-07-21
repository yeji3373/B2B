<main id='inventoryCheck-main'>
  <form method='post' action='<?=site_url('/address')?>' accept-charset='UTF-8'>
    <!-- <div class='inventory-header'>
      <div class='inventory-title'>재고요청 확인</div>
      <button class='btn-close'></button>
    </div> -->
    <div class='inventory-content'>
      <div class='d-flex flex-column w-100'>
        <?=view('/layout/includes/AddressForm')?>
      </div>
      <div class='mt-3 w-100'>
        <h7>요청사항</h7>
        <div class='accordion border border-1 rounded-1 p-3'>
          <div class='d-flex flex-column mb-3'>
            <div class='w-100 d-flex'>
              <label class='w-10 pe-1'>선택</label>
              <div class='w-90 d-flex flex-column ps-1'>
                <select class='mb-1 w-100'>
                  <option>요청사항 선택</option>
                </select>
                <div class='w-100'>
                선택한 값 append
                </div>
              </div>
            </div>
          </div>
          <div class='d-flex'>
            <label class='w-10 pe-1'>기타</label>
            <div class='w-90'>
              <textarea class='request-textarea'></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class='inventory-footer mt-3'>
      <input type='submit' class='btn btn-primary' value='Submit'>
    </div>
  </form>
</main>