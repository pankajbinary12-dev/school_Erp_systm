<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container-fluid">
        <h4 class="mb-0">Student Dashboard</h4>
        <div class="d-flex align-items-center">
            <span class="me-3">{{ now()->format('l, F d, Y') }}</span>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle"></i> {{ $student->first_name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('student.profile') }}"><i class="fas fa-user"></i> Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-nav').submit();"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<form id="logout-form-nav" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
