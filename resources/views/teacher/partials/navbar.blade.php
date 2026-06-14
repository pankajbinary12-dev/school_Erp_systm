<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container-fluid">
        <h4 class="mb-0">
            @yield('title', 'Teacher Dashboard')
        </h4>
        <div class="d-flex align-items-center">
            <span class="me-3 text-muted">{{ now()->format('l, F d, Y') }}</span>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle"></i> {{ $teacher->first_name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('teacher.profile') }}">
                            <i class="fas fa-user"></i> Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('teacher.my.attendance') }}">
                            <i class="fas fa-calendar-check"></i> My Attendance
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-nav').submit();">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<form id="logout-form-nav" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<style>
.navbar {
    position: sticky;
    top: 0;
    z-index: 999;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
