@extends('admin.layouts.horizontal')
@section('title', 'Collect Fees')
@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-money-bill-wave me-2"></i>Collect Fees</h5>
    </div>
    <div class="content-card-body">
        <!-- Search Student -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Search Student</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label>Class</label>
                        <select id="classSelect" class="form-control">
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Student</label>
                        <select id="studentSelect" class="form-control" disabled>
                            <option value="">Select Student</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>&nbsp;</label>
                        <button type="button" id="loadFeesBtn" class="btn btn-primary form-control" disabled>
                            <i class="fas fa-search"></i> Load Fees
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Info & Fees -->
        <div id="studentInfoSection" style="display: none;">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Student Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Name:</strong> <span id="studentName"></span>
                        </div>
                        <div class="col-md-3">
                            <strong>Admission No:</strong> <span id="studentAdmNo"></span>
                        </div>
                        <div class="col-md-3">
                            <strong>Class:</strong> <span id="studentClass"></span>
                        </div>
                        <div class="col-md-3">
                            <strong>Father Name:</strong> <span id="studentFather"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Fees Table -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Pending Fees</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="feesTable">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>Fee Type</th>
                                    <th>Total Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Due Amount</th>
                                    <th>Late Fee</th>
                                    <th>Total Due</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="feesTableBody">
                            </tbody>
                        </table>
                    </div>

                    <!-- Payment Form -->
                    <div id="paymentForm" style="display: none;">
                        <hr>
                        <h6>Payment Details</h6>
                        <form id="processPaymentForm">
                            @csrf
                            <input type="hidden" id="selectedStudentId" name="student_id">
                            <input type="hidden" id="selectedFeeId" name="student_fee_id">
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Amount to Pay <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" id="paymentAmount" name="amount" required>
                                    <small class="text-muted">Max: ₹<span id="maxAmount">0</span></small>
                                </div>
                                <div class="col-md-3">
                                    <label>Payment Mode <span class="text-danger">*</span></label>
                                    <select class="form-control" name="payment_mode" id="paymentMode" required>
                                        <option value="cash">Cash</option>
                                        <option value="upi">UPI</option>
                                        <option value="card">Card</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="online">Online</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Payment Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="payment_date" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-3" id="transactionIdDiv" style="display: none;">
                                    <label>Transaction ID</label>
                                    <input type="text" class="form-control" name="transaction_id">
                                </div>
                            </div>

                            <div class="row mt-3" id="chequeDetails" style="display: none;">
                                <div class="col-md-4">
                                    <label>Cheque No</label>
                                    <input type="text" class="form-control" name="cheque_no">
                                </div>
                                <div class="col-md-4">
                                    <label>Cheque Date</label>
                                    <input type="date" class="form-control" name="cheque_date">
                                </div>
                                <div class="col-md-4">
                                    <label>Bank Name</label>
                                    <input type="text" class="form-control" name="bank_name">
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <label>Remarks</label>
                                    <textarea class="form-control" name="remarks" rows="2"></textarea>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Process Payment
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="cancelPayment()">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Load students when class is selected
    $('#classSelect').change(function() {
        const classId = $(this).val();
        $('#studentSelect').prop('disabled', true).html('<option value="">Loading...</option>');
        $('#loadFeesBtn').prop('disabled', true);
        $('#studentInfoSection').hide();
        
        if (classId) {
            $.get('{{ route("admin.fees.students-by-class") }}', { class_id: classId }, function(response) {
                if (response.success) {
                    let options = '<option value="">Select Student</option>';
                    response.students.forEach(function(student) {
                        options += `<option value="${student.id}">${student.name} (${student.admission_no})</option>`;
                    });
                    $('#studentSelect').html(options).prop('disabled', false);
                }
            }).fail(function() {
                $('#studentSelect').html('<option value="">Error loading students</option>');
                alert('Error loading students');
            });
        } else {
            $('#studentSelect').html('<option value="">Select Student</option>');
        }
    });

    $('#studentSelect').change(function() {
        $('#loadFeesBtn').prop('disabled', !$(this).val());
    });

    // Load student fees
    $('#loadFeesBtn').click(function() {
        const studentId = $('#studentSelect').val();
        if (!studentId) return;

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');

        $.get('{{ route("admin.fees.student-fees") }}', { student_id: studentId }, function(response) {
            if (response.success) {
                displayStudentInfo(response.student);
                displayFees(response.fees);
                $('#studentInfoSection').show();
            }
            $('#loadFeesBtn').prop('disabled', false).html('<i class="fas fa-search"></i> Load Fees');
        }).fail(function() {
            alert('Error loading student fees');
            $('#loadFeesBtn').prop('disabled', false).html('<i class="fas fa-search"></i> Load Fees');
        });
    });

    // Payment mode change
    $('#paymentMode').change(function() {
        const mode = $(this).val();
        $('#transactionIdDiv').toggle(mode === 'upi' || mode === 'card' || mode === 'online');
        $('#chequeDetails').toggle(mode === 'cheque');
    });

    // Process payment
    $('#processPaymentForm').submit(function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        $.post('{{ route("admin.fees.process-payment") }}', $(this).serialize(), function(response) {
            if (response.success) {
                alert('Payment processed successfully! Receipt No: ' + response.receipt_no);
                window.open('{{ url("admin/fees/receipt") }}/' + response.payment_id + '/download', '_blank');
                $('#loadFeesBtn').click(); // Reload fees
                cancelPayment();
            }
            submitBtn.prop('disabled', false).html('<i class="fas fa-check"></i> Process Payment');
        }).fail(function(xhr) {
            alert('Error: ' + (xhr.responseJSON?.message || 'Payment failed'));
            submitBtn.prop('disabled', false).html('<i class="fas fa-check"></i> Process Payment');
        });
    });
});

function displayStudentInfo(student) {
    $('#studentName').text(student.name);
    $('#studentAdmNo').text(student.admission_no);
    $('#studentClass').text(student.class?.class_name || 'N/A');
    $('#studentFather').text(student.father_name);
    $('#selectedStudentId').val(student.id);
}

function displayFees(fees) {
    const tbody = $('#feesTableBody');
    tbody.empty();

    if (fees.length === 0) {
        tbody.append('<tr><td colspan="8" class="text-center">No pending fees found</td></tr>');
        return;
    }

    fees.forEach(fee => {
        const totalDue = parseFloat(fee.due_amount) + parseFloat(fee.calculated_late_fee);
        tbody.append(`
            <tr>
                <td><input type="radio" name="selectedFee" value="${fee.id}" data-amount="${totalDue}"></td>
                <td>${fee.fee_structure.fee_type.name}</td>
                <td>₹${parseFloat(fee.total_amount).toFixed(2)}</td>
                <td>₹${parseFloat(fee.paid_amount).toFixed(2)}</td>
                <td>₹${parseFloat(fee.due_amount).toFixed(2)}</td>
                <td>₹${parseFloat(fee.calculated_late_fee).toFixed(2)}</td>
                <td>₹${totalDue.toFixed(2)}</td>
                <td><span class="badge bg-${getStatusColor(fee.status)}">${fee.status.toUpperCase()}</span></td>
            </tr>
        `);
    });

    // Handle fee selection
    $('input[name="selectedFee"]').change(function() {
        const feeId = $(this).val();
        const amount = $(this).data('amount');
        $('#selectedFeeId').val(feeId);
        $('#maxAmount').text(amount);
        $('#paymentAmount').attr('max', amount).val(amount);
        $('#paymentForm').show();
    });
}

function getStatusColor(status) {
    const colors = {
        'pending': 'warning',
        'partial': 'info',
        'paid': 'success',
        'overdue': 'danger'
    };
    return colors[status] || 'secondary';
}

function cancelPayment() {
    $('#paymentForm').hide();
    $('#processPaymentForm')[0].reset();
    $('input[name="selectedFee"]').prop('checked', false);
}
</script>
@endpush
@endsection
