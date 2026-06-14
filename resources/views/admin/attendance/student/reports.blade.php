@extends('admin.layouts.horizontal')
@section('title', 'Attendance Reports')
@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-file-alt me-2"></i>Student Attendance Reports</h5>
    </div>
    <div class="content-card-body">
        <form action="{{ route('admin.attendance.student.generate.report') }}" method="POST" target="_blank">
            @csrf
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label">Report Type *</label>
                    <select name="report_type" id="report_type" class="form-select" required>
                        <option value="">Select Report Type</option>
                        <option value="daily">Daily Attendance</option>
                        <option value="monthly">Monthly Attendance</option>
                        <option value="student_wise">Student-wise Percentage</option>
                        <option value="defaulters">Defaulters List (&lt;75%)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Class *</label>
                    <select name="class_id" id="class_id" class="form-select" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Section</label>
                    <select name="section_id" id="section_id" class="form-select">
                        <option value="">All Sections</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Start Date *</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">End Date *</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" required>
                </div>
            </div>

            <div class="row mb-4" id="threshold_row" style="display: none;">
                <div class="col-md-3">
                    <label class="form-label">Attendance Threshold (%)</label>
                    <input type="number" name="threshold" class="form-control" value="75" min="0" max="100">
                    <small class="text-muted">Students below this percentage will be shown</small>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <label class="form-label">Format</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="format" id="format_web" value="web" checked>
                            <label class="form-check-label" for="format_web">
                                <i class="fas fa-desktop"></i> View Online
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="format" id="format_pdf" value="pdf">
                            <label class="form-check-label" for="format_pdf">
                                <i class="fas fa-file-pdf"></i> Download PDF
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-chart-bar"></i> Generate Report
                </button>
            </div>
        </form>

        <hr class="my-5">

        <div class="row">
            <div class="col-md-12">
                <h6 class="mb-3">Report Types:</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-calendar-day text-primary"></i> Daily Attendance</h6>
                                <p class="card-text">View attendance for a specific date. Shows all students with their attendance status.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-calendar-alt text-success"></i> Monthly Attendance</h6>
                                <p class="card-text">View attendance for a date range. Shows date-wise attendance for all students.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-user-graduate text-info"></i> Student-wise Percentage</h6>
                                <p class="card-text">View attendance percentage for each student in the selected date range.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-exclamation-triangle text-warning"></i> Defaulters List</h6>
                                <p class="card-text">View students with attendance below the threshold (default 75%).</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class_id');
    const sectionSelect = document.getElementById('section_id');
    const reportType = document.getElementById('report_type');
    const thresholdRow = document.getElementById('threshold_row');
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    // Set default dates
    const today = new Date().toISOString().split('T')[0];
    const weekAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
    startDate.value = weekAgo;
    endDate.value = today;

    // Load sections when class changes
    classSelect.addEventListener('change', function() {
        const classId = this.value;
        sectionSelect.innerHTML = '<option value="">All Sections</option>';
        sectionSelect.disabled = true;

        if (classId) {
            fetch(`/admin/attendance/student/sections/${classId}`)
                .then(r => r.json())
                .then(data => {
                    data.sections.forEach(section => {
                        const option = document.createElement('option');
                        option.value = section.id;
                        option.textContent = section.section_name;
                        sectionSelect.appendChild(option);
                    });
                    sectionSelect.disabled = false;
                });
        }
    });

    // Show/hide threshold based on report type
    reportType.addEventListener('change', function() {
        if (this.value === 'defaulters') {
            thresholdRow.style.display = 'block';
        } else {
            thresholdRow.style.display = 'none';
        }

        // For daily report, set start and end date to same
        if (this.value === 'daily') {
            endDate.value = startDate.value;
        }
    });

    // Sync end date with start date for daily report
    startDate.addEventListener('change', function() {
        if (reportType.value === 'daily') {
            endDate.value = this.value;
        }
    });
});
</script>
@endpush
@endsection
