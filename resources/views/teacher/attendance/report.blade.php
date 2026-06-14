@extends('layouts.teacher')

@section('title', 'Attendance Report')

@section('content')
<div class="d-flex">
    @include('teacher.partials.sidebar')

    <div class="main-content flex-grow-1">
        @include('teacher.partials.navbar')

        <div class="content-area">
            <div class="container-fluid">
                <h4 class="mb-4"><i class="fas fa-chart-bar me-2"></i>Attendance Report</h4>

                <!-- Filter Card -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Generate Report</h5>
                    </div>
                    <div class="card-body">
                        <form id="reportForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Class</label>
                                    <select class="form-select" id="class_id" name="class_id" required>
                                        <option value="">Select Class</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Section</label>
                                    <select class="form-select" id="section_id" name="section_id" required>
                                        <option value="">Select Class First</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-search"></i> Generate
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Report Card -->
                <div class="card d-none" id="reportCard">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Attendance Report</h5>
                        <button class="btn btn-light btn-sm" onclick="exportReport()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <strong>Total Sessions:</strong> <span id="totalSessions">0</span>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="reportTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Roll No</th>
                                        <th>Student Name</th>
                                        <th>Present</th>
                                        <th>Absent</th>
                                        <th>Late</th>
                                        <th>Leave</th>
                                        <th>Percentage</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="reportTableBody">
                                    <tr>
                                        <td colspan="8" class="text-center">No data</td>
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
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default dates (last 30 days)
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    document.getElementById('end_date').valueAsDate = today;
    document.getElementById('start_date').valueAsDate = thirtyDaysAgo;
    
    // Class change handler
    document.getElementById('class_id').addEventListener('change', function() {
        const classId = this.value;
        const sectionSelect = document.getElementById('section_id');
        
        sectionSelect.innerHTML = '<option value="">Loading...</option>';
        
        if (!classId) {
            sectionSelect.innerHTML = '<option value="">Select Class First</option>';
            return;
        }
        
        fetch(`/teacher/get-sections/${classId}`)
            .then(response => response.json())
            .then(result => {
                sectionSelect.innerHTML = '<option value="">Select Section</option>';
                const sections = result.data || result;
                sections.forEach(section => {
                    sectionSelect.innerHTML += `<option value="${section.id}">${section.section_name}</option>`;
                });
            })
            .catch(error => {
                console.error('Error:', error);
                sectionSelect.innerHTML = '<option value="">Error loading sections</option>';
            });
    });
    
    // Report form submission
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        generateReport();
    });
});

function generateReport() {
    const formData = new FormData(document.getElementById('reportForm'));
    const data = Object.fromEntries(formData);
    
    const params = new URLSearchParams(data);
    
    fetch(`{{ route('teacher.student.attendance.report.data') }}?${params}`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                document.getElementById('reportCard').classList.remove('d-none');
                document.getElementById('totalSessions').textContent = result.total_sessions;
                renderReport(result.data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error generating report');
        });
}

function renderReport(data) {
    const tbody = document.getElementById('reportTableBody');
    tbody.innerHTML = '';
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No data available</td></tr>';
        return;
    }
    
    data.forEach(student => {
        const percentageClass = student.percentage >= 75 ? 'success' : 
                               student.percentage >= 50 ? 'warning' : 'danger';
        
        const statusText = student.percentage >= 75 ? 'Good' : 
                          student.percentage >= 50 ? 'Average' : 'Poor';
        
        const row = `
            <tr>
                <td>${student.roll_number}</td>
                <td>${student.name}</td>
                <td><span class="badge bg-success">${student.present}</span></td>
                <td><span class="badge bg-danger">${student.absent}</span></td>
                <td><span class="badge bg-warning">${student.late}</span></td>
                <td><span class="badge bg-info">${student.leave}</span></td>
                <td><strong>${student.percentage}%</strong></td>
                <td><span class="badge bg-${percentageClass}">${statusText}</span></td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}

function exportReport() {
    // Simple CSV export
    const table = document.getElementById('reportTable');
    let csv = [];
    
    // Headers
    const headers = [];
    table.querySelectorAll('thead th').forEach(th => {
        headers.push(th.textContent);
    });
    csv.push(headers.join(','));
    
    // Data
    table.querySelectorAll('tbody tr').forEach(tr => {
        const row = [];
        tr.querySelectorAll('td').forEach(td => {
            row.push(td.textContent.trim());
        });
        if (row.length > 0) {
            csv.push(row.join(','));
        }
    });
    
    // Download
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `attendance_report_${new Date().getTime()}.csv`;
    a.click();
}
</script>
@endsection
