<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Welcome to NHIT - Setup</title>
    
    <!-- Bootstrap 5 -->
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
        }
        
        .welcome-container {
            max-width: 600px;
            width: 100%;
            padding: 20px;
        }
        
        .welcome-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        
        .welcome-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .welcome-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }
        
        .welcome-body {
            padding: 40px;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
        }
        
        .feature-list li {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
        }
        
        .feature-list li:last-child {
            border-bottom: none;
        }
        
        .feature-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
            font-size: 18px;
        }
        
        .btn-start {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .btn-start:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="welcome-card">
            <div class="welcome-header">
                <div class="welcome-icon">
                    <i class="bi bi-rocket-takeoff"></i>
                </div>
                <h1 class="mb-2">Welcome to NHIT</h1>
                <p class="mb-0 opacity-75">Let's set up your organization in just a few steps</p>
            </div>
            
            <div class="welcome-body">
                <h4 class="mb-4">What You'll Set Up:</h4>
                
                <ul class="feature-list mb-4">
                    <li>
                        <div class="feature-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <div>
                            <strong>Your Organization</strong>
                            <p class="mb-0 text-muted small">Create your organization profile and branding</p>
                        </div>
                    </li>
                    <li>
                        <div class="feature-icon">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div>
                            <strong>SuperAdmin Account</strong>
                            <p class="mb-0 text-muted small">Set up your administrator account with full access</p>
                        </div>
                    </li>
                    <li>
                        <div class="feature-icon">
                            <i class="bi bi-database"></i>
                        </div>
                        <div>
                            <strong>Database Setup</strong>
                            <p class="mb-0 text-muted small">Automatic database configuration and initialization</p>
                        </div>
                    </li>
                    <li>
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <strong>Security & Permissions</strong>
                            <p class="mb-0 text-muted small">Complete role-based access control system</p>
                        </div>
                    </li>
                </ul>
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Estimated Time:</strong> 2-3 minutes
                </div>
                
                <div class="text-center">
                    <a href="{{ route('onboarding.setup-organization') }}" class="btn btn-primary btn-start btn-lg w-100">
                        <i class="bi bi-arrow-right-circle me-2"></i>Start Setup
                    </a>
                </div>
                
                <div class="text-center mt-3">
                    <small class="text-muted">Already have an account? 
                        <a href="{{ route('backend.login') }}" class="text-decoration-none">Login here</a>
                    </small>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <small class="text-white opacity-75">
                © {{ date('Y') }} NHIT. All rights reserved.
            </small>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
