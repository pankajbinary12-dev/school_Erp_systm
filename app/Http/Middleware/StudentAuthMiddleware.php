<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('student_id')) {
            return redirect()->route('student.login')->withErrors(['error' => 'Please login to continue']);
        }

        return $next($request);
    }
}
