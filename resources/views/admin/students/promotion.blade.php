@extends('admin.layouts.horizontal')

@section('title', 'Student Promotion')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .promotion-card {
        background: white;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    .filter-section {
        background: #f8f9fc;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .student-table {
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }
    .student-table thead {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
    }
    .student-table thead th {
        padding: 12px;
        font-weight: 600;
        border: none;
    }
    .student-table tbody tr:hover {
        background: #f8f9fc;
    }
    .promotion-badge {
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 13px;
    }
    .from-class {
        background: #e7f0ff;
        color: #4e73df;
    }
    .to-class {
        background: #d4edda;
        color: #155724;
    }
    .select-all-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-level-up-alt me-2"></i>Student Promotion</h5>
        <div>
            <button class="btn btn-success" id="promoteSelectedBtn" style="display: none;">
                <i class="fas fa-arrow-up me-2"></i>Promote Selected Students
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label"><strong>Current Session</strong></label>
                <select class="form-control" id="currentSession">
                    <option value="">Select Session</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}" {{ $session->is_active ? 'selected' : '' }}>
                            {{ $session->session_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label"><strong>From Class</strong></label>
                <select class="form-control" id="fromClass">
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label"><strong>From Section</strong></label>
                <select class="form-control" id="fromSection">
                    <option value="">Select Section</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button class="btn btn-primary w-100" id="searchBtn">
                    <i class="fas fa-search me-2"></i>Search Students
                </button>
            </div>
        </div>
    </div>

    <!-- Promotion Settings -->
    <div class="promotion-card" id="promotionSettings" style="display: none;">
        <h6 class="mb-3"><i class="fas fa-cog me-2"></i>Promotion Settings</h6>
        <div class="row">
            <div class="col-md-3">
                <label class="form-label"><strong>Promote To Session</strong></label>
                <select class="form-control" id="toSession">
                    <option value="">Select Session</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}">{{ $session->session_name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label"><strong>Promote To Class</strong></label>
                <select class="form-control" id="toClass">
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label"><strong>Promote To Section</strong></label>
                <select class="form-control" id="toSection">
                    <option value="">Select Section</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button class="btn btn-success w-100" id="applyPromotionBtn">
                    <i class="fas fa-check me-2"></i>Apply Settings
                </button>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div id="studentsTableContainer" style="display: none;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="fas fa-users me-2"></i>
                Students List (<span id="studentCount">0</span> students)
            </h6>
            <div class="form-check">
                <input type="checkbox" class="form-check-input select-all-checkbox" id="selectAll">
                <label class="form-check-label" for="selectAll">
                    <strong>Select All</strong>
                </label>
            </div>
        </div>

        <div class="student-table">
            <table class="table table-hover mb-0" id="studentsTable">
                <thead>
                    <tr>
                        <th style="width: 5%;">
                            <input type="checkbox" class="form-check-input" id="selectAllHeader">
                        </th>
                        <th style="width: 10%;">Roll No</th>
                        <th style="width: 15%;">Admission No</th>
                        <th style="width: 25%;">Student Name</th>
                        <th style="width: 15%;">Current Class</th>
                        <th style="width: 15%;">Promote To</th>
                        <th style="width: 15%;">Status</th>
                    </tr>
                </thead>
                <tbody id="studentsTableBody">
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    console.log('Promotion page loaded');
    console.log('jQuery version:', $.fn.jquery);
    
    let selectedStudents = [];
    let promotionSettings = {
        toSession: null,
        toClass: null,
        toSection: null
    };

    // Load sections when class changes
    $('#fromClass').change(function() {
        const classId = $(this).val();
        console.log('From Class changed:', classId);
        loadSections(classId, '#fromSection');
    });

    $('#toClass').change(function() {
        const classId = $(this).val();
        console.log('To Class changed:', classId);
        loadSections(classId, '#toSection');
    });

    // Search students
    $('#searchBtn').click(function() {
        searchStudents();
    });

    // Select all checkbox
    $('#selectAll, #selectAllHeader').change(function() {
        const isChecked = $(this).is(':checked');
        $('.student-checkbox').prop('checked', isChecked);
        updateSelectedStudents();
    });

    // Apply promotion settings
    $('#applyPromotionBtn').click(function() {
        applyPromotionSettings();
    });

    // Promote selected students
    $('#promoteSelectedBtn').click(function() {
        promoteStudents();
    });

    // Student checkbox change
    $(document).on('change', '.student-checkbox', function() {
        updateSelectedStudents();
    });
    
    console.log('All event handlers attached');
});

function loadSections(classId, targetSelect) {
    if (!classId) {
        $(targetSelect).html('<option value="">Select Section</option>');
        return;
    }

    console.log('Loading sections for class:', classId);
    console.log('Target select:', targetSelect);

    $.ajax({
        url: `/admin/get-sections/${classId}`,
        type: 'GET',
        success: function(response) {
            console.log('Sections response:', response);
            
            let options = '<option value="">Select Section</option>';
            
            // Handle both response formats
            let sections = [];
            if (response.data) {
                sections = response.data;
            } else if (Array.isArray(response)) {
                sections = response;
            } else if (response.success && response.data) {
                sections = response.data;
            }
            
            console.log('Sections array:', sections);
            
            if (sections.length === 0) {
                options += '<option value="">No sections available</option>';
            } else {
                sections.forEach(function(section) {
                    options += `<option value="${section.id}">${section.section_name}</option>`;
                });
            }
            
            $(targetSelect).html(options);
            console.log('Sections loaded successfully');
        },
        error: function(xhr, status, error) {
            console.error('Error loading sections:', error);
            console.error('XHR:', xhr);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to load sections: ' + error
            });
        }
    });
}

function searchStudents() {
    const sessionId = $('#currentSession').val();
    const classId = $('#fromClass').val();
    const sectionId = $('#fromSection').val();

    if (!sessionId || !classId || !sectionId) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Information',
            text: 'Please select session, class and section'
        });
        return;
    }

    $.ajax({
        url: '{{ route("admin.students.promotion.search") }}',
        type: 'GET',
        data: {
            session_id: sessionId,
            class_id: classId,
            section_id: sectionId
        },
        success: function(response) {
            if (response.students.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'No Students Found',
                    text: 'No students found in selected class and section'
                });
                return;
            }

            renderStudentsTable(response.students, response.fromClass, response.fromSection);
            $('#promotionSettings').show();
            $('#studentsTableContainer').show();
            $('#studentCount').text(response.students.length);
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to search students'
            });
        }
    });
}

function renderStudentsTable(students, fromClass, fromSection) {
    let html = '';
    students.forEach(function(student) {
        html += `
            <tr>
                <td>
                    <input type="checkbox" class="form-check-input student-checkbox" 
                           data-student-id="${student.id}" 
                           data-student-name="${student.first_name} ${student.last_name}">
                </td>
                <td>${student.roll_no || '-'}</td>
                <td><code>${student.admission_no}</code></td>
                <td>
                    <strong>${student.first_name} ${student.last_name}</strong>
                </td>
                <td>
                    <span class="promotion-badge from-class">
                        ${fromClass} - ${fromSection}
                    </span>
                </td>
                <td id="promote-to-${student.id}">
                    <span class="text-muted">Not set</span>
                </td>
                <td>
                    <span class="badge bg-primary">Ready</span>
                </td>
            </tr>
        `;
    });
    $('#studentsTableBody').html(html);
}

function applyPromotionSettings() {
    const toSession = $('#toSession').val();
    const toClass = $('#toClass').val();
    const toSection = $('#toSection').val();

    if (!toSession || !toClass || !toSection) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Information',
            text: 'Please select promotion session, class and section'
        });
        return;
    }

    promotionSettings = {
        toSession: toSession,
        toClass: toClass,
        toSection: toSection,
        toClassName: $('#toClass option:selected').text(),
        toSectionName: $('#toSection option:selected').text()
    };

    // Update all rows
    $('.student-checkbox').each(function() {
        const studentId = $(this).data('student-id');
        $(`#promote-to-${studentId}`).html(`
            <span class="promotion-badge to-class">
                ${promotionSettings.toClassName} - ${promotionSettings.toSectionName}
            </span>
        `);
    });

    Swal.fire({
        icon: 'success',
        title: 'Settings Applied!',
        text: 'Promotion settings applied to all students',
        timer: 2000,
        showConfirmButton: false
    });
}

function updateSelectedStudents() {
    selectedStudents = [];
    $('.student-checkbox:checked').each(function() {
        selectedStudents.push({
            id: $(this).data('student-id'),
            name: $(this).data('student-name')
        });
    });

    if (selectedStudents.length > 0) {
        $('#promoteSelectedBtn').show().text(`Promote ${selectedStudents.length} Student(s)`);
    } else {
        $('#promoteSelectedBtn').hide();
    }
}

function promoteStudents() {
    if (selectedStudents.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Students Selected',
            text: 'Please select students to promote'
        });
        return;
    }

    if (!promotionSettings.toSession || !promotionSettings.toClass || !promotionSettings.toSection) {
        Swal.fire({
            icon: 'warning',
            title: 'Promotion Settings Not Applied',
            text: 'Please apply promotion settings first'
        });
        return;
    }

    Swal.fire({
        title: 'Confirm Promotion',
        html: `
            <p>Are you sure you want to promote <strong>${selectedStudents.length} student(s)</strong>?</p>
            <p class="text-muted">To: ${promotionSettings.toClassName} - ${promotionSettings.toSectionName}</p>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1cc88a',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Promote!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            performPromotion();
        }
    });
}

function performPromotion() {
    const studentIds = selectedStudents.map(s => s.id);

    Swal.fire({
        title: 'Promoting Students...',
        html: 'Please wait while we promote the students',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '{{ route("admin.students.promotion.promote") }}',
        type: 'POST',
        data: {
            student_ids: studentIds,
            to_session_id: promotionSettings.toSession,
            to_class_id: promotionSettings.toClass,
            to_section_id: promotionSettings.toSection,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                html: `
                    <p><strong>${response.promoted_count} student(s)</strong> promoted successfully!</p>
                    <p class="text-muted">To: ${promotionSettings.toClassName} - ${promotionSettings.toSectionName}</p>
                `,
                confirmButtonColor: '#1cc88a'
            }).then(() => {
                // Refresh the search
                $('#searchBtn').click();
            });
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: xhr.responseJSON?.message || 'Failed to promote students'
            });
        }
    });
}
</script>
@endpush
