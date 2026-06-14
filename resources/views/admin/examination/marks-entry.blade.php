@extends('admin.layouts.horizontal')
@section('title', 'Marks Entry')
@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-pen me-2"></i>Marks Entry</h5>
    </div>
    <div class="content-card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label">Select Exam *</label>
                <select id="exam_id" class="form-select">
                    <option value="">Select Exam</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}">{{ $exam->name }} - {{ $exam->class->class_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Select Subject *</label>
                <select id="exam_subject_id" class="form-select" disabled>
                    <option value="">Select Subject</option>
                </select>
            </div>
        </div>

        <div id="marks-form" style="display: none;">
            <div class="alert alert-info">
                <strong>Exam:</strong> <span id="selected-exam"></span><br>
                <strong>Subject:</strong> <span id="selected-subject"></span><br>
                <strong>Max Marks:</strong> Theory: <span id="theory-max"></span>, Practical: <span id="practical-max"></span>
            </div>

            <form id="marksEntryForm">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Roll No</th>
                                <th>Student Name</th>
                                <th>Theory Marks</th>
                                <th>Practical Marks</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="students-tbody">
                        </tbody>
                    </table>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Marks
                    </button>
                </div>
            </form>
        </div>

        <div id="loading" style="display: none;" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const examSelect = document.getElementById('exam_id');
    const subjectSelect = document.getElementById('exam_subject_id');
    const marksForm = document.getElementById('marks-form');
    const loading = document.getElementById('loading');

    examSelect.addEventListener('change', function() {
        const examId = this.value;
        subjectSelect.innerHTML = '<option value="">Select Subject</option>';
        subjectSelect.disabled = true;
        marksForm.style.display = 'none';

        if (examId) {
            loading.style.display = 'block';
            fetch(`/admin/examination/exam/${examId}/subjects-list`)
                .then(r => r.json())
                .then(data => {
                    data.subjects.forEach(sub => {
                        const option = document.createElement('option');
                        option.value = sub.id;
                        option.textContent = sub.subject.name;
                        option.dataset.subject = JSON.stringify(sub);
                        subjectSelect.appendChild(option);
                    });
                    subjectSelect.disabled = false;
                })
                .finally(() => loading.style.display = 'none');
        }
    });

    subjectSelect.addEventListener('change', function() {
        if (!this.value) {
            marksForm.style.display = 'none';
            return;
        }

        const examId = examSelect.value;
        const subjectId = this.value;
        const subjectData = JSON.parse(this.options[this.selectedIndex].dataset.subject);

        document.getElementById('selected-exam').textContent = examSelect.options[examSelect.selectedIndex].text;
        document.getElementById('selected-subject').textContent = subjectData.subject.name;
        document.getElementById('theory-max').textContent = subjectData.max_marks;
        document.getElementById('practical-max').textContent = subjectData.practical_marks;

        loading.style.display = 'block';
        fetch('/admin/examination/marks/students', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ exam_id: examId, exam_subject_id: subjectId })
        })
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById('students-tbody');
            tbody.innerHTML = '';

            data.students.forEach(student => {
                const existing = data.existingMarks[student.id];
                const row = `
                    <tr>
                        <td>${student.roll_no || 'N/A'}</td>
                        <td>${student.name}</td>
                        <td>
                            <input type="number" name="marks[${student.id}][theory]" 
                                class="form-control form-control-sm" 
                                min="0" max="${subjectData.max_marks}" step="0.01"
                                value="${existing?.theory_marks || ''}"
                                ${existing?.status === 'absent' ? 'disabled' : ''}>
                        </td>
                        <td>
                            <input type="number" name="marks[${student.id}][practical]" 
                                class="form-control form-control-sm" 
                                min="0" max="${subjectData.practical_marks}" step="0.01"
                                value="${existing?.practical_marks || ''}"
                                ${existing?.status === 'absent' ? 'disabled' : ''}>
                        </td>
                        <td>
                            <select name="marks[${student.id}][status]" class="form-select form-select-sm status-select" data-student="${student.id}">
                                <option value="present" ${existing?.status !== 'absent' ? 'selected' : ''}>Present</option>
                                <option value="absent" ${existing?.status === 'absent' ? 'selected' : ''}>Absent</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="marks[${student.id}][remarks]" 
                                class="form-control form-control-sm" 
                                value="${existing?.remarks || ''}">
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });

            // Handle status change
            document.querySelectorAll('.status-select').forEach(select => {
                select.addEventListener('change', function() {
                    const row = this.closest('tr');
                    const inputs = row.querySelectorAll('input[type="number"]');
                    inputs.forEach(input => {
                        input.disabled = this.value === 'absent';
                        if (this.value === 'absent') input.value = '';
                    });
                });
            });

            marksForm.style.display = 'block';
        })
        .finally(() => loading.style.display = 'none');
    });

    document.getElementById('marksEntryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const marks = {};
        
        for (let [key, value] of formData.entries()) {
            const match = key.match(/marks\[(\d+)\]\[(\w+)\]/);
            if (match) {
                const studentId = match[1];
                const field = match[2];
                if (!marks[studentId]) marks[studentId] = {};
                marks[studentId][field] = value;
            }
        }

        loading.style.display = 'block';
        fetch('/admin/examination/marks/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                exam_id: examSelect.value,
                exam_subject_id: subjectSelect.value,
                marks: marks
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('Marks saved successfully!');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .finally(() => loading.style.display = 'none');
    });
});
</script>
@endpush
@endsection
