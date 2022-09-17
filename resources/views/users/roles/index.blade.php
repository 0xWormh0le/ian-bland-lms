@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <i class="fa fa-user-shield"></i> {{$title}}
          </div>
          <div class="col-sm-6 text-right">
            @if(!\Auth::user()->company_id)
              <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="icon-plus"></i> @lang('modules.add_new_role')
                </button>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="{{ route('roles.create') }}">@lang('modules.for_system')</a>
                  <a class="dropdown-item" href="{{ route('roles.create') }}?type=client">@lang('modules.for_client')</a>
                </div>
              </div>
            @else
              @buttonAdd(['route'=>'roles.create'])
                @lang('modules.add_new_role')
              @endbuttonAdd
            @endif

          </div>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-striped table-bordered datatable" id="datatable" data-url="{!! route('roles.data') !!}">
          <thead>
              <tr>
                  <th>@lang('modules.role')</th>
                  <th>@lang('modules.date_added')</th>
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
<script src="{{ mix('scripts/roles/index.js') }}"></script>
@endpush
