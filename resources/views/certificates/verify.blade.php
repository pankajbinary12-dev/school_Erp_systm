<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verified - A&L Group College</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 50px 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .verify-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 50px;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .success-icon {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .success-icon i {
            font-size: 80px;
            color: #28a745;
            animation: scaleIn 0.5s ease-in-out;
        }
        
        @keyframes scaleIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .verify-title {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 10px;
        }
        
        .verify-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 40px;
        }
        
        .info-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: bold;
            color: #495057;
            width: 200px;
            flex-shrink: 0;
        }
        
        .info-value {
            color: #212529;
            flex-grow: 1;
        }
        
        .badge-verified {
            background: #28a745;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
        }
        
        .btn-back {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            color: white;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            color: white;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1 class="verify-title">Certificate Verified!</h1>
        <p class="verify-subtitle">This certificate is authentic and issued by A&L Group College</p>

        <div class="info-card">
            <h5 class="mb-4"><i class="fas fa-certificate me-2"></i>Certificate Details</h5>
            
            <div class="info-row">
                <div class="info-label">Certificate Number:</div>
                <div class="info-value"><strong>{{ $certificate->certificate_no }}</strong></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Certificate Type:</div>
                <div class="info-value">
                    <span class="badge bg-info">{{ $certificate->getTypeLabel() }}</span>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Issue Date:</div>
                <div class="info-value">{{ $certificate->issue_date->format('d M Y') }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="badge-verified">
                        <i class="fas fa-check me-1"></i>Active & Valid
                    </span>
                </div>
            </div>
        </div>

        <div class="info-card">
            <h5 class="mb-4"><i class="fas fa-user-graduate me-2"></i>Student Details</h5>
            
            <div class="info-row">
                <div class="info-label">Student Name:</div>
                <div class="info-value"><strong>{{ $certificate->student->first_name }} {{ $certificate->student->last_name }}</strong></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Father's Name:</div>
                <div class="info-value">{{ $certificate->student->father_name }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Roll Number:</div>
                <div class="info-value">{{ $certificate->student->roll_number }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Class:</div>
                <div class="info-value">{{ $certificate->student->class->name ?? 'N/A' }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Date of Birth:</div>
                <div class="info-value">{{ $certificate->student->date_of_birth }}</div>
            </div>
        </div>

        <div class="alert alert-success">
            <i class="fas fa-shield-alt me-2"></i>
            <strong>Verification Successful!</strong> This certificate has been verified against our official records.
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('certificates.verify.form') }}" class="btn-back">
                <i class="fas fa-arrow-left me-2"></i>Verify Another Certificate
            </a>
        </div>

        <div class="text-center mt-4">
            <small class="text-muted">
                Verified on {{ now()->format('d M Y, h:i A') }}<br>
                © {{ date('Y') }} A&L Group College. All rights reserved.
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
