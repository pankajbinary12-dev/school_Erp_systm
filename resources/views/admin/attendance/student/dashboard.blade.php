@extends('admin.layouts.horizontal')
@section('title', 'Student Attendance Dashboard')
@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-tachometer-alt me-2"></i>Student Attendance Dashboard</h5>
    </div>
    <div class="content-card-body">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Students</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_students'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Present Today</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['present_today'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-check fa-2x text-gray-300"></i>
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
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Absent Today</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['absent_today'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-times fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Attendance %</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['attendance_percentage'] }}%</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-percentage fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="mb-0 font-weight-bold text-primary">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <a href="{{ route('admin.attendance.student.mark') }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-plus"></i> Mark Attendance
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.attendance.student.view') }}" class="btn btn-success btn-block">
                                    <i class="fas fa-eye"></i> View Attendance
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.attendance.student.reports') }}" class="btn btn-info btn-block">
                                    <i class="fas fa-file-alt"></i> Reports
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.attendance.student.reports') }}?type=defaulters" class="btn btn-warning btn-block">
                                    <i class="fas fa-exclamation-triangle"></i> Defaulters
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class-wise Today's Attendance -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Class-wise Today's Attendance</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Class</th>
                                <th>Total</th>
                                <th>Present</th>
                                <th>Absent</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classWiseAttendance as $attendance)
                            <tr>
                                <td>{{ $attendance->class->class_name }}</td>
                                <td>{{ $attendance->total }}</td>
                                <td><span class="badge bg-success">{{ $attendance->present }}</span></td>
                                <td><span class="badge bg-danger">{{ $attendance->absent }}</span></td>
                                <td>
                                    @php
                                        $percentage = $attendance->total > 0 ? round(($attendance->present / $attendance->total) * 100, 2) : 0;
                                    @endphp
                                    <strong>{{ $percentage }}%</strong>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No attendance marked today</td>
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
.border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }
.btn-block { display: block; width: 100%; }
</style>
@endpush
@endsection
