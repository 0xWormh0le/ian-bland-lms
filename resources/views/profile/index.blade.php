@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="fa fa-user-cog"></i>
          {{$title}}
      </div>
      <form action="{{route('user.profile.update')}}" method="post" class="form-horizontal">
        @csrf()
      <div class="card-body">

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="first_name">@lang('modules.first_name') <code>*</code></label>
          <div class="col-md-9">
            <input type="text" id="first_name" name="first_name" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" value="{{old('first_name')?:@$user->first_name}}" required autofocus>
            @if ($errors->has('first_name'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('first_name') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="last_name">@lang('modules.last_name')</label>
          <div class="col-md-9">
            <input type="text" id="last_name" name="last_name" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" value="{{old('last_name')?:@$user->last_name}}">
            @if ($errors->has('last_name'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('last_name') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="avatar">@lang('modules.profile_image')</label>
          <div class="col-md-4">
              <div id="croppie_droppie" data-path="images" img-width="200" img-height="200">
                <input type="file" class="input-file" style="display: none">
                <!-- Input For image name to saved into database -->
                <input type="hidden" id="photo" name="photo" class="hidden-file">
                <textarea name="image_base64" id="base64result" style="display:none;"></textarea>
                <!-- Drop Zone -->
                <div class="drop-zone upload-drop-zone" style="max-height: 300px">
                  <!-- Image Zone -->
                    <img class="img-cropped">
                    <!-- Croppie Zone -->
                    <div class="croppie-zone"></div>
                    <!-- Show on Drop Zone -->
                    <span class="show-on-dropzone show-on">
                    @if($user->avatar)
                        <img src="{{asset('storage/avatars/'.$user->avatar)}}" width="200">
                        <br/>
                    @endif
                        <i class="fa fa-cloud-upload" style="font-size: 28px"></i>
                        <br/>
                        @lang('modules.drop_image_text')
                    </span>
                </div>
                <!-- Show on croppie (when cropping) -->
                <div class="show-on-croppie show-on">
                    <button type="button" class="croppie-change-btn"><i class="fa fa-refresh"></i> @lang('modules.change_photo')</button>
                    <button type="button" class="croppie-crop-btn"><i class="fa fa-cut"></i> @lang('modules.crop')</button>
                </div>
                <!-- Show on Crop Before Upload -->
                <div class="show-oncrop-before-upload show-on">
                    <button type="button" class="croppie-edit-btn"><i class="fa fa-edit"></i> @lang('modules.edit')</button>
                </div>
                <!-- Show on Crop After Upload -->
                <div class="show-oncrop-after-upload show-on">
                    <button type="button" class="croppie-change-btn"><i class="fa fa-refresh"></i> @lang('modules.change_photo')</button>
                </div>
            </div>
          </div>
        </div>

      </div>
      <div class="card-footer">
        <button id="submit" type="submit" class="btn btn-primary"><i class="icon-check"></i> @lang('modules.update_profile')</button>
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
</script>
@endpush
