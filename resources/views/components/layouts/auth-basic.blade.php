@props(['title' => 'Auth'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <meta name="description" content="{{ config('app.name') }} - {{ config('app.description') }}">
    <meta name="keywords" content="admin dashboard, admin template, administration, analytics, bootstrap, bootstrap 5, bootstrap admin template, charts, crm, laravel, laravel admin panel, laravel template, performance, php, responsive, saas, sass">
    <meta name="author" content="ThemeSelection">
    <meta name="robots" content="noindex, nofollow" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('materialize/assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;amp;display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('materialize/assets/vendor/fonts/remixicon/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('materialize/assets/vendor/fonts/iconify-icons.css') }}">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('materialize/assets/vendor/css/materialize.css') }}" class="template-customizer-css">
    <link rel="stylesheet" href="{{ asset('materialize/assets/vendor/css/theme-default.css') }}" class="template-customizer-css">
    <link rel="stylesheet" href="{{ asset('materialize/assets/vendor/libs/node-waves/node-waves.css') }}">
    <link rel="stylesheet" href="{{ asset('materialize/assets/vendor/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('materialize/assets/css/demo.css') }}">

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('materialize/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">

    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('materialize/assets/vendor/libs/@form-validation/form-validation.css') }}">

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('materialize/assets/vendor/css/pages/page-auth.css') }}">
    <link rel="stylesheet" href="{{ asset('materialize/assets/vendor/css/pages/front-page.css') }}">

    <!-- Custom Styles -->
    @stack('styles')

    <!-- Helpers -->
    <script src="{{ asset('materialize/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('materialize/assets/vendor/js/template-customizer.js') }}"></script>
    <script src="{{ asset('materialize/assets/js/config.js') }}"></script>
    <script src="{{ asset('materialize/assets/js/front-main.js') }}"></script>

    <!-- Livewire -->
    @livewireStyles
  </head>
  <body>
    <!-- Content -->
    {{ $slot }}

    <!-- Core JS -->
    <script src="{{ asset('materialize/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('materialize/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('materialize/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('materialize/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('materialize/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('materialize/assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('materialize/assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('materialize/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('materialize/assets/vendor/js/menu.js') }}"></script>

    <!-- Vendors JS -->
    <script src="{{ asset('materialize/assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('materialize/assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('materialize/assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
    <script src="{{ asset('materialize/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('materialize/assets/js/main.js') }}"></script>
    <script src="{{ asset('materialize/assets/js/front-page.js') }}"></script>

    <!-- Custom Scripts -->
    @stack('scripts')

    <!-- Livewire -->
    @livewireScripts
  </body>
</html>
