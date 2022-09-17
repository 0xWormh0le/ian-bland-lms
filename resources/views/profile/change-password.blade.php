@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="fa fa-key"></i>
          {{$title}}
      </div>
      <form action="{{route('user.password.update')}}" method="post" class="form-horizontal">
        @csrf()
      <div class="card-body">
        @if(session()->has('success'))
             <div class="alert alert-success">
                 {{ session()->get('success') }}
             </div>
         @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="m-0">
                  @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="old_password">@lang('modules.old_password')<code>*</code></label>
          <div class="col-md-9">
            <input type="password" id="old_password" name="old_password" class="form-control {{ $errors->has('old_password') ? 'is-invalid' : '' }}" value="{{old('old_password')}}"  autofocus>
            @if ($errors->has('old_password'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('old_password') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="new_password">@lang('modules.new_password')<code>*</code></label>
          <div class="col-md-9">
            <input type="password" id="new_password" name="new_password" class="form-control {{ $errors->has('new_password') ? 'is-invalid' : '' }}" value="{{old('new_password')}}" >
            @if ($errors->has('new_password'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('new_password') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="confirm_password">@lang('modules.confirm_new_password')<code>*</code></label>
          <div class="col-md-9">
            <input type="password" id="confirm_password" name="confirm_password" class="form-control {{ $errors->has('confirm_password') ? 'is-invalid' : '' }}" value="{{old('confirm_password')}}" >
            @if ($errors->has('confirm_password'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('confirm_password') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="google2fa_enable">@lang('modules.google2fa_enable')</label>
          <div class="col-md-9">
            @php
             $google2fa_status = (old('google2fa_enable')?:@$user->google2fa_enable);
            @endphp
            <select id="google2fa_enable" name="google2fa_enable"  >
               <option value="0" {{$google2fa_status==0?'selected':''}}>No</option>
               <option value="1" {{$google2fa_status==1?'selected':''}}>Yes</option>
            </select>
          </div>
        </div>

        <div class="form-group row" id="secret_key"  style="display:{{$google2fa_status == 1?'block':'none'}}">
          <label class="col-md-3 col-form-label" for="secret_key" >@lang('modules.secret_key')</label>
          <div class="col-md-6">
            <input type="text" id="google2fa_secret" name="google2fa_secret" class="form-control" value="{{old('google2fa_secret')?:@$user->google2fa_secret}}" readonly>
        </div>
        </div>
        <div class="form-group row" id="qr_img"  style="display:{{$google2fa_status == 1?'block':'none'}}">
          <label class="col-md-3 col-form-label" for="google2fa_QR"></label>
          <div class="col-md-9">
            <p>@lang('auth.google_2fa_secret')</p>
           @if(isset($auth_result))
            <img src="{{ $auth_result['qr_image']?:'' }}" id="google2fa_QR" width="200px" />
           @else
            <img src="" id="google2fa_QR" width="200px" />
           @endif
          </div>
        </div>


      </div>
      <div class="card-footer">
        <button id="submit" type="submit" class="btn btn-primary"><i class="icon-check"></i> @lang('modules.update_password')</button>
      </div>
      </form>
    </div>
  </div>
</div>
@endsection

@include('_plugins.croppie-droppie')

@push('scripts')
<script>
$('form').submit(function() {
  if($(".croppie-crop-btn").is(":visible"))
    $(".croppie-crop-btn").click();
});

$(document).ready(function(){

  $("select[name='google2fa_enable']").change(function(){

     if($(this).val() == 1)
     {
       $("#secret_key").css("display","block");
       $("#qr_img").css("display","block");

     $.ajax({

           url: "{{route('google-auth')}}",
           method: 'POST',
           type: 'json',
           data: {_token: '{{csrf_token()}}'},
           success: function(data) {
                  $("#google2fa_secret").val(data.google2fa_secret);
                  $("#google2fa_QR").attr("src", data.qr_image);
            }
         });
     }
     else {
       $("#secret_key").css("display","none");
       $("#qr_img").css("display","none");
     }

  });
});
</script>
@endpush
