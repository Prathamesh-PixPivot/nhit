/**
 * Organization Switcher JavaScript
 * Handles switching between organization entities for SuperAdmin users
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeOrganizationSwitcher();
});

function initializeOrganizationSwitcher() {
    const switcherItems = document.querySelectorAll('.organization-switch-item');
    
    switcherItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            const orgId = this.getAttribute('data-org-id');
            const orgName = this.querySelector('.fw-medium').textContent;
            
            if (orgId) {
                switchOrganization(orgId, orgName);
            }
        });
    });
}

let isSwitching = false;

function switchOrganization(organizationId, organizationName) {
    // Prevent multiple simultaneous switches
    if (isSwitching) {
        console.log('Switch already in progress, ignoring...');
        return;
    }
    
    isSwitching = true;
    
    // Show loading state using global loader
    if (window.globalLoader) {
        window.globalLoader.show(`Switching to ${organizationName}...`);
    } else {
        // Fallback to custom loader
        showSwitchingLoader(organizationName);
    }
    
    const startTime = Date.now();
    
    // Make AJAX request to switch organization
    fetch('/backend/organizations/switch', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            organization_id: organizationId
        })
    })
    .then(response => {
        console.log('Response received:', response.status);
        
        // Check if response is OK (200-299)
        if (!response.ok) {
            // If we get a 500 error, the switch might have still worked
            // (database context issue after successful switch)
            if (response.status === 500) {
                console.log('Got 500 error - switch might have succeeded, will reload to verify');
                return { success: true, _reload_needed: true };
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.json();
    })
    .then(data => {
        const elapsed = Date.now() - startTime;
        console.log(`Switch completed in ${elapsed}ms`, data);
        
        // Handle case where we got a 500 but switch likely succeeded
        if (data._reload_needed) {
            console.log('Reloading to verify switch status...');
            if (window.globalLoader) {
                window.globalLoader.show('Verifying switch...');
            }
            setTimeout(() => {
                isSwitching = false;
                window.location.reload();
            }, 500);
            return;
        }
        
        if (data.success) {
            console.log('Switch successful! Data:', data);
            
            // Show success message briefly
            const message = data.is_first_time 
                ? `Switched to ${organizationName} successfully! (First time setup completed)`
                : `Switched to ${organizationName} successfully!`;
            
            console.log('About to show success message and reload...');
            
            // Show success toast
            showSuccessMessage(message);
            
            // Keep global loader visible during page reload
            if (window.globalLoader) {
                window.globalLoader.show('Reloading page...');
            }
            
            // Reload the page after a very short delay to allow success message
            const delay = 500; // Reduced delay
            console.log(`Reloading page in ${delay}ms...`);
            setTimeout(() => {
                console.log('Reloading page now...');
                isSwitching = false; // Reset flag before reload
                window.location.reload();
            }, delay);
        } else {
            console.error('Switch failed:', data.message);
            showErrorMessage(data.message || 'Failed to switch organization');
            
            // Hide loaders on failure
            if (window.globalLoader) {
                window.globalLoader.hide();
            } else {
                hideSwitchingLoader();
            }
            isSwitching = false; // Reset flag on failure
        }
    })
    .catch(error => {
        console.error('Error switching organization:', error);
        
        // For any error, assume switch might have worked and reload to verify
        // This handles cases where the switch succeeds but response fails
        console.log('Error occurred - reloading to verify switch status...');
        if (window.globalLoader) {
            window.globalLoader.show('Verifying switch...');
        }
        setTimeout(() => {
            isSwitching = false; // Reset flag before reload
            window.location.reload();
        }, 1000);
    });
}

function showSwitchingLoader(organizationName) {
    // Create or show loading overlay
    let loader = document.getElementById('org-switch-loader');
    
    if (!loader) {
        loader = document.createElement('div');
        loader.id = 'org-switch-loader';
        loader.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
        loader.style.cssText = 'background: rgba(0,0,0,0.7); z-index: 9999;';
        
        loader.innerHTML = `
            <div class="bg-white p-4 rounded shadow text-center" style="min-width: 400px; max-width: 500px;">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 class="mb-3">Switching Organization</h5>
                <p class="text-muted mb-3">Switching to <strong class="text-primary">${organizationName}</strong></p>
                
                <div class="progress mb-3" style="height: 25px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" 
                         id="switch-progress-bar"
                         style="width: 0%"
                         aria-valuenow="0" 
                         aria-valuemin="0" 
                         aria-valuemax="100">0%</div>
                </div>
                
                <div id="switch-status" class="text-start small">
                    <div class="mb-2">
                        <i class="bi bi-hourglass-split text-warning me-2"></i>
                        <span id="status-text">Initializing switch...</span>
                    </div>
                    <div id="process-list" class="mt-3 text-muted" style="max-height: 150px; overflow-y: auto;">
                        <!-- Process steps will be added here -->
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(loader);
    } else {
        loader.querySelector('strong').textContent = organizationName;
        loader.classList.remove('d-none');
        resetProgressBar();
    }
    
    // Start progress simulation
    simulateProgress();
}

function resetProgressBar() {
    const progressBar = document.getElementById('switch-progress-bar');
    const processList = document.getElementById('process-list');
    if (progressBar) {
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';
    }
    if (processList) {
        processList.innerHTML = '';
    }
}

function updateProgress(percentage, statusText, processStep) {
    const progressBar = document.getElementById('switch-progress-bar');
    const statusTextEl = document.getElementById('status-text');
    const processList = document.getElementById('process-list');
    
    if (progressBar) {
        progressBar.style.width = percentage + '%';
        progressBar.textContent = Math.round(percentage) + '%';
        progressBar.setAttribute('aria-valuenow', percentage);
    }
    
    if (statusTextEl) {
        statusTextEl.textContent = statusText;
    }
    
    if (processList && processStep) {
        const stepEl = document.createElement('div');
        stepEl.className = 'mb-1';
        stepEl.innerHTML = `<i class="bi bi-check-circle-fill text-success me-2"></i>${processStep}`;
        processList.appendChild(stepEl);
        processList.scrollTop = processList.scrollHeight;
    }
}

function simulateProgress() {
    let progress = 0;
    const steps = [
        { percent: 15, text: 'Validating permissions...', step: 'Permission check completed' },
        { percent: 30, text: 'Checking database connection...', step: 'Database connection verified' },
        { percent: 45, text: 'Migrating user data...', step: 'User data synchronized' },
        { percent: 60, text: 'Syncing roles and permissions...', step: 'Roles and permissions synced' },
        { percent: 75, text: 'Switching database context...', step: 'Database context switched' },
        { percent: 90, text: 'Finalizing switch...', step: 'Switch finalized' }
    ];
    
    let currentStep = 0;
    
    const interval = setInterval(() => {
        if (currentStep < steps.length) {
            const step = steps[currentStep];
            updateProgress(step.percent, step.text, step.step);
            currentStep++;
        } else {
            clearInterval(interval);
        }
    }, 300);
}

function hideSwitchingLoader() {
    const loader = document.getElementById('org-switch-loader');
    if (loader) {
        loader.classList.add('d-none');
    }
}

function showSuccessMessage(message) {
    showToast(message, 'success');
}

function showErrorMessage(message) {
    showToast(message, 'error');
}

function showToast(message, type = 'info') {
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '10000';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toastClass = type === 'success' ? 'text-bg-success' : 
                     type === 'error' ? 'text-bg-danger' : 'text-bg-info';
    
    const toastHtml = `
        <div id="${toastId}" class="toast ${toastClass}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Initialize and show toast
    const toastElement = document.getElementById(toastId);
    
    if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: type === 'error' ? 5000 : 3000
        });
        toast.show();
    } else {
        // Fallback: just show the toast element
        toastElement.classList.add('show');
        setTimeout(() => {
            toastElement.remove();
        }, type === 'error' ? 5000 : 3000);
    }
    
    // Remove toast element after it's hidden
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

// Export functions for global access
window.organizationSwitcher = {
    switch: switchOrganization,
    showSuccess: showSuccessMessage,
    showError: showErrorMessage
};
