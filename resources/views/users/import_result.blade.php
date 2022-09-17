@extends('layouts.app')

@section('title', 'Import Result Log')

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="fa fa-file-excel-o"></i>
          @lang('modules.import_result_log')
      </div>

      <div class="card-body">
        @if (session('log'))
          <div id="log">
          {!! session('log') !!}
          </div>

          <br/>
          <br/>
        <!--  <button id="downloadLog" class="btn btn-primary btn-sm">@lang('modules.download_log_file')</button>-->
        @else
          <div class="alert alert-warning">
            @lang('modules.import_result_is_not_found').
            <br/>
            <br/>
            <a href="{{route('users.import')}}" class="btn btn-primary btn-sm">@lang('modules.go_to_import_page')</a>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ mix('scripts/users/import.js') }}"></script>
@endpush
