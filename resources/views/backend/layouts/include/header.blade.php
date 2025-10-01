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
