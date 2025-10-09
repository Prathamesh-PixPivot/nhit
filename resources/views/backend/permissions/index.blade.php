@extends('backend.layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>Dynamic Permissions Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('backend.superadmin.dashboard') }}">SuperAdmin</a></li>
                <li class="breadcrumb-item active">Permissions</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Permissions Configuration</h5>
                            <div>
                                <button type="button" class="btn btn-warning btn-sm" onclick="testPermissions()">
                                    <i class="bi bi-check-circle"></i> Test Permissions
                                </button>
                                <button type="button" class="btn btn-info btn-sm" onclick="resetToDefaults()">
                                    <i class="bi bi-arrow-clockwise"></i> Reset to Defaults
                                </button>
                            </div>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('backend.permissions.update') }}" method="POST" id="permissionsForm">
                            @csrf

                            <!-- Draft Edit Roles -->
                            <div class="card border-primary mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Draft Edit Roles</h6>
                                    <small>Roles that can edit draft payment notes and green notes</small>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <select class="form-select" id="draftEditRoles" name="draft_edit_roles[]" multiple>
                                                @foreach($currentPermissions['draft_edit_roles'] ?? [] as $role)
                                                    <option value="{{ $role }}" selected>{{ $role }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-primary" onclick="addRole('draftEditRoles')">
                                                <i class="bi bi-plus"></i> Add Role
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Note Permissions -->
                            <div class="card border-success mb-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Payment Note Permissions</h6>
                                </div>
                                <div class="card-body">
                                    @foreach(['view_all', 'create', 'edit', 'delete', 'approve'] as $action)
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label class="form-label text-capitalize">{{ str_replace('_', ' ', $action) }}</label>
                                            </div>
                                            <div class="col-md-6">
                                                <select class="form-select" name="payment_note_permissions[{{ $action }}][]" multiple>
                                                    @foreach($currentPermissions['payment_note_permissions'][$action] ?? [] as $role)
                                                        <option value="{{ $role }}" selected>{{ $role }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="button" class="btn btn-success btn-sm"
                                                        onclick="addRoleToSection('payment_note_permissions[{{ $action }}][]')">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Green Note Permissions -->
                            <div class="card border-info mb-4">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Green Note Permissions</h6>
                                </div>
                                <div class="card-body">
                                    @foreach(['view_all', 'create', 'edit', 'delete', 'approve', 'hold'] as $action)
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label class="form-label text-capitalize">{{ str_replace('_', ' ', $action) }}</label>
                                            </div>
                                            <div class="col-md-6">
                                                <select class="form-select" name="green_note_permissions[{{ $action }}][]" multiple>
                                                    @foreach($currentPermissions['green_note_permissions'][$action] ?? [] as $role)
                                                        <option value="{{ $role }}" selected>{{ $role }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="button" class="btn btn-info btn-sm"
                                                        onclick="addRoleToSection('green_note_permissions[{{ $action }}][]')">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Vendor Permissions -->
                            <div class="card border-warning mb-4">
                                <div class="card-header bg-warning text-white">
                                    <h6 class="mb-0">Vendor Permissions</h6>
                                </div>
                                <div class="card-body">
                                    @foreach(['view_all', 'create', 'edit', 'delete', 'manage_accounts'] as $action)
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label class="form-label text-capitalize">{{ str_replace('_', ' ', $action) }}</label>
                                            </div>
                                            <div class="col-md-6">
                                                <select class="form-select" name="vendor_permissions[{{ $action }}][]" multiple>
                                                    @foreach($currentPermissions['vendor_permissions'][$action] ?? [] as $role)
                                                        <option value="{{ $role }}" selected>{{ $role }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="button" class="btn btn-warning btn-sm"
                                                        onclick="addRoleToSection('vendor_permissions[{{ $action }}][]')">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Additional Settings -->
                            <div class="card border-secondary mb-4">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0">Additional Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">SuperAdmin Role</label>
                                                <input type="text" class="form-control" name="superadmin_role"
                                                       value="{{ $currentPermissions['superadmin_role'] ?? 'Super Admin' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox"
                                                       name="enable_department_access" value="1" id="enable_department_access"
                                                       {{ ($currentPermissions['enable_department_access'] ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="enable_department_access">
                                                    Enable Department-Based Access Control
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                       name="allow_creator_access" value="1" id="allow_creator_access"
                                                       {{ ($currentPermissions['allow_creator_access'] ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="allow_creator_access">
                                                    Allow Creators to Edit Their Own Records
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Save Permissions
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                    <i class="bi bi-arrow-clockwise"></i> Reset Form
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Permissions Modal -->
        <div class="modal fade" id="testPermissionsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Test Permissions</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="testForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">User</label>
                                    <select class="form-select" id="testUser" name="user_id">
                                        <option value="">Select User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->getRoleNames()->first() }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Model</label>
                                    <select class="form-select" id="testModel" name="model">
                                        <option value="">Select Model</option>
                                        <option value="payment_note">Payment Note</option>
                                        <option value="green_note">Green Note</option>
                                        <option value="vendor">Vendor</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Action</label>
                                    <select class="form-select" id="testAction" name="action">
                                        <option value="">Select Action</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                        <div id="testResult" class="mt-3" style="display: none;">
                            <div class="alert" id="testAlert">
                                <div id="testContent"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="runPermissionTest()">Test</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
<script>
let availableRoles = @json($allRoles);

function addRole(selectId) {
    const select = document.getElementById(selectId);
    const searchTerm = prompt('Enter role name to add:');

    if (searchTerm && !select.querySelector(`option[value="${searchTerm}"]`)) {
        // Check if role exists in available roles
        if (availableRoles.includes(searchTerm)) {
            const option = document.createElement('option');
            option.value = searchTerm;
            option.textContent = searchTerm;
            option.selected = true;
            select.appendChild(option);
        } else {
            alert('Role not found. Available roles: ' + availableRoles.join(', '));
        }
    }
}

function addRoleToSection(selectName) {
    const searchTerm = prompt('Enter role name to add:');

    if (searchTerm) {
        const select = document.querySelector(`select[name="${selectName}"]`);

        if (!select.querySelector(`option[value="${searchTerm}"]`)) {
            if (availableRoles.includes(searchTerm)) {
                const option = document.createElement('option');
                option.value = searchTerm;
                option.textContent = searchTerm;
                option.selected = true;
                select.appendChild(option);
            } else {
                alert('Role not found. Available roles: ' + availableRoles.join(', '));
            }
        }
    }
}

function resetForm() {
    if (confirm('Are you sure you want to reset the form? All changes will be lost.')) {
        location.reload();
    }
}

function resetToDefaults() {
    if (confirm('Are you sure you want to reset permissions to default configuration?')) {
        window.location.href = '{{ route("backend.permissions.reset") }}';
    }
}

function testPermissions() {
    const modal = new bootstrap.Modal(document.getElementById('testPermissionsModal'));
    modal.show();

    // Populate actions based on selected model
    document.getElementById('testModel').addEventListener('change', function() {
        const model = this.value;
        const actionSelect = document.getElementById('testAction');

        actionSelect.innerHTML = '<option value="">Select Action</option>';

        let actions = [];
        switch(model) {
            case 'payment_note':
                actions = ['view_all', 'create', 'edit', 'delete', 'approve'];
                break;
            case 'green_note':
                actions = ['view_all', 'create', 'edit', 'delete', 'approve', 'hold'];
                break;
            case 'vendor':
                actions = ['view_all', 'create', 'edit', 'delete', 'manage_accounts'];
                break;
        }

        actions.forEach(action => {
            const option = document.createElement('option');
            option.value = action;
            option.textContent = action.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
            actionSelect.appendChild(option);
        });
    });
}

function runPermissionTest() {
    const userId = document.getElementById('testUser').value;
    const model = document.getElementById('testModel').value;
    const action = document.getElementById('testAction').value;

    if (!userId || !model || !action) {
        alert('Please fill in all fields');
        return;
    }

    fetch('{{ route("backend.permissions.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            user_id: userId,
            model: model,
            action: action
        })
    })
    .then(response => response.json())
    .then(data => {
        const resultDiv = document.getElementById('testResult');
        const alertDiv = document.getElementById('testAlert');
        const contentDiv = document.getElementById('testContent');

        resultDiv.style.display = 'block';

        if (data.error) {
            alertDiv.className = 'alert alert-danger';
            contentDiv.innerHTML = `<strong>Error:</strong> ${data.error}`;
        } else {
            alertDiv.className = data.has_permission ? 'alert alert-success' : 'alert alert-warning';
            contentDiv.innerHTML = `
                <strong>User:</strong> ${data.user}<br>
                <strong>Role:</strong> ${data.role}<br>
                <strong>Action:</strong> ${data.action.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}<br>
                <strong>Model:</strong> ${data.model.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}<br>
                <strong>Permission:</strong> ${data.has_permission ? '<span class="text-success">GRANTED</span>' : '<span class="text-danger">DENIED</span>'}
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error testing permissions');
    });
}

// Auto-save draft functionality (optional)
let autoSaveTimer;
function enableAutoSave() {
    document.getElementById('permissionsForm').addEventListener('change', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
            // Could implement auto-save here if needed
            console.log('Form changed - could auto-save');
        }, 2000);
    });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    enableAutoSave();
});
</script>
@endpush
