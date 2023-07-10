<div class="w-100 p-3 border border-dark">
  <canvas id="orderStatistics" style="height: 17rem; width: 100%"></canvas>
</div>
<div class='w-100 mt-3'>
  <div class='border border-dark p-3' style='width: 40%; height: 35rem;'>
    <canvas id='orderBrands'><canvas>
  </div>
</div>

<script>
<?php 
  $data = Array();
  $labels = Array();
  $dateStart = date('Y-m-d', strtotime('-5 days'));
  $showDadys = ceil((strtotime('NOW') -  strtotime($dateStart)) / (24 * 60 * 60));

  for($i = 0; $i < $showDadys; $i++ ) {
    array_push($labels, date('Y-m-d', strtotime($dateStart."+$i days")) );
    array_push($data, 0);
  }

  if ( count($statistics) > 0 ) :
    foreach( $statistics AS $o ) :
      foreach($labels AS $j => $l) : 
        if ( date('Y-m-d', strtotime($l)) == date('Y-m-d', strtotime($o['created_at_co']))) {
          $data[$j] = $o['subtotal_amount'];
        }
      endforeach;
    endforeach;
  endif;

  $brandLabels = Array();
  $brandData = Array();
  if ( !empty($orderBrand) ) {
    foreach( $orderBrand AS $brandCnt ) :
      array_push($brandLabels, strtoupper($brandCnt['brand_name']));
      array_push($brandData, $brandCnt['cnt']);
    endforeach;
  }
?>
// console.log(<?//=json_encode($orderBrand)?>);

let data = <?=json_encode($data)?>;
let labels = <?=json_encode($labels)?>;
const ctx = document.getElementById('orderStatistics');

new Chart(ctx, {
  type: 'line',
  data: {
    labels : labels,
    datasets: [{
      label: 'Order Amount',
      data: data,
      borderWidth: 2
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true
      }
    },
    plugins: {
      title: {
        display: true,
        text: 'Daily order amount'
      }
    }
  }
});

const orderBrand = $("#orderBrands");
new Chart(orderBrand, {
  type: 'polarArea',
  data: {
    labels : <?=json_encode($brandLabels)?>,
    datasets: [{
      data: <?=json_encode($brandData)?>,
      // borderWidth: 1
    }]
  },
  options: {
    // responsive: true,
    // scales: {
    //   y: {
    //     beginAtZero: false
    //   }
    // },
    plugins: {
      title: {
        display: true,
        text: 'Order volume by brand'
      }
    }
  }
});
</script>