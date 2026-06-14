@extends('student.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Stats Cards Row 1 -->
    <div class="col-md-3 mb-4">
        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="fas fa-school"></i>
            </div>
            <h6 style="color: #858796; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 5px;">Class & Section</h6>
            <h3 style="color: #5a5c69; font-size: 24px; font-weight: 700; margin: 0;">
                {{ $student->class->class_name ?? 'N/A' }} - {{ $student->section->section_name ?? 'N/A' }}
            </h3>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stat-card success">
            <div class="stat-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <h6 style="color: #858796; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 5px;">Roll Number</h6>
            <h3 style="color: #5a5c69; font-size: 24px; font-weight: 700; margin: 0;">
                {{ $student->roll_no ?? 'N/A' }}
            </h3>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stat-card info">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <h6 style="color: #858796; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 5px;">Monthly Attendance</h6>
            <h3 style="color: #5a5c69; font-size: 24px; font-weight: 700; margin: 0;">
                {{ $monthlyPercentage }}%
            </h3>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stat-card warning">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <h6 style="color: #858796; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 5px;">Overall Attendance</h6>
            <h3 style="color: #5a5c69; font-size: 24px; font-weight: 700; margin: 0;">
                {{ $overallPercentage }}%
            </h3>
        </div>
    </div>
</div>

<!-- Stats Cards Row 2 -->
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="stat-card {{ $todayAttendance ? ($todayAttendance->status == 'Present' ? 'success' : 'danger') : 'warning' }}">
            <div class="stat-icon">
                <i class="fas fa-user-check"></i>
            </div>
            <h6 style="color: #858796; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 5px;">Today's Status</h6>
            <h3 style="color: #5a5c69; font-size: 20px; font-weight: 700; margin: 0;">
                {{ $todayAttendance ? $todayAttendance->status : 'Not Marked' }}
            </h3>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stat-card {{ $dueFee > 0 ? 'danger' : 'success' }}">
            <div class="stat-icon">
                <i class="fas fa-rupee-sign"></i>
            </div>
            <h6 style="color: #858796; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 5px;">Fees Due</h6>
            <h3 style="color: #5a5c69; font-size: 24px; font-weight: 700; margin: 0;">
                ₹{{ number_format($dueFee, 0) }}
            </h3>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <h6 style="color: #858796; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 5px;">Pending Assignments</h6>
            <h3 style="color: #5a5c69; font-size: 24px; font-weight: 700; margin: 0;">
                {{ $pendingAssignments }}
            </h3>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stat-card info">
            <div class="stat-icon">
                <i class="fas fa-bell"></i>
            </div>
            <h6 style="color: #858796; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 5px;">Notifications</h6>
            <h3 style="color: #5a5c69; font-size: 24px; font-weight: 700; margin: 0;">
                {{ $unreadNotifications }}
            </h3>
        </div>
    </div>
</div>

<!-- Latest Result & Recent Notifications -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="stat-card primary" style="height: 100%;">
            <h5 style="color: #5a5c69; font-weight: 700; margin-bottom: 20px;">
                <i class="fas fa-trophy me-2"></i>Latest Result
            </h5>
            @if($latestResult)
                <div style="background: #f8f9fc; padding: 20px; border-radius: 10px;">
                    <h6 style="color: #5a5c69; font-weight: 600;">{{ $latestResult->exam->exam_name ?? 'N/A' }}</h6>
                    <div class="row mt-3">
                        <div class="col-4">
                            <small style="color: #858796;">Total Marks</small>
                            <h4 style="color: #5a5c69; font-weight: 700;">{{ $latestResult->total_marks }}</h4>
                        </div>
                        <div class="col-4">
                            <small style="color: #858796;">Percentage</small>
                            <h4 style="color: #5a5c69; font-weight: 700;">{{ $latestResult->percentage }}%</h4>
                        </div>
                        <div class="col-4">
                            <small style="color: #858796;">Grade</small>
                            <h4 style="color: #5a5c69; font-weight: 700;">{{ $latestResult->grade }}</h4>
                        </div>
                    </div>
                    <a href="{{ route('student.results') }}" class="btn btn-sm btn-primary mt-3">
                        <i class="fas fa-eye me-1"></i>View All Results
                    </a>
                </div>
            @else
                <div style="text-align: center; padding: 40px; color: #858796;">
                    <i class="fas fa-trophy" style="font-size: 48px; opacity: 0.3; margin-bottom: 10px;"></i>
                    <p style="margin: 0;">No results available yet</p>
                </div>
            @endif
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="stat-card warning" style="height: 100%;">
            <h5 style="color: #5a5c69; font-weight: 700; margin-bottom: 20px;">
                <i class="fas fa-bell me-2"></i>Recent Notifications
            </h5>
            @if($recentNotifications->count() > 0)
                <div style="max-height: 300px; overflow-y: auto;">
                    @foreach($recentNotifications as $notification)
                        <div style="background: #f8f9fc; padding: 12px; border-radius: 8px; margin-bottom: 10px; border-left: 4px solid 
                            @if($notification->type == 'success') #1cc88a
                            @elseif($notification->type == 'warning') #f6c23e
                            @elseif($notification->type == 'danger') #e74a3b
                            @else #36b9cc @endif;">
                            <h6 style="margin: 0; color: #5a5c69; font-size: 14px; font-weight: 600;">
                                {{ $notification->title }}
                            </h6>
                            <small style="color: #858796; font-size: 12px;">
                                {{ $notification->message }}
                            </small>
                            <br>
                            <small style="color: #858796; font-size: 11px;">
                                <i class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('student.notifications') }}" class="btn btn-sm btn-warning mt-3">
                    <i class="fas fa-eye me-1"></i>View All Notifications
                </a>
            @else
                <div style="text-align: center; padding: 40px; color: #858796;">
                    <i class="fas fa-bell" style="font-size: 48px; opacity: 0.3; margin-bottom: 10px;"></i>
                    <p style="margin: 0;">No notifications</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="stat-card info">
            <h5 style="color: #5a5c69; font-weight: 700; margin-bottom: 20px;">
                <i class="fas fa-bolt me-2"></i>Quick Actions
            </h5>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <a href="{{ route('student.attendance') }}" class="btn btn-outline-primary w-100" style="padding: 15px;">
                        <i class="fas fa-calendar-check d-block mb-2" style="font-size: 24px;"></i>
                        View Attendance
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('student.assignments') }}" class="btn btn-outline-success w-100" style="padding: 15px;">
                        <i class="fas fa-tasks d-block mb-2" style="font-size: 24px;"></i>
                        Submit Assignment
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('student.results') }}" class="btn btn-outline-info w-100" style="padding: 15px;">
                        <i class="fas fa-trophy d-block mb-2" style="font-size: 24px;"></i>
                        View Results
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('student.timetable') }}" class="btn btn-outline-warning w-100" style="padding: 15px;">
                        <i class="fas fa-clock d-block mb-2" style="font-size: 24px;"></i>
                        View Timetable
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
