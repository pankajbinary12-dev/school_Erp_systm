<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fee Receipt - {{ $payment->receipt_no }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .receipt-title {
            text-align: center;
            background: #4e73df;
            color: white;
            padding: 10px;
            margin: 20px 0;
            font-size: 18px;
            font-weight: bold;
        }
        .info-section {
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .total-row {
            background: #e9ecef;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            text-align: center;
            margin-top: 50px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin: 0 auto;
            padding-top: 5px;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(0, 255, 0, 0.1);
            z-index: -1;
        }
        @media print {
            body { margin: 0; padding: 10px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="watermark">PAID</div>
    
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            @php
                $school = \App\Models\SchoolSetting::first();
            @endphp
            <h1>{{ $school->school_name ?? 'SCHOOL NAME' }}</h1>
            <p>{{ $school->address ?? 'School Address' }}</p>
            <p>Phone: {{ $school->phone ?? 'N/A' }} | Email: {{ $school->email ?? 'N/A' }}</p>
        </div>

        <!-- Receipt Title -->
        <div class="receipt-title">
            FEE RECEIPT
        </div>

        <!-- Receipt Info -->
        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Receipt No:</div>
                <div class="info-value">{{ $payment->receipt_no }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date:</div>
                <div class="info-value">{{ $payment->payment_date->format('d F Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Mode:</div>
                <div class="info-value">{{ strtoupper($payment->payment_mode) }}</div>
            </div>
            @if($payment->transaction_id)
            <div class="info-row">
                <div class="info-label">Transaction ID:</div>
                <div class="info-value">{{ $payment->transaction_id }}</div>
            </div>
            @endif
            @if($payment->cheque_no)
            <div class="info-row">
                <div class="info-label">Cheque No:</div>
                <div class="info-value">{{ $payment->cheque_no }} ({{ $payment->bank_name }})</div>
            </div>
            @endif
        </div>

        <!-- Student Info -->
        <div class="info-section" style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <h3 style="margin-top: 0;">Student Information</h3>
            <div class="info-row">
                <div class="info-label">Student Name:</div>
                <div class="info-value">{{ $payment->student->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Admission No:</div>
                <div class="info-value">{{ $payment->student->admission_no }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Class:</div>
                <div class="info-value">{{ $payment->student->class->class_name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Father Name:</div>
                <div class="info-value">{{ $payment->student->father_name }}</div>
            </div>
        </div>

        <!-- Payment Details -->
        <table>
            <thead>
                <tr>
                    <th>Fee Type</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Late Fee</th>
                    <th>Amount Paid</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $payment->studentFee->feeStructure->feeType->name }}</td>
                    <td>₹{{ number_format($payment->studentFee->total_amount, 2) }}</td>
                    <td>₹{{ number_format($payment->studentFee->paid_amount, 2) }}</td>
                    <td>₹{{ number_format($payment->late_fee_paid, 2) }}</td>
                    <td>₹{{ number_format($payment->amount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">Total Paid:</td>
                    <td>₹{{ number_format($payment->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Amount in Words -->
        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Amount in Words:</div>
                <div class="info-value" style="font-weight: bold; text-transform: capitalize;">
                    {{ ucwords(\NumberFormatter::create('en', \NumberFormatter::SPELLOUT)->format($payment->amount)) }} Rupees Only
                </div>
            </div>
        </div>

        @if($payment->remarks)
        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Remarks:</div>
                <div class="info-value">{{ $payment->remarks }}</div>
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div>
                <p><strong>Collected By:</strong> {{ $payment->collectedBy->username ?? 'N/A' }}</p>
            </div>
            <div class="signature">
                <div class="signature-line">Authorized Signature</div>
            </div>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px dashed #ccc; text-align: center; color: #666; font-size: 12px;">
            <p>This is a computer-generated receipt and does not require a signature.</p>
            <p>For any queries, please contact the accounts department.</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 30px; background: #4e73df; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
            <i class="fas fa-print"></i> Print Receipt
        </button>
    </div>
</body>
</html>
