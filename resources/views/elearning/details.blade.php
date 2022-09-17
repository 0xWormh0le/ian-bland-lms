
  @if ($modules->count() == 0)
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-6">
              <i class="icon-info"></i> @lang('modules.elearning')
            </div>

        @if (@$data->created_by == auth()->user()->id || auth()->user()->isSysAdmin())
            <div class="col-6 text-right">
              <a href="{{route('elearning.create', $data->slug)}}" class="btn btn-primary">
                <i class="icon-plus"></i> @lang('modules.new_module')
              </a>
            </div>
        @endif
          </div>
        </div>

        <div class="card-body text-center">
          No module
        </div>
      </div>
    </div>
  @else
  
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-6">
            <i class="icon-info"></i> @lang('modules.elearning')
          </div>

        @if (@$data->created_by == auth()->user()->id || auth()->user()->isSysAdmin())
            <div class="col-6 text-right">
              <a href="{{route('elearning.create', $data->slug)}}" class="btn btn-primary">
                <i class="icon-plus"></i> @lang('modules.add_lang_module')
              </a>
            </div>
        @endif

        </div>
      </div>

      <div class="card-body">

        <div class="bd-example">
  @foreach ($modules as $module)
          <dl class="row">
            <dt class="col-sm-6">{{$module->title}}</dt>            

          {{--
            <dd class="col-sm-3">@lang('modules.course')</dd>
            <dt class="col-sm-9">{{$module->course->title}}</dt>
            <dd class="col-sm-3">@lang('modules.module_sequence')</dd>
            <dt class="col-sm-9">#{{$module->order_no}}</dt>
            <dd class="col-sm-3"></dd>
            <dt class="col-sm-9">
          --}}
            <dd class="col-sm-6 text-right">
            @php
              $course = $module->course;
              $scormService = new \App\SCORMDispatchAPI\SCORMDispatchService;
              $courseService = $scormService->getCourseService();
              $redirectUrl = route('elearning.show', ['course'=>$course->slug, 'module'=>$module->slug]);
              $url = $courseService->GetPreviewUrl(@$module->elearning->scorm_id, $redirectUrl)
            @endphp

            @if (auth()->user()->isSysAdmin() || @$module->created_by== \Auth::id())
                {!! show_button('update', 'elearning.edit', ['course'=>$module->course->slug, 'slug'=>$module->slug], validate_role('courses.create'), 'btn-sm') !!}
                {!! show_button('remove', 'elearning.destroy', encrypt($module->id), validate_role('courses.create'), 'btn-sm') !!}
            @endif

            @isset($url)
              <a class="btn btn-success btn-sm" href="#"
                  onclick="launch('{{ route('elearning.scorm.deliver', ['id' => @$module->elearning->scorm_id,'user' => Auth::id()]) }}')">
                @lang('modules.preview')
              </a>

            @endisset
            </dd>
          </dl>
  @endforeach
        </div>

        

        
      </div>
    </div>
  </div>


@push('scripts')
<script>
    function launch(url) {
      var leftPosition, topPosition;
      var width = 1024;
      var height = 768;
    
      leftPosition = (window.screen.width / 2) - ((width / 2) + 10);
      topPosition = (window.screen.height / 2) - ((height / 2) + 50);

        var style = `fullscreen=yes, resizeable=no, menubar=no, location=no, titlebar=no, toolbar=no, status=no, width=${width}, height=${height}, screenX=${leftPosition}, screenY=${topPosition}`;
        var w = window.open(url, '_blank', style);

        setTimeout(function timeout() {
            if (w.closed) {
              window.location.reload();
            } else {
              setTimeout(timeout, 100);
            }
          }, 100);
        }
</script>
@endpush
@endif
