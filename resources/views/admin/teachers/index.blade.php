@extends('admin.layouts.app')

@section('title', 'Teachers Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-chalkboard-teacher me-2"></i>Teachers Management</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="teachersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Assigned Classes</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="teachersTableBody">
                        <tr>
                            <td colspan="7" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadTeachers();
});

function loadTeachers() {
    const tbody = document.getElementById('teachersTableBody');
    tbody.innerHTML = '<tr><td colspan="7" class="text-center"><div class="spinner-border spinner-border-sm"></div> Loading...</td></tr>';
    
    fetch('{{ route('admin.teachers.data') }}', {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderTeachers(data.data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading teachers</td></tr>';
    });
}

function renderTeachers(teachers) {
    const tbody = document.getElementById('teachersTableBody');
    tbody.innerHTML = '';
    
    if (teachers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No teachers found</td></tr>';
        return;
    }

    teachers.forEach(teacher => {
        const statusBadge = teacher.status === 'Active' ? 'success' : 'secondary';
        const assignedCount = teacher.assigned_count || 0;
        
        const row = `
            <tr>
                <td>${teacher.employee_id}</td>
                <td>${teacher.first_name} ${teacher.last_name}</td>
                <td>${teacher.email}</td>
                <td>${teacher.phone}</td>
                <td><span class="badge bg-info">${assignedCount} Classes</span></td>
                <td><span class="badge bg-${statusBadge}">${teacher.status}</span></td>
                <td>
                    <a href="/admin/teachers/${teacher.id}/assign-classes" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Assign Classes
                    </a>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}
</script>
@endsection
