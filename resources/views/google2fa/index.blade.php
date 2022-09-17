@extends('layouts.auth')

@section('content')
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card-group">
          <div class="card p-4 pd-5">
            <div class="card-body">
              <h1>@lang('auth.google_2fa_title')</h1>

              <form method="post" action="{{route('google-user-auth')}}">
                @csrf
              <div class="mb-4 pt-5">
                @if(session('valid') == 1)
                    <div class="alert alert-danger">@lang('auth.google_2fa_otp_error')</div>
                 @endif
               <div class="input-group">

                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="icon-lock"></i>
                    </span>
                  </div>
                  <div>
                  <input type="text" name="code" class="form-control" placeholder="@lang('auth.google_2fa_otp_input')" required>
                </div>

                </div>
                <div class="row">
                  <div class="col-6 pt-4">
                    <button type="submit" class="btn btn-primary px-4">@lang('auth.google_2fa_validate_btn')</button>
                  </div>
                </div>
              </div>
              </form>
              <p class="text-muted mt-5 alert alert-info">@lang('auth.google_2fa_message')</p>
              <a class="btn btn-primary px-4" href="{{ route('logout') }}"
                  onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                      @csrf
                  </form>
                <i class="fa fa-arrow-left"><</i>&nbsp;@lang('auth.otp_back_login')</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
