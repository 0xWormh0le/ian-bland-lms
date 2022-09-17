$(document).ready(function () {

  var circleProgress = $('.circle-progress');
  if(circleProgress.length > 0) {
    $.each(circleProgress, function(index, item) {
        $(item).circleProgress({
            value: $(item).data("value"),
            fill: "#20a8d8",
          })
          .on('circle-animation-progress', function(event, progress, stepValue) {
            $(this).find('strong').text(Math.round(100 * stepValue.toFixed(2))+'%');
        });
    });
  }

  $(".search-query").keyup(function(){
    var filter = $(this).val();
    $(".courseitem").each(function(){
     if ($(this).attr('data-title').search(new RegExp(filter, "i")) < 0) {
      $(this).fadeOut();
     } else {
      $(this).show();
     }
    });
  });


  $("select[name='subcategory']").change(function(){

     getCourses();
  });

 $("select[name='category']").change(function(){

    getCourses();

     var category_id =$(this).val() ;
     var subcategory_url = $("#subcategory_url").val() ;
     var token = $("#token").val() ;


    if(category_id == 0 || category_id==""){

      var options = '<option value="0">All</option>';

      $('#subcategory').html(options);
      $('#sub_category_option').css('display','none');
       return;
    }
    $.ajax({

          url: subcategory_url,
          method: 'POST',
          type: 'json',
          data: {'category_id': category_id,_token: token},
          success: function(data) {
             if(data.length > 0)
             {
               var options = '<option value="0">All</option>';

               for(var i=0;i < data.length; i++)
               {
                 options += '<option value="'+data[i].id+'">'+data[i].title+'</option>';
               }

               $('#subcategory').html(options);
               $('#sub_category_option').css('display','');
             }
             else {
               var options = '<option value="0">All</option>';

               $('#subcategory').html(options);
               $('#sub_category_option').css('display','none');
             }
           }
        });


 });

 function getCourses()
 {

   var category_id =$("select[name='category']").val() ;
   var sub_category_id =$("select[name='subcategory']").val() ;
   var course_url = $("#course_url").val() ;
   var token = $("#token").val() ;

   $.ajax({

         url: course_url,
         method: 'POST',
         type: 'json',
         data: {'type':'mycourse','category_id':category_id,'sub_category_id': sub_category_id,_token: token},
         success: function(data) {
              $("#courses").html(data);
          }
       });
 }

});
