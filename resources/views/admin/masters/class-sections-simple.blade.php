@extends('admin.layouts.horizontal')


<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 45px;
            padding: 5px;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: #4e73df;
            border-color: #4e73df;
            color: white;
            padding: 5px 10px;
            margin: 3px;
            font-size: 14px;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            margin-right: 5px;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ffcccc;
        }
    </style>
</head>
<body>
    <!-- Top Header -->
    <div class="top-header">
        <div class="logo-section">
            <div class="logo-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="logo-text">
                <h4>MCD Inter College</h4>
                <p>Management System</p>
            </div>
        </div>
        
        <div class="header-right">
            <div class="user-profile">
                <div class="user-avatar">AD</div>
                <span style="color: #5a5c69; font-weight: 500;">{{ auth()->guard('admin')->user()->username ?? 'Admin' }}</span>
            </div>
        </div>
    </div>

    <!-- Horizontal Menu -->
    

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-card">
            <div class="content-card-header">
                <h5><i class="fas fa-layer-group me-2"></i>Class-Section Management</h5>
            </div>
            
            <div class="card mt-3" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; border: none;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <label class="mb-2" style="font-weight: bold;">Select Class</label>
                            <select class="form-control form-control-lg" id="classSelect">
                                <option value="">-- Select Class --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 text-center">
                            <label class="mb-2" style="font-weight: bold;">Total Sections</label>
                            <h3 class="mb-0" id="sectionCount">0</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="sectionsArea" class="mt-4" style="display:none;">
                <div class="card" style="background: #f8f9fc; border: 2px dashed #4e73df;">
                    <div class="card-body">
                        <h6 class="mb-3">
                            <i class="fas fa-plus-circle me-2"></i>
                            Add Section to <strong id="selectedClassName"></strong>
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="mb-2">Select Sections <span class="text-danger">*</span></label>
                                <select multiple class="form-control" id="sectionNames">
                                    <option value="A">Section A</option>
                                    <option value="B">Section B</option>
                                    <option value="C">Section C</option>
                                    <option value="D">Section D</option>
                                    <option value="E">Section E</option>
                                    <option value="F">Section F</option>
                                    <option value="G">Section G</option>
                                    <option value="H">Section H</option>
                                </select>
                                <small class="text-muted">Click to select multiple sections</small>
                            </div>
                            <div class="col-md-3">
                                <label>Capacity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="capacity" value="40">
                                <small class="text-muted">For all sections</small>
                            </div>
                            <div class="col-md-2">
                                <label>Status</label>
                                <select class="form-control" id="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <button class="btn btn-primary w-100" id="addBtn">
                                    <i class="fas fa-plus me-1"></i>Add Sections
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">
                                <i class="fas fa-list me-2"></i>
                                Sections in <strong id="selectedClassName2"></strong>
                            </h6>
                            <button class="btn btn-sm btn-success" id="quickAddBtn">
                                <i class="fas fa-layer-group me-1"></i>Quick Add A-D
                            </button>
                        </div>
                        <div id="sectionsList"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/admin-script.js') }}"></script>
    <script>
    let selectedClassId = null;
    let selectedClassName = null;
    
    $(document).ready(function() {
        // Initialize Select2 with closeOnSelect: false for checkbox-like behavior
        $('#sectionNames').select2({
            theme: 'bootstrap-5',
            placeholder: 'Select sections...',
            allowClear: true,
            width: '100%',
            closeOnSelect: false,
            templateResult: formatOption,
            templateSelection: formatSelection
        });
        
        function formatOption(option) {
            if (!option.id) {
                return option.text;
            }
            return $('<span><i class="fas fa-check-square me-2"></i>' + option.text + '</span>');
        }
        
        function formatSelection(option) {
            return option.text;
        }
    });
    
    $('#classSelect').change(function() {
        selectedClassId = $(this).val();
        selectedClassName = $(this).find('option:selected').text();
        
        if (selectedClassId) {
            $('#selectedClassName, #selectedClassName2').text(selectedClassName);
            $('#sectionsArea').show();
            loadSections();
        } else {
            $('#sectionsArea').hide();
        }
    });
    
    $('#addBtn').click(function() {
        const selectedSections = $('#sectionNames').val();
        
        if (!selectedSections || selectedSections.length === 0) {
            Swal.fire('Warning!', 'Please select at least one section', 'warning');
            return;
        }
        
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Adding...');
        
        // Add sections one by one
        let addedCount = 0;
        let failedCount = 0;
        
        const addNextSection = (index) => {
            if (index >= selectedSections.length) {
                // All done
                btn.prop('disabled', false).html('<i class="fas fa-plus me-1"></i>Add Sections');
                
                if (addedCount > 0) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        html: `<strong>${addedCount}</strong> section(s) added successfully!${failedCount > 0 ? `<br><small>${failedCount} already existed</small>` : ''}`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#sectionNames').val(null).trigger('change');
                    loadSections();
                } else {
                    Swal.fire('Info', 'All selected sections already exist', 'info');
                    btn.prop('disabled', false).html('<i class="fas fa-plus me-1"></i>Add Sections');
                }
                return;
            }
            
            const name = selectedSections[index];
            
            $.ajax({
                url: '/admin/sections',
                type: 'POST',
                data: {
                    class_id: selectedClassId,
                    section_name: name,
                    capacity: $('#capacity').val(),
                    is_active: $('#status').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    addedCount++;
                    addNextSection(index + 1);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        failedCount++;
                    }
                    addNextSection(index + 1);
                }
            });
        };
        
        addNextSection(0);
    });
    
    $('#quickAddBtn').click(function() {
        Swal.fire({
            title: 'Quick Add Sections',
            html: `Add sections A, B, C, D to <strong>${selectedClassName}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1cc88a',
            confirmButtonText: 'Yes, Add!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/sections/quick-add',
                    type: 'POST',
                    data: {
                        class_id: selectedClassId,
                        sections: ['A', 'B', 'C', 'D'],
                        capacity: 40,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire('Success!', `${response.added_count} sections added!`, 'success');
                        loadSections();
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed', 'error');
                    }
                });
            }
        });
    });
    
    function loadSections() {
        $.ajax({
            url: '/admin/sections/data',
            type: 'GET',
            data: { class_id: selectedClassId },
            success: function(response) {
                $('#sectionCount').text(response.data.length);
                
                let html = '';
                if (response.data.length === 0) {
                    html = '<div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-3x mb-2"></i><p>No sections yet</p></div>';
                } else {
                    response.data.forEach(function(s) {
                        const badge = s.is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';
                        html += `
                            <div style="background: #f8f9fc; border: 2px solid #e3e6f0; border-radius: 8px; padding: 15px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-layer-group fa-2x text-primary me-3"></i>
                                    <div>
                                        <h6 class="mb-0">Section ${s.section_name}</h6>
                                        <small class="text-muted">Capacity: ${s.capacity} students</small>
                                    </div>
                                </div>
                                <div>
                                    ${badge}
                                    <button class="btn btn-sm btn-warning ms-2" onclick="editSection(${s.id}, '${s.section_name}', ${s.capacity})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger ms-1" onclick="deleteSection(${s.id}, '${s.section_name}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                }
                $('#sectionsList').html(html);
            }
        });
    }
    
    function editSection(id, name, capacity) {
        Swal.fire({
            title: 'Edit Section',
            html: `
                <div class="mb-3 text-start">
                    <label class="form-label">Section Name</label>
                    <select class="form-control" id="edit_name">
                        <option value="A" ${name === 'A' ? 'selected' : ''}>Section A</option>
                        <option value="B" ${name === 'B' ? 'selected' : ''}>Section B</option>
                        <option value="C" ${name === 'C' ? 'selected' : ''}>Section C</option>
                        <option value="D" ${name === 'D' ? 'selected' : ''}>Section D</option>
                        <option value="E" ${name === 'E' ? 'selected' : ''}>Section E</option>
                        <option value="F" ${name === 'F' ? 'selected' : ''}>Section F</option>
                        <option value="G" ${name === 'G' ? 'selected' : ''}>Section G</option>
                        <option value="H" ${name === 'H' ? 'selected' : ''}>Section H</option>
                    </select>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Capacity</label>
                    <input type="number" id="edit_capacity" class="form-control" value="${capacity}">
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Status</label>
                    <select class="form-control" id="edit_status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Update',
            cancelButtonText: 'Cancel',
            width: '500px',
            preConfirm: () => {
                return {
                    name: document.getElementById('edit_name').value,
                    capacity: document.getElementById('edit_capacity').value,
                    status: document.getElementById('edit_status').value
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/sections/' + id,
                    type: 'PUT',
                    data: {
                        class_id: selectedClassId,
                        section_name: result.value.name,
                        capacity: result.value.capacity,
                        is_active: result.value.status,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        Swal.fire({icon: 'success', title: 'Updated!', timer: 2000, showConfirmButton: false});
                        loadSections();
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to update', 'error');
                    }
                });
            }
        });
    }
    
    function deleteSection(id, name) {
        Swal.fire({
            title: 'Delete Section?',
            html: `Delete <strong>Section ${name}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/sections/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function() {
                        Swal.fire({icon: 'success', title: 'Deleted!', timer: 2000, showConfirmButton: false});
                        loadSections();
                    }
                });
            }
        });
    }
    </script>
</body>
</html>
