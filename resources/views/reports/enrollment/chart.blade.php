<div class="row justify-content-md-center">
  <div class="col-12">
    <canvas id="chart"></canvas>  
  </div>
</div>

<script>
var ctx = document.getElementById('chart').getContext('2d');

data = {!! $chart !!};

options = {
  responsive: true,
  plugins: {
    labels: {
      render: 'percentage',
      precision: 2,
      showZero: true,
      fontColor: '#fff',
    }
  }
};

var chart = new Chart(ctx,{
    type: 'pie',
    data: data,
    options: options,
});
</script>
