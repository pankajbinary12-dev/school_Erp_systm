<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Attendance Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 5px 0; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 4px; text-align: center; }
        th { background-color: #f0f0f0; font-size: 9px; }
        .student-name { text-align: left; }
        .bg-success { background-color: #d4edda; }
        .bg-danger { background-color: #f8d7da; }
        .bg-warning { background-color: #fff3cd; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Monthly Attendance Report</h2>
        <h3>{{ $class->class_name }}</h3>
        <p>Period: {{ $start_date }} to {{ $end_date }} ({{ $total_days }} days)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">Roll</th>
                <th rowspan="2">Student Name</th>
                @foreach($dates as $date)
                    <th>{{ \Carbon\Carbon::parse($date)->format('d') }}</th>
                @endforeach
                <th rowspan="2">P</th>
                <th rowspan="2">A</th>
                <th rowspan="2">%</th>
            </tr>
            <tr>
                @foreach($dates as $date)
                    <th style="font-size: 8px;">{{ \Carbon\Carbon::parse($date)->format('D') }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr>
                <td>{{ $student->roll_no ?? 'N/A' }}</td>
                <td class="student-name">{{ $student->first_name }} {{ $student->last_name }}</td>
                @php
                    $studentAttendance = $attendance[$student->id] ?? collect();
                    $present = 0;
                    $absent = 0;
                @endphp
                @foreach($dates as $date)
                    @php
                        $record = $studentAttendance->firstWhere('attendance_date', $date);
                        $status = $record ? $record->status : '-';
                        if ($status === 'Present') $present++;
                        if ($status === 'Absent') $absent++;
                        $cellClass = $status === 'Present' ? 'bg-success' : 
                                    ($status === 'Absent' ? 'bg-danger' : 
                                    ($status === 'Leave' ? 'bg-warning' : ''));
                    @endphp
                    <td class="{{ $cellClass }}">
                        {{ $status === 'Present' ? 'P' : ($status === 'Absent' ? 'A' : ($status === 'Leave' ? 'L' : '-')) }}
                    </td>
                @endforeach
                <td><strong>{{ $present }}</strong></td>
                <td><strong>{{ $absent }}</strong></td>
                <td><strong>{{ $total_days > 0 ? round(($present / $total_days) * 100, 1) : 0 }}%</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 10px;"><strong>Legend:</strong> P = Present, A = Absent, L = Leave, - = Not Marked</p>
    <p style="text-align: center; font-size: 8px; margin-top: 20px;">Generated on {{ date('d M Y H:i:s') }}</p>
</body>
</html>
