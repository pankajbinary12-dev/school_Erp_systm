@extends('admin.layouts.horizontal')
@section('title', 'View Certificate')

@section('content')
<div class="content-card">
    <div class="content-card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-certificate me-2"></i>Certificate Details</h5>
        <div>
            <a href="{{ route('admin.certificates.preview', $certificate->id) }}" 
               class="btn btn-primary btn-sm me-2" target="_blank">
                <i class="fas fa-eye me-1"></i>Preview PDF
            </a>
            <a href="{{ route('admin.certificates.download', $certificate->id) }}" 
               class="btn btn-success btn-sm">
                <i class="fas fa-download me-1"></i>Download PDF
            </a>
        </div>
    </div>

    <div class="content-card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Certificate Information</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Certificate No:</th>
                                <td><strong>{{ $certificate->certificate_no }}</strong></td>
                            </tr>
                            <tr>
                                <th>Type:</th>
                                <td><span class="badge bg-info">{{ $certificate->getTypeLabel() }}</span></td>
                            </tr>
                            <tr>
                                <th>Issue Date:</th>
                                <td>{{ $certificate->issue_date->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($certificate->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Cancelled</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Issued By:</th>
                                <td>{{ $certificate->issuedBy->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Generated On:</th>
                                <td>{{ $certificate->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">Student Information</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Name:</th>
                                <td>{{ $certificate->student->first_name }} {{ $certificate->student->last_name }}</td>
                            </tr>
                            <tr>
                                <th>Roll Number:</th>
                                <td>{{ $certificate->student->roll_number }}</td>
                            </tr>
                            <tr>
                                <th>Father Name:</th>
                                <td>{{ $certificate->student->father_name }}</td>
                            </tr>
                            <tr>
                                <th>Class:</th>
                                <td>{{ $certificate->student->class->class_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Date of Birth:</th>
                                <td>{{ $certificate->student->date_of_birth }}</td>
                            </tr>
                            <tr>
                                <th>Admission No:</th>
                                <td>{{ $certificate->student->admission_number ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Certificate Content</h6>
            </div>
            <div class="card-body">
                <p class="text-justify">{{ $certificate->content }}</p>
            </div>
        </div>

        @if($certificate->remarks)
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Remarks</h6>
            </div>
            <div class="card-body">
                <p>{{ $certificate->remarks }}</p>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">QR Code for Verification</h6>
            </div>
            <div class="card-body text-center">
                <img src="data:image/png;base64,{{ $certificate->qr_code }}" alt="QR Code">
                <p class="mt-2 text-muted">Scan to verify certificate authenticity</p>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('admin.certificates.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to List
            </a>
        </div>
    </div>
</div>
@endsection
