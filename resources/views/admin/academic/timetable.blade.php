@extends('admin.layouts.horizontal')
@section('title', 'Time Table')
@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-clock me-2"></i>Time Table Management</h5>
    </div>
    <div class="content-card-body">
        <div class="d-flex justify-content-end mb-3">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTimetableModal">
                <i class="fas fa-plus me-2"></i>Add Time Table
            </button>
        </div>

        <!-- Filters for better visibility -->
        <div class="row mb-4">
            <div class="col-md-3">
                <select id="filterClass" class="form-select">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select id="filterDay" class="form-select">
                    <option value="">All Days</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filterTeacher" class="form-select">
                    <option value="">All Teachers</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-secondary w-100" onclick="applyFilters()">
                    <i class="fas fa-filter me-2"></i>Apply Filters
                </button>
            </div>
        </div>

        <!-- Time Table Display -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="timetableTable">
                <thead class="table-dark">
                    <tr>
                        <th>Class</th>
                        <th>Section</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Day</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Room No.</th>
                        <th>Period Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="timetableBody">
                    
                    <tr>
                        <td></td>
                        <td></td>
                        <td>name </td>
                        <td>name </td>
                        <td><td>
                        <td>me</td>
                        <td> </td>
                        <td></td>
                        <!-- <td>
                            <span class="badge bg>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning edit-timetable" data-id="">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-timetable" data-id="">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td> -->
                    </tr>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
<!-- Add/Edit Time Table Modal -->
<div class="modal fade" id="addTimetableModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-clock me-2"></i>Add Time Table Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="timetableForm" method="POST" action="{{ route('admin.timetable.store') }}">
                @csrf
                <input type="hidden" name="id" id="timetableId">
                <div class="modal-body">
                    <div class="row">
                        <!-- Class & Section -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                    <select name="class_id" id="classSelect" class="form-select @error('class_id') is-invalid @enderror" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                        @endforeach
                    </select>
                    @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                 </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Section <span class="text-danger">*</span></label>
                            <select name="section_id" id="sectionSelect" class="form-select" required>
                                <option value="">Select Section</option>
                               
                            </select>
                        </div>

                        <!-- Subject & Teacher -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" id="subjectId" class="form-select" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teacher <span class="text-danger">*</span></label>
                            <select name="teacher_id" id="teacherId" class="form-select" required>
                                <option value="">Select Teacher</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->first_name }} {{ $teacher->last_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Day & Period Type -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Day <span class="text-danger">*</span></label>
                            <select name="day" id="day" class="form-select" required>
                                <option value="">Select Day</option>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Period Type</label>
                            <select name="period_type" id="periodType" class="form-select">
                                <option value="Theory">Theory</option>
                                <option value="Lab">Lab</option>
                                <option value="Tutorial">Tutorial</option>
                                <option value="Activity">Activity</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Room No.</label>
                            <input type="text" name="room_number" id="roomNo" class="form-control" placeholder="e.g., Room 201">
                        </div>

                        <!-- Time Slots -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" id="startTime" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" id="endTime" class="form-control" required>
                        </div>

                        <!-- Academic Year & Term (Advanced) -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Academic Year</label>
                            <select name="academic_year" id="academicYear" class="form-select">
                                <option value="2024-25">2024-25</option>
                                <option value="2025-26" selected>2025-26</option>
                                <option value="2026-27">2026-27</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Term</label>
                            <select name="term" id="term" class="form-select">
                                <option value="Term 1">Term 1</option>
                                <option value="Term 2">Term 2</option>
                                <option value="Final">Final</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="isActive" class="form-check-input" value="1" checked>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                    </div>
                     <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Timetable</button>
                </div>
                </div>
               
            </form>
        </div>
    </div>


<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this timetable entry?
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Submit form via AJAX
    $('#timetableForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var url = $(this).attr('action');
        var method = $('#timetableId').val() ? 'PUT' : 'POST';
        
        if ($('#timetableId').val()) {
            url = "{{ route('admin.timetable.update', '') }}/" + $('#timetableId').val();
        }
        
        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                 console.log(response);
                if (response.success) {
                   
                    $('#addTimetableModal').modal('hide');
                    location.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message || 'Something went wrong!');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                } else {
                    toastr.error('Server error! Please try again.');
                }
            }
        });
    });
    
    // Edit Timetable
    $('.edit-timetable').on('click', function() {
        var id = $(this).data('id');
        
        $.get("{{ route('admin.timetable.edit', '') }}/" + id, function(data) {
            $('#timetableId').val(data.id);
            $('#classId').val(data.class_id);
            $('#sectionId').val(data.section_id);
            $('#subjectId').val(data.subject_id);
            $('#teacherId').val(data.teacher_id);
            $('#day').val(data.day);
            $('#startTime').val(data.start_time);
            $('#endTime').val(data.end_time);
            $('#periodType').val(data.period_type);
            $('#roomNo').val(data.room_no);
            $('#academicYear').val(data.academic_year);
            $('#term').val(data.term);
            $('#isActive').prop('checked', data.is_active == 1);
            
            $('#submitBtn').text('Update Timetable');
            $('#addTimetableModal .modal-title').html('<i class="fas fa-edit me-2"></i>Edit Timetable');
            $('#addTimetableModal').modal('show');
        });
    });
    
    // Delete Timetable
    $('.delete-timetable').on('click', function() {
        var id = $(this).data('id');
        var deleteUrl = "{{ route('admin.timetable.destroy', '') }}/" + id;
        $('#deleteForm').attr('action', deleteUrl);
        $('#deleteModal').modal('show');
    });
    
    // Reset form when modal closes
    $('#addTimetableModal').on('hidden.bs.modal', function() {
        $('#timetableForm')[0].reset();
        $('#timetableId').val('');
        $('#submitBtn').text('Save Timetable');
        $('#addTimetableModal .modal-title').html('<i class="fas fa-clock me-2"></i>Add Time Table Entry');
    });
    
    // Check for teacher conflicts
    $('#teacherId, #day, #startTime, #endTime').on('change', function() {
        var teacherId = $('#teacherId').val();
        var day = $('#day').val();
        var startTime = $('#startTime').val();
        var endTime = $('#endTime').val();
        var currentId = $('#timetableId').val();
        
        if (teacherId && day && startTime && endTime) {
            $.ajax({
                url: "{{ route('admin.timetable.check-conflict') }}",
                type: 'POST',
                data: {
                    teacher_id: teacherId,
                    day: day,
                    start_time: startTime,
                    end_time: endTime,
                    id: currentId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.conflict) {
                        toastr.warning('Teacher already has a class at this time!');
                        $('#submitBtn').prop('disabled', true);
                    } else {
                        $('#submitBtn').prop('disabled', false);
                    }
                }
            });
        }
    });
});

function applyFilters() {
    var classId = $('#filterClass').val();
    var day = $('#filterDay').val();
    var teacherId = $('#filterTeacher').val();
    
    $.ajax({
        url: "{{ route('admin.timetable.filter') }}",
        type: 'GET',
        data: { class_id: classId, day: day, teacher_id: teacherId },
        success: function(data) {
            $('#timetableBody').html(data.html);
        }
    });
}
</script>
<script>
    // Load sections when class is selected
    $('#classSelect').change(function() {
        const classId = $(this).val();
        if (classId) {
            $.get(`/admin/get-sections/${classId}`, function(response) {
                $('#sectionSelect').html('<option value="">Select Section</option>');
                response.data.forEach(section => {
                    $('#sectionSelect').append(`<option value="${section.id}">${section.section_name}</option>`);
                });
            });
        } else {
            $('#sectionSelect').html('<option value="">Select Section</option>');
        }
    });
    </script>
@endpush

