<div class="col-sm-12">
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-sm-6">
          <i class="icon-layers"></i> @lang('modules.enrolled_companies')
        </div>
        <div class="col-sm-6 text-right">
          <button type="button" id="enrollcompany" class="btn btn-primary">
            <i class="icon-plus"></i> @lang('modules.enroll_company')
          </button>
        </div>
      </div>
    </div>
    <div class="card-body">
      <table class="table table-bordered datatable" id="companiesTable">
        <thead>
          <tr>
            <th>@lang('modules.company')</th>
            <th>@lang('modules.enrolled_date')</th>
            <th></th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

@push('modals')
<div id="companiesModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">@lang('modules.enroll_company')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table width="100%" class="table table-bordered datatable" id="unenrolledCompanies">
          <thead>
            <tr>
              <th>@lang('modules.company')</th>
              <th></th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    var companiesTable = $('#companiesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('courses.companies', $data->id) !!}',
        columns: [
            { data: 'company_name', name: 'companies.company_name' },
            { data: 'updated_at', name: 'updated_at', width: 120, class:'text-center', render: renderColumnDate },
            { data: 'action', name: 'action', sortable:false, class:'text-center', width: 150 },
        ],
    });

    var unenrolledCompanies = $('#unenrolledCompanies').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('courses.companies.unenrolled', $data->id) !!}',
        columns: [
            { data: 'company_name', name: 'companies.company_name' },
            { data: 'action', name: 'action', sortable:false, class:'text-center', width:80 },
        ],
    });

    $("#enrollcompany").on("click", function(){
      unenrolledCompanies.ajax.reload();
      $("#companiesModal").modal("show");
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
        url: "{{ route('courses.companies.enroll', $data->id) }}",
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'action': $this.data('action'),
            'id': $this.data('id'),
            'company_id': $this.data('id'),
            'course_id': "{{$data->id}}",
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
              unenrolledCompanies.ajax.reload();
            companiesTable.ajax.reload();
          }
        }
      });
    });
});
</script>
@endpush
