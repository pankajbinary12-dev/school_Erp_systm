@extends('admin.layouts.app')

@section('title', 'Generate ID Cards')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-id-card me-2"></i>Generate Student ID Cards</h4>
        <a href="{{ route('admin.id.cards.templates') }}" class="btn btn-secondary">
            <i class="fas fa-cog"></i> Manage Templates
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="generateForm">
                @csrf
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Select Class</label>
                        <select class="form-select" id="classSelect" required>
                            <option value="">Choose Class</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Select Section</label>
                        <select class="form-select" id="sectionSelect" required>
                            <option value="">Choose Section</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Select Template</label>
                        <select class="form-select" id="templateSelect" name="template_id" required>
                            <option value="">Choose Template</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="studentsTable">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Admission No</th>
                                <th>Name</th>
                                <th>Class</th>
                                <th>Roll No</th>
                            </tr>
                        </thead>
                        <tbody id="studentsBody">
                            <tr>
                                <td colspan="5" class="text-center">Select class and section to load students</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-print"></i> Generate ID Cards
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    loadClasses();
    loadTemplates();

    $('#classSelect').change(function() {
        const classId = $(this).val();
        if (classId) {
            loadSections(classId);
        }
    });

    $('#sectionSelect').change(function() {
        const classId = $('#classSelect').val();
        const sectionId = $(this).val();
        if (classId && sectionId) {
            loadStudents(classId, sectionId);
        }
    });

    $('#selectAll').change(function() {
        $('.student-checkbox').prop('checked', $(this).is(':checked'));
    });

    $('#generateForm').submit(function(e) {
        e.preventDefault();
        
        const selectedStudents = [];
        $('.student-checkbox:checked').each(function() {
            selectedStudents.push($(this).val());
        });

        if (selectedStudents.length === 0) {
            Swal.fire('Error!', 'Please select at least one student', 'error');
            return;
        }

        const templateId = $('#templateSelect').val();
        if (!templateId) {
            Swal.fire('Error!', 'Please select a template', 'error');
            return;
        }

        // Open in new window for printing
        const form = $('<form>', {
            'method': 'POST',
            'action': '{{ route("admin.id.cards.generate") }}',
            'target': '_blank'
        });

        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': '{{ csrf_token() }}'
        }));

        form.append($('<input>', {
            'type': 'hidden',
            'name': 'template_id',
            'value': templateId
        }));

        selectedStudents.forEach(id => {
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'student_ids[]',
                'value': id
            }));
        });

        $('body').append(form);
        form.submit();
        form.remove();
    });
});

function loadClasses() {
    $.get('/admin/get-active-classes', function(response) {
        if (response.success) {
            const select = $('#classSelect');
            select.empty().append('<option value="">Choose Class</option>');
            response.data.forEach(cls => {
                select.append(`<option value="${cls.id}">${cls.class_name}</option>`);
            });
        }
    });
}

function loadSections(classId) {
    $.get(`/admin/get-sections/${classId}`, function(response) {
        if (response.success) {
            const select = $('#sectionSelect');
            select.empty().append('<option value="">Choose Section</option>');
            response.data.forEach(section => {
                select.append(`<option value="${section.id}">${section.section_name}</option>`);
            });
        }
    });
}

function loadTemplates() {
    $.get('/admin/id-cards/templates/data', function(response) {
        if (response.success) {
            const select = $('#templateSelect');
            select.empty().append('<option value="">Choose Template</option>');
            response.data.forEach(template => {
                select.append(`<option value="${template.id}">${template.template_name}</option>`);
            });
        }
    });
}

function loadStudents(classId, sectionId) {
    $.get('/admin/students/data', {
        class_id: classId,
        section_id: sectionId
    }, function(response) {
        if (response.success) {
            displayStudents(response.data);
        }
    });
}

function displayStudents(students) {
    const tbody = $('#studentsBody');
    tbody.empty();

    if (students.length === 0) {
        tbody.html('<tr><td colspan="5" class="text-center">No students found</td></tr>');
        return;
    }

    students.forEach(student => {
        tbody.append(`
            <tr>
                <td><input type="checkbox" class="student-checkbox" value="${student.id}"></td>
                <td>${student.admission_no}</td>
                <td>${student.first_name} ${student.last_name}</td>
                <td>${student.class.class_name} - ${student.section.section_name}</td>
                <td>${student.roll_no || 'N/A'}</td>
            </tr>
        `);
    });
}
</script>
@endpush
