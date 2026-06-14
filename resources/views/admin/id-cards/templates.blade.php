@extends('admin.layouts.app')

@section('title', 'ID Card Templates')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-id-card me-2"></i>ID Card Templates</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#templateModal" onclick="resetForm()">
            <i class="fas fa-plus"></i> Create Template
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row" id="templatesContainer">
                <!-- Templates will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Template Modal -->
<div class="modal fade" id="templateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Create Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="templateForm">
                @csrf
                <input type="hidden" id="templateId" name="template_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Template Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="template_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Border Style <span class="text-danger">*</span></label>
                            <select class="form-select" name="border_style" id="borderStyleSelect" required>
                                <option value="wave">Wave Design (Colorful)</option>
                                <option value="corporate">Corporate (Professional)</option>
                                <option value="modern">Modern Gradient</option>
                                <option value="classic">Classic Border</option>
                                <option value="minimal">Minimal Clean</option>
                            </select>
                            <small class="text-muted">Choose your preferred card design style</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Border Color</label>
                            <input type="color" class="form-control form-control-color" name="border_color" value="#667eea">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Background Color</label>
                            <input type="color" class="form-control form-control-color" name="background_color" value="#ffffff">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Text Color</label>
                            <input type="color" class="form-control form-control-color" name="text_color" value="#000000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Header Background Color</label>
                            <input type="color" class="form-control form-control-color" name="header_bg_color" value="#667eea">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Display Options</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="show_logo" id="showLogo" checked>
                                <label class="form-check-label" for="showLogo">Show School Logo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="show_qr_code" id="showQR" checked>
                                <label class="form-check-label" for="showQR">Show QR Code</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="show_barcode" id="showBarcode">
                                <label class="form-check-label" for="showBarcode">Show Barcode</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Template</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let editMode = false;
let editId = null;

$(document).ready(function() {
    loadTemplates();

    $('#templateForm').submit(function(e) {
        e.preventDefault();
        
        const formData = {
            _token: '{{ csrf_token() }}',
            template_name: $('input[name="template_name"]').val(),
            border_style: $('select[name="border_style"]').val(),
            border_color: $('input[name="border_color"]').val(),
            background_color: $('input[name="background_color"]').val(),
            text_color: $('input[name="text_color"]').val(),
            header_bg_color: $('input[name="header_bg_color"]').val(),
            show_logo: $('#showLogo').is(':checked') ? 1 : 0,
            show_qr_code: $('#showQR').is(':checked') ? 1 : 0,
            show_barcode: $('#showBarcode').is(':checked') ? 1 : 0
        };

        const url = editMode ? 
            `/admin/id-cards/templates/${editId}` : 
            '/admin/id-cards/templates';
        
        const method = editMode ? 'PUT' : 'POST';

        if (editMode) {
            formData._method = 'PUT';
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000
                    });
                    $('#templateModal').modal('hide');
                    loadTemplates();
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                let errorMsg = 'Failed to save template!';
                if (errors) {
                    errorMsg = Object.values(errors).flat().join('\n');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMsg
                });
            }
        });
    });
});

function loadTemplates() {
    $.get('/admin/id-cards/templates/data', function(response) {
        if (response.success) {
            displayTemplates(response.data);
        }
    });
}

function displayTemplates(templates) {
    const container = $('#templatesContainer');
    container.empty();

    if (templates.length === 0) {
        container.html('<div class="col-12 text-center py-5"><p class="text-muted">No templates found. Create your first template!</p></div>');
        return;
    }

    templates.forEach(template => {
        const borderClass = getBorderClass(template.border_style);
        const card = `
            <div class="col-md-4 mb-4">
                <div class="card ${borderClass}" style="border-color: ${template.border_color};">
                    <div class="card-header text-white" style="background-color: ${template.header_bg_color};">
                        <h6 class="mb-0">${template.template_name}</h6>
                    </div>
                    <div class="card-body" style="background-color: ${template.background_color}; color: ${template.text_color};">
                        <p class="mb-2"><strong>Style:</strong> ${template.border_style}</p>
                        <p class="mb-2"><strong>Options:</strong></p>
                        <ul class="small">
                            <li>Logo: ${template.show_logo ? '✓' : '✗'}</li>
                            <li>QR Code: ${template.show_qr_code ? '✓' : '✗'}</li>
                            <li>Barcode: ${template.show_barcode ? '✓' : '✗'}</li>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-info" onclick="editTemplate(${template.id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteTemplate(${template.id})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.append(card);
    });
}

function getBorderClass(style) {
    const classes = {
        'modern': 'border-3',
        'classic': 'border-2',
        'colorful': 'border-4',
        'minimal': 'border-1'
    };
    return classes[style] || 'border-2';
}

function resetForm() {
    editMode = false;
    editId = null;
    $('#templateForm')[0].reset();
    $('#modalTitle').text('Create Template');
}

function editTemplate(id) {
    $.get(`/admin/id-cards/templates/data`, function(response) {
        const template = response.data.find(t => t.id === id);
        if (template) {
            editMode = true;
            editId = id;
            $('#modalTitle').text('Edit Template');
            $('input[name="template_name"]').val(template.template_name);
            $('select[name="border_style"]').val(template.border_style);
            $('input[name="border_color"]').val(template.border_color);
            $('input[name="background_color"]').val(template.background_color);
            $('input[name="text_color"]').val(template.text_color);
            $('input[name="header_bg_color"]').val(template.header_bg_color);
            $('#showLogo').prop('checked', template.show_logo);
            $('#showQR').prop('checked', template.show_qr_code);
            $('#showBarcode').prop('checked', template.show_barcode);
            $('#templateModal').modal('show');
        }
    });
}

function deleteTemplate(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This template will be deleted permanently!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/id-cards/templates/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', response.message, 'success');
                        loadTemplates();
                    }
                }
            });
        }
    });
}
</script>
@endpush
