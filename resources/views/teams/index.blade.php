@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <i class="fa fa-user-friends"></i> {{$title}}
          </div>
          <div class="col-sm-6 text-right">
            @buttonAdd(['route'=>'teams.create'])
              @lang('modules.add_new_team')
            @endbuttonAdd
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

        <table class="table table-striped table-bordered datatable" id="datatable" data-url="{!! route('teams.data') !!}">
          <thead>
              <tr>
                  <th>@lang('modules.name')</th>
                  <th>@lang('modules.company')</th>
                  <th>@lang('modules.manager')</th>
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
<script src="{{ mix('scripts/teams/index.js') }}"></script>
@endpush
