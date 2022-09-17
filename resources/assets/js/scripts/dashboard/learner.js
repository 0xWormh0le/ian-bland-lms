$(document).ready(function () {

  var monthlyEnroll = $('#monthly-chart'),
    labels = monthlyEnroll.data('label').split(','),
    enrolled = monthlyEnroll.data('enrolled').split(','),
    completed = monthlyEnroll.data('completed').split(',');
    incomplete = monthlyEnroll.data('incomplete').split(',');
    total = monthlyEnroll.data('total');

  var courseCtx = new Chart(monthlyEnroll, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: trans('js.enrolled'),
        backgroundColor: '#FF8C00',
        data: enrolled
      },
        {
          label: trans('js.completed'),
          backgroundColor: '#66CC00',
          data: completed
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
            stepSize: 1,
            max: total,
            autoSkip: false
          }
        }]
      }
    }
  });

});
