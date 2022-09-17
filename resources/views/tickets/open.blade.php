@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <i class="fa fa-envelope-open-text"></i> {{$title}}
          </div>
          <div class="col-sm-6 text-right">
          </div>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-striped table-bordered datatable" id="datatable" data-url="{{route('tickets.data', 'open')}}">
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

@push('modals')
<div id="assignTicketModal" class="modal" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">@lang('modules.assign_ticket')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group row">
          <label class="col-4">@lang('modules.assign_to')</label>
          <div class="col-8">
            <input type="hidden" id="ticket_id">
            <input type="hidden" id="assignee_url" value="{{route('tickets.get.assignee')}}" />
            <select id="assigned_to" class="form-control select2" style="width:100%;" required>
              <option value="">@lang('modules.select')</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer text-right">
        <button type="button" id="submitAssign" class="btn btn-primary" data-url="{{ route('tickets.assign') }}">@lang('modules.submit')</button>
      </div>
    </div>
  </div>
</div>
@endpush

@include('_plugins.datatables')
@include('_plugins.select2')
@push('scripts')
<script src="{{ mix('scripts/tickets/index.js') }}"></script>
@endpush
