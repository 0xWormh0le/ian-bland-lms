@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <i class="icon-info"></i>
            {{$title}}
          </div>
          <div class="col-sm-6 text-right">
            {!! show_button('update', 'roles.edit', encrypt($data->id)) !!}

            {!! show_button('remove', 'roles.destroy', encrypt($data->id)) !!}
          </div>
        </div>
      </div>

      <div class="card-body">

        <div class="bd-example">
          <dl class="row">
            <dd class="col-sm-3">@lang('modules.role_name')</dd>
            <dt class="col-sm-9">{{$data->role_name}}</dt>

            <dd class="col-sm-3">@lang('modules.company')</dd>
            <dt class="col-sm-9">{{$data->company_id ? $data->company->company_name : 'System Portal'}}</dt>

            <dd class="col-sm-3">@lang('modules.role_access')</dd>
            @include('users.roles.role')
          </dl>
        </div>

        @include('components.record_log')
      </div>
    </div>
  </div>
</div>
@endsection
