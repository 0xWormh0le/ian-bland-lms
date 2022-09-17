@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-12">
            <i class="fa fa-chalkboard-teacher"></i>
              {{$title}}
          </div>
        </div>
      </div>

      <div class="card-body">
        <table class="table table-bordered datatable" id="scheduleTable">
          <thead>
            <tr>
              <th width="150px">@lang('modules.schedule')</th>
              <th>@lang('modules.module_info')</th>
              <th width="80px"></th>
            </tr>
          </thead>
          <tbody>
            @foreach($schedules as $r)
            <tr>
              <td>{{ $r->start_date }} {{ $r->start_time }}</td>
              <td>
                {{ $r->module->title }}
                <small class="text-muted">
                  <dl class="row">
                    <dd class="col-sm-3">@lang('modules.type')</dd>
                    <dt class="col-sm-9">: {{ $r->module->type }}</dt>
                    <dd class="col-sm-3">@lang('modules.course_title')</dd>
                    <dt class="col-sm-9">: {{ $r->module->course->title }}</dt>
                    <dd class="col-sm-3">@lang('modules.duration')</dd>
                    <dt class="col-sm-9">: {{ $r->duration }} {{ $r->duration_type }}</dt>
                  </dl>
                </small>
              </td>
              <td class="text-center">
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
  $("#scheduleTable").DataTable({});
  $('.datatable').attr('style', 'border-collapse: collapse !important');
</script>
@endpush
