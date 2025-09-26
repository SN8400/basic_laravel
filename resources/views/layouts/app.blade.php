<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? config('app.name', 'App') }}</title>

  <link rel="stylesheet" href="{{ asset('assets/bootstrap/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}">
  <style> body{background:#f3f4f6;} </style>
</head>
<body>
  @yield('content')

  <script src="{{ asset('assets/jquery/jquery-3.7.1.min.js') }}"></script>
  <script src="{{ asset('assets/bootstrap/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
  @stack('scripts')
</body>
</html>
