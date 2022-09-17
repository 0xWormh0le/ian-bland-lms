@extends('layouts.app')

@section('title', $title)

@section('content')

<input type='hidden' id='language' value="{{ $language }}"/>

<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
            <i class="fa fa-envelope-open-o"></i> {{$title}}
          </div>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-striped table-bordered datatable" id="datatable" data-url="{{ route('email-setup.data') }}">
          <thead>
            <tr>
              <th>@lang('modules.template_name')</th>
              <th>@lang('modules.subject')</th>
              <th>@lang('modules.date_updated')</th>
              <th>@lang('modules.action')</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection


@push('modals')
<div id="templateModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document" style="width:80%">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">@lang('modules.email_template')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                 @lang("modules.edit_details")
              </div>
              <div class="card-body">
                  <div class="alert alert-danger" id="error_box" style="display:none">
                     <strong>@lang("messages.error")!</strong> <span id="error"></span>
                  </div>
                <div class="form-group row">
                  <label class="col-md-3 col-form-label" for="old_password">@lang('modules.template_name')<code>*</code></label>
                  <div class="col-md-9">
                    <input type="hidden" id="token" value="{{csrf_token()}}">
                    <input type="hidden" id="template_id" value="">
                    <input type="hidden" id="update_url" value="{{route('email-template.ajax.update')}}">
                    <input type="text" id="template_name" name="template_name" class="form-control" value=""  autofocus>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-md-3 col-form-label" for="old_password">@lang('modules.subject')<code>*</code></label>
                  <div class="col-md-9">
                    <input type="text" id="subject" name="subject" class="form-control" value=""  >
                  </div>
                </div>
              </div>
              <div class="card-footer text-center">
                <button type="button" id="ajaxSave"  class="btn btn-sm btn-block btn-primary" title="Save">@lang('modules.save')</button>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
@endpush

@include('_plugins.datatables')
@push('scripts')
<script src="{{ mix('scripts/email-setup/index.js') }}"></script>
<script>
function editTemplate(id)
{
  $("#template_id").val(id);
  $("#template_name").val("");
  $("#subject").val("");
  var url = "{{route('email-get')}}";
  getTemplateDetails(url,id, "{{csrf_token()}}");

}

var getTemplateDetails = function(url,id, token)
{

  $.ajax({
     type: 'POST',
     url: url,
     data: {
         '_token': token,
         'id':id
      },
     success: function(msg) {

           $("#template_name").val(msg.template_name);
           $("#subject").val(msg.subject);
           $("#templateModal").modal("show");
         },
     error:function(error){
       swal({
         title: 'Error occured!',
         text: '',
         type: 'success',
         timer: 2000,
         showConfirmButton: false,
       });
     }
   });
}
</script>
@endpush
