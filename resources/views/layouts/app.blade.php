<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="keyword" content="">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @hasSection('title')
          @yield('title') -
        @endif
        {{ config('app.name', 'LMS') }}
    </title>
    <!-- Icons-->
    <link href="{{asset('vendors/font-awesome/css/all.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/font-awesome/css/v4-shims.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/simple-line-icons/css/simple-line-icons.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/pace/pace.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet">

    <!-- Main styles for this application-->
    <link href="{{asset('css/theme.min.css')}}" rel="stylesheet">
    @stack('css')
    <link href="{{mix('css/build.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/chat.css')}}" rel="stylesheet">
  
    <style>
      .navbar {
        background: #{{@session('colourTheme')['top_bar'] ?: 'FFFFFF'}};
        @if(@@session('colourTheme')['top_bar'] && @session('colourTheme')['top_bar'] !== 'FFFFFF')
          border-color: #000000;
        @endif
      }
      .navbar .text-primary {
        color: #{{@session('colourTheme')['top_bar_text'] ?: '20A8D8'}} !important;
      }
      .navbar .nav-link i {
        color: #{{@session('colourTheme')['top_bar_text'] ?: 'FFFFFF'}};
      }
      .sidebar .nav-link:hover {
          background: #{{@session('colourTheme')['active_menu'] ?: '20A8D8'}};
          color: #{{@session('colourTheme')['active_menu_hover'] ?: 'FFFFFF'}};
        }
      .sidebar .nav-link:hover .nav-icon,
      .sidebar .nav-link:hover {
          color: #{{@session('colourTheme')['active_menu_hover'] ?: 'FFFFFF'}} !important;
      }
      .sidebar .nav-link.active .nav-icon {
          color: #{{@session('colourTheme')['active_menu'] ?: '20A8D8'}};
      }
      .text-primary, a {
          color: #{{@session('colourTheme')['text_primary'] ?: '20A8D8'}};
      }
    </style>
  </head>
  <body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
    <header class="app-header navbar">
      <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon"></span>
      </button>

      @php
        if(@\Auth::user()->company && \Auth::user()->company->logo)
          $logopath = asset('storage/logo/'.Auth::user()->company->logo);
        elseif(\App\SysConfig::first() && \App\SysConfig::first()->logo)
          $logopath = asset('storage/logo/'.\App\SysConfig::first()->logo);
        else
          $logopath = asset('img/brand/logo.svg');
      @endphp
      <a class="navbar-brand" href="{{url('/')}}" style="width: 198px;">
        <img class="navbar-brand-full" src="{{ $logopath }}" width="auto" height="25" alt="Company Logo">
      </a>

      <ul class="nav navbar-nav d-md-down-none">
        <li class="nav-item px-3" style="min-width:300px; text-align:left;">
          <strong class="text-primary">

          @if(@\Auth::user()->company && \Auth::user()->company->top_heading)
            {{ \Auth::user()->company->top_heading }}
          @elseif(@\App\SysConfig::first() && \App\SysConfig::first()->top_heading)
            {{ \App\SysConfig::first()->top_heading }}
          @else
            LMS
          @endif
          </strong>
        </li>
      </ul>
      <ul class="nav navbar-nav ml-auto">
        <li>
           <label for="lang">@lang('navigation.language') : </label>
           <span>
                 <select id="lang" name="lang">
                          <option value="en" {{ \Session::get('locale')=="en" ? 'selected' : ''}}>English</option>
                          <option value="zh_CN" {{ \Session::get('locale')=="zh_CN" ? 'selected' : ''}}>简体中文</option>
                          <option value="zh_TW" {{ \Session::get('locale')=="zh_TW" ? 'selected' : ''}}>繁體中文</option>
                          <option value="id" {{ \Session::get('locale')=="id" ? 'selected' : ''}}>Indonesian</option>
                  </select>
            </span>
         </li>
        @include('layouts.topnav-account')
      </ul>
    </header>
    <div class="app-body">
      <div class="sidebar">
        <nav class="sidebar-nav">
          <ul class="nav">
            <li class="nav-item">
              <a class="nav-link" href="{{route('home')}}">
                <i class="nav-icon fa fa-dashboard"></i> @lang('modules.dashboard')
              </a>
            </li>
              {!! session('menu') ? session('menu') : Auth::user()->getMenu() !!}
          </ul>
        </nav>
        <button class="sidebar-minimizer brand-minimizer" type="button"></button>
      </div>
      <main class="main">
        @include('components.breadcrumbs')

        <div class="container-fluid">
          <div class="animated fadeIn">
            @yield('content')
          </div>
        </div>
      </main>

    </div>
    <footer class="app-footer">
      <div>

      </div>
      <div class="ml-auto">
      </div>
    </footer>
    {{--
    @include('chat')
    --}}

    @stack('modals')

    <!-- Bootstrap and necessary plugins-->
    <script src="{{asset('vendors/jquery/dist/jquery.min.js')}}"></script>
    <script src="{{asset('vendors/popper.js/dist/umd/popper.min.js')}}"></script>
    <script src="{{asset('vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('vendors/pace-progress/pace.min.js')}}"></script>
    <script src="{{asset('vendors/perfect-scrollbar/dist/perfect-scrollbar.min.js')}}"></script>
    <script src="{{asset('vendors/@coreui/coreui/dist/js/coreui.min.js')}}"></script>
    <script src="{{asset('vendors/bootstrap-notify/bootstrap-notify.min.js')}}"></script>
    <script src="{{asset('vendors/sweetalert2/sweetalert2.min.js')}}"></script>
    @stack('js')
    <script src="{{asset('js/app.js')}}"></script>
    <script>
       window.translations = {!! Cache::get('translations')?:" " !!};
    </script>
    <script>
      $(document).ready(function(){
            $('#lang').change(function(){
              var lang = $(this).val();
              var url = "{{url('/language')}}/"+lang ;
              $(location).attr('href',url);
            })

      });
      function trans(key, replace = {})
        {
          if( window.translations != "" &&  window.translations!= null)
          {
            let translation = key.split('.').reduce((t, i) => t[i] || null, window.translations);

            for (var placeholder in replace) {
                translation = translation.replace(`:${placeholder}`, replace[placeholder]);
            }

            return translation;
          }
        }

        function renderColumnDate(data) {
          if (!data) {
            return '';
          }
        
          return data.slice(8, 10) + '-' +
                data.slice(5, 7) + '-' +
                data.slice(0, 4) + ' ' +
                data.slice(12)
        }

    </script>
    
    @include('sweet::alert')
    @stack('scripts')

  </body>
</html>
