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
      <form action="{{isset($data) ? route('elearning.update', encrypt($data->id)) : route('elearning.store')}}" method="post" class="form-horizontal" file="true" enctype="multipart/form-data">
        @csrf()
        @isset($data)
          @method('put')
        @endisset
      <div class="card-body">

        <input type="hidden" name="course_id" value="{{$course->id}}">

        <div class="form-group row">
          <label class="col-md-3 col-form-label">@lang('modules.course')</label>
          <div class="col-md-9">
            <input type="text" class="form-control" value="{{$course->title}}" readonly>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="title">@lang('modules.title') <code>*</code></label>
          <div class="col-md-9">
            <input type="text" id="title" name="title" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" value="{{old('title')?:@$data->title}}" required autofocus>
            @if ($errors->has('title'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('title') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row d-none">
          <label class="col-md-3 col-form-label" for="title">@lang('modules.order_no') <code>*</code></label>
          <div class="col-md-9">
            <input type="number" id="order_no" name="order_no" class="form-control{{ $errors->has('order_no') ? ' is-invalid' : '' }}" value="{{old('order_no')?: @$data->order_no ?: \App\Module::getNewOrderNo($course->id)}}" required>
            @if ($errors->has('order_no'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('order_no') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="description">@lang('modules.description')</label>
          <div class="col-md-9">
            <textarea id="description" name="description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" rows="5">{{old('description')!==null?:@$data->description}}</textarea>
            @if ($errors->has('description'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('description') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="scorm_file">SCORM File <code></code></label>
          <div class="col-md-9">
            <input type="file" name="scorm_file" id="scorm_file">
            @if(@$data->scorm) {{@$data->scorm}} @endif
            @if ($errors->has('file'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('file') }}</strong>
              </span>
            @endif
          </div>
        </div>

        @if(in_array('course_stream', explode(';', config('lms.enabled_modules'))))
        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="active">{{ @session('moduleLabel')['course_stream'] ?: __('menu.course_stream')}}</label>
          <div class="col-md-9">
            <label class="switch switch-label switch-primary">
              <input type="checkbox" name="course_stream" class="switch-input" {{isset($data) && $data->elearning->course_stream == 1 ? 'checked' : ''}}>
              <span class="switch-slider" data-checked="&#x2713" data-unchecked="&#x2715"></span>
            </label>
          </div>
        </div>
        @endif


      </div>
      <div class="card-footer">
        @include('components.form_submit')
      </div>
      </form>
    </div>
  </div>
</div>
@endsection
