@extends('student.layout')

@section('title', 'Timetable')
@section('page-title', 'Class Timetable')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="stat-card">
            <h5 style="color: #5a5c69; font-weight: 700; margin-bottom: 20px;">
                <i class="fas fa-clock me-2"></i>Weekly Timetable - {{ $student->class->class_name ?? 'N/A' }} {{ $student->section->section_name ?? 'N/A' }}
            </h5>
            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <tr>
                            <th style="width: 120px;">Day</th>
                            <th>Schedule</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($days as $day)
                        <tr>
                            <td style="background: #f8f9fc; font-weight: 600;">{{ $day }}</td>
                            <td>
                                @if(isset($timetable[$day]) && $timetable[$day]->count() > 0)
                                    <div class="row">
                                        @foreach($timetable[$day] as $period)
                                        <div class="col-md-4 mb-3">
                                            <div style="background: #f8f9fc; padding: 15px; border-radius: 8px; border-left: 4px solid #667eea;">
                                                <h6 style="color: #5a5c69; font-weight: 600; margin-bottom: 8px;">
                                                    {{ $period->subject->subject_name ?? 'N/A' }}
                                                </h6>
                                                <small style="color: #858796; display: block;">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ \Carbon\Carbon::parse($period->start_time)->format('h:i A') }} - 
                                                    {{ \Carbon\Carbon::parse($period->end_time)->format('h:i A') }}
                                                </small>
                                                <small style="color: #858796; display: block;">
                                                    <i class="fas fa-user me-1"></i>
                                                    {{ $period->teacher->first_name ?? 'N/A' }} {{ $period->teacher->last_name ?? '' }}
                                                </small>
                                                @if($period->room_number)
                                                <small style="color: #858796; display: block;">
                                                    <i class="fas fa-door-open me-1"></i>
                                                    Room: {{ $period->room_number }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p style="color: #858796; margin: 10px 0;">No classes scheduled</p>
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
@endsection
