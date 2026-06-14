@extends('admin.layouts.horizontal')
@section('title', 'Fee Dashboard')
@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-chart-line me-2"></i>Fee Dashboard</h5>
    </div>
    <div class="content-card-body">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Today's Collection</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($stats['total_collected_today'], 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Monthly Collection</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($stats['total_collected_month'], 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Pending</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($stats['total_pending'], 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Overdue Amount</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($stats['total_overdue'], 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Payments</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Receipt No</th>
                                <th>Student</th>
                                <th>Amount</th>
                                <th>Payment Mode</th>
                                <th>Date</th>
                                <th>Collected By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $payment)
                            <tr>
                                <td>{{ $payment->receipt_no }}</td>
                                <td>{{ $payment->student->name ?? 'N/A' }}</td>
                                <td>₹{{ number_format($payment->amount, 2) }}</td>
                                <td><span class="badge bg-info">{{ strtoupper($payment->payment_mode) }}</span></td>
                                <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                <td>{{ $payment->collectedBy->username ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('admin.fees.receipt.download', $payment->id) }}" class="btn btn-sm btn-primary" title="Download Receipt">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No recent payments found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
.border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
</style>
@endpush
@endsection
