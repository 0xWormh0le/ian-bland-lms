@extends('layouts.auth')

@section('content')
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card-group">
          <div class="card p-4">
            <div class="card-body">
              <h1>{{$token == 'resendtoken' ? @lang('modules.resend_verification_email') : @lang('modules.woops')}}</h1>
              <form method="post" action="{{route('user.verify.resend')}}">
                @csrf
                @if($token !== 'resendtoken')
                <div class="alert alert-danger">
                   @lang('modules.token_verification_fail_text')
                </div>

                <p class="text-muted"><a href="{{route('login')}}" class="btn btn-sm btn-primary">@lang('modules.login')</a> @lang('modules.form_request_new_token_text')</p>
                @else
                <p class="text-muted">@lang('modules.form_fill_request_new_token_text')</p>
                @endif
                <div class="input-group mb-4">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="icon-envelope"></i>
                    </span>
                  </div>
                  <input type="email" name="email" class="form-control" placeholder="@lang('modules.your_registered_email')" required>
                </div>
                <div class="row justify-content-center">
                  <div class="col-6 text-center">
                    <button type="submit" class="btn btn-primary btn-block">@lang('modules.resend_verification')</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
