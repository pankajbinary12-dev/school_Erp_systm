@extends('admin.layouts.horizontal')
@section('title', 'Pending Fees')
@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-clock me-2"></i>Pending Fees</h5>
    </div>
    <div class="content-card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Admission No</th>
                        <th>Class</th>
                        <th>Fee Type</th>
                        <th>Total Amount</th>
                        <th>Paid Amount</th>
                        <th>Due Amount</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingFees as $fee)
                    <tr>
                        <td>{{ $fee->student->name }}</td>
                        <td>{{ $fee->student->admission_no }}</td>
                        <td>{{ $fee->student->class->class_name ?? 'N/A' }}</td>
                        <td>{{ $fee->feeStructure->feeType->name }}</td>
                        <td>₹{{ number_format($fee->total_amount, 2) }}</td>
                        <td>₹{{ number_format($fee->paid_amount, 2) }}</td>
                        <td>₹{{ number_format($fee->due_amount, 2) }}</td>
                        <td>{{ $fee->due_date ? $fee->due_date->format('d M Y') : 'N/A' }}</td>
                        <td>
                            <span class="badge bg-{{ $fee->status == 'overdue' ? 'danger' : 'warning' }}">
                                {{ strtoupper($fee->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.fees.collect') }}?student_id={{ $fee->student_id }}" class="btn btn-sm btn-primary" title="Collect Fee">
                                <i class="fas fa-money-bill"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center">No pending fees found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $pendingFees->links() }}
        </div>
    </div>
</div>
@endsection
