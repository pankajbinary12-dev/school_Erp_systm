@extends('admin.layouts.horizontal')

@section('title', 'All Staff')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .action-buttons .btn {
        padding: 5px 10px;
        font-size: 12px;
        margin: 0 2px;
    }
    
    .status-badge {
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 600;
    }
    
    .status-active {
        background: #d4edda;
        color: #155724;
    }
    
    .status-inactive {
        background: #f8d7da;
        color: #721c24;
    }
    
    .status-leave {
        background: #fff3cd;
        color: #856404;
    }
</style>
@endpush

@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-users me-2"></i>All Staff Members</h5>
        <a href="{{ route('admin.staff.add') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Add New Staff
        </a>
    </div>
    
    <div class="table-responsive p-3">
        <table id="staffTable" class="table table-hover table-bordered">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Designation</th>
                    <th>Department</th>
                    <th>Joining Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Staff Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_staff_id">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Employee ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="employee_id" id="edit_employee_id" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="first_name" id="edit_first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="last_name" id="edit_last_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="phone" id="edit_phone" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date_of_birth" id="edit_date_of_birth" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select" name="gender" id="edit_gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Designation <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="designation" id="edit_designation" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" name="department" id="edit_department">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Qualification</label>
                            <input type="text" class="form-control" name="qualification" id="edit_qualification">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Joining Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="joining_date" id="edit_joining_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Salary</label>
                            <input type="number" class="form-control" name="salary" id="edit_salary" step="0.01">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" id="edit_address" rows="2"></textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city" id="edit_city">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">State</label>
                            <input type="text" class="form-control" name="state" id="edit_state">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">PIN Code</label>
                            <input type="text" class="form-control" name="pin_code" id="edit_pin_code">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="edit_status">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="On Leave">On Leave</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Photo</label>
                            <input type="file" class="form-control" name="photo" accept="image/*">
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Staff
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
    // Initialize DataTable
    const table = $('#staffTable').DataTable({
        ajax: {
            url: '{{ route("admin.staff.data") }}',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { data: 'employee_id' },
            { 
                data: null,
                render: function(data) {
                    return data.first_name + ' ' + data.last_name;
                }
            },
            { data: 'email' },
            { data: 'phone' },
            { data: 'designation' },
            { data: 'department', defaultContent: 'N/A' },
            { 
                data: 'joining_date',
                render: function(data) {
                    return new Date(data).toLocaleDateString('en-GB');
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    let badgeClass = 'status-active';
                    if (data === 'Inactive') badgeClass = 'status-inactive';
                    if (data === 'On Leave') badgeClass = 'status-leave';
                    return `<span class="status-badge ${badgeClass}">${data}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-info btn-edit" data-id="${row.id}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true
    });

    // Edit button
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/admin/staff/${id}/edit`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#edit_staff_id').val(data.id);
                    $('#edit_employee_id').val(data.employee_id);
                    $('#edit_first_name').val(data.first_name);
                    $('#edit_last_name').val(data.last_name);
                    $('#edit_email').val(data.email);
                    $('#edit_phone').val(data.phone);
                    $('#edit_date_of_birth').val(data.date_of_birth);
                    $('#edit_gender').val(data.gender);
                    $('#edit_designation').val(data.designation);
                    $('#edit_department').val(data.department);
                    $('#edit_qualification').val(data.qualification);
                    $('#edit_joining_date').val(data.joining_date);
                    $('#edit_salary').val(data.salary);
                    $('#edit_address').val(data.address);
                    $('#edit_city').val(data.city);
                    $('#edit_state').val(data.state);
                    $('#edit_pin_code').val(data.pin_code);
                    $('#edit_status').val(data.status);
                    
                    $('#editModal').modal('show');
                }
            }
        });
    });

    // Update form
    $('#editForm').submit(function(e) {
        e.preventDefault();
        
        const id = $('#edit_staff_id').val();
        const formData = new FormData(this);
        
        Swal.fire({
            title: 'Updating...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
        
        $.ajax({
            url: `/admin/staff/${id}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                $('#editModal').modal('hide');
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

    // Delete button
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "This staff member will be moved to trash!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/staff/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire('Deleted!', response.message, 'success');
                        table.ajax.reload();
                    }
                });
            }
        });
    });
});
</script>
@endpush
