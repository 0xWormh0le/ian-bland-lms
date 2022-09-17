@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="fa fa-comments"></i>
          {{$title}}
      </div>
      <form action="{{route('pusher.update')}}" method="post" class="form-horizontal">
        @csrf()
      <div class="card-body">

        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.app_id')</label>
          <div class="col-sm-9">
            <input type="text" id="pusher_app_id" name="pusher_app_id" class="form-control" value="{{old('pusher_app_id') ?: @$env['pusher_app_id']}}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.app_key')</label>
          <div class="col-sm-9">
            <input type="text" id="pusher_key" name="pusher_key" class="form-control" value="{{old('pusher_key') ?: @$env['pusher_key']}}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.app_secret')</label>
          <div class="col-sm-9">
            <input type="text" id="pusher_secret" name="pusher_secret" class="form-control" value="{{old('pusher_secret') ?: @$env['pusher_secret']}}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.app_cluster')</label>
          <div class="col-sm-9">
            <input type="text" id="pusher_cluster" name="pusher_cluster" class="form-control" value="{{old('pusher_cluster') ?: @$env['pusher_cluster']}}">
          </div>
        </div>

      </div>
      <div class="card-footer">
        @include('components.form_submit')
      </div>
      </form>
    </div>
  </div>
</div>
@endsection
