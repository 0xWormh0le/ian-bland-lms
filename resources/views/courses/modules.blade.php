<div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <i class="icon-docs"></i> @lang('modules.modules')
          </div>
          <div class="col-sm-6 text-right">
         @if(!@$data || @$data->created_by == \Auth::user()->id || \Auth::user()->isSysAdmin())
              <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="icon-plus"></i> @lang('modules.new_module')
                </button>
                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">

                  <a class="dropdown-item text-right" href="{{route('elearning.create', $data->slug)}}">{{session('moduleLabel')['elearning']}}</a>

                  <a class="dropdown-item text-right" href="{{route('document.create', $data->slug)}}">{{session('moduleLabel')['document']}}</a>

                </div>
              </div>
@endif
          </div>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-bordered datatable" id="modulesTable">
          <thead>
            <tr>
              <th>#</th>
              <th>@lang('modules.type')</th>
              <th>@lang('modules.title')</th>
              <th>@lang('modules.date_added')</th>
              <th></th>
          </thead>
        </table>
      </div>
    </div>
  </div>
