@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-sm-12">
  <div class="card">
    <div class="card-header bg-white">
     <strong>@lang('modules.filter') @lang('modules.report_data'):</strong> {{ $title }}
    </div>
    <div class="card-body">

      <form>
        <div class="row">
          <div class="col-sm-6 col-md-3 mt-3">
            <label for="select-status">@lang('modules.status')</label>
            <select id="select-status" class="form-control" name="status">
              <option value="none" @if ($filter_status == "none") selected @endif>None Selected</option>
              <option value="complete" @if ($filter_status == "complete") selected @endif>Complete</option>
              <option value="incomplete" @if ($filter_status == "incomplete") selected @endif>Incomplete</option>
            </select>
          </div>
          <div class="col-sm-6 col-md-3 mt-3">
            <label for="select-team">@lang('modules.team')</label>
            <select id="select-team" class="form-control" name="team">
              <option value="none" @if ($filter_team == "none") selected @endif>None Selected</option>
          @foreach ($team as $t)
              <option value="{{ $t->id }}" @if ($filter_team == $t->id) selected @endif>{{ title_case($t->team_name) }}</option>
          @endforeach
            </select>
          </div>
          <div class="col-sm-6 col-md-3 mt-3">
            <label for="select-department">@lang('modules.department')</label>
            <select id="select-department" class="form-control" name="department">
              <option value="none" @if ($filter_department == "none") selected @endif>None Selected</option>
            @foreach ($department as $d)
              <option value="{{ $d }}" @if ($filter_department == $d) selected @endif>{{ title_case($d) }}</option>
            @endforeach
            </select>
          </div>
          <div class="col-sm-6 col-md-3 mt-3">
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
      </form>
    </div>
  </div>
</div>
</div>
<div class="row">
  <!--/.col-->
  <div class="col-md-6 col-lg-3" >
     <div class="card overflow-hidden">
       <div class="card-body p-0 d-flex align-items-center">
       <div class="bg-primary p-4 mr-3">
        <i class="fa fa-users font-2xl"></i>
           </div>
        <div>
          <div class="text-value text-primary">{{ $totalUserEnroll }}</div>
          <div class="text-muted text-uppercase font-weight-bold small" >@lang('modules.users_enrolled')</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3" >
    <div class="card overflow-hidden">
      <div class="card-body p-0 d-flex align-items-center">
       <div class="bg-primary p-4 mr-3">
        <i class="fa fa-spinner font-2xl"></i>
        </div>
        <div>
          <div class="text-value text-primary">{{ $courseProgress }}</div>
          <div class="text-muted text-uppercase font-weight-bold small" >@lang('modules.user_in_progress')</div>
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
          <div class="text-value text-success">{{ $completedCourse }}</div>
          <div class="text-muted text-uppercase font-weight-bold small" >@lang('modules.user_completed')</div>
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

<input type="hidden" id="course_id" value="{{ $encCourseId }}">
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
          <span>
            <strong> @lang('modules.report_data')</strong>
          </span>
          <a href="{{ route('reports.course.users.data', [
            'id' => $encCourseId,
            'option' => $filter,
            'csv' => 'csv',
            'status' => $filter_status,
            'team' => $filter_team,
            'department' => $filter_department,
            'overdue' => $filter_overdue
            ]) }}" class="btn btn-success">
            <i class="fa fa-download"></i> Export Data <span class="caret"></span>
          </a>
        </div>
      </div>
      <div class="card-body">
          <table class="table table-striped table-bordered datatable" id="datatable">
          <thead>
              <tr>
                  <th>@lang("modules.first_name")</th>
                  <th>@lang("modules.last_name")</th>
                  <th>@lang("modules.enrolled_date")</th>
                  <th>@lang("modules.completion_status")</th>
                  <th>@lang("modules.status")</th>
                  <th>@lang("modules.score")</th>
                  <th>@lang("modules.total_time")</th>
                  <th>@lang("modules.completion_date")</th>
                  <th>@lang("modules.details")</th>
              </tr>
          </thead>

          <tbody>
    @foreach ($courseUsers as $cu)
              <tr>
                  <td>{{ $cu['first_name'] }}</td>
                  <td>{{ $cu['last_name'] }}</td>
                  <td>{{ $cu['enrolled_date'] }}</td>
                  <td>{{ $cu['complete_status'] }}</td>
                  <td>{{ $cu['satisfied_status'] }}</td>
                  <td>{{ $cu['score'] }}</td>
                  <td>{{ $cu['total_time'] }}</td>
                  <td>{{ $cu['completion_date'] }}</td>
                  <td>
                    <a href="{{ route('users.show', ['id' => encrypt($cu['user_id'])]) }}" class="btn btn-sm" title="view user details">
                      <i class="fa fa-user" aria-hidden="true"></i>
                    </a>
                  </td>
              </tr>
    @endforeach
          </tbody>

        </table>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-sm-12">
    <div class="card">
     <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <i class="fa fa-chalkboard-teacher"></i> @lang("js.users_enroll_statistics")
          </div>
        </div>
      </div>
      <div class="card-body">
        <div style="max-height:300px; width:100%">
          <canvas id="line-chart" class="chartjs" style="max-height:300px; width:100%"></canvas>
        </div>

       <input type="hidden" id="total_pass_score" value="{{$totalPassScore}}" />
       <input type="hidden" id="remaining" value="{{$remaining}}" />
       @php
              $monthData = implode(",",$monthData);
              $monthLabel = implode(",",$monthLabel);
       @endphp
       <input type="hidden" id="month_data" value="{{$monthData}}" />
        <input type="hidden" id="month_label" value="{{$monthLabel}}" />
      @php
             $yl = implode(",",$yLabel);
      @endphp
      <input type="hidden" id="ylable" value='{{$yl}}' />
      <input type="hidden" id="statistics_url" value='{{route("reports.user.statistic.data")}}' />
      <input type="hidden" id="token" value='{{csrf_token()}}' />
       <input type="hidden" id="filter" value="{{$filter}}" />
       <input type="hidden" id="total_users" value="{{$totalUserEnroll}}" />
       <input type="hidden" id="process_course" value="{{$courseProgress}}" />
       <input type="hidden" id="completed_course" value="{{$completedCourse}}" />

      </div>
    </div>
  </div>
</div>
@endsection

@push('modals')
<div id="periodModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">@lang('modules.report_statistic_period')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="form-group row">
            <label class="col-md-3 col-form-label" for="from date">@lang('modules.from_date')</label>
            <div class="col-md-6">
              <input type="text" class="form-control datepicker"  id="from_date" value="">
             </div>
          </div>
          <div class="form-group row">
            <label class="col-md-3 col-form-label" for="to date">@lang('modules.to_date')</label>
            <div class="col-md-6">
              <input type="text" class="form-control datepicker"  id="to_date" value="">
             </div>
          </div>
          <div class="form-group row">
            <label class="col-md-3 col-form-label" for="day range">@lang('modules.day_range')</label>
            <div class="col-md-6">
              <input type="number" class="form-control"  id="day_range" value="" placeholder="Enter a number">
              <span></span>
             </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="periodSubmit" class="btn btn-md btn-primary" ><i class="icon-plus"></i> @lang('modules.submit')</button>
      </div>
    </div>
  </div>
</div>
@endpush
@include('_plugins.datatables')
@include('_plugins.chartjs')
@push('scripts')
<script src="{{ mix('scripts/reports/report.js') }}"></script>
<script>
$(function() {
    $('#datatable').DataTable()
    $('.datatable').attr('style', 'border-collapse: collapse !important');
    // Both works and you can use ajax table as well
    return;


    var id = $("#user_id").val();

    $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: "{{ route('reports.course.users.data', [ 'id' => $encCourseId, 'filter' => $filter ]) }}",
          data: {
            status: "{{ $filter_status }}",
            team: "{{ $filter_team }}",
            department: "{{ $filter_department }}",
            overdue: "{{ $filter_overdue }}"
          }
        },
        columns: [
            { data: 'first_name', name: 'first_name' },
            { data: 'last_name', name: 'last_name' },
            { data: 'enrolled_date', name: 'enrolled_date' },
            { data: 'complete_status', name: 'complete_status' },
            { data: 'satisfied_status', name: 'satisfied_status' },
            { data: 'score', name: 'score' },
            { data: 'total_time', name: 'total_time' },
            { data: 'completion_date', name: 'completion_date' },
            { data: 'action', name: 'action', sortable: false, class: 'text-center' },
        ]
    });
    $('.datatable').attr('style', 'border-collapse: collapse !important');
});
</script>
@endpush
