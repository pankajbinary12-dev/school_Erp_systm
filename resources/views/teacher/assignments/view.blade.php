<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>View Assignment - Teacher Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
</head>
<body>
    <div class="d-flex">
        @include('teacher.partials.sidebar')
        
        <div class="main-content flex-grow-1" style="margin-left: 250px; min-height: 100vh; background: #f8f9fa;">
            @include('teacher.partials.navbar')
            
            <div class="container-fluid p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-file-alt"></i> Assignment Details</h2>
                    <a href="{{ route('teacher.assignments') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Assignments
                    </a>
                </div>

                <!-- Assignment Info -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ $assignment->title }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Class:</strong> {{ $assignment->class->class_name }}</p>
                                <p><strong>Section:</strong> {{ $assignment->section ? $assignment->section->section_name : 'All Sections' }}</p>
                                <p><strong>Subject:</strong> {{ $assignment->subject->subject_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Assigned Date:</strong> {{ \Carbon\Carbon::parse($assignment->assigned_date)->format('d M, Y') }}</p>
                                <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($assignment->due_date)->format('d M, Y') }}</p>
                                <p><strong>Total Marks:</strong> {{ $assignment->total_marks }}</p>
                            </div>
                        </div>
                        <hr>
                        <h6>Description:</h6>
                        <p>{{ $assignment->description }}</p>
                        
                        @if($assignment->instructions)
                        <h6>Instructions:</h6>
                        <p>{{ $assignment->instructions }}</p>
                        @endif

                        @if($assignment->attachment)
                        <p><strong>Attachment:</strong> 
                            <a href="{{ Storage::url($assignment->attachment) }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </p>
                        @endif
                    </div>
                </div>

                <!-- Submissions -->
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-users"></i> Student Submissions</h5>
                    </div>
                    <div class="card-body">
                        <table id="submissionsTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Roll No</th>
                                    <th>Student Name</th>
                                    <th>Status</th>
                                    <th>Submitted On</th>
                                    <th>Marks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignment->submissions as $submission)
                                <tr>
                                    <td>{{ $submission->student->roll_number }}</td>
                                    <td>{{ $submission->student->full_name }}</td>
                                    <td>
                                        @if($submission->status == 'Pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($submission->status == 'Submitted')
                                            <span class="badge bg-info">Submitted</span>
                                        @elseif($submission->status == 'Graded')
                                            <span class="badge bg-success">Graded</span>
                                        @endif
                                    </td>
                                    <td>{{ $submission->submitted_at ? \Carbon\Carbon::parse($submission->submitted_at)->format('d M, Y h:i A') : '-' }}</td>
                                    <td>
                                        @if($submission->marks_obtained !== null)
                                            {{ $submission->marks_obtained }} / {{ $assignment->total_marks }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($submission->status == 'Submitted')
                                        <button class="btn btn-sm btn-primary grade-btn" data-id="{{ $submission->id }}">
                                            <i class="fas fa-check"></i> Grade
                                        </button>
                                        @elseif($submission->status == 'Graded')
                                        <button class="btn btn-sm btn-info view-feedback-btn" data-feedback="{{ $submission->teacher_feedback }}">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade Modal -->
    <div class="modal fade" id="gradeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Grade Submission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="gradeForm">
                    <div class="modal-body">
                        <input type="hidden" id="submission_id">
                        <div class="mb-3">
                            <label class="form-label">Marks Obtained <span class="text-danger">*</span></label>
                            <input type="number" name="marks_obtained" class="form-control" min="0" max="{{ $assignment->total_marks }}" required>
                            <small class="text-muted">Out of {{ $assignment->total_marks }}</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Feedback</label>
                            <textarea name="teacher_feedback" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Grade</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize DataTable
        $('#submissionsTable').DataTable({
            order: [[0, 'asc']]
        });

        // Grade button
        $('.grade-btn').click(function() {
            const submissionId = $(this).data('id');
            $('#submission_id').val(submissionId);
            $('#gradeModal').modal('show');
        });

        // View feedback
        $('.view-feedback-btn').click(function() {
            const feedback = $(this).data('feedback');
            Swal.fire({
                title: 'Teacher Feedback',
                text: feedback || 'No feedback provided',
                icon: 'info'
            });
        });

        // Submit grade
        $('#gradeForm').submit(function(e) {
            e.preventDefault();
            
            const submissionId = $('#submission_id').val();
            const formData = $(this).serialize();
            
            $.ajax({
                url: `/teacher/assignments/submissions/${submissionId}/grade`,
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#gradeModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Error grading submission'
                    });
                }
            });
        });
    </script>
</body>
</html>
