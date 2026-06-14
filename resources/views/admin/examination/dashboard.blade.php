@extends('admin.layouts.horizontal')
@section('title', 'Examination Dashboard')
@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-tachometer-alt me-2"></i>Examination Dashboard</h5>
    </div>
    <div class="content-card-body">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Exams</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_exams'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Ongoing Exams</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['ongoing_exams'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed Exams</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['completed_exams'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Published Results</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['published_results'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-trophy fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="mb-0 font-weight-bold text-primary">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <a href="{{ route('admin.examination.exams') }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-plus"></i> Create New Exam
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.examination.marks.entry') }}" class="btn btn-success btn-block">
                                    <i class="fas fa-pen"></i> Enter Marks
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.examination.results') }}" class="btn btn-info btn-block">
                                    <i class="fas fa-trophy"></i> View Results
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.examination.grade.system') }}" class="btn btn-warning btn-block">
                                    <i class="fas fa-graduation-cap"></i> Grade System
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Exams -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Exams</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Exam Code</th>
                                <th>Exam Name</th>
                                <th>Class</th>
                                <th>Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentExams as $exam)
                            <tr>
                                <td>{{ $exam->exam_code ?? 'N/A' }}</td>
                                <td>{{ $exam->name ?? 'N/A' }}</td>
                                <td>{{ $exam->class->class_name ?? 'N/A' }}</td>
                                <td><span class="badge bg-info">{{ strtoupper(str_replace('_', ' ', $exam->exam_type ?? 'N/A')) }}</span></td>
                                <td>{{ $exam->start_date ? $exam->start_date->format('d M Y') : 'N/A' }}</td>
                                <td>{{ $exam->end_date ? $exam->end_date->format('d M Y') : 'N/A' }}</td>
                                <td>
                                    @if(isset($exam->status))
                                        @if($exam->status == 'scheduled')
                                            <span class="badge bg-warning">Scheduled</span>
                                        @elseif($exam->status == 'ongoing')
                                            <span class="badge bg-primary">Ongoing</span>
                                        @elseif($exam->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $exam->status }}</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.examination.exam.subjects', $exam->id) }}" class="btn btn-sm btn-info" title="Manage Subjects">
                                        <i class="fas fa-book"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle"></i> No exams found. 
                                        <a href="{{ route('admin.examination.exams') }}">Create your first exam</a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }
.btn-block { display: block; width: 100%; }
</style>
@endpush
@endsection
