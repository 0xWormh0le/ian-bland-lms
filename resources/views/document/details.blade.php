@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <i class="icon-info"></i> {{$title}}
          </div>
          <div class="col-sm-6 text-right">
            {!! show_button('update', 'document.edit', ['course'=>$data->course->slug, 'slug'=>$data->slug], validate_role('courses.create')) !!}

            {!! show_button('remove', 'document.destroy', encrypt($data->id), validate_role('courses.create')) !!}
          </div>
        </div>
      </div>

      <div class="card-body">

        <div class="bd-example">
          <dl class="row">
            <dd class="col-sm-3">{{trans("modules.course_attachment_title")}}</dd>
            <dt class="col-sm-9">{{$data->title}}</dt>
            <dd class="col-sm-3">{{trans("modules.course")}}</dd>
            <dt class="col-sm-9">{{$data->course->title}}</dt>
            <dd class="col-sm-3">{{trans("modules.module_sequence")}}</dd>
            <dt class="col-sm-9">#{{$data->order_no}}</dt>
          </dl>
        </div>

        @include('components.record_log')
      </div>
    </div>
  </div>
</div>
@endsection
