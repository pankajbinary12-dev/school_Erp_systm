@extends('admin.layouts.horizontal')
@section('title', 'Student-wise Attendance Report')
@section('content')
<div class="content-card">
    <div class="content-card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-user-graduate me-2"></i>Student-wise Attendance Report</h5>
        <a href="{{ route('admin.attendance.student.reports') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="content-card-body">
        <div class="text-center mb-4">
            <h4>{{ $class->class_name }} - Student-wise Attendance</h4>
            <h6>Period: {{ $start_date }} to {{ $end_date }}</h6>
            <p>Total Days: {{ $total_days }}</p>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th>Admission No</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Leave</th>
                        <th>Total Days</th>
                        <th>Percentage</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($studentStats as $stat)
                    <tr>
                        <td>{{ $stat['student']->roll_no ?? 'N/A' }}</td>
                        <td>{{ $stat['student']->name }}</td>
                        <td>{{ $stat['student']->admission_no }}</td>
                        <td><span class="badge bg-success">{{ $stat['present'] }}</span></td>
                        <td><span class="badge bg-danger">{{ $stat['absent'] }}</span></td>
                        <td><span class="badge bg-warning">{{ $stat['leave'] }}</span></td>
                        <td>{{ $stat['total_days'] }}</td>
                        <td><strong>{{ $stat['percentage'] }}%</strong></td>
                        <td>
                            @if($stat['percentage'] >= 75)
                                <span class="badge bg-success">Good</span>
                            @elseif($stat['percentage'] >= 60)
                                <span class="badge bg-warning">Average</span>
                            @else
                                <span class="badge bg-danger">Poor</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <p><strong>Note:</strong> Percentage is calculated as (Present Days / Total Days) × 100</p>
        </div>
    </div>
</div>
@endsection
