@extends('admin.layouts.horizontal')

@section('title', 'Books Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-book me-2"></i>Books Management</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bookModal" onclick="resetForm()">
            <i class="fas fa-plus me-1"></i>Add Book
        </button>
    </div>
    
    <div class="table-responsive p-3">
        <table id="booksTable" class="table table-hover table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Book No</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Available</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Book Modal -->
<div class="modal fade" id="bookModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bookForm">
                @csrf
                <input type="hidden" id="book_id">
                <input type="hidden" id="form_method" value="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Book No <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="book_no" id="book_no" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" id="title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category_id" id="category_id" required>
                                <option value="">Select Category</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Author <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="author" id="author" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Publisher</label>
                            <input type="text" class="form-control" name="publisher" id="publisher">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ISBN</label>
                            <input type="text" class="form-control" name="isbn" id="isbn">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Publication Year</label>
                            <input type="number" class="form-control" name="publication_year" id="publication_year" min="1900" max="{{ date('Y') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="quantity" id="quantity" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" class="form-control" name="price" id="price" step="0.01" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rack No</label>
                            <input type="text" class="form-control" name="rack_no" id="rack_no">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" name="status" id="status" required>
                                <option value="Available">Available</option>
                                <option value="Issued">Issued</option>
                                <option value="Lost">Lost</option>
                                <option value="Damaged">Damaged</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Book</button>
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
    loadCategories();
    
    const table = $('#booksTable').DataTable({
        ajax: {
            url: '{{ route("admin.library.books.data") }}',
            dataSrc: 'data'
        },
        columns: [
            { data: 'book_no' },
            { data: 'title' },
            { data: 'author' },
            { 
                data: 'category',
                render: function(data) {
                    return data ? data.category_name : 'N/A';
                }
            },
            { data: 'quantity' },
            { data: 'available_quantity' },
            { 
                data: 'status',
                render: function(data) {
                    const colors = {
                        'Available': 'success',
                        'Issued': 'warning',
                        'Lost': 'danger',
                        'Damaged': 'secondary'
                    };
                    return `<span class="badge bg-${colors[data]}">${data}</span>`;
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
    
    function loadCategories() {
        $.ajax({
            url: '{{ route("admin.library.categories.data") }}',
            type: 'GET',
            success: function(response) {
                let options = '<option value="">Select Category</option>';
                response.data.forEach(function(cat) {
                    if (cat.status === 'Active') {
                        options += `<option value="${cat.id}">${cat.category_name}</option>`;
                    }
                });
                $('#category_id').html(options);
            }
        });
    }
    
    // Add/Update Book
    $('#bookForm').submit(function(e) {
        e.preventDefault();
        
        const id = $('#book_id').val();
        const method = $('#form_method').val();
        const url = id ? `/admin/library/books/${id}` : '{{ route("admin.library.books.store") }}';
        
        const formData = $(this).serializeArray();
        const data = {};
        formData.forEach(item => {
            data[item.name] = item.value;
        });
        
        if (method === 'PUT') {
            data._method = 'PUT';
        }
        
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                $('#bookModal').modal('hide');
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
                $('#submitBtn').prop('disabled', false).html('Save Book');
            }
        });
    });
    
    // Edit Book
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        const row = table.rows().data().toArray().find(r => r.id === id);
        
        $('#book_id').val(row.id);
        $('#form_method').val('PUT');
        $('#book_no').val(row.book_no);
        $('#title').val(row.title);
        $('#category_id').val(row.category_id);
        $('#author').val(row.author);
        $('#publisher').val(row.publisher);
        $('#isbn').val(row.isbn);
        $('#publication_year').val(row.publication_year);
        $('#quantity').val(row.quantity);
        $('#price').val(row.price);
        $('#rack_no').val(row.rack_no);
        $('#description').val(row.description);
        $('#status').val(row.status);
        $('#modalTitle').text('Edit Book');
        $('#bookModal').modal('show');
    });
    
    // Delete Book
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
                    url: `/admin/library/books/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire('Deleted!', response.message, 'success');
                        table.ajax.reload();
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to delete book', 'error');
                    }
                });
            }
        });
    });
});

function resetForm() {
    $('#bookForm')[0].reset();
    $('#book_id').val('');
    $('#form_method').val('POST');
    $('#modalTitle').text('Add Book');
}
</script>
@endpush
