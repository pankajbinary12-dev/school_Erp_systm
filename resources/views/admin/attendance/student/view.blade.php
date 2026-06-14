@extends('admin.layouts.horizontal')
@section('title', 'View Attendance')
@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-eye me-2"></i>View Student Attendance</h5>
    </div>
    <div class="content-card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <label class="form-label">Start Date *</label>
                <input type="date" id="start_date" class="form-control" value="{{ date('Y-m-d', strtotime('-7 days')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date *</label>
                <input type="date" id="end_date" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Class *</label>
                <select id="class_id" class="form-select">
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Section</label>
                <select id="section_id" class="form-select" disabled>
                    <option value="">All Sections</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select id="status" class="form-select">
                    <option value="">All</option>
                    <option value="Present">Present</option>
                    <option value="Absent">Absent</option>
                    <option value="Leave">Leave</option>
                    <option value="Late">Late</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <button type="button" id="searchBtn" class="btn btn-primary">
                <i class="fas fa-search"></i> Search
            </button>
            <button type="button" id="resetBtn" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </button>
        </div>

        <div id="results" style="display: none;">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Roll No</th>
                            <th>Student Name</th>
                            <th>Class</th>
                            <th>Section</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Marked By</th>
                        </tr>
                    </thead>
                    <tbody id="records-tbody">
                    </tbody>
                </table>
            </div>
            <div id="pagination"></div>
        </div>

        <div id="loading" style="display: none;" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div id="no-results" style="display: none;" class="alert alert-info">
            <i class="fas fa-info-circle"></i> No attendance records found for the selected criteria.
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class_id');
    const sectionSelect = document.getElementById('section_id');
    const searchBtn = document.getElementById('searchBtn');
    const resetBtn = document.getElementById('resetBtn');
    const results = document.getElementById('results');
    const loading = document.getElementById('loading');
    const noResults = document.getElementById('no-results');

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

    // Search attendance
    searchBtn.addEventListener('click', function() {
        const classId = classSelect.value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        if (!classId || !startDate || !endDate) {
            alert('Please select class and date range');
            return;
        }

        loading.style.display = 'block';
        results.style.display = 'none';
        noResults.style.display = 'none';

        fetch('/admin/attendance/student/records', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                class_id: classId,
                section_id: sectionSelect.value,
                start_date: startDate,
                end_date: endDate,
                status: document.getElementById('status').value
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.records.data.length > 0) {
                displayRecords(data.records);
                results.style.display = 'block';
            } else {
                noResults.style.display = 'block';
            }
        })
        .finally(() => loading.style.display = 'none');
    });

    // Display records
    function displayRecords(records) {
        const tbody = document.getElementById('records-tbody');
        tbody.innerHTML = '';

        records.data.forEach(record => {
            const statusClass = record.status === 'Present' ? 'success' : 
                               record.status === 'Absent' ? 'danger' : 
                               record.status === 'Leave' ? 'warning' : 'info';

            const row = `
                <tr>
                    <td>${new Date(record.attendance_date).toLocaleDateString()}</td>
                    <td>${record.student.roll_no || 'N/A'}</td>
                    <td>${record.student.name}</td>
                    <td>${record.class.class_name}</td>
                    <td>${record.section ? record.section.section_name : 'N/A'}</td>
                    <td><span class="badge bg-${statusClass}">${record.status}</span></td>
                    <td>${record.remarks || '-'}</td>
                    <td>${record.marked_by || 'System'}</td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    // Reset
    resetBtn.addEventListener('click', function() {
        document.getElementById('start_date').value = '{{ date('Y-m-d', strtotime('-7 days')) }}';
        document.getElementById('end_date').value = '{{ date('Y-m-d') }}';
        classSelect.value = '';
        sectionSelect.value = '';
        document.getElementById('status').value = '';
        results.style.display = 'none';
        noResults.style.display = 'none';
    });
});
</script>
@endpush
@endsection
