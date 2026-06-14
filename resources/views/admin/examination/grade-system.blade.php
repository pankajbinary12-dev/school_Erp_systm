@extends('admin.layouts.horizontal')
@section('title', 'Grade System')
@section('content')
<div class="content-card">
    <div class="content-card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-graduation-cap me-2"></i>Grade System</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGradeModal">
            <i class="fas fa-plus"></i> Add Grade
        </button>
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
                        <th>Grade</th>
                        <th>Min Percentage</th>
                        <th>Max Percentage</th>
                        <th>Grade Point</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grades as $grade)
                    <tr>
                        <td><strong>{{ $grade->grade }}</strong></td>
                        <td>{{ $grade->min_percentage }}%</td>
                        <td>{{ $grade->max_percentage }}%</td>
                        <td>{{ $grade->grade_point }}</td>
                        <td>{{ $grade->description }}</td>
                        <td>
                            @if($grade->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No grades found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Grade Modal -->
<div class="modal fade" id="addGradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.examination.grade.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Grade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Grade *</label>
                        <input type="text" name="grade" class="form-control" placeholder="e.g., A+, B, C" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Min Percentage *</label>
                        <input type="number" name="min_percentage" class="form-control" min="0" max="100" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Max Percentage *</label>
                        <input type="number" name="max_percentage" class="form-control" min="0" max="100" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Grade Point</label>
                        <input type="text" name="grade_point" class="form-control" placeholder="e.g., 10, 9, 8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" placeholder="e.g., Outstanding, Excellent">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Grade</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
