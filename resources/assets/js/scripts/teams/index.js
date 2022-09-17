$(function () {
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
      { data: 'team_name', name: 'team_name' },
      { data: 'company_name', name: 'companies.company_name', width: 120 },
      { data: 'first_name', name: 'users.first_name' },
      { data: 'created_at', name: 'created_at', width: 100, render: renderColumnDate },
      { data: 'action', name: 'action', sortable: false, class: 'text-center', width: 120 },
    ],
  });
  $('.datatable').attr('style', 'border-collapse: collapse !important');

  $("#company_id").on("change", function () {
    table.ajax.reload();
  });
});
