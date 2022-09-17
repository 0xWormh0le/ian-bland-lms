@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-sm-8">
            <div class="card">
              <div class="card-header">
                <div class="row">
                  <div class="col-sm-6">
                    <strong><i class="fa fa-receipt"></i> @lang('modules.ticket_details')</strong>
                  </div>
                  <div class="col-sm-6 text-right">
                    @if(validate_role('tickets.assign') && $data->status == 'open')
                     <div class="float-right pl-4"><button type="button" class="btn btn-md btn-warning assign-ticket" data-id="{{$data->id}}">@lang('controllers.assign_to')</button></div>
                    @endif
                  <div>  <form id="ticketStatus" action="{{ route('tickets.status', encrypt($data->id)) }}" method="POST">
                      @csrf
                      @if($data->status == 'closed')
                      <button type="button" id="setTicketStatus" class="btn btn-md btn-success">@lang('modules.re_open_ticket')</button>
                      @else
                      <button type="button" id="setTicketStatus" class="btn btn-md btn-danger">@lang('modules.close_ticket')</button>
                      @endif
                    </form>
                  </div>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-12">
                    <div class="alert alert-info">
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
                    </div>
                  </div>

                  <div class="col-12" id="responses" data-id="{{ $data->ticket_number }}">
                  @foreach($data->responses as $r)
                    @if($r->responder_id)
                    <div class="alert alert-danger text-right">
                    @else
                    <div class="alert alert-info">
                    @endif
                      <strong>
                      @if($r->responder_id)
                      {{ $r->user->first_name .' '. $r->user->last_name }}
                      @else
                      {{ $r->ticket->user->first_name .' '. $r->ticket->user->last_name }}
                      @endif
                      : </strong>{!! $r->content !!}
                      <small class="text-muted"><i>{{ datetime_format($r->created_at, 'j M, H:i') }}</i></small>
                    </div>
                  @endforeach
                  </div>

                  @if($data->status !== 'closed' && validate_role('tickets.respond'))
                  <div class="col-12">
                    <textarea class="form-control" id="chat-response" data-url="{{route('tickets.response')}}" data-ticket_id="{{$data->id}}"></textarea>
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
          <div class="col-sm-4">
            <table class="table table-striped table-bordered">
              <tr>
                <th colspan="2"><i class="fa fa-info-circle"></i> @lang('modules.ticket_information')</th>
              </tr>
              <tr>
                <td width="120">@lang('modules.ticket_id')</td>
                <td>{{@$data->ticket_number}}</td>
              </tr>
              <tr>
                <td>@lang('modules.ticket_user')</td>
                <td>{{@$data->sender_name}} <a href="mailto:{{@$data->sender_email}}" class="text-info">{{@$data->sender_email}}</a></td>
              </tr>
              @if(@$data->assigned_to > 0)
               @php
                 $assignUser = \App\User::find($data->assigned_to);
               @endphp
              <tr>
                <td>@lang('modules.assigned_to')</td>
                <td>{{@$assignUser->first_name}} {{@$assignUser->last_name}}</td>
              </tr>
              @endif
              <tr>
                <td width="120">@lang('modules.company')</td>
                <td>{{@$data->company_name}}</td>
              </tr>
              <tr>
                <td>@lang('modules.ticket_source')</td>
                <td>{{@$data->source}}</td>
              </tr>
              <tr>
                <td>@lang('modules.opened_on')</td>
                <td>{{@$data->created_at ? datetime_format($data->created_at, 'd/m/Y H:i') : '-'}}</td>
              </tr>
              <tr>
                <td>@lang('modules.status')</td>
                <td>{{@$data->status}}</td>
              </tr>
            </table>

            <table class="table table-striped table-bordered">
              <tr>
                <th colspan="2"><i class="fa fa-history"></i> @lang('modules.ticket_history')</th>
              </tr>
              @foreach($histories as $r)

              <tr>
                <td>
                  @if($r->comments)
                  {{@$r->comments}}
                  @else
                  {{@$r->action}} by {{@$r->user->first_name}} {{@$r->user->last_name}}
                  @endif
                   on <small class="text-muted">
                  {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $r->created_at)->diffForHumans()}}</small>

                </td>
                <td>@lang('modules.ticket_status') : {{$r->ticket_status}}</td>
              </tr>
              @endforeach

            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection


@push('modals')
<div id="assignTicketModal" class="modal" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">@lang('modules.assign_ticket')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group row">
          <label class="col-4">@lang('modules.assign_to')</label>
          <div class="col-8">
            <input type="hidden" id="ticket_id" value="{{@$data->id}}">
            <input type="hidden" id="assignee_url" value="{{route('tickets.get.assignee')}}" />
            <select id="assigned_to" class="form-control select2" style="width:100%;" required>
              <option value="">@lang('modules.select')</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer text-right">
        <button type="button" id="submitAssign" class="btn btn-primary" data-url="{{ route('tickets.assign') }}">@lang('modules.submit')</button>
      </div>
    </div>
  </div>
</div>
@endpush
@include('_plugins.datatables')
@include('_plugins.select2')
@push('scripts')
  <script src="{{ asset('vendors/ckeditor/ckeditor.js') }}"></script>
  <script src="{{ mix('scripts/tickets/index.js') }}"></script>
  <script>
    @if($data->status !== 'closed' && validate_role('tickets.respond'))
    CKEDITOR.replace( 'chat-response' );

    $("#send-response").on("click", function(){
      $.ajax({
        type: 'POST',
        url: $("#chat-response").data('url'),
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'ticket_id': $("#chat-response").data('ticket_id'),
            'content': CKEDITOR.instances['chat-response'].getData(),
        },
        dataType: 'json',
        success: function(result) {
          CKEDITOR.instances['chat-response'].setData('');
          $("#responses").append(`
            <div class="alert alert-danger text-right">
              <strong>`+result.sender+` : </strong>`+result.message+`
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
    @endif

    $("#setTicketStatus").on("click", function(e){
      e.preventDefault();
      swal({
        title: 'Are you sure?',
        text: "",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes!',

      }).then((result) => {
        if (result.value)
        {
          $("#ticketStatus").submit();


        }
      })
    })
  </script>
@endpush
