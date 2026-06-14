<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Certificate - A&L Group College</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .verify-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 50px;
            max-width: 600px;
            width: 100%;
        }
        
        .verify-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .verify-icon {
            font-size: 80px;
            color: #667eea;
            margin-bottom: 20px;
        }
        
        .verify-title {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .verify-subtitle {
            color: #666;
            font-size: 16px;
        }
        
        .form-control {
            padding: 15px;
            font-size: 16px;
            border-radius: 10px;
        }
        
        .btn-verify {
            padding: 15px 40px;
            font-size: 18px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            width: 100%;
            margin-top: 20px;
        }
        
        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin-top: 30px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="verify-header">
            <div class="verify-icon">
                <i class="fas fa-certificate"></i>
            </div>
            <h1 class="verify-title">Verify Certificate</h1>
            <p class="verify-subtitle">Enter certificate number to verify authenticity</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('certificates.verify.check') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Certificate Number</label>
                <input type="text" 
                       name="certificate_no" 
                       class="form-control @error('certificate_no') is-invalid @enderror" 
                       placeholder="e.g., SCH/2026/BON/0001"
                       value="{{ request('cert') }}"
                       required>
                @error('certificate_no')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary btn-verify">
                <i class="fas fa-search me-2"></i>Verify Certificate
            </button>
        </form>

        <div class="info-box">
            <h6><i class="fas fa-info-circle me-2"></i>How to Verify?</h6>
            <ul class="mb-0">
                <li>Enter the certificate number printed on your certificate</li>
                <li>Or scan the QR code on the certificate</li>
                <li>Click "Verify Certificate" to check authenticity</li>
            </ul>
        </div>

        <div class="text-center mt-4">
            <small class="text-muted">
                © {{ date('Y') }} A&L Group College. All rights reserved.
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
