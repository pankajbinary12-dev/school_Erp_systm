@extends('admin.layouts.horizontal')

@section('title', 'View Student')

@push('styles')
<style>
    .student-profile-card {
        background: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .student-photo {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid var(--primary-color);
    }
    .student-photo-placeholder {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 60px;
        font-weight: bold;
        border: 5px solid var(--primary-color);
    }
    .info-label {
        font-weight: 600;
        color: #666;
        margin-bottom: 5px;
    }
    .info-value {
        font-size: 16px;
        color: #333;
        margin-bottom: 20px;
    }
    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--primary-color);
        margin-top: 30px;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--primary-color);
    }
</style>
@endpush

@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-user me-2"></i>Student Details</h5>
        <div>
            <a href="/admin/students/edit/{{ $student->id }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="/admin/students/all" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>
    
    <div class="student-profile-card">
        <div class="row">
            <div class="col-md-3 text-center">
                @if($student->photo)
                    <img src="{{ asset('storage/' . $student->photo) }}" alt="Student Photo" class="student-photo">
                @else
                    <div class="student-photo-placeholder">
                        {{ strtoupper(substr($student->first_name, 0, 1)) }}
                    </div>
                @endif
                <h4 class="mt-3">{{ $student->first_name }} {{ $student->last_name }}</h4>
                <p class="text-muted">{{ $student->admission_no }}</p>
                <span class="badge bg-{{ $student->status == 'Active' ? 'success' : 'danger' }} fs-6">
                    {{ $student->status }}
                </span>
            </div>
            
            <div class="col-md-9">
                <div class="section-title">Personal Information</div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-label">First Name</div>
                        <div class="info-value">{{ $student->first_name }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Last Name</div>
                        <div class="info-value">{{ $student->last_name }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Date of Birth</div>
                        <div class="info-value">{{ date('d M Y', strtotime($student->date_of_birth)) }}</div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-label">Gender</div>
                        <div class="info-value">{{ $student->gender }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Blood Group</div>
                        <div class="info-value">{{ $student->blood_group ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $student->email ?? 'N/A' }}</div>
                    </div>
                </div>

                <div class="section-title">Academic Information</div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-label">Admission Number</div>
                        <div class="info-value">{{ $student->admission_no }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Admission Date</div>
                        <div class="info-value">{{ date('d M Y', strtotime($student->admission_date)) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Roll Number</div>
                        <div class="info-value">{{ $student->roll_no ?? 'N/A' }}</div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-label">Class</div>
                        <div class="info-value">{{ $student->class->class_name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Section</div>
                        <div class="info-value">{{ $student->section->section_name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Session</div>
                        <div class="info-value">{{ $student->session->session_name ?? 'N/A' }}</div>
                    </div>
                </div>

                <div class="section-title">Parent/Guardian Information</div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-label">Father Name</div>
                        <div class="info-value">{{ $student->father_name }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Mother Name</div>
                        <div class="info-value">{{ $student->mother_name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Guardian Phone</div>
                        <div class="info-value">{{ $student->guardian_phone }}</div>
                    </div>
                </div>

                @if($student->address)
                <div class="row">
                    <div class="col-md-12">
                        <div class="info-label">Address</div>
                        <div class="info-value">{{ $student->address }}</div>
                    </div>
                </div>
                @endif

                <div class="section-title">Login Information</div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-label">Username</div>
                        <div class="info-value">{{ $student->username }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Created At</div>
                        <div class="info-value">{{ date('d M Y, h:i A', strtotime($student->created_at)) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Last Updated</div>
                        <div class="info-value">{{ date('d M Y, h:i A', strtotime($student->updated_at)) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
