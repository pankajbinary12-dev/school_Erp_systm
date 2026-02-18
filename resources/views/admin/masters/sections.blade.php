@extends('admin.layouts.horizontal')

@section('title', 'Section Management')

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
        <h5><i class="fas fa-layer-group me-2"></i>Section Management</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSectionModal">
            <i class="fas fa-plus me-1"></i>Add New Section
        </button>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Filter by Class:</label>
        <select class="form-select" id="filterClass" style="max-width: 300px;">
            <option value="">All Classes</option>
        </select>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover" id="sectionsTable">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Class</th>
                    <th>Section Name</th>
                    <th>Capacity</th>
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

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Section</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSectionForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class <span class="text-danger">*</span></label>
                        <select class="form-select" name="class_id" id="add_class_id" required>
                            <option value="">Select Class</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Section Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="section_name" placeholder="e.g., A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Capacity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="capacity" placeholder="e.g., 40" required min="1">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="sectionActive" value="1" checked>
                            <label class="form-check-label" for="sectionActive">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save Section
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Section Modal -->
<div class="modal fade" id="editSectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSectionForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_section_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class <span class="text-danger">*</span></label>
                        <select class="form-select" name="class_id" id="edit_class_id" required>
                            <option value="">Select Class</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Section Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_section_name" name="section_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Capacity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_capacity" name="capacity" required min="1">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_section_active" value="1">
                            <label class="form-check-label" for="edit_section_active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i>Update Section
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
    loadSections();
    
    // Filter by class
    $('#filterClass').on('change', function() {
        loadSections();
    });
    
    // Add Section Form Submit
    $('#addSectionForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("admin.sections.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                $('#addSectionModal').modal('hide');
                $('#addSectionForm')[0].reset();
                loadSections();
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong!', 'error');
            }
        });
    });

    // Edit Section Form Submit
    $('#editSectionForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_section_id').val();
        $.ajax({
            url: `/admin/sections/${id}`,
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                $('#editSectionModal').modal('hide');
                loadSections();
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong!', 'error');
            }
        });
    });
});

function loadClasses() {
    $.ajax({
        url: '{{ route("admin.get.classes") }}',
        method: 'GET',
        success: function(response) {
            let options = '<option value="">Select Class</option>';
            let filterOptions = '<option value="">All Classes</option>';
            response.data.forEach(function(cls) {
                options += `<option value="${cls.id}">${cls.class_name}</option>`;
                filterOptions += `<option value="${cls.id}">${cls.class_name}</option>`;
            });
            $('#add_class_id, #edit_class_id').html(options);
            $('#filterClass').html(filterOptions);
        }
    });
}

function loadSections() {
    const classId = $('#filterClass').val();
    $.ajax({
        url: '{{ route("admin.sections.data") }}',
        method: 'GET',
        data: { class_id: classId },
        success: function(response) {
            let html = '';
            if (response.data.length > 0) {
                response.data.forEach(function(section) {
                    html += `<tr>
                        <td>${section.id}</td>
                        <td><span class="badge bg-info">${section.class?.class_name || 'N/A'}</span></td>
                        <td><strong>${section.section_name}</strong></td>
                        <td>${section.capacity}</td>
                        <td><span class="badge bg-${section.is_active ? 'success' : 'secondary'}">${section.is_active ? 'Active' : 'Inactive'}</span></td>
                        <td>
                            <button class="btn btn-sm btn-warning action-btn" onclick='editSection(${JSON.stringify(section)})' title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger action-btn" onclick="deleteSection(${section.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="6" class="text-center">No sections found</td></tr>';
            }
            $('#sectionsTable tbody').html(html);
            
            // Initialize DataTable if not already initialized
            if (!$.fn.DataTable.isDataTable('#sectionsTable')) {
                $('#sectionsTable').DataTable({
                    order: [[0, 'desc']],
                    pageLength: 10
                });
            }
        },
        error: function(xhr) {
            $('#sectionsTable tbody').html('<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>');
        }
    });
}

function editSection(section) {
    $('#edit_section_id').val(section.id);
    $('#edit_class_id').val(section.class_id);
    $('#edit_section_name').val(section.section_name);
    $('#edit_capacity').val(section.capacity);
    $('#edit_section_active').prop('checked', section.is_active == 1);
    $('#editSectionModal').modal('show');
}

function deleteSection(id) {
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
                url: `/admin/sections/${id}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire('Deleted!', response.message, 'success');
                    loadSections();
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
