@extends('layouts.teacher')

@section('title', 'Student Attendance')

@section('content')
<div class="d-flex">
    @include('teacher.partials.sidebar')

    <div class="main-content flex-grow-1">
        @include('teacher.partials.navbar')

        <div class="content-area">
            <div class="container-fluid">
                <h4 class="mb-4"><i class="fas fa-user-check me-2"></i>Student Attendance</h4>

                <!-- Start Session Card -->
                <div class="card mb-4" id="startSessionCard">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Start Attendance Session</h5>
                    </div>
                    <div class="card-body">
                        <form id="startSessionForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Class</label>
                                    <select class="form-select" id="class_id" name="class_id" required>
                                        <option value="">Select Class</option>
                                        @foreach($assignments as $classId => $items)
                                            <option value="{{ $classId }}">{{ $items->first()->class_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Section</label>
                                    <select class="form-select" id="section_id" name="section_id" required>
                                        <option value="">Select Class First</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Subject</label>
                                    <select class="form-select" id="subject_id" name="subject_id" required>
                                        <option value="">Select Section First</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date</label>
                                    <input type="date" class="form-control" id="attendance_date" name="attendance_date" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">
                                <i class="fas fa-play me-1"></i>Start Session
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Active Session Card -->
                <div class="card d-none" id="activeSessionCard">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Active Session</h5>
                        <div>
                            <button class="btn btn-light btn-sm me-2" onclick="markAllPresent()">
                                <i class="fas fa-check-double"></i> Mark All Present
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="endSession()">
                                <i class="fas fa-stop"></i> End Session
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Session Info -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <strong>Session Details:</strong>
                                    <span id="sessionInfo"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3 id="presentCount">0</h3>
                                        <p class="mb-0">Present</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h3 id="absentCount">0</h3>
                                        <p class="mb-0">Absent</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h3 id="lateCount">0</h3>
                                        <p class="mb-0">Late</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h3 id="leaveCount">0</h3>
                                        <p class="mb-0">Leave</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Students List -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Roll No</th>
                                        <th>Student Name</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="studentsTableBody">
                                    <tr>
                                        <td colspan="4" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Sessions -->
                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Recent Sessions</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Class</th>
                                        <th>Section</th>
                                        <th>Subject</th>
                                        <th>Present</th>
                                        <th>Absent</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="recentSessionsBody">
                                    <tr>
                                        <td colspan="7" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentSessionId = null;
const assignments = @json($assignments);

document.addEventListener('DOMContentLoaded', function() {
    loadRecentSessions();
    
    // Class change handler
    document.getElementById('class_id').addEventListener('change', function() {
        const classId = this.value;
        const sectionSelect = document.getElementById('section_id');
        const subjectSelect = document.getElementById('subject_id');
        
        sectionSelect.innerHTML = '<option value="">Loading...</option>';
        subjectSelect.innerHTML = '<option value="">Select Section First</option>';
        sectionSelect.disabled = true;
        
        if (!classId) {
            sectionSelect.innerHTML = '<option value="">Select Class First</option>';
            sectionSelect.disabled = false;
            return;
        }
        
        // Load sections via AJAX
        fetch(`/teacher/get-sections/${classId}`, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(result => {
            sectionSelect.innerHTML = '<option value="">Select Section</option>';
            if (result.success && result.data && result.data.length > 0) {
                result.data.forEach(section => {
                    sectionSelect.innerHTML += `<option value="${section.id}">${section.section_name}</option>`;
                });
            } else {
                sectionSelect.innerHTML = '<option value="">No sections available</option>';
            }
            sectionSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error loading sections:', error);
            sectionSelect.innerHTML = '<option value="">Error loading sections</option>';
            sectionSelect.disabled = false;
        });
    });
    
    // Section change handler
    document.getElementById('section_id').addEventListener('change', function() {
        const classId = document.getElementById('class_id').value;
        const sectionId = this.value;
        const subjectSelect = document.getElementById('subject_id');
        
        subjectSelect.innerHTML = '<option value="">Select Subject</option>';
        
        if (!classId || !sectionId || !assignments[classId]) return;
        
        assignments[classId].forEach(item => {
            if (item.section_id == sectionId) {
                subjectSelect.innerHTML += `<option value="${item.subject_id}">${item.subject_name}</option>`;
            }
        });
    });
    
    // Start session form
    document.getElementById('startSessionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        startSession();
    });
});

function startSession() {
    const formData = new FormData(document.getElementById('startSessionForm'));
    const data = Object.fromEntries(formData);
    
    fetch('{{ route('teacher.student.attendance.start') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            currentSessionId = result.session_id;
            document.getElementById('startSessionCard').classList.add('d-none');
            document.getElementById('activeSessionCard').classList.remove('d-none');
            loadSessionStudents(result.session_id);
            alert(result.message);
        } else {
            alert(result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error starting session');
    });
}

function loadSessionStudents(sessionId) {
    fetch(`/teacher/student-attendance/get-students/${sessionId}`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                displaySessionInfo(result.session);
                renderStudents(result.students);
                updateCounts(result.session);
            }
        })
        .catch(error => console.error('Error:', error));
}

function displaySessionInfo(session) {
    document.getElementById('sessionInfo').innerHTML = `
        ${session.class.class_name} - ${session.section.section_name} | 
        ${session.subject.subject_name} | 
        Date: ${session.attendance_date}
    `;
}

function renderStudents(students) {
    const tbody = document.getElementById('studentsTableBody');
    tbody.innerHTML = '';
    
    students.forEach(student => {
        const statusClass = {
            'Present': 'success',
            'Absent': 'danger',
            'Late': 'warning',
            'Leave': 'info'
        }[student.status];
        
        const row = `
            <tr>
                <td>${student.roll_number}</td>
                <td>${student.name}</td>
                <td><span class="badge bg-${statusClass}">${student.status}</span></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-success" onclick="markAttendance(${student.id}, 'Present')">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-danger" onclick="markAttendance(${student.id}, 'Absent')">
                            <i class="fas fa-times"></i>
                        </button>
                        <button class="btn btn-warning" onclick="markAttendance(${student.id}, 'Late')">
                            <i class="fas fa-clock"></i>
                        </button>
                        <button class="btn btn-info" onclick="markAttendance(${student.id}, 'Leave')">
                            <i class="fas fa-calendar-times"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}

function markAttendance(attendanceId, status) {
    fetch('{{ route('teacher.student.attendance.mark') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            attendance_id: attendanceId,
            status: status
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            loadSessionStudents(currentSessionId);
            updateCountsFromResult(result.session_counts);
        }
    })
    .catch(error => console.error('Error:', error));
}

function markAllPresent() {
    if (!confirm('Mark all students as present?')) return;
    
    fetch('{{ route('teacher.student.attendance.mark.all') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            session_id: currentSessionId
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            loadSessionStudents(currentSessionId);
            updateCountsFromResult(result.session_counts);
            alert(result.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

function endSession() {
    if (!confirm('End this attendance session?')) return;
    
    fetch(`/teacher/student-attendance/end-session/${currentSessionId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(result.message);
            document.getElementById('activeSessionCard').classList.add('d-none');
            document.getElementById('startSessionCard').classList.remove('d-none');
            document.getElementById('startSessionForm').reset();
            currentSessionId = null;
            loadRecentSessions();
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateCounts(session) {
    document.getElementById('presentCount').textContent = session.present_count;
    document.getElementById('absentCount').textContent = session.absent_count;
    document.getElementById('lateCount').textContent = session.late_count;
    document.getElementById('leaveCount').textContent = session.leave_count;
}

function updateCountsFromResult(counts) {
    document.getElementById('presentCount').textContent = counts.present;
    document.getElementById('absentCount').textContent = counts.absent;
    document.getElementById('lateCount').textContent = counts.late;
    document.getElementById('leaveCount').textContent = counts.leave;
}

function loadRecentSessions() {
    fetch('{{ route('teacher.student.attendance.sessions') }}')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                renderRecentSessions(result.data);
            }
        })
        .catch(error => console.error('Error:', error));
}

function renderRecentSessions(sessions) {
    const tbody = document.getElementById('recentSessionsBody');
    tbody.innerHTML = '';
    
    if (sessions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No sessions yet</td></tr>';
        return;
    }
    
    sessions.slice(0, 10).forEach(session => {
        const statusBadge = session.status === 'Active' ? 'success' : 'secondary';
        const row = `
            <tr>
                <td>${new Date(session.attendance_date).toLocaleDateString()}</td>
                <td>${session.class.class_name}</td>
                <td>${session.section.section_name}</td>
                <td>${session.subject.subject_name}</td>
                <td><span class="badge bg-success">${session.present_count}</span></td>
                <td><span class="badge bg-danger">${session.absent_count}</span></td>
                <td><span class="badge bg-${statusBadge}">${session.status}</span></td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}
</script>
@endsection
