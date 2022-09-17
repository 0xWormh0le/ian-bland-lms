@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="fa fa-cogs"></i>
          {{$title}}
      </div>
      <form action="{{ route('configuration.update') }}" method="post" class="form-horizontal" file="true" enctype="multipart/form-data">
        @csrf()
        @method('put')
      <div class="card-body">

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="company_name">@lang('modules.company_name')<code>*</code></label>
          <div class="col-md-9">
            <input type="text" id="company_name" name="company_name" class="form-control{{ $errors->has('company_name') ? ' is-invalid' : '' }}" value="{{old('company_name')?:@$company->company_name}}" required autofocus>
            @if ($errors->has('company_name'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('company_name') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="timezone">@lang('modules.timezone')</label>
          <div class="col-md-9">
            <select id="timezone" name="timezone" >
              @foreach($timezones as $t)
                <option value="{{$t['zone']}}" {{((old('timezone')==$t['zone']) || ($company->timezone==$t['zone']))? 'selected':''}}>{{$t['diff_from_GMT'] . ' - ' . $t['zone']}}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="title">@lang('modules.language')</label>
          <div class="col-md-9">
            <select id="language" name="language" >
              @foreach(config('app.languages') as $key=>$val)
                <option value="{{$key}}" {{((old('language')==$key) || ($company->language==$key))? 'selected':''}}>{{$val}}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="top_heading">@lang('modules.top_navigation_heading')</label>
          <div class="col-md-9">
            <input type="text" id="top_heading" name="top_heading" class="form-control{{ $errors->has('top_heading') ? ' is-invalid' : '' }}" value="{{old('top_heading')?:@$company->top_heading}}">
            @if ($errors->has('top_heading'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('top_heading') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="logo">@lang('modules.logo')</label>
          <div class="col-md-9">
            <input type="file" id="logo" name="logo" class="form-control img-upload{{ $errors->has('logo') ? ' is-invalid' : '' }}" accept="image/*">
            @if ($errors->has('logo'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('logo') }}</strong>
              </span>
            @endif
            <img class="img-preview mt-3" src="{{ @$company->logo ? asset('storage/logo/'.$company->logo) : asset('img/no-img.jpg') }}" alt="logo" width="200"/>
          </div>
        </div>
        <h5>@lang('modules.color_theme')</h5>
        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="top_bar">@lang('modules.top_navbar')</label>
          <div class="col-md-2">
            <input name="top_bar" class="form-control jscolor" value="{{$company->top_bar ?: 'FFFFFF'}}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="top_bar_text">@lang('modules.navbar_text_color')</label>
          <div class="col-md-2">
            <input name="top_bar_text" class="form-control jscolor" value="{{$company->top_bar_text ?: '20A8D8'}}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="active_menu">@lang('modules.sidebar_menu')</label>
          <div class="col-md-2">
            <input name="active_menu" class="form-control jscolor" value="{{$company->active_menu ?: '20A8D8'}}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="active_menu_hover">@lang('modules.menu_hover')</label>
          <div class="col-md-2">
            <input name="active_menu_hover" class="form-control jscolor" value="{{$company->active_menu_hover ?: 'FFFFFF'}}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="text_primary">@lang('modules.url')</label>
          <div class="col-md-2">
            <input name="text_primary" class="form-control jscolor" value="{{$company->text_primary ?: '20A8D8'}}">
          </div>
        </div>



      </div>
      <div class="card-footer">
        @include('components.form_submit')
      </div>
      </form>
    </div>
  </div>
</div>
@endsection


@push('scripts')
<script src="{{asset('vendors/jscolor/jscolor.js')}}"></script>
@endpush
