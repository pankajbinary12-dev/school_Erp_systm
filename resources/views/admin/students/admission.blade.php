@extends('admin.layouts.horizontal')

@section('title', 'Student Admission Form')

@push('styles')
<style>
    .form-section {
        background: white;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .section-title {
        color: #4e73df;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #4e73df;
    }
    
    .required-field::after {
        content: " *";
        color: red;
    }
    
    .preview-image {
        max-width: 150px;
        max-height: 150px;
        margin-top: 10px;
        border-radius: 8px;
        display: none;
    }
</style>
@endpush

@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-user-plus me-2"></i>Student Admission Form</h5>
        <a href="{{ route('admin.students.admissions') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-list me-1"></i>View All Admissions
        </a>
    </div>
    
    <form id="admissionForm" enctype="multipart/form-data">
        @csrf
        
        <!-- Student Basic Information -->
        <div class="form-section">
            <h6 class="section-title"><i class="fas fa-user me-2"></i>Student Basic Information</h6>
            <div class="row">

              <div class="col-md-4 mb-3">
                    <label class="form-label required-field">Admission Number</label>
                    <input type="text" class="form-control" name="admission_no" id="admissionNo" readonly 
                           style="background-color: #e9ecef; font-weight: 600; color: #6104f8ec;">
                    <small class="text-muted">Auto-generated based on session year</small>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label required-field">Student Name</label>
                    <input type="text" class="form-control" name="student_name" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label required-field">Date of Birth</label>
                    <input type="date" class="form-control" name="dob" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label required-field">Gender</label>
                    <select class="form-select" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label required-field">Email</label>
                    <input type="email" class="form-control" name="student_email" required>
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
                <div class="col-md-4 mb-3">
                    <label class="form-label">Religion</label>
                    <input type="text" class="form-control" name="religion">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Caste</label>
                    <input type="text" class="form-control" name="caste">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nationality</label>
                    <input type="text" class="form-control" name="nationality" value="Indian">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Student Photo</label>
                    <input type="file" class="form-control" name="student_photo" accept="image/*" onchange="previewImage(this, 'studentPhotoPreview')">
                    <img id="studentPhotoPreview" class="preview-image">
                </div>
            </div>
        </div>

        <!-- Class & Admission Information -->
        <div class="form-section">
            <h6 class="section-title"><i class="fas fa-school me-2"></i>Class & Admission Information</h6>
            <div class="row">
              
                <div class="col-md-4 mb-3">
                    <label class="form-label">Class</label>
                    <select class="form-select" name="class_id" id="classSelect">
                        <option value="">Select Class</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Section</label>
                    <select class="form-select" name="section_id" id="sectionSelect">
                        <option value="">Select Section</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label required-field">Admission Date</label>
                    <input type="date" class="form-control" name="admission_date" id="admissionDate" required value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Previous School</label>
                    <input type="text" class="form-control" name="previous_school">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Previous School Name</label>
                    <input type="text" class="form-control" name="previous_school_name">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Previous Class</label>
                    <input type="text" class="form-control" name="previous_class">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">TC Number</label>
                    <input type="text" class="form-control" name="tc_number">
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="form-section">
            <h6 class="section-title"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Current Address</label>
                    <textarea class="form-control" name="stu_address" rows="3"></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Permanent Address</label>
                    <textarea class="form-control" name="permanent_address" rows="3"></textarea>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="sameAddress">
                        <label class="form-check-label" for="sameAddress">
                            Same as Current Address
                        </label>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">City</label>
                    <input type="text" class="form-control" name="city_name">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">State</label>
                    <input type="text" class="form-control" name="state_name">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">PIN Code</label>
                    <input type="text" class="form-control" name="pin_code" maxlength="6">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" name="phone" maxlength="15">
                </div>
            </div>
        </div>

        <!-- Father Information -->
        <div class="form-section">
            <h6 class="section-title"><i class="fas fa-male me-2"></i>Father Information</h6>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label required-field">Father Name</label>
                    <input type="text" class="form-control" name="father_name" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label required-field">Father Phone</label>
                    <input type="text" class="form-control" name="father_phone" maxlength="15" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Father Email</label>
                    <input type="email" class="form-control" name="father_email">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Father Occupation</label>
                    <input type="text" class="form-control" name="father_occupation">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Father Photo</label>
                    <input type="file" class="form-control" name="father_photo" accept="image/*" onchange="previewImage(this, 'fatherPhotoPreview')">
                    <img id="fatherPhotoPreview" class="preview-image">
                </div>
            </div>
        </div>

        <!-- Mother Information -->
        <div class="form-section">
            <h6 class="section-title"><i class="fas fa-female me-2"></i>Mother Information</h6>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label required-field">Mother Name</label>
                    <input type="text" class="form-control" name="mother_name" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label required-field">Mother Phone</label>
                    <input type="text" class="form-control" name="mother_phone" maxlength="15" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Mother Email</label>
                    <input type="email" class="form-control" name="mother_email">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Mother Occupation</label>
                    <input type="text" class="form-control" name="mother_occupation">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Mother Photo</label>
                    <input type="file" class="form-control" name="mother_photo" accept="image/*" onchange="previewImage(this, 'motherPhotoPreview')">
                    <img id="motherPhotoPreview" class="preview-image">
                </div>
            </div>
        </div>

        <!-- Guardian & Emergency Contact -->
        <div class="form-section">
            <h6 class="section-title"><i class="fas fa-user-shield me-2"></i>Guardian & Emergency Contact</h6>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Guardian Name</label>
                    <input type="text" class="form-control" name="guardian_name">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Guardian Phone</label>
                    <input type="text" class="form-control" name="guardian_phone" maxlength="15">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Guardian Email</label>
                    <input type="email" class="form-control" name="guardian_email">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Relation</label>
                    <input type="text" class="form-control" name="relation">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Emergency Contact</label>
                    <input type="text" class="form-control" name="emergency_contact" maxlength="15">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Contact Phone</label>
                    <input type="text" class="form-control" name="contact_phone" maxlength="15">
                </div>
            </div>
        </div>

        <!-- Documents -->
        <div class="form-section">
            <h6 class="section-title"><i class="fas fa-file-alt me-2"></i>Documents</h6>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Birth Certificate</label>
                    <input type="file" class="form-control" name="birth_certificate" accept="image/*,application/pdf">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Aadhar Card (Front)</label>
                    <input type="file" class="form-control" name="aadhar_card_front" accept="image/*,application/pdf">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Aadhar Card (Back)</label>
                    <input type="file" class="form-control" name="aadhar_card_back" accept="image/*,application/pdf">
                </div>
            </div>
        </div>

        <!-- Medical Information -->
        <div class="form-section">
            <h6 class="section-title"><i class="fas fa-heartbeat me-2"></i>Medical Information</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Medical Information</label>
                    <textarea class="form-control" name="medical_info" rows="3" placeholder="Any medical conditions, medications, etc."></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Allergies</label>
                    <textarea class="form-control" name="allergies" rows="3" placeholder="Food allergies, drug allergies, etc."></textarea>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="form-section">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="status" id="statusCheck" value="Active" checked>
                        <label class="form-check-label" for="statusCheck">
                            Active Status
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="text-end">
            <button type="reset" class="btn btn-secondary">
                <i class="fas fa-redo me-1"></i>Reset
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Submit Admission
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Generate admission number on page load
    generateAdmissionNumber();
    
    // Regenerate admission number when admission date changes
    $('#admissionDate').change(function() {
        generateAdmissionNumber();
    });
    
    // Load classes
    loadClasses();
    
    // Load sections when class is selected
    $('#classSelect').change(function() {
        const classId = $(this).val();
        if (classId) {
            loadSections(classId);
        } else {
            $('#sectionSelect').html('<option value="">Select Section</option>');
        }
    });
    
    // Same address checkbox
    $('#sameAddress').change(function() {
        if ($(this).is(':checked')) {
            $('[name="permanent_address"]').val($('[name="stu_address"]').val());
        }
    });
    
    // Form submission
    $('#admissionForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        Swal.fire({
            title: 'Submitting...',
            text: 'Please wait while we process the admission',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: '{{ route("admin.students.admission.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    showConfirmButton: true
                }).then(() => {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        $('#admissionForm')[0].reset();
                        $('.preview-image').hide();
                    }
                });
            },
            error: function(xhr) {
                let message = 'Something went wrong!';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    message = errors.join('<br>');
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: message
                });
            }
        });
    });
});

function loadClasses() {
    $.ajax({
        url: '{{ route("admin.get.classes") }}',
        type: 'GET',
        success: function(response) {
            let options = '<option value="">Select Class</option>';
            response.data.forEach(function(cls) {
                options += `<option value="${cls.id}">${cls.class_name}</option>`;
            });
            $('#classSelect').html(options);
        }
    });
}

function loadSections(classId) {
    $.ajax({
        url: `/admin/get-sections/${classId}`,
        type: 'GET',
        success: function(response) {
            let options = '<option value="">Select Section</option>';
            response.data.forEach(function(section) {
                options += `<option value="${section.id}">${section.section_name}</option>`;
            });
            $('#sectionSelect').html(options);
        }
    });
}

function generateAdmissionNumber() {
    const admissionDate = $('#admissionDate').val();
    
    if (!admissionDate) {
        return;
    }
    
    // Extract year from admission date
    const year = new Date(admissionDate).getFullYear();
    
    // Show loading
    $('#admissionNo').val('Generating...');
    
    // Get next admission number from server
    $.ajax({
        url: '{{ route("admin.students.admission.generate-number") }}',
        type: 'GET',
        data: { year: year },
        success: function(response) {
            if (response.success) {
                $('#admissionNo').val(response.admission_no);
            } else {
                $('#admissionNo').val('Error');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to generate admission number'
                });
            }
        },
        error: function() {
            $('#admissionNo').val('Error');
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to generate admission number'
            });
        }
    });
}

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
