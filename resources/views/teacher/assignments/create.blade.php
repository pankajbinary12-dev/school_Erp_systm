<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Assignment - Teacher Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="d-flex">
        @include('teacher.partials.sidebar')
        
        <div class="main-content flex-grow-1" style="margin-left: 250px; min-height: 100vh; background: #f8f9fa;">
            @include('teacher.partials.navbar')
            
            <div class="container-fluid p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-plus-circle"></i> Create New Assignment</h2>
                    <a href="{{ route('teacher.assignments') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Assignments
                    </a>
                </div>

                <div class="card shadow">
                    <div class="card-body">
                        <form id="assignmentForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Class <span class="text-danger">*</span></label>
                                    <select name="class_id" id="class_id" class="form-select" required>
                                        <option value="">Select Class</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Section</label>
                                    <select name="section_id" id="section_id" class="form-select">
                                        <option value="">All Sections</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Subject <span class="text-danger">*</span></label>
                                    <select name="subject_id" class="form-select" required>
                                        <option value="">Select Subject</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Total Marks <span class="text-danger">*</span></label>
                                    <input type="number" name="total_marks" class="form-control" min="1" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control" rows="4" required></textarea>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Instructions</label>
                                    <textarea name="instructions" class="form-control" rows="3"></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Assigned Date <span class="text-danger">*</span></label>
                                    <input type="date" name="assigned_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Due Date <span class="text-danger">*</span></label>
                                    <input type="date" name="due_date" class="form-control" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Attachment (Optional)</label>
                                    <input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png">
                                    <small class="text-muted">Max size: 10MB. Allowed: PDF, DOC, DOCX, PPT, PPTX, JPG, PNG</small>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Create Assignment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Load sections when class is selected
        $('#class_id').change(function() {
            const classId = $(this).val();
            $('#section_id').html('<option value="">Loading...</option>');
            
            if (classId) {
                $.get(`/teacher/get-sections/${classId}`, function(response) {
                    let options = '<option value="">All Sections</option>';
                    response.data.forEach(section => {
                        options += `<option value="${section.id}">${section.section_name}</option>`;
                    });
                    $('#section_id').html(options);
                });
            } else {
                $('#section_id').html('<option value="">All Sections</option>');
            }
        });

        // Submit form
        $('#assignmentForm').submit(function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            Swal.fire({
                title: 'Creating Assignment...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route("teacher.assignments.store") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000
                    }).then(() => {
                        window.location.href = '{{ route("teacher.assignments") }}';
                    });
                },
                error: function(xhr) {
                    let message = 'Error creating assignment';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message
                    });
                }
            });
        });
    </script>
</body>
</html>
