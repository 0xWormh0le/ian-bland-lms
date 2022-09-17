@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
              <i class="fa fa-users"></i> {{$title}}
          </div>
          <div class="col-sm-6 text-right">
          </div>
        </div>
      </div>
      <div class="card-body">

        <table class="table table-striped table-bordered datatable" id="datatable" >
          <thead>
              <tr>
                  <th>@lang('modules.id')</th>
                  <th>@lang('modules.course_title')</th>
                  <th>@lang('modules.slug')</th>
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
        ajax: '{!! route('courses.list.data') !!}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'title', name: 'title' },
            { data: 'slug', name: 'slug' },
        ]
    });
    $('.datatable').attr('style', 'border-collapse: collapse !important');
});
</script>
@endpush
