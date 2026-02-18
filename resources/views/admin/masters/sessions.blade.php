@extends('admin.layouts.horizontal')

@section('title', 'Session Management')

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
        <h5><i class="fas fa-calendar-alt me-2"></i>Session Management</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSessionModal">
            <i class="fas fa-plus me-1"></i>Add New Session
        </button>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover" id="sessionsTable">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Session Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
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

<!-- Add Session Modal -->
<div class="modal fade" id="addSessionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Session</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSessionForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Session Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="session_name" placeholder="e.g., 2024-2025" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="end_date" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="sessionActive" value="Active" checked>
                            <label class="form-check-label" for="sessionActive">Active (Note: Only one session can be active at a time)</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save Session
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Session Modal -->
<div class="modal fade" id="editSessionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Session</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSessionForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_session_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Session Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_session_name" name="session_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="edit_end_date" name="end_date" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_session_active" value="Active">
                            <label class="form-check-label" for="edit_session_active">Active (Note: Only one session can be active at a time)</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i>Update Session
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
    loadSessions();
    
    // Add Session Form Submit
    $('#addSessionForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("admin.sessions.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                $('#addSessionModal').modal('hide');
                $('#addSessionForm')[0].reset();
                loadSessions();
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong!', 'error');
            }
        });
    });

    // Edit Session Form Submit
    $('#editSessionForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_session_id').val();
        $.ajax({
            url: `/admin/sessions/${id}`,
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                $('#editSessionModal').modal('hide');
                loadSessions();
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong!', 'error');
            }
        });
    });
});

function loadSessions() {
    $.ajax({
        url: '{{ route("admin.sessions.data") }}',
        method: 'GET',
        success: function(response) {
            let html = '';
            if (response.data.length > 0) {
                response.data.forEach(function(session) {
                    html += `<tr>
                        <td>${session.id}</td>
                        <td><strong>${session.session_name}</strong></td>
                        <td>${formatDate(session.start_date)}</td>
                        <td>${formatDate(session.end_date)}</td>
                        <td><span class="badge bg-${session.is_active ? 'success' : 'secondary'}">${session.is_active ? 'Active' : 'Inactive'}</span></td>
                        <td>
                            <button class="btn btn-sm btn-warning action-btn" onclick='editSession(${JSON.stringify(session)})' title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger action-btn" onclick="deleteSession(${session.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="6" class="text-center">No sessions found</td></tr>';
            }
            $('#sessionsTable tbody').html(html);
            
            // Initialize DataTable if not already initialized
            if (!$.fn.DataTable.isDataTable('#sessionsTable')) {
                $('#sessionsTable').DataTable({
                    order: [[0, 'desc']],
                    pageLength: 10
                });
            }
        },
        error: function(xhr) {
            $('#sessionsTable tbody').html('<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>');
        }
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

function editSession(session) {
    $('#edit_session_id').val(session.id);
    $('#edit_session_name').val(session.session_name);
    $('#edit_start_date').val(session.start_date);
    $('#edit_end_date').val(session.end_date);
    $('#edit_session_active').prop('checked', session.is_active == 1);
    $('#editSessionModal').modal('show');
}

function deleteSession(id) {
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
                url: `/admin/sessions/${id}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire('Deleted!', response.message, 'success');
                    loadSessions();
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
