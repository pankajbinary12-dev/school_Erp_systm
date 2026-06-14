<div class="sidebar" style="width: 250px; min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="p-3 text-center border-bottom">
        <i class="fas fa-chalkboard-teacher fa-3x text-white mb-2"></i>
        <h5 class="text-white mb-0">{{ $teacher->full_name }}</h5>
        <small class="text-white-50">{{ $teacher->employee_id }}</small>
    </div>
    
    <nav class="nav flex-column mt-3">
        <a class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}" href="{{ route('teacher.dashboard') }}">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a class="nav-link {{ request()->routeIs('teacher.profile') ? 'active' : '' }}" href="{{ route('teacher.profile') }}">
            <i class="fas fa-user"></i> My Profile
        </a>
        <a class="nav-link {{ request()->routeIs('teacher.students') ? 'active' : '' }}" href="{{ route('teacher.students') }}">
            <i class="fas fa-users"></i> My Students
        </a>
        <a class="nav-link {{ request()->routeIs('teacher.subjects') ? 'active' : '' }}" href="{{ route('teacher.subjects') }}">
            <i class="fas fa-book"></i> My Subjects
        </a>
        <a class="nav-link {{ request()->routeIs('teacher.my.attendance') ? 'active' : '' }}" href="{{ route('teacher.my.attendance') }}">
            <i class="fas fa-calendar-check"></i> My Attendance
        </a>
        <a class="nav-link {{ request()->routeIs('teacher.student.attendance') ? 'active' : '' }}" href="{{ route('teacher.student.attendance') }}">
            <i class="fas fa-user-check"></i> Student Attendance
        </a>
        <a class="nav-link {{ request()->routeIs('teacher.student.attendance.report') ? 'active' : '' }}" href="{{ route('teacher.student.attendance.report') }}">
            <i class="fas fa-chart-bar"></i> Attendance Report
        </a>
        <a class="nav-link {{ request()->routeIs('teacher.assignments*') ? 'active' : '' }}" href="{{ route('teacher.assignments') }}">
            <i class="fas fa-file-alt"></i> Assignments
        </a>
        <a class="nav-link {{ request()->routeIs('teacher.gradebook') ? 'active' : '' }}" href="{{ route('teacher.gradebook') }}">
            <i class="fas fa-chart-line"></i> Grade Book
        </a>
        <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
    
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>

<style>
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    overflow-y: auto;
    z-index: 1000;
}

.sidebar .nav-link {
    color: rgba(255, 255, 255, 0.8);
    padding: 12px 20px;
    transition: all 0.3s;
    border-left: 3px solid transparent;
}

.sidebar .nav-link:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
    border-left-color: white;
}

.sidebar .nav-link.active {
    color: white;
    background: rgba(255, 255, 255, 0.2);
    border-left-color: white;
    font-weight: 600;
}

.sidebar .nav-link i {
    width: 20px;
    margin-right: 10px;
}
</style>
