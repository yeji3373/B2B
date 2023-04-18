<!-- <main class='p-3'> -->
  <!-- <section>
    <div class='swiper'>
      <div class='swiper-wrapper'>
        <div class='swiper-slide'>
          <img src="http://beautynetkorea.daouimg.com/banner_intense_line_b2b_400.jpg" alt="">
        </div>
        <div class='swiper-slide'>
          <img src="http://beautynetkorea.daouimg.com/glossyttint_banner_b2b_400.jpg" alt="">
        </div>
        <div class='swiper-slide'>
          <img src="http://beautynetkorea.daouimg.com/velvettint_banner_b2b_400.jpg" alt="">
        </div>
      </div>
    </div>
  </section> -->
  <section class='w-100 d-flex flex-row flex-wrap justify-content-between mb-3'>
    <!-- <div class="w-48 border border-dark">
      <div id="GoogleLineChart" style="height: 20rem; width: 100%"></div>
    </div> -->
    <div class="w-48 p-3 border border-dark">
      <div id="GoogleBarChart" style="height: 17rem; width: 100%"></div>
    </div>
  </section>
  <div class='w-100 d-grid mb-3' style='grid-template-columns: repeat(3, 1fr); grid-gap: 1rem;'>
    <section class='notice-sec border border-dark'>
      <h6 class='bg-black p-2 mb-0 text-white'>Notice</h6>
      <div class='d-flex flex-column' style='min-height: 9.54rem;'>
        <?php if ( !empty($notices) ) : 
          foreach($notices AS $notice) : ?>
          <div class='w-100 py-1 px-2 <?=!empty($notice['fixed']) ? 'bg-danger' : ''?>'><?=$notice['title']?></div>
        <?php endforeach;
        else : 
          echo "<div class='w-100 py-1 px-2 text-center'>no information</div>";
        endif; ?>
        <!-- <div class='w-100 py-1 px-2'>고정 공지사항</div>
        <div class='w-100 py-1 px-2'>고정 공지사항</div>
        <div class='w-100 py-1 px-2'>1. 공지사항</div>
        <div class='w-100 py-1 px-2'>2. 공지사항</div>
        <div class='w-100 py-1 px-2'>3. 공지사항</div> -->
      </div>
    </section>
    <section class='border border-dark'>
      <h6 class='bg-black p-2 mb-0 text-white'>Q&A</h6>
      <div class='d-flex flex-column'>
        <!-- <div class='w-100 py-1 px-2'>1. 문의 답변</div>
        <div class='w-100 py-1 px-2'>2. 문의 답변</div>
        <div class='w-100 py-1 px-2'>3. 문의 답변</div>
        <div class='w-100 py-1 px-2'>4. 문의 답변</div>
        <div class='w-100 py-1 px-2'>5. 문의 답변</div> -->
      </div>
    </section>
    <section class='delivery-sec border border-dark'>
      <h6 class='bg-black p-2 mb-0 text-white'>Packaging & Shipping Status</h6>
      <div class='w-100 d-flex flex-row justify-content-between'>
        <!-- <div class='border rounded' style='height: 9rem; width: 6rem;'>주문완료</div>
        <div class='border rounded' style='height: 9rem; width: 6rem;'>주문확인</div>
        <div class='border rounded' style='height: 9rem; width: 6rem;'>상품확인</div>
        <div class='border rounded' style='height: 9rem; width: 6rem;'>상품패킹</div>
        <div class='border rounded' style='height: 9rem; width: 6rem;'>상품발송</div> -->
      </div>
      <!-- 출고준비중, 출고완료 날짜도 표시하기 -->
    </section>
  </div>
<!-- </main> -->
<script>
  aaaaa();
  <?php 
  $data = [];
  foreach($order as $o ) : 
    array_merge($data, date('Y-m-d', strtotime($o['created_at']))."' ,".$o['order_amount']);
  endforeach;

  // print_r($data);
  echo "data=".json_encode($data);
  ?>
  // google.charts.setOnLoadCallback(drawLineChart($('#GoogleLineChart'), ['Day', 'Order Price'], data));
</script>