<div class="row courselist">
@if($courses->count() == 0)
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div class="alert alert-danger text-center" role="alert">
          <i class="fa fa-warning"></i> @lang('modules.no_course_found')
        </div>
      </div>
    </div>
  </div>
@else
  @foreach($courses as $course)
    <div class="col-xl-3 col-lg-4 col-md-6 courseitem" data-title="{{$course->title}}" >
      <a href="{{route('courses.show', $course->slug)}}">
      <div class="card">
        <div class="card-body image" data-content="{{$course->title}}" >
          <img src="{{$course->image ? asset('storage/courses/images/'.$course->image) : asset('img/no-img.jpg')}}" alt="{{$course->title}}" style="width:100%; height:auto;">
        </div>
        <div class="card-footer text-center">
          <h6>{{$course->title}}</h6>
        </div>
        <div class="card-footer text-center" style="min-height:120px;">
          {{$course->description}}
        </div>
        <div class="detail-text card-footer mt-3">
          @if($course->duration)

            <div class="pl-2">@lang("modules.duration"): {{$course->duration}}</div>


          @endif

            <div class="pl-2">@lang("modules.category"): {{\App\CourseCategory::getCategoryTitle($course->category_id)}}</div>


          @if($course->deadline_date)
          <div class="text-left row">
            <div class="float-left col-sm-5 pr-1">@lang("modules.deadline"):</div>
            <div class="col pl-0">{{$course->deadline_date}}</div>
          </div>
          @endif
        </div>

      </div>
      </a>
    </div>
  @endforeach
@endif
</div>
@push("css")
<style>
 .detail-text
 {
   color:gray;
   padding-left: 10px;
   padding-bottom: 20px;
 }
</style>
@endpush
