@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-12">
            <i class="fa fa-chalkboard-teacher"></i>
              @lang('modules.my_course_details')
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-6 col-xl-5">
              <img src="{{$course->image ? asset('storage/courses/images/'.$course->image) : asset('img/no-img.jpg')}}" width="100%">
          </div>
          <div class="col-lg-6  col-xl-7 pt-3">

            <h4>{{$course->title}}</h4>

            <p class="text-muted">{!! $course->description !!}</p>

            <div class="d-flex justify-content-between">
              <div class="callout callout-default m-0">
                <small class="text-muted">@lang('modules.enrolled')</small>
                <br>
                <strong>{{datetime_format($courseUser->enrol_date, 'd/m/Y')}}</strong>
              </div>

            @if($course->deadline_date)

              <div class="callout callout-default m-0">
                <small class="text-danger">@lang('modules.deadline')</small>
                <br/>
                <strong>{{$course->deadline_date}}</strong>
              </div>

              @endif

              <div class="callout callout-default m-0">
                <small class="text-muted">@lang('modules.completion')</small>
                <br/>
                @if($course_status == "Completed")
                <strong class="text-success"><i class="fa fa-check-double"></i> @lang('modules.completed')</strong>
                @else
                <strong class="text-danger"><i class="fa fa-exclamation-triangle"></i> @lang('modules.incomplete')</strong>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

@if (count($modules) > 1)
  @if ($course_status != "Completed")
    <div class="card">
      <div class="card-header">
        Select your prefered version of the course
      </div>
      <div class="card-body">
        <div class="border rounded">
    @foreach ($modules as $module)
      @if ($module->type !== 'Document')
          <div class="d-flex p-3 justify-content-between module-select-section">
            <input type="radio" id="module-{{ $module->id }}" class="m-2 radio-module" name="module" />
            <label for="module-{{ $module->id }}" class="mr-3 flex-grow-1">
              <p class="mb-1" style="font-size: larger;">{{ $module->title }}</p>
              <p class="m-0">{!! $module->detail['detail']->description !!}</p>
            </label>
            @include('my-courses.modules.launch', [
              'module'      => $module,
              'course'      => $course,
              'detail'      => $module->detail['detail'],
              'courseUser'  => $module->detail['courseUser'],
              'result'      => $module->detail['result'],
              'launch'      => $module->detail['launch'],
              'isExpired'   => $module->detail['isExpired'],
              'previousModule' => $module->detail['previousModule'],
            ])
          </div>
      @endif
    @endforeach
        </div>
      </div>
    </div>
  @endif
    <div class="card">
      <div class="card-body">
        @foreach ($modules as $module)
          @if ($module->type !== 'Document')
            @if (isset($module->detail['result']) && count($module->detail['result']->histories) > 0)
              @include('my-courses.modules.history', [
                'histories' => $module->detail['result']->histories,
                'title' => $module->title
              ])
            @endif
          @endif
        @endforeach
      </div>
    </div>
@else
  @foreach ($modules as $module)
    @if ($module->type !== 'Document')
      @include('my-courses.modules.elearning', [
        'module'      => $module,
        'course'      => $course,
        'detail'      => $module->detail['detail'],
        'courseUser'  => $module->detail['courseUser'],
        'result'      => $module->detail['result'],
        'launch'      => $module->detail['launch'],
        'isExpired'   => $module->detail['isExpired'],
        'previousModule' => $module->detail['previousModule'],
      ])
    @endif
  @endforeach
@endif

{{--
    <div class="card">
          <div class="card-header"><i class="fa fa-swatchbook"></i> @lang('modules.modules')</div>
          <div class="card-body">
           <div class="col-md-12 mt-3">
            <table class="table table-responsive-sm table-hover table-outline mb-0">

              <tbody>
                @php
                 $hasDocument = 0;
                @endphp
                @foreach($modules as $module)
                 @if($module->type !== 'Document')
                <tr>
                  <td class="text-center" width="80">
                    <a href="{{ route('my-courses.module', ['course'=>$course->slug, 'module' => $module->slug]) }}">
                      <div class="circle-icon">
                        @if($module->type == 'Elearning')
                        <i class="fa fa-money-check" title="@lang('modules.elearning')"></i>
                        @endif
                      </div>
                    </a>
                  </td>
                  <td>
                    <a href="{{ route('my-courses.module', ['course'=>$course->slug, 'module' => $module->slug]) }}">
                      <div>{{ $module->title }}</div>
                      <div class="text-muted">
                        @php
                          $result = \App\CourseResult::getModuleResult($courseUser->id, $module->id);
                        @endphp
                        @if($result->complete_status == 'Completed')
                          <small class="text-success"><i class="fa fa-check-circle"></i>  @lang('modules.completed')</small>
                        @elseif($result->complete_status == '')
                          <small class="text-muted">@lang('modules.not_started')</small>
                        @else
                          <small class="text-danger">@lang('modules.incomplete')</small>
                        @endif
                      </div>
                    </a>
                  </td>

                </tr>
                 @else
                   @php
                     $hasDocument = 1;
                   @endphp
                 @endif
                @endforeach
              </tbody>
            </table>
          </div>
          </div>
    </div>

        @if ($hasDocument == 1 && false)
          <div class="col-md-12 mt-3">
            <table class="table table-responsive-sm table-hover table-outline mb-0">
              <thead class="thead-light">
                <tr>
                  <th colspan="3"></th>
                </tr>
              </thead>
              <tbody>
              @if(count($documets) >0)
                @foreach($documets as $document)
                <tr>
                  <td class="text-center" width="80">
                      <div class="circle-icon">
                       <i class="fa fa-file-text-o" title="@lang('modules.document')"></i>
                      </div>
                    </a>
                  </td>
                  <td>
                    <a href="#">
                      <div>{{ $document->title }}</div>
                      <div class="text-muted">
                        <small class="text-muted">{{ $document->filename }} ({{ $document->filesize>0?(round($document->filesize/1000)).' KB':'' }})</small>
                      </div>
                    </a>
                  </td>
                  <td class="text-center" width="80">
                    <a href="{{ route('document.download', encrypt($document->id)) }}">
                      <div class="circle-icon bg-success">
                       <i class="fa fa-download" title="@lang('modules.document')"></i>
                      </div>
                    </a>
                  </td>
                </tr>
                @endforeach
              @endif
              </tbody>
            </table>
          </div>
         @endif
--}}

  </div>
</div>
@endsection

@push('scripts')
<script src="{{ mix('scripts/my-courses/details.js') }}"></script>
@endpush
