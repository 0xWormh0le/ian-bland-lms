
@push('scripts')
  <script src="{{ asset('js/echo.js') }}"></script>
  <script src="https://js.pusher.com/4.1/pusher.min.js"></script>
  <script>
    var auth_id = "{{ Auth::id() }}",
        canAssign = "{{ validate_role('tickets.assign')}}";

    function showNotifAlert()
    {
      $("#activeticket_counts").html((parseInt($("#activeticket_counts").html())+1));
      $("#activeticket_counts").show();
    }

    Pusher.logToConsole = true;

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ env("PUSHER_APP_KEY") }}',
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
        encrypted: true,
        logToConsole: true
    });

    @if(\Auth::user()->isSysAdmin() && validate_role('tickets.respond'))
    Echo.private('ticket')
      .listen('NewTicketNotification', (e) => {
        $.notify({
          icon: 'fa fa-envelope-open-text',
          title: "<strong>New ticket from "+e.message.sender+" : </strong>",
          message: e.message.message,
          url: e.message.url,
          target: "_self"
        });
        @if(\Route::currentRouteName() == 'tickets.show')
          if(e.message.id == $("#responses").data("id"))
          {
            $("#responses").append(`
              <div class="alert alert-info">
                <strong>`+e.message.sender+` : </strong>`+e.message.message+`
                <small class="text-muted"><i>`+e.message.datetime+`</i></small>
              </div>
            `);
          }
          else{
            if(e.message.assigned_to !== 0)
            {
              if(e.message.assigned_to == auth_id){
                showNotifAlert();
              }
            }
            else{
              if(canAssign == 1){
                showNotifAlert();
              }
            }
          }
        @else
          if(e.message.assigned_to !== 0)
          {
            if(e.message.assigned_to == auth_id){
              showNotifAlert();
            }
          }
          else{
            if(canAssign == 1){
              showNotifAlert();
            }
          }
        @endif
    });

    @else
    Echo.private('message.{{ \Auth::id() }}')
      .listen('NewTicketResponseNotification', (e) => {
        $.notify({
          icon: 'fa fa-envelope-open-text',
          title: "<strong>New ticket response from "+e.message.sender+" : </strong>",
          message: e.message.message,
          url:  e.message.url,
          target: "_self"
        });

        @if(\Route::currentRouteName() == 'my-tickets.show')
        if(e.message.id == $("#responses").data("id"))
        {
          $("#responses").append(`
            <div class="alert alert-danger">
              <strong>`+e.message.sender+` : </strong>`+e.message.message+`
              <small class="text-muted"><i>`+e.message.datetime+`</i></small>
            </div>
          `);
        }
        else{
          $("#unread_tickets").html((parseInt($("#unread_tickets").html())+1));
          $("#unread_tickets").show();
        }
        @else
          $("#unread_tickets").html((parseInt($("#unread_tickets").html())+1));
          $("#unread_tickets").show();
        @endif
    });


    @endif

  </script>
@endpush
