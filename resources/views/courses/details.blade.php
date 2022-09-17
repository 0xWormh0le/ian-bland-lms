@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <i class="icon-info"></i>
              {{$title}}
          </div>
          <div class="col-sm-6 text-right">
            {!! show_button('update', 'courses.edit', $data->slug, true) !!}
          @if(Auth::user()->isSysAdmin() || $data->created_by == \Auth::user()->id)
            {!! show_button('remove', 'courses.destroy', encrypt($data->id), true) !!}
          @endif

          @if (validate_role('reports.superadmin.index'))
            <a href="{{ route('reports.course.statistic', ['id' => encrypt($data->id), 'filter' => 'active']) }}" class="btn btn-primary btn-md">
              <i class="icon-doc"></i>
              @lang('menu.reports')
            </a>
          @endif
          </div>
        </div>
      </div>

      <div class="card-body">
          <div class="row">
            <div class="col-xl-4 col-lg-6">
               <img src="{{$data->image ? asset('storage/courses/images/'.$data->image) : asset('img/no-img.jpg')}}" width="100%">
            </div>
            <div class="col-xl-8 col-lg-6" >

              <h4 class="mt-3" style="margin-bottom: 0;">{{$data->title}}</h4>
              <small class="text-secondary">
                <i class="fa fa-tags"></i>
                {{\App\CourseCategory::getCategoryTitle($data->category_id)}} 
              
              @if (!empty($deadline))
                <i class="fa fa-calendar ml-3"></i>  
                @lang('modules.deadline'): {{ $deadline }} @lang('modules.from_enrollment')
              @endif

              </small>

              <p class="text-muted mt-3 mb-5">{{$data->description ?: ''}}</p>
          @if(@$data->deadline_date !="")
              <br>@lang('modules.deadline_date') : {{$data->deadline_date ?: '-'}}
          @endif


             @if (auth()->user()->isSysAdmin())

             @else
              <div class="callout callout-default">
                <small class="text-muted">@lang('modules.certificate')</small>
                <br>
                @if(is_null($data->config) || !$data->config->get_certificate)
                    <strong  class="text-secondary"> @lang('modules.this_course_has_no_certificate')</strong>
                @else
                    <strong  class="text-muted"> @lang('modules.this_course_has_a_certificate')</strong>
                @endif
              </div>

              <div class="callout callout-default">
                <small class="text-muted">@lang('modules.learning_path')</small>
                <br>
              @if(is_null($data->config) || $data->config->learning_path == '')
                <strong class="text-secondary"> @lang('modules.this_course_has_no_learning_path')</strong>
              @else
                <strong class="text-muted">
                @php

                    $cdetail = explode(",",$data->config->learning_path);
                    $course = \App\Course::select('title')->whereIn('id', $cdetail)->get();
                    
                    for ($c=0;$c < count($course);$c++)
                    {
                      if($c > 0) echo ", ";
                        echo $course[$c]->title;
                    }
                @endphp
                </strong>
              @endif
            </div>
                @endif

            </div>
          </div>

      </div>
    </div>
  </div>


  {{-- @include('courses.modules') --}}
  @include('elearning.details')

  @if (!\Auth::user()->company_id)
    @include('courses.companies')
  @elseif (!is_null($courseCompany))
    @include('courses.company.details_content', ['show_overview' => false, 'data' => $courseCompany]);

  <!-- <div class="col-sm-12">
    <div class="card">
      <div class="card-body text-right">
        <a href="{{ route('courses.companies.show', ['course' => $data->slug, 'company' => \Auth::user()->company->slug]) }}" class="btn btn-warning btn-lg btn-block">
          <i class="icon-pencil"></i> @lang('modules.company_course_info')
        </a>
      </div>
    </div>
  </div> -->
  @endif

</div>
@endsection

@include('_plugins.datatables')
@push('scripts')
<script>
$(document).ready(function() {
    $('#modulesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('courses.modules.data', $data->id) !!}',
        columns: [
            { data: 'order_no', name: 'order_no' },
            { data: 'type', name: 'type' },
            { data: 'title', name: 'title' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', sortable:false, class:'text-center' },
        ],
    });
    $('.datatable').attr('style', 'border-collapse: collapse !important');
});
</script>
@endpush
