<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Attendance Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-center { text-align: center; }
        .stats { margin-bottom: 20px; }
        .stats table { width: 100%; }
        .stats td { text-align: center; padding: 10px; font-weight: bold; }
        .badge-success { background-color: #28a745; color: white; padding: 3px 8px; border-radius: 3px; }
        .badge-danger { background-color: #dc3545; color: white; padding: 3px 8px; border-radius: 3px; }
        .badge-warning { background-color: #ffc107; color: black; padding: 3px 8px; border-radius: 3px; }
        .badge-secondary { background-color: #6c757d; color: white; padding: 3px 8px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Daily Attendance Report</h2>
        <h3>{{ $class->class_name }}</h3>
        <p>Date: {{ $date }}</p>
    </div>

    <div class="stats">
        <table>
            <tr>
                <td>Total Students: {{ $stats['total'] }}</td>
                <td>Present: {{ $stats['present'] }}</td>
                <td>Absent: {{ $stats['absent'] }}</td>
                <td>Leave: {{ $stats['leave'] }}</td>
                <td>Percentage: {{ $stats['total'] > 0 ? round(($stats['present'] / $stats['total']) * 100, 2) : 0 }}%</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Roll No</th>
                <th>Student Name</th>
                <th>Admission No</th>
                <th>Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr>
                <td>{{ $student->roll_no ?? 'N/A' }}</td>
                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                <td>{{ $student->admission_no }}</td>
                <td class="text-center">
                    @if(isset($attendance[$student->id]))
                        {{ $attendance[$student->id]->status }}
                    @else
                        Not Marked
                    @endif
                </td>
                <td>{{ $attendance[$student->id]->remarks ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 30px; text-align: center; font-size: 10px;">
        Generated on {{ date('d M Y H:i:s') }}
    </p>
</body>
</html>
