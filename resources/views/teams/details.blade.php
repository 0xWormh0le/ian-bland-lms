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
            {!! show_button('update', 'teams.edit', $data->slug) !!}

            {!! show_button('remove', 'teams.destroy', encrypt($data->id)) !!}
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="bd-example">
          <dl class="row">
            <dd class="col-sm-3">@lang('modules.company_name')</dd>
            <dt class="col-sm-9">{{$data->company->company_name}}</dt>

            <dd class="col-sm-3">@lang('modules.team_name')</dd>
            <dt class="col-sm-9">{{$data->team_name}}</dt>

            <dd class="col-sm-3">@lang('modules.manager')</dd>
            <dt class="col-sm-9">{{$data->manager_user_id ? $data->manager->first_name.' '.$data->manager->last_name : '-'}}</dt>
          </dl>
        </div>
        @include('components.record_log')
      </div>
    </div>
  </div>
  @include('teams.users')
  @include('teams.courses')
</div>
@endsection
