@extends('layouts.teacher')

@section('title', 'My Students')

@section('content')
<div class="d-flex">
    <!-- Sidebar -->
    @include('teacher.partials.sidebar')

    <!-- Main Content -->
    <div class="main-content flex-grow-1">
        <!-- Navbar -->
        @include('teacher.partials.navbar')

        <!-- Content -->
        <div class="content-area">
            <div class="container-fluid">
                <h4 class="mb-4"><i class="fas fa-users me-2"></i>My Students</h4>

                <!-- Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Select Class</label>
                                <select id="classFilter" class="form-select">
                                    <option value="">-- Select Class --</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Select Section</label>
                                <select id="sectionFilter" class="form-select">
                                    <option value="">-- All Sections --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label>
                                <button class="btn btn-primary d-block w-100" onclick="loadStudents()">
                                    <i class="fas fa-search me-1"></i>Load Students
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Students Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="studentsTable">
                                <thead>
                                    <tr>
                                        <th>Roll No</th>
                                        <th>Student Name</th>
                                        <th>Class</th>
                                        <th>Section</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="studentsTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center">Select class to view students</td>
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
// Load sections when class changes
document.getElementById('classFilter').addEventListener('change', function() {
    const classId = this.value;
    const sectionSelect = document.getElementById('sectionFilter');
    
    sectionSelect.innerHTML = '<option value="">Loading...</option>';
    sectionSelect.disabled = true;
    
    if (!classId) {
        sectionSelect.innerHTML = '<option value="">-- All Sections --</option>';
        sectionSelect.disabled = false;
        return;
    }
    
    // Fetch sections via AJAX
    fetch(`/teacher/get-sections/${classId}`, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        sectionSelect.innerHTML = '<option value="">-- All Sections --</option>';
        if (data.data && data.data.length > 0) {
            data.data.forEach(section => {
                sectionSelect.innerHTML += `<option value="${section.id}">${section.section_name}</option>`;
            });
        }
        sectionSelect.disabled = false;
    })
    .catch(error => {
        console.error('Error:', error);
        sectionSelect.innerHTML = '<option value="">-- All Sections --</option>';
        sectionSelect.disabled = false;
    });
});

function loadStudents() {
    const classId = document.getElementById('classFilter').value;
    const sectionId = document.getElementById('sectionFilter').value;
    
    if (!classId) {
        alert('Please select a class');
        return;
    }

    const tbody = document.getElementById('studentsTableBody');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center"><div class="spinner-border spinner-border-sm"></div> Loading...</td></tr>';

    fetch(`{{ route('teacher.students.data') }}?class_id=${classId}&section_id=${sectionId}`, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderStudents(data.data);
        } else {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading students</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading students</td></tr>';
    });
}

function renderStudents(students) {
    const tbody = document.getElementById('studentsTableBody');
    tbody.innerHTML = '';
    
    if (students.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No students found</td></tr>';
        return;
    }

    students.forEach(student => {
        const row = `
            <tr>
                <td>${student.roll_number || '-'}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(student.first_name + ' ' + student.last_name)}&size=32&background=667eea&color=fff" 
                             class="rounded-circle me-2" width="32" height="32">
                        <div>
                            <div>${student.first_name} ${student.last_name}</div>
                            <small class="text-muted">${student.admission_number || ''}</small>
                        </div>
                    </div>
                </td>
                <td>${student.class ? student.class.class_name : '-'}</td>
                <td>${student.section ? student.section.section_name : '-'}</td>
                <td>
                    ${student.email ? `<div><i class="fas fa-envelope text-muted"></i> ${student.email}</div>` : ''}
                    ${student.phone ? `<div><i class="fas fa-phone text-muted"></i> ${student.phone}</div>` : ''}
                </td>
                <td><span class="badge bg-success">Active</span></td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}
</script>
@endsection
