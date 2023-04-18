<div class='pi-form-container bg-body'>
  <div class='pi-company-name'>
    <div>
      <div class='fw-bold'>
        BEAUTY<span style='color: #f1196c;'>NET</span>KOREA
      </div>
      <div class='position-absolute top-0 end-0 me-4'>
        <?=form_open('orders/htmlToPDF')?>
        <input type='hidden' name='order_number' value='<?=$order['order_number']?>'>
        <input type='hidden' name='receipt_type' value='<?=$receipt['receipt_type']?>'>
        <button class='btn btn-secondary'>Download PI</button>
        <button class='btn btn-secondary btnClose'>Close</button>
        </form>
      </div>
    </div>
  </div>
  <div>
    <div class='text-end pi-date'><?=$order['orderDate']?></div>
    <div class='pi-seller-buyer'>
      <div>
        <p class='fw-bold fst-italic text-decoration-underline mb-1'>Seller: </p>
        <div>
          <p class='fw-bold fs-6'>BEAUTYNETKOREA CO.,LTD.</p>
          <p>21, Janggogae-ro 231beonan-gil, Seo-gu,</p>
          <p>Incheon, Republic of Korea (22827)</p>
          <p>
            <span>Tel:82-32-229-6868</span>
            <span>Fax.82-70-4325-4806</span>
          </p>
          <p>
            <label>Attn.</label>
            <span><?=!empty($buyer['manager_name']) ? $buyer['manager_name'] : ''?></span>
          </p>
        </div>
      </div>
      <div>
        <p class='fw-bold fst-italic text-decoration-underline mb-1'>Buyer: </p>
        <div>
          <?php if ( !empty($buyer) ) : ?>
          <p class='fw-bold fs-6'><?=$buyer['name']?></p>
          <p><?=$buyer['address']?></p>
          <p>
            <span>Tel: <?=empty($buyer['phone']) ? '-' : $buyer['phone']?></span>
            <!-- <span>Fax.82-70-4325-4806</span> -->
          </p>
          <?php endif; ?>
        </div>
      </div>
  </div>
  <div class='d-flex flex-row justify-content-end mb-2 pi-order-no'>
    <div class='w-25 border-bottom border-2 border-dark'>
      <span class='fw-bold'>Order No.</span>
      <span><?=$buyer['name']."_".date('Ymd', strtotime($order['orderDate']))?></span>
    </div>
  </div>
  <div class='mb-1'>
    Dear Sirs,<br/>
    We are pleased to offer the under-mentioned article(s) as per conditions and details desceibed as follows
  </div>
  <div class='w-100 text-end mb-2'>
    EXWORKS
  </div>
  <table class='border border-dark w-100'>
    <thead class='border-bottom border-dark pb-1' style='background-color: #d9e1f2;'>
      <tr>
        <th>No.</th>
        <th>Brand</th>
        <th>Description</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Amount</th>
        <th>Remark</th>
      </tr>
    </thead>
    <tbody class='w-100'>
      <?php if (!empty($orderDetails)) : ?>
      <?php foreach($orderDetails AS $i => $detail) : ?>
      <tr>
        <td><?=$i + 1?></td>
        <td class='w-15 text-uppercase'><?=$detail['brand_name']?></td>
        <td class='text-start ps-2'>
          <?=$detail['name_en'].' '.$detail['spec']?>
          <?=!empty($detail['type_en']) ? " #".$detail['type_en'] : '' ?>
        </td>
        <td class='text-end pe-2'><?=number_format($detail['prd_order_qty'])?></td>
        <td class='text-end pe-2'><?=$detail['currency_sign'] . ' ' . number_format($detail['prd_price'], $detail['currency_float'])?></td>
        <td class='text-end pe-2'><?=$detail['currency_sign'] . ' ' . number_format(($detail['prd_order_qty'] * $detail['prd_price']), $detail['currency_float'])?></td>
        <td>
          <?php if ( $detail['order_excepted'] == 1 ) : 
            echo "주문 제외";
          endif;?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </tboy>
    <tfoot>
    <?php if (!empty($receipt) && !empty($order)) : 
      // print_r($order);
      // print_r($receipts);?>

<tr class='fw-bold'>
        <td class='text-center py-2' colspan='5'>Product total</td>
        <td class='text-end py-2 px-1 color-red' colspan='2'>
          <?=$order['currency_sign'].' '.number_format($order['order_amount'], $order['currency_float']) ?>
        </td>
      </tr>
      <tr class='fw-bold'>
        <td class='text-center py-2' colspan='5'>Shipping cost</td>
        <td class='text-end py-2 px-1 color-red' colspan='2'>
          <?php if ( !empty($receipt['delivery_price']) ) {
            echo $order['currency_sign'].' '.number_format($receipt['delivery_price'], $order['currency_float']);
          } else echo "-";        
          ?>
        </td>
      </tr>      
      <tr class='fw-bold'>
        <td class='text-center py-2' colspan='5'>Deposit</td>
        <td class='text-end py-2 px-1 color-red' colspan='2'>
          <?=$order['currency_sign'].' '.number_format($receipt['rq_amount'], $order['currency_float'])?>
        </td>
      </tr>
      <tr class='fw-bold'>
        <td class='text-center py-2' colspan='5'>Balance</td>
        <td class='text-end py-2 px-1 color-red' colspan='2'>
          <?=$order['currency_sign'].' '.number_format($receipt['due_amount'], $order['currency_float'])?>
        </td>
      </tr>
      <tr class='fw-bold'>
        <td class='text-center py-2' colspan='5'>Grand total</td>
        <td class='text-end py-2 px-1 color-red' colspan='2'>
          <?=$order['currency_sign'].' '.number_format(($order['order_amount'] + $receipt['delivery_price']), $order['currency_float'])?>
        </td>
      </tr>
      <?php endif; ?>
    </tfoot>
  </table>
  <div class='w-90 m-auto mt-3 fw-bold invoice-notice'>
    <table class='w-100'>
      <tr>
        <td>Due date</td>
        <td class='color-red'>Within 7 days</td>
      </tr>
      <tr>
        <td>Port of loading</td>
        <td>Incheon, Korea</td>
      </tr>
      <tr>
        <td>Packing</td>
        <td>Export standard</td>
      </tr>
      <?php if ( !empty($order) ) : ?>
      <tr>
        <td>Payment</td>
        <?php if( strtolower($order['payment']) == 'paypal') : ?>
        <td><?=$order['payment']?></td>
        <?php else : ?>
        <td>100 % in advance(p/o)  by T/T</td>
        <?php endif ?>
      </tr>
      <?php if ( $order['payment'] != 'paypal') : ?>
      <?php if ( !empty($order['bank_name']) ) : ?>
      <tr>
        <td>Payment Bank</td>
        <td><?=$order['bank_name']?></td>
      </tr>
      <?php endif ?>
      <?php if ( !empty($order['account_no']) ) : ?>
      <tr>
        <td>Account No</td>
        <td><?=$order['account_no']?></td>
      </tr>
      <?php endif ?>
      <?php if ( !empty($order['swift_code']) ) : ?>
      <tr>
        <td>Swift Code</td>
        <td><?=$order['swift_code']?></td>
      </tr>
      <?php endif ?>
      <?php endif ?>
      <?php endif ?>
      <tr>
        <td>Beneficial Name</td>
        <td>Beautynetkorea</td>
      </tr>
      <tr>
        <td>Remark</td>
        <td class='color-red'>
          If you requested EYENLIP/FABYOU/SUMHAIR's certificates such as MSDS, FDA, CGMP, CPNP, COA ect. <br/>
          We can provide it to our customer. <br/>
          However we do not provide any certificate for the other brands' , we only provide MSDS documents.</br>
          Except AHC, INNISFREE, LANEIGE, SULWHASOO, WHOO's MSDS.(These brands' MSDS we cannot give you.<br/>
          Therefore if you need any certificate, please ask your sales manager before you place an order.<br/>
          Additionally, if you have any custom issues regarding the certificates of the other brands, <br/>
          Beautynetkorea do not take any responsibility.</br>
          And it causes the return to us, the importer should pay all the expenses.
        </td>
      </tr>
    </table>
  </div>
  <div class='mt-3 w-100 fw-bold'>
    <div class='sign'>
      <div class='mb-2'>Yours Faithfully</div>
      <div>
        <div class='border-bottom border-dark sign-div text-end'>
          <img src='<?=empty($sign) ? '/img/jmh_sign.png' : $sign ?>' width='70%'>
        </div>
        <p>Signature</p>
      </div>
    </div>
  </div>
</div>