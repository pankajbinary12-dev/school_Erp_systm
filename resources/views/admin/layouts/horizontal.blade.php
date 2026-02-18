<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - MCD Inter College</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-style.css') }}">
    @stack('styles')
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
                        <li><hr class="dropdown-divider"></li>
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
                <a href="/admin/dashboard" class="menu-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="menu-item">
                <a href="#" class="menu-link {{ request()->is('admin/students*') ? 'active' : '' }}">
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
                <a href="#" class="menu-link {{ request()->is('admin/staff*') ? 'active' : '' }}">
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
                <a href="#" class="menu-link {{ request()->is('admin/attendance*') ? 'active' : '' }}">
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
                <a href="#" class="menu-link {{ request()->is('admin/classes*') || request()->is('admin/sections*') || request()->is('admin/subjects*') || request()->is('admin/sessions*') ? 'active' : '' }}">
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
                        <span>Class-Section Assignment</span>
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
                <a href="#" class="menu-link {{ request()->is('admin/exams*') ? 'active' : '' }}">
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
                <a href="/admin/library" class="menu-link {{ request()->is('admin/library*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>Library</span>
                </a>
            </div>

            <div class="menu-item">
                <a href="#" class="menu-link {{ request()->is('admin/fees*') ? 'active' : '' }}">
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
                <a href="#" class="menu-link {{ request()->is('admin/settings*') ? 'active' : '' }}">
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
                    <div class="dropdown-divider"></div>
                    <a href="/admin/settings/roles" class="dropdown-item-custom">
                        <i class="fas fa-user-tag"></i>
                        <span>Roles Management</span>
                    </a>
                    <a href="/admin/settings/permissions" class="dropdown-item-custom">
                        <i class="fas fa-key"></i>
                        <span>Permissions Management</span>
                    </a>
                    <a href="/admin/settings/assign-permissions" class="dropdown-item-custom">
                        <i class="fas fa-link"></i>
                        <span>Assign Permissions</span>
                    </a>
                    <a href="/admin/settings/assign-roles" class="dropdown-item-custom">
                        <i class="fas fa-users-cog"></i>
                        <span>Assign Roles to Users</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/admin-script.js') }}"></script>
    @stack('scripts')
</body>
</html>
