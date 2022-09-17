@extends('layouts.auth')

@section('content')
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card-group">
          <div class="card p-4">
            <div class="card-body">
              <h1>@lang('auth.otp_authentication_title')</h1>

              <form method="post" action="{{route('otp-verify')}}">
                @csrf
                <p class="text-muted">@lang('auth.enter_verification_code') (<a href="{{route('otp-resend')}}">@lang('auth.otp_resend_text')</a>)</p>
                    @if ($errors->has('otp'))
                    <div class="alert alert-danger" role="alert">
                      <strong>{{ $errors->first('otp') }}</strong>
                    </div>
                    @endif
                 <div class="input-group mb-3 verify-code-block">
                    <input type="text" id="otp[0]" name="otp[]" class="form-control auth_digit" maxlength="1" value="{{old('otp.0')}}" required autofocus>
                    <input type="text" id="otp[1]" name="otp[]" class="form-control auth_digit" maxlength="1" value="{{old('otp.1')}}" required autofocus>
                    <input type="text" id="otp[2]" name="otp[]" class="form-control auth_digit" maxlength="1" value="{{old('otp.2')}}" required autofocus>
                    <input type="text" id="otp[3]" name="otp[]" class="form-control auth_digit" maxlength="1" value="{{old('otp.3')}}" required autofocus>
                    <input type="text" id="otp[4]" name="otp[]" class="form-control auth_digit" maxlength="1" value="{{old('otp.4')}}" required autofocus>
                    <input type="text" id="otp[5]" name="otp[]" class="form-control auth_digit" maxlength="1" value="{{old('otp.5')}}" required autofocus>
                    <div class="input-group-prepend">
                    <button type="submit" style="padding:0px;margin:0px;border-radius:0.25rem">  <span style="height:100%;width:100%" class="input-group-text">
                        <i class="icon-check" style="font-size:1.5rem"></i>
                      </span>
                    </button>
                  </div>
                </div>
              </form>
            </div>
            <div>
              <a class="dropdown-item" href="{{ route('logout') }}"
                  onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                      @csrf
                  </form>
                <i class="fa fa-arrow-left"><-</i>@lang('auth.otp_back_login')</a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('css')
<style>
.verify-code-block
{
  width:360px;
}
.auth_digit
{
  width:15px !important;
  margin-right:10px;
  font-size:24px;
  font-weight:bold;
  size:4
}
</style>
@endpush
@push('scripts')
<script>

 $(document).ready(function(){
   $("input[type=text]").keyup(function () {
     $(".icon-check").css("color","gray");
    if (this.value.length == this.maxLength) {
      $(this).next('input[type=text]').focus();
    }
     checkInputVal();
   });
   checkInputVal();
 });

 function checkInputVal()
 {
   var inputCheck = false ;
   $('input[type=text]').each(function() {
        if($.trim($(this).val()).length == 0){
          inputCheck = true;
        }
    });
    if(!inputCheck)
    {
      $(".icon-check").css("color","green");
    }
 }

</script>
@endpush
