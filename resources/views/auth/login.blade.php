@extends('layouts.auth')

@section('content')
 <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card-group">
          <div class="card p-4">
            <div class="card-body">
              <h1>@lang('auth.login')</h1>
              <form method="post" action="{{route('login')}}">
                @csrf

            @if ($login_view_type != 'default-step-2')
                <p class="text-muted">@lang('auth.sign_in_to_account')</p>
            @else
                <p class="text-muted">@lang('auth.sign_in_to') <span class="text-primary">{{ old('email') }}</span></p>
            @endif

            @if ($login_view_type == 'default-step-1' || $login_view_type == 'tradition')
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="icon-user"></i>
                    </span>
                  </div>
                  <input type="email" name="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="@lang('auth.email')" value="{{ old('email') }}" required autofocus>
                  @if ($errors->has('email'))
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                  </span>
                  @endif
                </div>
            @endif

            @if ($login_view_type == 'default-step-2' || $login_view_type == 'tradition')
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="icon-lock"></i>
                    </span>
                  </div>
              @if ($login_view_type == 'default-step-2')
                  <input type="hidden" name="email" value="{{ old('email') }}" />
              @endif
                  <input type="password" name="password" class="form-control" placeholder="@lang('auth.password')" required @if ($login_view_type == 'default-step-2') autofocus @endif>
                </div>
            @endif
                <div class="row">
            @if ($login_view_type == 'default-step-1')
                  <div class="col-6">
                    <button type="submit" class="btn btn-primary px-4">@lang('auth.next')</button>
                  </div>
                  <div class="col-6 text-right">
                    <a href="{{route('password.request')}}" class="btn btn-link px-0">@lang('auth.forgot_password')?</a>
                  </div>
            @endif

            @if ($login_view_type == 'default-step-2' || $login_view_type == 'tradition')
                  <div class="col-6 d-flex">
                    <button type="submit" class="btn btn-primary px-4">@lang('auth.login')</button>
                @if ($login_view_type == 'default-step-2')
                    <a class="btn btn-secondary px-4 ml-2" href="{{ route('login') }}">@lang('auth.back')</a>
                @endif
                  </div>
                  <div class="col-6 text-right">
                    <a href="{{route('password.request')}}" class="btn btn-link px-0">@lang('auth.forgot_password')?</a>
                  </div>
            @endif

            @if ($login_view_type == 'sso')
                  <div class="col-12 mt-2">
                    <a href='/login/microsoft' class='btn-sign-in-with-ms'></a>
                  </div>
            @endif
                </div>
              </form>
            </div>
          </div>
         
        </div>
      </div>
    </div>
  </div>
@endsection
