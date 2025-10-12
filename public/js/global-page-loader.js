/**
 * Global Page Loader
 * Shows loader during page refresh, navigation, and form submissions
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeGlobalLoader();
});

function initializeGlobalLoader() {
    createLoaderHTML();
    setupPageLoadingEvents();
    
    // Hide loader when page is fully loaded
    window.addEventListener('load', function() {
        hideGlobalLoader();
    });
}

function createLoaderHTML() {
    // Check if loader already exists
    if (document.getElementById('global-page-loader')) {
        return;
    }
    
    const loader = document.createElement('div');
    loader.id = 'global-page-loader';
    loader.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center hidden';
    loader.style.cssText = `
        background: rgba(255, 255, 255, 0.95);
        z-index: -1;
        transition: opacity 0.3s ease;
        display: none;
        opacity: 0;
        pointer-events: none;
    `;
    
    loader.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h5 class="mb-2 text-primary">Loading...</h5>
            <p class="text-muted mb-0">Please wait while the page loads</p>
            
            <!-- Progress bar for visual feedback -->
            <div class="progress mt-3" style="width: 200px; height: 4px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" 
                     id="global-progress-bar"
                     style="width: 0%"></div>
            </div>
        </div>
    `;
    
    document.body.appendChild(loader);
    
    // Start progress animation
    animateProgress();
}

function setupPageLoadingEvents() {
    // Show loader on page unload (refresh, navigation)
    window.addEventListener('beforeunload', function() {
        showGlobalLoader('Refreshing page...');
    });
    
    // Show loader on navigation (back/forward buttons)
    window.addEventListener('pagehide', function() {
        showGlobalLoader('Navigating...');
    });
    
    // Show loader for form submissions
    document.addEventListener('submit', function(e) {
        const form = e.target;
        
        // Skip if form has specific class to prevent loader
        if (form.classList.contains('no-loader')) {
            return;
        }
        
        showGlobalLoader('Submitting form...');
        
        // Hide loader after a timeout as fallback
        setTimeout(() => {
            hideGlobalLoader();
        }, 10000); // 10 seconds max
    });
    
    // Show loader for links that navigate away
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        
        if (link && !link.classList.contains('no-loader')) {
            const href = link.getAttribute('href');
            
            // Check if it's a navigation link (not hash, mailto, tel, etc.)
            if (href && 
                !href.startsWith('#') && 
                !href.startsWith('mailto:') && 
                !href.startsWith('tel:') && 
                !href.startsWith('javascript:') &&
                !link.hasAttribute('download') &&
                link.target !== '_blank') {
                
                showGlobalLoader('Loading page...');
            }
        }
    });
    
    // Show loader for AJAX requests (optional)
    setupAjaxLoader();
}

function setupAjaxLoader() {
    // Track active AJAX requests
    let activeRequests = 0;
    
    // Override fetch to show loader
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        const url = args[0];
        const options = args[1] || {};
        
        // Skip if request has no-loader header or is organization switch
        if ((options.headers && options.headers['X-No-Loader']) ||
            (typeof url === 'string' && url.includes('/organizations/switch'))) {
            return originalFetch.apply(this, args);
        }
        
        activeRequests++;
        if (activeRequests === 1) {
            showGlobalLoader('Processing request...');
        }
        
        return originalFetch.apply(this, args).finally(() => {
            activeRequests--;
            if (activeRequests === 0) {
                hideGlobalLoader();
            }
        });
    };
    
    // Override XMLHttpRequest for jQuery and other libraries
    const originalXHROpen = XMLHttpRequest.prototype.open;
    const originalXHRSend = XMLHttpRequest.prototype.send;
    
    XMLHttpRequest.prototype.open = function(method, url, async, user, password) {
        this._url = url;
        return originalXHROpen.apply(this, arguments);
    };
    
    XMLHttpRequest.prototype.send = function(data) {
        // Skip if URL contains no-loader parameter
        if (this._url && this._url.includes('no-loader=1')) {
            return originalXHRSend.apply(this, arguments);
        }
        
        activeRequests++;
        if (activeRequests === 1) {
            showGlobalLoader('Processing request...');
        }
        
        const originalOnReadyStateChange = this.onreadystatechange;
        this.onreadystatechange = function() {
            if (this.readyState === 4) { // Request completed
                activeRequests--;
                if (activeRequests === 0) {
                    hideGlobalLoader();
                }
            }
            if (originalOnReadyStateChange) {
                originalOnReadyStateChange.apply(this, arguments);
            }
        };
        
        return originalXHRSend.apply(this, arguments);
    };
}

function showGlobalLoader(message = 'Loading...') {
    const loader = document.getElementById('global-page-loader');
    if (loader) {
        // Remove hidden class when showing
        loader.classList.remove('hidden');
        loader.style.display = 'flex';
        loader.style.zIndex = '99999';
        loader.style.pointerEvents = 'auto';
        loader.style.backdropFilter = 'blur(5px)';
        loader.style.webkitBackdropFilter = 'blur(5px)';
        
        // Update message
        const messageEl = loader.querySelector('h5');
        if (messageEl) {
            messageEl.textContent = message;
        }
        
        loader.style.opacity = '1';
        
        // Restart progress animation
        animateProgress();
    }
}

function hideGlobalLoader() {
    const loader = document.getElementById('global-page-loader');
    if (loader) {
        loader.classList.add('hidden');
        loader.style.opacity = '0';
        loader.style.pointerEvents = 'none';

        // Remove backdrop-filter from inline styles
        loader.style.backdropFilter = 'none';
        loader.style.webkitBackdropFilter = 'none';

        setTimeout(() => {
            loader.style.display = 'none';
            loader.style.zIndex = '-1';
        }, 300);
    }
}

function animateProgress() {
    const progressBar = document.getElementById('global-progress-bar');
    if (!progressBar) return;
    
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 90) {
            progress = 90; // Don't complete until page actually loads
        }
        
        progressBar.style.width = progress + '%';
        
        if (progress >= 90) {
            clearInterval(interval);
        }
    }, 200);
}

// Utility functions for manual control
window.globalLoader = {
    show: showGlobalLoader,
    hide: hideGlobalLoader,
    
    // Show loader for specific duration
    showFor: function(duration = 3000, message = 'Loading...') {
        showGlobalLoader(message);
        setTimeout(() => {
            hideGlobalLoader();
        }, duration);
    }
};

// Handle page visibility changes
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        // Page became visible again, hide loader
        setTimeout(() => {
            hideGlobalLoader();
        }, 500);
    }
});
