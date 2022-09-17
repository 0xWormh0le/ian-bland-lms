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

    <title>{{ config('app.name', 'LMS') }}</title>
    <!-- Icons-->
    <link href="{{asset('vendors/flag-icon-css/css/flag-icon.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/simple-line-icons/css/simple-line-icons.css')}}" rel="stylesheet">
    <!-- Main styles for this application-->
    <link href="{{asset('css/theme.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/build.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/pace/pace.min.css')}}" rel="stylesheet">
      @stack('css')
  </head>
  <body class="app flex-row align-items-center">

    @yield('content')

    <!-- Bootstrap and necessary plugins-->
    <script src="{{asset('vendors/jquery/dist/jquery.min.js')}}"></script>
    <script src="{{asset('vendors/popper.js/dist/umd/popper.min.js')}}"></script>
    <script src="{{asset('vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('vendors/pace-progress/pace.min.js')}}"></script>
    <script src="{{asset('vendors/perfect-scrollbar/dist/perfect-scrollbar.min.js')}}"></script>
    <script src="{{asset('vendors/@coreui/coreui/dist/js/coreui.min.js')}}"></script>
      @stack('scripts')
  </body>
</html>
