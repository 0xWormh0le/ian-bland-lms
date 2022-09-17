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
      <form action="{{isset($data) ? route('courses.update', $data->id) : route('courses.store')}}" method="post" class="form-horizontal" file="true" enctype="multipart/form-data">
        @csrf()
        @isset($data)
          @method('put')
        @endisset
      <div class="card-body">

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="title">@lang('modules.title')   @if(!@$data || @$data->created_by == \Auth::user()->id || \Auth::user()->isSysAdmin())<code>*</code>@endif</label>
          <div class="col-md-9">
          @if(!@$data || @$data->created_by == \Auth::user()->id || \Auth::user()->isSysAdmin())
            <input type="text" id="title" name="title" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" value="{{old('title')?:@$data->title}}" required autofocus>
          @else
             {{@$data->title}}
             <input type="hidden" id="title" name="title"  value="{{@$data->title}}" >
          @endif
            @if ($errors->has('title'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('title') }}</strong>
              </span>
            @endif
          </div>
        </div>
        @if(!@$data || @$data->created_by == \Auth::user()->id || \Auth::user()->isSysAdmin())
        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="en_title">@lang('modules.en_title')</label>
          <div class="col-md-9">
            <input type="text" id="en_title" name="en_title" class="form-control{{ $errors->has('en_title') ? ' is-invalid' : '' }}" value="{{old('en_title')?:@$data->en_title}}"  autofocus>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="title">@lang('modules.category') <code>*</code></label>
          <div class="col-md-9">

            <select name="category_id" id="category_id" class="form-control{{ $errors->has('category_id') ? ' is-invalid' : '' }}" required>
              <option value="">Please Select</option>
              @foreach($categories as $category)
               @if(old('category_id') !== null))
                <option value="{{$category->id}}" {{old('category_id')==$category->id?'selected':''}}>{{$category->title}}</option>
               @else
                <option value="{{$category->id}}" @if(isset($data)) {{$data->category_id==$category->id?'selected':''}} @endif>{{$category->title}}</option>
              @endif
              @endforeach
            </select>
            @if ($errors->has('category_id'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('category_id') }}</strong>
              </span>
            @endif
          </div>
        </div>
        @else
           <div><input type="hidden" id="category_id" name="category_id"  value="{{@$data->category_id}}" ></div>
        @endif

        @if(!@$data || @$data->created_by == \Auth::user()->id || \Auth::user()->isSysAdmin())
        <div class="form-group row" id="sub_category_row" style="display:{{ (@count(@$data->sub_categories) > 0) ?'':'none'}}">
          <label class="col-md-3 col-form-label" for="title">{{trans_choice('modules.subcategory', 0)}}</label>
          <div class="col-md-9">
            <select name="sub_category_id" id="sub_category_id" class="form-control">
              <option value="">@lang("modules.please_select")</option>
            @if(isset($data->sub_categories))
              @foreach($data->sub_categories as $sub_category)
                <option value="{{$sub_category->id}}" @if(isset($data)){{$data->sub_category_id==$sub_category->id?'selected':''}} @endif>{{$sub_category->title}}</option>
              @endforeach
            @endif
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="description">@lang('modules.description')</label>
          <div class="col-md-9">
            <textarea id="description" name="description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" rows="5">{{old('description')?:@$data->description}}</textarea>
            @if ($errors->has('description'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('description') }}</strong>
              </span>
            @endif
          </div>
        </div>
        @endif

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="deadline_date">@lang('modules.deadline')</label>
          <div class="col-md-3">
          @if(\Auth::user()->isSysAdmin())
            <input type="text" id="deadline_date" name="deadline_date" class="form-control datepicker{{ $errors->has('deadline_date') ? ' is-invalid' : '' }}" value="{{old('deadline_date')?:@$data->deadline_date}}">
            @if ($errors->has('deadline_date'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('deadline_date') }}</strong>
              </span>
            @endif
          @else
            <div class="float-left"><input type="text" id="deadline"  name="deadline" style="width:60px; margin-right:10px" class="form-control {{ $errors->has('deadline') ? ' is-invalid' : '' }}" value="{{old('deadline')?:@$data->deadline}}"></div>
            <div ><select name="deadline_duration" class="form-control" style="width:100px" >
              <option value="day" {{isset($data) && $data->deadline_part=="day"?'selected':""}}>@lang("modules.days")</option>
              <option value="week" {{isset($data) && $data->deadline_part=="week"?'selected':""}}>@lang("modules.weeks")</option>
              <option value="month" {{isset($data) && $data->deadline_part=="month"?'selected':""}}>@lang("modules.months")</option>
              <option value="year" {{isset($data) && $data->deadline_part=="year"?'selected':""}}>@lang("modules.years")</option>
            </select> ( @lang("modules.from_enrolment"))</div>

            @if ($errors->has('deadline'))
              <span class="invalid-feedback" style="display:block" role="alert">
              <strong>{{ $errors->first('deadline') }}</strong>
              </span>
            @endif
          @endif
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="cover_image">@lang('modules.course_image')</label>
          <div class="col-md-3">
            <div class="main-img-preview">
                <img id="avatar_img" class="thumbnail img-preview" src="{{(@$data->image ? asset('storage/courses/images/'.$data->image) : asset('img/no-img.jpg'))}}" title="@lang('modules.preview')" style="width:100%;" >
            </div>
            @if(!@$data || @$data->created_by == \Auth::user()->id || \Auth::user()->isSysAdmin())
            <input id="cover_image" name="cover_image" type="file" class="attachment_upload" accept=".png, .jpeg, .jpg">
            @endif
            @if ($errors->has('cover_image'))
              <span class="invalid-feedback" style="display:block" role="alert">
                  @foreach($errors->get('cover_image') as $error)
                    {{ $error }} <br>
                  @endforeach
              </span>
            @endif
          </div>
        </div>

        @if(!@$data || @$data->created_by == \Auth::user()->id || \Auth::user()->isSysAdmin())

        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="duration">@lang('modules.duration') <code>*</code></label>
          <div class="col-md-6">
            <input type="text" id="duration"  name="duration" class="col-md-2 float-left form-control{{ $errors->has('duration') ? ' is-invalid' : '' }}" value="{{old('duration')?:@$data->duration_num}}" required autofocus>
            <select name="duration_type" class="form-control col-md-3">
              @foreach($course_durations as $cd)
               <option values="{{$cd}}" @if(isset($data)){{$data->duration_type==$cd?'selected':''}} @endif>{{$cd}}</option>
              @endforeach
            </select>
            @if ($errors->has('duration'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('duration') }}</strong>
              </span>
            @endif
          </div>
        </div>
        @else
         <div>
            <input type="hidden" name="duration" value="{{@$data->duration_num}}" />
            <input type="hidden" name="duration_type" value="{{@$data->duration_type}}" />
         </div>
        @endif
        @if(!@$data || @$data->created_by == \Auth::user()->id || \Auth::user()->isSysAdmin())

          <div class="form-group row">
          <label class="col-md-3 col-form-label" for="title">@lang('modules.language')</label>
          <div class="col-md-9">
            <select name="language" class="form-control" style="width:210px">
              @foreach(config('app.languages') as $key=>$val)
               <option value="{{$key}}" @if(isset($data)){{$data->language==$key?'selected':''}} @endif>{{$val}}</option>
              @endforeach
            </select>
          </div>
        </div>
        @endif

       @if(\Auth::user()->isClientAdmin())
        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="notification-reminder">@lang('modules.notification_reminder')</label>
          <div class="col-md-2">
            <input type="text" id="notification_reminder" name="notification_reminder" class="form-control {{ $errors->has('notification_reminder') ? ' is-invalid' : '' }}" value="{{old('notification_reminder')?:@$data->notification_reminder}}"  autofocus>
            (@lang('modules.reminder_frequency'))
            @if ($errors->has('notification_reminder'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('notification_reminder') }}</strong>
              </span>
            @endif
          </div>
        </div>


        <div class="form-group row">
          <label class="col-md-3 col-form-label" for="active">@lang('modules.completion_notification')</label>
          <div class="col-md-9">
            <label class="switch switch-label switch-primary">
              <input type="checkbox" name="completion_notification" class="switch-input" {{isset($data) && $data->completion_notification == 'on' ? 'checked' : ''}}>
              <span class="switch-slider" data-checked="&#x2713" data-unchecked="&#x2715"></span>
            </label>
          </div>
        </div>
       @endif


      </div>
      <div class="card-footer">
        @include('components.form_submit')
      </div>
      </form>
    </div>
  </div>
</div>

@if(!\Auth::user()->isSysAdmin())
  @include('courses.config_form')
@endif

@endsection

@include('_plugins.datepicker')

@push('scripts')
<script>
$(document).ready(function() {
    var brand = document.getElementById('cover_image');
        // brand.className = 'attachment_upload';
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('.img-preview').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#cover_image").change(function() {
            readURL(this);
        });

        $("select[name='category_id']").change(function(){

           var category_id =$(this).val() ;
           $.ajax({

                 url: "{{route('ajax_subcategory')}}",
                 method: 'POST',
                 type: 'json',
                 data: {'category_id': category_id,_token: '{{csrf_token()}}'},
                 success: function(data) {
                      var options = '<option value="">Please Select</option>';

                      for(var i=0;i < data.length; i++)
                      {
                        options += '<option value="'+data[i].id+'">'+data[i].title+'</option>';
                      }
                    if(data.length > 0)
                    {
                      $('#sub_category_id').html(options);
                      $('#sub_category_row').css('display','');
                    }
                    else
                    {
                        $('#sub_category_row').css('display','none');
                    }
                  }
               });


        });
});
</script>
@endpush
