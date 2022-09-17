@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="fa fa-file-excel-o"></i>
          {{$title}}
      </div>
      <form action="{{route('users.import.process')}}" method="post" class="form-horizontal" file="true" enctype="multipart/form-data">
        @csrf()
      <div class="card-body">
        <p>@lang('modules.please') <a href="{{ asset('downloads/import_users_template.csv') }}" class="btn btn-sm btn-success"><i class="fa fa-file-excel-o"></i> @lang('modules.download')</a> @lang('modules.the_following_import_template')</p>

        <br/>
        <br/>

        <table class="table">
          <tr>
            <th width="200">@lang('modules.first_name') <code>*</code></th>
            <td width="10">:</td>
            <td>@lang('modules.first_name_of_user')</td>
          </tr>
          <tr>
            <th>@lang('modules.last_name')</th>
            <td>:</td>
            <td>@lang('modules.last_name_of_user')</td>
          </tr>
          <tr>
            <th>@lang('modules.email') <code>*</code></th>
            <td>:</td>
            <td>@lang('modules.email_of_user_text')</td>
          </tr>
          @if(Auth::user()->isSysAdmin())
          <tr>
            <th>@lang('modules.company') <code>*</code></th>
            <td>:</td>
            <td>@lang('modules.company_must_same_text'). <a href="{{ route('companies.index') }}">[View Lists]</a></td>
          </tr>
          @endif
          <tr>
            <th>@lang('modules.team_name')</th>
            <td>:</td>
            <td>@lang('modules.can_be_empty_text'). <a href="{{ route('teams.index') }}">[View Lists]</a></td>
          </tr>
          <tr>
            <th>@lang('modules.department')</th>
            <td>:</td>
            <td>@lang('modules.department_name_can_be_empty')</a></td>
          </tr>
          <tr>
            <th>@lang('modules.role') @if(Auth::user()->isSysAdmin())<code>*</code>@endif</th>
            <td>:</td>
            <td>@lang('modules.role_name_same_text'). <a href="{{ route('roles.index') }}">[View Lists]</a></td>
          </tr>
          <tr>
            <th>@lang('modules.courses')</th>
            <td>:</td>
            <td>@lang('modules.course_text'). <a href="{{ route('courses.list') }}">[View Lists]</a></td>
          </tr>
          <tr>
            <th>@lang('modules.password')</th>
            <td>:</td>
            <td>@lang('modules.auto_email_text')</td>
          </tr>
        </table>

        <input type="file" id="file" name="file" accept=".csv" style="display:none;">

      @if(isset($limit) && $limit)
        <div class="card-body">
          <span class="alert-warning" role="alert">
            <strong>@lang("modules.max_users_limit_text")</strong>
          </span>
        </div>
      @else
        <button type="button" id="upload" class="btn btn-primary btn-lg btn-block">
          @lang('modules.upload')
        </button>
      @endif
      </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ mix('scripts/users/import.js') }}"></script>
@endpush
