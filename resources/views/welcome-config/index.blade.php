@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col">
    <div class="card">
      <form action="{{ route('welcome-config.update') }}" method="POST">
        @csrf()

        <div class="card-header">
          <div class="row">
            <div class="col">{{ trans('controllers.welcome_screen') }}</div>
            <div class="col text-right">
              <button type="button" id="help-btn" class="btn btn-primary">
                <i class="fa fa-question" ></i> {{ trans('modules.help') }}
              </button>
              <button type="submit" class="btn btn-primary">
                <i class="icon-check" ></i> {{ trans('modules.save') }}
              </button>
            </div>
          </div>
        </div>

        <div class="card-body">
          <textarea class="form-control d-none" id="summary-ckeditor" name="content">{{ $content }}</textarea>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('modals')
<div id="help-variable" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document" style="width:80%">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          {{ trans('controllers.welcome_screen') }}
          @lang('modules.template_variable_help')
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                @lang("modules.variables")
              </div>
              <div class="card-body">
                <p>@MY_COURSES</p>
                <p>@MY_CERTIFICATES</p>
                <p>@MY_SCHEDULES</p>
                <p>@MY_TICKETS</p>
                <p>@SECURITY_TRAIN</p>
                <p>@SUBMIT_TICKET</p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
@endpush

@push('scripts')
<script src="{{ asset('vendors/ckeditor/ckeditor.js') }}"></script>
<script>
  CKEDITOR.replace('summary-ckeditor');
  CKEDITOR.config.height="450px";
</script>
<script>
$(document).ready(function() {
  $("#help-btn").click(function() {
    $("#help-variable").modal("show");
  });
});
</script>
@endpush
