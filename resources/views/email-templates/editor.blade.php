@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <form id="submitForm" class="test-form" action="{{route('email-setup.update', [$data->id, $language])}}" method="POST" >
        @csrf()
        @method('put')
        <div class="card-header">
          <div class="row">
            <div class="col-sm-8">
              <i class="fa fa-envelope-open-o"></i> {{$data->template_name}}
            </div>
            <div class="col-sm-4 text-right">
              <button type="button" id="helpBtn"  data-id="{{$data->id}}" class="btn btn-primary btn-md pt-2 ml-4"  >
                <i class="fa fa-question" ></i> {{trans('modules.help')}}
              </button>
              <input type="hidden" id="token" value="{{csrf_token()}}" />
              <input type="hidden" id="slug" value="{{$data->slug}}" />
              <input type="hidden" id="urlvar" value="{{route('email-variable', $data->id)}}" />
              <button type="submit" class="btn btn-primary btn-md pt-2 ml-2"  >
                <i class="icon-check" ></i> {{trans('modules.save')}}
              </button>
            </div>
          </div>
        </div>
        <div class="card-body">
          <textarea class="form-control"  id="summary-ckeditor"  name="body">{{$data->content}}</textarea>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection


@push('modals')
<div id="helpVariable" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document" style="width:80%">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{$data->template_name}} @lang('modules.template_variable_help')</h5>
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
                <div class="form-group row">
                  <label class="col-md-12 col-form-label" id="variable" for="templabe variables"></label>

                </div>
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
    CKEDITOR.replace( 'summary-ckeditor' );
    CKEDITOR.config.height="450px";
</script>
<script>
$(document).ready(function() {

  $("#helpBtn").click(function(){
    $("#helpVariable").modal("show");


    var token = $("#token").val();
    var urlvar = $("#urlvar").val();
    var slug = $("#slug").val();

    $.ajax({
       type: 'POST',
       url: urlvar,
       data: {
           '_token': token,
           'slug': slug
       },
       success: function(msg) {
         $("#templateModal").modal("hide");

               if(msg.msg =='error')
               {
                 swal({
                   title: 'Error occured!',
                   text: '',
                   type: 'success',
                   timer: 2000,
                   showConfirmButton: false,
                 });
               }
               else{
                   $("#variable").html(msg);
             }
           },
       error:function(error){
          $("#templateModal").modal("hide");
             swal({
               title: 'Error occured!',
               text: '',
               type: 'success',
               timer: 2000,
               showConfirmButton: false,
             });

       }
     });
  })
  // $("#variable").html();
});
</script>
@endpush
