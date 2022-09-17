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
      <form action="{{isset($data) ? route('teams.update', $data->id) : route('teams.store')}}" method="post" class="form-horizontal">
        @csrf()
        <input type="hidden" id="token" value="{{csrf_token()}}" />
        @isset($data)
          @method('put')
        @endisset
      <div class="card-body">
      @if(!\Auth::user()->company_id)
        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="company_id">@lang('modules.company_name') <code>*</code></label>
          <div class="col-md-9">
            <select id="company_id" name="company_id" class="form-control{{ $errors->has('company_id') ? ' is-invalid' : '' }}" required>
              @if(count($companies) > 1)
              <option value=""></option>
              @endif
              @foreach($companies as $k => $v)
              <option value="{{$k}}"{{old('company_id') == $k || @$data->company_id == $k ? ' selected':''}}>{{$v}}</option>
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
          <label class="col-md-3 col-form-label" for="team_name">@lang('modules.team_name') <code>*</code></label>
          <div class="col-md-9">
            <input type="text" id="team_name" name="team_name" class="form-control{{ $errors->has('team_name') ? ' is-invalid' : '' }}" value="{{old('team_name')?:@$data->team_name}}" required autofocus>
            @if ($errors->has('team_name'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('team_name') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="manager_user_id">@lang('modules.team_manager')</label>
          <div class="col-md-9">
            <select id="manager_user_id" name="manager_user_id" class="form-control{{ $errors->has('manager_user_id') ? ' is-invalid' : '' }}">
              <option value=""></option>
              @foreach(\App\User::getList(\Auth::user()->company_id) as $k => $v)
              <option value="{{$k}}"{{old('manager_user_id') == $k || @$data->manager_user_id == $k ? ' selected':''}}>{{$v}}</option>
              @endforeach
            </select>
            @if ($errors->has('manager_user_id'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('manager_user_id') }}</strong>
              </span>
            @endif
          </div>
        </div>

      </div>
      <div class="card-footer">
        @include('components.form_submit')
      </div>
      </form>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
   $(document).ready(function(){
      $("#company_id").on("change", function(){

         var company_id = $(this).val();
         var token = $("#token").val();
         $.ajax({
             type : 'POST',
             url : '{{route("company.team.manager.list")}}',
             data : {'_token': token, 'company_id': company_id},

         success: function(data){
            var list = $.parseJSON(JSON.stringify(data)) ;
            var option = '<option value=""></option>';
            for(var l=0; l<list.length;l++)
            {
              option += '<option value="'+list[l].id+'">'+list[l].first_name+' '+list[l].last_name+'</option>';
            }
            $("#manager_user_id").html(option);
         },
         error: function(msg){
           swal({
             title: 'Error occured!',
             text: 'Server Error to get team manager list',
             type: 'success',
             timer: 2000,
             showConfirmButton: false,
           });
         }

       });
      })
   });
</script>
@endpush
