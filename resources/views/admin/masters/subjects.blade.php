@extends('admin.layouts.horizontal')

@section('title', 'Subject Management')

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
        <h5><i class="fas fa-book me-2"></i>Subject Management</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
            <i class="fas fa-plus me-1"></i>Add New Subject
        </button>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover" id="subjectsTable">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Subject Name</th>
                    <th>Subject Code</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Subject</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSubjectForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Subject Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="subject_name" placeholder="e.g., Mathematics" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="subject_code" placeholder="e.g., MATH101" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Optional description"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="subjectActive" value="1" checked>
                            <label class="form-check-label" for="subjectActive">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Subject Modal -->
<div class="modal fade" id="editSubjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSubjectForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_subject_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Subject Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_subject_name" name="subject_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_subject_code" name="subject_code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_subject_active" value="1">
                            <label class="form-check-label" for="edit_subject_active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i>Update Subject
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
    loadSubjects();
    
    // Add Subject Form Submit
    $('#addSubjectForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("admin.subjects.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                $('#addSubjectModal').modal('hide');
                $('#addSubjectForm')[0].reset();
                loadSubjects();
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong!', 'error');
            }
        });
    });

    // Edit Subject Form Submit
    $('#editSubjectForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_subject_id').val();
        $.ajax({
            url: `/admin/subjects/${id}`,
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                $('#editSubjectModal').modal('hide');
                loadSubjects();
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong!', 'error');
            }
        });
    });
});

function loadSubjects() {
    $.ajax({
        url: '{{ route("admin.subjects.data") }}',
        method: 'GET',
        success: function(response) {
            let html = '';
            if (response.data.length > 0) {
                response.data.forEach(function(subject) {
                    html += `<tr>
                        <td>${subject.id}</td>
                        <td><strong>${subject.subject_name}</strong></td>
                        <td><span class="badge bg-info">${subject.subject_code}</span></td>
                        <td>${subject.description || '-'}</td>
                        <td><span class="badge bg-${subject.is_active ? 'success' : 'secondary'}">${subject.is_active ? 'Active' : 'Inactive'}</span></td>
                        <td>
                            <button class="btn btn-sm btn-warning action-btn" onclick='editSubject(${JSON.stringify(subject)})' title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger action-btn" onclick="deleteSubject(${subject.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="6" class="text-center">No subjects found</td></tr>';
            }
            $('#subjectsTable tbody').html(html);
            
            // Initialize DataTable if not already initialized
            if (!$.fn.DataTable.isDataTable('#subjectsTable')) {
                $('#subjectsTable').DataTable({
                    order: [[0, 'desc']],
                    pageLength: 10
                });
            }
        },
        error: function(xhr) {
            $('#subjectsTable tbody').html('<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>');
        }
    });
}

function editSubject(subject) {
    $('#edit_subject_id').val(subject.id);
    $('#edit_subject_name').val(subject.subject_name);
    $('#edit_subject_code').val(subject.subject_code);
    $('#edit_description').val(subject.description);
    $('#edit_subject_active').prop('checked', subject.is_active == 1);
    $('#editSubjectModal').modal('show');
}

function deleteSubject(id) {
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
                url: `/admin/subjects/${id}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire('Deleted!', response.message, 'success');
                    loadSubjects();
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
