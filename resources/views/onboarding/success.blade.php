<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Setup Complete - NHIT</title>
    
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
        
        .success-container {
            max-width: 600px;
            width: 100%;
            padding: 20px;
        }
        
        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            text-align: center;
        }
        
        .success-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 40px;
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 50px;
            animation: scaleIn 0.5s ease-out;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        
        .success-body {
            padding: 40px;
        }
        
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 30px 0;
        }
        
        .quick-link-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .quick-link-card:hover {
            background: white;
            border-color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .quick-link-icon {
            font-size: 32px;
            color: #667eea;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-card">
            <div class="success-header">
                <div class="success-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <h1 class="mb-2">Setup Complete!</h1>
                <p class="mb-0 opacity-75">Your organization is ready to use</p>
            </div>
            
            <div class="success-body">
                <div class="alert alert-success border-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Welcome, <strong>{{ auth()->user()->name }}</strong>! You're now logged in as SuperAdmin.
                </div>
                
                <h5 class="mb-3">What's Next?</h5>
                
                <div class="quick-links">
                    <a href="{{ route('backend.dashboard.index') }}" class="quick-link-card">
                        <div class="quick-link-icon">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                        <strong>Dashboard</strong>
                        <p class="small text-muted mb-0">View overview</p>
                    </a>
                    
                    <a href="{{ route('backend.users.index') }}" class="quick-link-card">
                        <div class="quick-link-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <strong>Add Users</strong>
                        <p class="small text-muted mb-0">Invite team</p>
                    </a>
                    
                    <a href="{{ route('backend.roles.index') }}" class="quick-link-card">
                        <div class="quick-link-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <strong>Roles</strong>
                        <p class="small text-muted mb-0">Set permissions</p>
                    </a>
                    
                    <a href="{{ route('backend.organizations.index') }}" class="quick-link-card">
                        <div class="quick-link-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <strong>Organizations</strong>
                        <p class="small text-muted mb-0">Manage orgs</p>
                    </a>
                </div>
                
                <div class="alert alert-info border-0">
                    <h6 class="mb-2">
                        <i class="bi bi-lightbulb me-2"></i>Pro Tips
                    </h6>
                    <ul class="text-start small mb-0">
                        <li>Create departments and designations for better organization</li>
                        <li>Set up approval workflows for expense notes</li>
                        <li>Add vendors for payment processing</li>
                        <li>Configure email settings for notifications</li>
                    </ul>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('backend.dashboard.index') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-arrow-right-circle me-2"></i>Go to Dashboard
                    </a>
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
    <script>
        // Auto-redirect after 10 seconds
        setTimeout(function() {
            window.location.href = "{{ route('backend.dashboard.index') }}";
        }, 10000);
    </script>
</body>
</html>
