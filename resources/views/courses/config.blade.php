  @php
   $listRules = \App\CourseConfig::listRules();
  @endphp

  <div class="row mt-4">
    <div class="col">

        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#config1" role="tab" aria-controls="home">
              <i class="icon-layers"></i> @lang('modules.traversal_rules')</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#config2" role="tab" aria-controls="profile">
              <i class="icon-puzzle"></i> @lang('modules.completion_rules')</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#config3" role="tab" aria-controls="messages">
              <i class="icon-direction"></i> @lang('modules.learning_path')</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#config4" role="tab" aria-controls="messages">
              <i class="icon-badge"></i> @lang('modules.certificate')</a>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="config1" role="tabpanel">
            @if(is_null($data->config) || $data->config->transversal_rule == 'none')
            <div class="alert alert-warning" role="alert">
              {{ $listRules['transversal_rule']['none'] }}
            </div>
            @elseif(@$data->config->transversal_rule == 'sequential')
            <div class="alert alert-primary" role="alert">
              {{ $listRules['transversal_rule']['sequential'] }}
            </div>
            @endif
          </div>
          <div class="tab-pane" id="config2" role="tabpanel">
            @if(is_null($data->config) || $data->config->completion_rule == '')
              <div class="alert alert-warning" role="alert">
                @lang('modules.no_completion_rules_text')
              </div>
            @else
              <div class="alert alert-primary" role="alert">
                @if ($data->config->completion_rule == 'all')
                  {{ $listRules['completion_rule']['all'] }}
                @elseif ($data->config->completion_rule == 'any')
                  {{ $listRules['completion_rule']['any'] }}
                @elseif ($data->config->completion_rule == 'certain')
                <ul>
                  @if($data->config->completion_modules)
                    <li>{{ $listRules['completion_rule']['certain'] }} :
                    <ol>
                      @foreach(explode(',',$data->config->completion_modules) as $m_id)
                        @php
                          $module = \App\Module::find($m_id);
                        @endphp
                        @if($module)
                        <li>{{$module->title}} [{{$module->type}}]</li>
                        @endif
                      @endforeach
                    </ol>
                    <br/><br/>
                    </li>
                  @endif

                  @if($data->config->completion_percentage > 0)
                    <li><strong>{{ $data->config->completion_percentage }}%</strong>
                    @lang('modules.of_modules_must_be_completed')</li>
                  @endif
                </ul>
                @endif
              </div>
            @endif
          </div>
          <div class="tab-pane" id="config3" role="tabpanel">
            @if(is_null($data->config) || $data->config->learning_path == '')
              <div class="alert alert-warning" role="alert">
                @lang('modules.this_course_has_no_learning_path')
              </div>
            @else
            <div class="alert alert-primary" role="alert">
            @php

                $cdetail = explode(",",$data->config->learning_path);
                $course = \App\Course::select('title')->whereIn('id', $cdetail)->get();
             for($c=0;$c < count($course);$c++)
             {
               if($c > 0) echo ", ";
                echo $course[$c]->title;
              }

            @endphp
            </div>

            @endif
          </div>
          <div class="tab-pane" id="config4" role="tabpanel">
            @if(is_null($data->config) || !$data->config->get_certificate)
              <div class="alert alert-warning" role="alert">
                @lang('modules.this_course_has_no_certificate')
              </div>
            @else
              <div class="alert alert-info" role="alert">
                @lang('modules.assign_certificate_text')
                <h5>{{@$data->certificate->certificate_name}}</h5>
                <p>
                  Validity :
                  {{@$data->certificate->validity_years ? $data->certificate->validity_years .' years' : ''}}
                  {{@$data->certificate->validity_months ? $data->certificate->validity_months .' months' : ''}}
                  {{@$data->certificate->validity_weeks ? $data->certificate->validity_weeks .' weeks' : ''}}
                  {{@$data->certificate->validity_days ? $data->certificate->validity_days .' days' : ''}}
                </p>
                 <iframe src="{{ route('certificate-templates.preview', @$data->certificate->design_id?:0) }}#view=FitH&toolbar=0&navpanes=0&scrollbar=0" frameborder="0" class="grid-100 tablet-grid-100 mobile-grid-100 grid-parent" style="background:#f0f0f0; width:210px; height:auto;"></iframe>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
