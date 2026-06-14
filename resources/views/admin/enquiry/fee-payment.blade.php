@extends('admin.layouts.horizontal')

@section('title', 'Fee Payment')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-rupee-sign"></i> Fee Payment - {{ $enquiry->enquiry_number }}</h4>
            <a href="{{ route('admin.enquiry.view', $enquiry->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

<!-- Student & Fee Summary -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h6 class="m-0"><i class="fas fa-user"></i> Student Details</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ $enquiry->full_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Class:</strong></td>
                        <td>{{ $enquiry->class->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td>{{ $enquiry->phone }}</td>
                    </tr>
                    <tr>
                        <td><strong>Father Name:</strong></td>
                        <td>{{ $enquiry->father_name }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h6 class="m-0"><i class="fas fa-money-bill-wave"></i> Fee Summary</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td><strong>Total Fee:</strong></td>
                        <td class="text-end"><span class="fs-5 text-primary">₹{{ number_format($enquiry->registration_fee, 2) }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Already Paid:</strong></td>
                        <td class="text-end"><span class="fs-5 text-success">₹{{ number_format($enquiry->fee_paid, 2) }}</span></td>
                    </tr>
                    <tr class="border-top">
                        <td><strong>Balance Due:</strong></td>
                        <td class="text-end"><span class="fs-4 text-danger fw-bold">₹{{ number_format($enquiry->balance_amount, 2) }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Payment Form -->
<div class="card shadow mb-4">
    <div class="card-header bg-info text-white">
        <h6 class="m-0"><i class="fas fa-credit-card"></i> Collect Payment</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.enquiry.process-fee', $enquiry->id) }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Amount to Pay <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" step="0.01" name="fee_paid" class="form-control @error('fee_paid') is-invalid @enderror" 
                               value="{{ old('fee_paid', $enquiry->balance_amount) }}" 
                               max="{{ $enquiry->balance_amount }}" 
                               required>
                        @error('fee_paid')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <small class="text-muted">Maximum: ₹{{ number_format($enquiry->balance_amount, 2) }}</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                    <input type="date" name="fee_paid_date" class="form-control @error('fee_paid_date') is-invalid @enderror" 
                           value="{{ old('fee_paid_date', date('Y-m-d')) }}" 
                           required>
                    @error('fee_paid_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                    <select name="payment_mode" class="form-select @error('payment_mode') is-invalid @enderror" required>
                        <option value="">Select Payment Mode</option>
                        <option value="Cash" {{ old('payment_mode') == 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Online" {{ old('payment_mode') == 'Online' ? 'selected' : '' }}>Online</option>
                        <option value="Cheque" {{ old('payment_mode') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="Bank Transfer" {{ old('payment_mode') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    </select>
                    @error('payment_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Transaction ID / Cheque No</label>
                    <input type="text" name="transaction_id" class="form-control @error('transaction_id') is-invalid @enderror" 
                           value="{{ old('transaction_id') }}" 
                           placeholder="Optional">
                    @error('transaction_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Note:</strong> After full payment, you can convert this enquiry to admission and generate admission number.
                    </div>
                </div>

                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.enquiry.view', $enquiry->id) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Collect Payment
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Payment History -->
@if($enquiry->fee_paid > 0)
<div class="card shadow mb-4">
    <div class="card-header bg-secondary text-white">
        <h6 class="m-0"><i class="fas fa-history"></i> Payment History</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Payment Mode</th>
                        <th>Transaction ID</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $enquiry->fee_paid_date ? $enquiry->fee_paid_date->format('d-M-Y') : 'N/A' }}</td>
                        <td>₹{{ number_format($enquiry->fee_paid, 2) }}</td>
                        <td>{{ $enquiry->payment_mode ?? 'N/A' }}</td>
                        <td>{{ $enquiry->transaction_id ?? 'N/A' }}</td>
                        <td>
                            @if($enquiry->fee_status == 'Paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($enquiry->fee_status == 'Partial')
                                <span class="badge bg-warning">Partial</span>
                            @else
                                <span class="badge bg-danger">Pending</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
