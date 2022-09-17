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
      <form action="{{route('courses.companies.schedule.update', ['course' => $course->slug, 'company' => $company->slug, 'module_id' => $module->id])}}" method="post" class="form-horizontal" file="true" enctype="multipart/form-data">
        @csrf()
      <div class="card-body">

        <input type="hidden" name="course_id" value="{{$course->id}}">
        <input type="hidden" name="company_id" value="{{$company->id}}">
        <input type="hidden" name="module_id" value="{{$module->id}}">
        <input type="hidden" name="type" value="{{$module->type}}">

        <div class="form-group row">
          <label class="col-md-3 col-form-label">@lang('modules.course')</label>
          <div class="col-md-9">
            <input type="text" class="form-control" value="{{$course->title}}" readonly>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label">@lang('modules.company')</label>
          <div class="col-md-9">
            <input type="text" class="form-control" value="{{$company->company_name}}" readonly>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label">@lang('modules.module')</label>
          <div class="col-md-9">
            <input type="text" name="title" class="form-control" value="[{{session('moduleLabel')[strtolower($module->type)]}}] {{$module->title}}" readonly>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="title">@lang('modules.started_at')<code>*</code></label>
          <div class="col-md-2">
            <input type="text" id="start_date" name="start_date" class="form-control datepicker{{ $errors->has('start_date') ? ' is-invalid' : '' }}" value="{{old('start_date')?:dateformat(@$data->start_date, 'd/m/Y')}}" required>
            @if ($errors->has('start_date'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('start_date') }}</strong>
              </span>
            @endif
          </div>
          <div class="col-md-2">
            <input type="time" id="start_time" name="start_time" class="form-control{{ $errors->has('start_time') ? ' is-invalid' : '' }}" value="{{old('start_time')?:@$data->start_time}}" maxlength="5" placeholder="00:00" required>
            @if ($errors->has('start_time'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('start_time') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="duration">@lang('modules.duration')</label>
          <div class="col-md-2">
            <input type="text" id="duration" name="duration" class="form-control numberonly{{ $errors->has('duration') ? ' is-invalid' : '' }}" value="{{old('duration')?:@$data->duration}}" maxlength="3" placeholder="0">
            @if ($errors->has('duration'))
            <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('duration') }}</strong>
            </span>
            @endif
          </div>
          <div class="col-md-2">
            <select id="duration_type" name="duration_type" class="form-control">
              <option value="Minutes"{{old('duration_type') == 'Minutes' || @$data->duration_type == 'Minutes' ? ' selected':''}}>@lang('modules.minutes')</option>
              <option value="Hours"{{old('duration_type') == 'Hours' || @$data->duration_type == 'Hours' ? ' selected':''}}>@lang('modules.hours')</option>
            </select>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="instructor_user_id">@lang('modules.instructor') <code>*</code></label>
          <div class="col-md-9">
            <select id="instructor_user_id" name="instructor_user_id" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" required>
              <option value=""></option>
              @foreach(\App\User::getList(@$data->company_id, false) as $id => $name)
              <option value="{{$id}}"{{old('instructor_user_id') == $id || @$data->instructor_user_id == $id ? ' selected':''}}>{{$name}}</option>
              @endforeach
            </select>
            @if ($errors->has('instructor_user_id'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('instructor_user_id') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="capacity">@lang('modules.capacity')</label>
          <div class="col-md-2">
            <input type="text" id="capacity" name="capacity" class="form-control numberonly{{ $errors->has('capacity') ? ' is-invalid' : '' }}" value="{{old('capacity')?:@$data->capacity}}">
            @if ($errors->has('capacity'))
            <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('capacity') }}</strong>
            </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="description">@lang('modules.description')</label>
          <div class="col-md-9">
            <textarea id="description" name="description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" rows="3">{{old('description')?:@$data->description}}</textarea>
            @if ($errors->has('description'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('description') }}</strong>
              </span>
            @endif
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

@include('_plugins.datepicker')

@push('scripts')
<script>
$(document).ready(function(){

});
</script>
@endpush
