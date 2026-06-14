<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student-wise Attendance Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Student-wise Attendance Report</h2>
        <h3>{{ $class->class_name }}</h3>
        <p>Period: {{ $start_date }} to {{ $end_date }}</p>
        <p>Total Days: {{ $total_days }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Roll No</th>
                <th>Student Name</th>
                <th>Admission No</th>
                <th class="text-center">Present</th>
                <th class="text-center">Absent</th>
                <th class="text-center">Leave</th>
                <th class="text-center">Total Days</th>
                <th class="text-center">Percentage</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($studentStats as $stat)
            <tr>
                <td>{{ $stat['student']->roll_no ?? 'N/A' }}</td>
                <td>{{ $stat['student']->first_name }} {{ $stat['student']->last_name }}</td>
                <td>{{ $stat['student']->admission_no }}</td>
                <td class="text-center">{{ $stat['present'] }}</td>
                <td class="text-center">{{ $stat['absent'] }}</td>
                <td class="text-center">{{ $stat['leave'] }}</td>
                <td class="text-center">{{ $stat['total_days'] }}</td>
                <td class="text-center"><strong>{{ $stat['percentage'] }}%</strong></td>
                <td class="text-center">
                    @if($stat['percentage'] >= 75)
                        Good
                    @elseif($stat['percentage'] >= 60)
                        Average
                    @else
                        Poor
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 20px;"><strong>Note:</strong> Percentage is calculated as (Present Days / Total Days) × 100</p>
    <p style="text-align: center; font-size: 10px; margin-top: 20px;">Generated on {{ date('d M Y H:i:s') }}</p>
</body>
</html>
