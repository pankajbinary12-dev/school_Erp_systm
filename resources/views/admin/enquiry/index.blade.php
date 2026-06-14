@extends('admin.layouts.horizontal')

@section('title', 'Enquiry Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-clipboard-list"></i> Enquiry Management</h4>
            <a href="{{ route('admin.enquiry.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Enquiry
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-4 col-lg-2 mb-3">
        <div class="card border-left-primary shadow h-100">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Enquiries</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_enquiries'] }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-lg-2 mb-3">
        <div class="card border-left-warning shadow h-100">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_enquiries'] }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-lg-2 mb-3">
        <div class="card border-left-success shadow h-100">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Approved</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved_enquiries'] }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-lg-2 mb-3">
        <div class="card border-left-info shadow h-100">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Converted</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['converted_enquiries'] }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-lg-2 mb-3">
        <div class="card border-left-danger shadow h-100">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Fee Pending</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['fee_pending'] }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-lg-2 mb-3">
        <div class="card border-left-secondary shadow h-100">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Today</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['today_enquiries'] }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h6 class="m-0"><i class="fas fa-tasks"></i> Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.enquiry.create') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-plus-circle text-primary"></i> Add New Enquiry
                    </a>
                    <a href="{{ route('admin.enquiry.list') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-list text-info"></i> View All Enquiries
                    </a>
                    <a href="{{ route('admin.enquiry.list') }}?status=Pending" class="list-group-item list-group-item-action">
                        <i class="fas fa-clock text-warning"></i> Pending Approvals
                    </a>
                    <a href="{{ route('admin.enquiry.list') }}?fee_status=Pending" class="list-group-item list-group-item-action">
                        <i class="fas fa-rupee-sign text-danger"></i> Fee Pending
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h6 class="m-0"><i class="fas fa-info-circle"></i> Enquiry Process</h6>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li class="mb-2"><strong>Step 1:</strong> Create new enquiry with student details</li>
                    <li class="mb-2"><strong>Step 2:</strong> Review and approve/reject enquiry</li>
                    <li class="mb-2"><strong>Step 3:</strong> Collect registration fee after approval</li>
                    <li class="mb-2"><strong>Step 4:</strong> Convert to admission after fee payment</li>
                    <li class="mb-0"><strong>Step 5:</strong> Admission number generated automatically</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 4px solid #4e73df !important;
}
.border-left-success {
    border-left: 4px solid #1cc88a !important;
}
.border-left-info {
    border-left: 4px solid #36b9cc !important;
}
.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}
.border-left-danger {
    border-left: 4px solid #e74a3b !important;
}
.border-left-secondary {
    border-left: 4px solid #858796 !important;
}
</style>
@endsection
