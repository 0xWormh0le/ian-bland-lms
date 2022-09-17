$(document).ready(function () {


if(document.getElementById("doughnut-chart") != null)
{
    var donutEl = document.getElementById("doughnut-chart").getContext("2d");

    var total_pass_score = document.getElementById("total_pass_score").value;
    var remaining = document.getElementById("remaining").value;

    var data = {
      labels: [
          trans('js.completed'),
          trans('js.incomplete'),

      ],
        datasets: [{
              data:[total_pass_score, remaining],
              backgroundColor: ['#1eb812','#87cefa'],
              },
        ]
    };
    //(Math.random()*0xFFFFFF<<0).toString(16)
    new Chart(donutEl, {
        type: 'doughnut',
        data: data,
        options: 	{
        		segmentShowStroke : true,
        		segmentStrokeColor : "#fff",
        		segmentStrokeWidth : 2,
        		percentageInnerCutout : 50,
        		animationSteps : 100,
        		animationEasing : "easeOutBounce",
        		animateRotate : true,
        		animateScale : false,
        		responsive: true,
        		maintainAspectRatio: true,
        		showScale: true,
        		animateScale: true
        	}
    });
}
if(document.getElementById("bar-chart") != null)
{
    var barEl = document.getElementById("bar-chart").getContext("2d");
    var login_count = document.getElementById("login_count").value;
    var enroll_count = document.getElementById("enroll_count").value;

    var course_completion_count = document.getElementById("course_completion_count").value;
    var ylable = document.getElementById("ylable").value;
    var yArr = ylable.toString().split(",");
    var data = {
  //  labels: ["January", "February", "March", "April", "May", "June", "July","August","September","October","November","December"],
    labels: yArr,
    datasets: [
        {
            label: trans('js.enrolled'),
            backgroundColor: "#3342ff",
            borderColor: "#3342ff",
            borderWidth: 2,
            hoverBackgroundColor: "#42ace5",
            hoverBorderColor: "#42ace5",
            data: [enroll_count],
        },
        {
            label: trans('js.completed'),
            backgroundColor: "#1eb812",
            borderColor: "#1eb812",
            borderWidth: 2,
            hoverBackgroundColor: "#51e542",
            hoverBorderColor: "#51e542",
            data: [course_completion_count],
        }
    ]
};
var option = {
  responsive: true,
  maintainAspectRatio: false,
	scales: {
  	yAxes:[{
    		stacked:false,
        gridLines: {
        	display:true,
          color:"rgba(255,99,132,0.2)"
        },
        ticks: {
          beginAtZero: true,
          stepSize: 1,
        }
    }],
    xAxes:[{
        gridLines: {
        	display:false,
        },
        ticks: {
          autoSkip: false
        }
    }]
  }
};
var myBarChart = new Chart(barEl, {
    type: 'bar',
    data: data,
    options: option
});

}


$('.bar-btn').click(function(){

    // myBarChart.data.datasets.forEach((dataset) => {
    //     dataset.data.pop();
     //});
     var user_id =  $(this).data("id");
     var type = $(this).data("type");
     var filter = $(this).data("filter");

     if(type == "period")
     {
        $("#periodModal").modal("show");
     }
     else {
            statisticAjax(user_id, type, '', '', filter);
     }

  /* myBarChart.data.datasets.forEach((dataset) => {
        var dataset = [{data:[1]},{data:[2]}];
        dataset.data.push();
    });*/

});

  $("#periodSubmit").on("click", function(){

     $("#periodModal").modal("hide");
     var from_date = $("#from_date").val();
     var to_date = $("#to_date").val();
     var user_id = $(".bar-btn").data("id");
     var date_range = from_date+"#"+to_date ;
     var day_range = $("#day_range").val();
      var filter = $("#filter").val();

     $("#from_date").val('');
     $("#to_date").val('');
     $("#day_range").val('');
     statisticAjax(user_id, 'period', date_range, day_range, filter);
  });

var statisticAjax = function(user_id, type, date_range, day_range, filter){

  var statistics_url = $("#statistics_url").val();
  var token = $("#token").val();

  $.ajax({
    type: 'POST',
    url: statistics_url,
    data: {
        '_token': token,
        'id': user_id,
        'type': type,
        'date_range' : date_range,
        'day_range' : day_range,
        'filter' : filter
    },
    success: function(msg) {

           while(myBarChart.data.labels.length > 0)
           {
             myBarChart.data.labels.pop();
           }
           while(myBarChart.data.datasets.length > 0)
           {
             myBarChart.data.datasets.pop();
           }

            myBarChart.update();


            var labelArr = msg.yLabel.toString().split(",");
            var loginCArr = msg.loginCount.toString().split(",");
            var enrollCArr = msg.enrollCount.toString().split(",");
            var courseCArr = msg.courseCompletionCount.toString().split(",");
            for($c=0;$c<labelArr.length;$c++)
            {
              myBarChart.data.labels.push(labelArr[$c]);
            }

            myBarChart.data.datasets.push(  {
                  label: trans('js.enrolled'),
                  backgroundColor: "#3342ff",
                  borderColor: "#3342ff",
                  borderWidth: 2,
                  hoverBackgroundColor: "#42ace5",
                  hoverBorderColor: "#42ace5",
                  data: enrollCArr,
              });
            myBarChart.data.datasets.push(  {
                  label: trans('js.completed'),
                  backgroundColor: "#1eb812",
                  borderColor: "#1eb812",
                  borderWidth: 2,
                  hoverBackgroundColor: "#51e542",
                  hoverBorderColor: "#51e542",
                  data: courseCArr,
              });
            myBarChart.update();

        },
    error:function(error){

    }
  });
}

if(document.getElementById("line-chart") != null)
{
      var monthData = document.getElementById("month_data").value;
      var monthLabel = document.getElementById("month_label").value;

     var yArr = monthData.toString().split(",");
     var xArr = monthLabel.toString().split(",");

    var linedata = {
        labels: xArr,
        //labels: yArr,
        datasets: [
            {
                borderColor: "#87cefa",
                "lineTension":0.1,
                "fill":false,
                data: yArr,
            }
        ]
    };

    var lineEl = document.getElementById("line-chart").getContext("2d");
    var courseLineChart = new Chart(lineEl, {
        type: 'bar',
        data: linedata,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            display: false,
          },
          scales: {
            yAxes:[{
                stacked:false,
                gridLines: {
                  display:true,
                  color:"rgba(255,99,132,0.2)"
                },
                ticks: {
                  beginAtZero: true,
                }
            }],
            xAxes:[{
                gridLines: {
                  display:false,
                },
                ticks: {
                  autoSkip: false
                }
            }]
          }
        }
    });
}

if(document.getElementById("course-doughnut-chart") != null)
{
    var donutEl = document.getElementById("course-doughnut-chart").getContext("2d");

    var total_users = document.getElementById("total_users").value;
    var process_course = document.getElementById("process_course").value;
    var completed_course = document.getElementById("completed_course").value;

    var data = {
      labels: [
          trans('js.total_users'),
          trans('js.user_completed'),
          trans('js.user_in_progress'),
        ],
        datasets: [{
              data:[total_users, completed_course, process_course],
              backgroundColor: ['#87cefa','#1eb912','#cccccc'],
              },
        ]
    };
    //(Math.random()*0xFFFFFF<<0).toString(16)
    new Chart(donutEl, {
        type: 'doughnut',
        data: data,
        options: 	{
        		segmentShowStroke : true,
        		segmentStrokeColor : "#fff",
        		segmentStrokeWidth : 2,
        		percentageInnerCutout : 50,
        		animationSteps : 100,
        		animationEasing : "easeOutBounce",
        		animateRotate : true,
        		animateScale : false,
        		// responsive: false,
        		// maintainAspectRatio: false,
        		showScale: true,
        		animateScale: true
        	}
    });
}

if(document.getElementById("course-user-chart") != null)
{

  var monthData = document.getElementById("course_user_date_data").value;
  var monthLabel = document.getElementById("course_user_label").value;

 var yArr = monthData.toString().split(",");
 var xArr = monthLabel.toString().split(",");

var linedata = {
    labels: xArr,
    //labels: yArr,
    datasets: [
        {
            label: trans('js.user_course_score_statistics'),
            borderColor: "#87cefa",
            "lineTension":0.1,
            "fill":false,
            data: yArr,
        }
    ]
};

var lineEl = document.getElementById("course-user-chart").getContext("2d");
var courseLineChart = new Chart(lineEl, {
    type: 'line',
    data: linedata,
    options: {scales: {
      yAxes:[{
          stacked:false,
          gridLines: {
            display:true,
            color:"rgba(255,99,132,0.2)"
          },
          ticks: {
            beginAtZero: true
          }
      }],
      xAxes:[{
          gridLines: {
            display:false,
          },
          ticks: {
            autoSkip: false
          }
      }]
    }}
});

}


});
