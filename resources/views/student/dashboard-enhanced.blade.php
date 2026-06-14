@extends('layouts.student')

@section('title', 'Student Dashboard')

@section('styles')
<style>
/* Modern Dashboard Styles */
:root {
    --primary: #667eea;
    --secondary: #764ba2;
    --success: #10b981;
    --danger: #ef4444;
    --warning: #f59e0b;
    --info: #3b82f6;
    --dark: #1f2937;
    --light: #f3f4f6;
}

.dashboard-container {
    background: var(--light);
    min-height: 100vh;
}

.stat-card {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    border-radius: 15px;
    padding: 20px;
    color: white;
    transition: transform 0.3s, box-shadow 0.3s;
    margin-bottom: 20px;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.stat-card i {
    font-size: 2.5rem;
    opacity: 0.8;
}

.card-modern {
    border: none;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: transform 0.3s;
}

.card-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.12);
}

.progress-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: conic-gradient(var(--success) 0% var(--attendance-percent), var(--light) var(--attendance-percent) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.progress-circle::before {
    content: '';
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: white;
    position: absolute;
}

.progress-value {
    position: relative;
    z-index: 1;
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--dark);
}

.subject-card {
    border-left: 4px solid var(--primary);
    transition: all 0.3s;
}

.subject-card:hover {
    border-left-color: var(--secondary);
    background: var(--light);
}

.assignment-badge {
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
}

.chart-container {
    position: relative;
    height: 300px;
}

/* Dark Mode */
body.dark-mode {
    background: #1a1a2e;
    color: #eee;
}

body.dark-mode .card-modern {
    background: #16213e;
    color: #eee;
}

body.dark-mode .stat-card {
    opacity: 0.95;
}

.dark-mode-toggle {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    cursor: pointer;
    z-index: 1000;
    transition: all 0.3s;
}

.dark-mode-toggle:hover {
    transform: scale(1.1);
}
</style>
@endsection

@section('content')
<div class="dashboard-container">
    <div class="d-flex">
        @include('student.partials.sidebar')

        <div class="main-content flex-grow-1">
            @include('student.partials.navbar')

            <div class="container-fluid p-4">
                <!-- Welcome Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card-modern p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2>Welcome back, {{ $student->first_name }}! 👋</h2>
                                    <p class="text-muted mb-0">Here's what's happening with your academics today.</p>
                                </div>
                                <div class="text-end">
                                    <h5 class="mb-0">{{ now()->format('l') }}</h5>
                                    <p class="text-muted mb-0">{{ now()->format('F d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Attendance</h6>
                                    <h2 class="mb-0" id="attendancePercent">--</h2>
                                    <small>This Month</small>
                                </div>
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Assignments</h6>
                                    <h2 class="mb-0" id="pendingAssignments">--</h2>
                                    <small>Pending</small>
                                </div>
                                <i class="fas fa-tasks"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Average Grade</h6>
                                    <h2 class="mb-0" id="averageGrade">--</h2>
                                    <small>Overall</small>
                                </div>
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Class Rank</h6>
                                    <h2 class="mb-0" id="classRank">--</h2>
                                    <small>Out of {{ $totalStudents ?? 0 }}</small>
                                </div>
                                <i class="fas fa-trophy"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <!-- Subjects & Grades -->
                        <div class="card-modern mb-4">
                            <div class="card-body">
                                <h5 class="mb-3"><i class="fas fa-book text-primary"></i> My Subjects & Grades</h5>
                                <div id="subjectsContainer">
                                    <div class="text-center py-3">
                                        <div class="spinner-border text-primary"></div>
                                        <p class="mt-2">Loading subjects...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Chart -->
                        <div class="card-modern mb-4">
                            <div class="card-body">
                                <h5 class="mb-3"><i class="fas fa-chart-bar text-success"></i> Attendance Overview</h5>
                                <div class="chart-container">
                                    <canvas id="attendanceChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Analytics -->
                        <div class="card-modern mb-4">
                            <div class="card-body">
                                <h5 class="mb-3"><i class="fas fa-chart-pie text-info"></i> Performance Analytics</h5>
                                <div class="chart-container">
                                    <canvas id="performanceChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-4">
                        <!-- Pending Assignments -->
                        <div class="card-modern mb-4">
                            <div class="card-body">
                                <h5 class="mb-3"><i class="fas fa-clipboard-list text-warning"></i> Pending Assignments</h5>
                                <div id="assignmentsContainer">
                                    <div class="text-center py-3">
                                        <div class="spinner-border text-warning"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Today's Timetable -->
                        <div class="card-modern mb-4">
                            <div class="card-body">
                                <h5 class="mb-3"><i class="fas fa-clock text-info"></i> Today's Schedule</h5>
                                <div id="timetableContainer">
                                    <div class="text-center py-3">
                                        <div class="spinner-border text-info"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <div class="card-modern mb-4">
                            <div class="card-body">
                                <h5 class="mb-3"><i class="fas fa-bell text-danger"></i> Notifications</h5>
                                <div id="notificationsContainer">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> Welcome to your dashboard!
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fees Status -->
                        <div class="card-modern mb-4">
                            <div class="card-body">
                                <h5 class="mb-3"><i class="fas fa-money-bill-wave text-success"></i> Fees Status</h5>
                                <div id="feesContainer">
                                    <div class="text-center py-3">
                                        <div class="spinner-border text-success"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dark Mode Toggle -->
<button class="dark-mode-toggle" id="darkModeToggle">
    <i class="fas fa-moon"></i>
</button>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Dark Mode Toggle
document.getElementById('darkModeToggle').addEventListener('click', function() {
    document.body.classList.toggle('dark-mode');
    const icon = this.querySelector('i');
    icon.classList.toggle('fa-moon');
    icon.classList.toggle('fa-sun');
    localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
});

// Load dark mode preference
if (localStorage.getItem('darkMode') === 'true') {
    document.body.classList.add('dark-mode');
    document.querySelector('#darkModeToggle i').classList.replace('fa-moon', 'fa-sun');
}

// Load Dashboard Data
document.addEventListener('DOMContentLoaded', function() {
    loadSubjects();
    loadAttendance();
    loadAssignments();
    loadTimetable();
    loadFees();
    loadPerformanceData();
});

function loadSubjects() {
    fetch('/student/subjects')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderSubjects(data.data);
            }
        })
        .catch(error => console.error('Error:', error));
}

function renderSubjects(subjects) {
    const container = document.getElementById('subjectsContainer');
    if (subjects.length === 0) {
        container.innerHTML = '<p class="text-muted">No subjects assigned</p>';
        return;
    }

    let html = '<div class="row">';
    subjects.forEach(subject => {
        const gradeColor = getGradeColor(subject.grade);
        html += `
            <div class="col-md-6 mb-3">
                <div class="subject-card card-modern p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${subject.subject_name}</h6>
                            <small class="text-muted">${subject.teacher_name || 'N/A'}</small>
                        </div>
                        <div class="text-end">
                            <h4 class="mb-0" style="color: ${gradeColor}">${subject.marks || '--'}/${subject.total_marks || 100}</h4>
                            <span class="badge" style="background: ${gradeColor}">${subject.grade || 'N/A'}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
}

function loadAttendance() {
    fetch('/student/attendance/summary')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('attendancePercent').textContent = data.percentage + '%';
                renderAttendanceChart(data.monthly);
            }
        })
        .catch(error => console.error('Error:', error));
}

function renderAttendanceChart(monthlyData) {
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.labels,
            datasets: [{
                label: 'Attendance %',
                data: monthlyData.values,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}

function loadAssignments() {
    fetch('/student/assignments/pending')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('pendingAssignments').textContent = data.count;
                renderAssignments(data.data);
            }
        })
        .catch(error => console.error('Error:', error));
}

function renderAssignments(assignments) {
    const container = document.getElementById('assignmentsContainer');
    if (assignments.length === 0) {
        container.innerHTML = '<p class="text-muted">No pending assignments</p>';
        return;
    }

    let html = '';
    assignments.forEach(assignment => {
        const daysLeft = Math.ceil((new Date(assignment.due_date) - new Date()) / (1000 * 60 * 60 * 24));
        const urgencyClass = daysLeft <= 1 ? 'danger' : daysLeft <= 3 ? 'warning' : 'info';
        
        html += `
            <div class="border-bottom pb-2 mb-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">${assignment.title}</h6>
                        <small class="text-muted">${assignment.subject_name}</small>
                    </div>
                    <span class="assignment-badge bg-${urgencyClass} text-white">
                        ${daysLeft}d left
                    </span>
                </div>
                <small class="text-muted">Due: ${new Date(assignment.due_date).toLocaleDateString()}</small>
            </div>
        `;
    });
    container.innerHTML = html;
}

function loadTimetable() {
    fetch('/student/timetable/today')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderTimetable(data.data);
            }
        })
        .catch(error => console.error('Error:', error));
}

function renderTimetable(periods) {
    const container = document.getElementById('timetableContainer');
    if (periods.length === 0) {
        container.innerHTML = '<p class="text-muted">No classes today</p>';
        return;
    }

    let html = '';
    periods.forEach(period => {
        html += `
            <div class="border-bottom pb-2 mb-2">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">${period.subject_name}</h6>
                        <small class="text-muted">${period.teacher_name}</small>
                    </div>
                    <div class="text-end">
                        <small class="text-primary">${period.start_time} - ${period.end_time}</small>
                    </div>
                </div>
            </div>
        `;
    });
    container.innerHTML = html;
}

function loadFees() {
    fetch('/student/fees/status')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderFees(data.data);
            }
        })
        .catch(error => console.error('Error:', error));
}

function renderFees(fees) {
    const container = document.getElementById('feesContainer');
    const paid = fees.paid || 0;
    const total = fees.total || 0;
    const pending = total - paid;
    const percentage = total > 0 ? ((paid / total) * 100).toFixed(1) : 0;

    container.innerHTML = `
        <div class="mb-3">
            <div class="d-flex justify-content-between mb-2">
                <span>Total Fees:</span>
                <strong>₹${total.toLocaleString()}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Paid:</span>
                <strong class="text-success">₹${paid.toLocaleString()}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Pending:</span>
                <strong class="text-danger">₹${pending.toLocaleString()}</strong>
            </div>
        </div>
        <div class="progress" style="height: 25px;">
            <div class="progress-bar bg-success" style="width: ${percentage}%">
                ${percentage}%
            </div>
        </div>
        ${pending > 0 ? '<small class="text-danger mt-2 d-block">Payment pending</small>' : '<small class="text-success mt-2 d-block">All fees paid</small>'}
    `;
}

function loadPerformanceData() {
    fetch('/student/performance/analytics')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('averageGrade').textContent = data.average_grade || 'N/A';
                document.getElementById('classRank').textContent = data.rank || '--';
                renderPerformanceChart(data.subjects);
            }
        })
        .catch(error => console.error('Error:', error));
}

function renderPerformanceChart(subjects) {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: subjects.map(s => s.name),
            datasets: [{
                label: 'Your Performance',
                data: subjects.map(s => s.percentage),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.2)',
                pointBackgroundColor: '#667eea'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}

function getGradeColor(grade) {
    const colors = {
        'A+': '#10b981', 'A': '#10b981',
        'B+': '#3b82f6', 'B': '#3b82f6',
        'C+': '#f59e0b', 'C': '#f59e0b',
        'D': '#ef4444', 'F': '#ef4444'
    };
    return colors[grade] || '#6b7280';
}
</script>
@endsection
