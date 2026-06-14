@extends('admin.layouts.horizontal')
@section('title', 'My Profile')
@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-user-circle me-2"></i>My Profile</h5>
    </div>
    <div class="content-card-body">
        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <div class="profile-photo-section">
                    @if($admin->photo)
                        <img id="profilePhotoPreview" src="{{ asset('storage/'.$admin->photo) }}" alt="Profile" style="width: 200px; height: 200px; object-fit: cover; border-radius: 50%; border: 5px solid #667eea; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                    @else
                        <img id="profilePhotoPreview" src="https://ui-avatars.com/api/?name={{ urlencode($admin->username) }}&size=200&background=667eea&color=fff" alt="Profile" style="width: 200px; height: 200px; object-fit: cover; border-radius: 50%; border: 5px solid #667eea; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                    @endif
                    <h4 class="mt-3">{{ $admin->username }}</h4>
                    <p class="text-muted">{{ $admin->email }}</p>
                </div>
            </div>
            <div class="col-md-8">
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#edit-profile">Edit Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#change-password">Change Password</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Edit Profile Tab -->
                    <div id="edit-profile" class="tab-pane fade show active">
                        <form id="updateProfileForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" class="form-control" value="{{ $admin->username }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ $admin->email }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" value="{{ $admin->phone ?? '' }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Profile Photo</label>
                                    <input type="file" name="photo" class="form-control" accept="image/*" onchange="previewPhoto(this)">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                        </form>
                    </div>

                    <!-- Change Password Tab -->
                    <div id="change-password" class="tab-pane fade">
                        <form id="changePasswordForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Current Password <span class="text-danger">*</span></label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">New Password <span class="text-danger">*</span></label>
                                    <input type="password" name="new_password" class="form-control" required minlength="6">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                    <input type="password" name="new_password_confirmation" class="form-control" required minlength="6">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-2"></i>Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePhotoPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Update Profile Form
document.getElementById('updateProfileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
    
    fetch('{{ route("admin.profile.update") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                timer: 2000
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message || 'Failed to update profile'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'An error occurred while updating'
        });
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Profile';
    });
});

// Change Password Form
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Changing...';
    
    fetch('{{ route("admin.profile.change.password") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                timer: 2000
            });
            this.reset();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message || 'Failed to change password'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'An error occurred'
        });
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-key me-2"></i>Change Password';
    });
});
</script>
@endsection
