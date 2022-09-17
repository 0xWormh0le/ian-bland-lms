<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
    <a class="nav-link active show" data-toggle="tab" href="#tab-table" role="tab" aria-controls="home" aria-selected="true">
        <i class="fa fa-table"></i> @lang('modules.table')</a>
    </li>
    <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#tab-chart" role="tab" aria-controls="profile" aria-selected="false">
        <i class="fa fa-chart-pie"></i> @lang('modules.chart')</a>
    </li>
</ul>
<div class="tab-content">
  <div class="tab-pane active show" id="tab-table" role="tabpanel">
    <div class="col-sm-12 text-right mb-3">
        <div id="export" class="btn-group">
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-download"></i> @lang('modules.download')
        </button>
        <div class="dropdown-menu dropdown-menu-right">
            <button type="button" class="dropdown-item export" data-id="excel">@lang('modules.download_as') XLS</button>
            <button type="button" class="dropdown-item export" data-id="csv">@lang('modules.download_as') CSV</button>
        </div>
        </div>
    </div>
    @include('reports.enrollment.table')
  </div>
  <div class="tab-pane" id="tab-chart" role="tabpanel">
    @include('reports.enrollment.chart')
  </div>
</div>
