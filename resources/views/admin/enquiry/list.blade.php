@extends('admin.layouts.horizontal')

@section('title', 'All Enquiries')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-list"></i> All Enquiries</h4>
            <a href="{{ route('admin.enquiry.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Enquiry
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.enquiry.list') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="Converted" {{ request('status') == 'Converted' ? 'selected' : '' }}>Converted</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Fee Status</label>
                <select name="fee_status" class="form-select">
                    <option value="">All Fee Status</option>
                    <option value="Pending" {{ request('fee_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Partial" {{ request('fee_status') == 'Partial' ? 'selected' : '' }}>Partial</option>
                    <option value="Paid" {{ request('fee_status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name, Phone, Enquiry No" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.enquiry.list') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Enquiries Table -->
<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Enquiry No</th>
                        <th>Date</th>
                        <th>Student Name</th>
                        <th>Phone</th>
                        <th>Class</th>
                        <th>Status</th>
                        <th>Fee Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enquiries as $enquiry)
                    <tr>
                        <td>{{ $loop->iteration + ($enquiries->currentPage() - 1) * $enquiries->perPage() }}</td>
                        <td><strong>{{ $enquiry->enquiry_number }}</strong></td>
                        <td>{{ $enquiry->enquiry_date->format('d-M-Y') }}</td>
                        <td>{{ $enquiry->full_name }}</td>
                        <td>{{ $enquiry->phone }}</td>
                        <td>{{ $enquiry->class->name ?? 'N/A' }}</td>
                        <td>
                            @if($enquiry->status == 'Pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($enquiry->status == 'Approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($enquiry->status == 'Rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @elseif($enquiry->status == 'Converted')
                                <span class="badge bg-info">Converted</span>
                            @endif
                        </td>
                        <td>
                            @if($enquiry->fee_status == 'Pending')
                                <span class="badge bg-danger">Pending</span>
                            @elseif($enquiry->fee_status == 'Partial')
                                <span class="badge bg-warning">Partial</span>
                            @elseif($enquiry->fee_status == 'Paid')
                                <span class="badge bg-success">Paid</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.enquiry.view', $enquiry->id) }}" class="btn btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($enquiry->status != 'Converted')
                                <a href="{{ route('admin.enquiry.edit', $enquiry->id) }}" class="btn btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            No enquiries found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $enquiries->links() }}
        </div>
    </div>
</div>
@endsection
