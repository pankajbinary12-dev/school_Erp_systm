@extends('layouts.teacher')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="d-flex">
    <!-- Sidebar -->
    @include('teacher.partials.sidebar')

    <!-- Main Content -->
    <div class="main-content flex-grow-1" style="margin-left: 250px;">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <h4 class="mb-0">Teacher Dashboard</h4>
                <div class="d-flex align-items-center">
                    <span class="me-3">{{ now()->format('l, F d, Y') }}</span>
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> {{ $teacher->first_name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('teacher.profile') }}"><i class="fas fa-user"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('teacher.my.attendance') }}"><i class="fas fa-cog"></i> My Attendance</a></li>
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
                                <h6 class="mb-1">Total Classes</h6>
                                <h3 class="mb-0">5</h3>
                                <small>Assigned Classes</small>
                            </div>
                            <i class="fas fa-chalkboard"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Total Students</h6>
                                <h3 class="mb-0">150</h3>
                                <small>Under My Classes</small>
                            </div>
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Subjects</h6>
                                <h3 class="mb-0">3</h3>
                                <small>Teaching Subjects</small>
                            </div>
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Assignments</h6>
                                <h3 class="mb-0">12</h3>
                                <small>Pending Review</small>
                            </div>
                            <i class="fas fa-tasks"></i>
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
                                    <p class="text-muted">{{ $teacher->full_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Employee ID:</strong>
                                    <p class="text-muted">{{ $teacher->employee_id }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Date of Birth:</strong>
                                    <p class="text-muted">{{ $teacher->date_of_birth->format('d M, Y') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Gender:</strong>
                                    <p class="text-muted">{{ $teacher->gender }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Email:</strong>
                                    <p class="text-muted">{{ $teacher->email }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Phone:</strong>
                                    <p class="text-muted">{{ $teacher->phone }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Qualification:</strong>
                                    <p class="text-muted">{{ $teacher->qualification }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Joining Date:</strong>
                                    <p class="text-muted">{{ $teacher->joining_date->format('d M, Y') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Status:</strong>
                                    <p><span class="badge bg-success">{{ $teacher->status }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-calendar"></i> Today's Schedule</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <strong>9:00 AM - 10:00 AM</strong>
                                </div>
                                <p class="text-muted mb-0">Mathematics - Class 10-A</p>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <strong>10:30 AM - 11:30 AM</strong>
                                </div>
                                <p class="text-muted mb-0">Science - Class 9-B</p>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <strong>12:00 PM - 1:00 PM</strong>
                                </div>
                                <p class="text-muted mb-0">Physics - Class 11-A</p>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0"><i class="fas fa-bell"></i> Notifications</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Staff meeting at 2 PM
                            </div>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Grade submission deadline tomorrow
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
