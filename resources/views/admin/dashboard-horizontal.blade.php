<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MCD Inter College</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-style.css') }}">

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
            font-size: 24px;
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
</head>

<body>
    <!-- Top Header -->
    <div class="top-header">
        <div class="logo-section">
            <div class="logo-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="logo-text">
                <h4>MCD Inter College</h4>
                <p>Management System</p>
            </div>
        </div>

        <div class="header-right">
            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="notification-icon">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">3</span>
            </div>
            <div class="notification-icon">
                <i class="fas fa-envelope"></i>
                <span class="notification-badge">5</span>
            </div>
            <div class="user-profile">
                <div class="user-avatar">{{ strtoupper(substr(auth()->guard('admin')->user()->username ?? 'A', 0, 2)) }}</div>
                <div class="dropdown">
                    <span style="color: #5a5c69; font-weight: 500; cursor: pointer;" id="userDropdown" data-bs-toggle="dropdown">
                        {{ auth()->guard('admin')->user()->username ?? 'Admin' }}
                    </span>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="/admin/profile"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="/admin/settings"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger" style="border: none; background: none; width: 100%; text-align: left; cursor: pointer;">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Horizontal Menu Bar -->
     @extends('admin.layouts.horizontal')
    <!-- <div class="horizontal-menu">
        <div class="menu-container">
            <div class="menu-item">
                <a href="/admin/dashboard" class="menu-link active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="menu-item">
                <a href="#" class="menu-link">
                    <i class="fas fa-user-graduate"></i>
                    <span>Students</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <div class="dropdown-menu-custom">
                    <a href="/admin/students/all" class="dropdown-item-custom">
                        <i class="fas fa-list"></i>
                        <span>All Students</span>
                    </a>
                    <a href="/admin/students/admission" class="dropdown-item-custom">
                        <i class="fas fa-user-plus"></i>
                        <span>Student Admission</span>
                    </a>
                    <a href="/admin/students/admissions" class="dropdown-item-custom">
                        <i class="fas fa-clipboard-list"></i>
                        <span>All Admissions</span>
                    </a>
                    <a href="/admin/students/add" class="dropdown-item-custom">
                        <i class="fas fa-plus-circle"></i>
                        <span>Add Student</span>
                    </a>
                    <a href="/admin/students/strength" class="dropdown-item-custom">
                        <i class="fas fa-chart-bar"></i>
                        <span>Student Strength</span>
                    </a>
                    <a href="/admin/students/promotion" class="dropdown-item-custom">
                        <i class="fas fa-level-up-alt"></i>
                        <span>Student Promotion</span>
                    </a>
                    <a href="/admin/students/details" class="dropdown-item-custom">
                        <i class="fas fa-info-circle"></i>
                        <span>Student Details</span>
                    </a>
                </div>
            </div>

            <div class="menu-item">
                <a href="#" class="menu-link">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Staff</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <div class="dropdown-menu-custom">
                    <a href="/admin/staff/all" class="dropdown-item-custom">
                        <i class="fas fa-users"></i>
                        <span>All Staff</span>
                    </a>
                    <a href="/admin/staff/add" class="dropdown-item-custom">
                        <i class="fas fa-user-plus"></i>
                        <span>Add Staff</span>
                    </a>
                    <a href="/admin/staff/attendance" class="dropdown-item-custom">
                        <i class="fas fa-calendar-check"></i>
                        <span>Staff Attendance</span>
                    </a>
                    <a href="/admin/staff/leave" class="dropdown-item-custom">
                        <i class="fas fa-calendar-times"></i>
                        <span>Leave Management</span>
                    </a>
                </div>
            </div>
           <div class="menu-item">
                <a href="#" class="menu-link {{ request()->is('admin/teachers*') ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i>
                    <span>Teachers</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <div class="dropdown-menu-custom">
                    <a href="/admin/teachers" class="dropdown-item-custom">
                        <i class="fas fa-list"></i>
                        <span>All Teachers</span>
                    </a>
                </div>
            </div>
            <div class="menu-item">
                <a href="#" class="menu-link">
                    <i class="fas fa-calendar-check"></i>
                    <span>Attendance</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <div class="dropdown-menu-custom">
                    <a href="/admin/attendance/student" class="dropdown-item-custom">
                        <i class="fas fa-user-check"></i>
                        <span>Student Attendance</span>
                    </a>
                    <a href="/admin/attendance/staff" class="dropdown-item-custom">
                        <i class="fas fa-user-tie"></i>
                        <span>Staff Attendance</span>
                    </a>
                    <a href="/admin/attendance/report" class="dropdown-item-custom">
                        <i class="fas fa-file-alt"></i>
                        <span>Attendance Report</span>
                    </a>
                </div>
            </div>

            <div class="menu-item">
                <a href="#" class="menu-link">
                    <i class="fas fa-book-open"></i>
                    <span>Academic</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <div class="dropdown-menu-custom">
                    <a href="/admin/classes" class="dropdown-item-custom">
                        <i class="fas fa-school"></i>
                        <span>Classes</span>
                    </a>
                    <a href="/admin/sections" class="dropdown-item-custom">
                        <i class="fas fa-layer-group"></i>
                        <span>Sections</span>
                    </a>
                    <a href="/admin/class-sections" class="dropdown-item-custom">
                        <i class="fas fa-link"></i>
                        <span>Class-Section Management</span>
                    </a>
                    <a href="/admin/subjects" class="dropdown-item-custom">
                        <i class="fas fa-book"></i>
                        <span>Subjects</span>
                    </a>
                    <a href="/admin/sessions" class="dropdown-item-custom">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Sessions</span>
                    </a>
                    <a href="/admin/timetable" class="dropdown-item-custom">
                        <i class="fas fa-clock"></i>
                        <span>Time Table</span>
                    </a>
                </div>
            </div>

            <div class="menu-item">
                <a href="#" class="menu-link">
                    <i class="fas fa-file-alt"></i>
                    <span>Examination</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <div class="dropdown-menu-custom">
                    <a href="/admin/exams" class="dropdown-item-custom">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Exam List</span>
                    </a>
                    <a href="/admin/exams/schedule" class="dropdown-item-custom">
                        <i class="fas fa-calendar"></i>
                        <span>Exam Schedule</span>
                    </a>
                    <a href="/admin/exams/marks" class="dropdown-item-custom">
                        <i class="fas fa-pen"></i>
                        <span>Enter Marks</span>
                    </a>
                    <a href="/admin/exams/results" class="dropdown-item-custom">
                        <i class="fas fa-trophy"></i>
                        <span>Results</span>
                    </a>
                </div>
            </div>

            <div class="menu-item">
                <a href="/admin/library" class="menu-link">
                    <i class="fas fa-book"></i>
                    <span>Library</span>
                </a>
            </div>

            <div class="menu-item">
                <a href="#" class="menu-link">
                    <i class="fas fa-dollar-sign"></i>
                    <span>Fees</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <div class="dropdown-menu-custom">
                    <a href="/admin/fees/collect" class="dropdown-item-custom">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Collect Fees</span>
                    </a>
                    <a href="/admin/fees/structure" class="dropdown-item-custom">
                        <i class="fas fa-list-alt"></i>
                        <span>Fee Structure</span>
                    </a>
                    <a href="/admin/fees/report" class="dropdown-item-custom">
                        <i class="fas fa-chart-line"></i>
                        <span>Fee Report</span>
                    </a>
                </div>
            </div>

            <div class="menu-item">
                <a href="#" class="menu-link">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <div class="dropdown-menu-custom">
                    <a href="/admin/settings/general" class="dropdown-item-custom">
                        <i class="fas fa-sliders-h"></i>
                        <span>General Settings</span>
                    </a>
                    <a href="/admin/settings/school" class="dropdown-item-custom">
                        <i class="fas fa-school"></i>
                        <span>School Info</span>
                    </a>
                    <a href="/admin/settings/users" class="dropdown-item-custom">
                        <i class="fas fa-users-cog"></i>
                        <span>User Management</span>
                    </a>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Dashboard Content -->
    <div class="main-content">
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
                <!-- Today's Birthday - Real Data -->
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
                    <div style="max-height: 400px; overflow-y: auto;">
                        @if($staffAttendanceToday->count() > 0)
                            @foreach($staffAttendanceToday as $attendance)
                            @php
                                $statusColor = match($attendance->status) {
                                    'Present' => '#1cc88a',
                                    'Late' => '#f6c23e',
                                    'Leave' => '#36b9cc',
                                    'Absent' => '#e74a3b',
                                    default => '#858796'
                                };
                                $statusIcon = match($attendance->status) {
                                    'Present' => 'fa-check-circle',
                                    'Late' => 'fa-clock',
                                    'Leave' => 'fa-calendar-times',
                                    'Absent' => 'fa-times-circle',
                                    default => 'fa-question-circle'
                                };
                            @endphp
                            <div style="background: #f8f9fc; padding: 12px; border-radius: 8px; margin-bottom: 8px; border-left: 4px solid {{ $statusColor }};">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <h6 style="margin: 0; color: #5a5c69; font-size: 14px; font-weight: 600;">
                                            {{ $attendance->staff->first_name ?? 'N/A' }} {{ $attendance->staff->last_name ?? '' }}
                                        </h6>
                                        <small style="color: #858796; font-size: 12px;">
                                            <i class="fas fa-clock"></i> 
                                            {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : 'N/A' }}
                                        </small>
                                    </div>
                                    <div>
                                        <span style="background: {{ $statusColor }}; color: white; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600;">
                                            <i class="fas {{ $statusIcon }}"></i> {{ $attendance->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                        <div style="text-align: center; padding: 30px 0; color: #858796;">
                            <i class="fas fa-user-clock" style="font-size: 48px; opacity: 0.3; margin-bottom: 10px;"></i>
                            <p style="margin: 0;">No staff attendance marked today</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <!-- Student Attendance Details Today -->
                <div class="content-card">
                    <div class="content-card-header">
                        <h5>📋 Student Attendance Details</h5>
                        <a href="/admin/attendance/student" class="view-all-link">View All</a>
                    </div>
                    <div style="max-height: 400px; overflow-y: auto;">
                        @if($studentAttendanceDetails->count() > 0)
                            @foreach($studentAttendanceDetails as $attendance)
                            @php
                                $statusColor = match($attendance->status) {
                                    'Present' => '#1cc88a',
                                    'Late' => '#f6c23e',
                                    'Leave' => '#36b9cc',
                                    'Absent' => '#e74a3b',
                                    default => '#858796'
                                };
                                $statusIcon = match($attendance->status) {
                                    'Present' => 'fa-check-circle',
                                    'Late' => 'fa-clock',
                                    'Leave' => 'fa-calendar-times',
                                    'Absent' => 'fa-times-circle',
                                    default => 'fa-question-circle'
                                };
                            @endphp
                            <div style="background: #f8f9fc; padding: 10px; border-radius: 8px; margin-bottom: 6px; border-left: 4px solid {{ $statusColor }};">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="flex: 1;">
                                        <h6 style="margin: 0; color: #5a5c69; font-size: 13px; font-weight: 600;">
                                            {{ $attendance->student->first_name ?? 'N/A' }} {{ $attendance->student->last_name ?? '' }}
                                        </h6>
                                        <small style="color: #858796; font-size: 11px;">
                                            {{ $attendance->student->class->class_name ?? 'N/A' }} - {{ $attendance->student->section->section_name ?? 'N/A' }}
                                        </small>
                                    </div>
                                    <div>
                                        <span style="background: {{ $statusColor }}; color: white; padding: 3px 8px; border-radius: 10px; font-size: 10px; font-weight: 600;">
                                            <i class="fas {{ $statusIcon }}"></i> {{ $attendance->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                        <div style="text-align: center; padding: 30px 0; color: #858796;">
                            <i class="fas fa-clipboard-list" style="font-size: 48px; opacity: 0.3; margin-bottom: 10px;"></i>
                            <p style="margin: 0;">No student attendance marked today</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Class-wise Attendance Table -->
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="content-card">
                    <div class="content-card-header">
                        <h5>📊 Class-wise Attendance Today</h5>
                        <a href="/admin/attendance/student/reports" class="view-all-link">View Reports</a>
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
                                    <th>Attendance %</th>
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
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/admin-script.js') }}"></script>
</body>

</html>