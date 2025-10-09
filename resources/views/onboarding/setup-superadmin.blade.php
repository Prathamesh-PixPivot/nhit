<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SuperAdmin Setup - NHIT</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            padding: 20px;
        }
        
        .setup-container {
            max-width: 700px;
            width: 100%;
        }
        
        .setup-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .setup-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 20px 20px 0 0;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin: 0 10px;
        }
        
        .step.active {
            background: white;
            color: #667eea;
        }
        
        .step.completed {
            background: rgba(255, 255, 255, 0.5);
        }
        
        .step-line {
            width: 60px;
            height: 2px;
            background: rgba(255, 255, 255, 0.3);
        }
        
        .org-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .password-strength {
            height: 5px;
            border-radius: 3px;
            margin-top: 5px;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-card">
            <div class="setup-header">
                <div class="step-indicator">
                    <div class="step completed">
                        <i class="bi bi-check"></i>
                    </div>
                    <div class="step-line"></div>
                    <div class="step active">2</div>
                    <div class="step-line"></div>
                    <div class="step">3</div>
                </div>
                <h2 class="text-center mb-2">SuperAdmin Account</h2>
                <p class="text-center mb-0 opacity-75">Step 2 of 3: Create Your Administrator Account</p>
            </div>
            
            <div class="p-4 p-md-5">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <!-- Organization Summary -->
                <div class="org-summary">
                    <h6 class="mb-2">
                        <i class="bi bi-building me-2"></i>Your Organization
                    </h6>
                    <div class="d-flex align-items-center">
                        @if(isset($orgData['logo']))
                            <img src="{{ asset('storage/' . $orgData['logo']) }}" alt="Logo" class="me-3 rounded" style="width: 50px; height: 50px; object-fit: cover;">
                        @endif
                        <div>
                            <strong>{{ $orgData['name'] }}</strong>
                            <span class="badge bg-secondary ms-2">{{ $orgData['code'] }}</span>
                            @if(isset($orgData['description']))
                                <p class="mb-0 small text-muted mt-1">{{ $orgData['description'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('onboarding.complete') }}" id="superAdminForm">
                    @csrf
                    
                    <h5 class="mb-3">Personal Information</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label fw-semibold">
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required 
                                   placeholder="John Doe">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label fw-semibold">
                                Username <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('username') is-invalid @enderror" 
                                   id="username" 
                                   name="username" 
                                   value="{{ old('username') }}" 
                                   required 
                                   placeholder="johndoe">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">
                            Email Address <span class="text-danger">*</span>
                        </label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               placeholder="admin@example.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="designation" class="form-label fw-semibold">
                                Designation <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('designation') is-invalid @enderror" 
                                   id="designation" 
                                   name="designation" 
                                   value="{{ old('designation', 'SuperAdmin') }}" 
                                   required 
                                   placeholder="e.g., CEO, Director">
                            @error('designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="department" class="form-label fw-semibold">
                                Department <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('department') is-invalid @enderror" 
                                   id="department" 
                                   name="department" 
                                   value="{{ old('department', 'Administration') }}" 
                                   required 
                                   placeholder="e.g., Administration">
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="mb-3">Security</h5>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">
                            Password <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   placeholder="Enter strong password"
                                   minlength="8">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="passwordStrength"></div>
                        <div class="form-text">
                            <i class="bi bi-shield-check me-1"></i>
                            Minimum 8 characters, include uppercase, lowercase, numbers
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="passwordConfirmation" class="form-label fw-semibold">
                            Confirm Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" 
                               class="form-control @error('password_confirmation') is-invalid @enderror" 
                               id="passwordConfirmation" 
                               name="password_confirmation" 
                               required 
                               placeholder="Re-enter password"
                               minlength="8">
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> This account will have full administrative access to your organization.
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg" id="completeSetup">
                            <i class="bi bi-check-circle me-2"></i>Complete Setup
                        </button>
                        <a href="{{ route('onboarding.setup-organization') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Organization
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <small class="text-white opacity-75">
                © {{ date('Y') }} NHIT. All rights reserved.
            </small>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
        
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            const colors = ['#dc3545', '#fd7e14', '#ffc107', '#20c997', '#28a745'];
            const widths = ['20%', '40%', '60%', '80%', '100%'];
            
            if (password.length > 0) {
                strengthBar.style.width = widths[strength - 1] || '20%';
                strengthBar.style.backgroundColor = colors[strength - 1] || '#dc3545';
            } else {
                strengthBar.style.width = '0%';
            }
        });
        
        // Form submission with loading state
        document.getElementById('superAdminForm').addEventListener('submit', function() {
            const btn = document.getElementById('completeSetup');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Setting up your organization...';
        });
    </script>
</body>
</html>
