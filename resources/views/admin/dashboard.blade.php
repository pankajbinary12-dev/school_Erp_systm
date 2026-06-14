@extends('admin.layouts.horizontal')

@section('title', 'Admin Dashboard')

@push('styles')
<style>
    /* Dashboard Specific Styles */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border-left: 4px solid;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .stat-card.primary {
        border-left-color: var(--primary-color);
    }

    .stat-card.success {
        border-left-color: var(--success-color);
    }

    .stat-card.info {
        border-left-color: var(--info-color);
    }

    .stat-card.warning {
        border-left-color: var(--warning-color);
    }

    .stat-card-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-info h6 {
        color: #858796;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .stat-info h2 {
        color: #5a5c69;
        font-size: 32px;
        font-weight: 700;
        margin: 0;
    }

    .stat-info small {
        color: #1cc88a;
        font-size: 12px;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;no
        opacity: 0.2;
    }

    .stat-card.primary .stat-icon {
        background: var(--primary-color);
        color: var(--primary-color);
    }

    .stat-card.success .stat-icon {
        background: var(--success-color);
        color: var(--success-color);
    }

    .stat-card.info .stat-icon {
        background: var(--info-color);
        color: var(--info-color);
    }

    .stat-card.warning .stat-icon {
        background: var(--warning-color);
        color: var(--warning-color);
    }

    .view-all-link {
        color: var(--primary-color);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
    }

    .view-all-link:hover {
        text-decoration: underline;
    }

    .birthday-item {
        background: linear-gradient(135deg, #ffeef8 0%, #ffe8f0 100%);
        border-left: 4px solid #ff6b9d;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .birthday-icon {
        width: 40px;
        height: 40px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ff6b9d;
    }

    .birthday-info h6 {
        margin: 0;
        color: #5a5c69;
        font-size: 14px;
        font-weight: 600;
    }

    .birthday-info small {
        color: #858796;
        font-size: 12px;
    }

    .leave-request-item {
        background: #f8f9fc;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .leave-badge {
        background: var(--primary-color);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .stats-row {
            grid-template-columns: 1fr;
        }

        .stat-info h2 {
            font-size: 24px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }
    }
</style>
@endpush

@section('content')
<!-- Stats Cards Row 1: Basic Stats -->
<div class="stats-row">
    <div class="stat-card primary">
        <div class="stat-card-content">
            <div class="stat-info">
                <h6>Total Students</h6>
                <h2>{{ $stats['total_students'] }}</h2>
                <small><i class="fas fa-users"></i> Active Students</small>
            </div>
            <div class="stat-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-card-content">
            <div class="stat-info">
                <h6>Total Staff</h6>
                <h2>{{ $stats['total_teachers'] }}</h2>
                <small><i class="fas fa-chalkboard-teacher"></i> Teachers</small>
            </div>
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="stat-card info">
        <div class="stat-card-content">
            <div class="stat-info">
                <h6>Present Today</h6>
                <h2>{{ $attendanceStats['present_today'] }}</h2>
                <small><i class="fas fa-user-check"></i> Students</small>
            </div>
            <div class="stat-icon">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-card-content">
            <div class="stat-info">
                <h6>Attendance Rate</h6>
                <h2>{{ $attendanceStats['attendance_percentage'] }}%</h2>
                <small><i class="fas fa-chart-line"></i> Today</small>
            </div>
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards Row 2: Examination Stats -->
<div class="stats-row">
    <div class="stat-card primary">
        <div class="stat-card-content">
            <div class="stat-info">
                <h6>Total Exams</h6>
                <h2>{{ $examStats['total_exams'] }}</h2>
                <small><i class="fas fa-clipboard-list"></i> All Exams</small>
            </div>
            <div class="stat-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-card-content">
            <div class="stat-info">
                <h6>Ongoing Exams</h6>
                <h2>{{ $examStats['ongoing_exams'] }}</h2>
                <small><i class="fas fa-hourglass-half"></i> In Progress</small>
            </div>
            <div class="stat-icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-card-content">
            <div class="stat-info">
                <h6>Completed Exams</h6>
                <h2>{{ $examStats['completed_exams'] }}</h2>
                <small><i class="fas fa-check-circle"></i> Finished</small>
            </div>
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="stat-card info">
        <div class="stat-card-content">
            <div class="stat-info">
                <h6>Published Results</h6>
                <h2>{{ $examStats['published_results'] }}</h2>
                <small><i class="fas fa-trophy"></i> Results Out</small>
            </div>
            <div class="stat-icon">
                <i class="fas fa-trophy"></i>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards Row 3: Fee Stats -->
<div class="stats-row">
    <div class="stat-card success">
        <div class="stat-card-content">
            <div class="stat-info">
                <h6>Total Fee Collected</h6>
                <h2>₹{{ number_format($feeStats['total_fee_collected'], 0) }}</h2>
                <small><i class="fas fa-rupee-sign"></i> Collected</small>
            </div>
            <div class="stat-icon">
                <i class="fas fa-rupee-sign"></i>
            </div>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-card-content">
            <div class="stat-info">
                <h6>Pending Fees</h6>
                <h2>₹{{ number_format($feeStats['pending_fees'], 0) }}</h2>
                <small><i class="fas fa-exclamation-triangle"></i> Due</small>
            </div>
            <div class="stat-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>

    <div class="stat-card info">
        <div class="stat-card-content">
            <div class="stat-info">
                <h6>Students with Fees</h6>
                <h2>{{ $feeStats['total_students_with_fees'] }}</h2>
                <small><i class="fas fa-users"></i> Total</small>
            </div>
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="stat-card primary">
        <div class="stat-card-content">
            <div class="stat-info">
                <h6>Paid Students</h6>
                <h2>{{ $feeStats['paid_students'] }}</h2>
                <small><i class="fas fa-check"></i> Completed</small>
            </div>
            <div class="stat-icon">
                <i class="fas fa-check"></i>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards Row 4: Additional Student Stats -->
<div class="stats-row">
    <div class="stat-card primary">
        <div class="stat-card-content">
            <div class="stat-info">
                <h6>New Admissions</h6>
                <h2>{{ $studentStats['new_admissions_this_month'] }}</h2>
                <small><i class="fas fa-calendar"></i> This Month</small>
            </div>
            <div class="stat-icon">
                <i class="fas fa-user-plus"></i>
            </div>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-card-content">
            <div class="stat-info">
                <h6>Fee Defaulters</h6>
                <h2>{{ $studentStats['students_with_pending_fees'] }}</h2>
                <small><i class="fas fa-exclamation-circle"></i> Pending</small>
            </div>
            <div class="stat-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
        </div>
    </div>

    <div class="stat-card info">
        <div class="stat-card-content">
            <div class="stat-info">
                <h6>Male Students</h6>
                <h2>{{ $studentStats['total_male_students'] }}</h2>
                <small><i class="fas fa-male"></i> Active</small>
            </div>
            <div class="stat-icon">
                <i class="fas fa-male"></i>
            </div>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-card-content">
            <div class="stat-info">
                <h6>Female Students</h6>
                <h2>{{ $studentStats['total_female_students'] }}</h2>
                <small><i class="fas fa-female"></i> Active</small>
            </div>
            <div class="stat-icon">
                <i class="fas fa-female"></i>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-lg-4 mb-4">
        <!-- Today's Birthday -->
        <div class="content-card">
            <div class="content-card-header">
                <h5>🎂 Today's Birthday</h5>
                <a href="#" class="view-all-link">View All</a>
            </div>
            <div style="max-height: 400px; overflow-y: auto;">
                @if($todayBirthdays->count() > 0)
                    @foreach($todayBirthdays as $student)
                    <div class="birthday-item">
                        <div class="birthday-icon">
                            <i class="fas fa-birthday-cake"></i>
                        </div>
                        <div class="birthday-info">
                            <h6>{{ $student->first_name }} {{ $student->last_name }}</h6>
                            <small>{{ $student->class->class_name ?? 'N/A' }} - {{ $student->section->section_name ?? 'N/A' }}</small>
                            <small style="display: block; color: #ff6b9d;">
                                <i class="fas fa-gift"></i> {{ \Carbon\Carbon::parse($student->date_of_birth)->format('d M') }}
                            </small>
                        </div>
                    </div>
                    @endforeach
                @else
                <div style="text-align: center; padding: 30px 0; color: #858796;">
                    <i class="fas fa-birthday-cake" style="font-size: 48px; opacity: 0.3; margin-bottom: 10px;"></i>
                    <p style="margin: 0;">No birthdays today</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <!-- Staff Attendance Today -->
        <div class="content-card">
            <div class="content-card-header">
                <h5>👥 Staff Attendance Today</h5>
                <a href="/admin/attendance/staff" class="view-all-link">View All</a>
            </div>
            <div class="leave-request-container">
                @if($staffAttendanceToday->count() > 0)
                    @foreach($staffAttendanceToday as $attendance)
                    @php
                        $statusColor = match($attendance->status) {
                            'Present' => '#1cc88a',
                            'Absent' => '#e74a3b',
                            'Leave' => '#f6c23e',
                            'Late' => '#36b9cc',
                            default => '#858796'
                        };
                    @endphp
                    <div class="leave-request-item">
                        <div>
                            <h6 style="margin: 0; color: #5a5c69; font-size: 14px; font-weight: 600;">
                                {{ $attendance->staff->name ?? 'N/A' }}
                            </h6>
                            <small style="color: #858796;">
                                {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : 'Not checked in' }}
                            </small>
                        </div>
                        <span class="leave-badge" style="background: {{ $statusColor }};">
                            {{ $attendance->status }}
                        </span>
                    </div>
                    @endforeach
                @else
                <div style="text-align: center; padding: 30px 0; color: #858796;">
                    <i class="fas fa-user-clock" style="font-size: 48px; opacity: 0.3; margin-bottom: 10px;"></i>
                    <p style="margin: 0;">No attendance records today</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <!-- Leave Requests -->
        <div class="content-card">
            <div class="content-card-header">
                <h5>📝 Leave Requests</h5>
                <a href="/admin/staff/leave" class="view-all-link">View All</a>
            </div>
            <div class="leave-request-container">
                @if($leaveRequests->count() > 0)
                    @foreach($leaveRequests as $leave)
                    <div class="leave-request-item">
                        <div>
                            <h6 style="margin: 0; color: #5a5c69; font-size: 14px; font-weight: 600;">
                                {{ $leave->staff->name ?? 'N/A' }}
                            </h6>
                            <small style="color: #858796;">
                                {{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} - 
                                {{ \Carbon\Carbon::parse($leave->end_date)->format('d M') }}
                            </small>
                        </div>
                        <span class="leave-badge" style="background: {{ $leave->status == 'Pending' ? '#f6c23e' : ($leave->status == 'Approved' ? '#1cc88a' : '#e74a3b') }};">
                            {{ $leave->status }}
                        </span>
                    </div>
                    @endforeach
                @else
                <div style="text-align: center; padding: 30px 0; color: #858796;">
                    <i class="fas fa-clipboard-list" style="font-size: 48px; opacity: 0.3; margin-bottom: 10px;"></i>
                    <p style="margin: 0;">No leave requests</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Class-wise Attendance Table -->
<div class="row">
    <div class="col-12">
        <div class="content-card">
            <div class="content-card-header">
                <h5>📊 Class-wise Attendance Today</h5>
                <a href="/admin/attendance/student" class="view-all-link">View Details</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead style="background: #f8f9fc;">
                        <tr>
                            <th>Class</th>
                            <th>Total</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Leave</th>
                            <th>Late</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($classWiseAttendance->count() > 0)
                            @foreach($classWiseAttendance as $classAtt)
                            @php
                                $percentage = $classAtt->total > 0 ? round(($classAtt->present / $classAtt->total) * 100, 2) : 0;
                                $percentageColor = $percentage >= 90 ? '#1cc88a' : ($percentage >= 75 ? '#f6c23e' : '#e74a3b');
                            @endphp
                            <tr>
                                <td><strong>{{ $classAtt->class->class_name ?? 'N/A' }}</strong></td>
                                <td>{{ $classAtt->total }}</td>
                                <td><span style="color: #1cc88a; font-weight: 600;"><i class="fas fa-check-circle"></i> {{ $classAtt->present }}</span></td>
                                <td><span style="color: #e74a3b; font-weight: 600;"><i class="fas fa-times-circle"></i> {{ $classAtt->absent }}</span></td>
                                <td><span style="color: #36b9cc; font-weight: 600;"><i class="fas fa-calendar-times"></i> {{ $classAtt->on_leave }}</span></td>
                                <td><span style="color: #f6c23e; font-weight: 600;"><i class="fas fa-clock"></i> {{ $classAtt->late }}</span></td>
                                <td>
                                    <span style="background: {{ $percentageColor }}; color: white; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                        {{ $percentage }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 30px; color: #858796;">
                                    <i class="fas fa-info-circle"></i> No attendance data available for today
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
