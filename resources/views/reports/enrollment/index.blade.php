@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">

    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <i class="fa fa-id-card"></i> {{ $title }}
          </div>
          <div class="col-sm-6 text-right">
            <button type="button" id="generate" class="btn btn-primary btn-sm"><i class="fa fa-play-circle"></i> @lang('modules.run_report')</button>
          </div>
        </div>
      </div>
    </div>


    <div id="accordion" role="tablist">

      <div class="card" style="margin-bottom:0">
        <a class="toggleclass collapsed" data-toggle="collapse" href="#collapseColumns" aria-expanded="true" aria-controls="collapseColumns">
          <div class="card-header" role="tab" id="headingColumns">
            <i class="fa fa-align-justify "></i> @lang('modules.columns')
          </div>
        </a>
        <div id="collapseColumns" class="collapse" role="tabpanel" aria-labelledby="headingColumns" data-parent="#accordion">
          <div class="card-body">
            <p class="text-muted"><i class="fa fa-info-circle"></i> @lang('modules.selectd_columns_text').</p>

            <div class="form-group row">
              <label class="col-sm-12">@lang('modules.add_columns') : </label>
              <div class="col-sm-8">
                <select class="form-control select2" name="columns" id="columns">
                  <option value="">@lang('modules.select_a_column')...</option>
                  <option value="learner">@lang('modules.learner_name')</option>
                  <option value="team">@lang('modules.team')</option>
                  <option value="course">@lang('modules.course')</option>
                  <option value="enrolled">@lang('modules.enrolled_date')</option>
                  <option value="percentage">@lang('modules.completion') %</option>
                  <option value="completion">@lang('modules.status')</option>
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
      </div>

       <div class="card" style="margin-bottom:0">
        <a class="toggleclass collapsed" data-toggle="collapse" href="#collapseFilter" aria-expanded="true" aria-controls="collapseFilter">
          <div class="card-header" role="tab" id="headingFilter">
            <i class="fa fa-filter"></i> @lang('modules.filters')
          </div>
        </a>
        <div id="collapseFilter" class="collapse" role="tabpanel" aria-labelledby="headingFilter" data-parent="#accordion">
          <div class="card-body">
            <div class="row">

              <div class="col-sm-6" style="margin-bottom:20px;">
                <div class="row">
                  <label class="col-sm-12">@lang('modules.filter_by_learner') :</label>
                  <div class="col-sm-10">
                    <select id="filter_learner" name="filter_learner[]" class="form-control select2" multiple="multiple">
                      @foreach($learners as $learner)
                      <option value="{{$learner->id}}">{{$learner->first_name}} {{$learner->last_name}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-sm-2">
                    <button type="button" class="btn btn-light clearFilter" title="@lang('modules.clear_filter')"><i class="fa fa-undo"></i></button>
                  </div>
                </div>
              </div>

              <div class="col-sm-6" style="margin-bottom:20px;">
                <div class="row">
                  <label class="col-sm-12">@lang('modules.filter_by_team') :</label>
                  <div class="col-sm-10">
                    <select id="filter_team" name="filter_team[]" class="form-control select2" multiple="multiple">
                      @foreach($teams as $team)
                      <option value="{{$team->id}}">{{$team->team_name}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-sm-2">
                    <button type="button" class="btn btn-light clearFilter" title="@lang('modules.clear_filter')"><i class="fa fa-undo"></i></button>
                  </div>
                </div>
              </div>

              <div class="col-sm-6" style="margin-bottom:20px;">
                <div class="row">
                  <label class="col-sm-12">@lang('modules.filter_by_enrolled_date') :</label>
                  <label class="col-sm-2">@lang('modules.from') :</label>
                  <div class="col-sm-3">
                    <input type="text" id="filter_enrolledfrom" name="filter_enrolledfrom" class="form-control datepicker">
                  </div>
                  <label class="col-sm-2">@lang('modules.to') :</label>
                  <div class="col-sm-3">
                    <input type="text" id="filter_enrolledto" name="filter_enrolledto" class="form-control datepicker">
                  </div>
                  <div class="col-sm-2">
                    <button type="button" class="btn btn-light clearFilter" title="@lang('modules.clear_filter')"><i class="fa fa-undo"></i></button>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
      <!--
      <div class="card" style="margin-bottom:0">
        <a class="toggleclass collapsed" data-toggle="collapse" href="#collapseGroups" aria-expanded="true" aria-controls="collapseGroups">
          <div class="card-header" role="tab" id="headingGroups">
            <i class="fas fa-object-group"></i> Group By
          </div>
        </a>
        <div id="collapseGroups" class="collapse" role="tabpanel" aria-labelledby="headingGroups" data-parent="#accordion">
          <div class="card-body">

          </div>
        </div>
      </div> -->

      <div class="card">
        <a class="toggleclass" data-toggle="collapse" href="#collapseReport" aria-expanded="true" aria-controls="collapseReport">
          <div class="card-header" role="tab" id="headingReport">
            <i class="fa fa-receipt"></i> @lang('modules.results')
          </div>
        </a>
        <div id="collapseReport" class="collapse show" role="tabpanel" aria-labelledby="headingReport" data-parent="#accordion">
          <div class="card-body" id="result" data-url="{{ route('reports.enrollment.generate') }}">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



@endsection

@include('_plugins.select2')
@include('_plugins.datepicker')
@include('_plugins.datatables')
@include('_plugins.chartjs')


@push('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!-- <script src="{{ mix('scripts/teams/index.js') }}"></script> -->
<script>
$(document).ready(function () {
  $(".sortable").sortable();
  $(".select2").select2({ width: '100%' });

  $("#addColumn").on('click', function(){
    var col = $("#columns").val(),
        label = $("#columns option:selected").text();

    if($("#columns").val() !== '')
    {
      exists = false;
      $('input[name="columns[]"]').each(function(){
          if(exists == false && $(this).val() == col)
            exists = true;
      })

      if(exists == false)
        addColumn(col, label);
    }
  })

  $("#clearColumn").on('click', function(){
    $("#columnList").html("");
  });

  $('body').on('click', '.removeColumn', function() {
    $(this).closest('li').remove();
  });

  $('body').on('click', '.clearFilter', function() {
    parent = $(this).closest('.row');
    parent.find('select').val("").trigger("change");
    parent.find('input').val("");
  });

  function addColumn(col, label)
  {
    $("#columnList").append(`
        <li class="list-group-item d-flex list-group-item-action justify-content-between align-items-center">
          <span>
            <i class="fa fa-arrows-alt-v"></i> `+label+`
            <input type="hidden" name="columns[]" class="column_name" value="`+col+`">
          </span>
          <button type="button" class="btn btn-sm btn-light removeColumn"><i class="fa fa-minus"></i></button>
        </li>
    `);
  }

  setByDefault();

  function setByDefault()
  {
    $("#columnList").html("");
    var defaultColumns = $("#columnList").data("default").split(";");
    $.each(defaultColumns , function(index,   val) {
      $("#columns > option").each(function() {
          if(this.value == val)
            addColumn(val, this.text);
      });
    });

    generateReport();
  }

  function generateReport()
  {
    if(!$('#collapseReport').hasClass('show'))
      $('#collapseReport').collapse('toggle');


    $("#result").html('<div class="progress"><div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">Processing...</div></div>');

    var columns = [];
    $('input[name="columns[]"]').each(function(){
        columns.push( $(this).val() );
    })

    if(columns.length === 0)
      setByDefault();
    else{


      $.ajax({
        type: 'POST',
        url: $("#result").data('url'),
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'columns': JSON.stringify(columns),
            'filter_learner': $("#filter_learner").val(),
            'filter_team': $("#filter_team").val(),
            'filter_enrolledfrom': $("#filter_enrolledfrom").val(),
            'filter_enrolledto': $("#filter_enrolledto").val(),
        },
        success: function(result) {
          $("#result").html(result);
        }
      });
    }

  }

  $("#generate").on("click", function(){
    generateReport();
  });

});
</script>
@endpush
