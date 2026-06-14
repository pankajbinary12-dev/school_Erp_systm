@extends('student.layout')

@section('title', 'Fees')
@section('page-title', 'Fee Details')

@section('content')
<!-- Fee Summary -->
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="fas fa-rupee-sign"></i>
            </div>
            <h6 style="color: #858796; font-size: 12px; font-weight: 700; text-transform: uppercase;">Total Fee</h6>
            <h3 style="color: #5a5c69; font-size: 28px; font-weight: 700;">₹{{ number_format($totalFee, 0) }}</h3>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="stat-card success">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h6 style="color: #858796; font-size: 12px; font-weight: 700; text-transform: uppercase;">Paid Amount</h6>
            <h3 style="color: #5a5c69; font-size: 28px; font-weight: 700;">₹{{ number_format($paidAmount, 0) }}</h3>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="stat-card {{ $dueAmount > 0 ? 'danger' : 'success' }}">
            <div class="stat-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h6 style="color: #858796; font-size: 12px; font-weight: 700; text-transform: uppercase;">Due Amount</h6>
            <h3 style="color: #5a5c69; font-size: 28px; font-weight: 700;">₹{{ number_format($dueAmount, 0) }}</h3>
        </div>
    </div>
</div>

<!-- Fee Structure -->
@if($studentFee)
<div class="row">
    <div class="col-12 mb-4">
        <div class="stat-card info">
            <h5 style="color: #5a5c69; font-weight: 700; margin-bottom: 20px;">
                <i class="fas fa-list-alt me-2"></i>Fee Structure
            </h5>
            <div style="background: #f8f9fc; padding: 20px; border-radius: 8px;">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label style="color: #858796; font-size: 12px; font-weight: 600;">Fee Type</label>
                        <p style="color: #5a5c69; font-weight: 600; margin: 0;">{{ $studentFee->feeStructure->feeType->fee_type_name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label style="color: #858796; font-size: 12px; font-weight: 600;">Status</label>
                        <p style="margin: 0;">
                            <span class="badge" style="background: 
                                @if($studentFee->status == 'paid') #1cc88a
                                @elseif($studentFee->status == 'partial') #f6c23e
                                @else #e74a3b @endif; color: white; padding: 6px 12px;">
                                {{ ucfirst($studentFee->status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Payment History -->
<div class="row">
    <div class="col-12">
        <div class="stat-card warning">
            <h5 style="color: #5a5c69; font-weight: 700; margin-bottom: 20px;">
                <i class="fas fa-history me-2"></i>Payment History
            </h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead style="background: #f8f9fc;">
                        <tr>
                            <th>Receipt No</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Payment Mode</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->receipt_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                            <td><strong>₹{{ number_format($payment->amount, 0) }}</strong></td>
                            <td>
                                <span class="badge" style="background: #36b9cc; color: white; padding: 4px 10px;">
                                    {{ ucfirst($payment->payment_mode) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('student.fees.receipt', $payment->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-download me-1"></i>Receipt
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center" style="padding: 40px;">
                                <i class="fas fa-receipt" style="font-size: 48px; opacity: 0.3; color: #858796;"></i>
                                <p style="color: #858796; margin-top: 10px;">No payment history</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 6px 15px;
        border-radius: 6px;
        font-weight: 600;
        text-decoration: none;
        color: white;
    }
</style>
@endsection
