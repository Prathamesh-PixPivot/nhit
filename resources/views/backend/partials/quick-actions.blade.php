<!-- Quick Actions Sidebar -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="quickActionsOffcanvas" aria-labelledby="quickActionsOffcanvasLabel">
    <div class="offcanvas-header bg-primary text-white">
        <h5 class="offcanvas-title" id="quickActionsOffcanvasLabel">
            <i class="bi bi-lightning me-2"></i>Quick Actions
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <!-- New Features Section -->
        <div class="p-3 border-bottom">
            <h6 class="text-primary mb-3">
                <i class="bi bi-stars me-2"></i>New Features
            </h6>
            <div class="d-grid gap-2">
                <a href="{{ route('backend.payment-note.drafts') }}" class="btn btn-outline-success btn-sm text-start">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    <div>
                        <strong>Draft Payment Notes</strong>
                        <br><small class="text-muted">Manage auto-created drafts</small>
                    </div>
                </a>
                <a href="{{ route('backend.vendors.create') }}" class="btn btn-outline-info btn-sm text-start">
                    <i class="bi bi-building me-2"></i>
                    <div>
                        <strong>Create Vendor</strong>
                        <br><small class="text-muted">Auto-generate codes & multiple accounts</small>
                    </div>
                </a>
                <button type="button" class="btn btn-outline-warning btn-sm text-start" data-bs-toggle="modal" data-bs-target="#featureGuideModal">
                    <i class="bi bi-question-circle me-2"></i>
                    <div>
                        <strong>Feature Guide</strong>
                        <br><small class="text-muted">Learn how to use new features</small>
                    </div>
                </button>
            </div>
        </div>

        <!-- Expense Management Section -->
        <div class="p-3 border-bottom">
            <h6 class="text-success mb-3">
                <i class="bi bi-receipt me-2"></i>Expense Management
            </h6>
            <div class="d-grid gap-2">
                <a href="{{ route('backend.note.create') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-plus-circle me-2"></i>Create Green Note
                </a>
                <a href="{{ route('backend.note.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list me-2"></i>All Green Notes
                </a>
                <div class="btn-group w-100" role="group">
                    <button type="button" class="btn btn-outline-info btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-funnel me-2"></i>Filter Notes
                    </button>
                    <ul class="dropdown-menu w-100">
                        <li><a class="dropdown-item" href="{{ route('backend.note.index', ['status' => 'P']) }}">
                            <i class="bi bi-clock text-warning me-2"></i>Pending
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('backend.note.index', ['status' => 'A']) }}">
                            <i class="bi bi-check-circle text-success me-2"></i>Approved
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('backend.note.index', ['status' => 'H']) }}">
                            <i class="bi bi-pause-circle text-warning me-2"></i>On Hold
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('backend.note.index', ['status' => 'R']) }}">
                            <i class="bi bi-x-circle text-danger me-2"></i>Rejected
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Payment Management Section -->
        <div class="p-3 border-bottom">
            <h6 class="text-info mb-3">
                <i class="bi bi-credit-card me-2"></i>Payment Management
            </h6>
            <div class="d-grid gap-2">
                @if(auth()->user()->hasRole('Super Admin'))
                    <a href="{{ route('backend.payment-note.create-superadmin') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-plus-circle me-2"></i>Create Payment Note
                    </a>
                @endif
                <a href="{{ route('backend.payment-note.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list me-2"></i>All Payment Notes
                </a>
                <a href="{{ route('backend.payment-note.drafts') }}" class="btn btn-outline-warning btn-sm">
                    <i class="bi bi-file-earmark-text me-2"></i>Draft Payment Notes
                    @php
                        $draftCount = \App\Models\PaymentNote::where('is_draft', true)->count();
                    @endphp
                    @if($draftCount > 0)
                        <span class="badge bg-warning text-dark ms-1">{{ $draftCount }}</span>
                    @endif
                </a>
            </div>
        </div>

        <!-- Vendor Management Section -->
        <div class="p-3 border-bottom">
            <h6 class="text-warning mb-3">
                <i class="bi bi-building me-2"></i>Vendor Management
            </h6>
            <div class="d-grid gap-2">
                <a href="{{ route('backend.vendors.create') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-plus-circle me-2"></i>Create Vendor
                </a>
                <a href="{{ route('backend.vendors.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list me-2"></i>All Vendors
                </a>
                <button type="button" class="btn btn-outline-info btn-sm" onclick="showVendorCodeGenerator()">
                    <i class="bi bi-code-square me-2"></i>Generate Vendor Code
                </button>
            </div>
        </div>

        <!-- Reports Section -->
        <div class="p-3 border-bottom">
            <h6 class="text-danger mb-3">
                <i class="bi bi-graph-up me-2"></i>Reports & Analytics
            </h6>
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="generateExpenseReport()">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i>Expense Report
                </button>
                <button type="button" class="btn btn-outline-success btn-sm" onclick="generatePaymentReport()">
                    <i class="bi bi-cash-stack me-2"></i>Payment Report
                </button>
                <button type="button" class="btn btn-outline-info btn-sm" onclick="generateVendorReport()">
                    <i class="bi bi-building me-2"></i>Vendor Report
                </button>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="p-3">
            <h6 class="text-secondary mb-3">
                <i class="bi bi-speedometer2 me-2"></i>Quick Stats
            </h6>
            <div class="row g-2">
                <div class="col-6">
                    <div class="card bg-primary text-white text-center">
                        <div class="card-body p-2">
                            <h6 class="mb-0">{{ \App\Models\GreenNote::where('status', 'P')->count() }}</h6>
                            <small>Pending</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card bg-warning text-dark text-center">
                        <div class="card-body p-2">
                            <h6 class="mb-0">{{ \App\Models\GreenNote::where('status', 'H')->count() }}</h6>
                            <small>On Hold</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card bg-success text-white text-center">
                        <div class="card-body p-2">
                            <h6 class="mb-0">{{ \App\Models\PaymentNote::where('is_draft', true)->count() }}</h6>
                            <small>Drafts</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card bg-info text-white text-center">
                        <div class="card-body p-2">
                            <h6 class="mb-0">{{ \App\Models\Vendor::where('status', 'Active')->count() }}</h6>
                            <small>Vendors</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Toggle Button -->
<div class="position-fixed top-50 end-0 translate-middle-y" style="z-index: 1040;">
    <button class="btn btn-primary rounded-start shadow-lg" type="button" data-bs-toggle="offcanvas" data-bs-target="#quickActionsOffcanvas" title="Quick Actions">
        <i class="bi bi-lightning"></i>
    </button>
</div>

<!-- Vendor Code Generator Modal -->
<div class="modal fade" id="vendorCodeGeneratorModal" tabindex="-1" aria-labelledby="vendorCodeGeneratorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vendorCodeGeneratorModalLabel">
                    <i class="bi bi-code-square me-2"></i>Generate Vendor Code
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="vendorCodeForm">
                    <div class="mb-3">
                        <label for="vendorName" class="form-label">Vendor Name</label>
                        <input type="text" class="form-control" id="vendorName" required placeholder="Enter vendor name">
                    </div>
                    <div class="mb-3">
                        <label for="accountType" class="form-label">Account Type</label>
                        <select class="form-select" id="accountType" required>
                            <option value="Internal">Internal</option>
                            <option value="External">External</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="generatedCode" class="form-label">Generated Code</label>
                        <input type="text" class="form-control" id="generatedCode" readonly placeholder="Code will appear here">
                    </div>
                    <div class="d-grid">
                        <button type="button" class="btn btn-primary" onclick="generateCode()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Generate Code
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="copyCode()" disabled id="copyCodeBtn">
                    <i class="bi bi-clipboard me-1"></i>Copy Code
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Vendor Code Generator
function showVendorCodeGenerator() {
    const modal = new bootstrap.Modal(document.getElementById('vendorCodeGeneratorModal'));
    modal.show();
}

async function generateCode() {
    const vendorName = document.getElementById('vendorName').value;
    const accountType = document.getElementById('accountType').value;
    
    if (!vendorName.trim()) {
        alert('Please enter vendor name');
        return;
    }
    
    try {
        const response = await fetch('/api/backend/vendor/generate-code', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                vendor_name: vendorName,
                account_type: accountType
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('generatedCode').value = data.vendor_code;
            document.getElementById('copyCodeBtn').disabled = false;
        } else {
            alert('Error: ' + (data.message || 'Failed to generate code'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error generating code');
    }
}

function copyCode() {
    const codeField = document.getElementById('generatedCode');
    codeField.select();
    document.execCommand('copy');
    
    const btn = document.getElementById('copyCodeBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check me-1"></i>Copied!';
    btn.classList.remove('btn-success');
    btn.classList.add('btn-outline-success');
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.classList.remove('btn-outline-success');
        btn.classList.add('btn-success');
    }, 2000);
}

// Report Generation Functions
function generateExpenseReport() {
    // Placeholder for expense report generation
    showNotification('Expense report generation will be implemented', 'info');
}

function generatePaymentReport() {
    // Placeholder for payment report generation
    showNotification('Payment report generation will be implemented', 'info');
}

function generateVendorReport() {
    // Placeholder for vendor report generation
    showNotification('Vendor report generation will be implemented', 'info');
}

// Notification helper
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${message}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Auto-refresh stats every 30 seconds
setInterval(() => {
    // This would typically make an AJAX call to refresh stats
    // For now, we'll just log that it would refresh
    console.log('Stats would refresh here');
}, 30000);
</script>
