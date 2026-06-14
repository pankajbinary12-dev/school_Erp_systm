<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentFee;
use App\Models\FeePayment;
use App\Models\FeeType;
use App\Models\FeeStructure;
use App\Models\FeeDiscount;
use App\Models\Classes;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class FeeManagementController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        $stats = [
            'total_collected_today' => FeePayment::whereDate('payment_date', today())->sum('amount'),
            'total_collected_month' => FeePayment::whereMonth('payment_date', now()->month)->sum('amount'),
            'total_pending' => StudentFee::where('status', '!=', 'paid')->sum('due_amount'),
            'total_overdue' => StudentFee::where('status', 'overdue')->sum('due_amount'),
        ];

        $recentPayments = FeePayment::with(['student', 'collectedBy'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.fees.dashboard', compact('stats', 'recentPayments'));
    }

    // Fee Collection
    public function collectFee()
    {
        $classes = Classes::orderBy('class_name')->get();
        return view('admin.fees.collect', compact('classes'));
    }

    public function getStudentsByClass(Request $request)
    {
        $students = Student::where('class_id', $request->class_id)
            ->where('status', 'Active')
            ->orderBy('name')
            ->get(['id', 'name', 'admission_no']);

        return response()->json([
            'success' => true,
            'students' => $students
        ]);
    }

    public function getStudentFees(Request $request)
    {
        $student = Student::with(['studentFees.feeStructure.feeType'])
            ->findOrFail($request->student_id);

        $fees = $student->studentFees()
            ->where('status', '!=', 'paid')
            ->with('feeStructure.feeType')
            ->get()
            ->map(function($fee) {
                $fee->calculated_late_fee = $fee->calculateLateFee();
                return $fee;
            });

        return response()->json([
            'success' => true,
            'student' => $student,
            'fees' => $fees
        ]);
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'student_fee_id' => 'required|exists:student_fees,id',
            'amount' => 'required|numeric|min:0',
            'payment_mode' => 'required|in:cash,upi,card,cheque,bank_transfer,online',
            'payment_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $studentFee = StudentFee::findOrFail($request->student_fee_id);
            
            // Calculate late fee
            $lateFee = $studentFee->calculateLateFee();
            $totalDue = $studentFee->due_amount + $lateFee;
            
            if ($request->amount > $totalDue) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount exceeds due amount'
                ], 400);
            }

            // Create payment record
            $payment = FeePayment::create([
                'receipt_no' => FeePayment::generateReceiptNo(),
                'student_id' => $request->student_id,
                'student_fee_id' => $request->student_fee_id,
                'amount' => $request->amount,
                'late_fee_paid' => min($request->amount, $lateFee),
                'payment_mode' => $request->payment_mode,
                'transaction_id' => $request->transaction_id,
                'cheque_no' => $request->cheque_no,
                'cheque_date' => $request->cheque_date,
                'bank_name' => $request->bank_name,
                'remarks' => $request->remarks,
                'payment_date' => $request->payment_date,
                'collected_by' => auth()->guard('admin')->id(),
                'status' => 'success'
            ]);

            // Update student fee
            $studentFee->paid_amount += $request->amount;
            $studentFee->due_amount = max(0, $studentFee->total_amount - $studentFee->paid_amount);
            $studentFee->late_fee = max(0, $lateFee - $payment->late_fee_paid);
            $studentFee->updateStatus();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'receipt_no' => $payment->receipt_no,
                'payment_id' => $payment->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error processing payment: ' . $e->getMessage()
            ], 500);
        }
    }

    // Receipt
    public function downloadReceipt($id)
    {
        $payment = FeePayment::with(['student', 'studentFee.feeStructure.feeType', 'collectedBy'])
            ->findOrFail($id);

        $pdf = PDF::loadView('admin.fees.receipt', compact('payment'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('receipt_' . $payment->receipt_no . '.pdf');
    }

    public function printReceipt($id)
    {
        $payment = FeePayment::with(['student', 'studentFee.feeStructure.feeType', 'collectedBy'])
            ->findOrFail($id);

        return view('admin.fees.receipt', compact('payment'));
    }

    // Fee Structure
    public function feeStructure()
    {
        $structures = FeeStructure::with(['class', 'feeType', 'session'])
            ->latest()
            ->paginate(20);
        
        $classes = Classes::orderBy('class_name')->get();
        $feeTypes = FeeType::active()->get();

        return view('admin.fees.structure', compact('structures', 'classes', 'feeTypes'));
    }

    public function storeFeeStructure(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'fee_type_id' => 'required|exists:fee_types,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'late_fee_amount' => 'nullable|numeric|min:0',
            'late_fee_days' => 'nullable|integer|min:0',
        ]);

        FeeStructure::create($request->all());

        return redirect()->back()->with('success', 'Fee structure created successfully');
    }

    public function updateFeeStructure(Request $request, $id)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'fee_type_id' => 'required|exists:fee_types,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'late_fee_amount' => 'nullable|numeric|min:0',
            'late_fee_days' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $structure = FeeStructure::findOrFail($id);
        $structure->update($request->all());

        return redirect()->back()->with('success', 'Fee structure updated successfully');
    }

    // Reports
    public function reports(Request $request)
    {
        $type = $request->get('type', 'daily');
        $date = $request->get('date', today());

        $query = FeePayment::with(['student', 'studentFee.feeStructure.feeType']);

        if ($type === 'daily') {
            $query->whereDate('payment_date', $date);
        } elseif ($type === 'monthly') {
            $query->whereMonth('payment_date', now()->month)
                  ->whereYear('payment_date', now()->year);
        }

        $payments = $query->latest()->get();
        $total = $payments->sum('amount');

        return view('admin.fees.reports', compact('payments', 'total', 'type'));
    }

    // Assign Fees to Students
    public function assignFees()
    {
        $classes = Classes::with('students')->orderBy('class_name')->get();
        $feeStructures = FeeStructure::with(['feeType', 'class'])->active()->get();

        return view('admin.fees.assign', compact('classes', 'feeStructures'));
    }

    public function storeAssignFees(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'fee_structure_ids' => 'required|array',
            'fee_structure_ids.*' => 'exists:fee_structures,id',
        ]);

        $students = Student::where('class_id', $request->class_id)
            ->where('status', 'Active')
            ->get();

        $count = 0;
        foreach ($students as $student) {
            foreach ($request->fee_structure_ids as $structureId) {
                $structure = FeeStructure::find($structureId);
                
                // Check if already assigned
                $exists = StudentFee::where('student_id', $student->id)
                    ->where('fee_structure_id', $structureId)
                    ->exists();

                if (!$exists) {
                    StudentFee::create([
                        'student_id' => $student->id,
                        'fee_structure_id' => $structureId,
                        'total_amount' => $structure->amount,
                        'due_amount' => $structure->amount,
                        'due_date' => $structure->due_date,
                        'status' => 'pending'
                    ]);
                    $count++;
                }
            }
        }

        return redirect()->back()->with('success', "Fees assigned to {$count} student(s) successfully");
    }

    // Payment History
    public function paymentHistory(Request $request)
    {
        $query = FeePayment::with(['student', 'studentFee.feeStructure.feeType', 'collectedBy']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->to_date);
        }

        $payments = $query->latest()->paginate(20);

        return view('admin.fees.payment-history', compact('payments'));
    }

    // Pending Fees
    public function pendingFees()
    {
        $pendingFees = StudentFee::with(['student', 'feeStructure.feeType'])
            ->where('status', '!=', 'paid')
            ->latest()
            ->paginate(20);

        return view('admin.fees.pending', compact('pendingFees'));
    }
}
