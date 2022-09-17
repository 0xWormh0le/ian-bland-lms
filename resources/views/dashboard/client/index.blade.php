@extends('layouts.app')

@section('content')

@include('dashboard.client.count')

<div class="row">
@if (validate_role('reports.superadmin.index'))
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <span>@lang('controllers.course_reports')</span>
        <a class="btn btn-primary btn-sm" href="{{ route('reports.course') }}">@lang('modules.view_all')</a>
      </div>
      <div class="card-body">
        <table class="table table-striped table-bordered datatable" id="course-report">
          <thead>
            <tr>
              <th>@lang("modules.course")</th>
              <th>@lang("modules.category")</th>
              <th>@lang("modules.enrolled")</th>
              <th>@lang("modules.complete_percentage")</th>
              <th>@lang("modules.incomplete_percentage")</th>
              <th>@lang("modules.details")</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
@endif

@if (validate_role('reports.superadmin.index'))
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <span>@lang('controllers.user_reports')</span>
        <a class="btn btn-primary btn-sm" href="{{ route('reports.superadmin.index') }}">@lang('modules.view_all')</a>
      </div>
      <div class="card-body">
        <table class="table table-striped table-bordered datatable" id="user-report">
          <thead>
            <tr>
              <th>@lang("modules.user")</th>
              <th>@lang("modules.user_type")</th>
              <th>@lang("modules.last_login")</th>
              <th>@lang("modules.assigned_courses")</th>
              <th>@lang("modules.completed_courses")</th>
              <th>@lang("modules.details")</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
@endif

  <div class="col-sm-12 d-none">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-sm-5">
            <h4 class="card-title mb-0">@lang('modules.course_result')</h4>
          </div>
        </div>
        <div class="chart-wrapper" style="height:300px;margin-top:40px;">
          <canvas id="course-chart" class="chart" height="300" data-label="{{ implode(';', $courses) }}" data-completed="{{ implode(';', $courseCompleted) }}" data-incomplete="{{ implode(';', $courseIncomplete) }}" data-completednumber="{{ implode(';', $courseCompletedNumber) }}" data-incompletenumber="{{ implode(';', $courseIncompleteNumber) }}"></canvas>
        </div>
      </div>
      <div class="card-footer">
        <div class="row text-center">
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-12 d-none">
    <div class="card">
      <div class="card-header">
        <i class="far fa-star"></i> @lang('modules.team_results')
      </div>
      <div class="card-body">
        <table class="table table-responsive-sm table-hover table-outline mb-0 datatable" id="teamtable">
          <thead class="thead-light">
            <tr>
              <th>@lang('modules.team')</th>
              <th class="text-center" width="200">@lang('modules.average_score')</th>
              <th class="text-center" width="300">@lang('modules.completed_courses_by_team_member')</th>
            </tr>
          </thead>
          <tbody>
            @foreach($teams as $team)
            <tr>
              <td>
                <a href="{{route('dashboard.team-results', $team->slug)}}">{{$team->team_name}}</a>
              </td>
              <td class="text-center">
              @php
                $avg = \App\CourseResult::select(\DB::raw('avg(score) as avg_score'))
                                  ->leftJoin('course_users', 'course_users.id', '=', 'courseuser_id')
                                  ->leftJoin('users', 'users.id', '=', 'course_users.user_id')
                                  ->where('users.team_id', $team->id)
                                  ->first();
              @endphp
                {{number_format(@$avg->avg_score ?: 0, 2)}}
              </td>
              <td>
                @php
                   $teamUser= \App\CourseUser::join('courses', 'courses.id', '=', 'course_users.course_id')
                                ->join('users', 'users.id', '=', 'course_users.user_id')
                                ->where('users.team_id', $team->id)
                                ->get();
                  $completed = 0 ;
                  $taken = 0 ;
                  for($t=0;$t < count($teamUser); $t++)
                  {
                    $taken++;
                    $courseComResult = course_completion_rules_result($teamUser[$t]->course_id, $teamUser[$t]->user_id);

                    if($courseComResult['complete'] == 1){
                      $completed++;
                    }
                  }

                  if($taken > 0)
                    $percent = round(($completed/$taken)*100, 2);
                  else
                    $percent = 0;
                @endphp

                <div class="clearfix">
                  <div class="float-left">
                    <strong>{{$percent}}%</strong>
                  </div>
                  <div class="float-right">
                    <small class="text-muted">{{$completed}} @lang('modules.completed_of') {{$taken}}</small>
                  </div>
                </div>
                <div class="progress progress-xs">
                  <div class="progress-bar bg-success" role="progressbar" style="width: {{$percent}}%" aria-valuenow="{{$percent}}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
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



@include('_plugins.chartjs')
@push('scripts')
<script>
  $(function () {
    var table = $('#teamtable').DataTable({
      order: [[2, 'desc']]
    });
    $('.datatable').attr('style', 'border-collapse: collapse !important');
  });
</script>
<script>
  $(function() {
    $('#user-report').DataTable({
        lengthChange: false,
        displayLength: 5,
        processing: true,
        serverSide: true,
        ajax: "{!! route('reports.user.data', 'active') !!}",
        columns: [
            { data: 'user', name: 'user' },
            { data: 'role_name', name: 'role_name' },
            { data: 'last_login_at', name: 'last_login_at', render: renderColumnDate },
            { data: 'course_assigned', name: 'course_assigned', class:'text-center' },
            { data: 'course_completed', name: 'course_completed', class:'text-center' },
            { data: 'action', name: 'action', sortable: false, class: 'text-center', width: 220 },
        ]
    });

    $('#course-report').DataTable({
        lengthChange: false,
        displayLength: 5,
        processing: true,
        serverSide: true,
        ajax: "{!! route('reports.course.data', 'active') !!}",
        order: [[2, 'desc']],
        columns: [
          { data: 'title', name: 'title' },
          { data: 'category', name: 'category' },
          { data: 'enrolled', name: 'enrolled' },
          { data: 'complete', name: 'complete' },
          { data: 'incomplete', name: 'incomplete' },
          { data: 'action', name: 'action', sortable: false, class: 'text-center', width: 100 },
        ]
    });

    $('.datatable').attr('style', 'border-collapse: collapse !important');
});

</script>

<script src="{{ mix('scripts/dashboard/client.js') }}"></script>
@endpush
