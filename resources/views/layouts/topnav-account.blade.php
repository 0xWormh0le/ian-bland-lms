@if(\Auth::user()->isSysAdmin())
@php
  $unread = \App\Ticket::where('assigned_to', \Auth::id())->where('read_by_admin', false);
  $unread = $unread->count();
@endphp
<li class="nav-item d-md-down-none">
          <a class="nav-link" href="{{route('tickets.open')}}" title="Incoming Tickets">
            <i class="fa fa-envelope-open-text"></i>
            <span class="badge badge-pill badge-danger" id="activeticket_counts" style="@if($unread == 0)display:none;@endif">{{$unread}}</span>
          </a>
        </li>
@endif
@if(!\Auth::user()->isSysAdmin())
@php

  if(Auth::user()->isClientAdmin())
  {
    $unread = \App\Ticket::where('read_by_client_admin', false); //company admin

    $unread->where('company_id', \Auth::user()->company_id)
          ->where('created_by', '!=', auth()->user()->id)
          ->where('status', 'open');
  }
  else
  {
    $unread = \App\Ticket::where('read_by_user', false);   //learner
    $unread->where('created_by', \Auth::id());
  }

  $unread = $unread->count();
@endphp
<li class="nav-item d-md-down-none">
      @if(Auth::user()->isClientAdmin())
        <a class="nav-link" href="{{route('tickets.open')}}" title="Incoming Tickets">
      @else
        <a class="nav-link" href="{{route('my-tickets.index')}}" title="Incoming Tickets">
      @endif
            <i class="fa fa-envelope-open-text"></i>
            <span class="badge badge-pill badge-danger" id="activeticket_counts" style="@if($unread == 0)display:none;@endif">{{$unread}}</span>
          </a>
        </li>
@endif
<li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">

          @if(\Auth::user()->avatar)
            <img class="img-avatar" src="{{\Storage::disk('public')->url('avatars/'.\Auth::user()->avatar)}}" alt="{{\Auth::user()->first_name}}">
          @else
            <img class="img-avatar" src="{{route('user.avatar.initial', \Auth::id())}}" alt="{{\Auth::user()->first_name}}">
          @endif
          </a>
          <div class="dropdown-menu dropdown-menu-right">
            <div class="dropdown-header text-center">
              <strong>{{\Auth::user()->first_name}} {{\Auth::user()->last_name}}</strong>
            </div>
            <a class="dropdown-item" href="{{route('user.profile')}}">
              <i class="icon-user"></i>@lang('navigation.update_profile')</a>
            <a class="dropdown-item" href="{{route('user.password')}}">
              <i class="icon-key"></i>@lang('navigation.change_password')</a>
              @php
                $unread = \App\Ticket::where('created_by', \Auth::id())->where('read_by_user', false)->count();
              @endphp

              @if(!\Auth::user()->isSysAdmin())
                <a class="dropdown-item" href="{{ route('my-tickets.index') }}">
                  <i class="fa fa-envelope-open-text"></i>@lang('navigation.my_tickets')<span class="badge badge-pill badge-danger" id="unread_tickets" @if($unread==0) style="display:none;" @endif>{{$unread}}</span></a>
              @endif

            @if(\Auth::user()->isClient())

            <a class="dropdown-item showChat" href="javascript:void(0);">
              <i class="fa fa-headset"></i>@lang('navigation.contact_support')</a>
            @endif

            @if (\Auth::user()->isImpersonated())
              <a class="dropdown-item" href="{{ route('users.leave-impersonate') }}">
                <i class="fa fa-sign-out-alt"></i>@lang('navigation.logout')
              </a>
            @else
            <a class="dropdown-item" href="{{ route('logout') }}"
                onclick="event.preventDefault();
                              document.getElementById('logout-form').submit();">
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
              <i class="fa fa-sign-out-alt"></i>@lang('navigation.logout')</a>
            @endif
          </div>
        </li>
