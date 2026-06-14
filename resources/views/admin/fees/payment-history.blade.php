@extends('admin.layouts.horizontal')
@section('title', 'Payment History')
@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-history me-2"></i>Payment History</h5>
    </div>
    <div class="content-card-body">
        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.fees.payment-history') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <label>From Date</label>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label>To Date</label>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label>Student ID</label>
                            <input type="text" name="student_id" class="form-control" placeholder="Enter Student ID" value="{{ request('student_id') }}">
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary form-control">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payment History Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Receipt No</th>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Admission No</th>
                        <th>Fee Type</th>
                        <th>Amount</th>
                        <th>Late Fee</th>
                        <th>Payment Mode</th>
                        <th>Collected By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->receipt_no }}</td>
                        <td>{{ $payment->payment_date->format('d M Y') }}</td>
                        <td>{{ $payment->student->name }}</td>
                        <td>{{ $payment->student->admission_no }}</td>
                        <td>{{ $payment->studentFee->feeStructure->feeType->name }}</td>
                        <td>₹{{ number_format($payment->amount, 2) }}</td>
                        <td>₹{{ number_format($payment->late_fee_paid, 2) }}</td>
                        <td><span class="badge bg-info">{{ strtoupper($payment->payment_mode) }}</span></td>
                        <td>{{ $payment->collectedBy->username ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('admin.fees.receipt.download', $payment->id) }}" class="btn btn-sm btn-primary" title="Download Receipt">
                                <i class="fas fa-download"></i>
                            </a>
                            <a href="{{ route('admin.fees.receipt.print', $payment->id) }}" class="btn btn-sm btn-secondary" target="_blank" title="Print Receipt">
                                <i class="fas fa-print"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center">No payment records found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection
