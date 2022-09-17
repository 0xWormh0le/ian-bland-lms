@extends('layouts.app')

@section('title', $title)

@section('content')

<div class="row ">
  <div class="col-xl-4 col-lg-6  mb-4">
    <div class="card h-100">
      <div class="card-header">User Details
        <div style="float:right; margin:-6px; ">
          @isset($edit_button)
            {!! show_button('update', 'users.edit', encrypt($data->id)) !!}
            {!! show_button('remove', 'users.destroy', encrypt($data->id)) !!}
          @endif
        </div>
      </div>
      <div class="card-body pb-0">
        <table class="table table-responsiv e-sm mb-0">
          <tbody>
            <tr>
              <td class="bt-0" style="border-top:0;">@lang('modules.name')</td>
              <td style="border-top:0;"> {{$user->first_name}} {{$user->last_name}}</td>
            </tr>
            <tr>
              <td class="bt-0" >@lang('modules.role')</td>
              <td>
                <span class="badge badge-primary ">{{$role?$role->role_name:trans('modules.unknown')}}</span>
              </td>
            </tr>
            <tr>
              <td class="bt-0">@lang('modules.status')</td>
              <td>
                @if($data->active == "1")
                  <span class="badge badge-success "> @lang('modules.active')</span>
                @endif

                @if($data->active == "0")
                  <span class="badge badge-warning "> @lang('modules.inactive')</span>
                @endif
              </td>
            </tr>
            <tr>
              <td>Active Directory @lang('modules.user')</td>
              <td>{{$data->azure_id ? 'Yes' : 'No'}}</td>
            </tr>
            <tr>
              <td class="bt-0">@lang('modules.email')</td>
              <td>{{$data->email ?: '-'}}</td>
            </tr>
            <tr>
              <td>@lang('modules.company')</td>
              <td>{{$data->company_id ? $data->company->company_name: '-'}}</td>
            </tr>
            <tr>
              <td>@lang('modules.department')</td>
              <td>{{$data->department ?: '-'}}</td>
            </tr>
            <tr>
              <td>@lang('modules.team')</td>
              <td>{{ optional($data->team)->team_name ?? '-' }}</td>
            </tr>
            <tr>
              <td>@lang('modules.last_login') </td>
              <td>{{$last_login}}  @ {{$user->last_login_ip}}</td>
            </tr>

          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-xl-4 col-lg-6 mb-4">
    <div class="card h-100">
      <div class="card-header">
        Progress Overview
      </div>
      <div class="card-body p-2 pr-0 align-items-center">
        <div class="row">
          <div class="mx-auto mt-2" style="position: relative;">
            <canvas id="doughnut-chart" height="300"></canvas>
          </div>
        </div>

        <div class="brand-card-body" style="border-top:1px solid #c8ced3; width:100%;position:relative;bottom:-60px;">
          <div>
            <div class="text-value">{{$enrolledCount}}</div>
            <div class="text-uppercase text-muted small">@lang('modules.enrolled_courses')</div>
          </div>
          <div>
            <div class="text-value">{{$completedCourse}}</div>
            <div class="text-uppercase text-muted small">@lang('modules.completed_courses')</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-4 col-lg-12 mb-4">
    <div class="card h-100">
      <div class="card-header">
        Courses by Date
      </div>
      <div class="card-body">
        <div class="btn-group pb-4" role="group" aria-label="...">
          <button type="button" class="btn btn-default bar-btn" id="bar-btn" data-id="{{$user_id}}" data-type="week" data-filter="{{$filter}}">@lang("modules.week")</button>
          <button type="button" class="btn btn-default bar-btn" id="bar-btn" data-id="{{$user_id}}" data-type="month" data-filter="{{$filter}}">@lang("modules.month")</button>
          <button type="button" class="btn btn-default bar-btn" id="bar-btn" data-id="{{$user_id}}" data-type="year" data-filter="{{$filter}}">@lang("modules.year")</button>
          <button type="button" class="btn btn-default bar-btn" id="bar-btn" data-id="{{$user_id}}" data-type="period" data-filter="{{$filter}}">@lang("modules.period")</button>
        </div>
        <div style="height: 300px;">
          <canvas id="bar-chart" class="chartjs"></canvas>
        </div>

        <input type="hidden" id="total_pass_score" value="{{$totalPassScore}}" />
        <input type="hidden" id="remaining" value="{{$remaining}}" />
        <input type="hidden" id="filter" value="{{$filter}}" />
        <input type="hidden" id="login_count" value="{{$loginCount}}" />
        <input type="hidden" id="enroll_count" value="{{$enrolledCount}}" />
        <input type="hidden" id="course_completion_count" value="{{$courseCompletionCount}}" />
        
        @php
          $yl = implode(",",$yLabel);
        @endphp
        
        <input type="hidden" id="ylable" value='{{$yl}}' />
        <input type="hidden" id="statistics_url" value='{{route("reports.user.statistic.data")}}' />
        <input type="hidden" id="token" value='{{csrf_token()}}' />
      </div>
    </div>
  </div>
</div>

{{--
<div class="row" style="display:none;">
  <div class='col-sm-12'>
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-12">
            <i class="fa fa-chalkboard-teacher"></i>
              @lang("modules.user_courses_title")
          </div>
        </div>
      </div>

      <div class="card-body">
        <table class="table table-striped table-bordered datatable" id="datatable" data-url="{{ route('users.course.data', $data->id) }}">
          <input type="hidden" id="export_btn" value="{{trans('modules.export_user_courses_btn')}}" >
          <thead>
            <tr>
              <th>@lang("modules.course_title")</th>
              <th>@lang("modules.enrolled_date")</th>
              <th>@lang("modules.status")</th>
              <th>@lang("modules.completion_percentage")</th>
              <th>@lang("modules.completion_date")</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
--}}

<div class="row" >
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="fa fa-chalkboard-teacher"></i> @lang("modules.enrolled_courses")
        <div class="btn-group" style="float:right; margin-top:-5px; margin-bottom:-5px;">
          <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-download"></i> @lang("modules.export_in_csv") <span class="caret"></span>
          </button>
          <ul class="dropdown-menu p-1">
            <li class="p-1">
              <i class="fa fa-download pr-1"></i>
              <a href="{{route('reports.learneradmin.export', [$user_id, $filter])}}">@lang("modules.user_statistics")</a>
            </li>
            <li class="p-1">
              <i class="fa fa-download pr-1"></i>
              <a href="{{route('reports.learneradmin.export.course',[ $user_id, $filter])}}">@lang("modules.course_statistics")</a>
            </li>
          </ul>
        </div>
      </div>

      <div class="card-body">
      {{--
        <table class="table table-striped table-bordered datatable" id="datatable-course-detail" data-url="{!! route('reports.learner.course.data', [encrypt($data->id), 'active']) !!}">
          <thead>
            <tr>
              <th>@lang("modules.title")</th>
              <th>@lang("modules.enrolled_date")</th>
              <th>@lang("modules.complete_status")</th>
              <th>@lang("modules.score")</th>
              <th>@lang("modules.total_time")</th>
              <th>@lang("modules.completion_date")</th>
            </tr>
          </thead>
        </table>
      --}}
        <table class="table table-striped table-bordered datatable" id="datatable" data-url="{{ route('users.course.data', $data->id) }}">
          <thead>
            <tr>
              <th>@lang("modules.course_title")</th>
              <th>@lang("modules.enrolled_date")</th>
              <th>@lang("modules.status")</th>
              <th>@lang("modules.completion_percentage")</th>
              <th>@lang("modules.completion_status")</th>
              <th>@lang("modules.score")</th>
              <th>@lang("modules.total_time")</th>
              <th>@lang("modules.completion_date")</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  <div class="col-sm-12">
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
@include('_plugins.datepicker')
@include('_plugins.chartjs')

@push('scripts')
<script src="{{ mix('scripts/users/course_details.js') }}"></script>
<script src="{{ mix('scripts/reports/report.js') }}"></script>
@endpush
