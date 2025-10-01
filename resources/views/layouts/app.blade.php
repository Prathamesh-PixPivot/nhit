<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Laravel 11 Spatie User Roles and Permissions - AllPHPTricks.com')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="nhit-modern">
    <a class="skip-link" href="#main-content">Skip to content</a>
    <div id="app" data-theme="{{ session('theme', '') }}">

        <!-- Enhanced Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light shadow-sm" role="navigation" aria-label="Primary">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('home') }}">
                    <i class="bi bi-house-door text-primary"></i>
                    <span>AllPHPTricks.com</span>
                </a>

                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto" role="menubar">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">Home</a>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto d-flex align-items-center gap-3" role="menubar">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link btn btn-outline-primary btn-sm px-3" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link btn btn-primary btn-sm px-3" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            @canany(['create-role', 'edit-role', 'delete-role'])
                                <li class="nav-item">
                                    <a class="nav-link text-decoration-none" href="{{ route('roles.index') }}">
                                        <i class="bi bi-shield-check me-1"></i>Manage Roles
                                    </a>
                                </li>
                            @endcanany

                            @canany(['create-user', 'edit-user', 'delete-user'])
                                <li class="nav-item">
                                    <a class="nav-link text-decoration-none" href="{{ route('users.index') }}">
                                        <i class="bi bi-people me-1"></i>Manage Users
                                    </a>
                                </li>
                            @endcanany

                            @canany(['create-product', 'edit-product', 'delete-product'])
                                <li class="nav-item">
                                    <a class="nav-link text-decoration-none" href="{{ route('products.index') }}">
                                        <i class="bi bi-box-seam me-1"></i>Manage Products
                                    </a>
                                </li>
                            @endcanany

                            @can(['view-product'])
                                <li class="nav-item">
                                    <a class="nav-link text-decoration-none" href="{{ route('products.index') }}">
                                        <i class="bi bi-eye me-1"></i>View Products
                                    </a>
                                </li>
                            @endcan

                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="bi bi-person-circle"></i>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end border-0 shadow" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right"></i>{{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main id="main-content" class="py-4" role="main">
            <div class="container-fluid">
                <!-- Flash Messages -->
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="status" aria-live="polite">
                        <i class="bi bi-check-circle me-2"></i>{{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" aria-live="assertive" tabindex="-1" id="error-summary">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <h6 class="alert-heading mb-2">Please fix the following:</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Enhanced Page Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 fw-bold text-primary mb-1">
                                    @yield('title', 'Laravel 11 Spatie User Roles and Permissions')
                                </h1>
                                <p class="text-muted mb-0">AllPHPTricks.com</p>
                            </div>
                            @hasSection('actions')
                                <div class="d-flex gap-2">
                                    @yield('actions')
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="row">
                    <div class="col-12">
                        @yield('content')
                    </div>
                </div>

                <!-- Footer Links -->
                <div class="row justify-content-center text-center mt-5 pt-4 border-top">
                    <div class="col-md-8">
                        <p class="mb-2">
                            <strong>Back to Tutorial:</strong>
                            <a href="https://www.allphptricks.com/laravel-11-spatie-user-roles-and-permissions/" class="text-decoration-none">
                                Tutorial Link <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </p>
                        <p class="text-muted">
                            For More Web Development Tutorials Visit:
                            <a href="https://www.allphptricks.com/" class="text-decoration-none fw-semibold">AllPHPTricks.com</a>
                        </p>
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-4 bg-light border-top" role="contentinfo">
            <div class="container-fluid">
                <p class="mb-0 text-center text-muted">© {{ date('Y') }} AllPHPTricks.com</p>
            </div>
        </footer>
    </div>
</body>
</html>