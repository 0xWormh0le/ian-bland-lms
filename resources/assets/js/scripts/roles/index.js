$(function () {
  var table = $('#datatable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: $('#datatable').data("url"),
    },
    columns: [
      { data: 'role_name', name: 'role_name' },
      { data: 'created_at', name: 'created_at', width: 120, render: renderColumnDate },
      { data: 'action', name: 'action', sortable: false, class: 'text-center', width: 120 },
    ],
  });
  $('.datatable').attr('style', 'border-collapse: collapse !important');
});