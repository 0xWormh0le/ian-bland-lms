@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="fa fa-link"></i>
          {{$title}}
      </div>
      <form action="{{route('scormdispatch-api.update')}}" method="post" class="form-horizontal">
        @csrf()
      <div class="card-body">

        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.url')</label>
          <div class="col-sm-9">
            <input type="text" id="scorm_url" name="scorm_url" class="form-control" value="{{old('scorm_url') ?: @$env['scorm_url']}}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.app_id')</label>
          <div class="col-sm-9">
            <input type="text" id="scorm_id" name="scorm_id" class="form-control" value="{{old('scorm_id') ?: @$env['scorm_id']}}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.secret_key')</label>
          <div class="col-sm-9">
            <input type="text" id="scorm_secret" name="scorm_secret" class="form-control" value="{{old('scorm_secret') ?: @$env['scorm_secret']}}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.conference_url')</label>
          <div class="col-sm-9">
            <input type="text" id="bbb_url" name="bbb_url" class="form-control" value="{{old('bbb_url') ?: @$env['bbb_url']}}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.conference_secret_key')</label>
          <div class="col-sm-9">
            <input type="text" id="bbb_secret" name="bbb_secret" class="form-control" value="{{old('bbb_secret') ?: @$env['bbb_secret']}}">
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
