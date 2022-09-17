@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="icon-badge"></i>
          {{$title}}
      </div>
      <form action="{{isset($data) ? route('certificate-templates.update', $data->id) : route('certificate-templates.store')}}" method="post" class="form-horizontal" file="true" enctype="multipart/form-data">

        @csrf()
        @isset($data)
          @method('put')
        @endisset

      <div class="card-body">
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group row">
              <label class="col-sm-4" for="name">@lang('modules.template_name')</label>
              <div class="col-sm-8">
                <input type="text" name="name" value="{{old('name')?:@$data->name}}" class="form-control" required>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-4" for="orientation">@lang('modules.page_orientation')</label>
              <div class="col-sm-8">
                <select name="orientation" value="{{old('orientation')?:@$data->orientation}}" class="form-control" required>
                  @foreach(['landscape', 'portrait'] as $v)
                    <option value="{{$v}}"{{old('orientation') == $v || @$data->orientation == $v ? ' selected':''}}>{{ucfirst($v)}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-4" for="pagesize">@lang('modules.page_size')</label>
              <div class="col-sm-8">
                <select name="pagesize" value="{{old('pagesize')?:@$data->pagesize}}" class="form-control" required>
                  @foreach(['a4', 'letter'] as $v)
                    <option value="{{$v}}"{{old('pagesize') == $v || @$data->pagesize == $v ? ' selected':''}}>{{ucfirst($v)}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-4" for="name">@lang('modules.background_image')</label>
              <div class="col-sm-3">
                <img id="background_image" src="{{ asset($background) }}" class="img-responsive" width="100%">
                <br/>
                <br/>
                <button type="button" id="upload" class="btn btn-sm btn-block btn-primary">@lang('modules.upload_image')</button>
                <input type="file" id="background" name="background" value="{{old('background')}}" style="display:none;" accept="image/*" data-role="none">
              </div>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="card">
              <div class="card-header">
                <i class="fa fa-code"></i> @lang('modules.template_code')
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <textarea id="code" name="code">{{ $html }}</textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card-footer">
        <button type="submit" class="btn btn-md btn-primary">@lang('modules.save_and_preview')</button>
      </div>
      </form>
    </div>
  </div>

  @isset($data)
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <i class="icon-picture"></i> @lang('modules.preview')
      </div>
      <div class="card-body">
          <iframe src="{{ route('certificate-templates.preview', $data->id) }}#view=FitH&toolbar=0&navpanes=0&scrollbar=0" frameborder="0" class="grid-100 tablet-grid-100 mobile-grid-100 grid-parent" style="background:#f0f0f0; width:100%; min-height:500px;"></iframe>

          <!--
            Thumbnail :
          <iframe src="{{ route('certificate-templates.preview', $data->id) }}#view=FitH&toolbar=0&navpanes=0&scrollbar=0" frameborder="0" class="grid-100 tablet-grid-100 mobile-grid-100 grid-parent" style="background:#f0f0f0; width:210px; height:auto;"></iframe>
          -->
      </div>
    </div>
  </div>
  @endisset

</div>
@endsection

@push('css')
<!-- CodeMirror Style & Theme -->
<link href="{{asset('vendors/codemirror/addon/lint/lint.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('vendors/codemirror/lib/codemirror.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('vendors/codemirror/theme/monokai.css')}}" rel="stylesheet" type="text/css" />
@endpush

@push('js')
 <!-- The CodeMirror -->
<script src="{{asset('vendors/codemirror/lib/codemirror.js')}}" type="text/javascript"></script>
<!-- The CodeMirror Modes - note: for HTML rendering required: xml, css, javasript -->
<script src="{{asset('vendors/codemirror/mode/xml/xml.js')}}" type="text/javascript"></script>
<script src="{{asset('vendors/codemirror/mode/css/css.js')}}" type="text/javascript"></script>
<script src="{{asset('vendors/codemirror/mode/javascript/javascript.js')}}" type="text/javascript"></script>
<script src="{{asset('vendors/codemirror/mode/htmlmixed/htmlmixed.js')}}" type="text/javascript"></script>
<!-- KeyMap -->
<script src="{{asset('vendors/codemirror/keymap/sublime.js')}}" type="text/javascript"></script>
<!-- CodeMirror Addons -->
<script src="{{asset('vendors/codemirror/addon/selection/active-line.js')}}"></script>
<script src="{{asset('vendors/codemirror/addon/lint/lint.js')}}"></script>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
  var editor = CodeMirror.fromTextArea(code, {
        mode: "text/html",
        extraKeys: {"Ctrl-Space": "autocomplete"},
        lineNumbers: true,
        keyMap: 'sublime',
        theme : 'monokai',
      });


  $("#upload").click(function() {
    $("#background").click();
  })

  function readURL(input) {
      if (input.files && input.files[0]) {
          var reader = new FileReader();
          reader.onload = function(e) {
              $('#background_image').attr('src', e.target.result);
          };
          reader.readAsDataURL(input.files[0]);
      }
  }
  $("#background").change(function() {
      readURL(this);
  });

});
</script>
@endpush
