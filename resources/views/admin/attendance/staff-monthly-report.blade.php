@extends('admin.layouts.horizontal')
@section('title', 'Staff Monthly Attendance Report')
@section('content')
<div class="content-card">
    <div class="content-card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-calendar-alt me-2"></i>Monthly Attendance Report</h5>
        <a href="{{ route('admin.attendance.staff') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Back to Daily Attendance
        </a>
    </div>
    
    <div class="content-card-body">
        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-3">
                <label class="form-label">Year</label>
                <select id="filterYear" class="form-select">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Month</label>
                <select id="filterMonth" class="form-select">
                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $index => $monthName)
                        <option value="{{ $index + 1 }}" {{ ($index + 1) == $month ? 'selected' : '' }}>{{ $monthName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Staff Member (Optional)</label>
                <select id="filterStaff" class="form-select">
                    <option value="">All Staff</option>
                    @foreach($staff as $member)
                        <option value="{{ $member->id }}">{{ $member->full_name }} - {{ $member->designation }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-primary d-block w-100" onclick="loadReport()">
                    <i class="fas fa-search me-1"></i>Generate
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div id="summaryCards" class="row mb-4" style="display: none;">
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>Total Present</h6>
                        <h3 id="totalPresent">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h6>Total Absent</h6>
                        <h3 id="totalAbsent">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6>Total Late</h6>
                        <h3 id="totalLate">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6>Avg Attendance</h6>
                        <h3 id="avgAttendance">0%</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Table -->
        <div id="reportContainer" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 id="reportTitle">Report for</h6>
                <button class="btn btn-success btn-sm" onclick="exportReport()">
                    <i class="fas fa-file-excel me-1"></i>Export to Excel
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="reportTable">
                    <thead class="table-light">
                        <tr>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Late</th>
                            <th>Half Day</th>
                            <th>On Leave</th>
                            <th>Total Hours</th>
                            <th>Attendance %</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody">
                    </tbody>
                </table>
            </div>
        </div>

        <div id="noDataMessage" class="alert alert-info" style="display: none;">
            <i class="fas fa-info-circle me-2"></i>No attendance data found for the selected period.
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalTitle">Daily Attendance Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Working Hours</th>
                                <th>Late</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let reportData = [];

function loadReport() {
    const year = document.getElementById('filterYear').value;
    const month = document.getElementById('filterMonth').value;
    const staffId = document.getElementById('filterStaff').value;

    const url = `{{ route('admin.attendance.staff.monthly.data') }}?year=${year}&month=${month}${staffId ? '&staff_id=' + staffId : ''}`;

    fetch(url, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            reportData = data.data;
            renderReport(data.month_name);
            calculateSummary();
        } else {
            showNoData();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error loading report');
    });
}

function renderReport(monthName) {
    if (reportData.length === 0) {
        showNoData();
        return;
    }

    document.getElementById('reportTitle').textContent = `Report for ${monthName}`;
    document.getElementById('reportContainer').style.display = 'block';
    document.getElementById('noDataMessage').style.display = 'none';
    document.getElementById('summaryCards').style.display = 'flex';

    const tbody = document.getElementById('reportTableBody');
    tbody.innerHTML = '';

    reportData.forEach(staff => {
        const summary = staff.summary;
        const row = document.createElement('tr');
        
        row.innerHTML = `
            <td>${staff.employee_id}</td>
            <td>${staff.name}</td>
            <td>${staff.designation || 'N/A'}</td>
            <td>${staff.department || 'N/A'}</td>
            <td><span class="badge bg-success">${summary.present}</span></td>
            <td><span class="badge bg-danger">${summary.absent}</span></td>
            <td><span class="badge bg-warning">${summary.late}</span></td>
            <td><span class="badge bg-info">${summary.half_day}</span></td>
            <td><span class="badge bg-secondary">${summary.on_leave}</span></td>
            <td>${summary.total_working_hours.toFixed(2)}h</td>
            <td>
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar ${getProgressColor(summary.attendance_percentage)}" 
                         style="width: ${summary.attendance_percentage}%">
                        ${summary.attendance_percentage}%
                    </div>
                </div>
            </td>
            <td>
                <button class="btn btn-sm btn-info" onclick="viewDetails(${staff.staff_id}, '${staff.name}')">
                    <i class="fas fa-eye"></i> Details
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

function calculateSummary() {
    let totalPresent = 0;
    let totalAbsent = 0;
    let totalLate = 0;
    let totalPercentage = 0;

    reportData.forEach(staff => {
        totalPresent += staff.summary.present;
        totalAbsent += staff.summary.absent;
        totalLate += staff.summary.late;
        totalPercentage += staff.summary.attendance_percentage;
    });

    document.getElementById('totalPresent').textContent = totalPresent;
    document.getElementById('totalAbsent').textContent = totalAbsent;
    document.getElementById('totalLate').textContent = totalLate;
    document.getElementById('avgAttendance').textContent = 
        reportData.length > 0 ? (totalPercentage / reportData.length).toFixed(1) + '%' : '0%';
}

function getProgressColor(percentage) {
    if (percentage >= 90) return 'bg-success';
    if (percentage >= 75) return 'bg-info';
    if (percentage >= 60) return 'bg-warning';
    return 'bg-danger';
}

function showNoData() {
    document.getElementById('reportContainer').style.display = 'none';
    document.getElementById('summaryCards').style.display = 'none';
    document.getElementById('noDataMessage').style.display = 'block';
}

function viewDetails(staffId, staffName) {
    const staff = reportData.find(s => s.staff_id === staffId);
    
    if (!staff) return;

    document.getElementById('detailModalTitle').textContent = `Daily Attendance - ${staffName}`;
    
    const tbody = document.getElementById('detailTableBody');
    tbody.innerHTML = '';

    staff.daily_records.forEach(record => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${formatDate(record.attendance_date)}</td>
            <td><span class="badge bg-${getStatusColor(record.status)}">${record.status}</span></td>
            <td>${record.check_in || '-'}</td>
            <td>${record.check_out || '-'}</td>
            <td>${record.working_hours ? record.working_hours + 'h' : '-'}</td>
            <td>${record.is_late ? '<span class="badge bg-warning">Yes</span>' : '<span class="badge bg-success">No</span>'}</td>
            <td>${record.remarks || '-'}</td>
        `;
        tbody.appendChild(row);
    });

    new bootstrap.Modal(document.getElementById('detailModal')).show();
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

function exportReport() {
    const year = document.getElementById('filterYear').value;
    const month = document.getElementById('filterMonth').value;
    const staffId = document.getElementById('filterStaff').value;

    window.location.href = `{{ route('admin.attendance.staff.export') }}?year=${year}&month=${month}${staffId ? '&staff_id=' + staffId : ''}&format=excel`;
}

// Load report on page load
document.addEventListener('DOMContentLoaded', function() {
    loadReport();
});
</script>
@endsection
