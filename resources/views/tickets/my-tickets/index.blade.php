@extends('layouts.app')

@section('title', 'My Tickets')

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <i class="fa fa-envelope-open-text"></i> @lang('modules.my_tickets')
          </div>
          <div class="col-sm-6 text-right">
            <a href="{{ route('ticktes.create') }}"id="create_ticket" class="btn btn-sm btn-primary">@lang('modules.create_ticket')</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-striped table-bordered">
          <thead>
              <tr>
                  <th width="120">@lang('modules.opened_at')</th>
                  <th>@lang('modules.subject')</th>
                  <th width="120">@lang('modules.ticket_id')</th>
                  <th width="120">@lang('modules.status')</th>
                  <th width="80">@lang('modules.action')</th>
              </tr>
          </thead>
          <tbody>
            @foreach($tickets as $r)
            <tr>
              <td>{{ \Carbon\Carbon::parse($r->created_at)->format("d-m-Y h:i:s") }}</td>
              <td>{{ substr(strip_tags($r->content),0,100) }}</td>
              <td>{{ $r->ticket_number }}</td>
              <td>{{ $r->status }}
                @if(!$r->read_by_user)
                  <span class="badge badge-pill badge-danger">@lang('modules.unread_status')</span>
                @endif
              </td>
              <td align="center"><a href="{{ route('my-tickets.show', encrypt($r->id)) }}" class="btn btn-sm btn-secondary">@lang('modules.details')</a></td>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>



@endsection
