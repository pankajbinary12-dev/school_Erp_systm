<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    public function dashboard()
    {
        $teacher = auth()->guard('teacher')->user();
        return view('teacher.dashboard', compact('teacher'));
    }

    public function index()
    {
        return view('admin.teachers.index');
    }

    public function getData(Request $request)
    {
        $query = Teacher::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $teachers = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $teachers
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|unique:teachers',
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'email' => 'required|email|unique:teachers',
            'phone' => 'required|max:20',
            'qualification' => 'required|max:200',
            'joining_date' => 'required|date',
            'username' => 'required|unique:teachers|max:50',
            'password' => 'required|min:6',
            'photo' => 'nullable|image|max:2048'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = 'Active';

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('teachers', 'public');
        }

        $teacher = Teacher::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Teacher added successfully!',
            'data' => $teacher
        ]);
    }

    public function show($id)
    {
        $teacher = Teacher::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $teacher
        ]);
    }

    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'required|unique:teachers,employee_id,' . $id,
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'email' => 'required|email|unique:teachers,email,' . $id,
            'phone' => 'required|max:20',
            'qualification' => 'required|max:200',
            'joining_date' => 'required|date',
            'username' => 'required|unique:teachers,username,' . $id . '|max:50',
            'photo' => 'nullable|image|max:2048'
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }
            $validated['photo'] = $request->file('photo')->store('teachers', 'public');
        }

        $teacher->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Teacher updated successfully!',
            'data' => $teacher
        ]);
    }

    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        
        if ($teacher->photo) {
            Storage::disk('public')->delete($teacher->photo);
        }
        
        $teacher->delete();

        return response()->json([
            'success' => true,
            'message' => 'Teacher deleted successfully!'
        ]);
    }
}
