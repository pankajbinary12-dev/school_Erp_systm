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
        $user = null;
        $userId = null;

        if ($userType === 'student') {
            $student = Student::where('username', $credentials['username'])->first();
            
            if ($student && Hash::check($credentials['password'], $student->password)) {
                Auth::guard('student')->login($student);
                
                // Set session for StudentAuthMiddleware
                session(['student_id' => $student->id]);
                session(['student_name' => $student->first_name . ' ' . $student->last_name]);
                
                $user = $student;
                $userId = $student->id;
                $this->logLogin('student', $userId, $credentials['username'], 'success', $request);
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
                $user = $teacher;
                $userId = $teacher->id;
                $this->logLogin('teacher', $userId, $credentials['username'], 'success', $request);
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
                $user = $admin;
                $userId = $admin->id;
                $this->logLogin('admin', $userId, $credentials['username'], 'success', $request);
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful!',
                    'redirect' => route('admin.dashboard')
                ]);
            }
        }

        // Log failed attempt
        $this->logLogin($userType, 0, $credentials['username'], 'failed', $request);

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials!'
        ], 401);
    }

    private function logLogin($userType, $userId, $username, $status, $request)
    {
        $userAgent = $request->header('User-Agent');
        
        // Detect device type
        $deviceType = 'Desktop';
        if (preg_match('/mobile/i', $userAgent)) {
            $deviceType = 'Mobile';
        } elseif (preg_match('/tablet/i', $userAgent)) {
            $deviceType = 'Tablet';
        }

        // Detect browser
        $browser = 'Unknown';
        if (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/MSIE|Trident/i', $userAgent)) {
            $browser = 'Internet Explorer';
        }

        // Detect OS
        $os = 'Unknown';
        if (preg_match('/Windows/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/Mac/i', $userAgent)) {
            $os = 'Mac OS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iOS|iPhone|iPad/i', $userAgent)) {
            $os = 'iOS';
        }

        \DB::table('login_logs')->insert([
            'user_type' => $userType,
            'user_id' => $userId,
            'username' => $username,
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'device_type' => $deviceType,
            'browser' => $browser,
            'os' => $os,
            'login_at' => now(),
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now()
        ]);
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
