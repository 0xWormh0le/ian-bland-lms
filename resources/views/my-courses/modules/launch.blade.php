@if ($launch)
  @if (@$result->complete_status != 'Completed')
    <div class="text-right launch-button-section">
    @if ($module->elearning->course_stream)
      <button class="btn btn-primary btn-lg streamlaunch" style="padding-top:20px" data-url="{{route('my-courses.stream.presenter', $course->slug)}}" data-moduleid="{{$module->id}}">
        <i class="fa fa-play fa-2x"></i>
        <br/>
        <span>@lang('modules.launch')</span>
      </button>
    @else
    <!--    <button class="btn btn-success btn-lg launch" data-id="{{$courseUser->id}}" data-moduleid="{{$module->id}}" data-type="{{$module->type}}" data-url="{{route('my-courses.launch', $course->slug)}}" style="padding-top:20px">
        <i class="fa fa-play fa-2x"></i>
        <br/>
        <span>@lang('modules.launch')</span>
      </button> -->

      <button
        class="btn btn-success border border-light launch py-2 px-3"
        data-open-url="{{ route('elearning.scorm.deliver', ['id' => $detail->scorm_id,'user' => Auth::id(), 'module_id' => $module->id]) }}"
      >
        <div><i class="fa fa-play fa-2x mt-1"></i></div>
        <div>@lang('modules.launch')</div>
      </button>
    @endif
    </div>
  @endif
@else
  <div class="alert alert-danger text-center launch-button-section">
  @if ($isExpired)
    <i class="fa fa-times-circle"></i> @lang('modules.schedule_is_closed')
  @elseif ($course->config->transversal_rule == 'sequential' && $previousModule)
    <i class="fa fa-exclamation-triangle"></i>
    @lang('modules.module')
    <strong>{{$previousModule->title}}</strong>
    @lang('modules.must_be_completed_first')
  @endif
  </div>
@endif