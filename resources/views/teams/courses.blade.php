
<div class="col-sm-12">
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-sm-6">
          <i class="icon-people"></i> @lang('modules.team_courses')
        </div>
      </div>
    </div>
    <div class="card-body">
      <table class="table table-striped table-bordered datatable" id="coursesTable">
        <thead>
            <tr>
                <th>@lang('modules.course_title')</th>
                <th>@lang('modules.enrolled_by')</th>
            </tr>
        </thead>
      </table>
    </div>
  </div>
</div>



@include('_plugins.datatables')
@push('scripts')
<script>
$(function() {
    $('.datatable').attr('style', 'border-collapse: collapse !important');
    var usersTable = $('#coursesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{!! route('teams.courses', $data->id) !!}",
        columns: [
            { data: 'title', name: 'courses.title' },
            { data: 'enrolled_by', name: 'users.first_name' },
          ]
    });

});
</script>
@endpush
