<!-- Feature Guide Modal -->
<div class="modal fade" id="featureGuideModal" tabindex="-1" aria-labelledby="featureGuideModalLabel" aria-hidden="true" style="z-index: 100000;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="featureGuideModalLabel">
                    <i class="bi bi-question-circle me-2"></i>Feature Guide - How to Use New Features
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <button class="nav-link active" id="v-pills-overview-tab" data-bs-toggle="pill" data-bs-target="#v-pills-overview" type="button" role="tab">
                                <i class="bi bi-house me-2"></i>Overview
                            </button>
                            <button class="nav-link" id="v-pills-invoices-tab" data-bs-toggle="pill" data-bs-target="#v-pills-invoices" type="button" role="tab">
                                <i class="bi bi-receipt-cutoff me-2"></i>Multiple Invoices
                            </button>
                            <button class="nav-link" id="v-pills-hold-tab" data-bs-toggle="pill" data-bs-target="#v-pills-hold" type="button" role="tab">
                                <i class="bi bi-pause-circle me-2"></i>Hold Option
                            </button>
                            <button class="nav-link" id="v-pills-drafts-tab" data-bs-toggle="pill" data-bs-target="#v-pills-drafts" type="button" role="tab">
                                <i class="bi bi-file-earmark-text me-2"></i>Draft Payment Notes
                            </button>
                            <button class="nav-link" id="v-pills-banking-tab" data-bs-toggle="pill" data-bs-target="#v-pills-banking" type="button" role="tab">
                                <i class="bi bi-bank me-2"></i>Banking Auto-Population
                            </button>
                            <button class="nav-link" id="v-pills-vendor-tab" data-bs-toggle="pill" data-bs-target="#v-pills-vendor" type="button" role="tab">
                                <i class="bi bi-building me-2"></i>Vendor Management
                            </button>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="tab-content" id="v-pills-tabContent">
                            <!-- Overview Tab -->
                            <div class="tab-pane fade show active" id="v-pills-overview" role="tabpanel">
                                <h4 class="text-primary mb-3">
                                    <i class="bi bi-stars me-2"></i>New Features Overview
                                </h4>
                                <p class="lead">Welcome to the enhanced expense management system! We've added several powerful features to streamline your workflow.</p>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="card border-primary">
                                            <div class="card-body text-center">
                                                <i class="bi bi-receipt-cutoff text-primary fs-1 mb-2"></i>
                                                <h6>Multiple Invoices</h6>
                                                <p class="small text-muted">Attach multiple invoices to a single expense note</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-warning">
                                            <div class="card-body text-center">
                                                <i class="bi bi-pause-circle text-warning fs-1 mb-2"></i>
                                                <h6>Hold Option</h6>
                                                <p class="small text-muted">Put expense notes on hold when needed</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-success">
                                            <div class="card-body text-center">
                                                <i class="bi bi-file-earmark-text text-success fs-1 mb-2"></i>
                                                <h6>Draft Payment Notes</h6>
                                                <p class="small text-muted">Auto-created drafts for approved expenses</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-info">
                                            <div class="card-body text-center">
                                                <i class="bi bi-bank text-info fs-1 mb-2"></i>
                                                <h6>Smart Banking</h6>
                                                <p class="small text-muted">Auto-populate banking details and vendor codes</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info mt-4">
                                    <i class="bi bi-lightbulb me-2"></i>
                                    <strong>Pro Tip:</strong> Click on any feature tab to learn how to use it effectively!
                                </div>
                            </div>

                            <!-- Multiple Invoices Tab -->
                            <div class="tab-pane fade" id="v-pills-invoices" role="tabpanel">
                                <h4 class="text-primary mb-3">
                                    <i class="bi bi-receipt-cutoff me-2"></i>Multiple Invoices for Expense Notes
                                </h4>
                                
                                <div class="mb-4">
                                    <h6 class="text-success"><i class="bi bi-check-circle me-1"></i>How to Use:</h6>
                                    <ol class="list-group list-group-numbered">
                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-bold">Access Multiple Invoices</div>
                                                From any Green Note details page, click the "Multiple Invoices" button
                                            </div>
                                            <span class="badge bg-primary rounded-pill">Step 1</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-bold">Add Invoices</div>
                                                Click "Add Another Invoice" to add more invoice entries
                                            </div>
                                            <span class="badge bg-primary rounded-pill">Step 2</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-bold">Fill Details</div>
                                                Enter invoice number, date, value, and optional descriptions
                                            </div>
                                            <span class="badge bg-primary rounded-pill">Step 3</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-bold">Auto-Calculate</div>
                                                Total value is calculated automatically as you type
                                            </div>
                                            <span class="badge bg-primary rounded-pill">Step 4</span>
                                        </li>
                                    </ol>
                                </div>
                                
                                <div class="alert alert-success">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Benefits:</strong> Track multiple invoices separately while maintaining a single approval workflow.
                                </div>
                            </div>

                            <!-- Hold Option Tab -->
                            <div class="tab-pane fade" id="v-pills-hold" role="tabpanel">
                                <h4 class="text-primary mb-3">
                                    <i class="bi bi-pause-circle me-2"></i>Hold Option for Expense Notes
                                </h4>
                                
                                <div class="mb-4">
                                    <h6 class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>When to Use:</h6>
                                    <ul class="list-group">
                                        <li class="list-group-item">Missing or incomplete documentation</li>
                                        <li class="list-group-item">Need additional approvals or clarifications</li>
                                        <li class="list-group-item">Vendor payment issues or disputes</li>
                                        <li class="list-group-item">Budget or compliance concerns</li>
                                    </ul>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="text-success"><i class="bi bi-check-circle me-1"></i>How to Use:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header bg-warning text-dark">
                                                    <h6 class="mb-0">Putting on Hold</h6>
                                                </div>
                                                <div class="card-body">
                                                    <ol class="small">
                                                        <li>Click "Hold" button on expense note</li>
                                                        <li>Enter reason for hold</li>
                                                        <li>Confirm action</li>
                                                        <li>Note is paused from approval process</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header bg-success text-white">
                                                    <h6 class="mb-0">Removing Hold</h6>
                                                </div>
                                                <div class="card-body">
                                                    <ol class="small">
                                                        <li>Click "Remove Hold" button</li>
                                                        <li>Review hold details</li>
                                                        <li>Confirm removal</li>
                                                        <li>Approval process resumes</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="bi bi-shield-exclamation me-2"></i>
                                    <strong>Important:</strong> Only authorized users can put notes on hold or remove holds.
                                </div>
                            </div>

                            <!-- Draft Payment Notes Tab -->
                            <div class="tab-pane fade" id="v-pills-drafts" role="tabpanel">
                                <h4 class="text-primary mb-3">
                                    <i class="bi bi-file-earmark-text me-2"></i>Draft Payment Notes
                                </h4>
                                
                                <div class="mb-4">
                                    <h6 class="text-info"><i class="bi bi-robot me-1"></i>Auto-Creation Process:</h6>
                                    <div class="timeline">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <span class="text-white fw-bold">1</span>
                                            </div>
                                            <div>
                                                <strong>Green Note Approved</strong>
                                                <br><small class="text-muted">Final approval triggers auto-creation</small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <span class="text-white fw-bold">2</span>
                                            </div>
                                            <div>
                                                <strong>Draft Payment Note Created</strong>
                                                <br><small class="text-muted">System creates draft with expense details</small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <span class="text-white fw-bold">3</span>
                                            </div>
                                            <div>
                                                <strong>Review & Edit</strong>
                                                <br><small class="text-muted">Project accounts team can review and modify</small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <span class="text-white fw-bold">4</span>
                                            </div>
                                            <div>
                                                <strong>Convert to Active</strong>
                                                <br><small class="text-muted">Draft becomes active payment note</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="text-success"><i class="bi bi-tools me-1"></i>Managing Drafts:</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card border-info">
                                                <div class="card-body text-center">
                                                    <i class="bi bi-eye text-info fs-3"></i>
                                                    <h6 class="mt-2">View</h6>
                                                    <p class="small">Review draft details</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card border-warning">
                                                <div class="card-body text-center">
                                                    <i class="bi bi-pencil text-warning fs-3"></i>
                                                    <h6 class="mt-2">Edit</h6>
                                                    <p class="small">Modify draft content</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card border-success">
                                                <div class="card-body text-center">
                                                    <i class="bi bi-check-circle text-success fs-3"></i>
                                                    <h6 class="mt-2">Convert</h6>
                                                    <p class="small">Make active payment note</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="bi bi-people me-2"></i>
                                    <strong>Access Rights:</strong> Project accounts teams have edit rights for drafts related to their departments.
                                </div>
                            </div>

                            <!-- Banking Auto-Population Tab -->
                            <div class="tab-pane fade" id="v-pills-banking" role="tabpanel">
                                <h4 class="text-primary mb-3">
                                    <i class="bi bi-bank me-2"></i>Banking Details Auto-Population
                                </h4>
                                
                                <div class="mb-4">
                                    <h6 class="text-primary"><i class="bi bi-magic me-1"></i>Smart Features:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header bg-primary text-white">
                                                    <h6 class="mb-0">IFSC Auto-Complete</h6>
                                                </div>
                                                <div class="card-body">
                                                    <ul class="small">
                                                        <li>Enter 11-digit IFSC code</li>
                                                        <li>Bank name auto-fills</li>
                                                        <li>Branch name auto-fills</li>
                                                        <li>Validation indicators</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header bg-success text-white">
                                                    <h6 class="mb-0">Vendor Selection</h6>
                                                </div>
                                                <div class="card-body">
                                                    <ul class="small">
                                                        <li>Select vendor from dropdown</li>
                                                        <li>Banking details auto-populate</li>
                                                        <li>Multiple accounts available</li>
                                                        <li>Primary account selected by default</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="text-info"><i class="bi bi-gear me-1"></i>Where It Works:</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="list-group">
                                                <div class="list-group-item active">Travel Forms</div>
                                                <div class="list-group-item">Payment Forms</div>
                                                <div class="list-group-item">Expense Forms</div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="alert alert-light">
                                                <h6>How to Use:</h6>
                                                <ol class="mb-0">
                                                    <li>Start typing in IFSC field or select vendor</li>
                                                    <li>Watch as fields auto-populate</li>
                                                    <li>Verify the populated information</li>
                                                    <li>Make manual adjustments if needed</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-success">
                                    <i class="bi bi-clock me-2"></i>
                                    <strong>Time Saver:</strong> Reduces data entry time by up to 70% and minimizes errors!
                                </div>
                            </div>

                            <!-- Vendor Management Tab -->
                            <div class="tab-pane fade" id="v-pills-vendor" role="tabpanel">
                                <h4 class="text-primary mb-3">
                                    <i class="bi bi-building me-2"></i>Enhanced Vendor Management
                                </h4>
                                
                                <div class="mb-4">
                                    <h6 class="text-primary"><i class="bi bi-code-square me-1"></i>Auto-Generated Vendor Codes:</h6>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <h6>Format: <code>{TypePrefix}{NamePrefix}{Year}{Sequence}</code></h6>
                                                    <p class="small text-muted mb-0">
                                                        Example: <strong>INT_ABC_2024_001</strong>
                                                        <br>• INT = Internal vendor
                                                        <br>• ABC = First 3 letters of vendor name
                                                        <br>• 2024 = Current year
                                                        <br>• 001 = Sequential number
                                                    </p>
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    <button class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-arrow-clockwise me-1"></i>Generate Code
                                                    </button>
                                                    <br><small class="text-muted">Click to auto-generate</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="text-success"><i class="bi bi-credit-card me-1"></i>Multiple Bank Accounts:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card border-success">
                                                <div class="card-header bg-success text-white">
                                                    <h6 class="mb-0">Primary Account</h6>
                                                </div>
                                                <div class="card-body">
                                                    <ul class="small mb-0">
                                                        <li>Default for all payments</li>
                                                        <li>Required for every vendor</li>
                                                        <li>Clearly marked with badge</li>
                                                        <li>Can be changed anytime</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-info">
                                                <div class="card-header bg-info text-white">
                                                    <h6 class="mb-0">Additional Accounts</h6>
                                                </div>
                                                <div class="card-body">
                                                    <ul class="small mb-0">
                                                        <li>Add unlimited accounts</li>
                                                        <li>Different currencies/purposes</li>
                                                        <li>Easy to manage and switch</li>
                                                        <li>Individual activation status</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="bi bi-shield-check me-2"></i>
                                    <strong>Security:</strong> All vendor codes are unique and validated to prevent duplicates.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="markGuideAsRead()">
                    <i class="bi bi-check-circle me-1"></i>Got It!
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Feature Guide Button (Floating) -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 100001;">
    <button type="button" class="btn btn-primary rounded-circle shadow-lg" data-bs-toggle="modal" data-bs-target="#featureGuideModal" title="Feature Guide">
        <i class="bi bi-question-circle fs-5"></i>
    </button>
</div>

<style>
/* Ensure feature guide modal backdrop is above global loader */
#featureGuideModal ~ .modal-backdrop,
.modal-backdrop.feature-guide-backdrop {
    z-index: 99998 !important;
}

/* Ensure feature guide modal is above everything */
#featureGuideModal {
    z-index: 100000 !important;
}
</style>

<script>
function markGuideAsRead() {
    localStorage.setItem('featureGuideRead', 'true');
    const modal = bootstrap.Modal.getInstance(document.getElementById('featureGuideModal'));
    modal.hide();
    
    // Show success notification
    const notification = document.createElement('div');
    notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 100002; min-width: 300px;';
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle me-2"></i>
            Great! You can always access this guide using the help button.
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

// Show guide on first visit
document.addEventListener('DOMContentLoaded', function() {
    const hasReadGuide = localStorage.getItem('featureGuideRead');
    if (!hasReadGuide) {
        setTimeout(() => {
            const modal = new bootstrap.Modal(document.getElementById('featureGuideModal'));
            modal.show();
        }, 2000);
    }

    // Mark feature guide backdrop with specific class
    const featureGuideModal = document.getElementById('featureGuideModal');
    if (featureGuideModal) {
        featureGuideModal.addEventListener('show.bs.modal', function() {
            setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => {
                    backdrop.classList.add('feature-guide-backdrop');
                });
            }, 50);
        });
    }
});
</script>
