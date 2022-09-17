$(document).ready(function () {
  var options = function (chartData) {
    return {
      maintainAspectRatio: false,
      legend: {
        display: false
      },
      scales: {
        xAxes: [{
          gridLines: {
            color: 'transparent',
            zeroLineColor: 'transparent'
          },
          ticks: {
            fontSize: 2,
            fontColor: 'transparent'
          }
        }],
        yAxes: [{
          display: false,
          ticks: {
            display: false,
            beginAtZero: true,
            max: Math.max.apply(Math, chartData) + 1
          }
        }]
      },
      elements: {
        line: {
          borderWidth: 1
        },
        point: {
          radius: 4,
          hitRadius: 10,
          hoverRadius: 4
        }
      }
    }
  }

  var cardChart1 = $('#card-chart1'),
      chartLabels = cardChart1.data('label').split(","),
      chartData = cardChart1.data('value').split(",");
  var chart1 = new Chart(cardChart1, {
    type: 'line',
    data: {
      labels: chartLabels,
      datasets: [{
        label: trans('js.new_course'),
        backgroundColor: getStyle('--primary'),
        borderColor: 'rgba(255,255,255,.55)',
        data: chartData
      }]
    },
    options: options(chartData)
  });

  var cardChart2 = $('#card-chart2'),
    chartLabels = cardChart2.data('label').split(","),
    chartData = cardChart2.data('value').split(",");
  var chart2 = new Chart(cardChart2, {
    type: 'line',
    data: {
      labels: chartLabels,
      datasets: [{
        label: trans('js.new_user'),
        backgroundColor: getStyle('--info'),
        borderColor: 'rgba(255,255,255,.55)',
        data: chartData
      }]
    },
    options: options(chartData)
  });

  var cardChart3 = $('#card-chart3'),
    chartLabels = cardChart3.data('label').split(","),
    chartData = cardChart3.data('value').split(",");
  var chart3 = new Chart(cardChart3, {
    type: 'line',
    data: {
      labels: chartLabels,
      datasets: [{
        label: trans('js.completed'),
        backgroundColor: getStyle('--success'),
        borderColor: 'rgba(255,255,255,.55)',
        data: chartData
      }]
    },
    options: options(chartData)
  });

  var cardChart4 = $('#card-chart4'),
    chartLabels = cardChart4.data('label').split(","),
    chartData = cardChart4.data('value').split(",").map(function (d) {
      return Math.round((100 - d) * 100) / 100;
    });
  var chart4 = new Chart(cardChart4, {
    type: 'line',
    data: {
      labels: chartLabels,
      datasets: [{
        label: trans('js.incomplete'),
        backgroundColor: getStyle('--warning'),
        borderColor: 'rgba(255,255,255,.55)',
        data: chartData
      }]
    },
    options: options(chartData)
  });


  var courseChart = $('#course-chart'),
    labels = courseChart.data('label'),
    courseCompleted = courseChart.data('completed'),
    courseIncomplete = courseChart.data('incomplete'),
    courseCompletedNumber = courseChart.data('completednumber'),
    courseIncompleteNumber = courseChart.data('incompletenumber');

    if(labels.indexOf(";") > 0)
      labels = labels.split(';');
    else
      labels = [labels];
    if(courseCompleted.toString().indexOf(";") > 0)
      courseCompleted = courseCompleted.split(';');
    else
      courseCompleted = [courseCompleted];

    if (courseIncomplete.toString().indexOf(";") > 0)
      courseIncomplete = courseIncomplete.split(';');
    else
      courseIncomplete = [courseIncomplete];

    if (courseCompletedNumber.toString().indexOf(";") > 0)
      courseCompletedNumber = courseCompletedNumber.split(';');
    else
      courseCompletedNumber = [courseCompletedNumber];

    if (courseIncompleteNumber.toString().indexOf(";") > 0)
      courseIncompleteNumber = courseIncompleteNumber.split(';');
    else
      courseIncompleteNumber = [courseIncompleteNumber];

  var courseCtx = new Chart(courseChart, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: trans('js.completed'),
        backgroundColor: '#66CC00',
        data: courseCompleted
      },
        {
          label:  trans('js.incomplete'),
          backgroundColor: '#FF8C00',
          data: courseIncomplete
        }]
    },
    options: {
      scales: {
        xAxes:[{
            gridLines: {
            	display:true,
            },
            ticks: {
              autoSkip: false
            }
        }],
        yAxes: [{
          beginAtZero: true,
          ticks: {
            stepSize: 10,
            max: 100,
            min: 0,
            autoSkip: false
          }
        }]

      },
      tooltips: {
          callbacks: {
              label: function(tooltipItem, data) {
                    if(data.datasets[tooltipItem.datasetIndex].label == trans('js.completed'))
                    {
                      var label = data.datasets[tooltipItem.datasetIndex].label || '';
                      label += ': '+courseCompletedNumber[tooltipItem.index];
                    }
                    if(data.datasets[tooltipItem.datasetIndex].label == trans('js.incomplete'))
                    {
                      var label = data.datasets[tooltipItem.datasetIndex].label || '';
                      label += ': '+courseIncompleteNumber[tooltipItem.index];
                    }


                  return label;
              }
          }
      }

    }
  });

});
