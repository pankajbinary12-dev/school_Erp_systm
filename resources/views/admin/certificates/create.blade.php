@extends('admin.layouts.horizontal')
@section('title', 'Generate Certificate')

@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-certificate me-2"></i>Generate New Certificate</h5>
    </div>

    <div class="content-card-body">
        <form action="{{ route('admin.certificates.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Select Class <span class="text-danger">*</span></label>
                        <select class="form-select @error('class_id') is-invalid @enderror" 
                                id="classSelect" required>
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Select Student <span class="text-danger">*</span></label>
                        <select name="student_id" 
                                class="form-select @error('student_id') is-invalid @enderror" 
                                id="studentSelect" required>
                            <option value="">-- Select Student --</option>
                        </select>
                        @error('student_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Student Details Card -->
            <div id="studentDetails" class="card mb-3" style="display: none;">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Student Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Name:</strong> <span id="studentName"></span></p>
                            <p><strong>Roll No:</strong> <span id="studentRoll"></span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Father Name:</strong> <span id="studentFather"></span></p>
                            <p><strong>Class:</strong> <span id="studentClass"></span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>DOB:</strong> <span id="studentDOB"></span></p>
                            <p><strong>Admission No:</strong> <span id="studentAdmission"></span></p>
                        </div>
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

            <div class="mb-3">
                <label class="form-label">Remarks (Optional)</label>
                <textarea name="remarks" class="form-control" rows="3" 
                          placeholder="Any additional notes..."></textarea>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Note:</strong> Certificate number will be auto-generated. 
                You can preview and download the certificate after generation.
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-certificate me-1"></i>Generate Certificate
                </button>
                <a href="{{ route('admin.certificates.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

<script>
console.log('Certificate create page loaded');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    
    const classSelect = document.getElementById('classSelect');
    const studentSelect = document.getElementById('studentSelect');
    
    console.log('classSelect:', classSelect);
    console.log('studentSelect:', studentSelect);
    
    if (!classSelect || !studentSelect) {
        console.error('Elements not found!');
        return;
    }
    
    classSelect.addEventListener('change', function() {
        const classId = this.value;
        console.log('Class selected:', classId);
        
        studentSelect.innerHTML = '<option value="">-- Loading... --</option>';
        document.getElementById('studentDetails').style.display = 'none';
        
        if (!classId) {
            studentSelect.innerHTML = '<option value="">-- Select Student --</option>';
            return;
        }
        
        const url = `{{ route('admin.certificates.students-by-class') }}?class_id=${classId}`;
        console.log('Fetching from URL:', url);
        
        fetch(url)
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);
                studentSelect.innerHTML = '<option value="">-- Select Student --</option>';
                
                if (!data.students || data.students.length === 0) {
                    console.log('No students found');
                    studentSelect.innerHTML = '<option value="">-- No students in this class --</option>';
                    return;
                }
                
                data.students.forEach(student => {
                    const option = document.createElement('option');
                    option.value = student.id;
                    option.textContent = `${student.first_name} ${student.last_name} (${student.roll_number})`;
                    option.dataset.student = JSON.stringify(student);
                    studentSelect.appendChild(option);
                });
                console.log('Students loaded:', data.students.length);
            })
            .catch(error => {
                console.error('Error:', error);
                studentSelect.innerHTML = '<option value="">-- Error loading students --</option>';
            });
    });

    studentSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (!selectedOption.dataset.student) {
            document.getElementById('studentDetails').style.display = 'none';
            return;
        }
        
        const student = JSON.parse(selectedOption.dataset.student);
        
        document.getElementById('studentName').textContent = `${student.first_name} ${student.last_name}`;
        document.getElementById('studentRoll').textContent = student.roll_number || 'N/A';
        document.getElementById('studentFather').textContent = student.father_name || 'N/A';
        document.getElementById('studentClass').textContent = student.class?.class_name || 'N/A';
        document.getElementById('studentDOB').textContent = student.date_of_birth || 'N/A';
        document.getElementById('studentAdmission').textContent = student.admission_number || 'N/A';
        
        document.getElementById('studentDetails').style.display = 'block';
    });
});
</script>
