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
      <form action="{{isset($data) ? route('document.update', encrypt($data->id)) : route('document.module.store')}}" method="post" class="form-horizontal" file="true" enctype="multipart/form-data">
        @csrf()
        @isset($data)
          @method('put')
        @endisset
      <div class="card-body">

        <input type="hidden" name="course_id" value="{{$course->id}}">

        <div class="form-group row">
          <label class="col-md-3 col-form-label">{{trans("modules.course")}}</label>
          <div class="col-md-9">
            <input type="text" class="form-control" value="{{$course->title}}" readonly>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="title">{{trans("modules.title")}}</label>
          <div class="col-md-9">
            <input type="text"  name="title" class="form-control" value="{{trans('modules.course_attachment_title')}}" readonly>
          </div>
        </div>


        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="title">{{trans("modules.module_order_no")}}<code>*</code></label>
          <div class="col-md-9">
            <input type="number" id="order_no" name="order_no" class="form-control{{ $errors->has('order_no') ? ' is-invalid' : '' }}" value="{{old('order_no')?: @$data->order_no ?: \App\Module::getNewOrderNo($course->id)}}" required>
            @if ($errors->has('order_no'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('order_no') }}</strong>
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
