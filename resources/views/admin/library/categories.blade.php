@extends('admin.layouts.horizontal')

@section('title', 'Book Categories')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-list me-2"></i>Book Categories</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="resetForm()">
            <i class="fas fa-plus me-1"></i>Add Category
        </button>
    </div>
    
    <div class="table-responsive p-3">
        <table id="categoriesTable" class="table table-hover table-bordered">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Books Count</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm">
                @csrf
                <input type="hidden" id="category_id">
                <input type="hidden" id="form_method" value="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="category_name" id="category_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" id="status" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    const table = $('#categoriesTable').DataTable({
        ajax: {
            url: '{{ route("admin.library.categories.data") }}',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { data: 'category_name' },
            { data: 'description' },
            { data: 'books_count' },
            { 
                data: 'status',
                render: function(data) {
                    return data === 'Active' 
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-info btn-edit" data-id="${row.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    });
    
    // Add/Update Category
    $('#categoryForm').submit(function(e) {
        e.preventDefault();
        
        const id = $('#category_id').val();
        const method = $('#form_method').val();
        const url = id ? `/admin/library/categories/${id}` : '{{ route("admin.library.categories.store") }}';
        
        const formData = {
            _token: '{{ csrf_token() }}',
            category_name: $('#category_name').val(),
            description: $('#description').val(),
            status: $('#status').val()
        };
        
        if (method === 'PUT') {
            formData._method = 'PUT';
        }
        
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                $('#categoryModal').modal('hide');
                table.ajax.reload();
                resetForm();
            },
            error: function(xhr) {
                let message = 'Something went wrong!';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                Swal.fire('Error!', message, 'error');
            },
            complete: function() {
                $('#submitBtn').prop('disabled', false).html('Save Category');
            }
        });
    });
    
    // Edit Category
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        const row = table.rows().data().toArray().find(r => r.id === id);
        
        $('#category_id').val(row.id);
        $('#form_method').val('PUT');
        $('#category_name').val(row.category_name);
        $('#description').val(row.description);
        $('#status').val(row.status);
        $('#modalTitle').text('Edit Category');
        $('#categoryModal').modal('show');
    });
    
    // Delete Category
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        
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
                    url: `/admin/library/categories/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire('Deleted!', response.message, 'success');
                        table.ajax.reload();
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to delete category', 'error');
                    }
                });
            }
        });
    });
});

function resetForm() {
    $('#categoryForm')[0].reset();
    $('#category_id').val('');
    $('#form_method').val('POST');
    $('#modalTitle').text('Add Category');
}
</script>
@endpush
