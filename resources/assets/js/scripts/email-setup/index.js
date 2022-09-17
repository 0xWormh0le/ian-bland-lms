$(function () {

  var table;
  var language ;
  var initTemplateTable = function()
  {
       language = $("#language").val();
       table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: $('#datatable').data("url"),
          data: function(d){ d.language = $("#language").val();},
        },
        columns: [
          { data: 'template_name', name: 'template_name' },
          { data: 'subject', name: 'subject' },
          { data: 'updated_at', name: 'updated_at', width: 120 },
          { data: 'action', name: 'action', sortable: false, class: 'text-center', width: 150 },
        ]
      });

      $('.datatable').attr('style', 'border-collapse: collapse !important');
  }
  
  initTemplateTable();

 $("#ajaxSave").click(function(){
   var urlvar = $("#update_url").val();
   var id = $("#template_id").val();
   var token = $("#token").val();
   var template_name= $("#template_name").val();
   var subject= $("#subject").val();
   var language = $("#language").val();
  $("#error_box").css('display','none');
  if($.trim(template_name) == "")
  {
      $("#error_box").css('display','');
      $("#error").html(trans('js.template_name_error'));
  }
  else if($.trim(subject) == "")
  {
    $("#error_box").css('display','');
    $("#error").html(trans('js.template_subject_error'));
  }
  else
  {
  $.ajax({
     type: 'POST',
     url: urlvar,
     data: {
         '_token': token,
         'id': id,
         'template_name': template_name,
         'subject': subject,
         'language' : language
     },
     success: function(msg) {
       $("#templateModal").modal("hide");

             if(msg.msg =='error')
             {
               swal({
                 title: 'Error occured!',
                 text: '',
                 type: 'success',
                 timer: 2000,
                 showConfirmButton: false,
               });
             }
             else{
                swal({
                 title: msg.msg,
                 text: '',
                 type: 'success',
                 timer: 2000,
                 showConfirmButton: false,
               });
               table.ajax.reload();


             }
         },
     error:function(error){
        $("#templateModal").modal("hide");
           swal({
             title: 'Error occured!',
             text: '',
             type: 'success',
             timer: 2000,
             showConfirmButton: false,
           });

     }
   });
  }

 });

});
