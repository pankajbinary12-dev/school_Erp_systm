<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $certificate->getTypeLabel() }}</title>
    <style>
        @page {
            margin: 0;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 40px;
            background: #fff;
        }
        
        .certificate-container {
            border: 15px solid #1e3a8a;
            border-radius: 10px;
            padding: 30px;
            min-height: 900px;
            position: relative;
            background: linear-gradient(to bottom, #ffffff 0%, #f8f9fa 100%);
        }
        
        .certificate-border {
            border: 3px solid #3b82f6;
            padding: 30px;
            min-height: 840px;
            position: relative;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #1e3a8a;
            padding-bottom: 20px;
        }
        
        .school-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
        }
        
        .school-name {
            font-size: 32px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 10px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .school-address {
            font-size: 14px;
            color: #666;
            margin: 5px 0;
        }
        
        .certificate-title {
            text-align: center;
            margin: 40px 0 30px;
        }
        
        .certificate-type {
            font-size: 28px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 3px;
            border-bottom: 2px solid #3b82f6;
            display: inline-block;
            padding-bottom: 10px;
        }
        
        .certificate-number {
            text-align: right;
            font-size: 12px;
            color: #666;
            margin-bottom: 20px;
        }
        
        .certificate-content {
            text-align: justify;
            font-size: 16px;
            line-height: 2;
            margin: 40px 0;
            padding: 0 30px;
            color: #333;
        }
        
        .student-name {
            font-weight: bold;
            font-size: 18px;
            color: #1e3a8a;
            text-decoration: underline;
        }
        
        .signature-section {
            margin-top: 80px;
            display: table;
            width: 100%;
        }
        
        .signature-left {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 20px;
        }
        
        .signature-right {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 20px;
        }
        
        .signature-line {
            border-top: 2px solid #333;
            width: 200px;
            margin: 60px auto 10px;
        }
        
        .signature-label {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        
        .signature-title {
            font-size: 12px;
            color: #666;
        }
        
        .qr-code {
            position: absolute;
            bottom: 30px;
            left: 30px;
            text-align: center;
        }
        
        .qr-code img {
            width: 100px;
            height: 100px;
        }
        
        .qr-label {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        
        .issue-date {
            position: absolute;
            bottom: 30px;
            right: 30px;
            text-align: right;
        }
        
        .date-label {
            font-size: 12px;
            color: #666;
        }
        
        .date-value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(30, 58, 138, 0.05);
            font-weight: bold;
            z-index: -1;
        }
        
        .stamp-area {
            position: absolute;
            bottom: 120px;
            left: 50px;
            width: 150px;
            height: 150px;
            border: 2px dashed #ccc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #999;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-border">
            <!-- Watermark -->
            <div class="watermark">CERTIFIED</div>
            
            <!-- Header -->
            <div class="header">
                <div class="school-logo">
                    <!-- Add your school logo here -->
                    <svg width="80" height="80" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="45" fill="#1e3a8a"/>
                        <text x="50" y="60" font-size="40" fill="white" text-anchor="middle" font-weight="bold">S</text>
                    </svg>
                </div>
                <div class="school-name">A&L Group College</div>
                <div class="school-address">Management System</div>
                <div class="school-address">Email: info@algroup.edu | Phone: +91-XXXXXXXXXX</div>
            </div>
            
            <!-- Certificate Number -->
            <div class="certificate-number">
                Certificate No: <strong>{{ $certificate->certificate_no }}</strong>
            </div>
            
            <!-- Certificate Title -->
            <div class="certificate-title">
                <div class="certificate-type">{{ $certificate->getTypeLabel() }}</div>
            </div>
            
            <!-- Certificate Content -->
            <div class="certificate-content">
                <p>
                    This is to certify that 
                    <span class="student-name">{{ $certificate->student->first_name }} {{ $certificate->student->last_name }}</span>, 
                    son/daughter of 
                    <span class="student-name">{{ $certificate->student->father_name }}</span>, 
                    @if($certificate->type == 'transfer' || $certificate->type == 'character')
                        was a student
                    @else
                        is a bonafide student
                    @endif
                    of this institution 
                    @if($certificate->student->class)
                        studying in Class <span class="student-name">{{ $certificate->student->class->class_name }}</span>
                    @endif
                    @if($certificate->student->roll_number)
                        with Roll Number <span class="student-name">{{ $certificate->student->roll_number }}</span>
                    @endif
                    during the academic year {{ date('Y') }}-{{ date('Y') + 1 }}.
                </p>
                
                @if($certificate->type == 'bonafide')
                    <p>This certificate is issued for official purposes as requested by the student/parent.</p>
                @elseif($certificate->type == 'transfer')
                    <p>The student has been granted a Transfer Certificate on request. Date of Birth: <strong>{{ $certificate->student->date_of_birth }}</strong>. The student's conduct and character were satisfactory during their stay in this institution.</p>
                @elseif($certificate->type == 'character')
                    <p>During their stay in this institution, their conduct and character were found to be good. They are hardworking, sincere, and well-behaved.</p>
                @elseif($certificate->type == 'fee')
                    <p>The student has paid all the fees due to the institution up to date. No dues are pending against the student as of {{ $certificate->issue_date->format('d M Y') }}.</p>
                @elseif($certificate->type == 'migration')
                    <p>This certificate is issued to enable the student to seek admission in another institution. Date of Birth: <strong>{{ $certificate->student->date_of_birth }}</strong>.</p>
                @endif
                
                <p>We wish them all the best for their future endeavors.</p>
            </div>
            
            <!-- Signature Section -->
            <div class="signature-section">
                <div class="signature-left">
                    <div class="signature-line"></div>
                    <div class="signature-label">Class Teacher</div>
                    <div class="signature-title">{{ $certificate->student->class->class_name ?? 'N/A' }}</div>
                </div>
                <div class="signature-right">
                    <div class="signature-line"></div>
                    <div class="signature-label">Principal</div>
                    <div class="signature-title">A&L Group College</div>
                </div>
            </div>
            
            <!-- School Stamp Area -->
            <div class="stamp-area">
                School<br>Stamp
            </div>
            
            <!-- QR Code -->
            <div class="qr-code">
                <img src="data:image/png;base64,{{ $certificate->qr_code }}" alt="QR Code">
                <div class="qr-label">Scan to Verify</div>
            </div>
            
            <!-- Issue Date -->
            <div class="issue-date">
                <div class="date-label">Date of Issue:</div>
                <div class="date-value">{{ $certificate->issue_date->format('d M Y') }}</div>
            </div>
        </div>
    </div>
</body>
</html>
