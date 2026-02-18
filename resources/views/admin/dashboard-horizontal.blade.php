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
    <div class="horizontal-menu">
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
    </div>

    <!-- Dashboard Content -->
    <div class="main-content">
        <!-- Stats Cards -->
        <div class="stats-row">
            <div class="stat-card primary">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <h6>Total Students</h6>
                        <h2>{{ $stats['total_students'] }}</h2>
                        <small><i class="fas fa-arrow-up"></i> 5.2%</small>
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
                        <small><i class="fas fa-arrow-up"></i> 2.1%</small>
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
                        <h2>{{ $stats['total_students'] }}</h2>
                        <small><i class="fas fa-arrow-up"></i> 1.5%</small>
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
                        <h2>100%</h2>
                        <small><i class="fas fa-arrow-up"></i> 0.8%</small>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row -->
        <div class="row">
            <div class="col-lg-4 mb-4">
                <!-- Today's Leave Request -->
                <div class="content-card">
                    <div class="content-card-header">
                        <h5>Today's Leave Request</h5>
                        <a href="#" class="view-all-link">View All</a>
                    </div>
                    <!-- Scrollable container -->
                    <div class="leave-request-container">
                        <div class="leave-request-item">
                            <div>
                                <i class="fas fa-file-alt" style="color: var(--primary-color); margin-right: 10px;"></i>
                                <span style="font-weight: 500;">Leave Request</span>
                            </div>
                            <span class="leave-badge">3</span>
                        </div>

                        <div class="leave-request-item">
                            <div>
                                <i class="fas fa-file-alt" style="color: var(--primary-color); margin-right: 10px;"></i>
                                <span style="font-weight: 500;">Leave Request</span>
                            </div>
                            <span class="leave-badge">3</span>
                        </div>

                        <div class="leave-request-item">
                            <div>
                                <i class="fas fa-file-alt" style="color: var(--primary-color); margin-right: 10px;"></i>
                                <span style="font-weight: 500;">Leave Request</span>
                            </div>
                            <span class="leave-badge">3</span>
                        </div>

                        <div class="leave-request-item">
                            <div>
                                <i class="fas fa-file-alt" style="color: var(--primary-color); margin-right: 10px;"></i>
                                <span style="font-weight: 500;">Leave Request</span>
                            </div>
                            <span class="leave-badge">3</span>
                        </div>

                        <div class="leave-request-item">
                            <div>
                                <i class="fas fa-file-alt" style="color: var(--primary-color); margin-right: 10px;"></i>
                                <span style="font-weight: 500;">Leave Request</span>
                            </div>
                            <span class="leave-badge">3</span>
                        </div>

                        <div class="leave-request-item">
                            <div>
                                <i class="fas fa-file-alt" style="color: var(--primary-color); margin-right: 10px;"></i>
                                <span style="font-weight: 500;">Leave Request</span>
                            </div>
                            <span class="leave-badge">3</span>
                        </div>

                        <div class="leave-request-item">
                            <div>
                                <i class="fas fa-file-alt" style="color: var(--primary-color); margin-right: 10px;"></i>
                                <span style="font-weight: 500;">Leave Request</span>
                            </div>
                            <span class="leave-badge">3</span>
                        </div>



                    </div>
                </div>

                <!-- Absent Staff -->
                <div class="content-card">
                    <div class="content-card-header">
                        <h5>Absent Staff</h5>
                        <a href="#" class="view-all-link">View All</a>
                    </div>
                    <div style="text-align: center; padding: 30px 0; color: #858796;">
                        <i class="fas fa-users" style="font-size: 48px; opacity: 0.3; margin-bottom: 10px;"></i>
                        <p style="margin: 0;">No absent staff today</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <!-- Today's Birthday -->
                <div class="content-card">
                    <div class="content-card-header">
                        <h5>Today's Birthday</h5>
                        <a href="#" class="view-all-link">View All</a>
                    </div>
                    @if($recent_students->count() > 0)
                    @foreach($recent_students->take(3) as $student)
                    <div class="birthday-item">
                        <div class="birthday-icon">
                            <i class="fas fa-birthday-cake"></i>
                        </div>
                        <div class="birthday-info">
                            <h6>{{ $student->first_name }} {{ $student->last_name }}</h6>
                            <small>{{ $student->class->class_name ?? 'N/A' }} - {{ $student->section->section_name ?? 'N/A' }}</small>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/admin-script.js') }}"></script>
</body>

</html>