@extends('admin.layouts.horizontal')
@section('title', 'Report Card')
@section('content')
<div class="content-card">
    <div class="content-card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-file-alt me-2"></i>Report Card</h5>
        <div>
            <a href="{{ route('admin.examination.results.view', $exam->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('admin.examination.report.card.download', [$exam->id, $student->id]) }}" class="btn btn-primary">
                <i class="fas fa-download"></i> Download PDF
            </a>
        </div>
    </div>
    <div class="content-card-body">
        <div class="report-card">
            <div class="text-center mb-4">
                <h3>REPORT CARD</h3>
                <h5>{{ $exam->name }}</h5>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Student Name:</th>
                            <td>{{ $student->name }}</td>
                        </tr>
                        <tr>
                            <th>Admission No:</th>
                            <td>{{ $student->admission_no }}</td>
                        </tr>
                        <tr>
                            <th>Roll No:</th>
                            <td>{{ $student->roll_no ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Class:</th>
                            <td>{{ $student->class->class_name }}</td>
                        </tr>
                        <tr>
                            <th>Exam Type:</th>
                            <td>{{ strtoupper(str_replace('_', ' ', $exam->exam_type)) }}</td>
                        </tr>
                        <tr>
                            <th>Exam Date:</th>
                            <td>{{ $exam->start_date->format('d M Y') }} to {{ $exam->end_date->format('d M Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Subject</th>
                        <th>Theory</th>
                        <th>Practical</th>
                        <th>Total</th>
                        <th>Max Marks</th>
                        <th>Percentage</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($marks as $mark)
                    <tr>
                        <td>{{ $mark->examSubject->subject->name }}</td>
                        <td>{{ $mark->theory_marks ?? '-' }}</td>
                        <td>{{ $mark->practical_marks ?? '-' }}</td>
                        <td><strong>{{ $mark->total_marks }}</strong></td>
                        <td>{{ $mark->examSubject->total_max_marks }}</td>
                        <td>{{ number_format(($mark->total_marks / $mark->examSubject->total_max_marks) * 100, 2) }}%</td>
                        <td>
                            @if($mark->is_passed)
                                <span class="badge bg-success">Pass</span>
                            @else
                                <span class="badge bg-danger">Fail</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="3">Total</th>
                        <th>{{ number_format($result->total_marks_obtained, 2) }}</th>
                        <th>{{ number_format($result->total_max_marks, 2) }}</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>

            <div class="row mt-4">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="50%">Percentage:</th>
                            <td><strong>{{ number_format($result->percentage, 2) }}%</strong></td>
                        </tr>
                        <tr>
                            <th>Grade:</th>
                            <td><strong>{{ $result->grade }}</strong></td>
                        </tr>
                        <tr>
                            <th>Result:</th>
                            <td>
                                @if($result->result == 'pass')
                                    <strong class="text-success">PASS</strong>
                                @else
                                    <strong class="text-danger">FAIL</strong>
                                @endif
                            </td>
                        </tr>
                        @if($result->rank)
                        <tr>
                            <th>Rank:</th>
                            <td><strong>{{ $result->rank }} / {{ $result->total_students }}</strong></td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.report-card {
    background: white;
    padding: 30px;
}
@media print {
    .content-card-header, .btn { display: none; }
}
</style>
@endpush
@endsection
