@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="icon-{{isset($data)?'pencil':'plus'}}"></i>
          {{$title}}
      </div>
      <form action="{{isset($data) ? route('companies.update', $data->id) : route('companies.store')}}" method="post" class="form-horizontal" file="true" enctype="multipart/form-data">
        @csrf()
        @isset($data)
          @method('put')
        @endisset
      <div class="card-body">

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="company_name">@lang("modules.company_name") <code>*</code></label>
          <div class="col-md-9">
            <input type="text" id="company_name" name="company_name" class="form-control{{ $errors->has('company_name') ? ' is-invalid' : '' }}" value="{{old('company_name')?:@$data->company_name}}" required autofocus>
            @if ($errors->has('company_name'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('company_name') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="company_name">@lang("modules.logo")</label>
          <div class="col-md-9">
            <input type="file" id="logo" name="logo" class="form-control img-upload{{ $errors->has('logo') ? ' is-invalid' : '' }}" accept=".png, .jpg, .jpeg">
            @if ($errors->has('logo'))
              <span class="invalid-feedback" style="display:block" role="alert">
                  @foreach($errors->get('logo') as $error)
                    {{ $error }} <br>
                  @endforeach
              </span>
            @endif
            <img class="img-preview mt-3" src="{{ @$data->logo ? asset('storage/logo/'.$data->logo) : asset('img/no-img.jpg') }}" alt="logo" width="200"/>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="max_users">@lang("modules.max_no_of_users")</label>
          <div class="col-md-9">
            <input type="text" id="max_users" name="max_users" class="form-control{{ $errors->has('max_users') ? ' is-invalid' : '' }}" value="{{old('max_users')?:@$data->max_users}}" autofocus>
            @if ($errors->has('max_users'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('max_users') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="active_from">@lang("modules.active_from")</label>
          <div class="col-md-9">
            <input type="text" id="active_from" style="width:100px; display:inline" name="active_from" class="form-control{{ $errors->has('active_from') ? ' is-invalid' : '' }}" value="{{old('active_from')?:@$data->active_from}}"  autofocus>&nbsp;
            @lang("modules.to_cap") &nbsp; <input type="text" style="width:110px; display:inline" id="active_to" name="active_to" class="form-control{{ $errors->has('active_to') ? ' is-invalid' : '' }}" value="{{old('active_to')?:@$data->active_to}}"  autofocus>
            @if ($errors->has('active_from'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('active_from') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="active">@lang("modules.active")</label>
          <div class="col-md-9">
            <label class="switch switch-label switch-primary">
              <input type="checkbox"  name="active" class="switch-input" {{isset($data) && $data->active == 0 ? '' : 'checked'}}>
              <span class="switch-slider" style="z-index:0" data-checked="&#x2713" data-unchecked="&#x2715"></span>
            </label>
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

@push("css")
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush
@push("scripts")
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
$(document).ready(function(){
  var active_from_date = $("#active_from").val() ;
  var active_to_date =  $("#active_to").val() ;

  $( "#active_from" ).datepicker({
         dateFormat: 'dd-mm-yy',
         defaultDate: active_from_date
       });
  $( "#active_to" ).datepicker({
         dateFormat: 'dd-mm-yy',
         defaultDate: active_to_date
       });
} );
</script>
@endpush
