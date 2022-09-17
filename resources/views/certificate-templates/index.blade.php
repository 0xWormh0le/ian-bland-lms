@extends('layouts.app')

@section('title', $title)

@section('content')

<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body row">
        <div class="col-sm-4">
          <div class="input-group">
            <span class="input-group-prepend">
              <button type="button" class="btn btn-primary">
                <i class="fa fa-search"></i> @lang('modules.search')</button>
            </span>
            <input type="text" id="search" name="search" class="form-control" placeholder="" autocomplete="off">
          </div>
        </div>
        <div class="col-sm-8 text-right">
            @buttonAdd(['route'=>'certificate-templates.create'])
              @lang('modules.add_new_template')
            @endbuttonAdd
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row list">
@foreach($datas as $r)
  <div class="col-md-3 listitem" data-title="{{$r->name}}" >
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-8">{{$r->name}}</div>
          <div class="col-sm-4 text-right">
            @if($r->draft)
            <small class="text-danger">@lang('modules.draft')</small>
            @else
            <small class="text-success">@lang('modules.published')</small>
            @endif
          </div>
        </div>
      </div>
      <div class="card-body text-center" data-content="{{$r->name}}" >
        <iframe src="{{ route('certificate-templates.preview', $r->id) }}#view=FitH&toolbar=0&navpanes=0&scrollbar=0" frameborder="0" class="grid-100 tablet-grid-100 mobile-grid-100 grid-parent" style="background:#f0f0f0; width:210px; height:auto;"></iframe>
      </div>
      <div class="card-footer text-center">
            <a target="_blank" href="{{route('certificate-templates.preview', $r->id)}}" class="btn btn-sm btn-primary" title="@lang('modules.preview')"><i class="icon-screen-desktop"></i></a>

            <a href="{{route('certificate-templates.edit', $r->id)}}" class="btn btn-sm btn-warning" title="@lang('modules.edit')"><i class="icon-pencil"></i></a>

            <form action="{{route('certificate-templates.duplicate', $r->id)}}" method="POST" style="display:inline;">
              @csrf()
              <button type="submit" class="btn btn-sm btn-info duplicate" title="@lang('modules.duplicate')">
                <i class="fa fa-copy"></i>
              </button>
            </form>

            <form action="{{route('certificate-templates.destroy', $r->id)}}" method="POST" style="display:inline;">
              @method('DELETE')
              @csrf()
              <button type="submit" class="btn btn-sm btn-danger delete" title="@lang('modules.delete')">
                <i class="icon-trash"></i>
              </button>
            </form>

            <form action="{{route('certificate-templates.publish', $r->id)}}" method="POST" style="display:inline;">
              @csrf()
              @if($r->draft)
              <button type="submit" class="btn btn-sm btn-success publish" title="@lang('modules.publish')">
                <i class="icon-check"></i>
              </button>
              @else
              <button type="submit" class="btn btn-sm btn-default publish" title="@lang('modules.set_as_draft')">
                <i class="icon-close"></i>
              </button>
              @endif
            </form>


      </div>
    </div>
  </div>
@endforeach
</div>

@endsection

@push('scripts')
<script>

$(document).ready(function () {

  $("#search").keyup(function(){
    var filter = $(this).val();
    $(".listitem").each(function(){
     if ($(this).attr('data-title').search(new RegExp(filter, "i")) < 0) {
      $(this).fadeOut();
     } else {
      $(this).show();
     }
    });
  });

  $(document).on('click', '.duplicate', function (e) {
    e.preventDefault();
    var form = $(this).parents('form:first');
    swal({
      title: "Are you sure you want to duplicate this data?",
      text: "",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes',
      cancelButtonText: 'No',
    }).then((result) => {
      if (result.value) {
        form.submit();
      }
    });
  });

  $(document).on('click', '.publish', function (e) {
    e.preventDefault();
    var form = $(this).parents('form:first');
    swal({
      title: "Are you sure you want to "+$(this).attr('title')+" this ?",
      text: "",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes',
      cancelButtonText: 'No',
    }).then((result) => {
      if (result.value) {
        form.submit();
      }
    });
  });

});
</script>
@endpush
