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
        d.userlog = 1;
      }
    },
    columns: [
      { data: 'first_name', name: 'first_name' },
      { data: 'last_name', name: 'last_name' },
      { data: 'email', name: 'email' },
      { data: admin_type ? 'company_name' : 'team_name' , name: admin_type ? 'company_name' : 'team_name' },
      { data: 'role_name', name: 'role_name' },
      { data: 'last_login_at', name: 'last_login_at', render: renderColumnDate },
      { data: 'last_login_ip', name: 'last_login_ip' },
      { data: 'action', name: 'action', sortable: false, class: 'text-center', width: 220 },
    ],
      'order': [[5, 'desc']]
  });

  $('.datatable').attr('style', 'border-collapse: collapse !important');

  $("#company_id").on("change", function () {
    table.ajax.reload();
  });

});
