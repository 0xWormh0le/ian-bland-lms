@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="fa fa-user-cog"></i>
          {{$title}}
      </div>
      @if(isset($limit) && $limit)
      <div class="card-body">
        <span class="alert-warning" role="alert">
          <strong>@lang("modules.max_users_limit_text")</strong>
        </span>
      </div>
      @else
      <form action="{{ route('users.adsetup.update') }}" method="post" class="form-horizontal">
        @csrf()
        @method('put')
      <div class="card-body">

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="tenant_id">@lang('modules.tenant_id') <code>*</code></label>
          <div class="col-md-9">
            <input type="text" id="tenant_id" name="tenant_id" class="form-control" value="{{old('tenant_id')?:@$data->tenant_id}}" required autofocus>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="client_id">@lang('modules.client_id') <code>*</code></label>
          <div class="col-md-9">
            <input type="text" id="client_id" name="client_id" class="form-control" value="{{old('client_id')?:@$data->client_id}}" required>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="client_secret">@lang('modules.client_secret') <code>*</code></label>
          <div class="col-md-9">
            <input type="text" id="client_secret" name="client_secret" class="form-control" value="{{old('client_secret')?:@$data->client_secret}}" required>
          </div>
        </div>

      <div class='alert alert-info'>
        <ul class='mb-0'>
        @foreach ($status as $s)
          <li>{{ $s }}</li>
        @endforeach
        </ul>
      </div>

      @if ($maxUsers)
        <div class='alert alert-warning'>
          @lang('modules.max_no_of_users')
          : <strong>{{ $maxUsers }}</strong>
        </div>
      @endif

        @if ($errors->has('tenant_id'))
          <div class="alert alert-danger">
            <strong>{{ $errors->first('tenant_id') }}</strong>
          </div>
        @endif

        <div>
          <p>@lang('modules.permission_desc')</p>
          <ul>
            <li>@lang('modules.permissions')</li>
          </ul>
          <div class='alert alert-warning'>@lang('modules.permission_note')</div>
        </div>
        
        <div>
          <a data-fancybox="gallery" href="{{ asset('/img/aadsetup/permission1.png')}}">
            <img class="img-aad-desc" src="{{ asset('/img/aadsetup/permission1.png')}}">
          </a>
          <a data-fancybox="gallery" href="{{ asset('/img/aadsetup/permission2.png')}}">
            <img class="img-aad-desc" src="{{ asset('/img/aadsetup/permission2.png')}}">
          </a>
          <a data-fancybox="gallery" href="{{ asset('/img/aadsetup/permission3.png')}}">
            <img class="img-aad-desc" src="{{ asset('/img/aadsetup/permission3.png')}}">
          </a>
          <a data-fancybox="gallery" href="{{ asset('/img/aadsetup/permission4.png')}}">
            <img class="img-aad-desc" src="{{ asset('/img/aadsetup/permission4.png')}}">
          </a>
          <a data-fancybox="gallery" href="{{ asset('/img/aadsetup/permission5.png')}}">
            <img class="img-aad-desc" src="{{ asset('/img/aadsetup/permission5.png')}}">
          </a>
          <a data-fancybox="gallery" href="{{ asset('/img/aadsetup/permission6.png')}}">
            <img class="img-aad-desc" src="{{ asset('/img/aadsetup/permission6.png')}}">
          </a>
        </div>
      </div>

      <div class="card-footer">
        @include('components.form_submit')
      </div>
      </form>
      @endif
    </div>
  </div>
</div>
@endsection

@push('css')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
@endpush

@push('scripts')
  <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
@endpush
