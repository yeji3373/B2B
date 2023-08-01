<div class="w-100 p-3 border border-dark">
  <canvas id="orderStatistics" style="height: 17rem; width: 100%"></canvas>
</div>
<!-- <div class='w-100 mt-3'>
  <div class='border border-dark p-3' style='width: 40%; height: 35rem;'>
    <canvas id='orderBrands'><canvas>
  </div>
</div> -->

<script>
<?php 
$data = Array();
$data1 = Array();
$labels = Array();
//   $dateStart = date('Y-m-d', strtotime('-5 days'));
//   $showDadys = ceil((strtotime('NOW') -  strtotime($dateStart)) / (24 * 60 * 60));

//   // for($i = 0; $i < $showDadys; $i++ ) {
//   //   array_push($labels, date('Y-m-d', strtotime($dateStart."+$i days")) );
//   //   // array_push($data, 0);
//   // }

if ( count($statistics) > 0 ) :
  foreach($statistics AS $i => $o) :
    array_push($labels, $o['date']);
    array_push($data, $o['subtotal_amount']);
    array_push($data1, $o['request_amount']);
  endforeach;
endif;
//   // // $brandLabels = Array();
//   // // $brandData = Array();
//   // // if ( !empty($orderBrand) ) {
//   // //   foreach( $orderBrand AS $brandCnt ) :
//   // //     array_push($brandLabels, strtoupper($brandCnt['brand_name']));
//   // //     array_push($brandData, $brandCnt['cnt']);
//   // //   endforeach;
//   // // }
// ?>
// // console.log(<?//=json_encode($orderBrand)?>);

let data = <?=json_encode($data)?>;
let data1 = <?=json_encode($data1)?>;
let labels = <?=json_encode($labels)?>;
const ctx = document.getElementById('orderStatistics');

console.log(data);
new Chart(ctx, {
  type: 'line',
  data: {
    labels : labels,
    datasets: [{
      label: 'Order Amount',
      data: data,
      // parsing: {
      //   yAxisKey: 'order'
      // },
      borderWidth: 2
    }, {
      label: 'Request Amount',
      data: data1,
      // parsing: {
      //   yAxisKey: 'request'
      // },
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

// // const orderBrand = $("#orderBrands");
// // new Chart(orderBrand, {
// //   type: 'polarArea',
// //   data: {
// //     labels : <?//=json_encode($brandLabels)?>,
// //     datasets: [{
// //       data: <?//=json_encode($brandData)?>,
// //       // borderWidth: 1
// //     }]
// //   },
// //   options: {
// //     // responsive: true,
// //     // scales: {
// //     //   y: {
// //     //     beginAtZero: false
// //     //   }
// //     // },
// //     plugins: {
// //       title: {
// //         display: true,
// //         text: 'Order volume by brand'
// //       }
// //     }
// //   }
// // });
</script>