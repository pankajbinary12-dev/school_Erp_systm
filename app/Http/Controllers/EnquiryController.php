<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Classes;
use App\Models\Session;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EnquiryController extends Controller
{
    // Dashboard
    public function index()
    {
        $stats = [
            'total_enquiries' => Enquiry::count(),
            'pending_enquiries' => Enquiry::where('status', 'Pending')->count(),
            'approved_enquiries' => Enquiry::where('status', 'Approved')->count(),
            'converted_enquiries' => Enquiry::where('status', 'Converted')->count(),
            'fee_pending' => Enquiry::where('fee_status', 'Pending')->count(),
            'today_enquiries' => Enquiry::whereDate('created_at', today())->count(),
        ];

        return view('admin.enquiry.index', compact('stats'));
    }

    // List All Enquiries
    public function list()
    {
        $enquiries = Enquiry::with(['class', 'session', 'createdBy'])
            ->latest()
            ->paginate(20);

        return view('admin.enquiry.list', compact('enquiries'));
    }

    // Create Enquiry Form
    public function create()
    {
        $classes = Classes::where('is_active', 'Active')->orderBy('class_numeric')->get();
        $sessions = Session::where('is_active', 'Active')->orderBy('id', 'desc')->get();
        
        // Debug
        \Log::info('Enquiry Create - Classes Count: ' . $classes->count());
        \Log::info('Enquiry Create - Sessions Count: ' . $sessions->count());
        
        return view('admin.enquiry.create', compact('classes', 'sessions'));
    }

    // Store Enquiry
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'date_of_birth' => 'required|date',
                'gender' => 'required|in:Male,Female,Other',
                'email' => 'nullable|email',
                'phone' => 'required|string|max:15',
                'address' => 'required|string',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'pincode' => 'required|string|max:10',
                'class_id' => 'required|exists:classes,id',
                'session_id' => 'required|exists:sessions,id',
                'previous_school' => 'nullable|string|max:255',
                'previous_class' => 'nullable|string|max:50',
                'previous_percentage' => 'nullable|numeric|min:0|max:100',
                'father_name' => 'required|string|max:255',
                'father_phone' => 'required|string|max:15',
                'father_occupation' => 'nullable|string|max:100',
                'mother_name' => 'required|string|max:255',
                'mother_phone' => 'nullable|string|max:15',
                'mother_occupation' => 'nullable|string|max:100',
                'annual_income' => 'nullable|numeric|min:0',
                'source' => 'nullable|string|max:50',
                'reference_by' => 'nullable|string|max:255',
                'registration_fee' => 'required|numeric|min:0',
                'remarks' => 'nullable|string',
            ]);

            $validated['enquiry_number'] = Enquiry::generateEnquiryNumber();
            $validated['enquiry_date'] = today();
            $validated['status'] = 'Pending';
            $validated['fee_status'] = 'Pending';
            $validated['fee_paid'] = 0;
            $validated['created_by'] = auth()->guard('admin')->id();

            \Log::info('Creating enquiry with data:', $validated);

            $enquiry = Enquiry::create($validated);

            \Log::info('Enquiry created successfully with ID: ' . $enquiry->id);

            return redirect()->route('admin.enquiry.view', $enquiry->id)
                ->with('success', 'Enquiry created successfully! Enquiry Number: ' . $enquiry->enquiry_number);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation Error:', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error creating enquiry: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Error creating enquiry: ' . $e->getMessage())->withInput();
        }
    }

    // View Enquiry Details
    public function view($id)
    {
        $enquiry = Enquiry::with(['class', 'session', 'student', 'createdBy', 'approvedBy'])->findOrFail($id);
        
        return view('admin.enquiry.view', compact('enquiry'));
    }

    // Edit Enquiry
    public function edit($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $classes = Classes::where('is_active', 'Active')->orderBy('class_numeric')->get();
        $sessions = Session::where('is_active', 'Active')->orderBy('id', 'desc')->get();
        
        return view('admin.enquiry.edit', compact('enquiry', 'classes', 'sessions'));
    }

    // Update Enquiry
    public function update(Request $request, $id)
    {
        $enquiry = Enquiry::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'email' => 'nullable|email',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'class_id' => 'required|exists:classes,id',
            'session_id' => 'required|exists:sessions,id',
            'previous_school' => 'nullable|string|max:255',
            'previous_class' => 'nullable|string|max:50',
            'previous_percentage' => 'nullable|numeric|min:0|max:100',
            'father_name' => 'required|string|max:255',
            'father_phone' => 'required|string|max:15',
            'father_occupation' => 'nullable|string|max:100',
            'mother_name' => 'required|string|max:255',
            'mother_phone' => 'nullable|string|max:15',
            'mother_occupation' => 'nullable|string|max:100',
            'annual_income' => 'nullable|numeric|min:0',
            'source' => 'nullable|string|max:50',
            'reference_by' => 'nullable|string|max:255',
            'registration_fee' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $enquiry->update($validated);

        return redirect()->route('admin.enquiry.view', $enquiry->id)
            ->with('success', 'Enquiry updated successfully!');
    }

    // Approve Enquiry
    public function approve($id)
    {
        $enquiry = Enquiry::findOrFail($id);

        if ($enquiry->status !== 'Pending') {
            return back()->with('error', 'Only pending enquiries can be approved!');
        }

        $enquiry->update([
            'status' => 'Approved',
            'approved_by' => auth()->guard('admin')->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Enquiry approved successfully! Student can now pay registration fee.');
    }

    // Reject Enquiry
    public function reject(Request $request, $id)
    {
        $enquiry = Enquiry::findOrFail($id);

        $request->validate([
            'remarks' => 'required|string',
        ]);

        $enquiry->update([
            'status' => 'Rejected',
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'Enquiry rejected successfully!');
    }

    // Fee Payment Form
    public function feePaymentForm($id)
    {
        $enquiry = Enquiry::findOrFail($id);

        if ($enquiry->status !== 'Approved') {
            return back()->with('error', 'Enquiry must be approved before fee payment!');
        }

        return view('admin.enquiry.fee-payment', compact('enquiry'));
    }

    // Process Fee Payment
    public function processFeePayment(Request $request, $id)
    {
        $enquiry = Enquiry::findOrFail($id);

        if ($enquiry->status !== 'Approved') {
            return back()->with('error', 'Enquiry must be approved before fee payment!');
        }

        $validated = $request->validate([
            'fee_paid' => 'required|numeric|min:0',
            'payment_mode' => 'required|in:Cash,Online,Cheque,Bank Transfer',
            'transaction_id' => 'nullable|string|max:100',
            'fee_paid_date' => 'required|date',
        ]);

        $totalPaid = $enquiry->fee_paid + $validated['fee_paid'];
        $feeStatus = $totalPaid >= $enquiry->registration_fee ? 'Paid' : 'Partial';

        $enquiry->update([
            'fee_paid' => $totalPaid,
            'fee_status' => $feeStatus,
            'payment_mode' => $validated['payment_mode'],
            'transaction_id' => $validated['transaction_id'],
            'fee_paid_date' => $validated['fee_paid_date'],
        ]);

        if ($feeStatus === 'Paid') {
            return redirect()->route('admin.enquiry.view', $enquiry->id)
                ->with('success', 'Fee payment completed! You can now convert to admission.');
        }

        return redirect()->route('admin.enquiry.view', $enquiry->id)
            ->with('success', 'Partial fee payment recorded. Balance: ₹' . $enquiry->balance_amount);
    }

    // Convert to Admission
    public function convertToAdmission($id)
    {
        $enquiry = Enquiry::findOrFail($id);

        if (!$enquiry->canConvertToAdmission()) {
            return back()->with('error', 'Enquiry must be approved and fee must be paid before conversion!');
        }

        if ($enquiry->status === 'Converted') {
            return back()->with('error', 'This enquiry is already converted to admission!');
        }

        DB::beginTransaction();
        try {
            // Generate admission number
            $admissionNumber = $this->generateAdmissionNumber();

            // Generate username and password
            $username = strtolower($enquiry->first_name) . rand(100, 999);
            $password = 'student@' . rand(1000, 9999);

            // Create student
            $student = Student::create([
                'admission_no' => $admissionNumber,
                'admission_date' => today(),
                'first_name' => $enquiry->first_name,
                'last_name' => $enquiry->last_name,
                'date_of_birth' => $enquiry->date_of_birth,
                'gender' => $enquiry->gender,
                'email' => $enquiry->email,
                'phone' => $enquiry->phone,
                'address' => $enquiry->address,
                'city' => $enquiry->city,
                'state' => $enquiry->state,
                'pincode' => $enquiry->pincode,
                'class_id' => $enquiry->class_id,
                'section_id' => $enquiry->class_id,
                'guardian_phone' =>$enquiry->guardian_phone,
                'session_id' => $enquiry->session_id,
                'father_name' => $enquiry->father_name,
                'father_phone' => $enquiry->father_phone,
                'father_occupation' => $enquiry->father_occupation,
                'mother_name' => $enquiry->mother_name,
                'mother_phone' => $enquiry->mother_phone,
                'mother_occupation' => $enquiry->mother_occupation,
                'annual_income' => $enquiry->annual_income,
                'previous_school' => $enquiry->previous_school,
                'username' => $username,
                'password' => Hash::make($password),
                'status' => 'Active',
            ]);

            // Update enquiry
            $enquiry->update([
                'status' => 'Converted',
                'student_id' => $student->id,
                'admission_number' => $admissionNumber,
                'admission_date' => today(),
            ]);

            DB::commit();

            return redirect()->route('admin.enquiry.view', $enquiry->id)
                ->with('success', 'Enquiry converted to admission successfully!')
                ->with('admission_details', [
                    'admission_number' => $admissionNumber,
                    'username' => $username,
                    'password' => $password,
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error converting to admission: ' . $e->getMessage());
        }
    }

    // Generate Admission Number
    private function generateAdmissionNumber()
    {
        $year = date('Y');
        $lastStudent = Student::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastStudent && $lastStudent->admission_number) {
            $lastNumber = intval(substr($lastStudent->admission_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'ADM' . $year . '' . $newNumber;
    }

    // Delete Enquiry
    public function delete($id)
    {
        $enquiry = Enquiry::findOrFail($id);

        if ($enquiry->status === 'Converted') {
            return back()->with('error', 'Cannot delete converted enquiry!');
        }

        $enquiry->delete();

        return redirect()->route('admin.enquiry.list')
            ->with('success', 'Enquiry deleted successfully!');
    }

    // Follow-up
    public function followUp(Request $request, $id)
    {
        $enquiry = Enquiry::findOrFail($id);

        $validated = $request->validate([
            'follow_up_date' => 'required|date',
            'follow_up_notes' => 'required|string',
        ]);

        $enquiry->update($validated);

        return back()->with('success', 'Follow-up scheduled successfully!');
    }
}
