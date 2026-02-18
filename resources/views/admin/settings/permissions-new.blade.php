@extends('admin.layouts.app')

@section('title', 'Permissions Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Permissions Management</h2>
                <button class="btn btn-primary" id="addPermissionBtn">
                    <i class="fas fa-plus"></i> Add New Permission
                </button>
            </div>
        </div>
    </div>

    <div id="alertContainer"></div>

    <div class="card">
        <div class="card-body" id="permissionsContainer">
            <!-- Data will be loaded via AJAX -->
        </div>
    </div>
</div>

<!-- Add/Edit Permission Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="permissionForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="permissionModalTitle">Add New Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="permissionId" name="id">
                    
                    <div class="mb-3">
                        <label class="form-label">Name (Unique Key) <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="permissionName" class="form-control" required placeholder="e.g., view_reports">
                        <small class="text-muted">Use lowercase, no spaces (use underscore)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Display Name <span class="text-danger">*</span></label>
                        <input type="text" name="display_name" id="permissionDisplayName" class="form-control" required placeholder="e.g., View Reports">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Module <span class="text-danger">*</span></label>
                        <select name="module" id="permissionModule" class="form-control" required>
                            <option value="">Select Module</option>
                            <option value="students">Students</option>
                            <option value="teachers">Teachers</option>
                            <option value="staff">Staff</option>
                            <option value="attendance">Attendance</option>
                            <option value="exams">Exams</option>
                            <option value="fees">Fees</option>
                            <option value="library">Library</option>
                            <option value="academic">Academic</option>
                            <option value="reports">Reports</option>
                            <option value="settings">Settings</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="permissionDescription" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="permissionIsActive" checked>
                            <label class="form-check-label" for="permissionIsActive">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="savePermissionBtn">
                        <i class="fas fa-save"></i> Save Permission
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Load permissions on page load
    loadPermissions();

    // Add Permission Button
    $('#addPermissionBtn').click(function() {
        resetForm();
        $('#permissionModalTitle').text('Add New Permission');
        $('#permissionModal').modal('show');
    });

    // Form Submit
    $('#permissionForm').submit(function(e) {
        e.preventDefault();
        savePermission();
    });
});

// Load all permissions
function loadPermissions() {
    $.ajax({
        url: '{{ route("admin.settings.permissions") }}',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            renderPermissionsTable(response.permissions, response.modules);
        },
        error: function(xhr) {
            showAlert('Error loading permissions', 'danger');
        }
    });
}

// Render permissions table grouped by module
function renderPermissionsTable(permissions, modules) {
    let html = '';
    
    modules.forEach(function(module) {
        const modulePermissions = permissions.filter(p => p.module === module);
        if (modulePermissions.length === 0) return;
        
        html += `
            <h5 class="mt-3 mb-3">
                <span class="badge bg-primary">${capitalizeFirst(module)} Module</span>
            </h5>
            <div class="table-responsive mb-4">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Display Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        modulePermissions.forEach(function(permission) {
            html += `
                <tr>
                    <td>${permission.id}</td>
                    <td><code>${permission.name}</code></td>
                    <td>${permission.display_name}</td>
                    <td>${permission.description || '-'}</td>
                    <td>
                        ${permission.is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'}
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editPermission(${permission.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deletePermission(${permission.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
    });
    
    $('#permissionsContainer').html(html);
}

// Save permission (Add or Update)
function savePermission() {
    const permissionId = $('#permissionId').val();
    const url = permissionId ? `/admin/settings/permissions/${permissionId}` : '{{ route("admin.settings.permissions.store") }}';
    const method = permissionId ? 'PUT' : 'POST';
    
    const formData = {
        name: $('#permissionName').val(),
        display_name: $('#permissionDisplayName').val(),
        module: $('#permissionModule').val(),
        description: $('#permissionDescription').val(),
        is_active: $('#permissionIsActive').is(':checked') ? 1 : 0
    };

    $('#savePermissionBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

    $.ajax({
        url: url,
        type: method,
        data: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
            $('#permissionModal').modal('hide');
            showAlert(response.message || 'Permission saved successfully!', 'success');
            loadPermissions();
            resetForm();
        },
        error: function(xhr) {
            let errorMsg = 'Error saving permission';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            showAlert(errorMsg, 'danger');
        },
        complete: function() {
            $('#savePermissionBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save Permission');
        }
    });
}

// Edit permission
function editPermission(id) {
    $.ajax({
        url: `/admin/settings/permissions/${id}/edit`,
        type: 'GET',
        success: function(response) {
            const permission = response.permission;
            $('#permissionId').val(permission.id);
            $('#permissionName').val(permission.name);
            $('#permissionDisplayName').val(permission.display_name);
            $('#permissionModule').val(permission.module);
            $('#permissionDescription').val(permission.description);
            $('#permissionIsActive').prop('checked', permission.is_active);
            $('#permissionModalTitle').text('Edit Permission');
            $('#permissionModal').modal('show');
        },
        error: function(xhr) {
            showAlert('Error loading permission data', 'danger');
        }
    });
}

// Delete permission
function deletePermission(id) {
    if (!confirm('Are you sure you want to delete this permission?')) {
        return;
    }

    $.ajax({
        url: `/admin/settings/permissions/${id}`,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
            showAlert(response.message || 'Permission deleted successfully!', 'success');
            loadPermissions();
        },
        error: function(xhr) {
            let errorMsg = 'Error deleting permission';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            showAlert(errorMsg, 'danger');
        }
    });
}

// Reset form
function resetForm() {
    $('#permissionForm')[0].reset();
    $('#permissionId').val('');
    $('#permissionIsActive').prop('checked', true);
}

// Show alert
function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('#alertContainer').html(alertHtml);
    
    // Auto dismiss after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
}

// Capitalize first letter
function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}
</script>
@endsection
