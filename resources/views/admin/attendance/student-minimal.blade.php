<!DOCTYPE html>
<html>
<head>
    <title>Minimal Test</title>
    <style>
        body { 
            background: #f0f0f0; 
            padding: 50px;
            font-family: Arial;
        }
        .test-box {
            background: white;
            padding: 30px;
            border: 3px solid red;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="test-box">
        <h1 style="color: red;">🔴 MINIMAL TEST PAGE</h1>
        <p><strong>If you can see this, Laravel is working!</strong></p>
        <p>Current Time: {{ date('Y-m-d H:i:s') }}</p>
        <p>Auth User: {{ auth()->guard('admin')->user()->username ?? 'Not logged in' }}</p>
        <hr>
        <p>This is a completely standalone HTML page without any layout inheritance.</p>
    </div>
</body>
</html>
