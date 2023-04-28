<div class="w-100 p-3 border border-dark">
  <canvas id="orderStatistics" style="height: 17rem; width: 100%"></canvas>
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
?>

let data = <?=json_encode($data)?>;
let labels = <?=json_encode($labels)?>;
const ctx = document.getElementById('orderStatistics');

new Chart(ctx, {
  type: 'line',
  data: {
    labels : labels,
    datasets: [{
      label: 'Order Price',
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
        text: 'Order Status'
      }
    }
  }
});
</script>