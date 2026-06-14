@extends('student.layout')

@section('title', 'Attendance')
@section('page-title', 'My Attendance')

@section('content')
<div class="row">
    <!-- Summary Cards -->
    <div class="col-md-3 mb-4">
        <div class="stat-card success">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h6 style="color: #858796; font-size: 12px; font-weight: 700; text-transform: uppercase;">Present Days</h6>
            <h3 style="color: #5a5c69; font-size: 28px; font-weight: 700;">{{ $present }}</h3>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stat-card danger">
            <div class="stat-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <h6 style="color: #858796; font-size: 12px; font-weight: 700; text-transform: uppercase;">Absent Days</h6>
            <h3 style="color: #5a5c69; font-size: 28px; font-weight: 700;">{{ $absent }}</h3>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stat-card info">
            <div class="stat-icon">
                <i class="fas fa-calendar-times"></i>
            </div>
            <h6 style="color: #858796; font-size: 12px; font-weight: 700; text-transform: uppercase;">Leave Days</h6>
            <h3 style="color: #5a5c69; font-size: 28px; font-weight: 700;">{{ $leave }}</h3>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="fas fa-percentage"></i>
            </div>
            <h6 style="color: #858796; font-size: 12px; font-weight: 700; text-transform: uppercase;">Attendance %</h6>
            <h3 style="color: #5a5c69; font-size: 28px; font-weight: 700;">{{ $percentage }}%</h3>
        </div>
    </div>
</div>

<!-- Attendance Records -->
<div class="row">
    <div class="col-12">
        <div class="stat-card">
            <h5 style="color: #5a5c69; font-weight: 700; margin-bottom: 20px;">
                <i class="fas fa-calendar-alt me-2"></i>Monthly Attendance Records
            </h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead style="background: #f8f9fc;">
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceRecords as $record)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($record->attendance_date)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($record->attendance_date)->format('l') }}</td>
                            <td>
                                <span class="badge" style="background: 
                                    @if($record->status == 'Present') #1cc88a
                                    @elseif($record->status == 'Absent') #e74a3b
                                    @elseif($record->status == 'Leave') #36b9cc
                                    @else #f6c23e @endif; color: white; padding: 6px 12px;">
                                    {{ $record->status }}
                                </span>
                            </td>
                            <td>{{ $record->remarks ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center" style="padding: 40px;">
                                <i class="fas fa-calendar-times" style="font-size: 48px; opacity: 0.3; color: #858796;"></i>
                                <p style="color: #858796; margin-top: 10px;">No attendance records found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
