<main id='stock_request'>
<?php if ( !empty($product) ) : 
  // print_r($product);
  // echo '<Br/>';
  // print_r(session()->userData);
  // echo '<br/>';
  // print_r(session()->currency) 
  ?>
<form>
  <input type='hidden' name='prd_id' value='<?=$product['id']?>'>
  <div class='d-flex flex-column'>
    <?php if ($product['taxation'] == 1) : ?>
    <div class='d-flex flex-row'>
      <label>영세만 가능</label>
    </div>
    <?php endif ?>
    <div class='d-flex flex-row'>
      <label>lead time</label>
      <?=$product['lead_time_min']?> ~ <?=$product['lead_time_max']?>
    </div>
    <div class='d-flex flex-row'>
      <label>재고요청 수량</label>
      <div>
        <input type='text' name='moq' value='<?=$product['moq']?>'>
      </div>
    </div>
    <div class='d-flex flex-row'>
      <label>remark</label>
      <textarea class='w-80' name='stock-req-remark' rows='5'></textarea>
    </div>
  </div>
</form>
<?php endif ?>
</main>