<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="fa fa-history"></i>
        <strong>
          @lang('modules.attempt_histories')
        @if ($title)
          - {{ $title }}
        @endif
        </strong>
      </div>
      <div class="card-body p-0">
        <table class="table table-responsive-sm table-striped mb-0">
          <thead>
            <tr>
              <th>@lang('modules.datetime')</th>
              <th>@lang('modules.completion')</th>
              <th>@lang('modules.score')</th>
              <th>@lang('modules.total_time')</th>
              <th>@lang('modules.status')</th>
            </tr>
          </thead>
          <tbody>
            @foreach($histories as $history)
              <tr>
                <td>{{ datetime_format($history->updated_at, 'd/m/Y g:iA e') }}</td>
                <td>
                @if($history->complete_status == 'completed')
                  <span class="badge badge-success">@lang('modules.completed')</span>
                  at {{ $history->completion_date }}
                @else
                  {{ ucwords($history->complete_status) }}
                @endif
                </td>
                <td>{{ $history->score }}</td>
                <td>{{ $history->total_time }}</td>
                <td>
                @if($history->satisfied_status == 'passed')
                  <span class="badge badge-success">@lang('modules.passed')</span>
                @else
                  {{ ucwords($history->satisfied_status) }}
                @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>