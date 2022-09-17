$(function () {

  var c_t_column_data = 'company_name' ;
  var c_t_column_name = 'companies.company_name' ;

  var company_id = $("#company_id").val();

  if(company_id >0)
  {
     c_t_column_data = 'team_name' ;
     c_t_column_name = 'teams.team_name' ;
  }

  var admin_type = $("#datatable").data("admin-type");

  var table = $('#datatable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: $('#datatable').data("url"),
      data: function (d) {
        d.company_id = $('#company_id').val();
      }
    },
    columns: [
      { data: 'id', name: 'users.id' },
      { data: 'first_name', name: 'first_name' },
      { data: 'last_name', name: 'last_name' },
      { data: 'email', name: 'email' },
      { data: admin_type ? 'company_name' : 'team_name' , name: admin_type ? 'company_name' : 'team_name' },
      { data: 'department', name: 'department' },
      { data: 'role_name', name: 'role_name' },
      { data: 'ad', name: 'ad'},
      { data: 'active', name: 'active'},
      { data: 'action', name: 'action', sortable: false, class: 'text-center', width: 220 },
    ],
    'columnDefs': [
         {
            'targets': 0,
            'checkboxes': {
               'selectRow': true
            }
         }
      ],
      'select': {
         'style': 'multi'
      },
      'order': [[1, 'asc']]
  });


  $('.datatable').attr('style', 'border-collapse: collapse !important');

  $("#company_id").on("change", function () {
    table.ajax.reload();
  });
  updateStatus =function(id, element)
  {

    var user_id = id ;
    var status = 0;
    if(element.checked) status = 1;
    var token = $("#csrf").val();
    var urlvar = $("#status_route").val();

    $.ajax({
      type: 'POST',
      url: urlvar,
      data: {
          '_token': token,
          'id': user_id,
          'status': status
      },
      success: function(msg) {
              if(msg=='error')
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
                  title: msg,
                  text: '',
                  type: 'success',
                  timer: 2000,
                  showConfirmButton: false,
                });
               table.ajax.reload();
              }
          },
      error:function(error){
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


   // Handle form submission event
   $('.bulk-btn').on('click', function(e){

      var urlvar = $(this).data("url");
      var type = $(this).data("type");
      var rows_selected = table.column(0).checkboxes.selected();
      var ids = [];
      if(rows_selected.length > 0){
      swal({
        title: "Are you sure to "+type+" selected?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
      }).then((result) => {
        if (result.value) {


         // Iterate over all selected checkboxes
        var i=0;
        $.each(rows_selected, function(index, rowId){
           // Create a hidden element
           ids[i] = rowId;
           i++;
        });

        bulkAjax(urlvar, ids);
     }
    });
   }
   else {
     swal({
       title: 'Error occured!',
       text: 'Please select checkbox',
       type: 'success',
       timer: 2000,
       showConfirmButton: false,
     });
   }
  });

var bulkAjax = function(urlvar, ids){
   var token = $("#csrf").val();
   $.ajax({
     type: 'POST',
     url: urlvar,
     data: {
         '_token': token,
         'ids': JSON.stringify(ids)
     },
     success: function(msg) {
             if(msg=='error')
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
                 title: msg,
                 text: '',
                 type: 'success',
                 timer: 2000,
                 showConfirmButton: false,
               });
              table.ajax.reload();
             }
         },
     error:function(error){
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
