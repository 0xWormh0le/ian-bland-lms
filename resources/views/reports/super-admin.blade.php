@extends('layouts.app')

@section('content')

<form>
<div class="card">
<div class="card-header bg-white">
     <strong>@lang('modules.filter') @lang('modules.report_data'):</strong>
    </div>
<div class="card-body">
  <div class="row">
    <div class="col-md-4">
      <label for="select-team">@lang('modules.team')</label>
      <select name="team" id="select-team" class="form-control">
        <option value="none" @if ($filter_team == "none") selected @endif>None Selected</option>
      @foreach ($team as $t)
        <option value="{{ $t->id }}" @if ($filter_team == $t->id) selected @endif>{{ title_case($t->team_name) }}</option>
      @endforeach
      </select>
    </div>
    
    <div class="col-md-4">
      <label for="select-department">@lang('modules.department')</label>
      <select name="department" id="select-department" class="form-control">
        <option value="none" @if ($filter_department == "none") selected @endif>None Selected</option>
      @foreach ($department as $d)
        <option value="{{ $d }}" @if ($filter_department == $d) selected @endif>{{ title_case($d) }}</option>
      @endforeach
      </select>
    </div>

    <div class="col-md-4">
      <label for="select-overdue">@lang('modules.overdue')</label>
      <select id="select-overdue" class="form-control" name="overdue">
        <option value="none" @if ($filter_overdue == "none") selected @endif>None Selected</option>
        <option value="yes" @if ($filter_overdue == "yes") selected @endif>Yes</option>
        <option value="no" @if ($filter_overdue == "no") selected @endif>No</option>
      </select>
    </div>


  </div>
  <div class="row border-top mt-5">
           <div class="col-12  pt-3">
            <button class="btn btn-danger float-right">
              <i class="fa fa-play-circle mr-2"></i>@lang('modules.run_report')<span class="caret"></span>
            </button>
        </div>
        </div>
</div>
</div>
</form>

<div class="row">
  <!--/.col-->
  <div class="col-md-6 col-lg-3">
   <div class="card overflow-hidden">
       <div class="card-body p-0 d-flex align-items-center">
       <div class="bg-primary p-4 mr-3">
        <i class="fa fa-users font-2xl"></i>
           </div>
        <div>
          <div class="text-value text-primary">{{ $learners }}</div>
          <div class="text-muted text-uppercase font-weight-bold small" >@lang('modules.learners')</div>
        </div>
      </div>
    </div>
  </div>
   <div class="col-md-6 col-lg-3">
   <div class="card overflow-hidden">
       <div class="card-body p-0 d-flex align-items-center">
       <div class="bg-primary p-4 mr-3">
        <i class="fa fa-address-card-o font-2xl"></i>
           </div>
        <div>
          <div class="text-value text-primary">{{ $courseAssignment }}</div>
          <div class="text-muted text-uppercase font-weight-bold small" >@lang('modules.course_assignment')</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3" >
    <div class="card overflow-hidden">
      <div class="card-body p-0 d-flex align-items-center">
       <div class="bg-success p-4 mr-3">
        <i class="fa fa-check-square-o font-2xl"></i>
        </div>
        <div>
          <div class="text-value text-success">{{ round($completedCourse/$courseAssignment*100) }}%</div>
          <div class="text-muted text-uppercase font-weight-bold small" >@lang('modules.completed_courses')</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3" >
    <div class="card overflow-hidden">
      <div class="card-body p-0 d-flex align-items-center">
       <div class="bg-warning p-4 mr-3">
        <i class="fa fa-exclamation-triangle font-2xl"></i>
        </div>
        <div>
          <div class="text-value text-warning">{{ $overdue }}</div>
          <div class="text-muted text-uppercase font-weight-bold small" >@lang('modules.overdue')</div>
        </div>
      </div>
    </div>
  </div>

</div>

<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <span>
            <strong> @lang('modules.report_data')</strong>
        </span>
        <a href="{{ route('reports.user.data', [
            'filter' => $filter,
            'csv' => 'csv',
            'team' => $filter_team,
            'department' => $filter_department,
            'overdue' => $filter_overdue
            ]) }}" class="btn btn-success">
          <i class="fa fa-download"></i> Export Data <span class="caret"></span>
        </a>
      </div>
      <div class="card-body">
        <table class="table table-striped table-bordered datatable" id="datatable">
          <thead>
              <tr>
                  <th>@lang("modules.user")</th>
                  <th>@lang("modules.email")</th>
                  <th>@lang("modules.team")</th>
                  <th>@lang("modules.department")</th>
                  <th>@lang("modules.enrolled")</th>
                  <th>@lang("modules.completed")</th>
                  <th>@lang("modules.overdue")</th>
                  <th class="text-center">@lang("modules.action")</th>
              </tr>
          </thead>
          <tbody>
    @foreach ($users as $user)
              <tr>
                  <td>{{ $user['user'] }}</td>
                  <td>{{ $user['email'] }}</td>
                  <td>{{ $user['team'] }}</td>
                  <td>{{ $user['department'] }}</td>
                  <td>{{ $user['course_assigned'] }}</td>
                  <td>{{ $user['course_completed'] }}</td>
                  <td>{{ $user['overdue'] }}</td>
                  <td class="text-center">
                    <a href="{{ route('reports.learneradmin.index', [
                      encrypt($user['id']),
                      'none',
                      'none',
                      $filter
                    ]) }}" class="btn btn-sm" title="View User Details">
                    <i class="fa fa-user" aria-hidden="true"></i></a>
                  </td>
              </tr>
    @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@include('_plugins.datatables')
@push('scripts')
<script>
$(function() {
    $('.datatable').DataTable()
    $('.datatable').attr('style', 'border-collapse: collapse !important');
    return;

    $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{!! route('reports.user.data', $filter) !!}',
          data: {
            team: "'{{ $filter_team }}'",
            department: "'{{ $filter_department }}'",
            overdue: "'{{ $filter_overdue }}'"
          }
        },
        columns: [
            { data: 'user', name: 'user' },
            { data: 'email', name: 'email' },
            { data: 'team', name: 'team' },
            { data: 'department', name: 'department' },
            { data: 'course_assigned', name: 'course_assigned', class:'text-center' },
            { data: 'course_completed', name: 'course_completed', class:'text-center' },
            { data: 'overdue', name: 'overdue', class:'text-center' },
            { data: 'action', name: 'action', sortable: false, class: 'text-center' },

        ]
    });
    $('.datatable').attr('style', 'border-collapse: collapse !important');
});
</script>
@endpush
