<div class="row">
  <!--/.col-->
  <div class="col-6 col-lg-3">
    <div class="card">
      <div class="card-body p-3 d-flex align-items-center">
        <i class="fa fa-chalkboard-teacher bg-primary p-3 font-2xl mr-3"></i>
        <div>
          <div class="text-value-sm text-primary">{{\App\Course::count()}}</div>
          <div class="text-muted text-uppercase font-weight-bold small">@lang('modules.courses')</div>
        </div>
      </div>
      <div class="card-footer px-3 py-2">
        <a class="btn-block text-muted d-flex justify-content-between align-items-center" href="{{route('courses.index')}}">
          <span class="small font-weight-bold">@lang('modules.view_more')</span>
          <i class="fa fa-angle-right"></i>
        </a>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card">
      <div class="card-body p-3 d-flex align-items-center">
        <i class="far fa-building bg-primary p-3 font-2xl mr-3"></i>
        <div>
          <div class="text-value-sm text-primary">{{\App\Company::count()}}</div>
          <div class="text-muted text-uppercase font-weight-bold small">@lang('modules.companies')</div>
        </div>
      </div>
      <div class="card-footer px-3 py-2">
        <a class="btn-block text-muted d-flex justify-content-between align-items-center" href="{{route('companies.index')}}">
          <span class="small font-weight-bold">@lang('modules.view_more')</span>
          <i class="fa fa-angle-right"></i>
        </a>
      </div>
    </div>
  </div>
  <!--/.col-->
  <div class="col-6 col-lg-3">
    <div class="card">
      <div class="card-body p-3 d-flex align-items-center">
        <i class="fa fa-users bg-primary p-3 font-2xl mr-3"></i>
        <div>
          <div class="text-value-sm text-primary">{{\App\User::where('role_id', '!=', 0)->count()}}</div>
          <div class="text-muted text-uppercase font-weight-bold small">@lang('modules.users')</div>
        </div>
      </div>
      <div class="card-footer px-3 py-2">
        <a class="btn-block text-muted d-flex justify-content-between align-items-center" href="{{route('users.index')}}">
          <span class="small font-weight-bold">@lang('modules.view_more')</span>
          <i class="fa fa-angle-right"></i>
        </a>
      </div>
    </div>
  </div>
  <!--/.col-->
  <div class="col-6 col-lg-3">
    <div class="card">
      <div class="card-body p-3 d-flex align-items-center">
        <i class="fa fa-user-friends bg-primary p-3 font-2xl mr-3"></i>
        <div>
          <div class="text-value-sm text-primary">{{\App\Team::count()}}</div>
          <div class="text-muted text-uppercase font-weight-bold small">@lang('modules.teams')</div>
        </div>
      </div>
      <div class="card-footer px-3 py-2">
        <a class="btn-block text-muted d-flex justify-content-between align-items-center" href="{{route('teams.index')}}">
          <span class="small font-weight-bold">@lang('modules.view_more')</span>
          <i class="fa fa-angle-right"></i>
        </a>
      </div>
    </div>
  </div>
  <!--/.col-->
</div>
