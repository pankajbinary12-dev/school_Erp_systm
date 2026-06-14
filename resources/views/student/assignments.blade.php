@extends('student.layout')

@section('title', 'Assignments')
@section('page-title', 'Assignments & Homework')

@section('content')
<div class="row">
    @forelse($assignments as $assignment)
    <div class="col-md-6 mb-4">
        <div class="stat-card {{ in_array($assignment->id, $submissions) ? 'success' : 'warning' }}">
            <h5 style="color: #5a5c69; font-weight: 700; margin-bottom: 10px;">
                {{ $assignment->title }}
            </h5>
            <p style="color: #858796; margin-bottom: 10px;">
                <i class="fas fa-book me-2"></i>{{ $assignment->subject->subject_name ?? 'N/A' }}
            </p>
            <p style="color: #858796; margin-bottom: 10px;">
                <i class="fas fa-user-tie me-2"></i>{{ $assignment->teacher->first_name ?? 'N/A' }} {{ $assignment->teacher->last_name ?? '' }}
            </p>
            <p style="color: #858796; margin-bottom: 15px;">{{ $assignment->description }}</p>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <span style="color: #858796; font-size: 14px;">
                    <i class="fas fa-calendar me-1"></i>Due: {{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y') }}
                </span>
                @if(in_array($assignment->id, $submissions))
                    <span class="badge" style="background: #1cc88a; color: white; padding: 6px 12px;">
                        <i class="fas fa-check-circle me-1"></i>Submitted
                    </span>
                @else
                    <span class="badge" style="background: #f6c23e; color: white; padding: 6px 12px;">
                        <i class="fas fa-clock me-1"></i>Pending
                    </span>
                @endif
            </div>

            @if(!in_array($assignment->id, $submissions))
            <button type="button" class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#submitModal{{ $assignment->id }}">
                <i class="fas fa-upload me-2"></i>Submit Assignment
            </button>

            <!-- Submit Modal -->
            <div class="modal fade" id="submitModal{{ $assignment->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Submit Assignment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('student.assignments.submit', $assignment->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Submission Text</label>
                                    <textarea name="submission_text" class="form-control" rows="4" placeholder="Enter your submission text (optional)"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Upload File</label>
                                    <input type="file" name="file" class="form-control">
                                    <small class="text-muted">Max size: 10MB</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="stat-card">
            <div style="text-align: center; padding: 60px;">
                <i class="fas fa-tasks" style="font-size: 64px; opacity: 0.3; color: #858796; margin-bottom: 20px;"></i>
                <h5 style="color: #858796;">No assignments available</h5>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection

@section('styles')
<style>
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
    }
    .btn-secondary {
        background: #858796;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
    }
</style>
@endsection
