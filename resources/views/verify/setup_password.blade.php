@extends('layouts.auth')

@section('content')
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card-group">
          <div class="card p-4">
            <div class="card-body">
              <h1>@lang('modules.setup_password')</h1>
              <form method="post" action="{{route('user.verify.confirm', $token)}}">
                @csrf
                <div class="alert alert-info">
                  @lang('modules.welcome') <strong>{{$user->first_name}}</strong>,
                  <br/>
                  <br/>
                  @lang('modules.set_password_text')
                </div>

                <div class="input-group mb-4">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="icon-lock"></i>
                    </span>
                  </div>
                  <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : ''}}" placeholder="Password" required>

              @if ($errors->has('password'))
                  <span class="invalid-feedback" role="alert">{{ $errors->first('password') }}</span>
              @endif
                </div>

                <div class="input-group mb-4">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="icon-check"></i>
                    </span>
                  </div>
                  <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                </div>

                <div class="row justify-content-center">
                  <div class="col-4 text-center">
                    <button type="submit" class="btn btn-primary btn-block">@lang('modules.submit')</button>
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
