@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row justify-content-md-center">
  <div class="col-sm-6 col-md-3">
    <a href="{{route('tickets.open')}}" class="nodecoration">
      <div class="card text-white bg-info">
        <div class="card-body">
          <div class="h1 text-muted text-right mb-4">
            <i class="fa fa-envelope-open-text"></i>
          </div>
          <div class="text-value">{{$active_count}}</div>
          <h5 class="text-muted text-uppercase font-weight-bold">@lang('modules.active_tickets')</h5>
          <div class="progress progress-white progress-xs mt-3">
            <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
      </div>
    </a>
  </div>

  <div class="col-sm-6 col-md-3">
    <a href="{{route('tickets.closed')}}" class="nodecoration">
      <div class="card text-white bg-warning">
        <div class="card-body">
          <div class="h1 text-muted text-right mb-4">
            <i class="fa fa-envelope"></i>
          </div>
          <div class="text-value">{{$closed_count}}</div>
          <h5 class="text-muted text-uppercase font-weight-bold">@lang('modules.closed_tickets')</h5>
          <div class="progress progress-white progress-xs mt-3">
            <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
      </div>
    </a>
  </div>

  <div class="col-sm-6 col-md-3">
    <a href="{{route('tickets.open')}}" class="nodecoration">
      <div class="card text-white bg-danger">
        <div class="card-body">
          <div class="h1 text-muted text-right mb-4">
            <i class="fa fa-envelope-open"></i>
          </div>
          <div class="text-value">{{$unread_count}}</div>
          <h5 class="text-muted text-uppercase font-weight-bold">@lang('modules.unread')</h5>
          <div class="progress progress-white progress-xs mt-3">
            <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
      </div>
    </a>
  </div>
</div>
@endsection

@push('css')
<style>
  .nodecoration:hover {
    text-decoration: none;
  }
</style>
@endpush
