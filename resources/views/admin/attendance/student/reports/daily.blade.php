@extends('admin.layouts.horizontal')
@section('title', 'Daily Attendance Report')
@section('content')
<div class="content-card">
    <div class="content-card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-calendar-day me-2"></i>Daily Attendance Report</h5>
        <a href="{{ route('admin.attendance.student.reports') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="content-card-body">
        <div class="text-center mb-4">
            <h4>{{ $class->class_name }} - Daily Attendance</h4>
            <h6>Date: {{ $date }}</h6>
        </div>

        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ $stats['total'] }}</h3>
                        <p class="mb-0">Total Students</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ $stats['present'] }}</h3>
                        <p class="mb-0">Present</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h3>{{ $stats['absent'] }}</h3>
                        <p class="mb-0">Absent</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h3>{{ $stats['leave'] }}</h3>
                        <p class="mb-0">On Leave</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Table -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th>Admission No</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td>{{ $student->roll_no ?? 'N/A' }}</td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->admission_no }}</td>
                        <td>
                            @if(isset($attendance[$student->id]))
                                @php
                                    $status = $attendance[$student->id]->status;
                                    $badgeClass = $status === 'Present' ? 'success' : 
                                                 ($status === 'Absent' ? 'danger' : 
                                                 ($status === 'Leave' ? 'warning' : 'info'));
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ $status }}</span>
                            @else
                                <span class="badge bg-secondary">Not Marked</span>
                            @endif
                        </td>
                        <td>{{ $attendance[$student->id]->remarks ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4">
            <p class="text-muted">
                Attendance Percentage: 
                <strong>{{ $stats['total'] > 0 ? round(($stats['present'] / $stats['total']) * 100, 2) : 0 }}%</strong>
            </p>
        </div>
    </div>
</div>
@endsection
