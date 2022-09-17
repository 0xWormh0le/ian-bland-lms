@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="fa fa-award"></i>
          {{$title}}
      </div>
      <form action="{{route('client-certificate-config.update')}}" method="post" class="form-horizontal">
        @csrf()
      <div class="card-body">

        <div class="col-sm-12">
          <div class="form-group row">
            <label class="col-sm-3 pl-0" for="validity_duration">@lang('modules.validity_duration')</label>
            <div class="col-sm-2 pl-2">
              <div class="input-group">
                <input type="text" id="validity_years" name="validity_years" class="form-control numberonly" maxlength="2" value="{{old('validity_years')?:($certificate?$certificate->validity_years:'')}}">
                <div class="input-group-append">
                  <span class="input-group-text">
                    @lang('modules.years')
                  </span>
                </div>
              </div>
            </div>
            <div class="col-sm-2">
              <div class="input-group">
                <input type="text" id="validity_months" name="validity_months" class="form-control numberonly" maxlength="2" value="{{old('validity_months')?:($certificate?$certificate->validity_months:'')}}">
                <div class="input-group-append">
                  <span class="input-group-text">
                    @lang('modules.months')
                  </span>
                </div>
              </div>
            </div>
            <div class="col-sm-2">
              <div class="input-group">
                <input type="text" id="validity_weeks" name="validity_weeks" class="form-control numberonly" maxlength="2" value="{{old('validity_weeks')?:($certificate?$certificate->validity_weeks:'')}}">
                <div class="input-group-append">
                  <span class="input-group-text">
                    @lang('modules.weeks')
                  </span>
                </div>
              </div>
            </div>
            <div class="col-sm-2">
              <div class="input-group">
                <input type="text" id="validity_days" name="validity_days" class="form-control numberonly" maxlength="3" value="{{old('validity_days')?:($certificate?$certificate->validity_days:'')}}">
                <div class="input-group-append">
                  <span class="input-group-text">
                    @lang('modules.days')
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.signer')</label>
          <div class="col-sm-9">
            <input type="text" id="signer" name="signer" class="form-control" value="{{old('signer')?:($certificate?$certificate->signer:'')}}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3">@lang('modules.position')</label>
          <div class="col-sm-9">
            <input type="text" id="position" name="position" class="form-control" value="{{old('position')?:($certificate?$certificate->position:'')}}">
          </div>
        </div>

        <div class="form-group row" style="display:none;">
          <label class="col-md-3 col-form-label" for="avatar">@lang('modules.signature')</label>
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
                    @if($certificate && $certificate->signature)
                        <img src="{{asset('storage/certificates/signature/'.$certificate->signature)}}" width="200">
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
        @include('components.form_submit')
      </div>
      </form>
    </div>
  </div>
</div>
@endsection
@include('_plugins.datepicker')
@include('_plugins.croppie-droppie')

@push('scripts')
<script>
$('form').submit(function() {
  if($(".croppie-crop-btn").is(":visible"))
    $(".croppie-crop-btn").click();
});
</script>
@endpush
