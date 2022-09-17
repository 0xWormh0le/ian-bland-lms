@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">

   @if(!Auth::user()->isSysAdmin())
    <div class="card">
      <div class="card-header">
        <i class="fa fa-at"></i>
          @lang('modules.smtp_default_setting')
      </div>
      <form action="{{route('client.mail.update')}}" method="post" class="form-horizontal">
        @csrf()
      <div class="card-body">
        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.mail_from_address')</label>
          <div class="col-sm-9">
          {{config('mail.from.address')}}
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.mail_from_name')</label>
          <div class="col-sm-9">
            <input type="text" id="mail_from_name_custom" name="mail_from_name_custom" class="form-control" value="{{old('mail_from_name_custom') ?: @$env['mail_from_name_custom']}}">
          </div>
        </div>
      </div>

     <div class="card-footer">
       @include('components.form_submit')
     </div>
     </form>
    </div>
  @endif
    <div class="card">
      <div class="card-header">
        <i class="fa fa-at"></i>
        @if(Auth::user()->isSysAdmin())
          {{$title}}
        @else
          @lang('modules.smtp_custom_setting')
        @endif
      </div>
      <form action="{{$env['mail_type']==1? route('client-smtp-account.update') : route('smtp-account.update')}}" method="post" class="form-horizontal">
        @csrf()
      <div class="card-body">
        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.mail_driver')</label>
          <div class="col-sm-9">
            <select id="mail_driver" name="mail_driver" class="form-control" required>
              @foreach(['smtp' => 'SMTP', 'mailgun'=>'Mailgun', 'sparkpost' => 'Sparkpost'] as $k => $v)
              <option value="{{$k}}"{{old('mail_driver') == $k || $env['mail_driver'] == $k ? ' selected':''}}>{{$v}}</option>
              @endforeach
            </select>
          </div>
        </div>
         <div id="smtp-conf">
          <div class="form-group row">
            <label class="col-sm-3">@lang('modules.mail_host')</label>
            <div class="col-sm-9">
              <input type="text" id="mail_host" name="mail_host" class="form-control {{ $errors->has('mail_host') ? ' is-invalid' : '' }}" value="{{old('mail_host') ?: @$env['mail_host']}}">
              @if ($errors->has('mail_host'))
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('mail_host') }}</strong>
                </span>
              @endif
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-3">@lang('modules.mail_port')</label>
            <div class="col-sm-9">
              <input type="text" id="mail_port" name="mail_port" class="form-control {{ $errors->has('mail_port') ? ' is-invalid' : '' }}" value="{{old('mail_port') ?: @$env['mail_port']}}">
              @if ($errors->has('mail_port'))
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('mail_port') }}</strong>
                </span>
              @endif
             </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-3">@lang('modules.mail_username')</label>
            <div class="col-sm-9">
              <input type="text" id="mail_username" name="mail_username" class="form-control {{ $errors->has('mail_username') ? ' is-invalid' : '' }}" value="{{old('mail_username') ?: @$env['mail_username']}}">
              @if ($errors->has('mail_username'))
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('mail_username') }}</strong>
                </span>
              @endif
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-3">@lang('modules.mail_password')</label>
            <div class="col-sm-9">
              <input type="password" id="mail_password" name="mail_password" class="form-control {{ $errors->has('mail_password') ? ' is-invalid' : '' }}" value="{{old('mail_password') ?: @$env['mail_password']}}">
              @if ($errors->has('mail_password'))
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('mail_password') }}</strong>
                </span>
              @endif
            </div>
          </div>
        </div>

        <div id="mailgun-conf">
          <div class="form-group row">
            <label class="col-sm-3">@lang('modules.mailgun_domain')</label>
            <div class="col-sm-9">
              <input type="text" id="mailgun_domain" name="mailgun_domain" class="form-control {{ $errors->has('mailgun_domain') ? ' is-invalid' : '' }}" value="{{old('mailgun_domain') ?: @$env['mailgun_domain']}}">
              @if ($errors->has('mailgun_domain'))
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('mailgun_domain') }}</strong>
                </span>
              @endif
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-3">@lang('modules.mailgun_secret')</label>
            <div class="col-sm-9">
              <input type="text" id="mailgun_secret" name="mailgun_secret" class="form-control {{ $errors->has('mailgun_secret') ? ' is-invalid' : '' }}" value="{{old('mailgun_secret') ?: @$env['mailgun_secret']}}">
              @if ($errors->has('mailgun_secret'))
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('mailgun_secret') }}</strong>
                </span>
              @endif
            </div>
          </div>
        </div>

        <div id="sparkpost-conf">
          <div class="form-group row">
            <label class="col-sm-3">@lang('modules.sparkpost_secret')</label>
            <div class="col-sm-9">
              <input type="text" id="sparkpost_secret" name="sparkpost_secret" class="form-control {{ $errors->has('sparkpost_secret') ? ' is-invalid' : '' }}" value="{{old('sparkpost_secret') ?: @$env['sparkpost_secret']}}">
              @if ($errors->has('sparkpost_secret'))
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('sparkpost_secret') }}</strong>
                </span>
              @endif
           </div>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.mail_from_address')</label>
          <div class="col-sm-9">
            <input type="text" id="mail_from_address" name="mail_from_address" class="form-control" value="{{old('mail_from_address') ?: @$env['mail_from_address']}}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.mail_from_name')</label>
          <div class="col-sm-9">
            <input type="text" id="mail_from_name" name="mail_from_name" class="form-control" value="{{old('mail_from_name') ?: @$env['mail_from_name']}}">
          </div>
        </div>

      </div>
      <div class="card-footer">
         @include('components.form_submit')
           <button type="button" class="btn btn-secondary btn-md float-right reset_to_default"><i class="icon-refresh"></i> @lang('modules.reset_to_default')</button>
       </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ mix('scripts/sysconfig/smtp-account.js') }}"></script>
<script>
$(document).on('click', '.reset_to_default', function (e) {
  e.preventDefault();
  swal({
    title: "Are you sure? this will reset to default smtp configuration",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, do it!',
  }).then((result) => {
    if (result.value) {
       var url = "{{route('client-smtp-account.reset')}}";
       $(location).attr('href',url);
    }
  });
});
</script>
@endpush
