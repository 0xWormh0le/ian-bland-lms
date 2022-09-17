@extends('layouts.auth')

@section('content')
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card-group">
          <div class="card p-4">
            <div class="card-body">
              <h1>@lang('modules.woops')</h1>

                <p class="text-muted">
                   @lang('modules.your_email_is_not_registered')
                   <br/>
                   <br/>
                   <a href="{{route('login')}}" class="btn btn-md btn-primary">@lang('modules.login_page')</a>
                </p>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
