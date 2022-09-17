<hr>
<small>
  <div class="bd-example text-muted">
    <dl class="row">
      @isset($data->created_at)
      <dd class="col-sm-3">@lang('modules.date_added')</dd>
      <dd class="col-sm-9">
        {{\Carbon\Carbon::parse($data->created_at)->format('d M Y g:iA e')}}
        {{isset($data->creator) ? ' by '.$data->creator->first_name.' '.$data->creator->last_name : ''}}
      </dd>
      @endisset
      @isset($data->updated_by)
      <dd class="col-sm-3">@lang('modules.last_updated')</dd>
      <dd class="col-sm-9">
        {{\Carbon\Carbon::parse($data->updated_at)->format('d M Y g:iA e')}}
        {{isset($data->updater) ? ' by '.$data->updater->first_name.' '.$data->updater->last_name : ''}}
      </dd>
      @endisset
    </dl>
  </div>
</small>
