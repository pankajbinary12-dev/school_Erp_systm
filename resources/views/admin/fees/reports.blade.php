@extends('admin.layouts.horizontal')
@section('title', 'Fee Reports')
@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-chart-bar me-2"></i>Fee Reports</h5>
    </div>
    <div class="content-card-body">
        <!-- Report Type Selection -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.fees.reports') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Report Type</label>
                            <select name="type" class="form-control" onchange="this.form.submit()">
                                <option value="daily" {{ request('type') == 'daily' ? 'selected' : '' }}>Daily Report</option>
                                <option value="monthly" {{ request('type') == 'monthly' ? 'selected' : '' }}>Monthly Report</option>
                            </select>
                        </div>
                        @if(request('type') == 'daily')
                        <div class="col-md-4">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control" value="{{ request('date', date('Y-m-d')) }}" onchange="this.form.submit()">
                        </div>
                        @endif
                        <div class="col-md-4">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-success form-control" onclick="window.print()">
                                <i class="fas fa-print"></i> Print Report
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Report Summary -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">Report Summary</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h5>Total Payments: {{ $payments->count() }}</h5>
                    </div>
                    <div class="col-md-4">
                        <h5>Total Amount: ₹{{ number_format($total, 2) }}</h5>
                    </div>
                    <div class="col-md-4">
                        <h5>Report Type: {{ strtoupper($type) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Receipt No</th>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Fee Type</th>
                        <th>Amount</th>
                        <th>Payment Mode</th>
                        <th>Collected By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $index => $payment)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $payment->receipt_no }}</td>
                        <td>{{ $payment->payment_date->format('d M Y') }}</td>
                        <td>{{ $payment->student->name }}</td>
                        <td>{{ $payment->studentFee->feeStructure->feeType->name }}</td>
                        <td>₹{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ strtoupper($payment->payment_mode) }}</td>
                        <td>{{ $payment->collectedBy->username ?? 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No payments found for this period</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="5" class="text-end">Total:</th>
                        <th>₹{{ number_format($total, 2) }}</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Payment Mode Breakdown -->
        @if($payments->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Payment Mode Breakdown</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $modeBreakdown = $payments->groupBy('payment_mode')->map(function($group) {
                            return $group->sum('amount');
                        });
                    @endphp
                    @foreach($modeBreakdown as $mode => $amount)
                    <div class="col-md-3">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <h6>{{ strtoupper($mode) }}</h6>
                                <h4>₹{{ number_format($amount, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
@media print {
    .content-card-header, .btn, .card:first-child { display: none; }
}
</style>
@endpush
@endsection
