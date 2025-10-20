<!DOCTYPE html>
<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="ltr"
  data-skin="default"
  data-bs-theme="light"
  data-assets-path="{{ asset('materialize/assets/') }}/"
  data-template="vertical-menu-template">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ $title ?? config('app.name') }}</title>
    <meta name="description" content="{{ config('app.name') }} - {{ config('app.description') }}" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('materialize/assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('materialize/assets/vendor/fonts/iconify-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('materialize/assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('materialize/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('materialize/assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('materialize/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('materialize/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('materialize/assets/vendor/libs/swiper/swiper.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('materialize/assets/vendor/css/pages/cards-statistics.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('materialize/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('materialize/assets/vendor/js/template-customizer.js') }}"></script>
    <script src="{{ asset('materialize/assets/js/config.js') }}"></script>

    @stack('styles')
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        @include('components.partials.menu')

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          @include('components.partials.navbar')

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-fluid flex-grow-1 container-p-y">
              {{ $slot }}
            </div>
            <!-- /Content -->

            <!-- Footer -->
            @include('components.partials.footer')

            <div class="content-backdrop fade"></div>
          </div>
          <!-- /Content wrapper -->
        </div>
        <!-- /Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    <!-- /Layout wrapper -->

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
    <script src="{{ asset('materialize/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('materialize/assets/vendor/libs/swiper/swiper.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('materialize/assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('materialize/assets/js/dashboards-analytics.js') }}"></script>

    @stack('scripts')
  </body>
</html>
