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
            {!! show_button('update', 'companies.edit', $data->slug) !!}

            {!! show_button('remove', 'companies.destroy', encrypt($data->id)) !!}
          </div>
        </div>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-sm-8">
            <dl class="row">
              <dd class="col-sm-3">@lang("modules.company_name")</dd>
              <dt class="col-sm-9">{{$data->company_name}}</dt>

              <dd class="col-sm-3">@lang("modules.status")</dd>
              <dt class="col-sm-9">{{$data->active ? 'Active':'Inactive'}}</dt>

              <dd class="col-sm-3">@lang("modules.max_no_of_users")</dd>
              <dt class="col-sm-9">{{ $data->max_users }}</dt>

              <dd class="col-sm-3">@lang("modules.active_from")</dd>
              <dt class="col-sm-9">{{ $data->active_from }}</dt>

              <dd class="col-sm-3">@lang("modules.active_to")</dd>
              <dt class="col-sm-9">{{ $data->active_to ?: '-' }}</dt>
            </dl>
          </div>
          <div class="col-sm-4 text-right">
            <img class="img-preview mt-3" src="{{ @$data->logo ? asset('storage/logo/'.$data->logo) : asset('img/no-img.jpg') }}" alt="logo" width="200"/>
          </div>
        </div>

        @include('components.record_log')
      </div>
    </div>
  </div>


  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            @lang("modules.enrolled_courses")
          </div>
          <div class="col-sm-6 text-right">
          </div>
        </div>
      </div>

      <div class="card-body">
        <table class="table table-bordered table-striped" style='border-collapse: collapse !important'>
          <thead>
            <tr>
              <th>@lang("modules.course_title")</th>
              <th>@lang("modules.en_title")</th>
              <th>@lang("modules.language")</th>
              <th>@lang("modules.category")</th>
              <th>@lang("modules.category")</th>
              <th>@lang("modules.action")</th>
            </tr>
          </thead>
          <tbody>
        @forelse ($enrolled_courses as $course)
            <tr>
              <td>{{ $course->title }}</td>
              <td>{{ $course->en_title }}</td>
              <td>{{ $course->language }}</td>
              <td>{{ optional($course->category)->title }}</td>
              <td>{{ optional($course->category)->title }}</td>
              <td>
                <form action="{{ route('companies.courses.enrollMultipleCourse', $data->id) }}" method='post'>
                  @csrf
                  <input type='hidden' name='action' value='unenroll'/>
                  <input type='hidden' name='course' value='{{ $course->id }}'/>
                  <button type="submit" class="btn btn-danger btn-sm">
                    <i class="icon-close"></i>
                      @lang('controllers.unenroll')
                  </button>
                </form>
              </td>
            </tr>
        @empty
          <tr>
            <td colspan="6" class='text-center'>@lang('modules.no_course_enrolled')</td>
          </tr>
        @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-sm-12">
    <form action="{{ route('companies.courses.enrollMultipleCourse', $data->id) }}" method='post'>
      @csrf
      <input type='hidden' name='action' value='enroll'/>
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-sm">
              <button class="btn btn-primary btn-md" id="enroll-button"  type="submit">
                <i class="icon-plus"></i> @lang("modules.enroll")
              </button>
            </div>
          </div>
        </div>

        <div class="card-body">
          <table class="table table-bordered table-striped" style='border-collapse: collapse!important'>
            <thead>
              <tr>
                <th></th>
                <th>@lang("modules.course_title")</th>
                <th>@lang("modules.en_title")</th>
                <th>@lang("modules.language")</th>
                <th>@lang("modules.category")</th>
              </tr>
            </thead>
            <tbody>
          @forelse ($unenrolled_courses as $course)
              <tr>
                <td class='text-center'>
                  <input type='checkbox' name='courses[]' value='{{ $course->id }}' class='enroll-check'/>
                </td>
                <td>{{ $course->title }}</td>
                <td>{{ $course->en_title }}</td>
                <td>{{ $course->language }}</td>
                <td>{{ optional($course->category)->title }}</td>
              </tr>
          @empty
              <tr>
                <td colspan="6" class='text-center text-capitalize'>@lang('none')</td>
              </tr>
          @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </form>
  </div>


</div>
@endsection

@include('_plugins.datatables')
@push("scripts")

<script>
$(document).ready(function() {
  $("table").DataTable();
  $("#enroll-button").prop("disabled", $(".enroll-check:checked").length == 0);

  $(".enroll-check").click(function () {
    $("#enroll-button").prop("disabled", $(".enroll-check:checked").length == 0);
  });
} );
</script>

@endpush
