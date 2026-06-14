@extends('admin.layouts.horizontal')
@section('title', 'Attendance Defaulters Report')
@section('content')
<div class="content-card">
    <div class="content-card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-exclamation-triangle me-2"></i>Attendance Defaulters Report</h5>
        <a href="{{ route('admin.attendance.student.reports') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="content-card-body">
        <div class="text-center mb-4">
            <h4>{{ $class->class_name }} - Attendance Defaulters</h4>
            <h6>Period: {{ $start_date }} to {{ $end_date }}</h6>
            <p>Threshold: <strong class="text-danger">{{ $threshold }}%</strong> | Total Days: {{ $total_days }}</p>
        </div>

        @if(count($defaulters) > 0)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>{{ count($defaulters) }}</strong> student(s) found with attendance below {{ $threshold }}%
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Roll No</th>
                            <th>Student Name</th>
                            <th>Admission No</th>
                            <th>Father Name</th>
                            <th>Contact</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Total Days</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($defaulters as $defaulter)
                        <tr>
                            <td>{{ $defaulter['student']->roll_no ?? 'N/A' }}</td>
                            <td>{{ $defaulter['student']->name }}</td>
                            <td>{{ $defaulter['student']->admission_no }}</td>
                            <td>{{ $defaulter['student']->father_name ?? 'N/A' }}</td>
                            <td>{{ $defaulter['student']->contact_number ?? 'N/A' }}</td>
                            <td><span class="badge bg-success">{{ $defaulter['present'] }}</span></td>
                            <td><span class="badge bg-danger">{{ $defaulter['absent'] }}</span></td>
                            <td>{{ $defaulter['total_days'] }}</td>
                            <td>
                                <strong class="text-danger">{{ $defaulter['percentage'] }}%</strong>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <h6>Recommended Actions:</h6>
                <ul>
                    <li>Send SMS/Email notification to parents</li>
                    <li>Schedule parent-teacher meeting</li>
                    <li>Issue warning letter if attendance continues to be low</li>
                    <li>Provide counseling to students</li>
                </ul>
            </div>
        @else
            <div class="alert alert-success text-center">
                <i class="fas fa-check-circle"></i> 
                <strong>Excellent!</strong> No students found with attendance below {{ $threshold }}%
            </div>
        @endif
    </div>
</div>
@endsection
