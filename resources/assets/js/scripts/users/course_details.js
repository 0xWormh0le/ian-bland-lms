$(function () {
  var btn_txt = $("#export_btn").val();
  var table = $('#datatable').DataTable({
    processing: true,
    serverSide: true,
    searching: false,
    paging:false,
    dom: 'Bfrtip',
    buttons: [
      // { extend: 'excel',  text: btn_txt, className: "btn btn-sm btn-success" }
    ],
    ajax: {
      url: $('#datatable').data("url"),
      data: function (d) {
      //  d.user_id = $('#user_id').val();
     }
    },
    columns: [
      { data: 'title', name: 'title' },
      { data: 'enrol_date', name: 'enrol_date' },
      { data: 'active', name: 'active' },
      { data: 'completion_percentage', name: 'completion_percentage' },
      { data: 'completed', name: 'completed' },
      { data: 'score', name: 'score' },
      { data: 'total_time', name: 'total_time' },
      { data: 'completion_date', name: 'completion_date' },
    ]
  });

  $('#datatable-course-detail').DataTable({
    processing: true,
    serverSide: true,
    ajax: $("#datatable-course-detail").data("url"),
    columns: [
        { data: 'title', name: 'title' },
        // { data: 'mod_title', name: 'mod_title' },
        { data: 'enrol_date', name: 'enrol_date' },
        { data: 'complete_status', name: 'complete_status', class:'text-center' },
        { data: 'score', name: 'score'},
        { data: 'total_time', name: 'total_time' },
        { data: 'completion_date', name: 'completion_date' },

    ]
});

  $('.datatable').attr('style', 'border-collapse: collapse !important');


});
