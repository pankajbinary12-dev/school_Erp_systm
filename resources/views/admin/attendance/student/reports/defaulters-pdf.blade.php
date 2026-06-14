<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Defaulters Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 5px 0; color: #dc3545; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-center { text-align: center; }
        .alert { padding: 10px; margin-bottom: 15px; background-color: #fff3cd; border: 1px solid #ffc107; }
        .text-danger { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>⚠ Attendance Defaulters Report</h2>
        <h3>{{ $class->class_name }}</h3>
        <p>Period: {{ $start_date }} to {{ $end_date }}</p>
        <p>Threshold: <span class="text-danger">{{ $threshold }}%</span> | Total Days: {{ $total_days }}</p>
    </div>

    @if(count($defaulters) > 0)
        <div class="alert">
            <strong>{{ count($defaulters) }}</strong> student(s) found with attendance below {{ $threshold }}%
        </div>

        <table>
            <thead>
                <tr>
                    <th>Roll No</th>
                    <th>Student Name</th>
                    <th>Admission No</th>
                    <th>Father Name</th>
                    <th>Contact</th>
                    <th class="text-center">Present</th>
                    <th class="text-center">Absent</th>
                    <th class="text-center">Total Days</th>
                    <th class="text-center">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($defaulters as $defaulter)
                <tr>
                    <td>{{ $defaulter['student']->roll_no ?? 'N/A' }}</td>
                    <td>{{ $defaulter['student']->first_name }} {{ $defaulter['student']->last_name }}</td>
                    <td>{{ $defaulter['student']->admission_no }}</td>
                    <td>{{ $defaulter['student']->father_name ?? 'N/A' }}</td>
                    <td>{{ $defaulter['student']->guardian_phone ?? 'N/A' }}</td>
                    <td class="text-center">{{ $defaulter['present'] }}</td>
                    <td class="text-center">{{ $defaulter['absent'] }}</td>
                    <td class="text-center">{{ $defaulter['total_days'] }}</td>
                    <td class="text-center text-danger">{{ $defaulter['percentage'] }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            <h4>Recommended Actions:</h4>
            <ul>
                <li>Send SMS/Email notification to parents</li>
                <li>Schedule parent-teacher meeting</li>
                <li>Issue warning letter if attendance continues to be low</li>
                <li>Provide counseling to students</li>
            </ul>
        </div>
    @else
        <div style="text-align: center; padding: 30px; background-color: #d4edda; border: 1px solid #28a745;">
            <h3 style="color: #28a745;">✓ Excellent!</h3>
            <p>No students found with attendance below {{ $threshold }}%</p>
        </div>
    @endif

    <p style="text-align: center; font-size: 10px; margin-top: 30px;">Generated on {{ date('d M Y H:i:s') }}</p>
</body>
</html>
