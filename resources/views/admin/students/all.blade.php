@extends('admin.layouts.horizontal')

@section('title', 'All Students')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .student-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-users me-2"></i>All Students</h5>
        <a href="/admin/students/add" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Student
        </a>
    </div>
    
    <div class="table-responsive">
        <table id="studentsTable" class="table table-hover">
            <thead>
                <tr>
                    <th>Admission No</th>
                    <th>Name</th>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Father Name</th>
                    <th>Mobile</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr id="student-row-{{ $student->id }}">
                    <td>{{ $student->admission_no }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($student->photo)
                                <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->first_name }}" 
                                     style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; margin-right: 10px;">
                            @else
                                <div class="student-avatar me-2">
                                    {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                            </div>
                        </div>
                    </td>
                    <td>{{ $student->class->class_name ?? 'N/A' }}</td>
                    <td>{{ $student->section->section_name ?? 'N/A' }}</td>
                    <td>{{ $student->father_name }}</td>
                    <td>{{ $student->guardian_phone }}</td>
                    <td>
                        <span class="badge bg-{{ $student->status == 'Active' ? 'success' : 'danger' }}">
                            {{ $student->status }}
                        </span>
                    </td>
                    <td>
                        <a href="/admin/students/view/{{ $student->id }}" class="btn btn-sm btn-info" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/admin/students/edit/{{ $student->id }}" class="btn btn-sm btn-warning" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="deleteStudent({{ $student->id }})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#studentsTable').DataTable({
            pageLength: 25,
            order: [[0, 'asc']]
        });
    });

    function deleteStudent(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/students/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        $('#student-row-' + id).fadeOut(500, function() {
                            $(this).remove();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to delete student'
                        });
                    }
                });
            }
        });
    }
</script>
@endpush
