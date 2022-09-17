@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-sm-12">
  <div class="card">
    <div class="card-body">
      <div class="col-sm-6 float-left">
         <div class="float-left"><i class="fa fa-user-o fa-4x" aria-hidden="true" style="width:70px"></i>
        </div>
         <div>
           <div>{{$course->title}}</div>
           <input type="hidden" id="course_id" value="{{$encCourseId}}">
         </div>
      </div>
    </div>
  </div>
 </div>
</div>
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <i class="fa fa-chalkboard-teacher"></i> @lang("modules.course_enrolled")
          </div>
        </div>
      </div>
      <div class="card-body">
          <table class="table table-striped table-bordered datatable" id="datatable">
          <thead>
              <tr>
                  <th>@lang("modules.first_name")</th>
                  <th>@lang("modules.last_name")</th>
                  <th>@lang("modules.enrolled_date")</th>
                  <th>@lang("modules.completion_status")</th>
                  <th>@lang("modules.status")</th>
                  <th>@lang("modules.score")</th>
                  <th>@lang("modules.total_time")</th>
                  <th>@lang("modules.operations")</th>
              </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@include('_plugins.datatables')
@push('scripts')
<script>
$(function() {
    var id = $("#user_id").val();
    
    $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('reports.course.users.data', ["id"=>$encCourseId, "filter"=>$filter]) !!}',
        columns: [
            { data: 'first_name', name: 'first_name' },
            { data: 'last_name', name: 'last_name' },
            { data: 'enrol_date', name: 'course_users.enrol_date'},
            { data: 'complete_status', name: 'complete_status' },
            { data: 'satisfied_status', name: 'satisfied_status' },
            { data: 'score', name: 'score' },
            { data: 'total_time', name: 'total_time' },
            { data: 'action', name: 'action', sortable: false, class: 'text-center', width: 220 },

        ]
    });
    $('.datatable').attr('style', 'border-collapse: collapse !important');
});
</script>
@endpush
