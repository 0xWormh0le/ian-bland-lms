@extends('layouts.app')

@section('content')


<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="far fa-star"></i> @lang('modules.team_member_results')
      </div>
      <div class="card-body">
        <table class="table table-responsive-sm table-hover table-outline mb-0 datatable" id="teamtable">
          <thead class="thead-light">
            <tr>
              <th class="text-center" width="80">
                <i class="icon-people"></i>
              </th>
              <th>@lang('modules.name_of_user')</th>
              <th class="text-center" width="200">@lang('modules.average_score')</th>
              <th class="text-center" width="300">@lang('modules.completed_courses_by_team_member')</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
            <tr>
              <td class="text-center">
               @if($user->avatar)
                  <img class="img-avatar" src="{{\Storage::disk('public')->url('avatars/'.$user->avatar)}}" alt="@lang('modules.avatar')">
                @else
                  <img class="img-avatar" src="{{route('user.avatar.initial', $user->id)}}" alt="@lang('modules.avatar')">
                @endif
              </td>
              <td>
                {{$user->first_name}} {{$user->last_name}}
              </td>
              <td class="text-center">
              @php
                $avg = \App\CourseResult::select(\DB::raw('avg(score) as avg_score'))
                                  ->leftJoin('course_users', 'course_users.id', '=', 'courseuser_id')
                                  ->leftJoin('courses', 'courses.id', '=', 'course_users.course_id')
                                  ->where('course_users.user_id', $user->id)
                                  ->first();
              @endphp
                {{number_format(@$avg->avg_score ?: 0, 2)}}
              </td>
              <td>
                @php
                  $completed = \App\CourseUser::select('course_users.id')
                                ->join('courses', 'courses.id', '=', 'course_users.course_id')
                                ->where('user_id', $user->id)
                                ->where('completed', true)
                                ->count();
                  $taken = \App\CourseUser::select('course_users.id')
                                ->join('courses', 'courses.id', '=', 'course_users.course_id')
                                ->where('user_id', $user->id)
                                ->count();
                  if($taken > 0)
                    $percent = round(($completed/$taken)*100, 2);
                  else
                    $percent = 0;
                @endphp

                <div class="clearfix">
                  <div class="float-left">
                    <strong>{{$percent}}%</strong>
                  </div>
                  <div class="float-right">
                    <small class="text-muted">{{$completed}} @lang('modules.completed_of') {{$taken}}</small>
                  </div>
                </div>
                <div class="progress progress-xs">
                  <div class="progress-bar bg-success" role="progressbar" style="width: {{$percent}}%" aria-valuenow="{{$percent}}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@include('_plugins.datatables')



@include('_plugins.chartjs')
@push('scripts')
<script>
$(function () {
  var table = $('#teamtable').DataTable({
    order: [[3, 'desc']]
  });
  $('.datatable').attr('style', 'border-collapse: collapse !important');

});
</script>
@endpush
