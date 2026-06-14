<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - School ERP System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            display: flex;
            flex-direction: row;
        }

        .login-left {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            flex: 1;
            min-height: 500px;
        }

        .login-left .icon-main {
            font-size: 80px;
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .login-left h2 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .login-left p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        .feature-icons {
            display: flex;
            gap: 20px;
            font-size: 40px;
            margin-top: 20px;
        }

        .feature-icons i {
            opacity: 0.8;
            transition: all 0.3s;
        }

        .feature-icons i:hover {
            opacity: 1;
            transform: scale(1.2);
        }

        .login-right {
            padding: 60px 50px;
            flex: 1;
        }

        .login-right h3 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .login-right .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 15px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .btn-toggle-password {
            border: 2px solid #e0e0e0;
            border-left: none;
            border-radius: 0 10px 10px 0;
            background: #f8f9fa;
            color: #666;
        }

        .btn-toggle-password:hover {
            background: #e9ecef;
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s;
            font-size: 16px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .footer-text {
            text-align: center;
            margin-top: 25px;
            color: #999;
            font-size: 13px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 500px;
            }

            .login-left {
                padding: 40px 30px;
                min-height: auto;
            }

            .login-left .icon-main {
                font-size: 60px;
                margin-bottom: 15px;
            }

            .login-left h2 {
                font-size: 24px;
                margin-bottom: 10px;
            }

            .login-left p {
                font-size: 14px;
                margin-bottom: 20px;
            }

            .feature-icons {
                font-size: 30px;
                gap: 15px;
                margin-top: 15px;
            }

            .login-right {
                padding: 40px 30px;
            }

            .login-right h3 {
                font-size: 24px;
            }

            .login-right .subtitle {
                font-size: 14px;
                margin-bottom: 25px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .login-left {
                padding: 30px 20px;
            }

            .login-left .icon-main {
                font-size: 50px;
            }

            .login-left h2 {
                font-size: 20px;
            }

            .login-left p {
                font-size: 13px;
            }

            .feature-icons {
                font-size: 25px;
                gap: 10px;
            }

            .login-right {
                padding: 30px 20px;
            }

            .login-right h3 {
                font-size: 20px;
            }

            .form-control, .form-select {
                padding: 10px 12px;
                font-size: 14px;
            }

            .btn-login {
                padding: 12px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side -->
        <div class="login-left">
            <i class="fas fa-graduation-cap icon-main"></i>
            <h2>School ERP System</h2>
            <p>Complete School Management Solution</p>
            <div class="feature-icons">
                <i class="fas fa-book" title="Academics"></i>
                <i class="fas fa-chart-line" title="Analytics"></i>
                <i class="fas fa-calendar-check" title="Attendance"></i>
                <i class="fas fa-trophy" title="Results"></i>
            </div>
        </div>

        <!-- Right Side -->
        <div class="login-right">
            <h3>Welcome Back!</h3>
            <p class="subtitle">Please login to your account</p>

            <div id="alertContainer"></div>

            <form id="loginForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label">User Type</label>
                    <select class="form-select" name="user_type" id="userType" required>
                        <option value="">Select User Type</option>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" class="form-control" name="username" id="username" placeholder="Enter your username" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required>
                        <button class="btn btn-toggle-password" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </form>

            <div class="footer-text">
                <i class="fas fa-info-circle me-1"></i>
                Contact your administrator if you forgot your password
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Set CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Toggle password visibility
            $('#togglePassword').click(function() {
                const passwordField = $('#password');
                const icon = $(this).find('i');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Handle login form submission
            $('#loginForm').submit(function(e) {
                e.preventDefault();

                const userType = $('#userType').val();
                const username = $('#username').val();
                const password = $('#password').val();

                if (!userType) {
                    showAlert('Please select user type', 'warning');
                    return;
                }

                // Show loading
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Logging in...').prop('disabled', true);

                $.ajax({
                    url: '{{ route("login.post") }}',
                    type: 'POST',
                    data: {
                        username: username,
                        password: password,
                        user_type: userType
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message, 'success');
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 1000);
                        }
                    },
                    error: function(xhr) {
                        submitBtn.html(originalText).prop('disabled', false);
                        
                        let message = 'Login failed! Please check your credentials.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        
                        showAlert(message, 'danger');
                    }
                });
            });

            function showAlert(message, type) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                $('#alertContainer').html(alertHtml);
                
                // Auto dismiss after 5 seconds
                setTimeout(function() {
                    $('#alertContainer .alert').fadeOut();
                }, 5000);
            }
        });
    </script>
</body>
</html>