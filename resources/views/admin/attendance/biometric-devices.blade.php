@extends('admin.layouts.horizontal')
@section('title', 'Biometric Devices')
@section('content')
<div class="content-card">
    <div class="content-card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-fingerprint me-2"></i>Biometric Devices</h5>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
            <i class="fas fa-plus me-1"></i>Add Device
        </button>
    </div>
    
    <div class="content-card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="devicesTable">
                <thead class="table-light">
                    <tr>
                        <th>Device Code</th>
                        <th>Device Name</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>IP Address</th>
                        <th>Status</th>
                        <th>Last Sync</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="devicesTableBody">
                    <tr>
                        <td colspan="8" class="text-center">Loading devices...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Device Modal -->
<div class="modal fade" id="addDeviceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Biometric Device</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addDeviceForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Device Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="deviceCode" required placeholder="BIO001">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Device Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="deviceName" required placeholder="Main Gate Scanner">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Device Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="deviceType" required>
                                <option value="">-- Select Type --</option>
                                <option value="fingerprint">Fingerprint Scanner</option>
                                <option value="face">Face Recognition</option>
                                <option value="card">RFID Card</option>
                                <option value="hybrid">Hybrid (Multiple)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">School</label>
                            <select class="form-select" id="schoolId">
                                <option value="">-- Select School --</option>
                                <!-- Will be populated dynamically -->
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Location Description</label>
                        <input type="text" class="form-control" id="locationDesc" placeholder="Main Entrance, Building A">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="number" step="0.000001" class="form-control" id="latitude" placeholder="28.5677">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="number" step="0.000001" class="form-control" id="longitude" placeholder="77.1849">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">IP Address</label>
                            <input type="text" class="form-control" id="ipAddress" placeholder="192.168.1.100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Port</label>
                            <input type="number" class="form-control" id="port" placeholder="80">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">API Endpoint</label>
                        <input type="url" class="form-control" id="apiEndpoint" placeholder="http://192.168.1.100/api">
                        <small class="text-muted">Full URL to device API</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">API Key</label>
                        <input type="text" class="form-control" id="apiKey" placeholder="your-secret-api-key">
                        <small class="text-muted">Authentication key for device API</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="status">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Maintenance">Maintenance</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveDevice()">
                    <i class="fas fa-save me-1"></i>Save Device
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Test Device Modal -->
<div class="modal fade" id="testDeviceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Device Connection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="testResult" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Testing...</span>
                    </div>
                    <p class="mt-2">Testing device connection...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let devices = [];

document.addEventListener('DOMContentLoaded', function() {
    loadDevices();
    loadSchools();
});

function loadDevices() {
    fetch('{{ route('admin.attendance.biometric.list') }}', {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            devices = data.data;
            renderDevicesTable();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('devicesTableBody').innerHTML = 
            '<tr><td colspan="8" class="text-center text-danger">Error loading devices</td></tr>';
    });
}

function loadSchools() {
    // Load schools for dropdown
    fetch('/admin/schools/list', {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('schoolId');
            data.data.forEach(school => {
                const option = document.createElement('option');
                option.value = school.id;
                option.textContent = school.school_name;
                select.appendChild(option);
            });
        }
    })
    .catch(error => console.error('Error loading schools:', error));
}

function renderDevicesTable() {
    const tbody = document.getElementById('devicesTableBody');
    
    if (devices.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No devices found. Add your first device!</td></tr>';
        return;
    }

    tbody.innerHTML = '';
    devices.forEach(device => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${device.device_code}</strong></td>
            <td>${device.device_name}</td>
            <td><span class="badge bg-info">${device.device_type}</span></td>
            <td>${device.location_description || '-'}</td>
            <td>${device.ip_address || '-'}</td>
            <td><span class="badge bg-${getStatusColor(device.status)}">${device.status}</span></td>
            <td>${device.last_sync_at ? formatDate(device.last_sync_at) : 'Never'}</td>
            <td>
                <button class="btn btn-sm btn-success" onclick="testDevice(${device.id})" title="Test Connection">
                    <i class="fas fa-plug"></i>
                </button>
                <button class="btn btn-sm btn-primary" onclick="syncDevice(${device.id})" title="Sync Now">
                    <i class="fas fa-sync"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteDevice(${device.id})" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function getStatusColor(status) {
    const colors = {
        'Active': 'success',
        'Inactive': 'secondary',
        'Maintenance': 'warning'
    };
    return colors[status] || 'secondary';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString();
}

function saveDevice() {
    const data = {
        device_code: document.getElementById('deviceCode').value,
        device_name: document.getElementById('deviceName').value,
        device_type: document.getElementById('deviceType').value,
        school_id: document.getElementById('schoolId').value || null,
        location_description: document.getElementById('locationDesc').value,
        latitude: document.getElementById('latitude').value || null,
        longitude: document.getElementById('longitude').value || null,
        ip_address: document.getElementById('ipAddress').value,
        port: document.getElementById('port').value || null,
        api_endpoint: document.getElementById('apiEndpoint').value,
        api_key: document.getElementById('apiKey').value,
        status: document.getElementById('status').value
    };

    fetch('{{ route('admin.attendance.biometric.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Device added successfully!');
            bootstrap.Modal.getInstance(document.getElementById('addDeviceModal')).hide();
            document.getElementById('addDeviceForm').reset();
            loadDevices();
        } else {
            alert('❌ Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error saving device');
    });
}

function testDevice(deviceId) {
    const modal = new bootstrap.Modal(document.getElementById('testDeviceModal'));
    modal.show();
    
    document.getElementById('testResult').innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Testing...</span>
        </div>
        <p class="mt-2">Testing device connection...</p>
    `;

    fetch(`{{ url('admin/attendance/biometric/test') }}/${deviceId}`, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('testResult').innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <h5>Connection Successful!</h5>
                    <p>${data.message}</p>
                </div>
            `;
        } else {
            document.getElementById('testResult').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle fa-3x mb-3"></i>
                    <h5>Connection Failed</h5>
                    <p>${data.message}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        document.getElementById('testResult').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-times-circle fa-3x mb-3"></i>
                <h5>Error</h5>
                <p>Failed to test device connection</p>
            </div>
        `;
    });
}

function syncDevice(deviceId) {
    if (!confirm('Sync attendance data from this device?')) return;

    fetch(`{{ url('admin/attendance/biometric/sync') }}/${deviceId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            loadDevices();
        } else {
            alert('❌ Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error syncing device');
    });
}

function deleteDevice(deviceId) {
    if (!confirm('Are you sure you want to delete this device?')) return;

    fetch(`{{ url('admin/attendance/biometric/delete') }}/${deviceId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Device deleted successfully');
            loadDevices();
        } else {
            alert('❌ Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error deleting device');
    });
}
</script>
@endsection
