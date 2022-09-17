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
      <form action="{{isset($data) ? route('category.update', $data->id) : route('category.store')}}" method="post" class="form-horizontal" file="true" enctype="multipart/form-data">
        @csrf()
        @isset($data)
          @method('put')
        @endisset
      <div class="card-body">

        <div class="form-group row">
          <label class="col-md-1 col-form-label" for="company_name">@lang("modules.title") <code>*</code></label>
          <div class="col-md-6">
            <input type="text" id="title" name="title" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" value="{{old('title')?:@$data->title}}" required autofocus>
            <input type="hidden" name="parent" value="{{isset($data)?$data->parent:$id}}" />
            @if ($errors->has('title'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('title') }}</strong>
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
