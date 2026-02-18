<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'user_type' => 'required|in:student,teacher,admin'
        ]);

        $credentials = $request->only('username', 'password');
        $userType = $request->user_type;

        if ($userType === 'student') {
            $student = Student::where('username', $credentials['username'])->first();
            
            if ($student && Hash::check($credentials['password'], $student->password)) {
                Auth::guard('student')->login($student);
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful!',
                    'redirect' => route('student.dashboard')
                ]);
            }
        } elseif ($userType === 'teacher') {
            $teacher = Teacher::where('username', $credentials['username'])->first();
            
            if ($teacher && Hash::check($credentials['password'], $teacher->password)) {
                Auth::guard('teacher')->login($teacher);
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful!',
                    'redirect' => route('teacher.dashboard')
                ]);
            }
        } elseif ($userType === 'admin') {
            $admin = Admin::where('username', $credentials['username'])->first();
            
            if ($admin && Hash::check($credentials['password'], $admin->password)) {
                Auth::guard('admin')->login($admin);
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful!',
                    'redirect' => route('admin.dashboard')
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials!'
        ], 401);
    }

    public function logout(Request $request)
    {
        if (Auth::guard('student')->check()) {
            Auth::guard('student')->logout();
        } elseif (Auth::guard('teacher')->check()) {
            Auth::guard('teacher')->logout();
        } elseif (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Check if it's an AJAX request
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully!',
                'redirect' => route('login')
            ]);
        }

        // Regular form submission
        return redirect()->route('login')->with('success', 'Logged out successfully!');
    }
}
