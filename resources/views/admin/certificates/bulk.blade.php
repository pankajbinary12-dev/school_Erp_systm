@extends('admin.layouts.horizontal')
@section('title', 'Bulk Certificate Generation')

@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-layer-group me-2"></i>Bulk Certificate Generation</h5>
    </div>

    <div class="content-card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Bulk Generation:</strong> Generate certificates for all students in a class at once.
        </div>

        <form action="{{ route('admin.certificates.bulk.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Select Class <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" 
                                id="classSelect" required>
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Select Section (Optional)</label>
                        <select name="section_id" class="form-select" id="sectionSelect">
                            <option value="">All Sections</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Certificate Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="">-- Select Type --</option>
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Issue Date <span class="text-danger">*</span></label>
                        <input type="date" name="issue_date" 
                               class="form-control @error('issue_date') is-invalid @enderror" 
                               value="{{ date('Y-m-d') }}" required>
                        @error('issue_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Student Count Preview -->
            <div id="studentCount" class="alert alert-warning" style="display: none;">
                <i class="fas fa-users me-2"></i>
                <strong>Students Found:</strong> <span id="countNumber">0</span> students will receive certificates.
            </div>

            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Warning:</strong> This will generate certificates for all active students in the selected class/section. 
                This action cannot be undone.
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" onclick="return confirm('Generate certificates for all students in this class?')">
                    <i class="fas fa-layer-group me-1"></i>Generate Bulk Certificates
                </button>
                <a href="{{ route('admin.certificates.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('classSelect').addEventListener('change', function() {
    const classId = this.value;
    const sectionSelect = document.getElementById('sectionSelect');
    
    if (!classId) {
        sectionSelect.innerHTML = '<option value="">All Sections</option>';
        document.getElementById('studentCount').style.display = 'none';
        return;
    }
    
    // Load sections for this class (if you have sections)
    // For now, just load student count
    loadStudentCount(classId, '');
});

document.getElementById('sectionSelect').addEventListener('change', function() {
    const classId = document.getElementById('classSelect').value;
    const sectionId = this.value;
    
    if (classId) {
        loadStudentCount(classId, sectionId);
    }
});

function loadStudentCount(classId, sectionId) {
    let url = `{{ route('admin.certificates.students-by-class') }}?class_id=${classId}`;
    if (sectionId) {
        url += `&section_id=${sectionId}`;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            document.getElementById('countNumber').textContent = data.students.length;
            document.getElementById('studentCount').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
        });
}
</script>
@endsection
