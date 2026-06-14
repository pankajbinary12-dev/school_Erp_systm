@extends('admin.layouts.horizontal')
@section('title', 'View Results')
@section('content')
<div class="content-card">
    <div class="content-card-header d-flex justify-content-between align-items-center">
        <div>
            <h5><i class="fas fa-trophy me-2"></i>Exam Results</h5>
            <small class="text-muted">{{ $exam->name }} - {{ $exam->class->class_name }}</small>
        </div>
        <a href="{{ route('admin.examination.results') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="content-card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Rank</th>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th>Total Marks</th>
                        <th>Max Marks</th>
                        <th>Percentage</th>
                        <th>Grade</th>
                        <th>Result</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $result)
                    <tr>
                        <td>
                            @if($result->rank)
                                <strong>{{ $result->rank }}</strong>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $result->student->roll_no ?? 'N/A' }}</td>
                        <td>{{ $result->student->name }}</td>
                        <td>{{ number_format($result->total_marks_obtained, 2) }}</td>
                        <td>{{ number_format($result->total_max_marks, 2) }}</td>
                        <td>{{ number_format($result->percentage, 2) }}%</td>
                        <td><span class="badge bg-primary">{{ $result->grade }}</span></td>
                        <td>
                            @if($result->result == 'pass')
                                <span class="badge bg-success">PASS</span>
                            @elseif($result->result == 'fail')
                                <span class="badge bg-danger">FAIL</span>
                            @else
                                <span class="badge bg-secondary">{{ strtoupper($result->result) }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.examination.report.card', [$exam->id, $result->student_id]) }}" 
                               class="btn btn-sm btn-info" target="_blank">
                                <i class="fas fa-file-alt"></i> Report Card
                            </a>
                            <a href="{{ route('admin.examination.report.card.download', [$exam->id, $result->student_id]) }}" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-download"></i> PDF
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">No results found. Please generate results first.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $results->links() }}
    </div>
</div>
@endsection
