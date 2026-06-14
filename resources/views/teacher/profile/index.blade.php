@extends('layouts.teacher')

@section('title', 'My Profile')

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
                <div class="row">
                    <!-- Profile Card -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <img id="profileImage" src="{{ $teacher->photo ? asset('storage/' . $teacher->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($teacher->full_name) . '&size=150&background=667eea&color=fff' }}" 
                                     class="rounded-circle mb-3" 
                                     style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #667eea;" 
                                     alt="Profile Photo">
                                <h4>{{ $teacher->full_name }}</h4>
                                <p class="text-muted">{{ $teacher->employee_id }}</p>
                                <p class="text-muted">{{ $teacher->designation ?? 'Teacher' }}</p>
                                
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                    <i class="fas fa-edit"></i> Edit Profile
                                </button>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">Quick Stats</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subjects Teaching:</span>
                                    <strong>{{ $subjects->count() }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Classes Assigned:</span>
                                    <strong>{{ $classes->count() }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Experience:</span>
                                    <strong>{{ $teacher->experience ?? 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="col-md-8">
                        <!-- Personal Information -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted">First Name</label>
                                        <p class="fw-bold">{{ $teacher->first_name }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted">Last Name</label>
                                        <p class="fw-bold">{{ $teacher->last_name }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted">Email</label>
                                        <p class="fw-bold">{{ $teacher->email }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted">Phone</label>
                                        <p class="fw-bold">{{ $teacher->phone ?? 'Not provided' }}</p>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="text-muted">Address</label>
                                        <p class="fw-bold">{{ $teacher->address ?? 'Not provided' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted">Date of Birth</label>
                                        <p class="fw-bold">{{ $teacher->date_of_birth ? $teacher->date_of_birth->format('d M, Y') : 'Not provided' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted">Gender</label>
                                        <p class="fw-bold">{{ $teacher->gender ?? 'Not provided' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Subjects Teaching -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-book me-2"></i>Subjects Teaching</h5>
                            </div>
                            <div class="card-body">
                                @if($subjects->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Subject Code</th>
                                                    <th>Subject Name</th>
                                                    <th>Type</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($subjects as $subject)
                                                    <tr>
                                                        <td>{{ $subject->subject_code }}</td>
                                                        <td>{{ $subject->subject_name }}</td>
                                                        <td><span class="badge bg-info">{{ $subject->subject_type }}</span></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted">No subjects assigned yet.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Classes Assigned -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Classes Assigned</h5>
                            </div>
                            <div class="card-body">
                                @if($classes->count() > 0)
                                    <div class="row">
                                        @foreach($classes as $class)
                                            <div class="col-md-6 mb-3">
                                                <div class="border rounded p-3">
                                                    <h6>{{ $class->class_name }}</h6>
                                                    <p class="text-muted mb-0">
                                                        Sections: 
                                                        @foreach($class->sections as $section)
                                                            <span class="badge bg-secondary">{{ $section->section_name }}</span>
                                                        @endforeach
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">No classes assigned yet.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editProfileForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" value="{{ $teacher->first_name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" value="{{ $teacher->last_name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" value="{{ $teacher->phone }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2">{{ $teacher->address }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profile Photo</label>
                        <input type="file" class="form-control" name="photo" id="photoInput" accept="image/*">
                        <div class="mt-2">
                            <img id="photoPreview" src="" style="max-width: 150px; display: none;" class="rounded">
                        </div>
                        <small class="text-muted">Max size: 2MB. Formats: JPG, PNG, GIF</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Photo preview
document.getElementById('photoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreview').src = e.target.result;
            document.getElementById('photoPreview').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});

document.getElementById('editProfileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';
    
    fetch('{{ route('teacher.profile.update') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const modal = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
            modal.hide();
            
            // Show alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            
            // Reload after 1 second
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('Error: ' + data.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Save Changes';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating profile');
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Save Changes';
    });
});
</script>
@endsection
