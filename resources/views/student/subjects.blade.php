@extends('student.layout')

@section('title', 'Subjects')
@section('page-title', 'My Subjects')

@section('content')
<div class="row">
    @forelse($subjects as $subject)
    <div class="col-md-4 mb-4">
        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="fas fa-book"></i>
            </div>
            <h5 style="color: #5a5c69; font-weight: 700; margin-bottom: 10px;">{{ $subject->subject_name }}</h5>
            <p style="color: #858796; margin-bottom: 10px;">
                <i class="fas fa-code me-2"></i>Code: {{ $subject->subject_code ?? 'N/A' }}
            </p>
            @if($subject->teachers && $subject->teachers->count() > 0)
                <p style="color: #858796; margin: 0;">
                    <i class="fas fa-user-tie me-2"></i>
                    Teacher: {{ $subject->teachers->first()->first_name }} {{ $subject->teachers->first()->last_name }}
                </p>
            @else
                <p style="color: #858796; margin: 0;">
                    <i class="fas fa-user-tie me-2"></i>Teacher: Not Assigned
                </p>
            @endif
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="stat-card">
            <div style="text-align: center; padding: 60px;">
                <i class="fas fa-book" style="font-size: 64px; opacity: 0.3; color: #858796; margin-bottom: 20px;"></i>
                <h5 style="color: #858796;">No subjects assigned yet</h5>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
