@extends('admin.layouts.horizontal')

@section('title', 'View Enquiry')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-eye"></i> Enquiry Details</h4>
            <div class="d-flex gap-2">
                @if($enquiry->status != 'Converted')
                <a href="{{ route('admin.enquiry.edit', $enquiry->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @endif
                <a href="{{ route('admin.enquiry.list') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
    @if(session('admission_details'))
        <hr>
        <strong>Login Credentials:</strong><br>
        <strong>Admission Number:</strong> {{ session('admission_details')['admission_number'] }}<br>
        <strong>Username:</strong> {{ session('admission_details')['username'] }}<br>
        <strong>Password:</strong> {{ session('admission_details')['password'] }}<br>
        <small class="text-muted">Please save these credentials and share with student.</small>
    @endif
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Status and Actions -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Enquiry Number:</strong><br>
                        <span class="text-primary fs-5">{{ $enquiry->enquiry_number }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Status:</strong><br>
                        @if($enquiry->status == 'Pending')
                            <span class="badge bg-warning fs-6">Pending</span>
                        @elseif($enquiry->status == 'Approved')
                            <span class="badge bg-success fs-6">Approved</span>
                        @elseif($enquiry->status == 'Rejected')
                            <span class="badge bg-danger fs-6">Rejected</span>
                        @elseif($enquiry->status == 'Converted')
                            <span class="badge bg-info fs-6">Converted</span>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <strong>Fee Status:</strong><br>
                        @if($enquiry->fee_status == 'Pending')
                            <span class="badge bg-danger fs-6">Pending</span>
                        @elseif($enquiry->fee_status == 'Partial')
                            <span class="badge bg-warning fs-6">Partial</span>
                        @elseif($enquiry->fee_status == 'Paid')
                            <span class="badge bg-success fs-6">Paid</span>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <strong>Date:</strong><br>
                        {{ $enquiry->enquiry_date->format('d-M-Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body">
                <h6 class="mb-3">Quick Actions</h6>
                @if($enquiry->status == 'Pending')
                    <form action="{{ route('admin.enquiry.approve', $enquiry->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm mb-2 w-100" onclick="return confirm('Approve this enquiry?')">
                            <i class="fas fa-check"></i> Approve
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger btn-sm mb-2 w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fas fa-times"></i> Reject
                    </button>
                @endif
                
                @if($enquiry->status == 'Approved' && $enquiry->fee_status != 'Paid')
                    <a href="{{ route('admin.enquiry.fee-payment', $enquiry->id) }}" class="btn btn-primary btn-sm mb-2 w-100">
                        <i class="fas fa-rupee-sign"></i> Collect Fee
                    </a>
                @endif
                
                @if($enquiry->canConvertToAdmission() && $enquiry->status != 'Converted')
                    <form action="{{ route('admin.enquiry.convert', $enquiry->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-info btn-sm mb-2 w-100" onclick="return confirm('Convert to admission? This will create a student account.')">
                            <i class="fas fa-user-plus"></i> Convert to Admission
                        </button>
                    </form>
                @endif
                
                @if($enquiry->status != 'Converted')
                    <form action="{{ route('admin.enquiry.delete', $enquiry->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Delete this enquiry?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Student Information -->
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <h6 class="m-0"><i class="fas fa-user"></i> Student Information</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <strong>Full Name:</strong><br>
                {{ $enquiry->full_name }}
            </div>
            <div class="col-md-4 mb-3">
                <strong>Date of Birth:</strong><br>
                {{ $enquiry->date_of_birth->format('d-M-Y') }}
            </div>
            <div class="col-md-4 mb-3">
                <strong>Gender:</strong><br>
                {{ $enquiry->gender }}
            </div>
            <div class="col-md-4 mb-3">
                <strong>Email:</strong><br>
                {{ $enquiry->email ?? 'N/A' }}
            </div>
            <div class="col-md-4 mb-3">
                <strong>Phone:</strong><br>
                {{ $enquiry->phone }}
            </div>
            <div class="col-md-4 mb-3">
                <strong>Class:</strong><br>
                {{ $enquiry->class->name ?? 'N/A' }}
            </div>
        </div>
    </div>
</div>

<!-- Address Information -->
<div class="card shadow mb-4">
    <div class="card-header bg-success text-white">
        <h6 class="m-0"><i class="fas fa-map-marker-alt"></i> Address Information</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mb-3">
                <strong>Address:</strong><br>
                {{ $enquiry->address }}
            </div>
            <div class="col-md-4 mb-3">
                <strong>City:</strong><br>
                {{ $enquiry->city }}
            </div>
            <div class="col-md-4 mb-3">
                <strong>State:</strong><br>
                {{ $enquiry->state }}
            </div>
            <div class="col-md-4 mb-3">
                <strong>Pincode:</strong><br>
                {{ $enquiry->pincode }}
            </div>
        </div>
    </div>
</div>

<!-- Parent Information -->
<div class="card shadow mb-4">
    <div class="card-header bg-warning text-dark">
        <h6 class="m-0"><i class="fas fa-users"></i> Parent Information</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <strong>Father Name:</strong><br>
                {{ $enquiry->father_name }}
            </div>
            <div class="col-md-4 mb-3">
                <strong>Father Phone:</strong><br>
                {{ $enquiry->father_phone }}
            </div>
            <div class="col-md-4 mb-3">
                <strong>Father Occupation:</strong><br>
                {{ $enquiry->father_occupation ?? 'N/A' }}
            </div>
            <div class="col-md-4 mb-3">
                <strong>Mother Name:</strong><br>
                {{ $enquiry->mother_name }}
            </div>
            <div class="col-md-4 mb-3">
                <strong>Mother Phone:</strong><br>
                {{ $enquiry->mother_phone ?? 'N/A' }}
            </div>
            <div class="col-md-4 mb-3">
                <strong>Mother Occupation:</strong><br>
                {{ $enquiry->mother_occupation ?? 'N/A' }}
            </div>
            <div class="col-md-12 mb-3">
                <strong>Annual Income:</strong><br>
                ₹{{ number_format($enquiry->annual_income ?? 0, 2) }}
            </div>
        </div>
    </div>
</div>

<!-- Academic Information -->
<div class="card shadow mb-4">
    <div class="card-header bg-info text-white">
        <h6 class="m-0"><i class="fas fa-graduation-cap"></i> Academic Information</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <strong>Previous School:</strong><br>
                {{ $enquiry->previous_school ?? 'N/A' }}
            </div>
            <div class="col-md-4 mb-3">
                <strong>Previous Class:</strong><br>
                {{ $enquiry->previous_class ?? 'N/A' }}
            </div>
            <div class="col-md-4 mb-3">
                <strong>Previous Percentage:</strong><br>
                {{ $enquiry->previous_percentage ?? 'N/A' }}%
            </div>
        </div>
    </div>
</div>

<!-- Fee Information -->
<div class="card shadow mb-4">
    <div class="card-header bg-danger text-white">
        <h6 class="m-0"><i class="fas fa-rupee-sign"></i> Fee Information</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-3">
                <strong>Registration Fee:</strong><br>
                <span class="fs-5 text-primary">₹{{ number_format($enquiry->registration_fee, 2) }}</span>
            </div>
            <div class="col-md-3 mb-3">
                <strong>Fee Paid:</strong><br>
                <span class="fs-5 text-success">₹{{ number_format($enquiry->fee_paid, 2) }}</span>
            </div>
            <div class="col-md-3 mb-3">
                <strong>Balance:</strong><br>
                <span class="fs-5 text-danger">₹{{ number_format($enquiry->balance_amount, 2) }}</span>
            </div>
            <div class="col-md-3 mb-3">
                <strong>Payment Mode:</strong><br>
                {{ $enquiry->payment_mode ?? 'N/A' }}
            </div>
            @if($enquiry->transaction_id)
            <div class="col-md-6 mb-3">
                <strong>Transaction ID:</strong><br>
                {{ $enquiry->transaction_id }}
            </div>
            @endif
            @if($enquiry->fee_paid_date)
            <div class="col-md-6 mb-3">
                <strong>Fee Paid Date:</strong><br>
                {{ $enquiry->fee_paid_date->format('d-M-Y') }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Additional Information -->
<div class="card shadow mb-4">
    <div class="card-header bg-secondary text-white">
        <h6 class="m-0"><i class="fas fa-info-circle"></i> Additional Information</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <strong>Source:</strong><br>
                {{ $enquiry->source ?? 'N/A' }}
            </div>
            <div class="col-md-4 mb-3">
                <strong>Reference By:</strong><br>
                {{ $enquiry->reference_by ?? 'N/A' }}
            </div>
            <div class="col-md-4 mb-3">
                <strong>Created By:</strong><br>
                {{ $enquiry->createdBy->username ?? 'N/A' }}
            </div>
            @if($enquiry->approved_by)
            <div class="col-md-4 mb-3">
                <strong>Approved By:</strong><br>
                {{ $enquiry->approvedBy->username ?? 'N/A' }}
            </div>
            <div class="col-md-4 mb-3">
                <strong>Approved At:</strong><br>
                {{ $enquiry->approved_at->format('d-M-Y H:i') }}
            </div>
            @endif
            @if($enquiry->admission_number)
            <div class="col-md-4 mb-3">
                <strong>Admission Number:</strong><br>
                <span class="text-success fs-5">{{ $enquiry->admission_number }}</span>
            </div>
            <div class="col-md-4 mb-3">
                <strong>Admission Date:</strong><br>
                {{ $enquiry->admission_date->format('d-M-Y') }}
            </div>
            @endif
            @if($enquiry->remarks)
            <div class="col-md-12 mb-3">
                <strong>Remarks:</strong><br>
                {{ $enquiry->remarks }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.enquiry.reject', $enquiry->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Reject Enquiry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="remarks" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Enquiry</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
