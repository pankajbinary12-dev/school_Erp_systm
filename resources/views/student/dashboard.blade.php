@extends('layouts.student')

@section('title', 'Student Dashboard')

@section('content')
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar" style="width: 250px;">
        <div class="p-3 text-center border-bottom">
            <i class="fas fa-user-graduate fa-3x text-white mb-2"></i>
            <h5 class="text-white mb-0">{{ $student->full_name }}</h5>
            <small class="text-white-50">{{ $student->admission_no }}</small>
        </div>
        
        <nav class="nav flex-column mt-3">
            <a class="nav-link active" href="#"><i class="fas fa-home"></i> Dashboard</a>
            <a class="nav-link" href="#"><i class="fas fa-user"></i> My Profile</a>
            <a class="nav-link" href="#"><i class="fas fa-book"></i> My Subjects</a>
            <a class="nav-link" href="#"><i class="fas fa-calendar-check"></i> Attendance</a>
            <a class="nav-link" href="#"><i class="fas fa-file-alt"></i> Assignments</a>
            <a class="nav-link" href="#"><i class="fas fa-chart-line"></i> Results</a>
            <a class="nav-link" href="#" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content flex-grow-1">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <h4 class="mb-0">Student Dashboard</h4>
                <div class="d-flex align-items-center">
                    <span class="me-3">{{ now()->format('l, F d, Y') }}</span>
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> {{ $student->first_name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" id="logoutBtn2"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="container-fluid p-4">
            <!-- Stats Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Class</h6>
                                <h3 class="mb-0">{{ $student->class->class_name }}</h3>
                                <small>Section: {{ $student->section->section_name }}</small>
                            </div>
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Session</h6>
                                <h3 class="mb-0">{{ $student->session->session_name }}</h3>
                                <small>Academic Year</small>
                            </div>
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Attendance</h6>
                                <h3 class="mb-0">95%</h3>
                                <small>This Month</small>
                            </div>
                            <i class="fas fa-chart-pie"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Roll No</h6>
                                <h3 class="mb-0">{{ $student->roll_no ?? 'N/A' }}</h3>
                                <small>Class Roll</small>
                            </div>
                            <i class="fas fa-id-card"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Information -->
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-user"></i> Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <strong>Full Name:</strong>
                                    <p class="text-muted">{{ $student->full_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Admission No:</strong>
                                    <p class="text-muted">{{ $student->admission_no }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Date of Birth:</strong>
                                    <p class="text-muted">{{ $student->date_of_birth->format('d M, Y') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Gender:</strong>
                                    <p class="text-muted">{{ $student->gender }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Father's Name:</strong>
                                    <p class="text-muted">{{ $student->father_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Mother's Name:</strong>
                                    <p class="text-muted">{{ $student->mother_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Contact:</strong>
                                    <p class="text-muted">{{ $student->guardian_phone }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Status:</strong>
                                    <p><span class="badge bg-success">{{ $student->status }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-bell"></i> Notifications</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Welcome to your dashboard!
                            </div>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Assignment due tomorrow
                            </div>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> Attendance marked for today
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Logout functionality
    $('#logoutBtn, #logoutBtn2').click(function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Logout?',
            text: 'Are you sure you want to logout?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, logout!'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                $.ajax({
                    url: '{{ route("logout") }}',
                    type: 'POST',
                    success: function(response) {
                        hideLoading();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Logged Out!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = response.redirect;
                            });
                        }
                    },
                    error: function() {
                        hideLoading();
                        showError('Logout failed!');
                    }
                });
            }
        });
    });
});
</script>
@endpush
