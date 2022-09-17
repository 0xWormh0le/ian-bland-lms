@extends('layouts.app')

@section('content')
<div class="card">
<div class="card-body pt-3">
  <form>
  <div class="row">
    <div class="col-12 col-sm-4 col-md-3 col-xl-2">
      <label class="col-form-label" for="select-active">@lang('modules.filter')</label>
      <select name="filter" class="form-control" id="select-active">
        <option value="active"  {{ $filter == 'active' ? 'selected' : '' }} >Active</option>
        <option value="archive" {{ $filter == 'archive' ? 'selected' : '' }}>Archive</option>
      </select>
    </div>

    <div class="col-12 col-sm-4 col-md-3 col-xl-2">
      <label class="col-form-label" for="select-overdue">@lang('modules.overdue')</label>
      <select id="select-overdue" class="form-control" name="overdue">
        <option value="none" @if ($filter_overdue == "none") selected @endif>None Selected</option>
        <option value="yes" @if ($filter_overdue == "yes") selected @endif>Yes</option>
        <option value="no" @if ($filter_overdue == "no") selected @endif>No</option>
      </select>
    </div>
  </div>
  
  <hr/>

  <div class="row">
    <div class="col-12">
      <button class="btn btn-danger float-right">
        <i class="fa fa-play-circle mr-2"></i>Run Report<span class="caret"></span>
      </button>
    </div>
  </div>
  </form>
</div>
</div>
<div class="row">
  <!--/.col-->


  <div class="col-md-6 col-lg-3" >
    <div class="card overflow-hidden">
      <div class="card-body p-0 d-flex align-items-center">
       <div class="bg-primary p-4 mr-3">
        <i class="fa fa-graduation-cap font-2xl"></i>
        </div>
        <div>
          <div class="text-value text-primary">{{ $courses }}</div>
          <div class="text-muted text-uppercase font-weight-bold small" >@lang('modules.courses')</div>
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
          <div class="text-value text-success">{{ $courseAssignment ? round($completedCourse / $courseAssignment * 100) : 0 }}%</div>
          <div class="text-muted text-uppercase font-weight-bold small">@lang('modules.completed_courses')</div>
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
          <div class="text-muted text-uppercase font-weight-bold small">@lang('modules.overdue')</div>
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
        <button class="btn btn-primary btn-sm send-overdue-mail" course="0">
          <i class="nav-icon fa fa-envelope mr-2"></i>@lang('modules.all') @lang('modules.overdue') @lang('modules.email')
        </button>
      </div>
      <div class="card-body">
        <table class="table table-striped table-bordered datatable" id="datatable">
          <thead>
              <tr>
                  <th>@lang("modules.course")</th>
                  <th>@lang("modules.category")</th>
                  <th>@lang("modules.enrolled")</th>
                  <th>@lang("modules.complete_percentage")</th>
                  <th>@lang("modules.incomplete_percentage")</th>
                  <th>@lang("modules.overdue") @lang("modules.users")</th>
                  <th>@lang("modules.details")</th>
              </tr>
          </thead>
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

    $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: "{!! route('reports.course.data', $filter) !!}",
          data: {
            overdue: '{{ $filter_overdue }}'
          }
        },
        order: [[2, 'desc']],
        columns: [
            { data: 'title', name: 'title' },
            { data: 'category', name: 'category' },
            { data: 'enrolled', name: 'enrolled' },
            { data: 'complete', name: 'complete' },
            { data: 'incomplete', name: 'incomplete' },
            { data: 'overdue', name: 'overdue' },
            { data: 'action', name: 'action', sortable: false, class: 'text-center', render: function (data, type, row) {
              let html = data;
              if (row.overdue > 0) {
                html += '<button class="btn btn-sm btn-primary send-overdue-mail" title="@lang('modules.overdue') @lang('modules.email')" course="' + row.id + '">' + 
                  '<i class="nav-icon fa fa-envelope"></i></button>';
              }
              return html;
            } },
          ]
    });
    $('.datatable').attr('style', 'border-collapse: collapse !important');

    $("body").on('click', '.send-overdue-mail', function () {
      const btn = $(this);
      btn.removeClass('btn-primary').addClass('btn-warning');
      btn.find('i').removeClass('fa-envelope').addClass('fa-spinner fa-spin');

      $.post("{{ route('reports.overdue') }}",
        {
          'course': btn.attr("course"),
          '_token': $("meta[name=csrf-token]").attr('content')
        },
        function () {
          btn.removeClass('btn-warning').addClass('btn-primary');
          btn.find('i').removeClass('fa-spinner').removeClass('fa-spin').addClass('fa-envelope');
          Swal.fire(
            'Overdue email',
            'Overdue email has been sent',
            'success'
          );
        }
      );
    });
});
</script>
@endpush
