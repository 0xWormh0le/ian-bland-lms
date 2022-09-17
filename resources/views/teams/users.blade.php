
<div class="col-sm-12">
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-sm-6">
          <i class="icon-people"></i> @lang('modules.users_of_team')
        </div>
        <div class="col-sm-6 text-right">
          <button type="button" id="enrollUser" class="btn btn-md btn-primary"><i class="icon-plus"></i> @lang('modules.enroll_user')</button>
        </div>
      </div>
    </div>
    <div class="card-body">
      <table class="table table-striped table-bordered datatable" id="usersTable">
        <thead>
            <tr>
                <th>@lang('modules.first_name')</th>
                <th>@lang('modules.last_name')</th>
                <th>@lang('modules.email')</th>
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
        <table width="100%" class="table table-bordered datatable" id="unenrolledTable">
          <thead>
            <tr>
              <th>@lang('modules.first_name')</th>
              <th>@lang('modules.last_name')</th>
              <th>@lang('modules.role')</th>
              <th></th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@endpush

@include('_plugins.datatables')
@push('scripts')
<script>
$(function() {
    $('.datatable').attr('style', 'border-collapse: collapse !important');
    var usersTable = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('teams.users', $data->id) !!}',
        columns: [
            { data: 'first_name', name: 'first_name', width:120 },
            { data: 'last_name', name: 'last_name', width:120 },
            { data: 'email', name: 'email' },
            { data: 'role_name', name: 'roles.role_name' },
            { data: 'action', name: 'action', sortable:false, class:'text-center', width:120 },
        ]
    });

    var unenrolledTable = $('#unenrolledTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('teams.users.unenrolled', $data->company_id) !!}',
        columns: [
            { data: 'first_name', name: 'first_name' },
            { data: 'last_name', name: 'last_name' },
            { data: 'role_name', name: 'roles.role_name' },
            { data: 'action', name: 'action', sortable:false, class:'text-center', width:80 },
        ],
    });

    $("#enrollUser").on("click", function(){
      unenrolledTable.ajax.reload();
      $("#usersModal").modal("show");
    });

    $(document).on('click', '.enrol', function (e) {
      e.preventDefault();
      var $this = $(this);
      var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> enrolling...';
      if ($(this).html() !== loadingText) {
        $this.data('original-text', $(this).html());
        $this.html(loadingText);
      }
      $.ajax({
        type: 'POST',
        url: "{{ route('teams.users.enroll', $data->id) }}",
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'action': $this.data('action'),
            'user_id': $this.data('id'),
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
            if($this.data('action') == 'enroll')
              unenrolledTable.ajax.reload();
            usersTable.ajax.reload();
          }
        }
      });
    });

});
</script>
@endpush
