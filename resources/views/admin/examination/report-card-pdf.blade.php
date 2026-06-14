<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card - {{ $student->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .info-table { border: none; }
        .info-table td, .info-table th { border: none; padding: 5px; }
        .result-pass { color: green; font-weight: bold; }
        .result-fail { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>REPORT CARD</h2>
        <h3>{{ $exam->name }}</h3>
    </div>

    <table class="info-table">
        <tr>
            <td width="25%"><strong>Student Name:</strong></td>
            <td width="25%">{{ $student->name }}</td>
            <td width="25%"><strong>Class:</strong></td>
            <td width="25%">{{ $student->class->class_name }}</td>
        </tr>
        <tr>
            <td><strong>Admission No:</strong></td>
            <td>{{ $student->admission_no }}</td>
            <td><strong>Roll No:</strong></td>
            <td>{{ $student->roll_no ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Exam Type:</strong></td>
            <td>{{ strtoupper(str_replace('_', ' ', $exam->exam_type)) }}</td>
            <td><strong>Exam Date:</strong></td>
            <td>{{ $exam->start_date->format('d M Y') }} to {{ $exam->end_date->format('d M Y') }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th class="text-center">Theory</th>
                <th class="text-center">Practical</th>
                <th class="text-center">Total</th>
                <th class="text-center">Max Marks</th>
                <th class="text-center">Percentage</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($marks as $mark)
            <tr>
                <td>{{ $mark->examSubject->subject->name }}</td>
                <td class="text-center">{{ $mark->theory_marks ?? '-' }}</td>
                <td class="text-center">{{ $mark->practical_marks ?? '-' }}</td>
                <td class="text-center"><strong>{{ $mark->total_marks }}</strong></td>
                <td class="text-center">{{ $mark->examSubject->total_max_marks }}</td>
                <td class="text-center">{{ number_format(($mark->total_marks / $mark->examSubject->total_max_marks) * 100, 2) }}%</td>
                <td class="text-center">
                    @if($mark->is_passed)
                        Pass
                    @else
                        Fail
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total</th>
                <th class="text-center">{{ number_format($result->total_marks_obtained, 2) }}</th>
                <th class="text-center">{{ number_format($result->total_max_marks, 2) }}</th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>

    <table style="width: 50%;">
        <tr>
            <th width="50%">Percentage:</th>
            <td><strong>{{ number_format($result->percentage, 2) }}%</strong></td>
        </tr>
        <tr>
            <th>Grade:</th>
            <td><strong>{{ $result->grade }}</strong></td>
        </tr>
        <tr>
            <th>Result:</th>
            <td>
                @if($result->result == 'pass')
                    <span class="result-pass">PASS</span>
                @else
                    <span class="result-fail">FAIL</span>
                @endif
            </td>
        </tr>
        @if($result->rank)
        <tr>
            <th>Rank:</th>
            <td><strong>{{ $result->rank }} / {{ $result->total_students }}</strong></td>
        </tr>
        @endif
    </table>

    <div style="margin-top: 50px;">
        <table class="info-table">
            <tr>
                <td width="33%" class="text-center">
                    <br><br>
                    _________________<br>
                    Class Teacher
                </td>
                <td width="33%" class="text-center">
                    <br><br>
                    _________________<br>
                    Principal
                </td>
                <td width="33%" class="text-center">
                    <br><br>
                    _________________<br>
                    Parent Signature
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
