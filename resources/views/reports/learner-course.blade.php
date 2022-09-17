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
           <div>{{$user->first_name}} {{$user->last_name}}</div>
           <input type="hidden" id="user_id" value="{{$encUserId}}">
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
                  <th>@lang("modules.title")</th>
                  <th>@lang("modules.module")</th>
                  <th>@lang("modules.enrolled_date")</th>
                  <th>@lang("modules.complete_status")</th>
                  <th>@lang("modules.score")</th>
                  <th>@lang("modules.total_time")</th>
                  <th>@lang("modules.completion_date")</th>
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
        ajax: '{!! route('reports.learner.course.data', [$encUserId, $filter]) !!}',
        columns: [
            { data: 'title', name: 'title' },
            { data: 'mod_title', name: 'module.title' },
            { data: 'enrol_date', name: 'course_users.enrol_date' },
            { data: 'complete_status', name: 'course_results.complete_status', class:'text-center' },
            { data: 'score', name: 'course_results.score'},
            { data: 'total_time', name: 'course_results.total_time' },
            { data: 'completion_date', name: 'course_results.completion_date' },

        ]
    });
    $('.datatable').attr('style', 'border-collapse: collapse !important');
});
</script>
@endpush
