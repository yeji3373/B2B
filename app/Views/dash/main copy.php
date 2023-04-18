<main class='p-3'>
  <section>
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
  </section>
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
      <h6 class='bg-black p-2 mb-0 text-white'>공지사항</h6>
      <div class='d-flex flex-column'>
        <div class='w-100 py-1 px-2'>고정 공지사항</div>
        <div class='w-100 py-1 px-2'>고정 공지사항</div>
        <div class='w-100 py-1 px-2'>1. 공지사항</div>
        <div class='w-100 py-1 px-2'>2. 공지사항</div>
        <div class='w-100 py-1 px-2'>3. 공지사항</div>
      </div>
    </section>
    <section class='border border-dark'>
      <h6 class='bg-black p-2 mb-0 text-white'>문의 답변</h6>
      <div class='d-flex flex-column'>
        <div class='w-100 py-1 px-2'>1. 문의 답변</div>
        <div class='w-100 py-1 px-2'>2. 문의 답변</div>
        <div class='w-100 py-1 px-2'>3. 문의 답변</div>
        <div class='w-100 py-1 px-2'>4. 문의 답변</div>
        <div class='w-100 py-1 px-2'>5. 문의 답변</div>
      </div>
    </section>
    <section class='delivery-sec border border-dark'>
      <h6 class='bg-black p-2 mb-0 text-white'>배송</h6>
      <div class='w-100 d-flex flex-row justify-content-between'>
        <div class='border rounded' style='height: 9rem; width: 6rem;'>주문완료</div>
        <div class='border rounded' style='height: 9rem; width: 6rem;'>주문확인</div>
        <div class='border rounded' style='height: 9rem; width: 6rem;'>상품확인</div>
        <div class='border rounded' style='height: 9rem; width: 6rem;'>상품패킹</div>
        <div class='border rounded' style='height: 9rem; width: 6rem;'>상품발송</div>
      </div>
      <!-- 출고준비중, 출고완료 날짜도 표시하기 -->
    </section>
  </div>
</main>
<script>
  google.charts.load('current', {'packages':['corechart', 'bar']});
  google.charts.setOnLoadCallback(drawLineChart);
  google.charts.setOnLoadCallback(drawBarChart);
  function drawLineChart() {
    var data = google.visualization.arrayToDataTable([
      ['Day', 'Order Price'], 
      <?php foreach($order as $o ) : 
        echo  "['". date('Y-m-d', strtotime($o['created_at']))."' ,".$o['order_amount']."], ";
        endforeach ?>
    ]);
    var options = {
      title: 'Daily Sales Amount',
      curveType: 'function',
      legend: {
        position: 'top'
      }
    };
    var chart = new google.visualization.LineChart(document.getElementById('GoogleLineChart'));
    chart.draw(data, options);
  }


  // Bar Chart
  // google.charts.setOnLoadCallback(showBarChart);

  function drawBarChart() {
    var data = google.visualization.arrayToDataTable([
      ['Day', 'Total Price'], 
      <?php 
        $startDays = date('Y-m-d', strtotime(date('Y-m-d')."-".date('w')."days"));
        // $endDays = date('Y-m-d', strtotime(date($startDays)."+6days"));
        
        for($i = 0; $i < 7; $i++) {
          $a = date('Y-m-d', strtotime($startDays."+".$i."days"));
          foreach($order as $o) {
            if ( date('Y-m-d', strtotime($o['created_at'])) == $a ) {
              echo  "['". date('Y-m-d', strtotime($o['created_at']))."' ,".$o['subtotal_amount']."], "; 
            } else {
              echo "['". date('Y-m-d', strtotime($a))."' , 0], "; 
            }
          }
        }
        // echo "console.log($startDays $endDays);";
        // foreach($order as $o ) : 
        //   echo  "['". date('Y-m-d', strtotime($o['created_at']))."' ,".$o['order_amount']."], ";
        // endforeach;
      ?>
    ]);
    // var options = {
    //   title: 'Monthly Sales Amount',
    //   is3D: true,
    //   bars: 'horizontal'
    // };
    // var chart = new google.visualization.BarChart(document.getElementById('GoogleBarChart'));
    // chart.draw(data, options);
    var options = {
      chart: {
        title: 'Weekly Purchase Status',
        // subtitle: 'Sales, Expenses, and Profit: 2014-2017',
      },
      is3D: true,
      // bars: 'horizontal',
    };

    var chart = new google.charts.Bar(document.getElementById('GoogleBarChart'));
    chart.draw(data, google.charts.Bar.convertOptions(options));
  }
</script>