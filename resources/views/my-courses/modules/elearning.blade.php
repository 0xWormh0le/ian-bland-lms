
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-12">
            <i class="far fa-comments"></i>
             {{ $module->title }}
          </div>
        </div>
      </div>

      <div class="card-body">
        <div class="d-flex mb-5">
          <div class="circle-icon text-center mr-4 d-flex" style="min-width:2em; width:2em; height:2em">
            <i class="fa fa-money-check m-auto" title="@lang('modules.elearning')"></i>
          </div>

          <div>
            <h4>{{ $module->title }}</h4>

            <p class="my-3">{!! $detail->description !!}</p>

            @if(@$result->complete_status == 'Completed')
              <span class="text-success mr-5"><i class="fa fa-check-circle"></i>  @lang('modules.completed')</span>
            @elseif(@$result->complete_status == '')
              <span class="text-muted mr-5">@lang('modules.not_started')</span>
            @else
              <span class="text-danger mr-5">@lang('modules.incomplete')</span>
            @endif

            @if(@$result->satisfied_status == 'Passed')
              <span class="text-success"><i class="fa fa-check-circle"></i>  {!! ucwords(@$result->satisfied_status) !!}</span>
            @else
              <span class="text-danger">{!! ucwords(@$result->satisfied_status) !!}</span>
            @endif

            {{--
            <small>@lang('modules.completion') : </small><strong>{!! ucwords(@$result->complete_status) ?: 'N/A' !!}</strong>
            <br/>
            <small>@lang('modules.status') : </small><strong>{!! ucwords(@$result->satisfied_status) ?: 'N/A' !!}
            --}}
            </strong>
          </div>

          <div class="ml-auto pl-3">
            @include('my-courses.modules.launch', [
              'result'      => $result,
              'module'      => $module,
              'course'      => $course,
              'detail'      => $detail,
              'courseUser'  => $courseUser,
              'launch'      => $launch,
              'isExpired'   => $isExpired,
              'previousModule' => $previousModule,
            ])
          </div>
        </div>

        @if (isset($result->histories) && count($result->histories) > 0)
          @include('my-courses.modules.history', [
            'histories' => $result->histories,
            'title' => ''
          ])
        @endif
      </div>
    </div>

