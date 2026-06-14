@extends('layouts.teacher')

@section('title', 'My Attendance')

@section('content')
<div class="d-flex">
    <!-- Sidebar -->
    @include('teacher.partials.sidebar')

    <!-- Main Content -->
    <div class="main-content flex-grow-1" style="margin-left: 250px;"
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <h4 class="mb-0"><i class="fas fa-calendar-check me-2"></i>My Attendance Record</h4>
                <div class="d-flex align-items-center">
                    <span class="me-3">{{ now()->format('l, F d, Y') }}</span>
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> {{ $teacher->first_name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" id="logoutBtn2"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="container-fluid p-4">
            <!-- Today's Attendance Card -->
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Today's Attendance - {{ now()->format('l, F d, Y') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div id="todayStatus" class="text-center p-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <button type="button" class="btn btn-success btn-lg w-100" id="checkInBtn" onclick="markAttendance('check_in')">
                                        <i class="fas fa-sign-in-alt me-2"></i>Check In
                                    </button>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <button type="button" class="btn btn-danger btn-lg w-100" id="checkOutBtn" onclick="markAttendance('check_out')" disabled>
                                        <i class="fas fa-sign-out-alt me-2"></i>Check Out
                                    </button>
                                </div>
                            </div>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> Check-in before 9:00 AM to avoid late marking. Don't forget to check-out when leaving!
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Year</label>
                            <select id="filterYear" class="form-select">
                                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Month</label>
                            <select id="filterMonth" class="form-select">
                                @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $index => $monthName)
                                    <option value="{{ $index + 1 }}" {{ ($index + 1) == date('n') ? 'selected' : '' }}>{{ $monthName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-primary d-block w-100" onclick="loadAttendance()">
                                <i class="fas fa-search me-1"></i>View Attendance
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div id="summaryCards" class="row mb-4" style="display: none;">
                <div class="col-md-12 mb-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="mb-2"><i class="fas fa-user me-2"></i><span id="staffName"></span></h6>
                            <p class="mb-0 text-muted">
                                <strong>Employee ID:</strong> <span id="staffEmployeeId"></span> | 
                                <strong>Designation:</strong> <span id="staffDesignation"></span> | 
                                <strong>Department:</strong> <span id="staffDepartment"></span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="card-body">
                            <h6>Total Days Marked</h6>
                            <h2 id="totalDays">0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h6>Present</h6>
                            <h2 id="totalPresent">0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <h6>Absent</h6>
                            <h2 id="totalAbsent">0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h6>Late</h6>
                            <h2 id="totalLate">0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h6>Attendance %</h6>
                            <h2 id="attendancePercentage">0%</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Table -->
            <div id="attendanceContainer" class="card" style="display: none;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Attendance Details - <span id="monthTitle"></span></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Day</th>
                                    <th>Status</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Working Hours</th>
                                    <th>Late</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="noDataMessage" class="alert alert-info" style="display: none;">
                <i class="fas fa-info-circle me-2"></i>No attendance records found for the selected period. Please contact admin if you believe this is an error.
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Load today's attendance status on page load
function loadTodayStatus() {
    fetch('{{ route('teacher.my.attendance.today') }}', {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateTodayStatus(data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function updateTodayStatus(data) {
    const statusDiv = document.getElementById('todayStatus');
    const checkInBtn = document.getElementById('checkInBtn');
    const checkOutBtn = document.getElementById('checkOutBtn');
    
    if (data.data) {
        const attendance = data.data;
        let statusHtml = '<div class="text-center">';
        
        if (attendance.check_in && attendance.check_out) {
            // Both check-in and check-out done
            statusHtml += `
                <h4 class="text-success mb-3"><i class="fas fa-check-circle"></i> Attendance Complete</h4>
                <p class="mb-1"><strong>Check In:</strong> ${attendance.check_in}</p>
                <p class="mb-1"><strong>Check Out:</strong> ${attendance.check_out}</p>
                <p class="mb-1"><strong>Working Hours:</strong> ${attendance.working_hours || 'Calculating...'}h</p>
                <p class="mb-0"><span class="badge bg-${attendance.is_late ? 'warning' : 'success'}">${attendance.status}</span></p>
            `;
            checkInBtn.disabled = true;
            checkOutBtn.disabled = true;
        } else if (attendance.check_in) {
            // Only check-in done
            statusHtml += `
                <h4 class="text-info mb-3"><i class="fas fa-clock"></i> Checked In</h4>
                <p class="mb-1"><strong>Check In Time:</strong> ${attendance.check_in}</p>
                <p class="mb-0"><span class="badge bg-${attendance.is_late ? 'warning' : 'success'}">${attendance.status}</span></p>
                <p class="mt-2 text-muted">Don't forget to check out!</p>
            `;
            checkInBtn.disabled = true;
            checkOutBtn.disabled = false;
        }
        
        statusHtml += '</div>';
        statusDiv.innerHTML = statusHtml;
    } else {
        // No attendance marked yet
        statusDiv.innerHTML = `
            <div class="text-center">
                <h4 class="text-warning mb-3"><i class="fas fa-exclamation-triangle"></i> Not Marked</h4>
                <p class="text-muted">Please check in to mark your attendance for today</p>
            </div>
        `;
        checkInBtn.disabled = false;
        checkOutBtn.disabled = true;
    }
}

function markAttendance(action) {
    const actionText = action === 'check_in' ? 'Check In' : 'Check Out';
    
    // Check if GPS is available
    if (!navigator.geolocation) {
        // No GPS support - mark without location
        Swal.fire({
            title: `${actionText} without GPS?`,
            text: 'Your browser does not support GPS. Attendance will be marked without location verification.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: action === 'check_in' ? '#28a745' : '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Yes, ${actionText}!`
        }).then((result) => {
            if (result.isConfirmed) {
                submitAttendance(action, null);
            }
        });
        return;
    }

    // Try to get GPS location (with timeout)
    Swal.fire({
        title: `${actionText}?`,
        text: 'Trying to get your location... (Click "Skip GPS" if it takes too long)',
        icon: 'info',
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonText: 'Getting location...',
        denyButtonText: 'Skip GPS',
        cancelButtonText: 'Cancel',
        allowOutsideClick: false,
        didOpen: () => { 
            Swal.showLoading();
            Swal.getConfirmButton().disabled = true;
        }
    });

    // Get GPS location with timeout
    const locationTimeout = setTimeout(() => {
        Swal.update({
            title: 'GPS Taking Too Long',
            text: 'Click "Skip GPS" to mark attendance without location',
            icon: 'warning'
        });
        Swal.hideLoading();
        Swal.getConfirmButton().disabled = false;
        Swal.getConfirmButton().textContent = 'Skip GPS';
    }, 5000); // 5 second timeout

    navigator.geolocation.getCurrentPosition(
        function(position) {
            // Success - got location
            clearTimeout(locationTimeout);
            
            const locationData = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy: position.coords.accuracy,
                device_type: /Mobile|Android|iPhone/i.test(navigator.userAgent) ? 'mobile' : 'web',
                device_id: getDeviceId()
            };

            // Show confirmation with location
            Swal.fire({
                title: `${actionText}?`,
                html: `
                    <p>Your location has been detected:</p>
                    <p><strong>📍 Coordinates:</strong> ${locationData.latitude.toFixed(6)}, ${locationData.longitude.toFixed(6)}</p>
                    <p><strong>🎯 Accuracy:</strong> ±${Math.round(locationData.accuracy)}m</p>
                    <p class="text-muted mt-3">System will verify you are at school premises</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: action === 'check_in' ? '#28a745' : '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Yes, ${actionText}!`
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAttendance(action, locationData);
                }
            });
        },
        function(error) {
            // Error getting location - allow marking without GPS
            clearTimeout(locationTimeout);
            
            let errorMessage = 'Unable to get your location. ';
            
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage += 'Location access was denied.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage += 'Location information is unavailable.';
                    break;
                case error.TIMEOUT:
                    errorMessage += 'Location request timed out.';
                    break;
                default:
                    errorMessage += 'An unknown error occurred.';
            }
            
            Swal.fire({
                title: 'GPS Not Available',
                html: `
                    <p>${errorMessage}</p>
                    <hr>
                    <p><strong>Do you want to mark attendance without GPS?</strong></p>
                    <p class="text-muted"><small>Note: Attendance will be marked but location will not be verified.</small></p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: action === 'check_in' ? '#28a745' : '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Yes, ${actionText} without GPS`,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAttendance(action, null);
                }
            });
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );

    // Handle "Skip GPS" button
    Swal.getDenyButton().addEventListener('click', () => {
        clearTimeout(locationTimeout);
        submitAttendance(action, null);
    });
}

function submitAttendance(action, locationData) {
    Swal.fire({
        title: 'Processing...',
        text: locationData ? 'Verifying your location...' : 'Marking attendance...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    const requestData = {
        action: action,
        attendance_date: '{{ now()->format('Y-m-d') }}'
    };
    
    if (locationData) {
        requestData.location = locationData;
    }
    
    fetch('{{ route('teacher.my.attendance.mark') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let icon = 'success';
            let title = 'Success!';
            let html = `<p>${data.message}</p>`;
            
            if (action === 'check_in' && data.is_late) {
                icon = 'warning';
                title = 'Checked In (Late)';
            }
            
            // Add location verification info
            if (data.location_verified) {
                if (data.data && data.data.location_log) {
                    html += `
                        <hr>
                        <div class="text-start">
                            <p><strong>📍 Location Verified:</strong></p>
                            <p>✅ Distance from school: ${data.data.distance}m</p>
                            <p>${data.data.within_geofence ? '✅' : '⚠️'} ${data.data.within_geofence ? 'Within' : 'Outside'} geofence</p>
                            ${data.data.ai_confidence ? `<p>🤖 AI Confidence: ${data.data.ai_confidence}%</p>` : ''}
                        </div>
                    `;
                }
            } else {
                html += `<p class="text-muted mt-2"><small>⚠️ Marked without GPS verification</small></p>`;
            }
            
            Swal.fire({
                icon: icon,
                title: title,
                html: html,
                timer: 5000
            });
            
            // Reload today's status
            loadTodayStatus();
            
            // Reload attendance data
            loadAttendance();
        } else {
            // Error response
            let errorHtml = `<p>${data.message}</p>`;
            
            if (data.code === 'OUTSIDE_GEOFENCE' && data.data) {
                errorHtml += `
                    <hr>
                    <div class="text-start">
                        <p><strong>📍 Location Details:</strong></p>
                        <p>❌ Distance from school: ${data.data.distance}m</p>
                        <p>✅ Allowed radius: ${data.data.allowed_radius}m</p>
                        <p class="text-danger">You need to be ${Math.round(data.data.distance - data.data.allowed_radius)}m closer</p>
                    </div>
                `;
                
                if (data.data.ai_flags && data.data.ai_flags.length > 0) {
                    errorHtml += `<p class="text-muted mt-2"><small>AI Flags: ${data.data.ai_flags.join(', ')}</small></p>`;
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Verification Failed',
                html: errorHtml
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Failed to mark attendance. Please try again.'
        });
    });
}

// Generate or retrieve device ID
function getDeviceId() {
    let deviceId = localStorage.getItem('device_id');
    if (!deviceId) {
        deviceId = 'device_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('device_id', deviceId);
    }
    return deviceId;
}

function loadAttendance() {
    const year = document.getElementById('filterYear').value;
    const month = document.getElementById('filterMonth').value;

    const url = `{{ route('teacher.my.attendance.data') }}?year=${year}&month=${month}`;

    fetch(url, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderAttendance(data.data, data.month_name);
        } else {
            showNoData(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNoData('Error loading attendance data');
    });
}

function renderAttendance(data, monthName) {
    if (data.attendances.length === 0) {
        showNoData();
        return;
    }

    // Show containers
    document.getElementById('summaryCards').style.display = 'flex';
    document.getElementById('attendanceContainer').style.display = 'block';
    document.getElementById('noDataMessage').style.display = 'none';

    // Update staff info
    document.getElementById('staffName').textContent = data.staff_info.name;
    document.getElementById('staffEmployeeId').textContent = data.staff_info.employee_id;
    document.getElementById('staffDesignation').textContent = data.staff_info.designation || 'N/A';
    document.getElementById('staffDepartment').textContent = data.staff_info.department || 'N/A';

    // Update summary
    document.getElementById('totalDays').textContent = data.summary.total_days;
    document.getElementById('totalPresent').textContent = data.summary.present;
    document.getElementById('totalAbsent').textContent = data.summary.absent;
    document.getElementById('totalLate').textContent = data.summary.late;
    document.getElementById('attendancePercentage').textContent = data.summary.attendance_percentage + '%';

    // Update month title
    document.getElementById('monthTitle').textContent = monthName;

    // Render table
    const tbody = document.getElementById('attendanceTableBody');
    tbody.innerHTML = '';

    data.attendances.forEach(record => {
        const row = document.createElement('tr');
        const date = new Date(record.attendance_date);
        
        row.innerHTML = `
            <td>${formatDate(record.attendance_date)}</td>
            <td>${date.toLocaleDateString('en-US', { weekday: 'short' })}</td>
            <td><span class="badge bg-${getStatusColor(record.status)}">${record.status}</span></td>
            <td>${record.check_in || '-'}</td>
            <td>${record.check_out || '-'}</td>
            <td>${record.working_hours ? record.working_hours + 'h' : '-'}</td>
            <td>${record.is_late ? '<span class="badge bg-warning">Yes</span>' : '<span class="badge bg-success">No</span>'}</td>
            <td>${record.remarks || '-'}</td>
        `;
        
        tbody.appendChild(row);
    });
}

function showNoData(message = 'No attendance records found for the selected period.') {
    document.getElementById('summaryCards').style.display = 'none';
    document.getElementById('attendanceContainer').style.display = 'none';
    document.getElementById('noDataMessage').style.display = 'block';
    document.getElementById('noDataMessage').innerHTML = `<i class="fas fa-info-circle me-2"></i>${message}`;
}

function getStatusColor(status) {
    const colors = {
        'Present': 'success',
        'Absent': 'danger',
        'Half Day': 'warning',
        'Late': 'info',
        'On Leave': 'secondary'
    };
    return colors[status] || 'secondary';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

// Load current month on page load
document.addEventListener('DOMContentLoaded', function() {
    loadTodayStatus();
    loadAttendance();
});

// Logout functionality
$(document).ready(function() {
    $('#logoutBtn, #logoutBtn2').click(function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Logout?',
            text: 'Are you sure you want to logout?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, logout!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("logout") }}',
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Logged Out!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = response.redirect;
                            });
                        }
                    }
                });
            }
        });
    });
});
</script>
@endpush
