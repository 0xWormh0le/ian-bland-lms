$(function () {

  var table = $('#datatable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: $('#datatable').data("url")
    },
    columns: [
      { data: 'created_at', name: 'created_at', width: 120, render: renderColumnDate },
      { data: 'sender_name', name: 'sender_name' },
      { data: 'company_name', name: 'companies.company_name' },
      { data: 'content', name: 'content' },
      { data: 'ticket_number', name: 'ticket_number' },
      { data: 'status', name: 'status' },
      { data: 'first_name', name: 'users.first_name' },
      { data: 'action', name: 'action', sortable: false, class: 'text-center', width: 150 },
    ]
  });
  $('.datatable').attr('style', 'border-collapse: collapse !important');


  $("body").on("click", ".assign-ticket", function(){
    var id = $(this).data("id");
    $("#ticket_id").val(id);
    $("#assigned_to").val("");
    getAssigneeList();


  })

  $("#submitAssign").on("click", function(){
    $.ajax({
      type: 'POST',
      url: $(this).data('url'),
      data: {
        _token: $('meta[name="csrf-token"]').attr('content'),
        ticket_id: $("#ticket_id").val(),
        assigned_to: $("#assigned_to").val(),
      },
      dataType: 'JSON',
      success: function (result) {
        $("#assignTicketModal").modal("hide");
        table.ajax.reload();
        swal({
          type: result.type,
          title: result.msg,
          text: result.detail,
          showConfirmButton: false,
          timer: 2000,
        });
      }
    });
  });



  var getAssigneeList = function(){
     var url = $("#assignee_url").val();
     $.ajax({
      type: 'POST',
      url: url,
      data: {
        _token: $('meta[name="csrf-token"]').attr('content'),
        ticket_id: $("#ticket_id").val()
      },
      dataType: 'JSON',
      success: function (result) {

          if(result != null && result.length > 0 )
          {

           result = JSON.parse(JSON.stringify(result));

          var option =   '<option value="">Select</option>';
           for(var r=0; r<result.length; r++)
           {
               if(result[r].last_name == null) result[r].last_name ='';
                option +=   '<option value="'+result[r].id+'">'+result[r].first_name+' '+result[r].last_name+'</option>';
           }
          $("#assigned_to").html(option);
         }
          $("#assignTicketModal").modal("show");
      }
    });
  }


});
