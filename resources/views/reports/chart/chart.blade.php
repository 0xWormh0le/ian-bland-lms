<div class="row justify-content-md-center">
  <div class="col-12">
    <div id="chart" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
  </div>
</div>


<script>

var options = {
    chart: {
        renderTo: 'chart',
        type: '{!! $chartType !!}',
        @if($chart3d)
        options3d: {
            enabled: true,
            @if($chartType == 'column' || $chartType == 'bar')
            alpha: 10,
            beta: 25,
            depth: 70
            @elseif($chartType == 'pie')
            alpha: 45,
            beta: 0
            @endif
        }
        @endif
    },
    title: {
        text: '{!! $chartTitle !!}'
    },
    subtitle: {
        text: '{!! $chartSubtitle !!}'
    },
    credits: {
        @if(!$company)
        enabled: false,
        @endif
        text: "{{@$company->company_name}}",
        href: ""
    },
    xAxis: {
        type: 'category',
        @if($withCategories)
        categories: ['{!!$categories!!}']
        @endif
    },
    yAxis: {
        title: ''
    },
    legends: {
        enabled: false,
    },
    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b><br/>'
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:.1f}'
            }
        },
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            depth: 35,
            dataLabels: {
                enabled: true,
                format: '{point.name}'
            }
        }
    },
    
    series: {!! $chartResultSeries !!}
};

var chart = new Highcharts.chart('chart', options);
</script>
