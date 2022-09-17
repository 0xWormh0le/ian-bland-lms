
@php
  $listRules = \App\CourseConfig::listRules();
@endphp

<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="icon-equalizer"></i>
          {{trans('controllers.additional_config_for').$data->title}}
      </div>
      <form action="{{route('courses.config.update', $data->slug)}}" method="post" class="form-horizontal" file="true" enctype="multipart/form-data">
        @csrf()
        @method('put')

        <input type="hidden" name="id" value="{{@$courseConfig->id}}">

      <div class="card-body">
        <h5>@lang('modules.learning_path')</h5>
        <div class="form-group row">
          <label class="col-md-12 col-form-label" for="learning_path">@lang('modules.select_course_or_module_text')</label>
          <div class="col-md-12">
            @php
            if(\Auth::user()->isSysAdmin())
                $courses = \App\Course::where('id','!=', $data->id)->pluck('title', 'id');
            else
            {
                $courceCompanyIds = \App\CourseCompany::where('company_id', \Auth::user()->company_id)->pluck('course_id');
                $courses = \App\Course::where('id','!=', $data->id)->whereIn('id', $courceCompanyIds)->pluck('title', 'id');
             }


             $cdetail = array() ;

             if(old('learning_path'))
             {
               $cdetail = old('learning_path');
             }
             else if($courseConfig->learning_path !=""){
              $cdetail = explode(",",$courseConfig->learning_path);
               }
            @endphp
            <select id="learning_path" name="learning_path[]" class="form-control select2 {{ $errors->has('learning_path') ? 'is-invalid' : '' }}" multiple="multiple">
              <option value="">@lang('modules.none')</option>
              @foreach($courses as $k => $v)
               @if(in_array($k, $cdetail))
                <option value="{{$k}}" selected>[Course] {{$v}}</option>
               @else
               <option value="{{$k}}">[Course] {{$v}}</option>
               @endif
             @endforeach
            </select>
            @if ($errors->has('learning_path'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('learning_path') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <hr>
        <h5>@lang('modules.certificate')</h5>
        <div class="form-group row">
          <label class="col-md-4 col-form-label" for="get_certificate">@lang('modules.assign_certificate_text')</label>
          <div class="col-md-3">
            <label class="switch switch-label switch-outline-primary-alt">
              <input type="checkbox" id="get_certificate" name="get_certificate" value="1" class="switch-input"{{(old('get_certificate') or @$courseConfig->get_certificate)? 'checked':''}}>
              <span class="switch-slider" data-checked="On" data-unchecked="Off"></span>
            </label>
          </div>
        </div>

        <div class="row" id="certificateprops">

          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <i class="icon-note"></i> @lang('modules.properties')
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label for="certificate_name">@lang('modules.certificate_title')</label>
                      <input type="text" class="form-control {{ $errors->has('certificate_name') ? ' is-invalid' : '' }}" id="certificate_name" name="certificate_name" value="{{old('certificate_name') ?: @$certificate->certificate_name}}">
                      @if($errors->has('certificate_name'))
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $errors->first('certificate_name') }}</strong>
                        </span>
                      @endif
                    </div>
                  </div>

                  <div class="col-sm-12">
                    <div class="form-group row">
                      <label class="col-sm-12" for="validity_duration">@lang('modules.validity_duration')</label>
                      <div class="col-sm-3">
                        <div class="input-group">
                          @php
                           $year = old('validity_years') ;
                           if($year == "") $year = @$certificate->validity_years ;
                           if($year == "") $year = @$companyConfig->validity_years ;
                          @endphp
                          <input type="text" id="validity_years" name="validity_years" class="form-control numberonly" maxlength="2" value="{{$year}}">
                          <div class="input-group-append">
                            <span class="input-group-text">
                              @lang('modules.years')
                            </span>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="input-group">
                          @php
                           $month = old('validity_months') ;
                             if($month == "") $month = @$certificate->validity_months ;
                             if($month == "") $month = @$companyConfig->validity_months ;
                          @endphp
                          <input type="text" id="validity_months" name="validity_months" class="form-control numberonly" maxlength="2" value="{{$month}}">
                          <div class="input-group-append">
                            <span class="input-group-text">
                              @lang('modules.months')
                            </span>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="input-group">
                          @php
                           $week = old('validity_weeks') ;
                           if($week == "") $week = @$certificate->validity_weeks ;
                           if($week == "") $week = @$companyConfig->validity_weeks ;
                          @endphp
                          <input type="text" id="validity_weeks" name="validity_weeks" class="form-control numberonly" maxlength="2" value="{{$week}}">
                          <div class="input-group-append">
                            <span class="input-group-text">
                              @lang('modules.weeks')
                            </span>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="input-group">
                          @php
                           $day = old('validity_days') ;
                           if($day == "") $day = @$certificate->validity_days ;
                           if($day == "") $day = @$companyConfig->validity_days ;
                          @endphp
                          <input type="text" id="validity_days" name="validity_days" class="form-control numberonly" maxlength="3" value="{{$day}}">
                          <div class="input-group-append">
                            <span class="input-group-text">
                              @lang('modules.days')
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <i class="icon-picture"></i> @lang('modules.design')
              </div>
              <div class="card-body text-center">
                <div class="row justify-content-sm-center">
                  <div class="col-sm-12">
                    <div class="form-group">

                    <iframe id="selected_design" src="{{ route('certificate-templates.preview', @$certificate->design_id?:$defaultCertDesign) }}#view=FitH&toolbar=0&navpanes=0&scrollbar=0" frameborder="0" class="grid-100 tablet-grid-100 mobile-grid-100 grid-parent" style="background:#f0f0f0; width:200px; height:130px;"></iframe>

                    </div>
                  </div>
                  <div class="col-sm-8 ">
                    <button type="button" id="selectdesign" class="btn btn-md btn-block btn-primary">@lang('modules.select_design')</button>

                    <input type="hidden" id="design_id" name="design_id" value="{{old('design_id') ?: @$certificate->design_id}}">
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

      </div>
      <div class="card-footer">
        @include('components.form_submit')
      </div>
      </form>
    </div>
  </div>
</div>


@push('modals')
<div id="certificateModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document" style="width:80%">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">@lang('modules.certificate_designs')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
        @foreach(\App\CertificateDesign::where('draft', 0)->get() as $r)
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                {{$r->name}}
              </div>
              <div class="card-body center">
              <iframe src="{{ route('certificate-templates.preview', $r->id) }}#view=FitH&toolbar=0&navpanes=0&scrollbar=0" frameborder="0" class="grid-100 tablet-grid-100 mobile-grid-100 grid-parent" style="background:#f0f0f0; width:200px; height:130px;"></iframe>
          </div>
              <div class="card-footer text-center">
                <button type="button" class="btn btn-sm btn-block btn-primary selectthis" title="Select" data-id="{{$r->id}}" data-url="{{ route('certificate-templates.preview', $r->id) }}">@lang('modules.select')</button>
              </div>
            </div>
          </div>
        @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endpush

@include('_plugins.select2')

@push('scripts')
<script>
$(document).ready(function() {
  $("#select_modules").hide();
  $("#set_percentage").hide();


  if(!$("#completion_rule").is(':checked'))
  {
    $("#select_modules").show();
    $("#set_percentage").show();
  }

  $("#certificateprops").hide();
  if($("#get_certificate").is(':checked'))
  {
    $("#certificateprops").show();
  }


  @if(@$courseConfig->completion_modules)
  var modules = [];
  @foreach(explode(',',$courseConfig->completion_modules) as $id)
  modules.push("{{$id}}");
  @endforeach
  $('#completion_modules').val(modules).trigger('change');
  @endif

  @if ((!@$courseConfig->completion_rule == 'all') &&
      (!@$courseConfig->completion_rule == 'any') &&
      (old('completion_percentage') || @$courseConfig->completion_percentage))
    $("#set_percentage").show();
  @endif

  $('#completion_rule').on('change', function(){
    $('#completion_modules').val(['']).trigger('change');
    $('#completion_percentage').val('');

    $('#completion_modules').prop('required', false);
    $('#completion_percentage').prop('required', false);
    $("#select_modules").hide();
    $("#set_percentage").hide();

    if(!$(this).is(':checked'))
    {
      $('#completion_modules').prop('required', true);
      $("#select_modules").show();
      $('#completion_percentage').prop('required', true);
      $("#set_percentage").show();
    }
  });

  $("#completion_percentage").on('blur', function(){
    if(parseFloat($(this).val()) > 100 )
      $(this).val(100);
  })

  $('#get_certificate').on('change', function(){
    $("#certificateprops").hide();

    if($(this).is(':checked'))
    {

      $("#certificateprops").show();
    }
  });

  $("#selectdesign").on("click", function(){
    $("#certificateModal").modal("show");
  });

  $(document).on('click', '.selectthis', function (e) {
    e.preventDefault();
    $("#design_id").val($(this).data('id'));
    $('#selected_design').attr('src', $(this).data('url'));
    var $contents = $('#selected_design').contents();
    $contents.scrollTop($contents.height());
    $("#certificateModal").modal("hide");
  });

});
</script>
@endpush
