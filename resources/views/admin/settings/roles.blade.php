@extends('admin.layouts.app')

@section('title', 'Roles Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Roles Management</h2>
                <button class="btn btn-primary" id="addRoleBtn">
                    <i class="fas fa-plus"></i> Add New Role
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="rolesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Display Name</th>
                            <th>Description</th>
                            <th>Permissions</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="roleForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalTitle">Add New Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="roleId" name="id">
                    
                    <div class="mb-3">
                        <label class="form-label">Name (Unique Key) <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="roleName" class="form-control" required placeholder="e.g., manager">
                        <small class="text-muted">Use lowercase, no spaces (use underscore)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Display Name <span class="text-danger">*</span></label>
                        <input type="text" name="display_name" id="roleDisplayName" class="form-control" required placeholder="e.g., Manager">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="roleDescription" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="roleIsActive" checked>
                            <label class="form-check-label" for="roleIsActive">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveRoleBtn">
                        <i class="fas fa-save"></i> Save Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
console.log('Roles script loaded!');
console.log('jQuery version:', typeof jQuery !== 'undefined' ? jQuery.fn.jquery : 'Not loaded');

$(document).ready(function() {
    console.log('Roles document ready!');
    loadRoles();

    $('#addRoleBtn').click(function() {
        console.log('Add role button clicked');
        resetForm();
        $('#roleModalTitle').text('Add New Role');
        $('#roleModal').modal('show');
    });

    $('#roleForm').submit(function(e) {
        e.preventDefault();
        console.log('Role form submitted');
        saveRole();
    });
});

function loadRoles() {
    console.log('Loading roles...');
    $.ajax({
        url: '{{ route("admin.settings.roles") }}',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Roles loaded:', response);
            renderRolesTable(response.roles);
        },
        error: function(xhr) {
            console.error('Error loading roles:', xhr);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to load roles',
                confirmButtonColor: '#d33'
            });
        }
    });
}

function renderRolesTable(roles) {
    let html = '';
    roles.forEach(function(role) {
        html += `
            <tr>
                <td>${role.id}</td>
                <td><code>${role.name}</code></td>
                <td>${role.display_name}</td>
                <td>${role.description || '-'}</td>
                <td><span class="badge bg-info">${role.permissions_count} permissions</span></td>
                <td>
                    ${role.is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'}
                </td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editRole(${role.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteRole(${role.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    $('#rolesTable tbody').html(html);
}

function saveRole() {
    const roleId = $('#roleId').val();
    const url = roleId ? `/admin/settings/roles/${roleId}` : '{{ route("admin.settings.roles.store") }}';
    const method = roleId ? 'PUT' : 'POST';
    
    const formData = {
        name: $('#roleName').val(),
        display_name: $('#roleDisplayName').val(),
        description: $('#roleDescription').val(),
        is_active: $('#roleIsActive').is(':checked') ? 1 : 0
    };

    $('#saveRoleBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

    $.ajax({
        url: url,
        type: method,
        data: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
            $('#roleModal').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: response.message || 'Role saved successfully!',
                timer: 2000,
                showConfirmButton: false
            });
            loadRoles();
            resetForm();
        },
        error: function(xhr) {
            let errorMsg = 'Error saving role';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                errorMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: errorMsg,
                confirmButtonColor: '#d33'
            });
        },
        complete: function() {
            $('#saveRoleBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save Role');
        }
    });
}

function editRole(id) {
    $.ajax({
        url: `/admin/settings/roles/${id}/edit`,
        type: 'GET',
        success: function(response) {
            const role = response.role;
            $('#roleId').val(role.id);
            $('#roleName').val(role.name);
            $('#roleDisplayName').val(role.display_name);
            $('#roleDescription').val(role.description);
            $('#roleIsActive').prop('checked', role.is_active);
            $('#roleModalTitle').text('Edit Role');
            $('#roleModal').modal('show');
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to load role data',
                confirmButtonColor: '#d33'
            });
        }
    });
}

function deleteRole(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/settings/roles/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: response.message || 'Role deleted successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    loadRoles();
                },
                error: function(xhr) {
                    let errorMsg = 'Error deleting role';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMsg,
                        confirmButtonColor: '#d33'
                    });
                }
            });
        }
    });
}

function resetForm() {
    $('#roleForm')[0].reset();
    $('#roleId').val('');
    $('#roleIsActive').prop('checked', true);
}
</script>
@endsection
