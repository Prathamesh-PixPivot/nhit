<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center shadow-sm">

    <div class="d-flex align-items-center justify-content-between flex-grow-1">
        <div class="d-flex align-items-center">
            <a href="{{ route('backend.dashboard.index') }}" class="logo d-flex align-items-center me-4">
                <img src="{{ asset('theme/assets/img/logo.png') }}" alt="NHIT Logo" style="max-height: 45px;">
                <span class="d-none d-lg-block fw-bold text-primary ms-2 fs-5">NHIT</span>
            </a>
        </div>

        <div class="d-flex align-items-center gap-3">
            <!-- Organization Switcher -->
            @if(auth()->check())
                @php
                    $user = auth()->user();
                    $userAccessibleOrgs = $user->accessibleOrganizations();
                    $userCurrentOrg = $user->currentOrganization();
                @endphp
                @if($userAccessibleOrgs && $userAccessibleOrgs->count() > 0)
                <div class="dropdown" id="orgSwitcherDropdown">
                    <button class="btn btn-outline-primary btn-sm dropdown-toggle d-flex align-items-center" type="button" 
                            id="organizationSwitcher" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <i class="bi bi-building me-1"></i>
                        <span class="d-none d-lg-inline">
                            {{ $userCurrentOrg->name ?? 'Select Organization' }}
                        </span>
                        <span class="d-lg-none">
                            {{ $userCurrentOrg->code ?? 'ORG' }}
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="organizationSwitcher" style="min-width: 250px;">
                        <li class="dropdown-header">
                            <strong>Switch Organization</strong>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        @foreach($userAccessibleOrgs as $org)
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between organization-switch-item" 
                                   href="#" data-org-id="{{ $org->id }}"
                                   @if($userCurrentOrg && $userCurrentOrg->id == $org->id) style="background-color: var(--bs-primary-bg-subtle);" @endif>
                                    <div class="d-flex align-items-center">
                                        @if($org->logo)
                                            <img src="{{ asset('storage/' . $org->logo) }}" alt="{{ $org->name }}" 
                                                 class="me-2 rounded" style="width: 24px; height: 24px; object-fit: cover;">
                                        @else
                                            <div class="bg-primary bg-opacity-10 text-primary rounded d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 24px; height: 24px; font-size: 12px;">
                                                {{ strtoupper(substr($org->code, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-medium">{{ $org->name }}</div>
                                            <small class="text-muted">{{ $org->code }}</small>
                                        </div>
                                    </div>
                                    @if($userCurrentOrg && $userCurrentOrg->id == $org->id)
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('backend.organizations.index') }}">
                                <i class="bi bi-gear me-2"></i>
                                Manage Organizations
                            </a>
                        </li>
                    </ul>
                </div>
                @endif
            @endif

            <!-- Quick Actions -->
            <div class="d-none d-lg-flex gap-2">
                @can(['create-note'])
                    <a href="{{ route('backend.note.create') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-plus-circle me-1"></i>New Note
                    </a>
                @endcan
                @can(['create-reimbursement-note'])
                    <a href="{{ route('backend.reimbursement-note.create') }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-receipt me-1"></i>Reimbursement
                    </a>
                @endcan
            </div>

            <!-- System Info -->
            <div class="d-none d-md-block text-muted small me-3">
                ({{ config('app.short_name') }})
                @if(auth()->check())
                    @php $currentOrgCode = auth()->user()->currentOrganization()->code ?? null; @endphp
                    @if($currentOrgCode)
                        <br><small class="text-primary">{{ $currentOrgCode }}</small>
                    @endif
                @endif
            </div>
            <i class="bi bi-list toggle-sidebar-btn fs-4 text-primary cursor-pointer"></i>
        </div>
    </div><!-- End Logo Section -->

     <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#"
                    data-bs-toggle="dropdown">
                     <div class="d-flex align-items-center">
                         <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                             {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                         </div>
                         <div class="d-none d-md-block">
                             <span class="fw-medium">{{ Auth::user()->name }}</span>
                             <small class="d-block text-muted">{{ Auth::user()->designation->name ?? 'User' }}</small>
                         </div>
                     </div>
                 </a><!-- End Profile Image Icon -->

                 <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                     <li class="dropdown-header">
                         <h6>{{ Auth::user()->name }}</h6>
                         <span>{{ Auth::user()->designation->name ?? '-' }}</span>
                     </li>
                     <li>
                         <hr class="dropdown-divider">
                     </li>

                     <li>
                         <a class="dropdown-item d-flex align-items-center"
                             href="{{ route('backend.users.profile', auth()->user()->id) }}">
                             <i class="bi bi-person me-2"></i>
                             <span>My Profile</span>
                         </a>
                     </li>
                     <li>
                         <hr class="dropdown-divider">
                     </li>

                     <li>
                         <a class="dropdown-item d-flex align-items-center" href="{{ route('backend.logout') }}"
                             onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                             <i class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}
                         </a>

                         <form id="logout-form" action="{{ route('backend.logout') }}" method="POST" class="d-none">
                             @csrf
                         </form>
                     </li>
                 </ul><!-- End Profile Dropdown Items -->
             </li><!-- End Profile Nav -->
         </ul>
     </nav><!-- End Icons Navigation -->
 </header>
 <!-- End Header -->
 
 <script>
 // Ensure organization switcher dropdown works
 document.addEventListener('DOMContentLoaded', function() {
     console.log('Initializing organization switcher...');
     
     const orgSwitcherBtn = document.getElementById('organizationSwitcher');
     const orgDropdown = document.getElementById('orgSwitcherDropdown');
     
     console.log('Button found:', !!orgSwitcherBtn);
     console.log('Dropdown found:', !!orgDropdown);
     console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
     
     if (orgSwitcherBtn) {
         // Remove any existing event listeners
         const newBtn = orgSwitcherBtn.cloneNode(true);
         orgSwitcherBtn.parentNode.replaceChild(newBtn, orgSwitcherBtn);
         
         // Add click event
         newBtn.addEventListener('click', function(e) {
             console.log('Organization switcher clicked');
             e.preventDefault();
             e.stopPropagation();
             
             const dropdownMenu = this.nextElementSibling;
             if (dropdownMenu) {
                 dropdownMenu.classList.toggle('show');
                 this.setAttribute('aria-expanded', dropdownMenu.classList.contains('show'));
             }
         });
         
         // Initialize Bootstrap dropdown if available
         if (typeof bootstrap !== 'undefined') {
             try {
                 new bootstrap.Dropdown(newBtn);
                 console.log('Bootstrap dropdown initialized');
             } catch(e) {
                 console.error('Failed to initialize Bootstrap dropdown:', e);
             }
         }
     }
     
     // Close dropdown when clicking outside
     document.addEventListener('click', function(e) {
         if (orgDropdown && !orgDropdown.contains(e.target)) {
             const dropdownMenu = orgDropdown.querySelector('.dropdown-menu');
             if (dropdownMenu) {
                 dropdownMenu.classList.remove('show');
                 const btn = orgDropdown.querySelector('button');
                 if (btn) btn.setAttribute('aria-expanded', 'false');
             }
         }
     });
 });
 </script>
