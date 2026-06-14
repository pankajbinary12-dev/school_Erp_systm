@extends('admin.layouts.horizontal')
@section('title', 'User Management')
@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-users-cog me-2"></i>User Management & Login History</h5>
    </div>
    <div class="content-card-body">
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#all-users">All Users</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#login-history">Login History</a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- All Users Tab -->
            <div id="all-users" class="tab-pane fade show active">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select id="userTypeFilter" class="form-select">
                            <option value="">All User Types</option>
                            <option value="admin">Admin</option>
                            <option value="teacher">Teacher</option>
                            <option value="student">Student</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="text" id="searchUsers" class="form-control" placeholder="Search by name, email, username...">
                    </div>
                    <div class="col-md-3 text-end">
                        <button class="btn btn-primary" onclick="refreshUsers()">
                            <i class="fas fa-sync me-2"></i>Refresh
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-sm" id="usersTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User Type</th>
                                <th>Username/Name</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <tr>
                                <td colspan="8" class="text-center">
                                    <i class="fas fa-spinner fa-spin"></i> Loading...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Login History Tab -->
            <div id="login-history" class="tab-pane fade">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select id="historyUserType" class="form-select">
                            <option value="">All User Types</option>
                            <option value="admin">Admin</option>
                            <option value="teacher">Teacher</option>
                            <option value="student">Student</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="historyDate" class="form-control">
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-primary" onclick="refreshHistory()">
                            <i class="fas fa-sync me-2"></i>Refresh
                        </button>
                        <button class="btn btn-success" onclick="exportHistory()">
                            <i class="fas fa-download me-2"></i>Export
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-sm" id="historyTable">
                        <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>User Type</th>
                                <th>Username</th>
                                <th>IP Address</th>
                                <th>Device</th>
                                <th>Browser</th>
                                <th>OS</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <tr>
                                <td colspan="8" class="text-center">
                                    <i class="fas fa-spinner fa-spin"></i> Loading...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Password Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Credentials</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Username:</label>
                    <p id="modalUsername" class="form-control-plaintext"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Email:</label>
                    <p id="modalEmail" class="form-control-plaintext"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Password (Encrypted):</label>
                    <textarea id="modalPassword" class="form-control" rows="2" readonly style="font-size: 11px;"></textarea>
                    <small class="text-danger d-block mt-2">
                        <i class="fas fa-exclamation-triangle"></i> Passwords are encrypted with bcrypt and cannot be decrypted for security reasons.
                    </small>
                    <small class="text-info d-block mt-1">
                        <i class="fas fa-info-circle"></i> Default password for most users is: <strong>password</strong> or <strong>123456</strong>
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="resetPassword()">Reset Password</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentUserId = null;
let currentUserType = null;
let allUsers = [];

// Load all users
function loadUsers() {
    const userType = document.getElementById('userTypeFilter').value;
    
    fetch(`/admin/settings/users/data?type=${userType}`)
        .then(response => response.json())
        .then(data => {
            allUsers = data.users;
            displayUsers(allUsers);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('usersTableBody').innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error loading users</td></tr>';
        });
}

function displayUsers(users) {
    const tbody = document.getElementById('usersTableBody');
    if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No users found</td></tr>';
        return;
    }
    
    tbody.innerHTML = users.map(user => `
        <tr>
            <td>${user.id}</td>
            <td><span class="badge bg-${getUserTypeBadge(user.type)}">${user.type}</span></td>
            <td>${user.name}</td>
            <td>${user.email || 'N/A'}</td>
            <td>
                <button class="btn btn-sm btn-info" onclick='viewPassword(${user.id}, "${user.type}", "${escapeHtml(user.name)}", "${escapeHtml(user.email)}", \`${user.password}\`)'>
                    <i class="fas fa-eye"></i> View
                </button>
            </td>
            <td><span class="badge bg-${user.status === 'Active' ? 'success' : 'danger'}">${user.status}</span></td>
            <td>${user.last_login || 'Never'}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick='quickResetPassword(${user.id}, "${user.type}", "${escapeHtml(user.name)}")' title="Reset Password">
                    <i class="fas fa-key"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

// Search functionality
document.getElementById('searchUsers').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const filtered = allUsers.filter(user => {
        return user.name.toLowerCase().includes(searchTerm) ||
               (user.email && user.email.toLowerCase().includes(searchTerm)) ||
               user.type.toLowerCase().includes(searchTerm);
    });
    displayUsers(filtered);
});

// Load login history
function loadHistory() {
    const userType = document.getElementById('historyUserType').value;
    const date = document.getElementById('historyDate').value;
    
    fetch(`/admin/settings/users/login-history?type=${userType}&date=${date}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('historyTableBody');
            if (data.logs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center">No login history found</td></tr>';
                return;
            }
            
            tbody.innerHTML = data.logs.map(log => `
                <tr>
                    <td>${log.login_at}</td>
                    <td><span class="badge bg-${getUserTypeBadge(log.user_type)}">${log.user_type}</span></td>
                    <td>${log.username}</td>
                    <td>${log.ip_address || 'N/A'}</td>
                    <td>${log.device_type || 'N/A'}</td>
                    <td>${log.browser || 'N/A'}</td>
                    <td>${log.os || 'N/A'}</td>
                    <td><span class="badge bg-${log.status === 'success' ? 'success' : 'danger'}">${log.status}</span></td>
                </tr>
            `).join('');
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('historyTableBody').innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error loading history</td></tr>';
        });
}

function getUserTypeBadge(type) {
    const badges = {
        'admin': 'danger',
        'teacher': 'primary',
        'student': 'success',
        'staff': 'warning'
    };
    return badges[type] || 'secondary';
}

function viewPassword(id, type, name, email, password) {
    currentUserId = id;
    currentUserType = type;
    document.getElementById('modalUsername').textContent = name;
    document.getElementById('modalEmail').textContent = email || 'N/A';
    document.getElementById('modalPassword').value = password;
    const modal = new bootstrap.Modal(document.getElementById('passwordModal'));
    modal.show();
}

function quickResetPassword(id, type, name) {
    currentUserId = id;
    currentUserType = type;
    
    Swal.fire({
        title: `Reset Password for ${name}`,
        html: `
            <input type="password" id="swal-password" class="swal2-input" placeholder="New password (min 6 characters)">
            <input type="password" id="swal-confirm" class="swal2-input" placeholder="Confirm password">
        `,
        showCancelButton: true,
        confirmButtonText: 'Reset Password',
        confirmButtonColor: '#ffc107',
        preConfirm: () => {
            const password = document.getElementById('swal-password').value;
            const confirm = document.getElementById('swal-confirm').value;
            
            if (!password || password.length < 6) {
                Swal.showValidationMessage('Password must be at least 6 characters');
                return false;
            }
            if (password !== confirm) {
                Swal.showValidationMessage('Passwords do not match');
                return false;
            }
            return password;
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            fetch('/admin/settings/users/reset-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    user_id: currentUserId,
                    user_type: currentUserType,
                    new_password: result.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', data.message, 'success');
                    loadUsers();
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Failed to reset password', 'error');
            });
        }
    });
}

function resetPassword() {
    if (!currentUserId || !currentUserType) {
        Swal.fire('Error!', 'Please select a user first', 'error');
        return;
    }

    Swal.fire({
        title: 'Reset Password',
        html: `
            <input type="password" id="swal-password" class="swal2-input" placeholder="New password (min 6 characters)">
            <input type="password" id="swal-confirm" class="swal2-input" placeholder="Confirm password">
        `,
        showCancelButton: true,
        confirmButtonText: 'Reset Password',
        preConfirm: () => {
            const password = document.getElementById('swal-password').value;
            const confirm = document.getElementById('swal-confirm').value;
            
            if (!password || password.length < 6) {
                Swal.showValidationMessage('Password must be at least 6 characters');
                return false;
            }
            if (password !== confirm) {
                Swal.showValidationMessage('Passwords do not match');
                return false;
            }
            return password;
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            fetch('/admin/settings/users/reset-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    user_id: currentUserId,
                    user_type: currentUserType,
                    new_password: result.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', data.message, 'success');
                    const modalElement = document.getElementById('passwordModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                    loadUsers();
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Failed to reset password', 'error');
            });
        }
    });
}

function refreshUsers() {
    loadUsers();
}

function refreshHistory() {
    loadHistory();
}

function exportHistory() {
    const userType = document.getElementById('historyUserType').value;
    const date = document.getElementById('historyDate').value;
    window.location.href = `/admin/settings/users/export-history?type=${userType}&date=${date}`;
}

// Event listeners
document.getElementById('userTypeFilter').addEventListener('change', loadUsers);
document.getElementById('historyUserType').addEventListener('change', loadHistory);
document.getElementById('historyDate').addEventListener('change', loadHistory);

// Initial load
loadUsers();
loadHistory();
</script>
@endsection
