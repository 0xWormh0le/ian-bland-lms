@if($chartActive == "on")
    @include('reports.chart.chart')
@endif
@if($tableActive == "on")
    @include('reports.chart.table')
@endif
  