@extends('admin.layouts.horizontal')
@section('title', 'Fee Structure')
@section('content')
<div class="content-card">
    <div class="content-card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-list me-2"></i>Fee Structure</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStructureModal">
            <i class="fas fa-plus"></i> Add Fee Structure
        </button>
    </div>
    <div class="content-card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Class</th>
                        <th>Fee Type</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Late Fee</th>
                        <th>Late Fee Days</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($structures as $structure)
                    <tr>
                        <td>{{ $structure->class->class_name }}</td>
                        <td>{{ $structure->feeType->name }}</td>
                        <td>₹{{ number_format($structure->amount, 2) }}</td>
                        <td>{{ $structure->due_date ? $structure->due_date->format('d M Y') : 'N/A' }}</td>
                        <td>₹{{ number_format($structure->late_fee_amount, 2) }}</td>
                        <td>{{ $structure->late_fee_days }} days</td>
                        <td>
                            <span class="badge bg-{{ $structure->status == 'active' ? 'success' : 'secondary' }}">
                                {{ strtoupper($structure->status) }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info edit-btn" 
                                data-id="{{ $structure->id }}"
                                data-class="{{ $structure->class_id }}"
                                data-feetype="{{ $structure->fee_type_id }}"
                                data-amount="{{ $structure->amount }}"
                                data-duedate="{{ $structure->due_date ? $structure->due_date->format('Y-m-d') : '' }}"
                                data-latefee="{{ $structure->late_fee_amount }}"
                                data-latefeedays="{{ $structure->late_fee_days }}"
                                data-status="{{ $structure->status }}"
                                title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No fee structures found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $structures->links() }}
        </div>
    </div>
</div>

<!-- Add Structure Modal -->
<div class="modal fade" id="addStructureModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Fee Structure</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.fees.structure.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Class <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-control" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Fee Type <span class="text-danger">*</span></label>
                        <select name="fee_type_id" class="form-control" required>
                            <option value="">Select Fee Type</option>
                            @foreach($feeTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Due Date</label>
                        <input type="date" name="due_date" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Late Fee Amount</label>
                        <input type="number" step="0.01" name="late_fee_amount" class="form-control" value="0">
                    </div>

                    <div class="mb-3">
                        <label>Late Fee Days</label>
                        <input type="number" name="late_fee_days" class="form-control" value="0">
                        <small class="text-muted">Days after due date when late fee applies</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Structure</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Structure Modal -->
<div class="modal fade" id="editStructureModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Fee Structure</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStructureForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Class <span class="text-danger">*</span></label>
                        <select name="class_id" id="edit_class_id" class="form-control" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Fee Type <span class="text-danger">*</span></label>
                        <select name="fee_type_id" id="edit_fee_type_id" class="form-control" required>
                            <option value="">Select Fee Type</option>
                            @foreach($feeTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" id="edit_amount" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Due Date</label>
                        <input type="date" name="due_date" id="edit_due_date" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Late Fee Amount</label>
                        <input type="number" step="0.01" name="late_fee_amount" id="edit_late_fee_amount" class="form-control" value="0">
                    </div>

                    <div class="mb-3">
                        <label>Late Fee Days</label>
                        <input type="number" name="late_fee_days" id="edit_late_fee_days" class="form-control" value="0">
                        <small class="text-muted">Days after due date when late fee applies</small>
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" id="edit_status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Structure</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Edit button click
    $('.edit-btn').click(function() {
        const id = $(this).data('id');
        const classId = $(this).data('class');
        const feeTypeId = $(this).data('feetype');
        const amount = $(this).data('amount');
        const dueDate = $(this).data('duedate');
        const lateFee = $(this).data('latefee');
        const lateFeeDays = $(this).data('latefeedays');
        const status = $(this).data('status');

        // Set form action
        $('#editStructureForm').attr('action', '/admin/fees/structure/' + id);

        // Fill form fields
        $('#edit_class_id').val(classId);
        $('#edit_fee_type_id').val(feeTypeId);
        $('#edit_amount').val(amount);
        $('#edit_due_date').val(dueDate);
        $('#edit_late_fee_amount').val(lateFee);
        $('#edit_late_fee_days').val(lateFeeDays);
        $('#edit_status').val(status);

        // Show modal
        $('#editStructureModal').modal('show');
    });
});
</script>
@endpush
@endsection
