@extends('admin.layouts.horizontal')
@section('title', 'Examination Results')
@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-trophy me-2"></i>Examination Results</h5>
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
                        <th>Exam Name</th>
                        <th>Class</th>
                        <th>Exam Type</th>
                        <th>Status</th>
                        <th>Result Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                    <tr>
                        <td>{{ $exam->name }}</td>
                        <td>{{ $exam->class->class_name }}</td>
                        <td><span class="badge bg-info">{{ strtoupper(str_replace('_', ' ', $exam->exam_type)) }}</span></td>
                        <td>
                            @if($exam->status == 'completed')
                                <span class="badge bg-success">Completed</span>
                            @else
                                <span class="badge bg-warning">{{ ucfirst($exam->status) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($exam->result_published)
                                <span class="badge bg-success">Published</span>
                            @else
                                <span class="badge bg-secondary">Not Published</span>
                            @endif
                        </td>
                        <td>
                            @if(!$exam->result_published)
                                <form action="{{ route('admin.examination.results.generate') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                                    <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Generate results for this exam?')">
                                        <i class="fas fa-calculator"></i> Generate
                                    </button>
                                </form>
                                <form action="{{ route('admin.examination.results.publish') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Publish results? Students will be able to see them.')">
                                        <i class="fas fa-upload"></i> Publish
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('admin.examination.results.view', $exam->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No completed exams found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
