@extends('admin.layouts.horizontal')

@section('title', 'Book Issue & Return')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .status-issued { background: #fff3cd; color: #856404; }
    .status-returned { background: #d4edda; color: #155724; }
    .status-overdue { background: #f8d7da; color: #721c24; }
</style>
@endpush

@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-book-reader me-2"></i>Book Issue & Return</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#issueModal" onclick="resetForm()">
            <i class="fas fa-plus me-1"></i>Issue Book
        </button>
    </div>
    
    <div class="table-responsive p-3">
        <table id="issuesTable" class="table table-hover table-bordered">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Book</th>
                    <th>Member Type</th>
                    <th>Member Name</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Fine</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Issue Book Modal -->
<div class="modal fade" id="issueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Issue Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="issueForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Book <span class="text-danger">*</span></label>
                        <select class="form-select" name="book_id" id="book_id" required>
                            <option value="">Select Book</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Member Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="member_type" id="member_type" required>
                            <option value="">Select Type</option>
                            <option value="Student">Student</option>
                            <option value="Teacher">Teacher</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Member <span class="text-danger">*</span></label>
                        <select class="form-select" name="member_id" id="member_id" required disabled>
                            <option value="">Select Member Type First</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Issue Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="issue_date" id="issue_date" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="due_date" id="due_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" name="remarks" id="remarks" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Issue Book</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Return Book Modal -->
<div class="modal fade" id="returnModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Return Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="returnForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="issue_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Return Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="return_date" id="return_date" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fine Amount</label>
                        <input type="number" class="form-control" name="fine_amount" id="fine_amount" step="0.01" min="0" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" name="remarks" id="return_remarks" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" id="returnBtn">Return Book</button>
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
    loadAvailableBooks();
    
    const table = $('#issuesTable').DataTable({
        ajax: {
            url: '{{ route("admin.library.issue.data") }}',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { 
                data: 'book',
                render: function(data) {
                    return data ? data.title : 'N/A';
                }
            },
            { data: 'member_type' },
            { data: 'member_name' },
            { 
                data: 'issue_date',
                render: function(data) {
                    return new Date(data).toLocaleDateString('en-GB');
                }
            },
            { 
                data: 'due_date',
                render: function(data) {
                    return new Date(data).toLocaleDateString('en-GB');
                }
            },
            { 
                data: 'return_date',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString('en-GB') : '-';
                }
            },
            { 
                data: 'fine_amount',
                render: function(data) {
                    return '₹' + parseFloat(data).toFixed(2);
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    let className = 'status-issued';
                    if (data === 'Returned') className = 'status-returned';
                    if (data === 'Overdue') className = 'status-overdue';
                    return `<span class="badge ${className}">${data}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    if (row.status === 'Issued' || row.status === 'Overdue') {
                        return `
                            <button class="btn btn-sm btn-success btn-return" data-id="${row.id}">
                                <i class="fas fa-undo"></i> Return
                            </button>
                        `;
                    }
                    return '<span class="text-muted">Returned</span>';
                }
            }
        ],
        order: [[0, 'desc']]
    });
    
    function loadAvailableBooks() {
        $.ajax({
            url: '{{ route("admin.library.available.books") }}',
            type: 'GET',
            success: function(response) {
                let options = '<option value="">Select Book</option>';
                response.data.forEach(function(book) {
                    options += `<option value="${book.id}">${book.title} - ${book.author} (Available: ${book.available_quantity})</option>`;
                });
                $('#book_id').html(options);
            }
        });
    }
    
    // Load members based on type
    $('#member_type').change(function() {
        const type = $(this).val();
        if (!type) {
            $('#member_id').prop('disabled', true).html('<option value="">Select Member Type First</option>');
            return;
        }
        
        $.ajax({
            url: '{{ route("admin.library.members") }}',
            type: 'GET',
            data: { type: type },
            success: function(response) {
                let options = '<option value="">Select Member</option>';
                response.data.forEach(function(member) {
                    options += `<option value="${member.id}">${member.first_name} ${member.last_name} (${member.code})</option>`;
                });
                $('#member_id').prop('disabled', false).html(options);
            }
        });
    });
    
    // Issue Book
    $('#issueForm').submit(function(e) {
        e.preventDefault();
        
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Issuing...');
        
        $.ajax({
            url: '{{ route("admin.library.issue.store") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                $('#issueModal').modal('hide');
                $('#issueForm')[0].reset();
                table.ajax.reload();
                loadAvailableBooks();
            },
            error: function(xhr) {
                let message = 'Something went wrong!';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                Swal.fire('Error!', message, 'error');
            },
            complete: function() {
                $('#submitBtn').prop('disabled', false).html('Issue Book');
            }
        });
    });
    
    // Return Book
    $(document).on('click', '.btn-return', function() {
        const id = $(this).data('id');
        $('#issue_id').val(id);
        $('#returnModal').modal('show');
    });
    
    $('#returnForm').submit(function(e) {
        e.preventDefault();
        
        const id = $('#issue_id').val();
        
        $('#returnBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        
        $.ajax({
            url: `/admin/library/return/${id}`,
            type: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                $('#returnModal').modal('hide');
                $('#returnForm')[0].reset();
                table.ajax.reload();
                loadAvailableBooks();
            },
            error: function() {
                Swal.fire('Error!', 'Failed to return book', 'error');
            },
            complete: function() {
                $('#returnBtn').prop('disabled', false).html('Return Book');
            }
        });
    });
});

function resetForm() {
    $('#issueForm')[0].reset();
    $('#member_id').prop('disabled', true).html('<option value="">Select Member Type First</option>');
    $('#issue_date').val('{{ date("Y-m-d") }}');
}
</script>
@endpush
