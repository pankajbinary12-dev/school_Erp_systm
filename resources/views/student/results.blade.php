@extends('student.layout')

@section('title', 'Results')
@section('page-title', 'Exam Results')

@section('content')
<div class="row">
    @forelse($results as $result)
    <div class="col-md-6 mb-4">
        <div class="stat-card primary">
            <h5 style="color: #5a5c69; font-weight: 700; margin-bottom: 15px;">
                <i class="fas fa-trophy me-2"></i>{{ $result->exam->exam_name ?? 'N/A' }}
            </h5>
            
            <div class="row mb-3">
                <div class="col-4 text-center">
                    <small style="color: #858796;">Total Marks</small>
                    <h4 style="color: #5a5c69; font-weight: 700; margin: 5px 0;">{{ $result->total_marks }}</h4>
                </div>
                <div class="col-4 text-center">
                    <small style="color: #858796;">Percentage</small>
                    <h4 style="color: #5a5c69; font-weight: 700; margin: 5px 0;">{{ $result->percentage }}%</h4>
                </div>
                <div class="col-4 text-center">
                    <small style="color: #858796;">Grade</small>
                    <h4 style="color: #5a5c69; font-weight: 700; margin: 5px 0;">{{ $result->grade }}</h4>
                </div>
            </div>

            @if($result->marks && $result->marks->count() > 0)
            <div style="background: #f8f9fc; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                <h6 style="color: #5a5c69; font-weight: 600; margin-bottom: 10px;">Subject-wise Marks</h6>
                @foreach($result->marks as $mark)
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: #858796;">{{ $mark->subject->subject_name ?? 'N/A' }}</span>
                    <span style="color: #5a5c69; font-weight: 600;">{{ $mark->marks_obtained }}/{{ $mark->total_marks }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <div style="display: flex; gap: 10px;">
                <a href="{{ route('student.results.download', $result->id) }}" class="btn btn-primary btn-sm flex-fill">
                    <i class="fas fa-download me-2"></i>Download Report Card
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="stat-card">
            <div style="text-align: center; padding: 60px;">
                <i class="fas fa-trophy" style="font-size: 64px; opacity: 0.3; color: #858796; margin-bottom: 20px;"></i>
                <h5 style="color: #858796;">No results available yet</h5>
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
        text-decoration: none;
        display: inline-block;
    }
</style>
@endsection
