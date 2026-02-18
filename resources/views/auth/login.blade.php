@extends('layouts.auth')

@section('title', 'Login - School ERP')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-graduation-cap fa-4x text-primary mb-3"></i>
                        <h2 class="fw-bold">School ERP System</h2>
                        <p class="text-muted">Login to your account</p>
                    </div>

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
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" name="username" id="username" placeholder="Enter username" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Remember me</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <small class="text-muted">© 2026 School ERP System. All rights reserved.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .card {
        border-radius: 20px;
        border: none;
    }

    .input-group-text {
        background: #f8f9fc;
        border-right: none;
    }

    .form-control {
        border-left: none;
    }

    .form-control:focus {
        box-shadow: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        font-weight: 600;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }
</style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
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

            const formData = {
                username: $('#username').val(),
                password: $('#password').val(),
                user_type: $('#userType').val()
            };

            showLoading();

            $.ajax({
                url: '{{ route("login.post") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false,
                            position: 'top-end', 
                            toast: true, 
                            width: 400,
                            padding: '1em',
                            showCloseButton: true
                        }).then(() => {
                            window.location.href = response.redirect;
                        });
                    }
                },
                error: function(xhr) {
                    hideLoading();
                    let message = 'Login failed! Please check your credentials.';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed!',
                        text: message
                    });
                }
            });
        });
    });
</script>
@endpush