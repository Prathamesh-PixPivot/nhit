@extends('backend.layouts.guest')

@section('content')
<!-- Modern Backend Login -->
<div class="backend-login-wrapper">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-xl-8 col-lg-10 col-md-12">

                <!-- System Selection Header -->
                <!-- <div class="text-center mb-4">
                    <div class="system-selector">
                        <h3 class="system-title">
                            <i class="bi bi-diagram-3 text-success me-2"></i>
                            Select Your System
                        </h3>
                        <p class="system-subtitle">Choose the appropriate system to access</p>
                    </div>
                </div>

                System Selection Buttons
                <div class="system-buttons mb-5">
                    <div class="row g-3 justify-content-center">
                        @php
                            $currentUrl = url()->current();
                            $systems = [
                                ['url' => 'https://payment.nhit.co.in/neppl/public/backend/login', 'name' => 'NEPPL', 'color' => 'success'],
                                ['url' => 'https://payment.nhit.co.in/nhit/public/backend/login', 'name' => 'NWPPL', 'color' => 'info'],
                                ['url' => 'https://payment.nhit.co.in/nsppl/public/backend/login', 'name' => 'NSPPL', 'color' => 'warning'],
                                ['url' => 'https://payment.nhit.co.in/nhit-trust/public/backend/login', 'name' => 'NHIT-TRUST', 'color' => 'primary']
                            ];
                        @endphp

                        @foreach($systems as $system)
                            <div class="col-md-3 col-sm-6">
                                <a href="{{ $system['url'] }}"
                                   class="btn btn-system btn-{{ $system['color'] }} {{ $currentUrl === $system['url'] ? 'active' : '' }}">
                                    <div class="system-icon">
                                        <i class="bi bi-building"></i>
                                    </div>
                                    <div class="system-name">{{ $system['name'] }}</div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div> -->

                <!-- Session Messages -->
                @if (Session::has('message'))
                    <div class="alert alert-info border-0 shadow-sm mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        {!! Session::get('message') !!}
                    </div>
                @endif

                @if (Session::has('success'))
                    <div class="alert alert-success border-0 shadow-sm mb-4">
                        <i class="bi bi-check-circle me-2"></i>
                        {!! Session::get('success') !!}
                    </div>
                @endif

                @if (Session::has('warnings'))
                    <div class="alert alert-warning border-0 shadow-sm mb-4">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {!! Session::get('warnings') !!}
                    </div>
                @endif

                @if (Session::has('error'))
                    <div class="alert alert-danger border-0 shadow-sm mb-4">
                        <i class="bi bi-x-circle me-2"></i>
                        {!! Session::get('error') !!}
                    </div>
                @elseif (count($errors) > 0)
                    <div class="alert alert-danger border-0 shadow-sm mb-4">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Login Card -->
                <div class="login-card">
                    <div class="card border-0 shadow-lg">
                        <div class="card-header bg-white border-0 py-4">
                            <div class="text-center">
                                <h4 class="card-title mb-2">
                                    <i class="bi bi-box-arrow-in-right text-success me-2"></i>
                                    Login to Your Account
                                </h4>
                                <p class="text-muted mb-0">Enter your credentials to access the system</p>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('backend.login.attempt') }}" class="row g-3">
                                @csrf

                                <!-- Username Field -->
                                <div class="col-12">
                                    <label for="email" class="form-label fw-semibold">
                                        <i class="bi bi-person text-success me-2"></i>Username
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-person text-muted"></i>
                                        </span>
                                        <input id="email" type="text"
                                            class="form-control @error('email') is-invalid @enderror"
                                            name="email" value="{{ old('email') }}" required autofocus
                                            placeholder="Enter your username">
                                        @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Password Field -->
                                <div class="col-12">
                                    <label for="password" class="form-label fw-semibold">
                                        <i class="bi bi-key text-success me-2"></i>Password
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-key text-muted"></i>
                                        </span>
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            name="password" required placeholder="Enter your password">
                                        <button class="btn btn-outline-secondary" type="button"
                                                onclick="togglePasswordVisibility()">
                                            <i class="bi bi-eye" id="passwordToggleIcon"></i>
                                        </button>
                                        @error('password')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Remember Me -->
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="remember">
                                            <span class="text-success">Remember me</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Login Button -->
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success btn-lg w-100">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                                    </button>
                                </div>

                                <!-- Forgot Password -->
                                <div class="col-12 text-center">
                                    @if (Route::has('backend.password.request'))
                                        <a href="{{ route('backend.password.request') }}" class="text-decoration-none">
                                            <i class="bi bi-question-circle text-success me-1"></i>
                                            Forgot Your Password?
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="bi bi-shield-check text-success me-1"></i>
                        Secure Enterprise Login Portal
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
/* Backend Login Container */
.backend-login-wrapper {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    min-height: 100vh;
    position: relative;
}

.backend-login-wrapper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image:
        radial-gradient(circle at 25% 25%, rgba(25, 135, 84, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 75% 75%, rgba(253, 126, 20, 0.05) 0%, transparent 50%);
    pointer-events: none;
}

/* System Selector */
.system-selector {
    padding: 2rem;
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 20px rgba(25, 135, 84, 0.15);
    margin-bottom: 2rem;
}

.system-title {
    color: #198754;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.system-subtitle {
    color: #6c757d;
    margin-bottom: 0;
}

/* System Selection Buttons */
.system-buttons {
    margin-bottom: 3rem;
}

.btn-system {
    width: 100%;
    padding: 1.5rem;
    border-radius: 0.75rem;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-system:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(25, 135, 84, 0.2);
}

.btn-system.active {
    ring: 3px solid #198754;
    ring-offset: 2px;
}

.btn-success {
    background: linear-gradient(135deg, #198754 0%, #20c997 100%);
    color: white;
}

.btn-info {
    background: linear-gradient(135deg, #0dcaf0 0%, #20c997 100%);
    color: white;
}

.btn-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: #212529;
}

.btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #198754 100%);
    color: white;
}

.system-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.system-name {
    font-size: 1.1rem;
    font-weight: 600;
}

/* Login Card */
.login-card .card {
    border-radius: 1rem;
    box-shadow: 0 10px 30px rgba(25, 135, 84, 0.15);
    border: none;
}

.card-header {
    border-radius: 1rem 1rem 0 0 !important;
    background: linear-gradient(135deg, #198754 0%, #20c997 100%) !important;
    color: white;
}

.card-header .card-title {
    color: white !important;
}

/* Form Controls */
.form-control {
    border-radius: 0.5rem;
    border: 2px solid #e9ecef;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.2s ease;
}

.form-control:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

.input-group-text {
    border-radius: 0.5rem 0 0 0.5rem;
    border: 2px solid #e9ecef;
    border-right: none;
    background-color: #f8f9fa;
}

.input-group .form-control {
    border-left: none;
    border-radius: 0 0.5rem 0.5rem 0;
}

.input-group .form-control:focus {
    border-color: #198754;
    box-shadow: none;
}

.input-group .form-control:focus + .btn-outline-secondary {
    border-color: #198754;
}

/* Form Labels */
.form-label {
    color: #495057;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

/* Buttons */
.btn {
    border-radius: 0.5rem;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    transition: all 0.2s ease;
}

.btn-success {
    background: linear-gradient(135deg, #198754 0%, #20c997 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(25, 135, 84, 0.3);
}

.btn-success:hover {
    background: linear-gradient(135deg, #157347 0%, #198754 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(25, 135, 84, 0.4);
}

.btn-outline-secondary {
    border-color: #e9ecef;
    color: #6c757d;
    background-color: transparent;
}

.btn-outline-secondary:hover {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #495057;
}

/* Links */
a {
    color: #198754;
    text-decoration: none;
    transition: all 0.2s ease;
}

a:hover {
    color: #157347;
    text-decoration: underline;
}

/* Alerts */
.alert {
    border-radius: 0.75rem;
    border: none;
    font-weight: 500;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    color: #0c5460;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
}

.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    color: #856404;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
}

/* Form Check */
.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

.form-check-label {
    color: #495057;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
    .system-buttons .row {
        --bs-gutter-x: 1rem;
    }

    .btn-system {
        padding: 1rem;
    }

    .system-icon {
        font-size: 1.5rem;
        margin-bottom: 0.25rem;
    }

    .system-name {
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .backend-login-wrapper {
        padding: 1rem;
    }

    .system-selector {
        padding: 1.5rem;
    }

    .system-title {
        font-size: 1.5rem;
    }

    .btn-system {
        padding: 0.75rem;
        font-size: 0.9rem;
    }
}

/* Loading States */
.btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

/* Focus States */
.btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

/* Animation for entrance */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.login-card .card {
    animation: fadeInUp 0.6s ease-out;
}

.system-selector {
    animation: fadeInUp 0.4s ease-out;
}
</style>
@endpush

@push('script')
<script>
    // Toggle password visibility
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('passwordToggleIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('bi-eye');
            toggleIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('bi-eye-slash');
            toggleIcon.classList.add('bi-eye');
        }
    }

    // Form validation enhancement
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        // Auto-focus management
        if (!emailInput.value) {
            emailInput.focus();
        }

        // Enhanced form submission
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Signing In...';
            submitBtn.disabled = true;

            // Re-enable after 3 seconds as fallback
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });
    });
</script>
@endpush