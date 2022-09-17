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
            @buttonAdd(['route'=>'users.create'])
              @lang('modules.add_new_user')
            @endbuttonAdd

            @if(validate_role('users.create'))
            <a href="{{route('users.import')}}" class="btn btn-md btn-success"><i class="fa fa-file-excel-o"></i> @lang('modules.bulk_import')</a>
            @endif

            @if(validate_role('users.create') && auth()->user()->isClientAdmin())
            <a href="{{route('users.adsetup')}}" class="btn btn-md btn-success"><i class="fa fa-user-cog"></i> @lang('modules.ad_setup')</a>
            @endif
          </div>
        </div>
      </div>
      <div class="card-body">
        @if(!\Auth::user()->company_id)
        <div class="alert alert-info" style="padding-bottom:0">
          <div class="form-group row">
            <label class="col-sm-4">@lang('modules.company')</label>
            <div class="col-sm-8">
              <select id="company_id" name="company_id" class="form-control">
                @if(count($companies) > 1)
                <option value="">@lang('modules.all')</option>
                @endif
                @foreach($companies as $k => $v)
                <option value="{{$k}}">{{$v}}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        @else
          <input type="hidden" id="company_id" name="company_id" value="{{ \Auth::user()->company_id }}" readonly>
        @endif
        <input type="hidden" id="status_route"  value="{{ route('user.status.update') }}">
        <input type="hidden" id="csrf"  value="{{ csrf_token() }}">
        <div class="btn-group" role="group">
          <button id="btnGroupDrop1" type="button" style="z-index:1000" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-bolt" aria-hidden="true"></i>
             Bulk Action
          </button>
          <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
            <a class="dropdown-item bulk-btn" href="#" data-url="{{route('users.bulk.active')}}" data-type="active" >Active</a>
            <a class="dropdown-item bulk-btn" href="#" data-url="{{route('users.bulk.inactive')}}" data-type="in-active" >Inactive</a>
            <a class="dropdown-item bulk-btn" href="#" data-url="{{route('users.bulk.delete')}}" data-type="delete">Delete</a>
          </div>
        </div>
        <table class="table table-striped table-bordered datatable" id="datatable" data-url="{{ route('users.data') }}" data-admin-type="{{ Auth::user()->isSysAdmin() }}">
          <thead>
              <tr>
                  <th></th>
                  <th>@lang('modules.first_name')</th>
                  <th>@lang('modules.last_name')</th>
                  <th>@lang('modules.email')</th>
                  @if(Auth::user()->isSysAdmin())
                  <th>@lang('modules.company')</th>
                  @else
                  <th>@lang('modules.team')</th>
                  @endif
                  <th>@lang('modules.department')</th>
                  <th>@lang('modules.role')</th>
                  <th>@lang('modules.ad')</th>
                  <th>@lang('modules.status')</th>
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
<script src="{{ mix('scripts/users/index.js') }}"></script>
<script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/js/dataTables.checkboxes.min.js"></script>
@endpush
@push('css')
<style>
 #btnGroupDrop1
 {
   position:absolute!important;
   top: 5px!important;
   left:200px!important;
 }
</style>
<link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css" rel="stylesheet" />
@endpush
