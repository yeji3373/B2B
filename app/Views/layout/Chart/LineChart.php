<div class="w-100 p-3 border border-dark">
  <canvas id="orderStatistics" style="height: 17rem; width: 100%"></canvas>
</div>
<script>
<?php 
$data = Array();
$data1 = Array();
$labels = Array();

if ( !empty($statistics) ) :
  foreach($statistics AS $i => $o) :
    array_push($labels, $o['date']);
    array_push($data, $o['subtotal_amount']);
    array_push($data1, $o['request_amount']);
  endforeach;
endif;
?>

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
    }, {
      label: 'Request Amount',
      data: data1,
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
</script>