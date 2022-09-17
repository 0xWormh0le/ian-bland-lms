@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <i class="fa fa-envelope"></i> {{$title}}
          </div>
          <div class="col-sm-6 text-right">
          </div>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-striped table-bordered datatable" id="datatable" data-url="{{route('tickets.data', 'closed')}}">
          <thead>
              <tr>
                  <th>@lang('modules.opened_at')</th>
                  <th>@lang('modules.sender')</th>
                  <th>@lang('modules.company')</th>
                  <th>@lang('modules.subject')</th>
                  <th>@lang('modules.ticket_id')</th>
                  <th>@lang('modules.status')</th>
                  <th>@lang('modules.assigned_to')</th>
                  <th>@lang('modules.action')</th>
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
<script src="{{ mix('scripts/tickets/index.js') }}"></script>
@endpush
