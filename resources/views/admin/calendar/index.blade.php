@extends('admin.layouts.app')

@section('title', 'School Calendar')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
    #calendar {
        max-width: 1200px;
        margin: 0 auto;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .fc-event {
        cursor: pointer;
    }

    .legend {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 4px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-calendar-alt me-2"></i>School Calendar</h4>
        <div class="btn-group">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eventModal">
                <i class="fas fa-plus"></i> Add Event
            </button>
            <button class="btn btn-secondary" onclick="exportCalendar()">
                <i class="fas fa-download"></i> Export
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color" style="background: #dc3545;"></div>
                    <span>Exams</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #28a745;"></div>
                    <span>Holidays</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #ffc107;"></div>
                    <span>Staff Leaves</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #17a2b8;"></div>
                    <span>Events</span>
                </div>
            </div>

            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Event Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="eventForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Event Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Event Type</label>
                        <select class="form-select" name="type" required>
                            <option value="exam">Exam</option>
                            <option value="holiday">Holiday</option>
                            <option value="event">School Event</option>
                            <option value="meeting">Meeting</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date (Optional)</label>
                        <input type="date" class="form-control" name="end_date">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventDetailsTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventDetailsBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
let calendar;

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        events: '/admin/calendar/events',
        eventClick: function(info) {
            showEventDetails(info.event);
        },
        dateClick: function(info) {
            $('#eventModal').modal('show');
            $('input[name="start_date"]').val(info.dateStr);
        },
        height: 'auto',
        eventDisplay: 'block',
        displayEventTime: false
    });
    
    calendar.render();

    $('#eventForm').submit(function(e) {
        e.preventDefault();
        
        Swal.fire({
            icon: 'info',
            title: 'Coming Soon',
            text: 'Event creation feature will be available soon!'
        });
        
        $('#eventModal').modal('hide');
    });
});

function showEventDetails(event) {
    $('#eventDetailsTitle').text(event.title);
    $('#eventDetailsBody').html(`
        <p><strong>Type:</strong> ${event.extendedProps.type || 'Event'}</p>
        <p><strong>Date:</strong> ${event.start.toLocaleDateString()}</p>
        ${event.end ? `<p><strong>End Date:</strong> ${event.end.toLocaleDateString()}</p>` : ''}
    `);
    $('#eventDetailsModal').modal('show');
}

function exportCalendar() {
    Swal.fire({
        icon: 'info',
        title: 'Coming Soon',
        text: 'Calendar export feature will be available soon!'
    });
}
</script>
@endpush
