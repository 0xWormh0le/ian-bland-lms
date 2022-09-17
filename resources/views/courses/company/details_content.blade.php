
@if ($show_overview)
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="icon-info"></i>
          {{$title}}
      </div>
      <div class="card-body">
        <div class="bd-example">
          <dl class="row">
            <dd class="col-sm-3">@lang('modules.course_title')</dd>
            <dt class="col-sm-9"><a href="{{route('courses.show', $data->course->slug)}}">{{$data->course->title}}</a></dt>

            <dd class="col-sm-3">@lang('modules.company')</dd>
            <dt class="col-sm-9">{{$data->company->company_name}}</dt>

            <dd class="col-sm-3">@lang('modules.enrolled_date')</dd>
            <dt class="col-sm-9">{{datetime_format($data->updated_at, 'd F Y H:i')}}</dt>
          </dl>
        </div>
      </div>
    </div>
  </div>
@endif

{{--
  <!-- <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="icon-calendar"></i>
          @lang('modules.modules_schedule')
      </div>
      <div class="card-body">
        <table class="table table-bordered datatable" id="scheduleTable">
          <thead>
            <tr>
              <th width="80">@lang('modules.module')</th>
              <th>@lang('modules.title')</th>
              <th width="120">@lang('modules.schedule')</th>
              <th width="60">@lang('modules.duration')</th>
              <th width="60">@lang('modules.capacity')</th>
              <th width="120">@lang('modules.instructor')</th>
              <th>@lang('modules.description')</th>
              <th width="60"></th>
            </tr>
          </thead>
          <tbody> -->
--}}
            @php
             $has_document = 0 ;
            @endphp
            @foreach($data->course->modules as $module)
              @php
                if($module->type == 'Document')
                 $has_document = 1;
              @endphp
              @if($module->type !== 'Elearning' && $module->type !== 'Document')
              @php
                $info = $data->getModule($module->type, $module->id);
              @endphp

{{--              
              <!-- <tr>
                <td>{{session('moduleLabel')[strtolower($module->type)]}}</td>
                <td>{{$module->title}}</td>
                <td>{{dateformat(@$info->start_date, 'l, j M Y')}}<br/> {{timeformat(@$info->start_time, 'g:i A e')}}</td>
                <td>{{@$info->duration ? $info->duration.' '.$info->duration_type : ''}}</td>
                <td>{{@$info->capacity}}</td>
                <td>{{@$info->instructor->first_name}} {{@$info->instructor->last_name}}</td>
                <td>{{@$info->description}}</td>
                <td align="center">
                  {!! show_button('update', 'courses.companies.schedule.edit', ['course' => $data->course->slug, 'company' => $data->company->slug, 'module_id' => $module->id], validate_role('courses.index')) !!}
                </td>
              </tr>-->
--}}
              @endif
            @endforeach
{{--            
<!--            
          </tbody>
        </table>
      </div>
    </div>
  </div>
-->
--}}
@if($has_document ==1)
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header col-sm-12">
        <div class="row">
          <div class="col-sm-6 float-left">
          <i class="fa fa-paperclip"></i>
            @lang('modules.course_attachment')
          </div>
          <div class="col-sm-6 float-right text-right">
            <button id="add_attachment" class="btn btn-primary">
              <i class="icon-plus"></i>&nbsp;@lang("modules.new_document")
            </button>
          </div>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-bordered datatable" id="attachmentTable">
          <thead>
            <tr>
              <th>@lang('modules.title')</th>
              <th>@lang('modules.type')</th>
              <th>@lang('modules.date_added')</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @push('modals')
  <div id="attachmentModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">@lang('modules.course_attachment')</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="error" class="alert alert-danger d-none"></div>
          <input type="hidden" id="course_id" value="{{$data->course->id}}">
          <input type="hidden" id="document_id" value="0">

          <div class="form-group row">
            <label class="col-md-3 col-form-label">@lang("modules.course")</label>
            <div class="col-md-9">
              <input type="text" class="form-control" value="{{$data->course->title}}" readonly>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-3 col-form-label" for="title">@lang("modules.title") <code>*</code></label>
            <div class="col-md-9">
              <input type="text" id="title" name="title" class="form-control" value=""  autofocus>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-md-3 col-form-label" for="description">@lang("modules.description")</label>
            <div class="col-md-9">
              <textarea id="description" name="description" class="form-control" rows="5"></textarea>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-md-3 col-form-label" for="upload_file">@lang("modules.file") <code>*</code></label>
            <div class="col-md-9">
              <input type="file" name="upload_file" id="upload_file" >
              <div id="file_option" class="d-none"><div id="file" class="float-left"></div> &nbsp;
          <!--    <a href="javascript:deleteAttachFile()" id="delete_file" data-url="" class="btn btn-sm btn-danger">
                <i class="icon-trash"></i>
              </a> --></div>
            </div>
          </div>


          <div class="modal-footer">
            <button type="button" id="attachmentSubmit" class="btn btn-md btn-primary" data-id="" data-type="" data-action="">
              <i class="icon-plus"></i> @lang('modules.add')
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>
  @endpush
@endif
  @include('courses.company.teams')
  @include('courses.company.users')



@push('scripts')
<script>
var courseAttachments = null;
$(document).ready(function() {
    $('.datatable').attr('style', 'border-collapse: collapse !important');

    $("#scheduleTable").DataTable();

    var unenrolledCompanies = $('#unenrolledCompanies').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('courses.companies.unenrolled', $data->id) !!}',
        columns: [
            { data: 'company_name', name: 'company_name' },
            { data: 'action', name: 'action', sortable:false, class:'text-center', width:80 },
        ],
    });

     courseAttachments = $('#attachmentTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('course.attachments', $data->course->id) !!}',
        columns: [
            { data: 'title', name: 'title' },
            { data: 'filetype', name: 'filetype' },
            { data: 'created_at', name: 'created_at', render: renderColumnDate },
            { data: 'action', name: 'action', sortable:false, class:'text-center', width:80 }
        ],
    });


    $("#enrollcompany").on("click", function(){
      unenrolledCompanies.ajax.reload();
      $("#companiesModal").modal("show");
    });

    $(document).on('click', '.enrol', function (e) {
      e.preventDefault();
      var $this = $(this);
      var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> enrolling...';
      if ($(this).html() !== loadingText) {
        $this.data('original-text', $(this).html());
        $this.html(loadingText);
      }
      $.ajax({
        type: 'POST',
        url: "{{ route('courses.companies.enroll', $data->id) }}",
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'action': $this.data('action'),
            'id': $this.data('id'),
            'company_id': $this.data('id'),
            'course_id': "{{$data->id}}",
        },
        success: function(msg) {
          if(msg=='error')
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
            swal({
              title: msg,
              text: '',
              type: 'success',
              timer: 2000,
              showConfirmButton: false,
            });
            unenrolledCompanies.ajax.reload();
            companiesTable.ajax.reload();
          }
        }
      });

    });

  $('#add_attachment').on('click', function(){
      $("#attachmentModal").modal("show");
      $("#title").val('');
      $("#description").val('');
      $("#upload_file").val('');
      $("#file_option").addClass('d-none');
      $("#file").html('');
      $("#upload_file").removeClass('d-none');
      $("#document_id").val('0');
      $("#error").html('');
      $("#error").addClass('d-none');
      $("#error").removeClass('d-block');



  });

  $('#attachmentSubmit').on('click', function(){

    var document_id =$("#document_id").val();
    var course_id = $("#course_id").val();
    var title = $("#title").val();
    var description = $("#description").val();
    var upload_file = $("#upload_file").prop('files')[0];
    var url = "{{ route('document.store')}}";

    var form_data = new FormData();
    form_data.append("id", document_id);
    form_data.append("course_id", course_id);
    form_data.append("title", title);
    form_data.append("description", description);
    form_data.append("upload_file", upload_file);
    form_data.append("_token", $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
      type: 'POST',
      url: url,
      data: form_data,
      dayaType:'json',
      processData: false,
      contentType: false,
      enctype: "multipart/form-data",
      success: function(msg) {

        if(msg=='error')
        {
          swal({
            title: 'Error occured!',
            text: '',
            type: 'success',
            timer: 2000,
            showConfirmButton: false,
          });
          $("#attachmentModal").modal("hide");


        }
        else{

          $("#error").addClass('d-none');

        if(!msg.success)
        {
          msg = JSON.parse(msg);
          if(!msg.success && !$.isEmptyObject(msg))
             {

               swal({
                 title: 'Error occured!',
                 text: '',
                 type: 'success',
                 timer: 2000,
                 showConfirmButton: false,
               });
               var error = "";
               $.each( msg, function( key, value ) {
                 error += value + "<br>";
                });
                $("#error").removeClass('d-none');
                $("#error").html(error);
             }
           }
           else{
                swal({
                   title: msg.success,
                   text: '',
                   type: 'success',
                   timer: 2000,
                   showConfirmButton: false,
                 });
                 $("#error").addClass('d-none');
                 $("#attachmentModal").modal("hide");
                  courseAttachments.ajax.reload();
              }
         }
      },
      error: function(error) {

        swal({
          title: 'Error occured!',
          text: '',
          type: 'success',
          timer: 2000,
          showConfirmButton: false,
        });
      },
     });
   });



});


   function deleteAttachFile(id)
   {
      if (confirm('Are you sure ?')) {
        var url = $('#delete_'+id).data('url');

        $.ajax({
          type: 'GET',
          url: url,
          data: {"_token": $('meta[name="csrf-token"]').attr('content')},
          dayaType:'json',
          processData: false,
          contentType: false,
          success: function(msg) {
            if(msg=='error')
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
              courseAttachments.ajax.reload();
              swal({
                title: msg.msg,
                text: '',
                type: 'success',
                timer: 2000,
                showConfirmButton: false,
              });

            }
          },
          error: function(error) {
            swal({
              title: 'Error occured!',
              text: '',
              type: 'success',
              timer: 2000,
              showConfirmButton: false,
            });
          },
         });
    }
   }

   function showAttachDetails(id)
  {
    var url = $('#show_'+id).data('url');

    $.ajax({
      type: 'GET',
      url: url,
      data: {"_token": $('meta[name="csrf-token"]').attr('content')},
      dayaType:'json',
      processData: false,
      contentType: false,
      success: function(msg) {
        if(msg=='error')
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

           $("#course_id").val(msg.course_id);
           $("#title").val(msg.title);
           $("#description").val(msg.description);
          // $("#delete_file").data('url', msg.delete_url);
           $("#file").html("<a href='"+msg.filepath+"'>"+msg.filename+"</a>");
           $("#document_id").val(msg.id);

          if(msg.filepath !="")
          $("#upload_file").addClass('d-none');

          $("#file_option").removeClass('d-none');
          $("#file_option").addClass('d-block');

          $("#attachmentModal").modal("show");

        }
      },
      error: function(error) {
        swal({
          title: 'Error occured!',
          text: '',
          type: 'success',
          timer: 2000,
          showConfirmButton: false,
        });
      },
     });

  }





</script>
@endpush
