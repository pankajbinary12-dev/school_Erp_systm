<div class="sidebar" style="width: 250px; min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="p-3 text-center border-bottom">
        <i class="fas fa-user-graduate fa-3x text-white mb-2"></i>
        <h5 class="text-white mb-0">{{ $student->first_name }} {{ $student->last_name }}</h5>
        <small class="text-white-50">{{ $student->admission_no }}</small>
    </div>
    
    <nav class="nav flex-column mt-3">
        <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a class="nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}" href="{{ route('student.profile') }}">
            <i class="fas fa-user"></i> My Profile
        </a>
        <a class="nav-link {{ request()->routeIs('student.subjects') ? 'active' : '' }}" href="{{ route('student.subjects') }}">
            <i class="fas fa-book"></i> My Subjects
        </a>
        <a class="nav-link {{ request()->routeIs('student.attendance') ? 'active' : '' }}" href="{{ route('student.attendance') }}">
            <i class="fas fa-calendar-check"></i> Attendance
        </a>
        <a class="nav-link {{ request()->routeIs('student.assignments') ? 'active' : '' }}" href="{{ route('student.assignments') }}">
            <i class="fas fa-file-alt"></i> Assignments
        </a>
        <a class="nav-link {{ request()->routeIs('student.results') ? 'active' : '' }}" href="{{ route('student.results') }}">
            <i class="fas fa-chart-line"></i> Results
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
