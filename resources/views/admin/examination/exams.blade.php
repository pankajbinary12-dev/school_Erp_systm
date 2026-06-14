@extends('admin.layouts.horizontal')
@section('title', 'Manage Exams')
@section('content')
<div class="content-card">
    <div class="content-card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-clipboard-list me-2"></i>Manage Exams</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExamModal">
            <i class="fas fa-plus"></i> Add New Exam
        </button>
    </div>
    <div class="content-card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Exam Code</th>
                        <th>Exam Name</th>
                        <th>Class</th>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                    <tr>
                        <td>{{ $exam->exam_code }}</td>
                        <td>{{ $exam->name }}</td>
                        <td>{{ $exam->class->class_name }}</td>
                        <td><span class="badge bg-info">{{ strtoupper(str_replace('_', ' ', $exam->exam_type)) }}</span></td>
                        <td>{{ $exam->start_date->format('d M Y') }}</td>
                        <td>{{ $exam->end_date->format('d M Y') }}</td>
                        <td>
                            @if($exam->status == 'scheduled')
                                <span class="badge bg-warning">Scheduled</span>
                            @elseif($exam->status == 'ongoing')
                                <span class="badge bg-primary">Ongoing</span>
                            @elseif($exam->status == 'completed')
                                <span class="badge bg-success">Completed</span>
                            @else
                                <span class="badge bg-secondary">{{ $exam->status }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.examination.exam.subjects', $exam->id) }}" class="btn btn-sm btn-info" title="Manage Subjects">
                                <i class="fas fa-book"></i>
                            </a>
                            <button class="btn btn-sm btn-warning edit-exam" data-exam="{{ json_encode($exam) }}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No exams found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $exams->links() }}
    </div>
</div>

<!-- Add Exam Modal -->
<div class="modal fade" id="addExamModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.examination.exam.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Exam</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Exam Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Class *</label>
                            <select name="class_id" class="form-select" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Exam Type *</label>
                            <select name="exam_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="unit_test">Unit Test</option>
                                <option value="midterm">Midterm</option>
                                <option value="final">Final</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="half_yearly">Half Yearly</option>
                                <option value="annual">Annual</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Session</label>
                            <select name="session_id" class="form-select">
                                <option value="">Select Session</option>
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}">{{ $session->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date *</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date *</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Passing Percentage *</label>
                            <input type="number" name="passing_percentage" class="form-control" min="0" max="100" step="0.01" value="33" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Exam</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Exam Modal -->
<div class="modal fade" id="editExamModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editExamForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Exam</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Exam Name *</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Class *</label>
                            <select name="class_id" id="edit_class_id" class="form-select" required>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Exam Type *</label>
                            <select name="exam_type" id="edit_exam_type" class="form-select" required>
                                <option value="unit_test">Unit Test</option>
                                <option value="midterm">Midterm</option>
                                <option value="final">Final</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="half_yearly">Half Yearly</option>
                                <option value="annual">Annual</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status *</label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="scheduled">Scheduled</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date *</label>
                            <input type="date" name="start_date" id="edit_start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date *</label>
                            <input type="date" name="end_date" id="edit_end_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Passing Percentage *</label>
                            <input type="number" name="passing_percentage" id="edit_passing_percentage" class="form-control" min="0" max="100" step="0.01" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Exam</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit exam
    document.querySelectorAll('.edit-exam').forEach(btn => {
        btn.addEventListener('click', function() {
            const exam = JSON.parse(this.dataset.exam);
            document.getElementById('editExamForm').action = `/admin/examination/exam/${exam.id}/update`;
            document.getElementById('edit_name').value = exam.name;
            document.getElementById('edit_class_id').value = exam.class_id;
            document.getElementById('edit_exam_type').value = exam.exam_type;
            document.getElementById('edit_status').value = exam.status;
            document.getElementById('edit_start_date').value = exam.start_date;
            document.getElementById('edit_end_date').value = exam.end_date;
            document.getElementById('edit_passing_percentage').value = exam.passing_percentage;
            document.getElementById('edit_description').value = exam.description || '';
            
            new bootstrap.Modal(document.getElementById('editExamModal')).show();
        });
    });
});
</script>
@endpush
@endsection
