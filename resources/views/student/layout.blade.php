<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Dashboard') - School ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --sidebar-width: 260px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fc;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: var(--primary-color);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 10px;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: all 0.3s;
        }

        .mobile-menu-toggle:hover {
            background: var(--secondary-color);
            transform: scale(1.05);
        }

        .mobile-menu-toggle:active {
            transform: scale(0.95);
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h4 {
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 5px 0;
            color: white;
        }

        .sidebar-header small {
            font-size: 12px;
            opacity: 0.8;
            color: white;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(255,255,255,0.1);
            color: white;
            padding-left: 30px;
        }

        .menu-item i {
            width: 25px;
            margin-right: 10px;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s;
        }

        /* Top Header */
        .top-header {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        /* Content Area */
        .content-area {
            padding: 30px;
        }

        /* Cards */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .stat-card.primary { border-left-color: var(--primary-color); }
        .stat-card.success { border-left-color: var(--success-color); }
        .stat-card.info { border-left-color: var(--info-color); }
        .stat-card.warning { border-left-color: var(--warning-color); }
        .stat-card.danger { border-left-color: var(--danger-color); }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .stat-card.primary .stat-icon { background: rgba(102, 126, 234, 0.1); color: var(--primary-color); }
        .stat-card.success .stat-icon { background: rgba(28, 200, 138, 0.1); color: var(--success-color); }
        .stat-card.info .stat-icon { background: rgba(54, 185, 204, 0.1); color: var(--info-color); }
        .stat-card.warning .stat-icon { background: rgba(246, 194, 62, 0.1); color: var(--warning-color); }
        .stat-card.danger .stat-icon { background: rgba(231, 74, 59, 0.1); color: var(--danger-color); }

        /* Responsive Design */
        @media (max-width: 992px) {
            .content-area {
                padding: 20px;
            }
            
            .stat-card {
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .top-header {
                padding: 15px 70px 15px 70px;
            }

            .top-header h5 {
                font-size: 16px;
            }

            .user-info {
                gap: 10px;
            }

            .user-info div {
                display: none;
            }

            .content-area {
                padding: 15px;
            }

            .stat-card {
                padding: 15px;
                margin-bottom: 15px;
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
        }

        @media (max-width: 480px) {
            .top-header {
                padding: 12px 60px 12px 60px;
            }

            .top-header h5 {
                font-size: 14px;
            }

            .user-avatar {
                width: 35px;
                height: 35px;
                font-size: 14px;
            }

            .content-area {
                padding: 10px;
            }

            .stat-card {
                padding: 12px;
            }

            .stat-card h6 {
                font-size: 14px;
            }

            .stat-card h3 {
                font-size: 20px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Mobile Menu Toggle Button -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            @php
                $student = \App\Models\Student::find(session('student_id'));
            @endphp
            
            @if($student && $student->photo)
                <img src="{{ asset('storage/' . $student->photo) }}" alt="Profile" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 15px; border: 3px solid rgba(255,255,255,0.3);">
            @else
                <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.2); color: white; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 700; margin-bottom: 15px; border: 3px solid rgba(255,255,255,0.3);">
                    {{ strtoupper(substr(session('student_name'), 0, 2)) }}
                </div>
            @endif
            
            <h4>{{ session('student_name') }}</h4>
            @if($student)
                <small>{{ $student->admission_no ?? 'Student' }}</small>
                <small style="display: block; margin-top: 5px;">{{ $student->class->class_name ?? '' }} - {{ $student->section->section_name ?? '' }}</small>
            @else
                <small>Student Portal</small>
            @endif
        </div>
        <div class="sidebar-menu">
            <a href="{{ route('student.dashboard') }}" class="menu-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('student.profile') }}" class="menu-item {{ request()->routeIs('student.profile') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span>My Profile</span>
            </a>
            <a href="{{ route('student.attendance') }}" class="menu-item {{ request()->routeIs('student.attendance') ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i>
                <span>Attendance</span>
            </a>
            <a href="{{ route('student.subjects') }}" class="menu-item {{ request()->routeIs('student.subjects') ? 'active' : '' }}">
                <i class="fas fa-book"></i>
                <span>Subjects</span>
            </a>
            <a href="{{ route('student.assignments') }}" class="menu-item {{ request()->routeIs('student.assignments') ? 'active' : '' }}">
                <i class="fas fa-tasks"></i>
                <span>Assignments</span>
            </a>
            <a href="{{ route('student.results') }}" class="menu-item {{ request()->routeIs('student.results') ? 'active' : '' }}">
                <i class="fas fa-trophy"></i>
                <span>Results</span>
            </a>
            <a href="{{ route('student.fees') }}" class="menu-item {{ request()->routeIs('student.fees') ? 'active' : '' }}">
                <i class="fas fa-rupee-sign"></i>
                <span>Fees</span>
            </a>
            <a href="{{ route('student.timetable') }}" class="menu-item {{ request()->routeIs('student.timetable') ? 'active' : '' }}">
                <i class="fas fa-clock"></i>
                <span>Timetable</span>
            </a>
            <a href="{{ route('student.notifications') }}" class="menu-item {{ request()->routeIs('student.notifications') ? 'active' : '' }}">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
            </a>
            <form action="{{ route('student.logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="menu-item" style="width: 100%; border: none; background: none; text-align: left; cursor: pointer;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header -->
        <div class="top-header">
            <div>
                <h5 style="margin: 0; color: #5a5c69;">@yield('page-title', 'Dashboard')</h5>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(session('student_name'), 0, 2)) }}
                </div>
                <div>
                    <strong>{{ session('student_name') }}</strong>
                    <br>
                    <small class="text-muted">Student</small>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile Menu Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.querySelector('.sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            // Toggle sidebar
            function toggleSidebar() {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
                
                // Change icon
                const icon = mobileMenuToggle.querySelector('i');
                if (sidebar.classList.contains('show')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }

            // Click on toggle button
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', toggleSidebar);
            }

            // Click on overlay to close
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', toggleSidebar);
            }

            // Close sidebar when clicking on menu item (mobile only)
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        toggleSidebar();
                    }
                });
            });

            // Close sidebar on window resize if desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    const icon = mobileMenuToggle.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
