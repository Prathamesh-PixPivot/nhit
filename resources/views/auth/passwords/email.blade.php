@extends('layouts.app')

@section('content')
    <!-- Forgot Password Container -->
    <div class="forgot-password-container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-xl-5 col-lg-6 col-md-8 col-sm-10">
                <!-- Welcome Header -->
                <div class="text-center mb-4">
                    <div class="forgot-logo mb-3">
                        <i class="bi bi-key text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h2 class="fw-bold text-success mb-2">Forgot Your Password?</h2>
                    <p class="text-muted">No worries! Enter your email to receive a reset link</p>
                </div>

                <!-- Forgot Password Card -->
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-white border-0 py-4">
                        <h4 class="card-title mb-0 text-center fw-bold">
                            <i class="bi bi-envelope-exclamation text-success me-2"></i>Password Reset Request
                        </h4>
                    </div>

                    <div class="card-body p-4">
                        @if (session('status'))
                            <div class="alert alert-success border-0 shadow-sm" role="alert">
                                <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
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
                                        autocomplete="email" autofocus placeholder="Enter your registered email">
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                <small class="text-muted mt-1 d-block">
                                    <i class="bi bi-info-circle text-info me-1"></i>
                                    We'll send you a link to reset your password
                                </small>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-send me-2"></i>Send Reset Link
                                </button>
                            </div>

                            <!-- Back to Login -->
                            <div class="text-center">
                                <p class="mb-0">Remember your password?
                                    <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">
                                        <i class="bi bi-box-arrow-in-right text-success me-1"></i>Back to Login
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="bi bi-shield-check text-success me-1"></i>
                        Secure Password Reset
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Forgot Password Container */
    .forgot-password-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 100vh;
        position: relative;
    }

    .forgot-password-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image:
            radial-gradient(circle at 30% 40%, rgba(25, 135, 84, 0.08) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(253, 126, 20, 0.08) 0%, transparent 50%);
        pointer-events: none;
    }

    /* Forgot Password Card */
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

    /* Forgot Password Logo */
    .forgot-logo {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    /* Alert Styling */
    .alert {
        border-radius: 0.5rem;
        border: none;
    }

    .alert-success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
    }

    /* Helper Text */
    .text-muted {
        font-size: 0.875rem;
    }

    /* Error Messages */
    .invalid-feedback {
        display: flex;
        align-items: center;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    /* Responsive Design */
    @media (max-width: 576px) {
        .forgot-password-container {
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
    // Form validation enhancement
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const emailInput = document.getElementById('email');

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

            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Sending...';
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
