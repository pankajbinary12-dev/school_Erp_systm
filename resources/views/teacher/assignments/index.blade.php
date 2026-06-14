@extends('layouts.teacher')

@section('title', 'Assignments')

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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-tasks me-2"></i>My Assignments</h4>
                    <a href="{{ route('teacher.assignments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Create New Assignment
                    </a>
                </div>

                <!-- Assignments List -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="assignmentsTable">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th>Assigned Date</th>
                                        <th>Due Date</th>
                                        <th>Submissions</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="assignmentsTableBody">
                                    <tr>
                                        <td colspan="8" class="text-center">Loading...</td>
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
document.addEventListener('DOMContentLoaded', function() {
    loadAssignments();
});

function loadAssignments() {
    const tbody = document.getElementById('assignmentsTableBody');
    tbody.innerHTML = '<tr><td colspan="8" class="text-center"><div class="spinner-border spinner-border-sm"></div> Loading...</td></tr>';
    
    fetch('{{ route('teacher.assignments.data') }}', {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            renderAssignments(data.data);
        } else {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error: ' + (data.message || 'Failed to load assignments') + '</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error loading assignments. Please refresh the page.</td></tr>';
    });
}

function renderAssignments(assignments) {
    const tbody = document.getElementById('assignmentsTableBody');
    tbody.innerHTML = '';
    
    if (assignments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No assignments yet. Create your first assignment!</td></tr>';
        return;
    }

    assignments.forEach(assignment => {
        const statusBadge = assignment.status === 'Active' ? 'success' : 
                           assignment.status === 'Completed' ? 'info' : 'secondary';
        
        const row = `
            <tr>
                <td><strong>${assignment.title}</strong></td>
                <td>${assignment.class ? assignment.class.class_name : '-'}</td>
                <td>${assignment.subject ? assignment.subject.subject_name : '-'}</td>
                <td>${new Date(assignment.assigned_date).toLocaleDateString()}</td>
                <td>${new Date(assignment.due_date).toLocaleDateString()}</td>
                <td>
                    <span class="badge bg-primary">${assignment.submission_count || 0}/${assignment.total_students || 0}</span>
                    <small class="text-muted">(${assignment.submission_percentage || 0}%)</small>
                </td>
                <td><span class="badge bg-${statusBadge}">${assignment.status}</span></td>
                <td>
                    <a href="/teacher/assignments/${assignment.id}" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i>
                    </a>
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
    if (!confirm('Are you sure you want to delete this assignment?')) return;
    
    fetch(`/teacher/assignments/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Assignment deleted successfully');
            loadAssignments();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endsection
