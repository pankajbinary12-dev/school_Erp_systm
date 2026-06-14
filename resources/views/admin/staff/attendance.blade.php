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
        
        // First get all staff
        $.ajax({
            url: '{{ route("admin.staff.data") }}',
            type: 'GET',
            success: function(staffResponse) {
                // Then get attendance for selected date
                $.ajax({
                    url: '{{ route("admin.staff.attendance.data") }}',
                    type: 'GET',
                    data: { date: date },
                    success: function(attendanceResponse) {
                        let html = '';
                        const attendanceMap = {};
                        
                        // Create map of attendance by staff_id
                        attendanceResponse.data.forEach(function(att) {
                            attendanceMap[att.staff_id] = att;
                        });
                        
                        staffResponse.data.forEach(function(staff) {
                            const attendance = attendanceMap[staff.id];
                            const checkIn = attendance ? attendance.check_in : '';
                            const checkOut = attendance ? attendance.check_out : '';
                            const status = attendance ? attendance.status : 'Present';
                            const remarks = attendance ? attendance.remarks : '';
                            
                            html += `
                                <tr>
                                    <td>${staff.employee_id}</td>
                                    <td>${staff.first_name} ${staff.last_name}</td>
                                    <td>${staff.designation}</td>
                                    <td><input type="time" class="form-control form-control-sm check-in" data-id="${staff.id}" value="${checkIn}"></td>
                                    <td><input type="time" class="form-control form-control-sm check-out" data-id="${staff.id}" value="${checkOut}"></td>
                                    <td>
                                        <select class="form-select form-select-sm status-select" data-id="${staff.id}">
                                            <option value="Present" ${status === 'Present' ? 'selected' : ''}>Present</option>
                                            <option value="Absent" ${status === 'Absent' ? 'selected' : ''}>Absent</option>
                                            <option value="Half Day" ${status === 'Half Day' ? 'selected' : ''}>Half Day</option>
                                            <option value="Late" ${status === 'Late' ? 'selected' : ''}>Late</option>
                                            <option value="On Leave" ${status === 'On Leave' ? 'selected' : ''}>On Leave</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control form-control-sm remarks" data-id="${staff.id}" placeholder="Remarks" value="${remarks}"></td>
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
        });
    }
    
    $(document).on('click', '.btn-mark', function() {
        const staffId = $(this).data('id');
        const date = $('#attendanceDate').val();
        const checkIn = $(`.check-in[data-id="${staffId}"]`).val();
        const checkOut = $(`.check-out[data-id="${staffId}"]`).val();
        const status = $(`.status-select[data-id="${staffId}"]`).val();
        const remarks = $(`.remarks[data-id="${staffId}"]`).val();
        
        const btn = $(this);
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '{{ route("admin.staff.attendance.mark") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                staff_id: staffId,
                attendance_date: date,
                check_in: checkIn || null,
                check_out: checkOut || null,
                status: status,
                remarks: remarks || null
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    btn.prop('disabled', false).html(originalHtml);
                } else {
                    Swal.fire('Error!', response.message || 'Failed to mark attendance', 'error');
                    btn.prop('disabled', false).html(originalHtml);
                }
            },
            error: function(xhr) {
                let message = 'Failed to mark attendance';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: message
                });
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
});
</script>
@endpush
