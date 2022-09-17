@extends('layouts.app')

@section('title', $title)

@section('content')

<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body row">
        <div class="col-md-3 pb-2">
          <div class="input-group">
            <select name="language" class="form-control" >
              <option value="0">All Languages</option>
              @foreach(config('app.languages') as $key=>$val)
               <option value="{{$key}}">{{$val}}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="col-md-3 pb-2">
          <div class="input-group">
            <select name="category" class="form-control" >
               <option value="0">All Categories</option>
              @foreach($parentCategory as $category)
               <option value="{{$category->id}}">{{$category->title}}</option>
              @endforeach
            </select>
            <input type="hidden" id="course_url" value="{{route('ajax.courses')}}"/>
            <input type="hidden" id="subcategory_url" value="{{route('ajax_subcategory')}}"/>
            <input type="hidden" id="token" value="{{csrf_token()}}"/>
          </div>
        </div>

        <div class="col-md-3 pb-2" >
          <div class="input-group" id="sub_category_option" style="display:none">
            <select id="subcategory" name="subcategory" class="form-control" >
              <option value="0">All Sub Categories</option>
            </select>
          </div>
        </div>
         <div class="col-md-3 pb-2 text-right">
           @if(\Auth::user()->isSysAdmin())
           @buttonAdd(['route'=>'courses.create'])
              @lang('modules.add_new_course')
            @endbuttonAdd
            @endif
        </div>
      </div>
    </div>
     @if(\Auth::user()->isSysAdmin())
     <div class="card">
      <div class="card-body row">
         <div class="col-sm-12">
          <div class="input-group">
            <span class="input-group-prepend">
              <button type="button" class="btn btn-primary">
                <i class="fa fa-search"></i> @lang('modules.search')</button>
            </span>
            <input type="text" id="search" name="search" class="form-control" placeholder="" autocomplete="off">
            <input type="hidden" id="page" value="{{@$page}}" />
          </div>
        </div>
      </div>
      </div>
      @endif
  </div>
</div>
<div id="courses">
@include('courses.lists')
</div>
@endsection

@push('scripts')
<script src="{{ mix('scripts/courses/index.js') }}"></script>
@endpush
