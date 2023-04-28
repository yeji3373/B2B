function aaaaa(array) {
  console.log("load");
  google.charts.load('current', {'packages':['corechart', 'bar']});
}

function drawLineChart(target, legend, data, ops) {
  var chartData = google.visualization.arrayToDataTable([
    legend,
    data
  ]);

  var options = {
    title: 'Daily Sales Amount',
    curveType: 'function',
    legend: {
      position: 'top'
    }
  };

  var chart = new google.visualization.LineChart(target);
  chart.draw(chartData, options);
}