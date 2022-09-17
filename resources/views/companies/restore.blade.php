@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <i class="fa fa-building"></i> {{$title}}
          </div>
         </div>
      </div>
      <div class="card-body">
        <table class="table table-striped table-bordered datatable" id="datatable">
          <thead>
              <tr>
                  <th>@lang("modules.name")</th>
                  <th>@lang("modules.date_added")</th>
                  <th>@lang("modules.date_deleted")</th>
                  <th>@lang("modules.action")</th>
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
        ajax: '{!! route('companies.restore.data') !!}',
        columns: [
            { data: 'company_name', name: 'company_name' },
            { data: 'created_at', name: 'created_at' },
            { data: 'deleted_at', name: 'deleted_at' },
            { data: 'action', name: 'action', sortable:false, class:'text-center' },
        ],
        columnDefs: [
            { width: "120", targets: 1 },
            { width: "130", targets: 2 },
            { width: "100", targets: 3 },
        ]
    });
    $('.datatable').attr('style', 'border-collapse: collapse !important');
});
</script>
@endpush
