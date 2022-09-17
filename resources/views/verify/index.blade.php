@extends('layouts.auth')

@section('content')
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card-group">
          <div class="card p-4">
            <div class="card-body">
              <h1>@lang('modules.verify_your_email')</h1>

                <p class="text-muted">
                   @lang('modules.email_verification_text')
                   <br/>
                   <br/>
                   @lang('modules.email_make_sure_text')  <a href="{{route('user.verify','resendtoken')}}" class="btn btn-sm btn-primary">@lang('modules.click_here')</a> @lang('modules.to') <strong>@lang('modules.request_new_verification_email')</strong>.

                   <br/>
                   <br/>
                   <br/>
                   <a href="{{route('login')}}" class="btn btn-md btn-primary">Login Page</a>
                </p>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
