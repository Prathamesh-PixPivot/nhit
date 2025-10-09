<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Organization Setup - NHIT</title>
    
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
        
        .step-line {
            width: 60px;
            height: 2px;
            background: rgba(255, 255, 255, 0.3);
        }
        
        .logo-preview {
            width: 120px;
            height: 120px;
            border: 3px dashed #dee2e6;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            overflow: hidden;
            background: #f8f9fa;
        }
        
        .logo-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        
        .logo-preview .placeholder {
            color: #adb5bd;
            font-size: 48px;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-card">
            <div class="setup-header">
                <div class="step-indicator">
                    <div class="step active">1</div>
                    <div class="step-line"></div>
                    <div class="step">2</div>
                    <div class="step-line"></div>
                    <div class="step">3</div>
                </div>
                <h2 class="text-center mb-2">Organization Setup</h2>
                <p class="text-center mb-0 opacity-75">Step 1 of 3: Create Your Organization</p>
            </div>
            
            <div class="p-4 p-md-5">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('onboarding.store-organization') }}" enctype="multipart/form-data" id="orgSetupForm">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Organization Logo (Optional)</label>
                        <div class="logo-preview mb-3" id="logoPreview">
                            <div class="placeholder">
                                <i class="bi bi-building"></i>
                            </div>
                        </div>
                        <input type="file" 
                               class="form-control @error('organization_logo') is-invalid @enderror" 
                               id="organizationLogo" 
                               name="organization_logo" 
                               accept="image/*">
                        @error('organization_logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="organizationName" class="form-label fw-semibold">
                            Organization Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control form-control-lg @error('organization_name') is-invalid @enderror" 
                               id="organizationName" 
                               name="organization_name" 
                               value="{{ old('organization_name') }}" 
                               required 
                               placeholder="e.g., NHIT Technologies">
                        @error('organization_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="organizationCode" class="form-label fw-semibold">
                            Organization Code <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control form-control-lg @error('organization_code') is-invalid @enderror" 
                               id="organizationCode" 
                               name="organization_code" 
                               value="{{ old('organization_code') }}" 
                               required 
                               placeholder="e.g., NHIT"
                               maxlength="10"
                               style="text-transform: uppercase;">
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            A unique code for your organization (2-10 characters)
                        </div>
                        @error('organization_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="organizationDescription" class="form-label fw-semibold">
                            Description (Optional)
                        </label>
                        <textarea class="form-control @error('organization_description') is-invalid @enderror" 
                                  id="organizationDescription" 
                                  name="organization_description" 
                                  rows="3" 
                                  placeholder="Brief description of your organization">{{ old('organization_description') }}</textarea>
                        @error('organization_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-arrow-right-circle me-2"></i>Continue to Admin Setup
                        </button>
                        <a href="{{ route('onboarding.welcome') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back
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
        // Auto-generate code from name
        document.getElementById('organizationName').addEventListener('input', function() {
            const codeInput = document.getElementById('organizationCode');
            if (!codeInput.value) {
                const code = this.value
                    .replace(/[^a-zA-Z0-9\s]/g, '')
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase())
                    .join('')
                    .substring(0, 10);
                codeInput.value = code;
            }
        });
        
        // Force uppercase for code
        document.getElementById('organizationCode').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
        
        // Logo preview
        document.getElementById('organizationLogo').addEventListener('change', function() {
            const file = this.files[0];
            const preview = document.getElementById('logoPreview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Logo Preview">`;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '<div class="placeholder"><i class="bi bi-building"></i></div>';
            }
        });
    </script>
</body>
</html>
