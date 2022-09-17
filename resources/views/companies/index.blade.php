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
          <div class="col-sm-6 text-right">
            @buttonRestore(['route'=>'companies.restore'])
              @lang("modules.restore_company")
            @endbuttonRestore
            @buttonAdd(['route'=>'companies.create'])
              @lang("modules.add_new_company")
            @endbuttonAdd
          </div>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-striped table-bordered datatable" id="datatable">
          <thead>
              <tr>
                  <th>@lang("modules.name")</th>
                  <th>@lang("modules.max_no_of_users")</th>
                  <th>@lang("modules.active_from")</th>
                  <th>@lang("modules.active_to")</th>
                  <th>@lang("modules.date_added")</th>
                  <th>@lang("modules.active")</th>
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
        ajax: '{!! route('companies.data') !!}',
        columns: [
            { data: 'company_name', name: 'company_name' },
            { data: 'max_users', name: 'max_users' },
            { data: 'active_from', name: 'active_from', render: renderColumnDate },
            { data: 'active_to', name: 'active_to', render: renderColumnDate },
            { data: 'created_at', name: 'created_at', render: renderColumnDate },
            { data: 'active', name: 'active' },
            { data: 'action', name: 'action', sortable:false, class:'text-center' },
        ],
        columnDefs: [
            { width: "60", targets: 1, class:'text-center' },
            { width: "120", targets: 4 },
            { width: "100", targets: 6 },
        ]
    });
    $('.datatable').attr('style', 'border-collapse: collapse !important');
});
</script>
@endpush
