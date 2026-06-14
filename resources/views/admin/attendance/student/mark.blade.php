@extends('admin.layouts.horizontal')
@section('title', 'Mark Attendance')
@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-user-check me-2"></i>Mark Student Attendance</h5>
    </div>
    <div class="content-card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <label class="form-label">Date *</label>
                <input type="date" id="attendance_date" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Class *</label>
                <select id="class_id" class="form-select">
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Section</label>
                <select id="section_id" class="form-select" disabled>
                    <option value="">Select Section</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="button" id="loadStudents" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Load Students
                </button>
            </div>
        </div>

        <div id="attendance-form" style="display: none;">
            <div class="alert alert-info">
                <strong>Date:</strong> <span id="selected-date"></span> | 
                <strong>Class:</strong> <span id="selected-class"></span>
            </div>

            <div class="mb-3">
                <button type="button" id="markAllPresent" class="btn btn-success">
                    <i class="fas fa-check-double"></i> Mark All Present
                </button>
                <button type="button" id="markAllAbsent" class="btn btn-danger">
                    <i class="fas fa-times"></i> Mark All Absent
                </button>
            </div>

            <form id="attendanceForm">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="10%">Roll No</th>
                                <th width="25%">Student Name</th>
                                <th width="15%">Admission No</th>
                                <th width="15%">Status</th>
                                <th width="30%">Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="students-tbody">
                        </tbody>
                    </table>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Save Attendance
                    </button>
                </div>
            </form>
        </div>

        <div id="loading" style="display: none;" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class_id');
    const sectionSelect = document.getElementById('section_id');
    const dateInput = document.getElementById('attendance_date');
    const loadBtn = document.getElementById('loadStudents');
    const attendanceForm = document.getElementById('attendance-form');
    const loading = document.getElementById('loading');

    // Load sections when class changes
    classSelect.addEventListener('change', function() {
        const classId = this.value;
        sectionSelect.innerHTML = '<option value="">All Sections</option>';
        sectionSelect.disabled = true;
        attendanceForm.style.display = 'none';

        if (classId) {
            fetch(`/admin/attendance/student/sections/${classId}`)
                .then(r => r.json())
                .then(data => {
                    data.sections.forEach(section => {
                        const option = document.createElement('option');
                        option.value = section.id;
                        option.textContent = section.section_name;
                        sectionSelect.appendChild(option);
                    });
                    sectionSelect.disabled = false;
                });
        }
    });

    // Load students
    loadBtn.addEventListener('click', function() {
        const classId = classSelect.value;
        const sectionId = sectionSelect.value;
        const date = dateInput.value;

        if (!classId || !date) {
            alert('Please select class and date');
            return;
        }

        loading.style.display = 'block';
        attendanceForm.style.display = 'none';

        fetch('/admin/attendance/student/get-students', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ class_id: classId, section_id: sectionId, attendance_date: date })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('selected-date').textContent = date;
                document.getElementById('selected-class').textContent = classSelect.options[classSelect.selectedIndex].text;

                const tbody = document.getElementById('students-tbody');
                tbody.innerHTML = '';

                data.students.forEach((student, index) => {
                    const existing = data.existingAttendance[student.id];
                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${student.roll_no || 'N/A'}</td>
                            <td>${student.name}</td>
                            <td>${student.admission_no}</td>
                            <td>
                                <select name="attendance[${student.id}][status]" class="form-select form-select-sm" required>
                                    <option value="Present" ${existing?.status === 'Present' ? 'selected' : ''}>Present</option>
                                    <option value="Absent" ${existing?.status === 'Absent' ? 'selected' : ''}>Absent</option>
                                    <option value="Leave" ${existing?.status === 'Leave' ? 'selected' : ''}>Leave</option>
                                    <option value="Late" ${existing?.status === 'Late' ? 'selected' : ''}>Late</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="attendance[${student.id}][remarks]" class="form-control form-control-sm" value="${existing?.remarks || ''}">
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });

                attendanceForm.style.display = 'block';
            }
        })
        .finally(() => loading.style.display = 'none');
    });

    // Mark all present
    document.getElementById('markAllPresent').addEventListener('click', function() {
        document.querySelectorAll('select[name*="[status]"]').forEach(select => {
            select.value = 'Present';
        });
    });

    // Mark all absent
    document.getElementById('markAllAbsent').addEventListener('click', function() {
        document.querySelectorAll('select[name*="[status]"]').forEach(select => {
            select.value = 'Absent';
        });
    });

    // Save attendance
    document.getElementById('attendanceForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const attendance = {};

        for (let [key, value] of formData.entries()) {
            const match = key.match(/attendance\[(\d+)\]\[(\w+)\]/);
            if (match) {
                const studentId = match[1];
                const field = match[2];
                if (!attendance[studentId]) attendance[studentId] = {};
                attendance[studentId][field] = value;
            }
        }

        loading.style.display = 'block';

        fetch('/admin/attendance/student/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                class_id: classSelect.value,
                section_id: sectionSelect.value,
                attendance_date: dateInput.value,
                attendance: attendance
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('Attendance saved successfully!');
                loadBtn.click(); // Reload to show updated data
            } else {
                alert('Error: ' + data.message);
            }
        })
        .finally(() => loading.style.display = 'none');
    });
});
</script>
@endpush
@endsection
