@extends('layouts.app')

@section('content')
    <!-- Login Container -->
    <div class="login-container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-xl-5 col-lg-6 col-md-8 col-sm-10">
                <!-- Welcome Header -->
                <div class="text-center mb-4">
                    <div class="login-logo mb-3">
                        <i class="bi bi-shield-check text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h2 class="fw-bold text-success mb-2">Welcome Back</h2>
                    <p class="text-muted">Sign in to your account to continue</p>
                </div>

                <!-- Login Card -->
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-white border-0 py-4">
                        <h4 class="card-title mb-0 text-center fw-bold">
                            <i class="bi bi-box-arrow-in-right text-success me-2"></i>Login to Your Account
                        </h4>
                    </div>

                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email Field -->
                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="bi bi-envelope text-success me-2"></i>Email Address
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-envelope text-muted"></i>
                                    </span>
                                    <input id="email" type="email"
                                        class="form-control border-start-0 @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email') }}" required
                                        autocomplete="email" autofocus placeholder="Enter your email address">
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Password Field -->
                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="bi bi-key text-success me-2"></i>Password
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-key text-muted"></i>
                                    </span>
                                    <input id="password" type="password"
                                        class="form-control border-start-0 @error('password') is-invalid @enderror"
                                        name="password" required autocomplete="current-password"
                                        placeholder="Enter your password">
                                    <button class="btn btn-outline-secondary border-start-0" type="button"
                                            onclick="togglePasswordVisibility()">
                                        <i class="bi bi-eye" id="passwordToggleIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        <span class="fw-semibold">Remember me</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Login Button -->
                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                                </button>
                            </div>

                            <!-- Forgot Password Link -->
                            @if (Route::has('password.request'))
                                <div class="text-center mb-4">
                                    <a href="{{ route('password.request') }}" class="text-decoration-none">
                                        <i class="bi bi-question-circle text-success me-1"></i>
                                        Forgot your password?
                                    </a>
                                </div>
                            @endif

                            <!-- Demo Credentials (for development) -->
                            @if(config('app.debug'))
                                <div class="mt-4 p-3 bg-light rounded">
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-info-circle text-info me-1"></i>Demo Credentials:
                                    </small>
                                    <div class="row g-2">
                                        <div class="col-sm-6">
                                            <small class="d-block">Admin: admin@demo.com</small>
                                            <small class="d-block">Password: password</small>
                                        </div>
                                        <div class="col-sm-6">
                                            <small class="d-block">User: user@demo.com</small>
                                            <small class="d-block">Password: password</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="bi bi-shield-check text-success me-1"></i>
                        Secure Login Powered by Laravel
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Login Container */
    .login-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 100vh;
        position: relative;
    }

    .login-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image:
            radial-gradient(circle at 20% 50%, rgba(25, 135, 84, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(253, 126, 20, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 40% 80%, rgba(25, 135, 84, 0.05) 0%, transparent 50%);
        pointer-events: none;
    }

    /* Login Card */
    .card {
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
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
        font-size: 0.95rem;
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

    /* Login Logo */
    .login-logo {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    /* Demo Credentials Box */
    .bg-light {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        border: 1px solid #dee2e6;
    }

    /* Error Messages */
    .invalid-feedback {
        display: flex;
        align-items: center;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.5rem;
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
    @media (max-width: 576px) {
        .login-container {
            padding: 1rem;
        }

        .card {
            margin: 0;
        }

        .btn-lg {
            padding: 0.625rem 1.25rem;
            font-size: 1rem;
        }
    }

    /* Focus States */
    .btn:focus {
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
    }

    /* Loading State */
    .btn:disabled {
        opacity: 0.65;
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

        // Add real-time validation feedback
        emailInput.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });

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
