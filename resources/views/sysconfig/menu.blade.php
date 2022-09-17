@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="fa fa-list"></i>
          {{$title}}
      </div>
      <form action="{{route('menu.store')}}" method="post" class="form-horizontal">
        @csrf()
      <div class="card-body">

        <div class="alert alert-info">
          @lang('modules.customize_the_label_of_menu').
        </div>

        @foreach($listMenu as $level => $menu)
        <h5>{{ucfirst($level)}}</h5>
          @foreach($menu as $id)
          @php
            $label = \App\Menu::findMenu($level == 'client' ? true:false, $id);
          @endphp
          <div class="form-group row">
            <div class="col-sm-12">
              <input type="text"name="menu[{{$level}}][{{$id}}]" class="form-control" value="{{@$label->label ?: __('menu.'.$id)}}" placeholder="{{__('menu.'.$id)}}">
            </div>
          </div>
          @endforeach
        @endforeach


      </div>
      <div class="card-footer">
        @include('components.form_submit')
      </div>
      </form>
    </div>
  </div>
</div>
@endsection
