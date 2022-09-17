@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="icon-{{isset($data)?'pencil':'plus'}}"></i>
          {{$title}}
      </div>
      @if(isset($limit) && $limit)
      <div class="card-body">
        <span class="alert-warning" role="alert">
          <strong>@lang("modules.max_users_limit_text")</strong>
        </span>
      </div>
      @else
      <form action="{{isset($data) ? route('users.update', encrypt($data->id)) : route('users.store')}}" method="post" class="form-horizontal">
        @csrf()
        @isset($data)
          @method('put')
        @endisset
      <div class="card-body">

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="first_name">@lang('modules.first_name') <code>*</code></label>
          <div class="col-md-9">
            <input type="text" id="first_name" name="first_name" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" value="{{old('first_name')?:@$data->first_name}}" required autofocus>
            @if ($errors->has('first_name'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('first_name') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="last_name">@lang('modules.last_name')</label>
          <div class="col-md-9">
            <input type="text" id="last_name" name="last_name" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" value="{{old('last_name')?:@$data->last_name}}">
            @if ($errors->has('last_name'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('last_name') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="email">@lang('modules.email') <code>*</code></label>
          <div class="col-md-9">
            <input type="email" id="email" name="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"  value="{{old('email')?:@$data->email}}" required>
            @if ($errors->has('email'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('email') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="password">@lang('modules.password') <br/><small class="text-muted"><i>@lang('modules.leave_it_text')</i></small></label>
          <div class="col-md-9">
            <input type="password" id="password" name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"  value="">
            @if ($errors->has('password'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('password') }}</strong>
              </span>
            @endif
          </div>
        </div>

        @if(!\Auth::user()->company_id)
        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="company_id">@lang('modules.company') <code>*</code></label>
          <div class="col-md-9">
            <select class="form-control" name="company_id" id="company_id" required>
              @if(count($companies) > 0)
              <option value=""></option>
              @endif
              @foreach($companies as $k => $v)
              <option value="{{$k}}"{{old('company_id') === $k || @$data->company_id == $k ? ' selected':''}}>{{$v}}</option>
              @endforeach
            </select>
            @if ($errors->has('company_id'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('company_id') }}</strong>
              </span>
            @endif
          </div>
        </div>
        @endif

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="role_id">@lang('modules.role') <code>*</code></label>
          <div class="col-md-9">
            <select class="form-control" name="role_id" id="role_id" required>
              <option></option>
                @foreach(\App\Role::getLists(@$data->company_id ?: \Auth::user()->company_id) as $k => $v)
                  @php
                    $role = \App\Role::find($k);
                  @endphp
                <option value="{{$k}}"{{old('role_id') === $k || @$data->role_id == $k ? ' selected':''}} data-learner="{{$role->is_learner}}">{{$v}}</option>
                @endforeach
            </select>
            @if ($errors->has('role_id'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('role_id') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="team_id">@lang('modules.team')</label>
          <div class="col-md-9">
            <select class="form-control" name="team_id" id="team_id">
              <option></option>
                @foreach(\App\Team::getLists(@$data->company_id ?: \Auth::user()->company_id) as $k => $v)
                <option value="{{$k}}"{{old('team_id') === $k || @$data->team_id == $k ? ' selected':''}}>{{$v}}</option>
                @endforeach
            </select>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="department">@lang('modules.department')</label>
          <div class="col-md-9">
            <input type="department" id="department" name="department" class="form-control{{ $errors->has('department') ? ' is-invalid' : '' }}"  value="{{old('department')?:@$data->department}}">
            @if ($errors->has('department'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('department') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="active">@lang('modules.active')</label>
          <div class="col-md-9">
            <label class="switch switch-label switch-primary">
              <input type="checkbox" name="active" class="switch-input" {{isset($data) && $data->active == 0 ? '' : 'checked'}}>
              <span class="switch-slider" data-checked="&#x2713" data-unchecked="&#x2715"></span>
            </label>
          </div>
        </div>



      </div>
      <div class="card-footer">
        @include('components.form_submit')
      </div>
      </form>
      @endif
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  $("#company_id").on("change", function(){
    var company_id = $(this).val();

    $.ajax({
        type: 'GET',
        url: "{{ route('api.get-roles') }}",
        data: {
            'company_id': company_id,
        },
        dataType: 'json',
        success: function(res) {
          var options = '<option value=""></option>';
          $.each(res, function(i) {
              options += '<option value="'+res[i].id+'" data-learner="'+res[i].is_learner+'">'+res[i].role_name+'</option>';
          });

          $("#role_id").empty().append(options);
        }
    });

    $.ajax({
        type: 'GET',
        url: "{{ route('api.get-teams') }}",
        data: {
            'company_id': company_id,
        },
        dataType: 'json',
        success: function(res) {
          var options = '<option value=""></option>';
          $.each(res, function(i) {
              options += '<option value="'+res[i].id+'">'+res[i].team_name+'</option>';
          });

          $("#team_id").empty().append(options);
        }
    });
  })

  $("#role_id").on("change", function(){
    if($(this).find(':selected').data('learner') == 1)
    {
      $("#company_id").attr("required", true);
    }else{
      $("#company_id").attr("required", false);
    }
  });
});
</script>
@endpush
