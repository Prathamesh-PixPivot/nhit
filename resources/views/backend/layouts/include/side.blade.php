  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar bg-white shadow-sm">

      <!-- Sidebar Brand -->
      <div class="sidebar-brand d-flex align-items-center justify-content-center py-4 border-bottom">
          <div class="d-flex align-items-center">
              <div class="bg-primary bg-opacity-10 text-primary rounded p-2 me-2">
                  <i class="bi bi-speedometer2 fs-5"></i>
              </div>
              <span class="fw-bold text-primary fs-6">NHIT Dashboard</span>
          </div>
      </div>

      <ul class="sidebar-nav" id="sidebar-nav">
          <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('backend.dashboard.index') ? 'active' : '' }}"
                  href="{{ route('backend.dashboard.index') }}">
                  <i class="bi bi-grid-3x3-gap"></i>
                  <span>Dashboard</span>
              </a>
          </li><!-- End Dashboard Nav -->

          <!-- Expense Management Section -->
          <li class="nav-section-title mt-3 mb-2 px-3">
              <small class="text-muted fw-semibold text-uppercase">Expense Management</small>
          </li>

          @canany(['view-note'])
              <li class="nav-item">
                  <a class="nav-link {{ request()->routeIs('backend.note.*') ? 'active' : 'collapsed' }}"
                      data-bs-target="#expense-notes-nav" data-bs-toggle="collapse" href="#">
                      <i class="bi bi-file-earmark-text"></i>
                      <span>Expense Approval Notes</span>
                      <i class="bi bi-chevron-down ms-auto"></i>
                  </a>
                  <ul id="expense-notes-nav"
                      class="nav-content collapse {{ request()->routeIs('backend.note.*') ? 'show' : '' }}"
                      data-bs-parent="#sidebar-nav">
                      @can(['create-note'])
                          <li>
                              <a href="{{ route('backend.note.create') }}"
                                  class="nav-link {{ request()->routeIs('backend.note.create') ? 'active' : '' }}">
                                  <i class="bi bi-plus-circle"></i><span>Create Note</span>
                              </a>
                          </li>
                      @endcan
                      <li>
                          <a href="{{ route('backend.note.index') }}"
                              class="nav-link {{ request()->routeIs('backend.note.index') ? 'active' : '' }}">
                              <i class="bi bi-list-ul"></i><span>All Notes</span>
                          </a>
                      </li>
                      @can(['view-rule'])
                          <li>
                              <a href="{{ route('backend.note.rule') }}"
                                  class="nav-link {{ request()->routeIs('backend.note.rule') ? 'active' : '' }}">
                                  <i class="bi bi-gear"></i><span>Approval Rules</span>
                              </a>
                          </li>
                      @endcan
                  </ul>
              </li><!-- End Expense Notes Nav -->
          @endcanany

          @canany(['view-payment-note'])
              <li class="nav-item">
                  <a class="nav-link {{ request()->routeIs('backend.payment-note.*') ? 'active' : 'collapsed' }}"
                      data-bs-target="#payment-notes-nav" data-bs-toggle="collapse" href="#">
                      <i class="bi bi-credit-card"></i>
                      <span>Payment Notes</span>
                      <i class="bi bi-chevron-down ms-auto"></i>
                  </a>
                  <ul id="payment-notes-nav"
                      class="nav-content collapse {{ request()->routeIs('backend.payment-note.*') ? 'show' : '' }}"
                      data-bs-parent="#sidebar-nav">
                      <li>
                          <a href="{{ route('backend.payment-note.index') }}"
                              class="nav-link {{ request()->routeIs('backend.payment-note.index') ? 'active' : '' }}">
                              <i class="bi bi-list-ul"></i><span>All Payment Notes</span>
                          </a>
                      </li>
                      @can(['payment-note-view-rule'])
                          <li>
                              <a href="{{ route('backend.payment-note.rule') }}"
                                  class="nav-link {{ request()->routeIs('backend.payment-note.rule') ? 'active' : '' }}">
                                  <i class="bi bi-gear"></i><span>Approval Rules</span>
                              </a>
                          </li>
                      @endcan
                  </ul>
              </li><!-- End Payment Notes Nav -->
          @endcanany

          @canany(['view-reimbursement-note'])
              <li class="nav-item">
                  <a class="nav-link {{ request()->routeIs('backend.reimbursement-note.*') ? 'active' : 'collapsed' }}"
                      data-bs-target="#reimbursement-nav" data-bs-toggle="collapse" href="#">
                      <i class="bi bi-receipt"></i>
                      <span>Travel & Reimbursement</span>
                      <i class="bi bi-chevron-down ms-auto"></i>
                  </a>
                  <ul id="reimbursement-nav"
                      class="nav-content collapse {{ request()->routeIs('backend.reimbursement-note.*') ? 'show' : '' }}"
                      data-bs-parent="#sidebar-nav">
                      @can(['create-reimbursement-note'])
                          <li>
                              <a href="{{ route('backend.reimbursement-note.create') }}"
                                  class="nav-link {{ request()->routeIs('backend.reimbursement-note.create') ? 'active' : '' }}">
                                  <i class="bi bi-plus-circle"></i><span>Create Request</span>
                              </a>
                          </li>
                      @endcan
                      @can(['create-all-user-reimbursement-note'])
                          <li>
                              <a href="{{ route('backend.reimbursement-note.create.user.selection') }}"
                                  class="nav-link {{ request()->routeIs('backend.reimbursement-note.create.user.selection') ? 'active' : '' }}">
                                  <i class="bi bi-people"></i><span>Create for All Users</span>
                              </a>
                          </li>
                      @endcan
                      <li>
                          <a href="{{ route('backend.reimbursement-note.index') }}"
                              class="nav-link {{ request()->routeIs('backend.reimbursement-note.index') ? 'active' : '' }}">
                              <i class="bi bi-list-ul"></i><span>All Reimbursements</span>
                          </a>
                      </li>
                  </ul>
              </li><!-- End Reimbursement Nav -->
          @endcanany

          <!-- Management Section -->
          <li class="nav-section-title mt-3 mb-2 px-3">
              <small class="text-muted fw-semibold text-uppercase">Management</small>
          </li>

          @canany(['create-user', 'edit-user', 'delete-user'])
              <li class="nav-item">
                  <a class="nav-link {{ request()->routeIs('backend.users.*') ? 'active' : 'collapsed' }}"
                      data-bs-target="#users-nav" data-bs-toggle="collapse" href="#">
                      <i class="bi bi-people"></i>
                      <span>User Management</span>
                      <i class="bi bi-chevron-down ms-auto"></i>
                  </a>
                  <ul id="users-nav"
                      class="nav-content collapse {{ request()->routeIs('backend.users.*') ? 'show' : '' }}"
                      data-bs-parent="#sidebar-nav">
                      <li>
                          <a href="{{ route('backend.users.index') }}"
                              class="nav-link {{ request()->routeIs('backend.users.index') ? 'active' : '' }}">
                              <i class="bi bi-list-ul"></i><span>All Users</span>
                          </a>
                      </li>
                      @can(['create-user'])
                          <li>
                              <a href="{{ route('backend.users.create') }}"
                                  class="nav-link {{ request()->routeIs('backend.users.create') ? 'active' : '' }}">
                                  <i class="bi bi-person-plus"></i><span>Add New User</span>
                              </a>
                          </li>
                      @endcan
                  </ul>
              </li><!-- End Users Nav -->
          @endcanany

          @canany(['create-role', 'edit-role', 'delete-role'])
              <li class="nav-item">
                  <a class="nav-link {{ request()->routeIs('backend.roles.*') ? 'active' : 'collapsed' }}"
                      data-bs-target="#roles-nav" data-bs-toggle="collapse" href="#">
                      <i class="bi bi-shield-check"></i>
                      <span>Role Management</span>
                      <i class="bi bi-chevron-down ms-auto"></i>
                  </a>
                  <ul id="roles-nav"
                      class="nav-content collapse {{ request()->routeIs('backend.roles.*') ? 'show' : '' }}"
                      data-bs-parent="#sidebar-nav">
                      <li>
                          <a href="{{ route('backend.roles.index') }}"
                              class="nav-link {{ request()->routeIs('backend.roles.index') ? 'active' : '' }}">
                              <i class="bi bi-list-ul"></i><span>All Roles</span>
                          </a>
                      </li>
                      @can(['create-role'])
                          <li>
                              <a href="{{ route('backend.roles.create') }}"
                                  class="nav-link {{ request()->routeIs('backend.roles.create') ? 'active' : '' }}">
                                  <i class="bi bi-plus-circle"></i><span>Create Role</span>
                              </a>
                          </li>
                      @endcan
                  </ul>
              </li><!-- End Roles Nav -->
          @endcanany

          @canany(['create-department', 'edit-department', 'delete-department'])
              <li class="nav-item">
                  <a class="nav-link {{ request()->routeIs('backend.departments.*') ? 'active' : 'collapsed' }}"
                      data-bs-target="#departments-nav" data-bs-toggle="collapse" href="#">
                      <i class="bi bi-building"></i>
                      <span>Departments</span>
                      <i class="bi bi-chevron-down ms-auto"></i>
                  </a>
                  <ul id="departments-nav"
                      class="nav-content collapse {{ request()->routeIs('backend.departments.*') ? 'show' : '' }}"
                      data-bs-parent="#sidebar-nav">
                      @can(['create-department'])
                          <li>
                              <a href="{{ route('backend.departments.create') }}"
                                  class="nav-link {{ request()->routeIs('backend.departments.create') ? 'active' : '' }}">
                                  <i class="bi bi-plus-circle"></i><span>Create Department</span>
                              </a>
                          </li>
                      @endcan
                      <li>
                          <a href="{{ route('backend.departments.index') }}"
                              class="nav-link {{ request()->routeIs('backend.departments.index') ? 'active' : '' }}">
                              <i class="bi bi-list-ul"></i><span>All Departments</span>
                          </a>
                      </li>
                  </ul>
              </li><!-- End Departments Nav -->
          @endcanany

          @canany(['create-designation', 'edit-designation', 'delete-designation'])
              <li class="nav-item">
                  <a class="nav-link {{ request()->routeIs('backend.designations.*') ? 'active' : 'collapsed' }}"
                      data-bs-target="#designations-nav" data-bs-toggle="collapse" href="#">
                      <i class="bi bi-tag"></i>
                      <span>Designations</span>
                      <i class="bi bi-chevron-down ms-auto"></i>
                  </a>
                  <ul id="designations-nav"
                      class="nav-content collapse {{ request()->routeIs('backend.designations.*') ? 'show' : '' }}"
                      data-bs-parent="#sidebar-nav">
                      @can(['create-designations'])
                          <li>
                              <a href="{{ route('backend.designations.create') }}"
                                  class="nav-link {{ request()->routeIs('backend.designations.create') ? 'active' : '' }}">
                                  <i class="bi bi-plus-circle"></i><span>Create Designation</span>
                              </a>
                          </li>
                      @endcan
                      <li>
                          <a href="{{ route('backend.designations.index') }}"
                              class="nav-link {{ request()->routeIs('backend.designations.index') ? 'active' : '' }}">
                              <i class="bi bi-list-ul"></i><span>All Designations</span>
                          </a>
                      </li>
                  </ul>
              </li><!-- End Designations Nav -->
          @endcanany

          @canany(['view-vendors'])
              <li class="nav-item">
                  <a class="nav-link {{ request()->routeIs('backend.vendors.*') ? 'active' : 'collapsed' }}"
                      data-bs-target="#vendors-nav" data-bs-toggle="collapse" href="#">
                      <i class="bi bi-shop"></i>
                      <span>Vendor Management</span>
                      <i class="bi bi-chevron-down ms-auto"></i>
                  </a>
                  <ul id="vendors-nav"
                      class="nav-content collapse {{ request()->routeIs('backend.vendors.*') ? 'show' : '' }}"
                      data-bs-parent="#sidebar-nav">
                      <li>
                          <a href="{{ route('backend.vendors.index') }}"
                              class="nav-link {{ request()->routeIs('backend.vendors.index') ? 'active' : '' }}">
                              <i class="bi bi-list-ul"></i><span>All Vendors</span>
                          </a>
                      </li>
                      @can(['create-vendors'])
                          <li>
                              <a href="{{ route('backend.vendors.create') }}"
                                  class="nav-link {{ request()->routeIs('backend.vendors.create') ? 'active' : '' }}">
                                  <i class="bi bi-plus-circle"></i><span>Add Vendor</span>
                              </a>
                          </li>
                      @endcan
                  </ul>
              </li><!-- End Vendors Nav -->
          @endcanany

          <!-- Activity & Reports Section -->
          <li class="nav-section-title mt-3 mb-2 px-3">
              <small class="text-muted fw-semibold text-uppercase">Activity & Reports</small>
          </li>

          <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('backend.activity.*') ? 'active' : 'collapsed' }}"
                  data-bs-target="#activity-nav" data-bs-toggle="collapse" href="#">
                  <i class="bi bi-activity"></i>
                  <span>Activity Logs</span>
                  <i class="bi bi-chevron-down ms-auto"></i>
              </a>
              <ul id="activity-nav"
                  class="nav-content collapse {{ request()->routeIs('backend.activity.*') ? 'show' : '' }}"
                  data-bs-parent="#sidebar-nav">
                  <li>
                      <a href="{{ route('backend.activity.index') }}"
                          class="nav-link {{ request()->routeIs('backend.activity.index') ? 'active' : '' }}">
                          <i class="bi bi-list-ul"></i><span>Activity Logs</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('backend.activity.loginHistory') }}"
                          class="nav-link {{ request()->routeIs('backend.activity.loginHistory') ? 'active' : '' }}">
                          <i class="bi bi-clock-history"></i><span>Login History</span>
                      </a>
                  </li>
              </ul>
          </li><!-- End Activity Nav -->

      </ul>
  </aside>
  <!-- End Sidebar-->

  <style>
      .nav-section-title {
          margin: 1rem 0 0.5rem 0;
          font-size: 0.75rem;
          font-weight: 600;
          letter-spacing: 0.05em;
          text-transform: uppercase;
          color: #6c757d;
      }

      .sidebar .nav-link {
          padding: 0.75rem 1rem;
          border-radius: 0.5rem;
          margin: 0.125rem 0.5rem;
          transition: all 0.3s ease;
          font-size: 0.875rem;
      }

      .sidebar .nav-link:hover {
          background-color: #f8f9fa;
          color: #0d6efd;
      }

      .sidebar .nav-link.active {
          background-color: #0d6efd;
          color: white;
          box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
      }

      .sidebar .nav-link i {
          font-size: 1rem;
          margin-right: 0.75rem;
          width: 1.25rem;
          text-align: center;
      }

      .nav-content {
          padding-left: 1rem;
          background-color: rgba(248, 249, 250, 0.5);
          border-radius: 0.375rem;
          margin-top: 0.25rem;
          position: relative;
          z-index: 1000;
      }

      .nav-content .nav-link {
          padding: 0.5rem 0.75rem;
          margin: 0.125rem 0.25rem;
          font-size: 0.8rem;
          border-radius: 0.375rem;
          transition: all 0.3s ease;
          display: block;
      }

      .nav-content .nav-link:hover {
          background-color: rgba(13, 110, 253, 0.1);
          color: #0d6efd;
          transform: translateX(4px);
      }

      .nav-content .nav-link.active {
          background-color: rgba(13, 110, 253, 0.1);
          color: #0d6efd;
      }

      /* Ensure dropdown items are visible when parent is expanded */
      .nav-item.show .nav-content {
          display: block !important;
      }

      /* Fix for Bootstrap collapse animation */
      .nav-content.collapse.show {
          display: block !important;
          opacity: 1;
      }

      .nav-content.collapse {
          display: none;
          opacity: 0;
          transition: opacity 0.3s ease;
      }

      .sidebar-brand {
          background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
          color: white;
          border-radius: 0.5rem;
          margin-bottom: 1rem;
          box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
      }

      .sidebar-brand:hover {
          background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
          color: white;
      }
  </style>
