@extends('layouts.teacher')

@section('title', 'My Subjects')

@section('content')
<div class="d-flex">
    <!-- Sidebar -->
    @include('teacher.partials.sidebar')

    <!-- Main Content -->
    <div class="main-content flex-grow-1">
        <!-- Navbar -->
        @include('teacher.partials.navbar')

        <!-- Content -->
        <div class="content-area">
            <div class="container-fluid">
                <h4 class="mb-4"><i class="fas fa-book me-2"></i>My Subjects</h4>

                <div class="row">
                    @forelse($subjects as $subject)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">{{ $subject->subject_name }}</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Subject Code:</strong> {{ $subject->subject_code }}</p>
                                    <p><strong>Type:</strong> <span class="badge bg-info">{{ $subject->subject_type }}</span></p>
                                    <p><strong>Description:</strong> {{ $subject->description ?? 'No description' }}</p>
                                    
                                    <hr>
                                    
                                    <h6>Assigned Classes:</h6>
                                    @if($subject->classes && $subject->classes->count() > 0)
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($subject->classes as $class)
                                                <span class="badge bg-secondary">{{ $class->class_name }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted">No classes assigned</p>
                                    @endif
                                </div>
                                <div class="card-footer">
                                    <a href="{{ route('teacher.assignments') }}?subject_id={{ $subject->id }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-tasks me-1"></i>View Assignments
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>No subjects assigned yet. Please contact admin.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
