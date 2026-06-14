@extends('admin.layouts.horizontal')
@section('title', 'School Info')
@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-school me-2"></i>School Information</h5>
    </div>
    <div class="content-card-body">
        <form id="schoolInfoForm" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">School Name <span class="text-danger">*</span></label>
                    <input type="text" name="school_name" class="form-control" value="{{ $school->school_name ?? '' }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">School Code</label>
                    <input type="text" name="school_code" class="form-control" value="{{ $school->school_code ?? '' }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $school->email ?? '' }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ $school->phone ?? '' }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Mobile</label>
                    <input type="text" name="mobile" class="form-control" value="{{ $school->mobile ?? '' }}">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2">{{ $school->address ?? '' }}</textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="{{ $school->city ?? '' }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control" value="{{ $school->state ?? '' }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Pincode</label>
                    <input type="text" name="pincode" class="form-control" value="{{ $school->pincode ?? '' }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Website</label>
                    <input type="url" name="website" class="form-control" value="{{ $school->website ?? '' }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Affiliation No</label>
                    <input type="text" name="affiliation_no" class="form-control" value="{{ $school->affiliation_no ?? '' }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Board</label>
                    <input type="text" name="board" class="form-control" value="{{ $school->board ?? '' }}" placeholder="e.g., CBSE, ICSE, State Board">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Principal Name</label>
                    <input type="text" name="principal_name" class="form-control" value="{{ $school->principal_name ?? '' }}">
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-3 mb-3">
                    <label class="form-label">School Logo</label>
                    <input type="file" name="logo" class="form-control" accept="image/*" onchange="previewImage(this, 'logoPreview')">
                    @if($school && $school->logo)
                        <img id="logoPreview" src="{{ asset('storage/'.$school->logo) }}" class="mt-2" style="max-width: 150px; border: 2px solid #ddd; padding: 5px; border-radius: 5px;">
                    @else
                        <img id="logoPreview" src="" class="mt-2" style="max-width: 150px; border: 2px solid #ddd; padding: 5px; border-radius: 5px; display: none;">
                    @endif
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Favicon</label>
                    <input type="file" name="favicon" class="form-control" accept="image/*" onchange="previewImage(this, 'faviconPreview')">
                    @if($school && $school->favicon)
                        <img id="faviconPreview" src="{{ asset('storage/'.$school->favicon) }}" class="mt-2" style="max-width: 100px; border: 2px solid #ddd; padding: 5px; border-radius: 5px;">
                    @else
                        <img id="faviconPreview" src="" class="mt-2" style="max-width: 100px; border: 2px solid #ddd; padding: 5px; border-radius: 5px; display: none;">
                    @endif
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Header Image</label>
                    <input type="file" name="header_image" class="form-control" accept="image/*" onchange="previewImage(this, 'headerPreview')">
                    @if($school && $school->header_image)
                        <img id="headerPreview" src="{{ asset('storage/'.$school->header_image) }}" class="mt-2" style="max-width: 200px; border: 2px solid #ddd; padding: 5px; border-radius: 5px;">
                    @else
                        <img id="headerPreview" src="" class="mt-2" style="max-width: 200px; border: 2px solid #ddd; padding: 5px; border-radius: 5px; display: none;">
                    @endif
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Principal Signature</label>
                    <input type="file" name="principal_signature" class="form-control" accept="image/*" onchange="previewImage(this, 'signaturePreview')">
                    @if($school && $school->principal_signature)
                        <img id="signaturePreview" src="{{ asset('storage/'.$school->principal_signature) }}" class="mt-2" style="max-width: 150px; border: 2px solid #ddd; padding: 5px; border-radius: 5px;">
                    @else
                        <img id="signaturePreview" src="" class="mt-2" style="max-width: 150px; border: 2px solid #ddd; padding: 5px; border-radius: 5px; display: none;">
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">About School</label>
                    <textarea name="about" class="form-control" rows="4">{{ $school->about ?? '' }}</textarea>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save School Information
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('schoolInfoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    
    fetch('{{ route("admin.settings.school.update") }}', {
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
                text: data.message || 'Failed to update school information'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'An error occurred while saving'
        });
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save School Information';
    });
});
</script>
@endsection
