@extends('admin.layouts.horizontal')

@section('title', 'Class Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .action-btn {
        padding: 5px 10px;
        margin: 0 2px;
    }
</style>
@endpush

@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-school me-2"></i>Class Management</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addClassModal">
            <i class="fas fa-plus me-1"></i>Add New Class
        </button>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover" id="classesTable">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Class Name</th>
                    <th>Class Numeric</th>
                    <th>Total Sections</th>
                    <th>Total Students</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<!-- Add Class Modal -->
<div class="modal fade" id="addClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Class</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addClassForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="class_name" placeholder="e.g., Class 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Class Numeric <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="class_numeric" placeholder="e.g., 1" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="Active" id="classActive" value="Active" checked>
                            <label class="form-check-label" for="classActive">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Class Modal -->
<div class="modal fade" id="editClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editClassForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_class_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_class_name" name="class_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Class Numeric <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_class_numeric" name="class_numeric" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_class_active" value="Active">
                            <label class="form-check-label" for="edit_class_active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i>Update Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    loadClasses();
    
    // Add Class Form Submit
    $('#addClassForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("admin.classes.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                $('#addClassModal').modal('hide');
                $('#addClassForm')[0].reset();
                loadClasses();
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong!', 'error');
            }
        });
    });

    // Edit Class Form Submit
    $('#editClassForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_class_id').val();
        $.ajax({
            url: `/admin/classes/${id}`,
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                $('#editClassModal').modal('hide');
                loadClasses();
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong!', 'error');
            }
        });
    });
});

function loadClasses() {
    $.ajax({
        url: '{{ route("admin.classes.data") }}',
        method: 'GET',
        success: function(response) {
            let html = '';
            if (response.data.length > 0) {
           
                response.data.forEach(function(cls) {
                    html += `<tr>
                        <td>${cls.id}</td>
                        <td><strong>${cls.class_name}</strong></td>
                        <td><span class="badge bg-info">${cls.class_numeric}</span></td>
                        <td>${cls.sections_count || 0}</td>
                        <td>${cls.students_count || 0}</td>
                        <td><span class="badge bg-${cls.is_active ? 'success' : 'secondary'}">${cls.is_active ? 'Active' : 'Inactive'}</span></td>
                        <td>
                            <button class="btn btn-sm btn-warning action-btn" onclick="editClass(${cls.id}, '${cls.class_name}', ${cls.class_numeric}, ${cls.is_active})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger action-btn" onclick="deleteClass(${cls.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="7" class="text-center">No classes found</td></tr>';
            }
            $('#classesTable tbody').html(html);
            
            // Initialize DataTable if not already initialized
            if (!$.fn.DataTable.isDataTable('#classesTable')) {
                $('#classesTable').DataTable({
                    order: [[0, 'desc']],
                    pageLength: 10
                });
            }
        },
        error: function(xhr) {
            $('#classesTable tbody').html('<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>');
        }
    });
}

function editClass(id, name, numeric, isActive) {
    $('#edit_class_id').val(id);
    $('#edit_class_name').val(name);
    $('#edit_class_numeric').val(numeric);
    $('#edit_class_active').prop('checked', isActive == 1);
    $('#editClassModal').modal('show');
}

function deleteClass(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/classes/${id}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire('Deleted!', response.message, 'success');
                    loadClasses();
                },
                error: function(xhr) {
                    Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong!', 'error');
                }
            });
        }
    });
}
</script>
@endpush
