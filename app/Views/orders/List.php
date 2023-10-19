<?php if (session()->has('error')) : ?>
  <div class="mx-3 notification error">
    <?= session('error') ?>
  </div>
<?php endif ?>
<main class='m-3 w-auto orders-main'>
  <div class='w-90 m-auto d-grid orders-container'>
    <section class='orders-list-container border border-secondary'>
      <div class='w-100 border-bottom'>
        <input type='text' class='w-100 border-0 p-2' name='order_number' placeholder='Order Number'>
      </div>
      <div class='d-flex flex-column flex-wrap list-group rounded-0 orders-list'>
        <?php if (isset($orders) && !empty($orders)) : ?>
        <div>
          <?php foreach ( $orders as $o ) : ?>
          <a class='list-group-item order-item fw-bold <?=(isset($_GET['order_number']) && $o['order_number'] == $_GET['order_number']) ? 'active' : ''?>'
              href='<?=site_url('orders').'?order_number='.$o['order_number'] . (!empty($_GET['page']) ? '&page='.$_GET['page'] : '')?>'>
              <span class='order-number'>
                <?=$o['order_number']?>
              </span>
              <span class='parenthesis small'><?=date('Y-m-d', strtotime($o['created_at']))?></span>
          </a>
          <?php endforeach ?>
        </div>
        <?=$ordersPager->links('default', 'pager_simple');?>
        <?php else : ?>
          <div class='w-100 order-item'><?=lang('Lang.isEmpty')?></div>
        <?php endif ?>
      </div>
    </section>
    <?php if ( !empty($_GET['order_number']) && !empty($order) ) : ?>
    <section class='order-info-container py-2 px-4 border border-secondary'>
      <?=view('orders/OrderInfo') ?>
      <div class='pi-viewer'>
        <div class='w-60 mx-auto bg-body position-relative'></div>
      </div>
    </section>
    <section class='order-detail-container'>
      <?=view('orders/Detail')?>
    </section>
    <?php else : ?>
    <section class='order-empty-container grid-column-span-2'>
      <?=view('layout/Chart/LineChart')?>
    </section>
    <?php endif; ?>
  </div>
  <div class='pre-order'></div>
</main>