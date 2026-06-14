@extends('admin.layouts.horizontal')
@section('title', 'Exam Subjects')
@section('content')
<div class="content-card">
    <div class="content-card-header d-flex justify-content-between align-items-center">
        <div>
            <h5><i class="fas fa-book me-2"></i>Exam Subjects</h5>
            <small class="text-muted">{{ $exam->name }} - {{ $exam->class->class_name }}</small>
        </div>
        <div>
            <a href="{{ route('admin.examination.exams') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                <i class="fas fa-plus"></i> Add Subject
            </button>
        </div>
    </div>
    <div class="content-card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Subject</th>
                        <th>Theory Marks</th>
                        <th>Practical Marks</th>
                        <th>Total Marks</th>
                        <th>Passing Marks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exam->examSubjects as $examSubject)
                    <tr>
                        <td>{{ $examSubject->subject->name }}</td>
                        <td>{{ $examSubject->max_marks }}</td>
                        <td>{{ $examSubject->practical_marks }}</td>
                        <td><strong>{{ $examSubject->total_max_marks }}</strong></td>
                        <td>{{ $examSubject->passing_marks }}</td>
                        <td>
                            <form action="{{ route('admin.examination.exam.subject.delete', $examSubject->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No subjects added yet</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="3" class="text-end">Total Exam Marks:</th>
                        <th>{{ $exam->total_marks ?? 0 }}</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.examination.exam.subject.store') }}" method="POST">
                @csrf
                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">Add Subject to Exam</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Subject *</label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Theory Marks *</label>
                        <input type="number" name="max_marks" class="form-control" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Practical Marks</label>
                        <input type="number" name="practical_marks" class="form-control" min="0" step="0.01" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Passing Marks *</label>
                        <input type="number" name="passing_marks" class="form-control" min="0" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
