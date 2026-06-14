@extends('admin.layouts.horizontal')

@section('title', 'Add Student')

@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-user-plus me-2"></i>Add New Student</h5>
        <a href="/admin/students/all" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>
    
    <form id="addStudentForm" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Admission Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="admission_no" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">First Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="first_name" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="last_name" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="date_of_birth" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Gender <span class="text-danger">*</span></label>
                <select class="form-select" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Blood Group</label>
                <select class="form-select" name="blood_group">
                    <option value="">Select Blood Group</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Class <span class="text-danger">*</span></label>
                <select class="form-select" name="class_id" id="classSelect" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Section <span class="text-danger">*</span></label>
                <select class="form-select" name="section_id" id="sectionSelect" required>
                    <option value="">Select Section</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Session <span class="text-danger">*</span></label>
                <select class="form-select" name="session_id" required>
                    <option value="">Select Session</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}" {{ $session->is_active ? 'selected' : '' }}>
                            {{ $session->session_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Father Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="father_name" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Mother Name</label>
                <input type="text" class="form-control" name="mother_name">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                <input type="tel" class="form-control" name="guardian_phone" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Username <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control" name="password" required minlength="6">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="admission_date" required value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Photo</label>
                <input type="file" class="form-control" name="photo" accept="image/*">
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Address</label>
                <textarea class="form-control" name="address" rows="3"></textarea>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="Active" selected>Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Student
            </button>
            <a href="/admin/students/all" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Load sections when class is selected
    $('#classSelect').change(function() {
        const classId = $(this).val();
        if (classId) {
            $.get(`/admin/get-sections/${classId}`, function(response) {
                $('#sectionSelect').html('<option value="">Select Section</option>');
                response.data.forEach(section => {
                    $('#sectionSelect').append(`<option value="${section.id}">${section.section_name}</option>`);
                });
            });
        } else {
            $('#sectionSelect').html('<option value="">Select Section</option>');
        }
    });

    // Form submission
    $('#addStudentForm').submit(function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("admin.students.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = '/admin/students/all';
                });
            },
            error: function(xhr) {
                let message = 'Something went wrong!';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: message
                });
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
</script>
@endpush
