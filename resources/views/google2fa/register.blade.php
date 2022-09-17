@extends('layouts.auth')

@section('content')
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card-group">
          <div class="card p-4 pd-5">
            <div class="card-body">
              <h1>@lang('auth.google_2fa_title')</h1>

                @csrf
              <div class="mb-4 pt-5">
                @if(session('valid') == 1)
                    <div class="alert alert-danger">@lang('auth.google_2fa_otp_error')</div>
                 @endif

                <div class="row">
                  <div class="col-6 pt-4">
                    <div>
                      <form action="route('complete-registration')" method="post" >
                         <input type="hidden" name="first_name" value="" />
                         <input type="hidden" name="email" value="" />
                         <input type="hidden" name="name" value="" />
                         <input type="hidden" name="name" value="" />
                      </form>
                        <a href="/complete-registration"><button class="btn-primary">Complete Registration</button></a>
                    </div>
                  </div>
                </div>
              </div>
              <p class="text-muted mt-5 alert alert-info">@lang('auth.google_2fa_secret')</p>
              <p class="text-muted alert alert-danger">{{ $secret }}</p>
            </div>
          </div>
          <div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">
            <div class="card-body text-center">
              <div >
                <h2 >@lang('auth.google_2fa_qrcode_title')</h2>
                <p>@lang('auth.google_2fa_qrcode_text')</p>
                <p>
                    <img src="{{ $QR_Image }}" alt="">
                </p>
               </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
