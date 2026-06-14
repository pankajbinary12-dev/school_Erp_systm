@extends('admin.layouts.horizontal')
@section('title', 'Biometric Test Simulator')
@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-vial me-2"></i>Biometric Attendance Test Simulator</h5>
    </div>
    
    <div class="content-card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Testing Tool:</strong> Use this page to simulate biometric device scans without a real device.
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Simulate Biometric Scan</h6>
                    </div>
                    <div class="card-body">
                        <form id="biometricTestForm">
                            <div class="mb-3">
                                <label class="form-label">Device Code</label>
                                <input type="text" class="form-control" id="deviceCode" value="BIO001" required>
                                <small class="text-muted">Must match a device in Biometric Devices</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Employee ID</label>
                                <select class="form-select" id="employeeId" required>
                                    <option value="">-- Select Staff --</option>
                                    @foreach(\App\Models\StaffMember::where('status', 'Active')->get() as $staff)
                                        <option value="{{ $staff->employee_id }}">
                                            {{ $staff->employee_id }} - {{ $staff->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Timestamp (Optional)</label>
                                <input type="datetime-local" class="form-control" id="timestamp" 
                                       value="{{ date('Y-m-d\TH:i') }}">
                                <small class="text-muted">Leave empty for current time</small>
                            </div>

                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-fingerprint me-2"></i>Simulate Biometric Scan
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-info btn-sm w-100 mb-2" onclick="simulateMultiple()">
                            <i class="fas fa-users me-1"></i>Simulate 5 Random Scans
                        </button>
                        <button class="btn btn-warning btn-sm w-100 mb-2" onclick="simulateCheckOut()">
                            <i class="fas fa-sign-out-alt me-1"></i>Simulate Check-Out for All
                        </button>
                        <a href="{{ route('admin.attendance.biometric.devices') }}" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-cog me-1"></i>Manage Devices
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">Test Results</h6>
                    </div>
                    <div class="card-body">
                        <div id="testResults" style="max-height: 500px; overflow-y: auto;">
                            <p class="text-muted text-center">No tests run yet. Submit the form to test.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Today's Biometric Attendance</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Time</th>
                                <th>Employee</th>
                                <th>Action</th>
                                <th>Status</th>
                                <th>Device</th>
                            </tr>
                        </thead>
                        <tbody id="todayAttendance">
                            <tr>
                                <td colspan="5" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadTodayAttendance();
    
    document.getElementById('biometricTestForm').addEventListener('submit', function(e) {
        e.preventDefault();
        simulateScan();
    });
});

function simulateScan() {
    const deviceCode = document.getElementById('deviceCode').value;
    const employeeId = document.getElementById('employeeId').value;
    const timestamp = document.getElementById('timestamp').value;

    if (!deviceCode || !employeeId) {
        alert('Please fill all required fields');
        return;
    }

    const data = {
        device_code: deviceCode,
        employee_id: employeeId
    };

    if (timestamp) {
        data.timestamp = timestamp;
    }

    addResult('info', 'Sending biometric scan...', deviceCode, employeeId);

    fetch('{{ route('admin.attendance.biometric.process') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addResult('success', data.message, deviceCode, employeeId, data.data);
            loadTodayAttendance();
        } else {
            addResult('danger', data.message, deviceCode, employeeId);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        addResult('danger', 'Network error: ' + error.message, deviceCode, employeeId);
    });
}

function addResult(type, message, deviceCode, employeeId, data = null) {
    const resultsDiv = document.getElementById('testResults');
    
    // Clear "no tests" message
    if (resultsDiv.querySelector('.text-muted')) {
        resultsDiv.innerHTML = '';
    }

    const time = new Date().toLocaleTimeString();
    const resultHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <strong>${time}</strong><br>
            <strong>Device:</strong> ${deviceCode}<br>
            <strong>Employee:</strong> ${employeeId}<br>
            <strong>Result:</strong> ${message}
            ${data ? `<br><strong>Details:</strong> ${JSON.stringify(data, null, 2)}` : ''}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    resultsDiv.insertAdjacentHTML('afterbegin', resultHtml);
}

function loadTodayAttendance() {
    const today = '{{ date('Y-m-d') }}';
    
    fetch(`{{ route('admin.attendance.staff.by-date') }}?date=${today}`, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderTodayAttendance(data.data);
        }
    })
    .catch(error => console.error('Error:', error));
}

function renderTodayAttendance(staffList) {
    const tbody = document.getElementById('todayAttendance');
    tbody.innerHTML = '';

    let biometricCount = 0;
    
    staffList.forEach(staff => {
        if (staff.attendances && staff.attendances.length > 0) {
            const att = staff.attendances[0];
            
            // Only show biometric attendance
            if (att.remarks && att.remarks.includes('Biometric')) {
                biometricCount++;
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${att.check_in || '-'}</td>
                    <td>${staff.full_name}</td>
                    <td>
                        ${att.check_in && !att.check_out ? 
                            '<span class="badge bg-success">Check-In</span>' : 
                            '<span class="badge bg-info">Check-Out</span>'}
                    </td>
                    <td><span class="badge bg-${getStatusColor(att.status)}">${att.status}</span></td>
                    <td><i class="fas fa-fingerprint text-success"></i> Biometric</td>
                `;
                tbody.appendChild(row);
            }
        }
    });

    if (biometricCount === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No biometric attendance today</td></tr>';
    }
}

function getStatusColor(status) {
    const colors = {
        'Present': 'success',
        'Absent': 'danger',
        'Late': 'warning',
        'Half Day': 'info',
        'On Leave': 'secondary'
    };
    return colors[status] || 'secondary';
}

function simulateMultiple() {
    const staffSelect = document.getElementById('employeeId');
    const options = Array.from(staffSelect.options).filter(opt => opt.value);
    
    if (options.length === 0) {
        alert('No staff members available');
        return;
    }

    const count = Math.min(5, options.length);
    const selected = [];
    
    // Select random staff
    while (selected.length < count) {
        const random = options[Math.floor(Math.random() * options.length)];
        if (!selected.includes(random.value)) {
            selected.push(random.value);
        }
    }

    // Simulate scans with delay
    selected.forEach((employeeId, index) => {
        setTimeout(() => {
            document.getElementById('employeeId').value = employeeId;
            simulateScan();
        }, index * 1000); // 1 second delay between each
    });
}

function simulateCheckOut() {
    const today = '{{ date('Y-m-d') }}';
    
    fetch(`{{ route('admin.attendance.staff.by-date') }}?date=${today}`, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const checkedIn = data.data.filter(staff => 
                staff.attendances && 
                staff.attendances.length > 0 && 
                staff.attendances[0].check_in && 
                !staff.attendances[0].check_out &&
                staff.attendances[0].remarks && 
                staff.attendances[0].remarks.includes('Biometric')
            );

            if (checkedIn.length === 0) {
                alert('No staff checked in via biometric today');
                return;
            }

            // Simulate check-out for each
            checkedIn.forEach((staff, index) => {
                setTimeout(() => {
                    document.getElementById('employeeId').value = staff.employee_id;
                    simulateScan();
                }, index * 1000);
            });
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endsection
