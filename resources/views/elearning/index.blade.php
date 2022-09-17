@extends('layouts.app')

@section('title', 'List of Courses')

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="icon-list"></i> @lang('modules.list_of_courses')
      </div>
      <div class="card-body">
        <table class="table table-striped table-bordered datatable" id="datatable">
          <thead>
              <tr>
                  <th>@lang('modules.title')</th>
                  <th>@lang('modules.date_added')</th>
                  <th>@lang('modules.completion')</th>
                  <th>@lang('modules.success')</th>
                  <th>@lang('modules.score')</th>
                  <th>@lang('modules.total_time')</th>
                  <th>@lang('modules.action')</th>
              </tr>
          </thead>
          <tbody>
          @foreach($datas as $r)
              <tr>
                <td>{{$r['title']}}</td>
                <td>{{$r['added_date']}}</td>
                <td>{{$r['complete']}}</td>
                <td>{{$r['success']}}</td>
                <td>{{$r['score']}}</td>
                <td>{{$r['time']}}</td>
                <td>
                  <a href="{{$r['launch_url']}}" class="btn btn-primary" title="@lang('modules.title')">@lang('modules.title')</a>
                </td>
              </tr>
          @endforeach
          </tbody>
        </table>
      </div>
      <div class="card-footer">
        @buttonAdd(['route'=>'courses.create'])
          @lang('modules.add_new_course')
        @endbuttonAdd
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
        columnDefs: [
            { width: "120", targets: 1 },
            { width: "100", targets: 2 },
        ]
    });
    $('.datatable').attr('style', 'border-collapse: collapse !important');
});
</script>
@endpush
