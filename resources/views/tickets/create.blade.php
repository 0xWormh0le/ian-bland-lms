 @extends('layouts.app')

@section('title', 'Create Ticket')

@section('content')
<form enctype="multipart/form-data" method="post" action="{{ route('tickets.store') }}">
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-header">
          <i class="nav-icon fa fa-envelope-open-text"></i>
          <strong>@lang('modules.create_ticket')</strong>
        </div>
        @csrf
        <div class="card-body">
          <div class="form-group row align-items-baseline">
            <div class="col-sm-4 col-md-3 col-xl-2 text-sm-right">
              <label for="chat_name">@lang('modules.your_name')</label>
            </div>
            <div class="col-sm-5 col-md-4 col-xl-3">
              <input type="text" class="form-control" id="chat_name" name="chat_name" value="{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}" required>
            </div>
          </div>

          <div class="form-group row align-items-baseline">
            <div class="col-sm-4 col-md-3 col-xl-2 text-sm-right">
              <label for="chat_email">@lang('modules.email_address')</label>
            </div>
            <div class="col-sm-5 col-md-4 col-xl-3">
              <input type="email" class="form-control" id="chat_email" name="chat_email" value="{{ auth()->user()->email }}" required>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-sm-4 col-md-3 col-xl-2 text-sm-right">
              <label>@lang('modules.how_we_can_help_you')</label>
            </div>
            <div class="col-md-9 col-xl-10">
              <textarea class="form-control d-none" id="chat_message" name="chat_message" rows="3" required></textarea>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-sm-4 col-md-3 col-xl-2 text-sm-right">
              <label for="attachment-file">@lang('modules.attachments')</label>
            </div>
            <div class="col-sm-8 col-md-9 col-xl-10">
              
              <div class="chat-attachment-group">
                <div class="row mb-2">
                  <div class="col-12">
                    <button type="button" class="btn btn-default add_chat_files"><i class="fa fa-paperclip"></i> @lang('modules.add_up_to_files')</button>
                  </div>
                </div>
                <div class="row chat-attachment-row mb-2" style="display:none;">
                  <div class="col-9 d-flex">
                    <div class="chat-attachment">
                      <button type="button" class="btn btn-default chat-upload">@lang('modules.filename')</button>
                      <input type="file" class="attachment-file" name="attachment[]" />
                    </div>
                  </div>
                  <div class="col-3 d-flex justify-content-end">
                    <button type="button" class="btn btn-danger rm-attach" title="@lang('modules.remove_attachment')"><i class="fa fa-trash-alt"></i></button>
                  </div>
                </div>
              </div>
              
            </div>
          </div>
        </div>
        
        <div class="card-footer">
          <div class="row">
            <div class="col-6">
              <button type="reset" class="btn btn-secondary">@lang('modules.cancel')</button>
            </div>
            <div class="col-6 text-right">
              <button class="btn btn-primary">@lang('modules.send') <i class="fa fa-paper-plane"></i></button>
            </div>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</form>
@endsection

@push('scripts')
<script src="{{ asset('vendors/ckeditor/ckeditor.js') }}"></script>
<script src="{{ mix('scripts/tickets/chat.js') }}"></script>
<script>
  CKEDITOR.replace('chat_message');
  CKEDITOR.config.height="250px";
</script>
@endpush
