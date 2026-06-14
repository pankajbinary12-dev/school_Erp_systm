@extends('admin.layouts.horizontal')
@section('title', 'Staff Attendance')
@section('content')
<div class="content-card">
    <div class="content-card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-user-tie me-2"></i>Staff Attendance - 3 Methods</h5>
        <div>
            <a href="{{ route('admin.attendance.biometric.test.simulator') }}" class="btn btn-warning btn-sm me-2">
                <i class="fas fa-vial me-1"></i>Test Biometric
            </a>
            <a href="{{ route('admin.attendance.biometric.devices') }}" class="btn btn-info btn-sm me-2">
                <i class="fas fa-fingerprint me-1"></i>Biometric Devices
            </a>
            <a href="{{ route('admin.attendance.staff.monthly') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-calendar-alt me-1"></i>Monthly Report
            </a>
        </div>
    </div>
    
    <div class="content-card-body">
        <!-- Method Tabs -->
        <ul class="nav nav-tabs mb-4" id="attendanceMethodTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="bulk-tab" data-bs-toggle="tab" data-bs-target="#bulk" type="button">
                    <i class="fas fa-users me-1"></i>Bulk Attendance
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button">
                    <i class="fas fa-user-edit me-1"></i>Admin Manual Mark
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats" type="button">
                    <i class="fas fa-chart-bar me-1"></i>Today's Stats
                </button>
            </li>
        </ul>

        <div class="tab-content" id="attendanceMethodTabsContent">
            <!-- Bulk Attendance Tab -->
            <div class="tab-pane fade show active" id="bulk" role="tabpanel">
                <!-- Date Selection -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Attendance Date</label>
                        <input type="date" id="attendanceDate" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-primary d-block" onclick="loadAttendance()">
                            <i class="fas fa-search me-1"></i>Load Attendance
                        </button>
                    </div>
                </div>

                <!-- Attendance Table -->
                <div id="attendanceTableContainer" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Designation</th>
                                    <th>Status</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Working Hours</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <button type="button" class="btn btn-success" onclick="saveAllAttendance()">
                            <i class="fas fa-save me-1"></i>Save All Attendance
                        </button>
                    </div>
                </div>
            </div>

            <!-- Admin Manual Mark Tab -->
            <div class="tab-pane fade" id="manual" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-user-check me-2"></i>Admin Manual Attendance</h6>
                            </div>
                            <div class="card-body">
                                <form id="manualAttendanceForm">
                                    <div class="mb-3">
                                        <label class="form-label">Select Staff</label>
                                        <select class="form-select" id="manualStaffId" required>
                                            <option value="">-- Select Staff --</option>
                                            @foreach($staff as $member)
                                                <option value="{{ $member->id }}">
                                                    {{ $member->employee_id }} - {{ $member->full_name }} ({{ $member->designation }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Date</label>
                                        <input type="date" class="form-control" id="manualDate" value="{{ date('Y-m-d') }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Action</label>
                                        <select class="form-select" id="manualAction" required>
                                            <option value="check_in">Check In</option>
                                            <option value="check_out">Check Out</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Time</label>
                                        <input type="time" class="form-control" id="manualTime" value="{{ date('H:i') }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" id="manualStatus">
                                            <option value="Present">Present</option>
                                            <option value="Late">Late</option>
                                            <option value="Half Day">Half Day</option>
                                            <option value="On Leave">On Leave</option>
                                            <option value="Absent">Absent</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Remarks</label>
                                        <textarea class="form-control" id="manualRemarks" rows="2" placeholder="Reason for manual entry..."></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-check me-1"></i>Mark Attendance
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Instructions</h6>
                            </div>
                            <div class="card-body">
                                <h6>Admin Manual Attendance</h6>
                                <p class="text-muted">Use this form to manually mark attendance for any staff member.</p>
                                
                                <div class="alert alert-info">
                                    <strong>When to use:</strong>
                                    <ul class="mb-0">
                                        <li>Staff forgot to check-in/out</li>
                                        <li>System was down</li>
                                        <li>Corrections needed</li>
                                        <li>Past dates entry</li>
                                        <li>Special cases (sick leave, etc.)</li>
                                    </ul>
                                </div>

                                <div class="alert alert-warning">
                                    <strong>Note:</strong> All manual entries will be marked with "[Admin marked]" in remarks and logged with your admin ID.
                                </div>

                                <h6 class="mt-3">Three Methods Available:</h6>
                                <ol>
                                    <li><strong>Teacher Self:</strong> Teacher marks via dashboard</li>
                                    <li><strong>Biometric:</strong> Automatic via fingerprint/face</li>
                                    <li><strong>Admin Manual:</strong> You mark manually (this form)</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Stats Tab -->
            <div class="tab-pane fade" id="stats" role="tabpanel">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-success" id="statPresent">0</h3>
                                <p class="mb-0">Present</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-danger" id="statAbsent">0</h3>
                                <p class="mb-0">Absent</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-info" id="statLate">0</h3>
                                <p class="mb-0">Late</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-warning" id="statLeave">0</h3>
                                <p class="mb-0">On Leave</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">Attendance by Method</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Method</th>
                                        <th>Count</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody id="methodStatsBody">
                                    <tr>
                                        <td><i class="fas fa-mobile-alt text-primary"></i> Teacher Self</td>
                                        <td id="methodSelf">0</td>
                                        <td id="methodSelfPct">0%</td>
                                    </tr>
                                    <tr>
                                        <td><i class="fas fa-fingerprint text-success"></i> Biometric</td>
                                        <td id="methodBio">0</td>
                                        <td id="methodBioPct">0%</td>
                                    </tr>
                                    <tr>
                                        <td><i class="fas fa-user-shield text-info"></i> Admin Manual</td>
                                        <td id="methodAdmin">0</td>
                                        <td id="methodAdminPct">0%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Today's Attendance Details</h6>
                        <input type="text" id="statsSearch" class="form-control form-control-sm" style="width: 250px;" placeholder="Search staff...">
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>Staff Name</th>
                                        <th>Status</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Method</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody id="todayAttendanceList">
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <i class="fas fa-spinner fa-spin"></i> Loading...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editAttendanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editStaffId">
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="editStatus">
                        <option value="Present">Present</option>
                        <option value="Absent">Absent</option>
                        <option value="Half Day">Half Day</option>
                        <option value="Late">Late</option>
                        <option value="On Leave">On Leave</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Check In</label>
                    <input type="time" class="form-control" id="editCheckIn">
                </div>
                <div class="mb-3">
                    <label class="form-label">Check Out</label>
                    <input type="time" class="form-control" id="editCheckOut">
                </div>
                <div class="mb-3">
                    <label class="form-label">Remarks</label>
                    <textarea class="form-control" id="editRemarks" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveAttendance()">Save</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let staffData = [];
let attendanceRecords = {};
let todayAttendanceData = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, initializing...');
    
    // Attach form handler
    const manualForm = document.getElementById('manualAttendanceForm');
    if (manualForm) {
        manualForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitManualAttendance(e);
        });
    }
    
    // Load initial data
    loadAttendance();
    
    // Attach tab change listener for stats
    const statsTab = document.getElementById('stats-tab');
    if (statsTab) {
        statsTab.addEventListener('shown.bs.tab', function() {
            console.log('Stats tab opened, loading data...');
            loadTodayStats();
        });
    }
    
    // Search functionality
    const searchInput = document.getElementById('statsSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const filtered = todayAttendanceData.filter(staff => {
                const name = `${staff.first_name} ${staff.last_name}`.toLowerCase();
                return name.includes(searchTerm);
            });
            displayTodayAttendanceList(filtered);
        });
    }
});

function submitManualAttendance(event) {
    const form = event.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';
    
    console.log('Submitting manual attendance...');
    console.log('Submitting manual attendance...');
    
    const data = {
        staff_id: document.getElementById('manualStaffId').value,
        attendance_date: document.getElementById('manualDate').value,
        action: document.getElementById('manualAction').value,
        time: document.getElementById('manualTime').value,
        status: document.getElementById('manualStatus').value,
        remarks: document.getElementById('manualRemarks').value
    };
    
    console.log('Data to send:', data);

    fetch('{{ route('admin.attendance.staff.admin-mark') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        
        if (data.success) {
            showAlert('success', data.message, '#manual .card-body');
            form.reset();
            document.getElementById('manualDate').value = '{{ date('Y-m-d') }}';
            document.getElementById('manualTime').value = '{{ date('H:i') }}';
            loadTodayStats();
            loadAttendance();
        } else {
            showAlert('danger', data.message, '#manual .card-body');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        showAlert('danger', 'Network error: ' + error.message, '#manual .card-body');
    });
}

function showAlert(type, message, containerSelector) {
    const container = document.querySelector(containerSelector);
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Remove any existing alerts
    const existingAlerts = container.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    container.insertBefore(alertDiv, container.firstChild);
    setTimeout(() => alertDiv.remove(), 5000);
}

function loadTodayStats() {
    const today = '{{ date('Y-m-d') }}';
    console.log('Loading today stats for:', today);
    
    // Show loading in table
    const tbody = document.getElementById('todayAttendanceList');
    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
    }
    
    fetch(`{{ route('admin.attendance.staff.by-date') }}?date=${today}`, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Stats response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Stats data received:', data);
        if (data.success) {
            calculateStats(data.data);
            displayTodayAttendanceList(data.data);
        } else {
            console.error('Stats load failed:', data);
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>';
            }
        }
    })
    .catch(error => {
        console.error('Error loading stats:', error);
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Network error: ' + error.message + '</td></tr>';
        }
    });
}

function displayTodayAttendanceList(staffList) {
    todayAttendanceData = staffList;
    const tbody = document.getElementById('todayAttendanceList');
    
    console.log('Displaying attendance list, count:', staffList.length);
    
    if (!staffList || staffList.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No staff members found</td></tr>';
        return;
    }

    tbody.innerHTML = staffList.map(staff => {
        const att = staff.attendances && staff.attendances.length > 0 ? staff.attendances[0] : null;
        
        if (!att) {
            return `
                <tr>
                    <td>${staff.first_name} ${staff.last_name}</td>
                    <td><span class="badge bg-secondary">Not Marked</span></td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
            `;
        }

        const statusBadge = {
            'Present': 'success',
            'Absent': 'danger',
            'Late': 'warning',
            'On Leave': 'info',
            'Half Day': 'secondary'
        };

        let method = 'Self';
        if (att.remarks && att.remarks.includes('Biometric')) {
            method = '<i class="fas fa-fingerprint text-success"></i> Biometric';
        } else if (att.marked_by) {
            method = '<i class="fas fa-user-shield text-info"></i> Admin';
        } else {
            method = '<i class="fas fa-mobile-alt text-primary"></i> Self';
        }

        return `
            <tr>
                <td>${staff.first_name} ${staff.last_name}</td>
                <td><span class="badge bg-${statusBadge[att.status] || 'secondary'}">${att.status}</span></td>
                <td>${att.check_in || '-'}</td>
                <td>${att.check_out || '-'}</td>
                <td>${method}</td>
                <td>${att.remarks || '-'}</td>
            </tr>
        `;
    }).join('');
}

function calculateStats(staffList) {
    let present = 0, absent = 0, late = 0, leave = 0;
    let methodSelf = 0, methodBio = 0, methodAdmin = 0;
    let total = 0;

    console.log('Calculating stats for', staffList.length, 'staff members');

    staffList.forEach(staff => {
        if (staff.attendances && staff.attendances.length > 0) {
            const att = staff.attendances[0];
            total++;
            
            // Count by status
            if (att.status === 'Present') present++;
            else if (att.status === 'Absent') absent++;
            else if (att.status === 'Late') late++;
            else if (att.status === 'On Leave') leave++;
            
            // Count by method
            if (att.remarks && att.remarks.includes('Biometric')) {
                methodBio++;
            } else if (att.marked_by) {
                methodAdmin++;
            } else {
                methodSelf++;
            }
        }
    });

    console.log('Stats:', {present, absent, late, leave, methodSelf, methodBio, methodAdmin});

    // Update status counts
    document.getElementById('statPresent').textContent = present;
    document.getElementById('statAbsent').textContent = absent;
    document.getElementById('statLate').textContent = late;
    document.getElementById('statLeave').textContent = leave;

    // Update method counts
    document.getElementById('methodSelf').textContent = methodSelf;
    document.getElementById('methodBio').textContent = methodBio;
    document.getElementById('methodAdmin').textContent = methodAdmin;

    // Update percentages
    if (total > 0) {
        document.getElementById('methodSelfPct').textContent = Math.round((methodSelf/total)*100) + '%';
        document.getElementById('methodBioPct').textContent = Math.round((methodBio/total)*100) + '%';
        document.getElementById('methodAdminPct').textContent = Math.round((methodAdmin/total)*100) + '%';
    } else {
        document.getElementById('methodSelfPct').textContent = '0%';
        document.getElementById('methodBioPct').textContent = '0%';
        document.getElementById('methodAdminPct').textContent = '0%';
    }
}

function loadAttendance() {
    const date = document.getElementById('attendanceDate').value;
    
    console.log('Loading attendance for date:', date);
    
    if (!date) {
        showAlert('warning', 'Please select a date', '#bulk');
        return;
    }

    fetch(`{{ route('admin.attendance.staff.by-date') }}?date=${date}`, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Attendance response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Attendance data received:', data);
        if (data.success) {
            staffData = data.data;
            renderAttendanceTable();
            document.getElementById('attendanceTableContainer').style.display = 'block';
        } else {
            showAlert('danger', 'Error loading attendance data', '#bulk');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'Network error: ' + error.message, '#bulk');
    });
}

function renderAttendanceTable() {
    const tbody = document.getElementById('attendanceTableBody');
    tbody.innerHTML = '';

    staffData.forEach(staff => {
        const attendance = staff.attendances[0] || {};
        const row = document.createElement('tr');
        
        // Store attendance data
        if (!attendanceRecords[staff.id]) {
            attendanceRecords[staff.id] = {
                staff_id: staff.id,
                status: attendance.status || 'Present',
                check_in: attendance.check_in || '09:00',
                check_out: attendance.check_out || '',
                remarks: attendance.remarks || ''
            };
        }

        const record = attendanceRecords[staff.id];
        const workingHours = calculateWorkingHours(record.check_in, record.check_out);

        row.innerHTML = `
            <td>${staff.employee_id}</td>
            <td>${staff.first_name} ${staff.last_name}</td>
            <td>${staff.designation || 'N/A'}</td>
            <td>
                <span class="badge bg-${getStatusColor(record.status)}">${record.status}</span>
            </td>
            <td>${record.check_in || '-'}</td>
            <td>${record.check_out || '-'}</td>
            <td>${workingHours}</td>
            <td>${record.remarks || '-'}</td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="editAttendance(${staff.id})">
                    <i class="fas fa-edit"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
    });
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

function calculateWorkingHours(checkIn, checkOut) {
    if (!checkIn || !checkOut) return '-';
    
    const [inHour, inMin] = checkIn.split(':').map(Number);
    const [outHour, outMin] = checkOut.split(':').map(Number);
    
    const inMinutes = inHour * 60 + inMin;
    const outMinutes = outHour * 60 + outMin;
    
    const diff = outMinutes - inMinutes;
    const hours = Math.floor(diff / 60);
    const minutes = diff % 60;
    
    return `${hours}h ${minutes}m`;
}

function editAttendance(staffId) {
    const record = attendanceRecords[staffId];
    
    document.getElementById('editStaffId').value = staffId;
    document.getElementById('editStatus').value = record.status;
    document.getElementById('editCheckIn').value = record.check_in;
    document.getElementById('editCheckOut').value = record.check_out;
    document.getElementById('editRemarks').value = record.remarks;
    
    new bootstrap.Modal(document.getElementById('editAttendanceModal')).show();
}

function saveAttendance() {
    const staffId = document.getElementById('editStaffId').value;
    
    attendanceRecords[staffId] = {
        staff_id: staffId,
        status: document.getElementById('editStatus').value,
        check_in: document.getElementById('editCheckIn').value,
        check_out: document.getElementById('editCheckOut').value,
        remarks: document.getElementById('editRemarks').value
    };
    
    renderAttendanceTable();
    bootstrap.Modal.getInstance(document.getElementById('editAttendanceModal')).hide();
}

function saveAllAttendance() {
    const date = document.getElementById('attendanceDate').value;
    const records = Object.values(attendanceRecords);
    
    console.log('Saving bulk attendance, records:', records.length);
    
    if (records.length === 0) {
        showAlert('warning', 'No attendance records to save', '#bulk');
        return;
    }

    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';

    fetch('{{ route('admin.attendance.staff.bulk') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            attendance_date: date,
            attendance_records: records
        })
    })
    .then(response => {
        console.log('Bulk save response:', response.status);
        return response.json();
    })
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        
        console.log('Bulk save result:', data);
        
        if (data.success) {
            showAlert('success', data.message, '#bulk');
            loadAttendance();
            loadTodayStats();
        } else {
            showAlert('danger', 'Error: ' + data.message, '#bulk');
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        console.error('Error:', error);
        showAlert('danger', 'Network error: ' + error.message, '#bulk');
    });
}

</script>
@endsection
