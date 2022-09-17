@extends('layouts.app')

@section('title', $title)

@section('content')

{{--
 <!---
 <div class="row" style="margin-bottom: 30px; padding:0;">
  <div class="col-sm-4">
    <div class="searchbox">
        <div class="input-group col-md-12">
            <input type="text" class="search-query form-control" placeholder="@lang('modules.search_course')" />
            <span class="input-group-btn">
                <button class="btn btn-primary" type="button">
                    <i class="fa fa-search"></i>
                </button>
            </span>
        </div>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="input-group">
      <span class="input-group-prepend">
        <button type="button" class="btn btn-primary">
          <i class="fa fa-list"></i>@lang('modules.category')</button>
      </span>
      <input type="hidden" id="course_url" value="{{route('ajax.courses')}}"/>
      <input type="hidden" id="subcategory_url" value="{{route('ajax_subcategory')}}"/>
      <input type="hidden" id="token" value="{{csrf_token()}}"/>
      <select name="category" class="form-control" >
         <option value="0">All</option>
      @if($parentCategory)
        @foreach($parentCategory as $category)
         <option value="{{$category->id}}">{{$category->title}}</option>
        @endforeach
      @endif
      </select>
    </div>
  </div>

  <div class="col-sm-4" id="sub_category_option" style="display:none">
    <div class="input-group">
      <span class="input-group-prepend">
        <button type="button" class="btn btn-primary">
          <i class="fa fa-list-alt "></i>{{trans_choice('modules.subcategory', 0)}}</button>
      </span>
      <select id="subcategory" name="subcategory" class="form-control" >
        <option value="0">All</option>
      </select>
    </div>
  </div>
</div>
--->
--}}

<div id="courses">
 <div class="row courselist">

@if($mycourses->count() == 0)
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div class="alert alert-warning" role="alert">
          <i class="fa fa-warning"></i> @lang('modules.no_course_enrolled')
        </div>
      </div>
    </div>
  </div>
@else
  @foreach($mycourses as $mycourse)
    @php
      $course = \App\Course::where('id', $mycourse->course_id)->first();
     if($course) {
      \App\CourseUser::updateResult($mycourse->course_id, $mycourse->user_id);

      $mycourse = \App\CourseUser::find($mycourse->id);
      $course = $mycourse->course;

      $course_status = "Incomplete";
      $total_modules = 0;
      $course_module_complete_count = 0;
      $percentage_complete = 0;


      $courseStatusResult = course_completion_rules_result($mycourse->course_id, \Auth::id());


      $percentage_complete = ($courseStatusResult['percentage'] / 100);

     if($courseStatusResult['complete'] == 1)
       {
         $course_status = "Completed" ;
       }


      $disable = false ;
      $overdue = 0 ;
      $today = \Carbon\Carbon::today()->format("d/m/Y");
      if(strtotime($mycourse->start_date) > strtotime($today)) $disable = true;

      if($course && \Auth::user()->company_id)
      {
        $result = \App\CourseCompany::select('deadline')
                          ->where('company_id', \Auth::user()->company_id)
                          ->where('active', true)
                          ->where('course_id', $course->id)
                          ->first();
        if($result && $result->deadline !="")
        {

          $duration = explode(" ",$result->deadline);

        if($mycourse->start_date != "")
          {
            $start_date = str_replace("/","-",$mycourse->start_date);
            $start = \Carbon\Carbon::createFromFormat('d-m-Y', $start_date)->format('Y-m-d') ;
            $start = \Carbon\Carbon::createFromFormat('Y-m-d', $start);
          }
        else
         $start = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',  $mycourse->enrol_date);

         switch($duration[1])
         {
           case 'day' :
            $start->addDay($duration[0]); break;
           case 'week' :
            $start->addWeek($duration[0]);  break;
           case 'month' :
            $start->addMonth($duration[0]);  break;
           case 'year' :
            $start->addYear($duration[0]);  break;
         }

          $course->deadline_date = \Carbon\Carbon::parse($start)->format("d/m/Y");

          if($percentage_complete < 1 && $total_modules > 0 && (\Carbon\Carbon::today() > \Carbon\Carbon::parse($start)))
          {
            $overdue = 1;
          }

        }
      }





    @endphp


    <div class="col-sm-6 col-lg-4 courseitem mb-4"  data-title="{{$course->title}}" >
     <a href="{{route('my-courses.show', $course->slug)}}" class="d-block h-100">
        <div class="card h-100">
          <div class="card-body" data-content="{{$course->title}}" style="padding:0">
          @if($course_status == "Completed")
            <span class="badge badge-completed"><i class="fa fa-check-double"></i> @lang('modules.completed')</span>
          @endif
          @if($overdue == 1 && $course_status != "Completed")
            <span class="badge badge-overdue"><i class="fa fa-warning"></i> @lang('modules.overdue')</span>
          @endif
            <img src="{{$course->image ? asset('storage/courses/images/'.$course->image) : asset('img/no-img.jpg')}}" alt="{{$course->title}}" width="100%">
          </div>
          <div class="card-footer text-center">
            <div class="circle-progress" data-size="70" data-value="{{$percentage_complete}}">
              <strong></strong>
            </div>

            <div class="text-center">
              <h5 class="mb-3">{{ucfirst($course->title)}}</h5>
            </div>
            <div class="card-footer">
              {{ucfirst($course->description)}}
            </div>
            <div class="detail-text card-footer text-left mt-3">
              @if($course->duration)


                <i class="fa fa-clock"></i> {{$course->duration}}

              @endif




              @if($mycourse->start_date !="" && $mycourse->self_enroll==0 && $disable)

                <div class="float-right">@lang("modules.start_date"): {{$mycourse->start_date}}</div>

             @elseif($course->deadline_date)

                <div class="float-right">@lang("modules.deadline"): {{$course->deadline_date}}</div>


             @endif
            </div>
          </div>
        </div>
      </a>
    </div>
   @php
    }
   @endphp
  @endforeach
@endif
 </div>
</div>
@endsection


@push('scripts')
<script src="{{asset('vendors/circle-progress/circle-progress.min.js')}}"></script>
<script src="{{ mix('scripts/my-courses/index.js') }}"></script>
@endpush
@push("css")
<style>
 .detail-text
 {
   color:gray;
   padding-left: 5px;
   padding-bottom: 20px;
 }
 .disablediv {
    pointer-events: none;
    opacity: 0.5;
}
</style>
@endpush
