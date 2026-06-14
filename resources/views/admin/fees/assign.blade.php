@extends('admin.layouts.horizontal')
@section('title', 'Assign Fees')
@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-user-tag me-2"></i>Assign Fees to Students</h5>
    </div>
    <div class="content-card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Assign Fee Structure to Class</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.fees.assign.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label>Select Class <span class="text-danger">*</span></label>
                            <select name="class_id" id="classSelect" class="form-control" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->class_name }} ({{ $class->students->count() }} students)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Select Fee Structures <span class="text-danger">*</span></label>
                            <div class="border p-3" style="max-height: 200px; overflow-y: auto;">
                                @foreach($feeStructures as $structure)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fee_structure_ids[]" value="{{ $structure->id }}" id="fee{{ $structure->id }}">
                                    <label class="form-check-label" for="fee{{ $structure->id }}">
                                        {{ $structure->class->class_name }} - {{ $structure->feeType->name }} (₹{{ number_format($structure->amount, 2) }})
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Assign Fees
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="alert alert-info mt-4">
            <i class="fas fa-info-circle"></i> <strong>Note:</strong> 
            <ul class="mb-0 mt-2">
                <li>Fees will be assigned to all active students in the selected class</li>
                <li>If a fee is already assigned to a student, it will be skipped</li>
                <li>Students can have multiple fee types assigned</li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#classSelect').change(function() {
        const classId = $(this).val();
        // Filter fee structures by selected class
        $('input[name="fee_structure_ids[]"]').each(function() {
            const label = $(this).next('label').text();
            const checkbox = $(this);
            checkbox.prop('checked', false);
        });
    });
});
</script>
@endpush
@endsection
