<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>NHIT</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <!-- Modern Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicons -->
    <link href="{{ asset('theme/assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('theme/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Vendor CSS Files -->
    {{-- <link href="{{ asset('theme/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="{{ asset('theme/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <!-- Datatable css -->
    {{-- <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet"> --}}
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('theme/assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">
    <!-- toastr css -->
    <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"> --}}
    <link href="{{ asset('theme/assets/css/fSelect.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
    <!-- Global Page Loader Styles -->
    <link href="{{ asset('css/global-loader.css') }}" rel="stylesheet">

    <!-- ✅ Latest jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-..." crossorigin="anonymous"></script>

    <!-- ✅ Latest Bootstrap 5 -->



    <!-- Template Main CSS File -->
    <link href="{{ asset('theme/assets/css/style.css') }}" rel="stylesheet">
    
    <!-- Modern Design System -->
    <link href="{{ asset('css/modern-design-system.css') }}" rel="stylesheet">
    
    @vite(['resources/sass/app.scss', 'resources/css/tw.css', 'resources/js/app.js'])
    {!! Toastr::message() !!}
    <!-- =======================================================
  * Template Name: NiceAdmin
  * Updated: Mar 13 2024 with Bootstrap v5.3.3
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
    {{-- @vite(['resources/backend/js/letterTpl.js']) --}}
    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}

    @stack('style')
    <style>
        /*AVI*/


        td {
            padding: 12px 0 !important;
            vertical-align: top;
        }

        .select2-results__option {
            padding: 0;
            font-size: 16px;
        }

        span#select2-template_type-container {
            font-size: 12px;
        }

        input:read-only,
        textarea:read-only {
            color: #878787;
            border: none !important;
            background: #e7e7e7 !important;
        }

        .form-control {
            font-size: 12px;
        }

        ul#select2-from_account-results li {
            font-size: 15px;
        }

        input.col-md-2.offset-md-0.btn.btn-primary.btn-sm.request-form-submit {
            background: #0081ff !important;
        }

        th {
            font-size: 12px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            font-size: 12px;
        }

        #employee-table input,
        td {
            width: 130px;
            font-size: 15px;
        }

        .form-check-input:checked {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }

        #employee-table td div {
            color: #878787;
            font-size: 15px;
            line-height: 28px;
        }



        .btn-group-xs>.btn,
        .btn-xs {
            padding: .35rem 0.5rem;
            font-size: .675rem;
            line-height: .5;
            border-radius: .2rem;
        }


        /* Custom Alert Background */
        .custom-alert {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            /* Semi-transparent background */
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: all 0.3s ease-in-out;
        }

        /* Hide Custom Alert */
        .custom-alert.hidden {
            visibility: hidden;
            opacity: 0;
            pointer-events: none;
        }

        /* Alert Content */
        .custom-alert-content {
            background: white;
            padding: 20px 30px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            animation: scaleIn 0.3s ease-in-out;
        }

        /* OK Button */
        #custom-alert-ok {
            margin-top: 20px;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        #custom-alert-ok:hover {
            background: #0056b3;
        }

        /* Animation for Content */
        @keyframes scaleIn {
            from {
                transform: scale(0.8);
            }

            to {
                transform: scale(1);
            }
        }

        /* Custom Confirm Background */
        .custom-confirm {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            /* Semi-transparent background */
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: all 0.3s ease-in-out;
        }

        /* Hide Custom Confirm */
        .custom-confirm.hidden {
            visibility: hidden;
            opacity: 0;
            pointer-events: none;
        }

        /* Confirm Content */
        .custom-confirm-content {
            background: white;
            padding: 20px 30px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            animation: scaleIn 0.3s ease-in-out;
        }

        /* Confirm Buttons */
        .custom-confirm-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        #custom-confirm-ok,
        #custom-confirm-cancel {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        #custom-confirm-cancel {
            background: #dc3545;
        }

        #custom-confirm-ok:hover {
            background: #0056b3;
        }

        #custom-confirm-cancel:hover {
            background: #b02a37;
        }

        /* Animation for Content */
        @keyframes scaleIn {
            from {
                transform: scale(0.8);
            }

            to {
                transform: scale(1);
            }
        }

        /* Icon Styles */
        .custom-alert-icon,
        .custom-confirm-icon {
            font-size: 40px;
            margin-bottom: 10px;
            display: block;
        }

        /* Success Icon */
        .icon-success {
            color: #28a745;
        }

        /* Error Icon */
        .icon-error {
            color: #dc3545;
        }

        /* Info Icon */
        .icon-info {
            color: #007bff;
        }

        /* Warning Icon */
        .icon-warning {
            color: #ffc107;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-image: none;
            background-color: black;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            /* padding: 10px; */
        }

        .carousel-control-prev-icon::after,
        .carousel-control-next-icon::after {
            content: '';
            display: block;
            width: 60%;
            height: 60%;
            mask-size: cover;
            -webkit-mask-size: cover;
            mask-repeat: no-repeat;
            -webkit-mask-repeat: no-repeat;
            background-color: white;
        }

        .carousel-control-prev-icon::after {
            mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='white' viewBox='0 0 16 16'%3E%3Cpath d='M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z'/%3E%3C/svg%3E");
        }

        .carousel-control-next-icon::after {
            mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='white' viewBox='0 0 16 16'%3E%3Cpath d='M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
        }
    </style>
    <style>
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.15em;
        }

        .button-with-spinner .spinner-border {
            display: none;
            margin-left: 8px;
        }

        .button-with-spinner.loading .spinner-border {
            display: inline-block;
        }

        .button-with-spinner.loading span {
            display: none;
        }
    </style>
</head>

<body class="nhit-modern">
    <a class="skip-link" href="#main">Skip to content</a>
    <div id="app" data-theme="light">
        <!-- ======= Header ======= -->
        @include('backend.layouts.include.header')
        <!-- End Header -->
        <!-- ======= Sidebar ======= -->
        @include('backend.layouts.include.side')
        <!-- End Sidebar-->

        <!-- End #main -->
        <main id="main" class="main" role="main">
            @include('backend.layouts.include.message')
            @yield('content')
        </main><!-- End #main -->
        
        <!-- Include Feature Guide and Quick Actions -->
        @include('backend.partials.feature-guide')
        @include('backend.partials.quick-actions')
        
        <!-- ======= Sidebar ======= -->
        @include('backend.layouts.include.footer')
        <!-- End Sidebar-->
    </div>

    {{-- Customize jquery confirm alert box --}}
    <div id="custom-alert" class="custom-alert hidden">
        <div class="custom-alert-content">
            <span id="custom-alert-icon" class="custom-alert-icon"></span>
            <p id="custom-alert-message"></p>
            <button id="custom-alert-ok">OK</button>
        </div>
    </div>
    {{-- Customize jquery alert box --}}
    <div id="custom-confirm" class="custom-confirm hidden">
        <div class="custom-confirm-content">
            <span id="custom-confirm-icon" class="custom-confirm-icon"></span>
            <p id="custom-confirm-message"></p>
            <div class="custom-confirm-buttons">
                <button id="custom-confirm-ok">OK</button>
                <button id="custom-confirm-cancel">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Vendor JS Files -->
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="{{ asset('theme/assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    {{-- <script src="{{ asset('theme/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('theme/assets/vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/quill/quill.min.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/tinymce/tinymce.min.js') }}"></script>
    {{-- <script src="{{ asset('theme/assets/vendor/php-email-form/validate.js') }}"></script> --}}


    <!-- Datatable js -->
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script> --}}
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('theme/assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <!-- toastr js -->
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>
    <!-- Template Main JS File -->
    <script src="{{ asset('theme/assets/js/jquery.form-repeater.js') }}"></script>
    <script src="{{ asset('theme/assets/js/main.js') }}"></script>
    <script src="{{ asset('theme/assets/js/letterTpl.js') }}"></script>
    <script src="{{ asset('theme/assets/js/fSelect.js') }}"></script>

    @if (Session::has('message'))
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true
        }
        toastr.success("{{ session('message') }}");
    </script>
    @endif

    @if (Session::has('error'))
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true
        }
        toastr.error("{{ session('error') }}");
    </script>
    @endif

    @if (Session::has('info'))
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true
        }
        toastr.info("{{ session('info') }}");
    </script>
    @endif

    @if (Session::has('warning'))
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true
        }
        toastr.warning("{{ session('warning') }}");
    </script>
    @endif

    <script>

        /*  function customAlert(message) {
             // Set the message
             $('#custom-alert-message').text(message);

             // Show the alert
             $('#custom-alert').removeClass('hidden');

             // Close the alert when OK button is clicked
             $('#custom-alert-ok').off('click').on('click', function() {
                 $('#custom-alert').addClass('hidden');
             });
         } */



        // Override the default alert
        function customAlert(message, type = 'info') {
            // Set the message
            $('#custom-alert-message').text(message);

            // Set the icon
            const iconClasses = {
                success: 'fas fa-check-circle icon-success',
                error: 'fas fa-times-circle icon-error',
                warning: 'fas fa-exclamation-circle icon-warning',
                info: 'fas fa-info-circle icon-info'
            };

            $('#custom-alert-icon').attr('class', `custom-alert-icon ${iconClasses[type]}`);

            // Show the alert
            $('#custom-alert').removeClass('hidden');

            // Close the alert when OK button is clicked
            $('#custom-alert-ok').off('click').on('click', function() {
                $('#custom-alert').addClass('hidden');
            });
        }

        // Override the default alert
        window.alert = function(message) {
            customAlert(message);
        };

        function customConfirm(message, callback, type = 'info') {
            // Set the message
            $('#custom-confirm-message').text(message);

            // Set the icon
            /* const iconClasses = {
                success: 'icon-success',
                error: 'icon-error',
                warning: 'icon-warning',
                info: 'icon-info'
            }; */
            const iconClasses = {
                success: 'fas fa-check-circle icon-success',
                error: 'fas fa-times-circle icon-error',
                warning: 'fas fa-exclamation-circle icon-warning',
                info: 'fas fa-info-circle icon-info'
            };
            $('#custom-confirm-icon').attr('class', `custom-confirm-icon ${iconClasses[type]}`);

            // Show the confirm box
            $('#custom-confirm').removeClass('hidden');

            // Handle OK button
            $('#custom-confirm-ok').off('click').on('click', function() {
                $('#custom-confirm').addClass('hidden');
                callback(true); // Call the callback with true
            });

            // Handle Cancel button
            $('#custom-confirm-cancel').off('click').on('click', function() {
                $('#custom-confirm').addClass('hidden');
                callback(false); // Call the callback with false
            });
        }

        // Example Usage
        /*  customConfirm('Are you sure you want to proceed?', function(result) {
             if (result) {
                 console.log('Confirmed!');
             } else {
                 console.log('Cancelled!');
             }
         }, 'warning'); */
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fix for sidebar dropdown functionality
            const sidebarNavLinks = document.querySelectorAll('.sidebar .nav-link[data-bs-toggle="collapse"]');

            sidebarNavLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    const targetId = this.getAttribute('data-bs-target');
                    const targetElement = document.querySelector(targetId);

                    if (targetElement) {
                        // Toggle the collapse state
                        const isShown = targetElement.classList.contains('show');

                        // Close all other dropdowns
                        document.querySelectorAll('.sidebar .nav-content.show').forEach(function(content) {
                            if (content !== targetElement) {
                                content.classList.remove('show');
                                const parentNav = content.closest('.nav-item');
                                if (parentNav) {
                                    parentNav.classList.remove('show');
                                    const parentLink = parentNav.querySelector('.nav-link[data-bs-toggle="collapse"]');
                                    if (parentLink) {
                                        parentLink.setAttribute('aria-expanded', 'false');
                                        parentLink.classList.remove('active');
                                    }
                                }
                            }
                        });

                        // Toggle current dropdown
                        if (isShown) {
                            targetElement.classList.remove('show');
                            this.closest('.nav-item').classList.remove('show');
                            this.setAttribute('aria-expanded', 'false');
                            this.classList.remove('active');
                        } else {
                            targetElement.classList.add('show');
                            this.closest('.nav-item').classList.add('show');
                            this.setAttribute('aria-expanded', 'true');
                            this.classList.add('active');
                        }
                    }
                });
            });

            // Handle active state based on current route
            const currentPath = window.location.pathname;
            document.querySelectorAll('.sidebar .nav-content .nav-link').forEach(function(link) {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                    const parentCollapse = link.closest('.nav-content');
                    if (parentCollapse) {
                        parentCollapse.classList.add('show');
                        parentCollapse.closest('.nav-item').classList.add('show');
                        const parentLink = parentCollapse.closest('.nav-item').querySelector('.nav-link[data-bs-toggle="collapse"]');
                        if (parentLink) {
                            parentLink.classList.add('active');
                            parentLink.setAttribute('aria-expanded', 'true');
                        }
                    }
                }
            });
        });
    </script>
    
    <!-- Global Page Loader JavaScript -->
    <script src="{{ asset('js/global-page-loader.js') }}"></script>
    
    <!-- Organization Switcher JavaScript -->
    <script src="{{ asset('js/organization-switcher.js') }}"></script>
    
    @stack('script')

</body>

</html>
