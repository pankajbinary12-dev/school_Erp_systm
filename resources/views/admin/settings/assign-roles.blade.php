@extends('admin.layouts.app')

@section('title', 'Assign Roles to Users')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Assign Roles to Users</h2>
            <p class="text-muted">Assign roles to Admins, Teachers, and Staff members</p>
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

    <!-- Admins Section -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-user-shield"></i> Admins</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Current Roles</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admins as $admin)
                        <tr>
                            <td>{{ $admin->id }}</td>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td><code>{{ $admin->username }}</code></td>
                            <td>
                                @if($admin->roles && $admin->roles->count() > 0)
                                    @foreach($admin->roles as $role)
                                        <span class="badge bg-info">{{ $role->display_name }}</span>
                                    @endforeach
                                @else
                                    <span class="badge bg-secondary">No roles</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="assignRoles('admin', {{ $admin->id }}, '{{ $admin->name }}', {{ $admin->roles ? $admin->roles->pluck('id') : '[]' }})">
                                    <i class="fas fa-user-tag"></i> Assign Roles
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Teachers Section -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-chalkboard-teacher"></i> Teachers</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Employee ID</th>
                            <th>Current Roles</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teachers as $teacher)
                        <tr>
                            <td>{{ $teacher->id }}</td>
                            <td>{{ $teacher->first_name }} {{ $teacher->last_name }}</td>
                            <td>{{ $teacher->email }}</td>
                            <td><code>{{ $teacher->employee_id }}</code></td>
                            <td>
                                @if($teacher->roles && $teacher->roles->count() > 0)
                                    @foreach($teacher->roles as $role)
                                        <span class="badge bg-info">{{ $role->display_name }}</span>
                                    @endforeach
                                @else
                                    <span class="badge bg-secondary">No roles</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-success" onclick="assignRoles('teacher', {{ $teacher->id }}, '{{ $teacher->first_name }} {{ $teacher->last_name }}', {{ $teacher->roles ? $teacher->roles->pluck('id') : '[]' }})">
                                    <i class="fas fa-user-tag"></i> Assign Roles
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Staff Section -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-users"></i> Staff Members</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Employee ID</th>
                            <th>Current Roles</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $member)
                        <tr>
                            <td>{{ $member->id }}</td>
                            <td>{{ $member->first_name }} {{ $member->last_name }}</td>
                            <td>{{ $member->email }}</td>
                            <td><code>{{ $member->employee_id }}</code></td>
                            <td>
                                @if($member->roles && $member->roles->count() > 0)
                                    @foreach($member->roles as $role)
                                        <span class="badge bg-info">{{ $role->display_name }}</span>
                                    @endforeach
                                @else
                                    <span class="badge bg-secondary">No roles</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="assignRoles('staff', {{ $member->id }}, '{{ $member->first_name }} {{ $member->last_name }}', {{ $member->roles ? $member->roles->pluck('id') : '[]' }})">
                                    <i class="fas fa-user-tag"></i> Assign Roles
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Assign Roles Modal -->
<div class="modal fade" id="assignRolesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="assignRolesForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assign Roles to <span id="userName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Select roles to assign to this user:</p>
                    @foreach($roles as $role)
                        <div class="form-check mb-2">
                            <input class="form-check-input role-checkbox" 
                                   type="checkbox" 
                                   name="roles[]" 
                                   value="{{ $role->id }}" 
                                   id="role_{{ $role->id }}">
                            <label class="form-check-label" for="role_{{ $role->id }}">
                                <strong>{{ $role->display_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $role->description }}</small>
                                <br>
                                <small class="text-info">{{ $role->permissions->count() }} permissions</small>
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Roles
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function assignRoles(userType, userId, userName, currentRoles) {
    // Set form action based on user type
    let action = '';
    if (userType === 'admin') {
        action = `/admin/settings/admins/${userId}/roles`;
    } else if (userType === 'teacher') {
        action = `/admin/settings/teachers/${userId}/roles`;
    } else if (userType === 'staff') {
        action = `/admin/settings/staff/${userId}/roles`;
    }
    
    document.getElementById('assignRolesForm').action = action;
    document.getElementById('userName').textContent = userName;
    
    // Uncheck all checkboxes first
    document.querySelectorAll('.role-checkbox').forEach(cb => cb.checked = false);
    
    // Check current roles
    if (Array.isArray(currentRoles)) {
        currentRoles.forEach(roleId => {
            const checkbox = document.getElementById(`role_${roleId}`);
            if (checkbox) checkbox.checked = true;
        });
    }
    
    // Show modal
    new bootstrap.Modal(document.getElementById('assignRolesModal')).show();
}

// Form submission with loading state
document.getElementById('assignRolesForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
});
</script>
@endsection
