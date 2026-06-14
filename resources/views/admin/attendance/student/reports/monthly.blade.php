@extends('admin.layouts.horizontal')
@section('title', 'Monthly Attendance Report')
@section('content')
<div class="content-card">
    <div class="content-card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-calendar-alt me-2"></i>Monthly Attendance Report</h5>
        <a href="{{ route('admin.attendance.student.reports') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="content-card-body">
        <div class="text-center mb-4">
            <h4>{{ $class->class_name }} - Monthly Attendance</h4>
            <h6>Period: {{ $start_date }} to {{ $end_date }}</h6>
            <p>Total Days: {{ $total_days }}</p>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th rowspan="2">Roll No</th>
                        <th rowspan="2">Student Name</th>
                        @foreach($dates as $date)
                            <th class="text-center">{{ \Carbon\Carbon::parse($date)->format('d') }}</th>
                        @endforeach
                        <th rowspan="2">P</th>
                        <th rowspan="2">A</th>
                        <th rowspan="2">%</th>
                    </tr>
                    <tr>
                        @foreach($dates as $date)
                            <th class="text-center small">{{ \Carbon\Carbon::parse($date)->format('D') }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td>{{ $student->roll_no ?? 'N/A' }}</td>
                        <td>{{ $student->name }}</td>
                        @php
                            $studentAttendance = $attendance[$student->id] ?? collect();
                            $present = 0;
                            $absent = 0;
                        @endphp
                        @foreach($dates as $date)
                            @php
                                $record = $studentAttendance->firstWhere('attendance_date', $date);
                                $status = $record ? $record->status : '-';
                                if ($status === 'Present') $present++;
                                if ($status === 'Absent') $absent++;
                                $cellClass = $status === 'Present' ? 'bg-success text-white' : 
                                            ($status === 'Absent' ? 'bg-danger text-white' : 
                                            ($status === 'Leave' ? 'bg-warning' : ''));
                            @endphp
                            <td class="text-center {{ $cellClass }}">
                                {{ $status === 'Present' ? 'P' : ($status === 'Absent' ? 'A' : ($status === 'Leave' ? 'L' : '-')) }}
                            </td>
                        @endforeach
                        <td><strong>{{ $present }}</strong></td>
                        <td><strong>{{ $absent }}</strong></td>
                        <td><strong>{{ $total_days > 0 ? round(($present / $total_days) * 100, 1) : 0 }}%</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <p><strong>Legend:</strong> P = Present, A = Absent, L = Leave, - = Not Marked</p>
        </div>
    </div>
</div>

@push('styles')
<style>
.table-sm th, .table-sm td { padding: 0.3rem; font-size: 0.85rem; }
</style>
@endpush
@endsection
