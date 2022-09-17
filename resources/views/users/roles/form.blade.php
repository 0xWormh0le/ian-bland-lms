@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="icon-{{isset($data)?'pencil':'plus'}}"></i>
          {{$title}}
      </div>
      <form id="form" action="{{isset($data) ? route('roles.update', encrypt($data->id)) : route('roles.store')}}" method="post" class="form-horizontal">
        @csrf()
        @isset($data)
          @method('put')
        @endisset
      <div class="card-body">
        <input type="hidden" name="is_client" value="{{@$data ? $data->is_client : $is_client}}">
        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="role_name">@lang('modules.role_name') <code>*</code></label>
          <div class="col-md-9">
            <input type="text" id="role_name" name="role_name" class="form-control{{ $errors->has('role_name') ? ' is-invalid' : '' }}" value="{{old('role_name')?:@$data->role_name}}" required autofocus>
            @if ($errors->has('role_name'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('role_name') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="role_access">@lang('modules.role_access')</label>
          <textarea name="role_access" id="role_access" class="d-none"></textarea>

          @include('users.roles.role')
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
