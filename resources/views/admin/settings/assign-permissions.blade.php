@extends('admin.layouts.app')

@section('title', 'Assign Permissions to Roles')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Assign Permissions to Roles</h2>
            <p class="text-muted">Select a role and assign permissions to it</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5>Select Role</h5>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($roles as $role)
                        <a href="#" class="list-group-item list-group-item-action role-item" 
                           data-role-id="{{ $role->id }}"
                           data-role-name="{{ $role->display_name }}"
                           data-permissions="{{ $role->permissions->pluck('id')->toJson() }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $role->display_name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $role->permissions->count() }} permissions</small>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card" id="permissionsCard" style="display: none;">
                <div class="card-header">
                    <h5>Assign Permissions to <span id="selectedRoleName"></span></h5>
                </div>
                <div class="card-body">
                    <form id="assignPermissionsForm" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllPermissions()">
                                Select All Permissions
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllPermissions()">
                                Deselect All
                            </button>
                        </div>
                        
                        @foreach($modules as $module)
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2">
                                    <span class="badge bg-primary">{{ ucfirst($module) }} Module</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary float-end" 
                                            onclick="toggleModule('{{ $module }}')">
                                        Toggle All
                                    </button>
                                </h6>
                                <div class="row">
                                    @foreach($permissions->where('module', $module) as $permission)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox module-{{ $module }}" 
                                                       type="checkbox" 
                                                       name="permissions[]" 
                                                       value="{{ $permission->id }}" 
                                                       id="perm_{{ $permission->id }}">
                                                <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                    {{ $permission->display_name }}
                                                    <br>
                                                    <small class="text-muted">{{ $permission->name }}</small>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Permissions
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card" id="selectRoleMessage">
                <div class="card-body text-center py-5">
                    <i class="fas fa-hand-pointer fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Please select a role from the left panel</h5>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.querySelectorAll('.role-item').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Remove active class from all items
        document.querySelectorAll('.role-item').forEach(i => i.classList.remove('active'));
        this.classList.add('active');
        
        const roleId = this.dataset.roleId;
        const roleName = this.dataset.roleName;
        const permissions = JSON.parse(this.dataset.permissions);
        
        // Update form action
        document.getElementById('assignPermissionsForm').action = `/admin/settings/roles/${roleId}/permissions`;
        
        // Update role name
        document.getElementById('selectedRoleName').textContent = roleName;
        
        // Uncheck all checkboxes first
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
        
        // Check assigned permissions
        permissions.forEach(permId => {
            const checkbox = document.getElementById(`perm_${permId}`);
            if (checkbox) checkbox.checked = true;
        });
        
        // Show permissions card
        document.getElementById('selectRoleMessage').style.display = 'none';
        document.getElementById('permissionsCard').style.display = 'block';
    });
});

function toggleModule(module) {
    const checkboxes = document.querySelectorAll(`.module-${module}`);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(cb => cb.checked = !allChecked);
}

function selectAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
}

function deselectAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
}

// Form submission with loading state
document.getElementById('assignPermissionsForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
});
</script>
@endsection
