@extends('student.layout')

@section('title', 'Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="row">
    <!-- Profile Card -->
    <div class="col-md-4 mb-4">
        <div class="stat-card primary">
            <div class="text-center">
                @if($student->photo)
                    <img src="{{ asset('storage/' . $student->photo) }}" alt="Profile" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-bottom: 20px;">
                @else
                    <div style="width: 150px; height: 150px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 48px; font-weight: 700; margin: 0 auto 20px;">
                        {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                    </div>
                @endif
                <h4 style="color: #5a5c69; font-weight: 700;">{{ $student->first_name }} {{ $student->last_name }}</h4>
                <p style="color: #858796; margin-bottom: 5px;">{{ $student->class->class_name ?? 'N/A' }} - {{ $student->section->section_name ?? 'N/A' }}</p>
                <p style="color: #858796;">Roll No: {{ $student->roll_no ?? 'N/A' }}</p>
                <span class="badge" style="background: {{ $student->status == 'Active' ? '#1cc88a' : '#e74a3b' }}; color: white; padding: 6px 15px; border-radius: 20px;">
                    {{ $student->status }}
                </span>
            </div>
        </div>
    </div>

    <!-- Personal Information -->
    <div class="col-md-8 mb-4">
        <div class="stat-card">
            <h5 style="color: #5a5c69; font-weight: 700; margin-bottom: 20px;">
                <i class="fas fa-user me-2"></i>Personal Information
            </h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label style="color: #858796; font-size: 12px; font-weight: 600; text-transform: uppercase;">Admission Number</label>
                    <p style="color: #5a5c69; font-weight: 600; margin: 0;">{{ $student->admission_no }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label style="color: #858796; font-size: 12px; font-weight: 600; text-transform: uppercase;">Date of Birth</label>
                    <p style="color: #5a5c69; font-weight: 600; margin: 0;">{{ \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label style="color: #858796; font-size: 12px; font-weight: 600; text-transform: uppercase;">Gender</label>
                    <p style="color: #5a5c69; font-weight: 600; margin: 0;">{{ $student->gender }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label style="color: #858796; font-size: 12px; font-weight: 600; text-transform: uppercase;">Email</label>
                    <p style="color: #5a5c69; font-weight: 600; margin: 0;">{{ $student->email ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label style="color: #858796; font-size: 12px; font-weight: 600; text-transform: uppercase;">Phone</label>
                    <p style="color: #5a5c69; font-weight: 600; margin: 0;">{{ $student->phone ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label style="color: #858796; font-size: 12px; font-weight: 600; text-transform: uppercase;">Academic Session</label>
                    <p style="color: #5a5c69; font-weight: 600; margin: 0;">{{ $student->session->session_name ?? 'N/A' }}</p>
                </div>
                <div class="col-12 mb-3">
                    <label style="color: #858796; font-size: 12px; font-weight: 600; text-transform: uppercase;">Address</label>
                    <p style="color: #5a5c69; font-weight: 600; margin: 0;">{{ $student->address ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Family Information -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="stat-card success">
            <h5 style="color: #5a5c69; font-weight: 700; margin-bottom: 20px;">
                <i class="fas fa-male me-2"></i>Father's Information
            </h5>
            <div class="mb-3">
                <label style="color: #858796; font-size: 12px; font-weight: 600; text-transform: uppercase;">Name</label>
                <p style="color: #5a5c69; font-weight: 600; margin: 0;">{{ $student->father_name }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="stat-card info">
            <h5 style="color: #5a5c69; font-weight: 700; margin-bottom: 20px;">
                <i class="fas fa-female me-2"></i>Mother's Information
            </h5>
            <div class="mb-3">
                <label style="color: #858796; font-size: 12px; font-weight: 600; text-transform: uppercase;">Name</label>
                <p style="color: #5a5c69; font-weight: 600; margin: 0;">{{ $student->mother_name }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Update Profile Form -->
<div class="row">
    <div class="col-12">
        <div class="stat-card warning">
            <h5 style="color: #5a5c69; font-weight: 700; margin-bottom: 20px;">
                <i class="fas fa-edit me-2"></i>Update Profile
            </h5>
            <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $student->email }}" placeholder="Enter email">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ $student->phone }}" placeholder="Enter phone">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Enter address">{{ $student->address }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Profile Photo</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        <small class="text-muted">Max size: 2MB</small>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
</style>
@endsection
