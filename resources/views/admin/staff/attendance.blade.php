@extends('admin.layouts.horizontal')

@section('title', 'Staff Attendance')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-calendar-check me-2"></i>Staff Attendance</h5>
        <div>
            <input type="date" id="attendanceDate" class="form-control form-control-sm d-inline-block" style="width: auto;" value="{{ date('Y-m-d') }}">
            <button class="btn btn-primary btn-sm" id="markAllPresent">
                <i class="fas fa-check-double me-1"></i>Mark All Present
            </button>
        </div>
    </div>
    
    <div class="table-responsive p-3">
        <table id="attendanceTable" class="table table-hover table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="attendanceBody">
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    loadAttendance();
    
    $('#attendanceDate').change(function() {
        loadAttendance();
    });
    
    $('#markAllPresent').click(function() {
        $('.status-select').val('Present');
    });
    
    function loadAttendance() {
        const date = $('#attendanceDate').val();
        
        $.ajax({
            url: '{{ route("admin.staff.data") }}',
            type: 'GET',
            success: function(response) {
                let html = '';
                response.data.forEach(function(staff) {
                    html += `
                        <tr>
                            <td>${staff.employee_id}</td>
                            <td>${staff.first_name} ${staff.last_name}</td>
                            <td>${staff.designation}</td>
                            <td><input type="time" class="form-control form-control-sm check-in" data-id="${staff.id}"></td>
                            <td><input type="time" class="form-control form-control-sm check-out" data-id="${staff.id}"></td>
                            <td>
                                <select class="form-select form-select-sm status-select" data-id="${staff.id}">
                                    <option value="Present">Present</option>
                                    <option value="Absent">Absent</option>
                                    <option value="Half Day">Half Day</option>
                                    <option value="Late">Late</option>
                                    <option value="On Leave">On Leave</option>
                                </select>
                            </td>
                            <td><input type="text" class="form-control form-control-sm remarks" data-id="${staff.id}" placeholder="Remarks"></td>
                            <td>
                                <button class="btn btn-sm btn-success btn-mark" data-id="${staff.id}">
                                    <i class="fas fa-check"></i> Mark
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#attendanceBody').html(html);
            }
        });
    }
    
    $(document).on('click', '.btn-mark', function() {
        const staffId = $(this).data('id');
        const date = $('#attendanceDate').val();
        const checkIn = $(`.check-in[data-id="${staffId}"]`).val();
        const checkOut = $(`.check-out[data-id="${staffId}"]`).val();
        const status = $(`.status-select[data-id="${staffId}"]`).val();
        const remarks = $(`.remarks[data-id="${staffId}"]`).val();
        
        $.ajax({
            url: '{{ route("admin.staff.attendance.mark") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                staff_id: staffId,
                attendance_date: date,
                check_in: checkIn,
                check_out: checkOut,
                status: status,
                remarks: remarks
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function() {
                Swal.fire('Error!', 'Failed to mark attendance', 'error');
            }
        });
    });
});
</script>
@endpush
