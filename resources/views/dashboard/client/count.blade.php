<div class="row">
  <div class="col-sm-6 col-lg-3">
    <div class="card text-white bg-primary">
      <div class="card-body pb-0">
        <a href="{{ route('courses.index') }}" class="btn btn-transparent p-0 float-right">
          <i class="icon-cursor"></i>
        </a>
        <div class="text-value">{{ number_format($courseEnrolled) }}</div>
        <div>@lang('modules.course_enrolled')</div>
      </div>
      <div class="chart-wrapper mt-3 px-3" style="height:70px;">
        <canvas id="card-chart1" class="chart" height="70" data-label="{{implode(',', $statLabels)}}" data-value="{{implode(',', $courseStatValue)}}"></canvas>
      </div>
    </div>
  </div>
  <!--/.col-->
  <div class="col-sm-6 col-lg-3">
    <div class="card text-white bg-info">
      <div class="card-body pb-0">
        <a href="{{ route('users.index') }}" class="btn btn-transparent p-0 float-right">
          <i class="icon-cursor"></i>
        </a>
        <div class="text-value">{{ number_format($users) }}</div>
        <div>@lang('modules.users')</div>
      </div>
      <div class="chart-wrapper mt-3 px-3" style="height:70px;">
        <canvas id="card-chart2" class="chart" height="70" data-label="{{implode(',', $statLabels)}}" data-value="{{implode(',', $userStatValue)}}" style="margin-bottom:20px;"></canvas>
      </div>
    </div>
  </div>
  <!--/.col-->

  <div class="col-sm-6 col-lg-3">
    <div class="card text-white bg-success">
      <div class="card-body pb-0">
        <div class="text-value">{{ number_format($completed) }}</div>
        <div>@lang('modules.complete_percentage')</div>
      </div>
      <div class="chart-wrapper mt-3 px-3" style="height:70px;">
        <canvas id="card-chart3" class="chart" height="70" data-label="{{implode(',', $statLabels)}}" data-value="{{implode(',', $completedStatValue)}}"></canvas>
      </div>
    </div>
  </div>

  <!--/.col-->
  <div class="col-sm-6 col-lg-3">
    <div class="card text-white bg-warning">
      <div class="card-body pb-0">
        <div class="text-value">{{ number_format(100 - $completed) }}</div>
        <div>@lang('modules.incomplete_percentage')</div>
        <div class="chart-wrapper mt-3 px-3" style="height:70px;">
          <canvas id="card-chart4" class="chart" height="70" data-label="{{implode(',', $statLabels)}}" data-value="{{implode(',', $completedStatValue)}}"></canvas>
        </div>
      </div>
    </div>
  </div>
  <!--/.col-->
</div>
