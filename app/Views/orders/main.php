<div class="w-100 p-3 border border-dark">
  <div id="GoogleBarChart" style="height: 17rem; width: 100%"></div>
</div>
<?php print_r($statistics); 
foreach( $statistics AS $o ) : 
  // echo json_encode($o)."<br/>";
  echo json_encode([date('Y-m-d', strtotime($o['created_at_co'])), $o['subtotal_amount']]);
  echo "<br/>";
endforeach;?>
<script>
  let data = [];
  <?php 
  $data = Array();
  foreach( $statistics AS $o ) : 
    // array_merge($data, [date('Y-m-d', strtotime($o['created_at_co'])), $o['subtotal_amount']]);
    array_push($data, 
    "'".date('Y-m-d', strtotime($o['created_at_co']))."', '".$o['subtotal_amount']."'");
  //   array_merge($data, $o);
    // print_r($o);
    // echo date('Y-m-d', strtotime($o['created_at_co']));
  endforeach;

  // print_r($data);
  echo "data=".json_encode($data);
  ?>

  // console.log(data);
  // // google.charts.setOnLoadCallback(drawLineChart($('#GoogleLineChart'), ['Day', 'Order Price'], data));
</script>