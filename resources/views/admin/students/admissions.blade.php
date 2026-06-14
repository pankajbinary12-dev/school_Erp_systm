@extends('admin.layouts.horizontal')

@section('title', 'All Admissions')

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
    
    .student-photo-thumb {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ddd;
    }
    
    .student-avatar-thumb {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
    }
    
    .doc-preview-img {
        max-width: 100px;
        max-height: 100px;
        border-radius: 5px;
        border: 2px solid #ddd;
        margin-top: 5px;
    }
    
    .doc-preview-link {
        display: inline-block;
        margin-top: 5px;
        font-size: 12px;
    }
</style>
@endpush

@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-clipboard-list me-2"></i>All Student Admissions</h5>
        <div>
            <a href="{{ route('admin.students.admission') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>New Admission
            </a>
        </div>
    </div>
    
    <div class="table-responsive p-3">
        <table id="admissionsTable" class="table table-hover table-bordered">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Photo</th>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Father Name</th>
                    <th>Phone</th>
                    <th>Admission Date</th>
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Student Admission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_admission_id" name="admission_id">
                
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <!-- Student Basic Information -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2"><i class="fas fa-user me-2"></i>Student Basic Information</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Student Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="student_name" id="edit_student_name" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="dob" id="edit_dob" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select class="form-select" name="gender" id="edit_gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="student_email" id="edit_student_email" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Blood Group</label>
                                <select class="form-select" name="blood_group" id="edit_blood_group">
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
                                <input type="text" class="form-control" name="religion" id="edit_religion">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Caste</label>
                                <input type="text" class="form-control" name="caste" id="edit_caste">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Nationality</label>
                                <input type="text" class="form-control" name="nationality" id="edit_nationality">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" id="edit_phone">
                            </div>
                        </div>
                    </div>

                    <!-- Class & Admission -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2"><i class="fas fa-school me-2"></i>Class & Admission</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Class</label>
                                <select class="form-select" name="class_id" id="edit_class_id">
                                    <option value="">Select Class</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Section</label>
                                <select class="form-select" name="section_id" id="edit_section_id">
                                    <option value="">Select Section</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="admission_date" id="edit_admission_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Previous School</label>
                                <input type="text" class="form-control" name="previous_school" id="edit_previous_school">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">TC Number</label>
                                <input type="text" class="form-control" name="tc_number" id="edit_tc_number">
                            </div>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2"><i class="fas fa-map-marker-alt me-2"></i>Address</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Current Address</label>
                                <textarea class="form-control" name="stu_address" id="edit_stu_address" rows="2"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Permanent Address</label>
                                <textarea class="form-control" name="permanent_address" id="edit_permanent_address" rows="2"></textarea>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name="city_name" id="edit_city_name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">State</label>
                                <input type="text" class="form-control" name="state_name" id="edit_state_name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">PIN Code</label>
                                <input type="text" class="form-control" name="pin_code" id="edit_pin_code">
                            </div>
                        </div>
                    </div>

                    <!-- Father Information -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2"><i class="fas fa-male me-2"></i>Father Information</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Father Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="father_name" id="edit_father_name" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Father Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="father_phone" id="edit_father_phone" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Father Email</label>
                                <input type="email" class="form-control" name="father_email" id="edit_father_email">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Father Occupation</label>
                                <input type="text" class="form-control" name="father_occupation" id="edit_father_occupation">
                            </div>
                        </div>
                    </div>

                    <!-- Mother Information -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2"><i class="fas fa-female me-2"></i>Mother Information</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Mother Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="mother_name" id="edit_mother_name" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Mother Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="mother_phone" id="edit_mother_phone" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Mother Email</label>
                                <input type="email" class="form-control" name="mother_email" id="edit_mother_email">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Mother Occupation</label>
                                <input type="text" class="form-control" name="mother_occupation" id="edit_mother_occupation">
                            </div>
                        </div>
                    </div>

                    <!-- Guardian & Emergency -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2"><i class="fas fa-user-shield me-2"></i>Guardian & Emergency</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Guardian Name</label>
                                <input type="text" class="form-control" name="guardian_name" id="edit_guardian_name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Guardian Phone</label>
                                <input type="text" class="form-control" name="guardian_phone" id="edit_guardian_phone">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Emergency Contact</label>
                                <input type="text" class="form-control" name="emergency_contact" id="edit_emergency_contact">
                            </div>
                        </div>
                    </div>

                    <!-- Medical Info -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2"><i class="fas fa-heartbeat me-2"></i>Medical Information</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Medical Information</label>
                                <textarea class="form-control" name="medical_info" id="edit_medical_info" rows="2"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Allergies</label>
                                <textarea class="form-control" name="allergies" id="edit_allergies" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Documents & Photos -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2"><i class="fas fa-images me-2"></i>Documents & Photos</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Student Photo</label>
                                <input type="file" class="form-control" name="student_photo" id="edit_student_photo" accept="image/*">
                                <div id="current_student_photo_preview" class="mt-2"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Father Photo</label>
                                <input type="file" class="form-control" name="father_photo" id="edit_father_photo" accept="image/*">
                                <div id="current_father_photo_preview" class="mt-2"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Mother Photo</label>
                                <input type="file" class="form-control" name="mother_photo" id="edit_mother_photo" accept="image/*">
                                <div id="current_mother_photo_preview" class="mt-2"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Birth Certificate</label>
                                <input type="file" class="form-control" name="birth_certificate" id="edit_birth_certificate" accept="image/*,application/pdf">
                                <div id="current_birth_certificate_preview" class="mt-2"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Aadhar Card (Front)</label>
                                <input type="file" class="form-control" name="aadhar_card_front" id="edit_aadhar_card_front" accept="image/*,application/pdf">
                                <div id="current_aadhar_front_preview" class="mt-2"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Aadhar Card (Back)</label>
                                <input type="file" class="form-control" name="aadhar_card_back" id="edit_aadhar_card_back" accept="image/*,application/pdf">
                                <div id="current_aadhar_back_preview" class="mt-2"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="status" id="edit_status" value="1">
                            <label class="form-check-label" for="edit_status">
                                Active Status
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Admission
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
    const table = $('#admissionsTable').DataTable({
        ajax: {
            url: '{{ route("admin.students.admissions.data") }}',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { 
                data: 'student_photo',
                orderable: false,
                render: function(data, type, row) {
                    if (data) {
                        return `<img src="/storage/${data}" alt="${row.student_name}" class="student-photo-thumb">`;
                    } else {
                        const initial = row.student_name.charAt(0).toUpperCase();
                        return `<div class="student-avatar-thumb">${initial}</div>`;
                    }
                }
            },
            { data: 'student_name' },
            { data: 'student_email' },
            { 
                data: 'class',
                render: function(data) {
                    return data ? data.class_name : 'N/A';
                }
            },
            { 
                data: 'section',
                render: function(data) {
                    return data ? data.section_name : 'N/A';
                }
            },
            { data: 'father_name' },
            { data: 'father_phone' },
            { 
                data: 'admission_date',
                render: function(data) {
                    return new Date(data).toLocaleDateString('en-GB');
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    return data ? 
                        '<span class="status-badge status-active">Active</span>' : 
                        '<span class="status-badge status-inactive">Inactive</span>';
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

    // Load classes for edit modal
    loadClassesForEdit();

    // Edit button click
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/admin/students/admissions/${id}/edit`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    
                    // Fill form fields
                    $('#edit_admission_id').val(data.id);
                    $('#edit_student_name').val(data.student_name);
                    $('#edit_dob').val(data.dob ? data.dob.split('T')[0] : '');
                    $('#edit_gender').val(data.gender);
                    $('#edit_student_email').val(data.student_email);
                    $('#edit_blood_group').val(data.blood_group);
                    $('#edit_religion').val(data.religion);
                    $('#edit_caste').val(data.caste);
                    $('#edit_nationality').val(data.nationality);
                    $('#edit_phone').val(data.phone);
                    $('#edit_class_id').val(data.class_id);
                    $('#edit_section_id').val(data.section_id);
                    $('#edit_admission_date').val(data.admission_date ? data.admission_date.split('T')[0] : '');
                    $('#edit_previous_school').val(data.previous_school);
                    $('#edit_tc_number').val(data.tc_number);
                    $('#edit_stu_address').val(data.stu_address);
                    $('#edit_permanent_address').val(data.permanent_address);
                    $('#edit_city_name').val(data.city_name);
                    $('#edit_state_name').val(data.state_name);
                    $('#edit_pin_code').val(data.pin_code);
                    $('#edit_father_name').val(data.father_name);
                    $('#edit_father_phone').val(data.father_phone);
                    $('#edit_father_email').val(data.father_email);
                    $('#edit_father_occupation').val(data.father_occupation);
                    $('#edit_mother_name').val(data.mother_name);
                    $('#edit_mother_phone').val(data.mother_phone);
                    $('#edit_mother_email').val(data.mother_email);
                    $('#edit_mother_occupation').val(data.mother_occupation);
                    $('#edit_guardian_name').val(data.guardian_name);
                    $('#edit_guardian_phone').val(data.guardian_phone);
                    $('#edit_emergency_contact').val(data.emergency_contact);
                    $('#edit_medical_info').val(data.medical_info);
                    $('#edit_allergies').val(data.allergies);
                    $('#edit_status').prop('checked', data.status);
                    
                    // Load sections if class is selected
                    if (data.class_id) {
                        loadSectionsForEdit(data.class_id, data.section_id);
                    }
                    
                    $('#editModal').modal('show');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Failed to load admission data', 'error');
            }
        });
    });

    // Update form submission
    $('#editForm').submit(function(e) {
        e.preventDefault();
        
        const id = $('#edit_admission_id').val();
        const formData = new FormData(this);
        
        Swal.fire({
            title: 'Updating...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: `/admin/students/admissions/${id}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    timer: 2000
                });
                $('#editModal').modal('hide');
                table.ajax.reload();
            },
            error: function(xhr) {
                let message = 'Something went wrong!';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    message = errors.join('<br>');
                }
                Swal.fire('Error!', message, 'error');
            }
        });
    });

    // Delete button click
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "This admission will be moved to trash!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/students/admissions/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire('Deleted!', response.message, 'success');
                        table.ajax.reload();
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to delete admission', 'error');
                    }
                });
            }
        });
    });

    // Class change in edit modal
    $('#edit_class_id').change(function() {
        const classId = $(this).val();
        if (classId) {
            loadSectionsForEdit(classId);
        } else {
            $('#edit_section_id').html('<option value="">Select Section</option>');
        }
    });
});

function loadClassesForEdit() {
    $.ajax({
        url: '{{ route("admin.get.classes") }}',
        type: 'GET',
        success: function(response) {
            let options = '<option value="">Select Class</option>';
            response.data.forEach(function(cls) {
                options += `<option value="${cls.id}">${cls.class_name}</option>`;
            });
            $('#edit_class_id').html(options);
        }
    });
}

function loadSectionsForEdit(classId, selectedSection = null) {
    $.ajax({
        url: `/admin/get-sections/${classId}`,
        type: 'GET',
        success: function(response) {
            let options = '<option value="">Select Section</option>';
            response.data.forEach(function(section) {
                const selected = selectedSection == section.id ? 'selected' : '';
                options += `<option value="${section.id}" ${selected}>${section.section_name}</option>`;
            });
            $('#edit_section_id').html(options);
        }
    });
}
</script>
@endpush
