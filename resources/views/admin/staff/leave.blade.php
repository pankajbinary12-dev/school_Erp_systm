@extends('admin.layouts.horizontal')

@section('title', 'Staff Leave Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .status-pending { background: #fff3cd; color: #856404; }
    .status-approved { background: #d4edda; color: #155724; }
    .status-rejected { background: #f8d7da; color: #721c24; }
</style>
@endpush

@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-calendar-times me-2"></i>Staff Leave Management</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
            <i class="fas fa-plus me-1"></i>Apply Leave
        </button>
    </div>
    
    <div class="table-responsive p-3">
        <table id="leaveTable" class="table table-hover table-bordered">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Staff Name</th>
                    <th>Leave Type</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Days</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Apply Leave Modal -->
<div class="modal fade" id="applyLeaveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Apply Leave</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="applyLeaveForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Staff Member <span class="text-danger">*</span></label>
                        <select class="form-select" name="staff_id" required id="staffSelect">
                            <option value="">Select Staff</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Leave Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="leave_type" required>
                            <option value="">Select Type</option>
                            <option value="Sick Leave">Sick Leave</option>
                            <option value="Casual Leave">Casual Leave</option>
                            <option value="Earned Leave">Earned Leave</option>
                            <option value="Maternity Leave">Maternity Leave</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">From Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="from_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">To Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="to_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Leave</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Leave Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="leave_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" id="leave_status" required>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin Remarks</label>
                        <textarea class="form-control" name="admin_remarks" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
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
    // Load staff for dropdown
    loadStaff();
    
    // Initialize DataTable
    const table = $('#leaveTable').DataTable({
        ajax: {
            url: '{{ route("admin.staff.leave.data") }}',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { 
                data: 'staff',
                render: function(data) {
                    return data ? data.first_name + ' ' + data.last_name : 'N/A';
                }
            },
            { data: 'leave_type' },
            { 
                data: 'from_date',
                render: function(data) {
                    return new Date(data).toLocaleDateString('en-GB');
                }
            },
            { 
                data: 'to_date',
                render: function(data) {
                    return new Date(data).toLocaleDateString('en-GB');
                }
            },
            { data: 'total_days' },
            { data: 'reason' },
            { 
                data: 'status',
                render: function(data) {
                    let className = 'status-pending';
                    if (data === 'Approved') className = 'status-approved';
                    if (data === 'Rejected') className = 'status-rejected';
                    return `<span class="badge ${className}">${data}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-info btn-status" data-id="${row.id}" data-status="${row.status}">
                            <i class="fas fa-edit"></i> Update
                        </button>
                    `;
                }
            }
        ],
        order: [[0, 'desc']]
    });
    
    function loadStaff() {
        $.ajax({
            url: '{{ route("admin.staff.data") }}',
            type: 'GET',
            success: function(response) {
                let options = '<option value="">Select Staff</option>';
                response.data.forEach(function(staff) {
                    options += `<option value="${staff.id}">${staff.first_name} ${staff.last_name} (${staff.employee_id})</option>`;
                });
                $('#staffSelect').html(options);
            }
        });
    }
    
    // Apply leave
    $('#applyLeaveForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("admin.staff.leave.apply") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                $('#applyLeaveModal').modal('hide');
                $('#applyLeaveForm')[0].reset();
                table.ajax.reload();
            },
            error: function(xhr) {
                let message = 'Something went wrong!';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                Swal.fire('Error!', message, 'error');
            }
        });
    });
    
    // Update status
    $(document).on('click', '.btn-status', function() {
        const id = $(this).data('id');
        const status = $(this).data('status');
        
        $('#leave_id').val(id);
        $('#leave_status').val(status);
        $('#statusModal').modal('show');
    });
    
    $('#statusForm').submit(function(e) {
        e.preventDefault();
        
        const id = $('#leave_id').val();
        
        $.ajax({
            url: `/admin/staff/leave/${id}/status`,
            type: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                $('#statusModal').modal('hide');
                table.ajax.reload();
            },
            error: function() {
                Swal.fire('Error!', 'Failed to update status', 'error');
            }
        });
    });
});
</script>
@endpush
