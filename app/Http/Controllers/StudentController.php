<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function dashboard()
    {
        $student = auth()->guard('student')->user();
        return view('student.dashboard', compact('student'));
    }

    public function index()
    {
        return view('admin.students.index');
    }

    public function getData(Request $request)
    {
        $query = Student::with(['class', 'section', 'session']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('admission_no', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('roll_no', 'like', "%{$search}%");
            });
        }

        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('section_id') && $request->section_id != '') {
            $query->where('section_id', $request->section_id);
        }

        $students = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'admission_no' => 'required|unique:students',
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'father_name' => 'required|max:100',
            'mother_name' => 'required|max:100',
            'guardian_phone' => 'required|max:20',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'session_id' => 'required|exists:sessions,id',
            'username' => 'required|unique:students|max:50',
            'password' => 'required|min:6',
            'admission_date' => 'required|date',
            'photo' => 'nullable|image|max:2048'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = 'Active';

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }
         
        $student = Student::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student admitted successfully!',
            'data' => $student->load(['class', 'section', 'session'])
        ]);
    }

    public function show($id)
    {
        $student = Student::with(['class', 'section', 'session'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'admission_no' => 'required|unique:students,admission_no,' . $id,
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'father_name' => 'required|max:100',
            'mother_name' => 'required|max:100',
            'guardian_phone' => 'required|max:20',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'session_id' => 'required|exists:sessions,id',
            'username' => 'required|unique:students,username,' . $id . '|max:50',
            'photo' => 'nullable|image|max:2048'
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully!',
            'data' => $student->load(['class', 'section', 'session'])
        ]);
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }
        
        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully!'
        ]);
    }

    public function promote(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'new_class_id' => 'required|exists:classes,id',
            'new_section_id' => 'required|exists:sections,id',
            'new_session_id' => 'required|exists:sessions,id'
        ]);

        Student::whereIn('id', $validated['student_ids'])->update([
            'class_id' => $validated['new_class_id'],
            'section_id' => $validated['new_section_id'],
            'session_id' => $validated['new_session_id']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Students promoted successfully!'
        ]);
    }
}
