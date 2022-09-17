@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">

    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-2">
            <select id="filter_courses" class="form-control multiple" data-label="Courses" data-url="{{route('api.get-modules-by-courses')}}">
              @foreach($courses as $r)
              <option value="{{$r->id}}">{{$r->title}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-sm-2">
            <select id="filter_teams" class="form-control multiple" data-label="Teams" multiple="multiple" data-url="{{route('api.get-users-by-teams')}}">
              @foreach($teams as $r)
              <option value="{{$r->id}}">{{$r->team_name}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-sm-2">
            <select id="filter_learners" class="form-control multiple" data-label="Learners" multiple="multiple">
            </select>
          </div>
          <div class="col-sm-2">
            <select id="filter_scores" class="form-control multiple" data-label="Scores" multiple="multiple">
              @foreach($scores as $id => $text)
              <option value="{{$id}}">{{$text}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-sm-4 text-right">
            <button type="button" id="generate" class="btn btn-danger btn-sm"><i class="fa fa-play-circle"></i> @lang('modules.run_report')</button>
          </div>
          <div class="col-sm-12 mt-3">
            <div class="dropdown">
              <button class="btn btn-dark btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-filter"></i> @lang('modules.add_criteria')
              </button>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item addcriteria" href="javascript:void(0)" data-type="enrolled_date">@lang('modules.enrolled_date')</a>
                <a class="dropdown-item addcriteria" href="javascript:void(0)" data-type="enrolled_between">@lang('modules.enrolled_between')</a>
                <a class="dropdown-item addcriteria" href="javascript:void(0)" data-type="completion">@lang('modules.completion') (%)</a>
                <a class="dropdown-item addcriteria" href="javascript:void(0)" data-type="completion_range">@lang('modules.completion') (%) @lang('modules.range')</a>
                <a class="dropdown-item addcriteria" href="javascript:void(0)" data-type="status">@lang('modules.status')</a>
                <a class="dropdown-item addcriteria" href="javascript:void(0)" data-type="module">@lang('modules.module')</a>
                <a class="dropdown-item addcriteria" href="javascript:void(0)" data-type="success_status">@lang('modules.success_status')</a>
                <a class="dropdown-item addcriteria" href="javascript:void(0)" data-type="completion_status">@lang('modules.completion_status')</a>
              </div>
            </div>
          </div>
        </div>
        <div id="criteria_lists">
          <div id="filterscore_between" class="row mt-3 score_filter" style="display:none;">
            <div class="col-sm-2">
              <label>@lang('modules.score_between')</label>
            </div>
            <div class="col-sm-2">
              <input type="number" class="form-control" id="score_between" max="100" min="0" step=".1">
            </div>
            <div class="col-sm-1 text-center">
              <label>@lang('modules.and')</label>
            </div>
            <div class="col-sm-2">
              <input type="number" class="form-control" id="score_between_to" max="100" min="0" step=".1">
            </div>
          </div>

          <div id="filterscore_lower_than" class="row mt-3 score_filter" style="display:none;">
            <div class="col-sm-2">
              <label>@lang('modules.score_lower_than')</label>
            </div>
            <div class="col-sm-2">
              <input type="number" class="form-control" id="score_lower_than" max="100" min="0" step=".1">
            </div>
          </div>

          <div id="filterscore_higher_than" class="row mt-3 score_filter" style="display:none;">
            <div class="col-sm-2">
              <label>@lang('modules.score_higher_than')</label>
            </div>
            <div class="col-sm-2">
              <input type="number" class="form-control" id="score_higher_than" max="100" min="0" step=".1">
            </div>
          </div>

          <div id="criteria_enrolled_date" class="row mt-3" style="display:none;">
            <div class="col-sm-2">
              <label>@lang('modules.enrolled_date')</label>
            </div>
            <div class="col-sm-2">
              <input type="text" class="form-control datepicker" id="enrolled_date">
            </div>
            <div class="col-sm-1 text-center">
            </div>
            <div class="col-sm-2">
            </div>
            <div class="col-sm-1">
              <button type="button" class="btn btn-sm btn-danger rm-criteria" title="@lang('modules.remove_criteria')"><i class="fa fa-close"></i></button>
            </div>
          </div>
          <div id="criteria_enrolled_between" class="row mt-3" style="display:none;">
            <div class="col-sm-2">
              <label>@lang('modules.enrolled_between')</label>
            </div>
            <div class="col-sm-2">
              <input type="text" class="form-control datepicker" id="enrolled_between">
            </div>
            <div class="col-sm-1 text-center" style="padding-top:4px;">
              <label>@lang('modules.and')</label>
            </div>
            <div class="col-sm-2">
              <input type="text" class="form-control datepicker" id="enrolled_between_to">
            </div>
            <div class="col-sm-1">
              <button type="button" class="btn btn-sm btn-danger rm-criteria" title="@lang('modules.remove_criteria')"><i class="fa fa-close"></i></button>
            </div>
          </div>
          <div id="criteria_completion" class="row mt-3" style="display:none;">
            <div class="col-sm-2">
              <label>@lang('modules.completion')</label>
            </div>
            <div class="col-sm-2">
              <input type="number" class="form-control" id="completion">
            </div>
            <div class="col-sm-1" style="padding-top:8px;">
              %
            </div>
            <div class="col-sm-2">
            </div>
            <div class="col-sm-1">
              <button type="button" class="btn btn-sm btn-danger rm-criteria" title="@lang('modules.remove_criteria')"><i class="fa fa-close"></i></button>
            </div>
          </div>
          <div id="criteria_completion_range" class="row mt-3" style="display:none;">
            <div class="col-sm-2">
              <label>@lang('modules.completion_range')</label>
            </div>
            <div class="col-sm-2">
              <input type="number" class="form-control" id="completion_range" placeholder="%">
            </div>
            <div class="col-sm-1 text-center" style="padding-top:4px;">
              <label>and</label>
            </div>
            <div class="col-sm-2">
              <input type="number" class="form-control" id="completion_range_to" placeholder="%">
            </div>
            <div class="col-sm-1">
              <button type="button" class="btn btn-sm btn-danger rm-criteria" title="@lang('modules.remove_criteria')"><i class="fa fa-close"></i></button>
            </div>
          </div>
          <div id="criteria_status" class="row mt-3" style="display:none;">
            <div class="col-sm-2">
              <label>@lang('modules.status')</label>
            </div>
            <div class="col-sm-2">
              <select class="form-control multiselect" id="status" multiple="multiple">
                <option value="complete">@lang('modules.complete')</option>
                <option value="incomplete">@lang('modules.incomplete')</option>
                <option value="not_started">@lang('modules.not_started')</option>
              </select>
            </div>
            <div class="col-sm-1" style="padding-top:8px;">
            </div>
            <div class="col-sm-2">
            </div>
            <div class="col-sm-1">
              <button type="button" class="btn btn-sm btn-danger rm-criteria" title="@lang('modules.remove_criteria')"><i class="fa fa-close"></i></button>
            </div>
          </div>
          <div id="criteria_module" class="row mt-3" style="display:none;">
            <div class="col-sm-2">
              <label>@lang('modules.module')</label>
            </div>
            <div class="col-sm-2">
              <select class="form-control multiselect" id="module" multiple="multiple">
              </select>
            </div>
            <div class="col-sm-1" style="padding-top:8px;">
            </div>
            <div class="col-sm-2">
            </div>
            <div class="col-sm-1">
              <button type="button" class="btn btn-sm btn-danger rm-criteria" title="@lang('modules.remove_criteria')"><i class="fa fa-close"></i></button>
            </div>
          </div>
          <div id="criteria_success_status" class="row mt-3" style="display:none;">
            <div class="col-sm-2">
              <label>@lang('modules.success_status')</label>
            </div>
            <div class="col-sm-2">
              <select class="form-control multiselect" id="success_status" multiple="multiple">
                <option value="pass">@lang('modules.pass')</option>
                <option value="complete">@lang('modules.complete')</option>
                <option value="failed">@lang('modules.failed')</option>
                <option value="incomplete">@lang('modules.incomplete')</option>
              </select>
            </div>
            <div class="col-sm-1" style="padding-top:8px;">
            </div>
            <div class="col-sm-2">
            </div>
            <div class="col-sm-1">
              <button type="button" class="btn btn-sm btn-danger rm-criteria" title="@lang('modules.remove_criteria')"><i class="fa fa-close"></i></button>
            </div>
          </div>
          <div id="criteria_completion_status" class="row mt-3" style="display:none;">
            <div class="col-sm-2">
              <label>@lang('modules.status')</label>
            </div>
            <div class="col-sm-2">
              <select class="form-control multiselect" id="completion_status" multiple="multiple">
                <option value="pass">@lang('modules.pass')</option>
                <option value="complete">@lang('modules.complete')</option>
                <option value="failed">@lang('modules.failed')</option>
                <option value="incomplete">@lang('modules.incomplete')</option>
              </select>
            </div>
            <div class="col-sm-1" style="padding-top:8px;">
            </div>
            <div class="col-sm-2">
            </div>
            <div class="col-sm-1">
              <button type="button" class="btn btn-sm btn-danger rm-criteria" title="@lang('modules.remove_criteria')"><i class="fa fa-close"></i></button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card" style="margin-bottom:0">
      <div class="card-header">
        <a href="#" id="expandChart" class="card-header-action btn-minimize" data-toggle="collapse" data-target="#chartProperties" aria-expanded="true">
          <i class="fa fa-chart-bar"></i> @lang('modules.chart')
        </a>
        <div class="card-header-actions">
          <label id="switchChart" class="switch switch-label switch-pill switch-primary" style="margin-bottom:0">
            <input type="checkbox" class="switch-input" id="active_chart" checked>
            <span class="switch-slider" data-checked="On" data-unchecked="Off"></span>
          </label>
        </div>
      </div>
      <div class="card-body collapse" id="chartProperties">
        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.chart_type') : </label>
          <div class="col-sm-9">
            <select name="chart_type" id="chart_type" class="form-control select2icon" required>
            @foreach($chartTypes as $type)
              <option value="{{str_slug($type)}}" data-image="{{asset('vendors/highchartsjs/example/'.str_slug($type).'.svg')}}">{{$type}}</option>
            @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.chart_title') : </label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="chart_title" id="chart_title" value="@lang('modules.chart_title')">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.chart_subtitle') : </label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="chart_subtitle" id="chart_subtitle" value=">@lang('modules.chart_subtitle')">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.group_by') : </label>
          <div class="col-sm-8">
            <select id="chart_series" name="chart_series[]" class="form-control select2">
              @foreach($dataOptions as $key => $value)
              <option value="{{$key}}">{{$value}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-sm-1">
            <button type="button" class="btn btn-light clearFilter" title="@lang('modules.clear_filter')"><i class="fa fa-undo"></i></button>
          </div>
        </div>

        <!-- <div class="form-group row">
          <label class="col-sm-3">Categories : </label>
          <div class="col-sm-8">
            <select id="chart_categories" name="chart_categories[]" class="form-control select2">
              <option value="">No Categories</option>
            </select>
          </div>
          <div class="col-sm-1">
            <button type="button" class="btn btn-light clearFilter" title="Clear Filter"><i class="fa fa-undo"></i></button>
          </div>
        </div> -->

      </div>
    </div>

    <div class="card" style="margin-bottom:0">
      <div class="card-header" role="tab" id="headingColumns">
        <a href="#" id="expandTable" class="card-header-action btn-minimize" data-toggle="collapse" data-target="#tableProperties" aria-expanded="true">
          <i class="fa fa-table "></i> @lang('modules.table')
        </a>
        <div class="card-header-actions">
          <label id="switchTable" class="switch switch-label switch-pill switch-primary" style="margin-bottom:0">
            <input type="checkbox" class="switch-input" id="active_table" checked>
            <span class="switch-slider" data-checked="On" data-unchecked="Off"></span>
          </label>
        </div>
      </div>
      <div class="card-body collapse" id="tableProperties">
        <div class="form-group row">
          <label class="col-sm-12">@lang('modules.add_columns') : </label>
          <div class="col-sm-8">
            <select class="form-control select2" name="columns" id="columns">
              <option value="course">@lang('modules.course_title')</option>
              <option value="team">@lang('modules.team_name')</option>
              <option value="learner">@lang('modules.learner_name')</option>
              <option value="module">@lang('modules.module')</option>
              <option value="enrolled">@lang('modules.enrolled_date')</option>
              <option value="completed">@lang('modules.completed_date')</option>
              <option value="completion">@lang('modules.completion') %</option>
              <option value="score">@lang('modules.score')</option>
              <option value="status">@lang('modules.status')</option>
            </select>
          </div>
          <div class="col-sm-1">
            <button type="button" id="addColumn" class="btn btn-light"><i class="fa fa-plus"></i></button>
          </div>
          <div class="col-sm-3">
            <button type="button" id="clearColumn" class="btn btn-light btn-block"><i class="fa fa-times"></i> @lang('modules.remove_all_columns')</button>
          </div>
        </div>

        <ul id="columnList" class="list-group sortable" data-default="{{implode(';', $defaultColumns)}}">

        </ul>

      </div>
    </div>


    <div class="card">
      <a class="toggleclass" data-toggle="collapse" href="#collapseReport" aria-expanded="true" aria-controls="collapseReport">
        <div class="card-header" role="tab" id="headingReport">
          <i class="fa fa-receipt"></i> @lang('modules.results')
        </div>
      </a>
      <div id="collapseReport" class="collapse show" role="tabpanel" aria-labelledby="headingReport" data-parent="#accordion">
        <div class="card-body" id="result" data-url="{{ route('reports.generate') }}">
        </div>
      </div>
    </div>

@endsection


@include('_plugins.bootstrap-multiselect')
@include('_plugins.select2')
@include('_plugins.datepicker')
@include('_plugins.datatables')

@push('scripts')
<script src="{{ asset('vendors/highchartsjs/highcharts.js') }}"></script>
<script src="{{ asset('vendors/highchartsjs/highcharts-3d.js') }}"></script>
<script src="{{ asset('vendors/highchartsjs/modules/exporting.js') }}"></script>
<script src="{{ asset('vendors/highchartsjs/modules/export-data.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ mix('scripts/reports/index.js') }}"></script>
@endpush
