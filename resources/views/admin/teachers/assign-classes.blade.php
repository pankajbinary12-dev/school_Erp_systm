@extends('admin.layouts.app')

@section('title', 'Assign Classes to Teacher')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-user-plus me-2"></i>Assign Classes to {{ $teacher->first_name }} {{ $teacher->last_name }}</h4>
        <a href="{{ route('admin.teachers') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Teachers
        </a>
    </div>

    <div class="row">
        <!-- Assign New Class Form -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Assign New Class</h5>
                </div>
                <div class="card-body">
                    <form id="assignClassForm">
                        <div class="mb-3">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select class="form-select" id="class_id" name="class_id" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Section <span class="text-danger">*</span></label>
                            <select class="form-select" id="section_id" name="section_id" required>
                                <option value="">Select Class First</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <select class="form-select" id="subject_id" name="subject_id" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-1"></i>Assign Class
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Assigned Classes List -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Assigned Classes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Section</th>
                                    <th>Subject</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="assignedClassesBody">
                                <tr>
                                    <td colspan="4" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const teacherId = {{ $teacher->id }};

document.addEventListener('DOMContentLoaded', function() {
    loadAssignedClasses();
    
    // Load sections when class is selected
    document.getElementById('class_id').addEventListener('change', function() {
        const classId = this.value;
        const sectionSelect = document.getElementById('section_id');
        
        if (!classId) {
            sectionSelect.innerHTML = '<option value="">Select Class First</option>';
            return;
        }
        
        sectionSelect.innerHTML = '<option value="">Loading...</option>';
        
        fetch(`/admin/get-sections/${classId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(result => {
                sectionSelect.innerHTML = '<option value="">Select Section</option>';
                const sections = result.data || result;
                if (sections.length === 0) {
                    sectionSelect.innerHTML = '<option value="">No sections available</option>';
                    return;
                }
                sections.forEach(section => {
                    sectionSelect.innerHTML += `<option value="${section.id}">${section.section_name}</option>`;
                });
            })
            .catch(error => {
                console.error('Error:', error);
                sectionSelect.innerHTML = '<option value="">Error loading sections</option>';
            });
    });
    
    // Handle form submission
    document.getElementById('assignClassForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        fetch(`/admin/teachers/${teacherId}/assign-classes`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                this.reset();
                loadAssignedClasses();
            } else {
                alert(data.message || 'Error assigning class');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error assigning class');
        });
    });
});

function loadAssignedClasses() {
    const tbody = document.getElementById('assignedClassesBody');
    tbody.innerHTML = '<tr><td colspan="4" class="text-center"><div class="spinner-border spinner-border-sm"></div> Loading...</td></tr>';
    
    fetch(`/admin/teachers/${teacherId}/assigned-classes`, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderAssignedClasses(data.data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error loading assigned classes</td></tr>';
    });
}

function renderAssignedClasses(assignments) {
    const tbody = document.getElementById('assignedClassesBody');
    tbody.innerHTML = '';
    
    if (assignments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">No classes assigned yet</td></tr>';
        return;
    }

    assignments.forEach(assignment => {
        const row = `
            <tr>
                <td>${assignment.class_name}</td>
                <td>${assignment.section_name}</td>
                <td>${assignment.subject_name}</td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="deleteAssignment(${assignment.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}

function deleteAssignment(id) {
    if (!confirm('Are you sure you want to remove this assignment?')) return;
    
    fetch(`/admin/teachers/assignments/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Assignment removed successfully');
            loadAssignedClasses();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endsection
