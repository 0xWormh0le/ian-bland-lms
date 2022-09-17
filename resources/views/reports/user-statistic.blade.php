@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-sm-12">
  <div class="card">
    <div class="card-body">
    <div class="col-sm-6 float-left">
       <div class="float-left mr-2"><i class="fa fa-chalkboard-teacher fa-4x " aria-hidden="true" style="width:70px"></i>
      </div>
       <div>
         <div><b>@lang('modules.name') :</b> {{$user->first_name}} {{$user->last_name}} </div>
         <div><b>@lang('modules.course') :</b> {{$course->title}}</div>
          <div><b>@lang('modules.enrolled_date') :</b> {{$enrolDate}}</div>
      </div>
    </div>

    <div class="col-sm-6 float-right">
      <div class="btn-group float-right">
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
                  <th>@lang("modules.module")</th>
                  <th>@lang("modules.completion_status")</th>
                  <th>@lang("modules.status")</th>
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

    $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('reports.course.users.history.data', ["id" => $userId, "course_id" => $courseId, "option" => $filter]) !!}',
        columns: [
            { data: 'title', name: 'title' },
            { data: 'complete_status', name: 'complete_status' },
            { data: 'satisfied_status', name: 'satisfied_status' },
            { data: 'score', name: 'score' },
            { data: 'total_time', name: 'total_time' },
            { data: 'completion_date', name: 'completion_date' },
        ]
    });
    $('.datatable').attr('style', 'border-collapse: collapse !important');
});
</script>
@endpush
