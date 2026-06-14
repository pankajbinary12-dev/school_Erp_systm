@extends('admin.layouts.horizontal')

@section('title', 'Edit Student')

@push('styles')
<style>
    .current-photo-preview {
        width: 120px;
        height: 120px;
        border-radius: 10px;
        object-fit: cover;
        border: 3px solid #ddd;
        margin-top: 10px;
    }
    .photo-preview-container {
        position: relative;
        display: inline-block;
    }
    .remove-photo-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        cursor: pointer;
        font-size: 12px;
    }
</style>
@endpush

@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-user-edit me-2"></i>Edit Student</h5>
        <a href="/admin/students/all" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>
    
    <form id="editStudentForm" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Admission Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="admission_no" value="{{ $student->admission_no }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">First Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="first_name" value="{{ $student->first_name }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="last_name" value="{{ $student->last_name }}" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="date_of_birth" value="{{ date('Y-m-d', strtotime($student->date_of_birth)) }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Gender <span class="text-danger">*</span></label>
                <select class="form-select" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male" {{ $student->gender == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ $student->gender == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ $student->gender == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Blood Group</label>
                <select class="form-select" name="blood_group">
                    <option value="">Select Blood Group</option>
                    <option value="A+" {{ $student->blood_group == 'A+' ? 'selected' : '' }}>A+</option>
                    <option value="A-" {{ $student->blood_group == 'A-' ? 'selected' : '' }}>A-</option>
                    <option value="B+" {{ $student->blood_group == 'B+' ? 'selected' : '' }}>B+</option>
                    <option value="B-" {{ $student->blood_group == 'B-' ? 'selected' : '' }}>B-</option>
                    <option value="O+" {{ $student->blood_group == 'O+' ? 'selected' : '' }}>O+</option>
                    <option value="O-" {{ $student->blood_group == 'O-' ? 'selected' : '' }}>O-</option>
                    <option value="AB+" {{ $student->blood_group == 'AB+' ? 'selected' : '' }}>AB+</option>
                    <option value="AB-" {{ $student->blood_group == 'AB-' ? 'selected' : '' }}>AB-</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Class <span class="text-danger">*</span></label>
                <select class="form-select" name="class_id" id="classSelect" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ $student->class_id == $class->id ? 'selected' : '' }}>
                            {{ $class->class_name }}
                        </option>
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
                        <option value="{{ $session->id }}" {{ $student->session_id == $session->id ? 'selected' : '' }}>
                            {{ $session->session_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Father Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="father_name" value="{{ $student->father_name }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Mother Name</label>
                <input type="text" class="form-control" name="mother_name" value="{{ $student->mother_name }}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                <input type="tel" class="form-control" name="guardian_phone" value="{{ $student->guardian_phone }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="{{ $student->email }}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Username <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="username" value="{{ $student->username }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Password <small class="text-muted">(Leave blank to keep current)</small></label>
                <input type="password" class="form-control" name="password" minlength="6">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="admission_date" value="{{ date('Y-m-d', strtotime($student->admission_date)) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Photo</label>
                <input type="file" class="form-control" name="photo" id="photoInput" accept="image/*">
                @if($student->photo)
                    <div class="photo-preview-container mt-2" id="currentPhotoContainer">
                        <img src="{{ asset('storage/' . $student->photo) }}" alt="Current Photo" class="current-photo-preview" id="currentPhoto">
                        <button type="button" class="remove-photo-btn" onclick="removeCurrentPhoto()" title="Remove Photo">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <input type="hidden" name="remove_photo" id="removePhotoFlag" value="0">
                @endif
                <div id="newPhotoPreview" class="mt-2" style="display: none;">
                    <img src="" alt="New Photo" class="current-photo-preview" id="newPhoto">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Address</label>
                <textarea class="form-control" name="address" rows="3">{{ $student->address }}</textarea>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="Active" {{ $student->status == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ $student->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Update Student
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
    // Load sections on page load
    $(document).ready(function() {
        const classId = $('#classSelect').val();
        if (classId) {
            loadSections(classId, {{ $student->section_id }});
        }
    });

    // Load sections when class is selected
    $('#classSelect').change(function() {
        const classId = $(this).val();
        if (classId) {
            loadSections(classId);
        } else {
            $('#sectionSelect').html('<option value="">Select Section</option>');
        }
    });

    function loadSections(classId, selectedSectionId = null) {
        $.get(`/admin/get-sections/${classId}`, function(response) {
            $('#sectionSelect').html('<option value="">Select Section</option>');
            response.data.forEach(section => {
                const selected = selectedSectionId == section.id ? 'selected' : '';
                $('#sectionSelect').append(`<option value="${section.id}" ${selected}>${section.section_name}</option>`);
            });
        });
    }

    // Photo preview on file select
    $('#photoInput').change(function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#newPhoto').attr('src', e.target.result);
                $('#newPhotoPreview').show();
                $('#currentPhotoContainer').hide();
            }
            reader.readAsDataURL(file);
        }
    });

    // Remove current photo
    function removeCurrentPhoto() {
        Swal.fire({
            title: 'Remove Photo?',
            text: "This will remove the current photo",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#currentPhotoContainer').hide();
                $('#removePhotoFlag').val('1');
            }
        });
    }

    // Form submission
    $('#editStudentForm').submit(function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Updating...');
        
        const formData = new FormData(this);
        formData.append('_method', 'PUT');
        
        $.ajax({
            url: '/admin/students/{{ $student->id }}',
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
