@extends('layouts.app')

@section('content')

<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body row">
        <div class="col-sm-12">
          <div class="input-group">
            <span class="input-group-prepend">
              <button type="button" class="btn btn-primary">
                <i class="fa fa-search"></i> @lang('modules.search')</button>
            </span>
            <input type="text" id="search" name="search" class="form-control" placeholder="@lang('modules.search_by_course_title')" autocomplete="off">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row list">
@if(count($certificates) > 0)
  @foreach($certificates as $r)
   @php
     $r->course = \App\Course::withTrashed()->where('id', $r->course_id)->first();
   @endphp
   @if(@$r->course->certificate)
    <div class="col-lg-4 col-md-6 listitem" data-title="{{$r->course->title}}" >
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-sm-8">{{$r->course->title}}</div>
            <div class="col-sm-4 text-right">
            </div>
          </div>
        </div>
        <div class="card-body" data-content="{{$r->name}}" >
          <iframe src="{{ route('certificate.preview', ['user_id' => \Auth::id(), 'course_id' => $r->course_id]) }}#view=FitH&toolbar=0&navpanes=0&scrollbar=0" frameborder="0" class="grid-100 tablet-grid-100 mobile-grid-100 grid-parent" style="background:#f0f0f0; width:100%; height:320px;"></iframe>
        </div>
        <div class="card-footer text-center">
          <a target="_blank" href="{{ route('certificate.preview', ['user_id' => \Auth::id(), 'course_id' => $r->course_id]) }}" class="btn btn-sm btn-primary btn-block" title="@lang('modules.preview')"><i class="icon-screen-desktop"></i>  @lang('modules.preview')</a>
        </div>
      </div>
    </div>
  @endif
  @endforeach
@else
  <div class="col-sm-12">
    <div class="alert alert-danger text-center">
      <i class="fa fa-exclamation-triangle"></i> @lang('modules.no_certificate')
    </div>
  </div>
@endif
</div>


@endsection

@push('scripts')
<script>

$(document).ready(function () {

  $("#search").keyup(function(){
    var filter = $(this).val();
    $(".listitem").each(function(){
     if ($(this).attr('data-title').search(new RegExp(filter, "i")) < 0) {
      $(this).fadeOut();
     } else {
      $(this).show();
     }
    });
  });

});
</script>
@endpush
