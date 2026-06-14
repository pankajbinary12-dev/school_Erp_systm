@extends('admin.layouts.horizontal')
@section('title', 'Certificates')

@section('content')
<div class="content-card">
    <div class="content-card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-certificate me-2"></i>Student Certificates</h5>
        <div>
            <a href="{{ route('admin.certificates.bulk.create') }}" class="btn btn-info btn-sm me-2">
                <i class="fas fa-layer-group me-1"></i>Bulk Generate
            </a>
            <a href="{{ route('admin.certificates.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>Generate Certificate
            </a>
        </div>
    </div>

    <div class="content-card-body">
        <!-- Filters -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Certificate Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary d-block w-100">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <a href="{{ route('admin.certificates.index') }}" class="btn btn-secondary d-block w-100">
                    <i class="fas fa-redo me-1"></i>Reset
                </a>
            </div>
        </form>

        <!-- Certificates Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Certificate No</th>
                        <th>Student</th>
                        <th>Type</th>
                        <th>Issue Date</th>
                        <th>Issued By</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $certificate)
                        <tr>
                            <td><strong>{{ $certificate->certificate_no }}</strong></td>
                            <td>
                                {{ $certificate->student->first_name }} {{ $certificate->student->last_name }}
                                <br><small class="text-muted">{{ $certificate->student->roll_number }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $certificate->getTypeLabel() }}</span>
                            </td>
                            <td>{{ $certificate->issue_date->format('d M Y') }}</td>
                            <td>{{ $certificate->issuedBy->name ?? 'N/A' }}</td>
                            <td>
                                @if($certificate->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.certificates.show', $certificate->id) }}" 
                                   class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.certificates.preview', $certificate->id) }}" 
                                   class="btn btn-sm btn-primary" title="Preview" target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <a href="{{ route('admin.certificates.download', $certificate->id) }}" 
                                   class="btn btn-sm btn-success" title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                @if($certificate->status == 'active')
                                    <form action="{{ route('admin.certificates.cancel', $certificate->id) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Cancel this certificate?')">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Cancel">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No certificates found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $certificates->links() }}
        </div>
    </div>
</div>
@endsection
