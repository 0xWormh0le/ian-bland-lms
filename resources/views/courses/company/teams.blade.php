
<div class="col-xl-6">
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-sm-6">
          <i class="fa fa-user-friends"></i> @lang('modules.team_member')
        </div>
        <div class="col-sm-6 text-right">
          <button type="button" id="enrollTeam" class="btn btn-md btn-primary"><i class="icon-plus"></i> @lang('modules.enroll_team')</button>
        </div>
      </div>
    </div>
    <div class="card-body">
      <table class="table table-striped table-bordered datatable" id="teamsTable">
        <thead>
            <tr>
                <th>@lang('modules.team_name')</th>
                <th>@lang('modules.action')</th>
            </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

@push('modals')
<div id="teamsModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">@lang('modules.enroll_team')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table width="100%" class="table table-bordered datatable" id="unenrolledTeamTable">
          <thead>
            <tr>
              <th>@lang('modules.team_name')</th>
              <th></th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@endpush
