@extends('admin.layouts.horizontal')

@section('title', 'Add Staff Member')

@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-user-plus me-2"></i>Add New Staff Member</h5>
        <a href="{{ route('admin.staff.all') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-list me-1"></i>View All Staff
        </a>
    </div>
    
    <form id="addStaffForm" enctype="multipart/form-data" class="p-4">
        @csrf
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Employee ID <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="employee_id" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">First Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="first_name" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="last_name" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Phone <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="phone" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="date_of_birth" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Gender <span class="text-danger">*</span></label>
                <select class="form-select" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Designation <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="designation" required placeholder="e.g., Teacher, Principal, Clerk">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Department</label>
                <input type="text" class="form-control" name="department" placeholder="e.g., Science, Mathematics">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Qualification</label>
                <input type="text" class="form-control" name="qualification" placeholder="e.g., B.Ed, M.Sc">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Joining Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="joining_date" required value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Salary</label>
                <input type="number" class="form-control" name="salary" step="0.01" placeholder="Monthly salary">
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Address</label>
                <textarea class="form-control" name="address" rows="2"></textarea>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">City</label>
                <input type="text" class="form-control" name="city">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">State</label>
                <input type="text" class="form-control" name="state">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">PIN Code</label>
                <input type="text" class="form-control" name="pin_code" maxlength="6">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="Active" selected>Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="On Leave">On Leave</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Photo</label>
                <input type="file" class="form-control" name="photo" accept="image/*">
            </div>
        </div>

        <div class="text-end mt-3">
            <button type="reset" class="btn btn-secondary">
                <i class="fas fa-redo me-1"></i>Reset
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Add Staff Member
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#addStaffForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        Swal.fire({
            title: 'Submitting...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
        
        $.ajax({
            url: '{{ route("admin.staff.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message
                }).then(() => {
                    window.location.href = '{{ route("admin.staff.all") }}';
                });
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
});
</script>
@endpush
