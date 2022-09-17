
<div class="col-xl-6">
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-sm-4 pr-0">
          <i class="fa fa-user"></i> @lang('modules.individual_member')
        </div>
        <div class="col-sm-8 text-right">
          <button type="button" id="enrollUser" class="btn btn-md btn-primary"><i class="icon-plus"></i> @lang('modules.enroll_individual')</button>
          <button type="button" id="enrollAll" class="btn btn-md btn-primary"><i class="icon-plus"></i> @lang('modules.enroll_all')</button>
        </div>
      </div>
    </div>
    <div class="card-body">
      <table class="table table-striped table-bordered datatable" id="usersTable">
        <thead>
            <tr>
                <th>@lang('modules.first_name')</th>
                <th>@lang('modules.last_name')</th>
                <th>@lang('modules.role')</th>
                <th>@lang('modules.action')</th>
            </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

@push('modals')
<div id="usersModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">@lang('modules.enroll_user')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table width="100%" class="table table-bordered datatable" id="unenrolledUserTable">
          <thead>
            <tr>
              <th>@lang('modules.first_name')</th>
              <th>@lang('modules.last_name')</th>
              <th>@lang('modules.role')</th>
              <th>@lang('modules.team')</th>
              <th></th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@endpush



@push('modals')
<div id="selfEnrollModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">@lang('modules.enroll_setting')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="course name">@lang('modules.course_title')</label>
          <div class="col-md-6">
           <input type="text" name="course_name" class="form-control" id="course_name" value="{{$data->course->title}}" readonly>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="start date">@lang('modules.start_date')</label>
          <div class="col-md-6">
            <input type="text" name="start_date" class="form-control datepicker"  id="start_date" value="{{\Carbon\Carbon::now()->format('d/m/Y')}}">
           </div>
        </div>
        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="self endroll">@lang('modules.self_enroll')</label>
          <div class="col-md-1">
            <label class="switch switch-label switch-primary">
              <input type="checkbox" name="self_enroll" id="self_enroll" class="switch-input">
              <span class="switch-slider" data-checked="On" data-unchecked="Off"></span>
            </label>
         </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="enrollSubmit" class="btn btn-md btn-primary" data-id="" data-type="" data-action=""><i class="icon-plus"></i> @lang('modules.enroll')</button>
      </div>
    </div>
  </div>
</div>
@endpush
@push('css')
<style>
.dataTables_filter input { width: 150px!important }
</style>
@endpush
@include('_plugins.datepicker')
@push('scripts')
<script>
$(function() {
    var teamsTable = $('#teamsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('courses.companies.team-members', ['course_id' => $data->course_id, 'company_id' => $data->company_id]) !!}',
        columns: [
            { data: 'team_name', name: 'team_name' },
            { data: 'action', name: 'action', sortable:false, class:'text-center', width:60 },
        ]
    });

    var unenrolledTeamTable = $('#unenrolledTeamTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('courses.companies.team-members.unenrolled', ['course_id' => $data->course_id, 'company_id' => $data->company_id]) !!}',
        columns: [
            { data: 'team_name', name: 'team_name' },
            { data: 'action', name: 'action', sortable:false, class:'text-center', width:60 },
        ],
    });

    $("#enrollTeam").on("click", function(){
      unenrolledTeamTable.ajax.reload();
      $("#teamsModal").modal("show");
    });

    var usersTable = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('courses.companies.user-members', ['course_id' => $data->course_id, 'company_id' => $data->company_id]) !!}',
        columns: [
            { data: 'first_name', name: 'users.first_name' },
            { data: 'last_name', name: 'users.last_name' },
            { data: 'role_name', name: 'roles.role_name' },
            { data: 'action', name: 'action', sortable:false, class:'text-center', width:60 },
        ]
    });

    var unenrolledUserTable = $('#unenrolledUserTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('courses.companies.user-members.unenrolled', ['course_id' => $data->course_id, 'company_id' => $data->company_id]) !!}',
        columns: [
            { data: 'first_name', name: 'users.first_name' },
            { data: 'last_name', name: 'users.last_name' },
            { data: 'role_name', name: 'roles.role_name' },
            { data: 'team_name', name: 'teams.team_name' },
            { data: 'action', name: 'action', sortable:false, class:'text-center', width:80 },
        ],
    });

    $("#enrollUser").on("click", function(){
      unenrolledUserTable.ajax.reload();
      $("#usersModal").modal("show");
    });

    var enrollAll = 0 ;
    $("#enrollAll").on("click", function(){
      enrollAll = 1;
      $("#start_date").val("{{\Carbon\Carbon::now()->format('d/m/Y')}}");
      $(".switch-input").prop("checked", false);
      $("#selfEnrollModal").modal("show");

    });


    var actionVar = '';
    var typeVar = '';
    var idVar = '';
    $(document).on('click', '.enrolmember', function (e) {
     var $this = $(this);
     enrollAll = 0;
     actionVar = $this.data('action');
     typeVar = $this.data('type');
     idVar = $this.data('id');
     $('#enrollSubmit').attr('data-action', $this.data('action'));
     $('#enrollSubmit').attr('data-type', $this.data('type'));
     $('#enrollSubmit').attr('data-id', $this.data('id'));
     $("#start_date").val("{{\Carbon\Carbon::now()->format('d/m/Y')}}");
     $(".switch-input").prop("checked", false);

    if(actionVar == "enroll")
    {
     $("#selfEnrollModal").modal("show");

      if(typeVar == "team")
       $("#teamsModal").modal("hide");
      if(typeVar == "user")
       $("#usersModal").modal("hide");
    }
    else{
         $( "#enrollSubmit" ).html("{{ trans('modules.enroll')}}");
         $( "#enrollSubmit" ).trigger( "click" );
    }

   });

    $(document).on('click', '#enrollSubmit', function (e) {
      e.preventDefault();
      var $this = $(this);
      var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> {{ trans("modules.enrolling")}}...';
      if ($(this).html() !== loadingText) {
        $this.data('original-text', $(this).html());
        $this.html(loadingText);
      }

      var self_enroll = 0 ;
      var start_date = $("#start_date").val();
      if($(".switch-input").is(':checked'))
      {
          self_enroll = 1;
      }
      if(enrollAll == 1)
      {
        $.ajax({
          type: 'POST',
          url: "{{ route('courses.companies.users.enroll.all', ['course_id' => $data->course_id, 'company_id' => $data->company_id]) }}",
          data: {
              '_token': $('meta[name="csrf-token"]').attr('content'),
              'start_date': start_date,
              'self_enroll': self_enroll
          },
          success: function(msg) {
            $( "#enrollSubmit" ).html("{{ trans('modules.enroll')}}");
            $("#selfEnrollModal").modal("hide");

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

                unenrolledUserTable.ajax.reload();
                usersTable.ajax.reload();

            }
          },
        'error': function(msg)
          {
            $( "#enrollSubmit" ).html("{{ trans('modules.enroll')}}");
            swal({
              title: 'Error occured!',
              text: '',
              type: 'error',
              timer: 2000,
              showConfirmButton: false,
            });
          }
        });
      }
      else{
          $.ajax({
            type: 'POST',
            url: "{{ route('courses.companies.members.enroll', ['course_id' => $data->course_id, 'company_id' => $data->company_id]) }}",
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'action':actionVar,
                'type': typeVar,
                'id': idVar,
                'start_date': start_date,
                'self_enroll': self_enroll
            },
            success: function(msg) {
              $( "#enrollSubmit" ).html("{{ trans('modules.enroll')}}");
              $("#selfEnrollModal").modal("hide");

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
              if(typeVar == 'user')
                {
                 if(actionVar == 'enroll')
                  unenrolledUserTable.ajax.reload();
                  usersTable.ajax.reload();
                }else{
                  if(actionVar == 'enroll')
                    unenrolledTeamTable.ajax.reload();
                  teamsTable.ajax.reload();
                  usersTable.ajax.reload();
                }
              }
              if(typeVar == "team" && actionVar == "enroll")
               $("#teamsModal").modal("show");
              if(typeVar == "user" && actionVar == "enroll")
               $("#usersModal").modal("show");
            }
          });
     }
    });

});
</script>
@endpush
