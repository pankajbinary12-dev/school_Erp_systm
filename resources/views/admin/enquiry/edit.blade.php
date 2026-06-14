@extends('admin.layouts.horizontal')

@section('title', 'Edit Enquiry')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-edit"></i> Edit Enquiry - {{ $enquiry->enquiry_number }}</h4>
            <a href="{{ route('admin.enquiry.view', $enquiry->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

<form action="{{ route('admin.enquiry.update', $enquiry->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <!-- Student Information -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0"><i class="fas fa-user"></i> Student Information</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $enquiry->first_name) }}" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $enquiry->last_name) }}" required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $enquiry->date_of_birth->format('Y-m-d')) }}" required>
                    @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender', $enquiry->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $enquiry->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $enquiry->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $enquiry->email) }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $enquiry->phone) }}" required>
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Address Information -->
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white">
            <h6 class="m-0"><i class="fas fa-map-marker-alt"></i> Address Information</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">Address <span class="text-danger">*</span></label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" required>{{ old('address', $enquiry->address) }}</textarea>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">City <span class="text-danger">*</span></label>
                    <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $enquiry->city) }}" required>
                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">State <span class="text-danger">*</span></label>
                    <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state', $enquiry->state) }}" required>
                    @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pincode <span class="text-danger">*</span></label>
                    <input type="text" name="pincode" class="form-control @error('pincode') is-invalid @enderror" value="{{ old('pincode', $enquiry->pincode) }}" required>
                    @error('pincode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Academic Information -->
    <div class="card shadow mb-4">
        <div class="card-header bg-info text-white">
            <h6 class="m-0"><i class="fas fa-graduation-cap"></i> Academic Information</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Class <span class="text-danger">*</span></label>
                    <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id', $enquiry->class_id) == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                        @endforeach
                    </select>
                    @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Session <span class="text-danger">*</span></label>
                    <select name="session_id" class="form-select @error('session_id') is-invalid @enderror" required>
                        <option value="">Select Session</option>
                        @foreach($sessions as $session)
                        <option value="{{ $session->id }}" {{ old('session_id', $enquiry->session_id) == $session->id ? 'selected' : '' }}>{{ $session->session_name }}</option>
                        @endforeach
                    </select>
                    @error('session_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Previous School</label>
                    <input type="text" name="previous_school" class="form-control @error('previous_school') is-invalid @enderror" value="{{ old('previous_school', $enquiry->previous_school) }}">
                    @error('previous_school')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Previous Class</label>
                    <input type="text" name="previous_class" class="form-control @error('previous_class') is-invalid @enderror" value="{{ old('previous_class', $enquiry->previous_class) }}">
                    @error('previous_class')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Previous Percentage</label>
                    <input type="number" step="0.01" name="previous_percentage" class="form-control @error('previous_percentage') is-invalid @enderror" value="{{ old('previous_percentage', $enquiry->previous_percentage) }}">
                    @error('previous_percentage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Parent Information -->
    <div class="card shadow mb-4">
        <div class="card-header bg-warning text-dark">
            <h6 class="m-0"><i class="fas fa-users"></i> Parent Information</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Father Name <span class="text-danger">*</span></label>
                    <input type="text" name="father_name" class="form-control @error('father_name') is-invalid @enderror" value="{{ old('father_name', $enquiry->father_name) }}" required>
                    @error('father_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Father Phone <span class="text-danger">*</span></label>
                    <input type="text" name="father_phone" class="form-control @error('father_phone') is-invalid @enderror" value="{{ old('father_phone', $enquiry->father_phone) }}" required>
                    @error('father_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Father Occupation</label>
                    <input type="text" name="father_occupation" class="form-control @error('father_occupation') is-invalid @enderror" value="{{ old('father_occupation', $enquiry->father_occupation) }}">
                    @error('father_occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Mother Name <span class="text-danger">*</span></label>
                    <input type="text" name="mother_name" class="form-control @error('mother_name') is-invalid @enderror" value="{{ old('mother_name', $enquiry->mother_name) }}" required>
                    @error('mother_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Mother Phone</label>
                    <input type="text" name="mother_phone" class="form-control @error('mother_phone') is-invalid @enderror" value="{{ old('mother_phone', $enquiry->mother_phone) }}">
                    @error('mother_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Mother Occupation</label>
                    <input type="text" name="mother_occupation" class="form-control @error('mother_occupation') is-invalid @enderror" value="{{ old('mother_occupation', $enquiry->mother_occupation) }}">
                    @error('mother_occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label">Annual Income</label>
                    <input type="number" step="0.01" name="annual_income" class="form-control @error('annual_income') is-invalid @enderror" value="{{ old('annual_income', $enquiry->annual_income) }}">
                    @error('annual_income')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="card shadow mb-4">
        <div class="card-header bg-secondary text-white">
            <h6 class="m-0"><i class="fas fa-info-circle"></i> Additional Information</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Source</label>
                    <select name="source" class="form-select @error('source') is-invalid @enderror">
                        <option value="">Select Source</option>
                        <option value="Walk-in" {{ old('source', $enquiry->source) == 'Walk-in' ? 'selected' : '' }}>Walk-in</option>
                        <option value="Phone" {{ old('source', $enquiry->source) == 'Phone' ? 'selected' : '' }}>Phone</option>
                        <option value="Website" {{ old('source', $enquiry->source) == 'Website' ? 'selected' : '' }}>Website</option>
                        <option value="Reference" {{ old('source', $enquiry->source) == 'Reference' ? 'selected' : '' }}>Reference</option>
                        <option value="Social Media" {{ old('source', $enquiry->source) == 'Social Media' ? 'selected' : '' }}>Social Media</option>
                        <option value="Other" {{ old('source', $enquiry->source) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('source')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Reference By</label>
                    <input type="text" name="reference_by" class="form-control @error('reference_by') is-invalid @enderror" value="{{ old('reference_by', $enquiry->reference_by) }}">
                    @error('reference_by')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Registration Fee <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="registration_fee" class="form-control @error('registration_fee') is-invalid @enderror" value="{{ old('registration_fee', $enquiry->registration_fee) }}" required>
                    @error('registration_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" rows="3">{{ old('remarks', $enquiry->remarks) }}</textarea>
                    @error('remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Buttons -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.enquiry.view', $enquiry->id) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Enquiry
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
