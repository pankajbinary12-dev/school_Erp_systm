@extends('admin.layouts.horizontal')

@section('title', 'Trashed Admissions')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .action-buttons .btn {
        padding: 5px 10px;
        font-size: 12px;
        margin: 0 2px;
    }
    
    .deleted-row {
        background-color: #fff3cd;
    }
</style>
@endpush

@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-trash me-2"></i>Trashed Student Admissions</h5>
        <a href="{{ route('admin.students.admissions') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Back to Admissions
        </a>
    </div>
    
    <div class="table-responsive p-3">
        <table id="trashedTable" class="table table-hover table-bordered">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Father Name</th>
                    <th>Phone</th>
                    <th>Deleted At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
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
    // Initialize DataTable
    const table = $('#trashedTable').DataTable({
        ajax: {
            url: '{{ route("admin.students.admissions.trash.data") }}',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
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
                data: 'deleted_at',
                render: function(data) {
                    return new Date(data).toLocaleString('en-GB');
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-success btn-restore" data-id="${row.id}" title="Restore">
                                <i class="fas fa-undo"></i> Restore
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[7, 'desc']],
        pageLength: 25,
        responsive: true,
        rowCallback: function(row, data) {
            $(row).addClass('deleted-row');
        }
    });

    // Restore button click
    $(document).on('click', '.btn-restore', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "This admission will be restored!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, restore it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/students/admissions/${id}/restore`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Restored!',
                            text: response.message,
                            timer: 2000
                        });
                        table.ajax.reload();
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to restore admission', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
