$(document).ready(function () {

  $("#search").keyup(function(){
    var filter = $(this).val();
    $(".courseitem").each(function(){
     if ($(this).attr('data-title').search(new RegExp(filter, "i")) < 0) {
      $(this).fadeOut();
     } else {
      $(this).show();
     }
    });
  });

  $("select[name='language']").change(function(){
      getCourses();
   });

   $("select[name='subcategory']").change(function(){

      getCourses();
   });

  $("select[name='category']").change(function(){

     getCourses();

      var category_id =$(this).val() ;
      var sub_category_url = $("#subcategory_url").val();
      var token = $("#token").val();

      if(category_id == 0 || category_id==""){

        var options = '<option value="0">All</option>';

        $('#subcategory').html(options);
        $('#sub_category_option').css('display','none');
         return;
      }

     if(category_id == 0) return;
     $.ajax({

           url: sub_category_url,
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
    var language =$("select[name='language']").val() ;
    var course_url = $("#course_url").val();
    var token = $("#token").val();
    var page = $("#page").val();

    $.ajax({

          url: course_url,
          method: 'POST',
          type: 'json',
          data: {'category_id':category_id,'sub_category_id': sub_category_id,'language': language,'page':page,_token: token},
          success: function(data) {

               $("#courses").html(data);
           }
        });
  }
});
