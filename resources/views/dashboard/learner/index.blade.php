@extends('layouts.app')

@section('content')
@if($dueCourses > 0)
<div class="alert alert-danger alert-dismissible">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <i class="fa fa-times-circle-o"></i> @lang("modules.overdue_msg")
</div>
@endif

<div class="card-group mb-4">
    <div class="card">
        <div class="card-body">
            <div class="h1 text-muted text-right mb-4">
                <i class="fa fa-tags"></i>
            </div>
            <div class="text-value">{{ $enrolledCourses }}</div>
            <small class="text-muted text-uppercase font-weight-bold">@lang('modules.enrolled_courses')</small>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="h1 text-muted text-right mb-4">
                <i class="fa fa-check-double"></i>
            </div>
            <div class="text-value">{{ $completedCourses }}</div>
            <small class="text-muted text-uppercase font-weight-bold">@lang('modules.completed_courses')</small>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="h1 text-muted text-right mb-4">
                <i class="fa fa-tasks"></i>
            </div>
            <div class="text-value">{{ $inProgressCourses }}</div>
            <small class="text-muted text-uppercase font-weight-bold">@lang('modules.inprogress_courses')</small>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="h1 text-muted text-right mb-4">
                <i class="fa fa-money-check"></i>
            </div>
            <div class="text-value">{{ $notStarted }}</div>
            <small class="text-muted text-uppercase font-weight-bold">@lang('modules.not_started')</small>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="h1 text-muted text-right mb-4">
                <i class="fa fa-clipboard-list"></i>
            </div>
            <div class="text-value">{{ $avg_score }}</div>
            <small class="text-muted text-uppercase font-weight-bold">@lang('modules.avg_score')</small>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="h1 text-muted text-right mb-4">
                <i class="fa fa-stopwatch"></i>
            </div>
            <div class="text-value">{{ $avg_time }}</div>
            <small class="text-muted text-uppercase font-weight-bold">@lang('modules.avg_completed_time')</small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        {!! $welcomeTemplate !!}
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
</div>

@endsection
