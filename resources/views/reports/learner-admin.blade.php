@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <div class="col-sm-6 float-left">
          <div class="float-left"><i class="fa fa-user-o fa-4x" aria-hidden="true" style="width:70px"></i>
          </div>
          <div>
            <div>{{$user->first_name}} {{$user->last_name}} <span class="badge badge-primary">{{$role?$role->role_name:trans('modules.unknown')}}</span></div>
            <div>{{$user->email}}</div>
          </div>
        </div>
        <div class="col-sm-6 float-right">
          <div class="btn-group float-right">
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
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!--/.col-->
  <div class="col-6 col-lg-3 pr-0" style="flex-basis:20%">
    <div class="card pr-0">
      <div class="card-body p-2 pr-0  d-flex align-items-center">
        <i class="fa fa-spinner bg-primary p-2 font-1xl mr-3"></i>
        <div>
          <div class="text-value-sm text-primary">{{$courseProgress}}</div>
          <div class="text-muted text-uppercase font-weight-bold small" style="font-size:65%">@lang('modules.course_in_progress')</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-6 col-lg-3 pr-0" style="flex-basis:20%">
    <div class="card">
      <div class="card-body p-2 pr-0  d-flex align-items-center">
        <i class="fa fa-window-close-o bg-primary p-2 font-1xl mr-3"></i>
        <div>
          <div class="text-value-sm text-primary">{{$courseNotPass}}</div>
          <div class="text-muted text-uppercase font-weight-bold small" style="font-size:65%">@lang('modules.course_not_complete')</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-6 col-lg-3 pr-0" style="flex-basis:20%">
    <div class="card">
      <div class="card-body p-2 pr-0  d-flex align-items-center">
        <i class="fa fa-check-square-o bg-primary p-2 font-1xl mr-3"></i>
        <div>
          <div class="text-value-sm text-primary">{{$completedCourse}}</div>
          <div class="text-muted text-uppercase font-weight-bold small" style="font-size:65%">@lang('modules.completed_courses')</div>
        </div>
      </div>
    </div>
  </div>


  <div class="col-6 col-lg-3 pr-0" style="flex-basis:20%">
    <div class="card">
      <div class="card-body p-2  pr-0 d-flex align-items-center">
        <i class="fa fa-hourglass-half bg-primary p-2 font-1xl mr-3"></i>
        <div>
          <div class="text-value-sm text-primary">{{$trainingTime}}</div>
          <div class="text-muted text-uppercase font-weight-bold small" style="font-size:65%">@lang('modules.training_time')</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-6 col-lg-3" style="flex-basis:20%">
    <div class="card">
      <div class="card-body p-2 pr-0  d-flex align-items-center">
        <i class="fa fa fa-certificate bg-primary p-2 font-1xl mr-3"></i>
        <div>
          <div class="text-value-sm text-primary">{{$certification}}</div>
          <div class="text-muted text-uppercase font-weight-bold small" style="font-size:65%">@lang('modules.certification')</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 text-center">
            <div class="text-muted pb-3">@lang('modules.last_login') : {{$last_login}} - @lang('modules.ip_address') : {{$user->last_login_ip}}</div>
            <div class="btn-group pb-4" role="group" aria-label="...">
              <button type="button" class="btn btn-default bar-btn" id="bar-btn" data-id="{{$user_id}}" data-type="today" data-filter="{{$filter}}">@lang("modules.today")</button>
              <button type="button" class="btn btn-default bar-btn" id="bar-btn" data-id="{{$user_id}}" data-type="yesterday" data-filter="{{$filter}}">@lang("modules.yesterday")</button>
              <button type="button" class="btn btn-default bar-btn" id="bar-btn" data-id="{{$user_id}}" data-type="week" data-filter="{{$filter}}">@lang("modules.week")</button>
              <button type="button" class="btn btn-default bar-btn" id="bar-btn" data-id="{{$user_id}}" data-type="month" data-filter="{{$filter}}">@lang("modules.month")</button>
              <button type="button" class="btn btn-default bar-btn" id="bar-btn" data-id="{{$user_id}}" data-type="year" data-filter="{{$filter}}">@lang("modules.year")</button>
              <button type="button" class="btn btn-default bar-btn" id="bar-btn" data-id="{{$user_id}}" data-type="period" data-filter="{{$filter}}">@lang("modules.period")</button>
            </div>
            <div style="height: 300px;">
              <canvas id="bar-chart" class="chartjs"></canvas>
            </div>
          </div>
          <div class="col-md-6 d-flex">
            <div style="height: 300px;" class="m-auto">
              <canvas id="doughnut-chart" class="chartjs" height=300></canvas>
            </div>
          </div>
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
@include('_plugins.datepicker')
@include('_plugins.chartjs')
@push('scripts')
<script src="{{ mix('scripts/reports/report.js') }}"></script>
@endpush
