<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Dashboard - NiceAdmin Bootstrap Template</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <!-- Favicons -->




    <!-- Favicons -->
    <link href="{{ asset('theme/assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('theme/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('theme/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="{{ asset('theme/assets/css/style.css') }}" rel="stylesheet">

    <!-- =======================================================
  * Template Name: NiceAdmin
  * Updated: Mar 13 2024 with Bootstrap v5.3.3
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->

    {{-- @vite(['resources/theme/css/style.css']) --}}
    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
</head>

<body>
    <div id="app">
        <!-- ======= Header ======= -->
        <!-- End Header -->
        <!-- ======= Sidebar ======= -->
        <!-- End Sidebar-->

        <!-- End #main -->
        <main id="" class="">
            <div class="container">
                @yield('content')
            </div>
        </main><!-- End #main -->
    </div>



    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="{{ asset('theme/assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/quill/quill.min.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/php-email-form/validate.js') }}"></script>

    <!-- Template Main JS File -->
    <script src="{{ asset('theme/assets/js/main.js') }}"></script>
</body>

</html>
