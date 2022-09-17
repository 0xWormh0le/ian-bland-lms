@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-sm-12">
            <div class="card">
              <div class="card-header">
                <div class="row">
                  <div class="col-sm-12">
                    <strong><i class="fa fa-receipt"></i> @lang('modules.ticket_details')</strong>
                    @if($data->status == 'closed')
                      <span class="badge badge-pill badge-danger">@lang('modules.closed')</span>
                    @else
                      <span class="badge badge-pill badge-success">@lang('modules.open')</span>
                    @endif
                  </div>
                  <div class="col-sm-4">

                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-12">
                    <div class="alert alert-info text-right">
                      {!! $data->content !!}
                      @if($data->attachment_id)
                        <br/>
                        @foreach(explode(',', $data->attachment_id) as $attachment_id)
                          @php
                            $file = \App\TicketAttachment::find($attachment_id);
                          @endphp
                          @if($file)
                            <a href="{{ route('tickets.attachment', encrypt($file->id)) }}" class="btn btn-sm btn-secondary">{{ $file->filename }}</a>
                          @endif
                        @endforeach
                      @endif
                      <br/>
                      <br/>
                      <small><i>{{ datetime_format($data->created_at,'j M, H:i') }}</i></small>
                    </div>
                  </div>

                  <div class="col-12" id="responses" data-id="{{ $data->ticket_number }}">
                  @foreach($data->responses as $r)
                    @if($r->responder_id)
                    <div class="alert alert-danger">
                    @else
                    <div class="alert alert-info text-right">
                    @endif
                      <strong>
                      @if($r->responder_id)
                        {{ $r->user->first_name .' '. $r->user->last_name }}
                      @else
                        Me
                      @endif
                       : </strong>{!! $r->content !!}
                      <small class="text-muted"><i>{{ datetime_format($r->created_at,'j M, H:i') }}</i></small>
                    </div>
                  @endforeach
                  </div>

                  @if($data->status !== 'closed')
                  <div class="col-12">
                    <textarea class="form-control" id="chat-response" data-url="{{route('my-tickets.response', encrypt($data->id))}}"></textarea>
                  </div>
                  <div class="col-12 text-right">
                    <br/>
                    <button id="send-response" class="btn btn-md btn-primary">@lang('modules.reply') <i class="fa fa-paper-plane"></i></button>
                  </div>
                  @endif

                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection


@push('scripts')
  <script src="{{ asset('vendors/ckeditor/ckeditor.js') }}"></script>
  <script>
    CKEDITOR.replace( 'chat-response' );

    $("#send-response").on("click", function(){
      $.ajax({
        type: 'POST',
        url: $("#chat-response").data('url'),
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'content': CKEDITOR.instances['chat-response'].getData(),
        },
        dataType: 'json',
        success: function(result) {
          CKEDITOR.instances['chat-response'].setData('');
          $("#responses").append(`
            <div class="alert alert-info text-right">
              <strong>Me : </strong>`+result.message+`
              <small class="text-muted"><i>`+result.datetime+`</i></small>
            </div>
          `);
          swal({
            type: 'success',
            title: 'Send Successful',
            text: '',
            showConfirmButton: false,
            timer: 2000,
          });
        }
      });
    })
  </script>
@endpush
