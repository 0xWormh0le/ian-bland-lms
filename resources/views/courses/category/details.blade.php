@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <i class="icon-info"></i>
              {{$title}}
          </div>
          <div class="col-sm-6 text-right">
            {!! show_button('update', 'category.edit', $data->id) !!}

            {!! show_button('remove', 'category.destroy', encrypt($data->id)) !!}
          </div>
        </div>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-sm-8">
            <dl class="row">
              <dd class="col-sm-3">@lang("modules.title")</dd>
              <dt class="col-sm-9">{{$data->title}}</dt>

              <dd class="col-sm-3">@lang("modules.parent")</dd>
              <dt class="col-sm-9">
               @if($parent && $parent!="")
                {{$parent->title}}
               @endif
              </dt>
            </dl>
          </div>
       </div>

        @include('components.record_log')
      </div>
    </div>
  </div>
</div>
@endsection
