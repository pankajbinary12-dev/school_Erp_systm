<?php

use App\Http\Controllers\TimetableController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RolePermissionController;

// Public Routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Public Certificate Verification Routes
Route::get('/verify-certificate', [\App\Http\Controllers\CertificateController::class, 'verify'])->name('certificates.verify.form');
Route::post('/verify-certificate', [\App\Http\Controllers\CertificateController::class, 'verify'])->name('certificates.verify.check');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Student Dashboard Routes (New)
Route::prefix('student')->name('student.')->group(function () {
    // Login Routes - Redirect to main login
    Route::get('/login', function() {
        return redirect()->route('login');
    })->name('login');
    Route::post('/login', [\App\Http\Controllers\StudentDashboardController::class, 'login'])->name('login.post');
    
    // Protected Routes
    Route::middleware(['student.auth'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\StudentDashboardController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [\App\Http\Controllers\StudentDashboardController::class, 'logout'])->name('logout');
        
        // Profile
        Route::get('/profile', [\App\Http\Controllers\StudentDashboardController::class, 'profile'])->name('profile');
        Route::post('/profile/update', [\App\Http\Controllers\StudentDashboardController::class, 'updateProfile'])->name('profile.update');
        
        // Attendance
        Route::get('/attendance', [\App\Http\Controllers\StudentDashboardController::class, 'attendance'])->name('attendance');
        
        // Subjects
        Route::get('/subjects', [\App\Http\Controllers\StudentDashboardController::class, 'subjects'])->name('subjects');
        
        // Assignments
        Route::get('/assignments', [\App\Http\Controllers\StudentDashboardController::class, 'assignments'])->name('assignments');
        Route::post('/assignments/{id}/submit', [\App\Http\Controllers\StudentDashboardController::class, 'submitAssignment'])->name('assignments.submit');
        
        // Results
        Route::get('/results', [\App\Http\Controllers\StudentDashboardController::class, 'results'])->name('results');
        Route::get('/results/{id}/download', [\App\Http\Controllers\StudentDashboardController::class, 'downloadReportCard'])->name('results.download');
        
        // Fees
        Route::get('/fees', [\App\Http\Controllers\StudentDashboardController::class, 'fees'])->name('fees');
        Route::get('/fees/{id}/receipt', [\App\Http\Controllers\StudentDashboardController::class, 'downloadFeeReceipt'])->name('fees.receipt');
        
        // Timetable
        // Route::get('/timetable', [\App\Http\Controllers\StudentDashboardController::class, 'timetable'])->name('timetable');
        
        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\StudentDashboardController::class, 'notifications'])->name('notifications');
    });
});

// Teacher Routes
Route::middleware(['auth:teacher'])->prefix('teacher')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
    // Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    
    // Attendance
    Route::get('/my-attendance', [TeacherController::class, 'myAttendance'])->name('teacher.my.attendance');
    Route::get('/my-attendance/data', [TeacherController::class, 'getMyAttendanceData'])->name('teacher.my.attendance.data');
    Route::post('/my-attendance/mark', [TeacherController::class, 'markMyAttendance'])->name('teacher.my.attendance.mark');
    Route::get('/my-attendance/today', [TeacherController::class, 'getTodayAttendance'])->name('teacher.my.attendance.today');
    
    // Profile
    Route::get('/profile', [TeacherController::class, 'myProfile'])->name('teacher.profile');
    Route::post('/profile/update', [TeacherController::class, 'updateProfile'])->name('teacher.profile.update');
    
    // Students
    Route::get('/students', [TeacherController::class, 'myStudents'])->name('teacher.students');
    Route::get('/students/data', [TeacherController::class, 'getStudentsData'])->name('teacher.students.data');
    
    // Helper Routes
    Route::get('/get-sections/{classId}', [TeacherController::class, 'getSectionsByClass'])->name('teacher.get.sections');
    
    // Subjects
    Route::get('/subjects', [TeacherController::class, 'mySubjects'])->name('teacher.subjects');
    
    // Assignments
    Route::get('/assignments', [TeacherController::class, 'assignments'])->name('teacher.assignments');
    Route::get('/assignments/data', [TeacherController::class, 'getAssignmentsData'])->name('teacher.assignments.data');
    Route::get('/assignments/create', [TeacherController::class, 'createAssignment'])->name('teacher.assignments.create');
    Route::post('/assignments/store', [TeacherController::class, 'storeAssignment'])->name('teacher.assignments.store');
    Route::get('/assignments/{id}', [TeacherController::class, 'viewAssignment'])->name('teacher.assignments.view');
    Route::post('/assignments/submissions/{id}/grade', [TeacherController::class, 'gradeSubmission'])->name('teacher.assignments.grade');
    Route::delete('/assignments/{id}', [TeacherController::class, 'deleteAssignment'])->name('teacher.assignments.delete');
    
    // Student Attendance Module
    Route::get('/student-attendance', [TeacherController::class, 'studentAttendance'])->name('teacher.student.attendance');
    Route::post('/student-attendance/start-session', [TeacherController::class, 'startAttendanceSession'])->name('teacher.student.attendance.start');
    Route::get('/student-attendance/get-students/{sessionId}', [TeacherController::class, 'getSessionStudents'])->name('teacher.student.attendance.students');
    Route::post('/student-attendance/mark', [TeacherController::class, 'markStudentAttendance'])->name('teacher.student.attendance.mark');
    Route::post('/student-attendance/mark-all', [TeacherController::class, 'markAllPresent'])->name('teacher.student.attendance.mark.all');
    Route::post('/student-attendance/end-session/{sessionId}', [TeacherController::class, 'endAttendanceSession'])->name('teacher.student.attendance.end');
    Route::get('/student-attendance/sessions', [TeacherController::class, 'getAttendanceSessions'])->name('teacher.student.attendance.sessions');
    Route::get('/student-attendance/report', [TeacherController::class, 'attendanceReport'])->name('teacher.student.attendance.report');
    Route::get('/student-attendance/report/data', [TeacherController::class, 'getAttendanceReportData'])->name('teacher.student.attendance.report.data');
    
    // Grade Book
    Route::get('/gradebook', function() {
        $teacher = auth()->guard('teacher')->user();
        return view('teacher.gradebook.index', compact('teacher'));
    })->name('teacher.gradebook');
});

// Admin Routes
Route::middleware(['auth:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Debug Routes
    Route::get('/debug/teachers', function() {
        $teachers = \App\Models\Teacher::select('id', 'first_name', 'last_name', 'email', 'username')->get();
        return response()->json([
            'count' => $teachers->count(),
            'teachers' => $teachers->toArray(),
            'note' => 'Default password is usually: password or teacher123'
        ]);
    });
    
    // Enquiry Management Routes
    Route::prefix('enquiry')->name('admin.enquiry.')->group(function () {
        Route::get('/test-data', function() {
            $classes = \App\Models\Classes::where('is_active', 'Active')->orderBy('class_numeric')->get();
            $sessions = \App\Models\Session::where('is_active', 'Active')->orderBy('id', 'desc')->get();
            return response()->json([
                'classes_count' => $classes->count(),
                'classes' => $classes->toArray(),
                'sessions_count' => $sessions->count(),
                'sessions' => $sessions->toArray(),
            ]);
        });
        
        Route::get('/test-form', function() {
            $classes = \App\Models\Classes::where('is_active', 'Active')->orderBy('class_numeric')->get();
            $sessions = \App\Models\Session::where('is_active', 'Active')->orderBy('id', 'desc')->get();
            return view('admin.enquiry.test-form', compact('classes', 'sessions'));
        });
        
        Route::get('/', [\App\Http\Controllers\EnquiryController::class, 'index'])->name('index');
        Route::get('/list', [\App\Http\Controllers\EnquiryController::class, 'list'])->name('list');
        Route::get('/create', [\App\Http\Controllers\EnquiryController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\EnquiryController::class, 'store'])->name('store');
        Route::get('/{id}/view', [\App\Http\Controllers\EnquiryController::class, 'view'])->name('view');
        Route::get('/{id}/edit', [\App\Http\Controllers\EnquiryController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [\App\Http\Controllers\EnquiryController::class, 'update'])->name('update');
        Route::post('/{id}/approve', [\App\Http\Controllers\EnquiryController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [\App\Http\Controllers\EnquiryController::class, 'reject'])->name('reject');
        Route::get('/{id}/fee-payment', [\App\Http\Controllers\EnquiryController::class, 'feePaymentForm'])->name('fee-payment');
        Route::post('/{id}/process-fee', [\App\Http\Controllers\EnquiryController::class, 'processFeePayment'])->name('process-fee');
        Route::post('/{id}/convert', [\App\Http\Controllers\EnquiryController::class, 'convertToAdmission'])->name('convert');
        Route::delete('/{id}/delete', [\App\Http\Controllers\EnquiryController::class, 'delete'])->name('delete');
        Route::post('/{id}/follow-up', [\App\Http\Controllers\EnquiryController::class, 'followUp'])->name('followup');
    });
    
    // API endpoints
    Route::get('/api/stats', [AdminController::class, 'getStats'])->name('admin.api.stats');
    Route::get('/api/recent-students', [AdminController::class, 'getRecentStudents'])->name('admin.api.recent.students');
    
    // Master Routes
    Route::get('/sessions', [MasterController::class, 'sessions'])->name('admin.sessions');
    Route::get('/sessions/data', [MasterController::class, 'getSessionsData'])->name('admin.sessions.data');
    Route::post('/sessions', [MasterController::class, 'storeSession'])->name('admin.sessions.store');
    Route::put('/sessions/{id}', [MasterController::class, 'updateSession'])->name('admin.sessions.update');
    Route::delete('/sessions/{id}', [MasterController::class, 'deleteSession'])->name('admin.sessions.delete');

    Route::get('/classes', [MasterController::class, 'classes'])->name('admin.classes');
    Route::get('/classes/data', [MasterController::class, 'getClassesData'])->name('admin.classes.data');
    Route::post('/classes', [MasterController::class, 'storeClass'])->name('admin.classes.store');
    Route::put('/classes/{id}', [MasterController::class, 'updateClass'])->name('admin.classes.update');
    Route::delete('/classes/{id}', [MasterController::class, 'deleteClass'])->name('admin.classes.delete');

    Route::get('/sections', [MasterController::class, 'sections'])->name('admin.sections');
    Route::get('/sections/data', [MasterController::class, 'getSectionsData'])->name('admin.sections.data');
    Route::post('/sections', [MasterController::class, 'storeSection'])->name('admin.sections.store');
    Route::put('/sections/{id}', [MasterController::class, 'updateSection'])->name('admin.sections.update');
    Route::delete('/sections/{id}', [MasterController::class, 'deleteSection'])->name('admin.sections.delete');

    Route::get('/subjects', [MasterController::class, 'subjects'])->name('admin.subjects');
    Route::get('/subjects/data', [MasterController::class, 'getSubjectsData'])->name('admin.subjects.data');
    Route::post('/subjects', [MasterController::class, 'storeSubject'])->name('admin.subjects.store');
    Route::put('/subjects/{id}', [MasterController::class, 'updateSubject'])->name('admin.subjects.update');
    Route::delete('/subjects/{id}', [MasterController::class, 'deleteSubject'])->name('admin.subjects.delete');

    // Helper Routes
    Route::get('/get-active-classes', [MasterController::class, 'getActiveClasses'])->name('admin.get.classes');
    Route::get('/get-sections/{classId}', [MasterController::class, 'getSectionsByClass'])->name('admin.get.sections');
    Route::get('/get-active-sessions', [MasterController::class, 'getActiveSessions'])->name('admin.get.sessions');
    
    // Class-Section Assignment
    Route::get('/class-sections', [MasterController::class, 'classSections'])->name('admin.class.sections');
    
    // Direct test route
    Route::get('/class-sections-direct', function() {
        return '<h1>TEST WORKING</h1><p>If you see this, routing works!</p>';
    });
    
    Route::get('/class-sections-with-data', function() {
        $classes = \App\Models\Classes::where('is_active', true)->orderBy('class_numeric')->get();
        return view('admin.masters.class-sections-simple', compact('classes'));
    });
    
    Route::get('/class-sections/get', [MasterController::class, 'getClassSections'])->name('admin.class.sections.get');
    Route::post('/class-sections/assign', [MasterController::class, 'assignSectionsToClass'])->name('admin.class.sections.assign');
    Route::post('/sections/quick-add', [MasterController::class, 'quickAddSections'])->name('admin.sections.quick-add');
    
    // Debug route
    Route::get('/debug-admissions', function() {
        try {
            $count = \App\Models\StudentAdmission::count();
            $data = \App\Models\StudentAdmission::all();
            return response()->json([
                'success' => true,
                'count' => $count,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    });

    // Student Routes - Specific routes first, then dynamic {id} routes
    Route::get('/students', [AdminController::class, 'students'])->name('admin.students');
    Route::get('/students/all', [AdminController::class, 'allStudents'])->name('admin.students.all');
    Route::get('/students/add', [AdminController::class, 'addStudent'])->name('admin.students.add');
    Route::get('/students/data', [StudentController::class, 'getData'])->name('admin.students.data');
    Route::post('/students', [StudentController::class, 'store'])->name('admin.students.store');
    Route::get('/students/admission', [AdminController::class, 'studentAdmission'])->name('admin.students.admission');
    Route::get('/students/admission/generate-number', [AdminController::class, 'generateAdmissionNumber'])->name('admin.students.admission.generate-number');
    Route::post('/students/admission', [AdminController::class, 'storeAdmission'])->name('admin.students.admission.store');
    Route::get('/students/admissions', [AdminController::class, 'allAdmissions'])->name('admin.students.admissions');
    Route::get('/students/admissions/data', [AdminController::class, 'getAdmissionsData'])->name('admin.students.admissions.data');
    Route::get('/students/admissions/trash', [AdminController::class, 'trashedAdmissions'])->name('admin.students.admissions.trash');
    Route::get('/students/admissions/trash/data', [AdminController::class, 'getTrashedAdmissionsData'])->name('admin.students.admissions.trash.data');
    Route::get('/students/admissions/{id}/edit', [AdminController::class, 'editAdmission'])->name('admin.students.admissions.edit');
    Route::put('/students/admissions/{id}', [AdminController::class, 'updateAdmission'])->name('admin.students.admissions.update');
    Route::delete('/students/admissions/{id}', [AdminController::class, 'deleteAdmission'])->name('admin.students.admissions.delete');
    Route::post('/students/admissions/{id}/restore', [AdminController::class, 'restoreAdmission'])->name('admin.students.admissions.restore');
    Route::get('/students/strength', [AdminController::class, 'studentStrength'])->name('admin.students.strength');
    Route::get('/students/promotion', [AdminController::class, 'studentPromotion'])->name('admin.students.promotion');
    Route::get('/students/promotion/search', [AdminController::class, 'searchStudentsForPromotion'])->name('admin.students.promotion.search');
    Route::post('/students/promotion/promote', [AdminController::class, 'promoteStudents'])->name('admin.students.promotion.promote');
    Route::get('/students/details', [AdminController::class, 'studentDetails'])->name('admin.students.details');
    Route::get('/students/edit/{id}', [AdminController::class, 'editStudent'])->name('admin.students.edit');
    Route::get('/students/view/{id}', [AdminController::class, 'viewStudent'])->name('admin.students.view');
    // Dynamic {id} routes at the end
    Route::get('/students/{id}', [StudentController::class, 'show'])->name('admin.students.show');
    Route::put('/students/{id}', [StudentController::class, 'update'])->name('admin.students.update');
    Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('admin.students.destroy');

    // Teacher Routes
    Route::get('/teachers', [AdminController::class, 'teachers'])->name('admin.teachers');
    Route::get('/teachers/data', [AdminController::class, 'getTeachersData'])->name('admin.teachers.data');
    Route::get('/teachers/{id}/assign-classes', [AdminController::class, 'assignClasses'])->name('admin.teachers.assign.classes');
    Route::post('/teachers/{id}/assign-classes', [AdminController::class, 'storeAssignedClasses'])->name('admin.teachers.assign.classes.store');
    Route::get('/teachers/{id}/assigned-classes', [AdminController::class, 'getAssignedClasses'])->name('admin.teachers.assigned.classes');
    Route::delete('/teachers/assignments/{id}', [AdminController::class, 'deleteAssignment'])->name('admin.teachers.assignments.delete');

    // Staff Routes
    Route::get('/staff/all', [\App\Http\Controllers\StaffController::class, 'index'])->name('admin.staff.all');
    Route::get('/staff/data', [\App\Http\Controllers\StaffController::class, 'getStaffData'])->name('admin.staff.data');
    Route::get('/staff/add', [\App\Http\Controllers\StaffController::class, 'create'])->name('admin.staff.add');
    Route::post('/staff', [\App\Http\Controllers\StaffController::class, 'store'])->name('admin.staff.store');
    Route::get('/staff/{id}/edit', [\App\Http\Controllers\StaffController::class, 'edit'])->name('admin.staff.edit');
    Route::put('/staff/{id}', [\App\Http\Controllers\StaffController::class, 'update'])->name('admin.staff.update');
    Route::delete('/staff/{id}', [\App\Http\Controllers\StaffController::class, 'destroy'])->name('admin.staff.delete');
    Route::post('/staff/{id}/restore', [\App\Http\Controllers\StaffController::class, 'restore'])->name('admin.staff.restore');
    Route::get('/staff/attendance', [\App\Http\Controllers\StaffController::class, 'attendance'])->name('admin.staff.attendance');
    Route::get('/staff/attendance/data', [\App\Http\Controllers\StaffController::class, 'getAttendanceData'])->name('admin.staff.attendance.data');
    Route::post('/staff/attendance/mark', [\App\Http\Controllers\StaffController::class, 'markAttendance'])->name('admin.staff.attendance.mark');
    Route::get('/staff/leave', [\App\Http\Controllers\StaffController::class, 'leave'])->name('admin.staff.leave');
    Route::get('/staff/leave/data', [\App\Http\Controllers\StaffController::class, 'getLeaveData'])->name('admin.staff.leave.data');
    Route::post('/staff/leave/apply', [\App\Http\Controllers\StaffController::class, 'applyLeave'])->name('admin.staff.leave.apply');
    Route::put('/staff/leave/{id}/status', [\App\Http\Controllers\StaffController::class, 'updateLeaveStatus'])->name('admin.staff.leave.status');

    // Attendance Routes
    Route::get('/attendance/test-direct', function() {
        return '<h1 style="color: red; padding: 50px;">DIRECT ROUTE TEST - If you see this, routing works!</h1>';
    });
    
    Route::get('/attendance/student', [\App\Http\Controllers\AttendanceController::class, 'studentAttendance'])->name('admin.attendance.student');
    Route::get('/attendance/students/load', [\App\Http\Controllers\AttendanceController::class, 'loadStudents'])->name('admin.attendance.students.load');
    Route::post('/attendance/students/save', [\App\Http\Controllers\AttendanceController::class, 'saveAttendance'])->name('admin.attendance.students.save');
    Route::post('/attendance/biometric/scan', [\App\Http\Controllers\AttendanceController::class, 'biometricScan'])->name('admin.attendance.biometric.scan');
    Route::get('/attendance/report', [\App\Http\Controllers\AttendanceController::class, 'getReport'])->name('admin.attendance.report');
    Route::get('/attendance/export', [\App\Http\Controllers\AttendanceController::class, 'export'])->name('admin.attendance.export');
    
    // Staff Attendance Routes
    Route::get('/attendance/staff', [\App\Http\Controllers\StaffController::class, 'attendance'])->name('admin.attendance.staff');
    Route::post('/attendance/staff/mark', [\App\Http\Controllers\StaffController::class, 'markAttendance'])->name('admin.attendance.staff.mark');
    Route::post('/attendance/staff/bulk', [\App\Http\Controllers\StaffController::class, 'bulkMarkAttendance'])->name('admin.attendance.staff.bulk');
    Route::get('/attendance/staff/by-date', [\App\Http\Controllers\StaffController::class, 'getAttendanceByDate'])->name('admin.attendance.staff.by-date');
    Route::get('/attendance/staff/monthly-report', [\App\Http\Controllers\StaffController::class, 'monthlyReport'])->name('admin.attendance.staff.monthly');
    Route::get('/attendance/staff/monthly-data', [\App\Http\Controllers\StaffController::class, 'getMonthlyReportData'])->name('admin.attendance.staff.monthly.data');
    Route::get('/attendance/staff/export', [\App\Http\Controllers\StaffController::class, 'exportMonthlyReport'])->name('admin.attendance.staff.export');
    
    // Admin Manual Attendance
    Route::post('/attendance/staff/admin-mark', [\App\Http\Controllers\StaffController::class, 'adminMarkAttendance'])->name('admin.attendance.staff.admin-mark');
    
    // Biometric Attendance Routes
    Route::post('/attendance/biometric/process', [\App\Http\Controllers\StaffController::class, 'processBiometric'])->name('admin.attendance.biometric.process');
    Route::post('/attendance/biometric/sync/{deviceId}', [\App\Http\Controllers\StaffController::class, 'syncBiometricDevice'])->name('admin.attendance.biometric.sync');
    Route::get('/attendance/biometric/test/{deviceId}', [\App\Http\Controllers\StaffController::class, 'testBiometricDevice'])->name('admin.attendance.biometric.test');
    
    // Biometric Device Management Routes
    Route::get('/attendance/biometric/devices', [\App\Http\Controllers\StaffController::class, 'biometricDevices'])->name('admin.attendance.biometric.devices');
    Route::get('/attendance/biometric/list', [\App\Http\Controllers\StaffController::class, 'listBiometricDevices'])->name('admin.attendance.biometric.list');
    Route::post('/attendance/biometric/store', [\App\Http\Controllers\StaffController::class, 'storeBiometricDevice'])->name('admin.attendance.biometric.store');
    Route::delete('/attendance/biometric/delete/{id}', [\App\Http\Controllers\StaffController::class, 'deleteBiometricDevice'])->name('admin.attendance.biometric.delete');
    Route::get('/attendance/biometric/test-simulator', [\App\Http\Controllers\StaffController::class, 'biometricTestSimulator'])->name('admin.attendance.biometric.test.simulator');

    // Academic Routes
     Route::get('timetable', [TimetableController::class, 'index'])->name('admin.timetables');
    Route::prefix('admin')->name('admin.')->group(function() {
    Route::resource('timetable', TimetableController::class);
    
    // Custom routes
    Route::post('timetable/check-conflict', [TimetableController::class, 'checkConflict'])->name('timetable.check-conflict');
    Route::get('timetable/filter', [TimetableController::class, 'filter'])->name('timetable.filter');
   });

    // Examination Routes
    Route::get('/exams', [AdminController::class, 'exams'])->name('admin.exams');
    Route::get('/exams/schedule', [AdminController::class, 'examSchedule'])->name('admin.exams.schedule');
    Route::get('/exams/marks', [AdminController::class, 'examMarks'])->name('admin.exams.marks');
    Route::get('/exams/results', [AdminController::class, 'examResults'])->name('admin.exams.results');

    // Library Routes
    Route::get('/library', [AdminController::class, 'library'])->name('admin.library');
    Route::get('/library/categories', [\App\Http\Controllers\LibraryController::class, 'categories'])->name('admin.library.categories');
    Route::get('/library/categories/data', [\App\Http\Controllers\LibraryController::class, 'getCategoriesData'])->name('admin.library.categories.data');
    Route::post('/library/categories', [\App\Http\Controllers\LibraryController::class, 'storeCategory'])->name('admin.library.categories.store');
    Route::put('/library/categories/{id}', [\App\Http\Controllers\LibraryController::class, 'updateCategory'])->name('admin.library.categories.update');
    Route::delete('/library/categories/{id}', [\App\Http\Controllers\LibraryController::class, 'deleteCategory'])->name('admin.library.categories.delete');
    
    Route::get('/library/books', [\App\Http\Controllers\LibraryController::class, 'books'])->name('admin.library.books');
    Route::get('/library/books/data', [\App\Http\Controllers\LibraryController::class, 'getBooksData'])->name('admin.library.books.data');
    Route::post('/library/books', [\App\Http\Controllers\LibraryController::class, 'storeBook'])->name('admin.library.books.store');
    Route::put('/library/books/{id}', [\App\Http\Controllers\LibraryController::class, 'updateBook'])->name('admin.library.books.update');
    Route::delete('/library/books/{id}', [\App\Http\Controllers\LibraryController::class, 'deleteBook'])->name('admin.library.books.delete');
    
    Route::get('/library/issue', [\App\Http\Controllers\LibraryController::class, 'issue'])->name('admin.library.issue');
    Route::get('/library/issue/data', [\App\Http\Controllers\LibraryController::class, 'getIssuesData'])->name('admin.library.issue.data');
    Route::get('/library/members', [\App\Http\Controllers\LibraryController::class, 'getMembers'])->name('admin.library.members');
    Route::get('/library/available-books', [\App\Http\Controllers\LibraryController::class, 'getAvailableBooks'])->name('admin.library.available.books');
    Route::post('/library/issue', [\App\Http\Controllers\LibraryController::class, 'issueBook'])->name('admin.library.issue.store');
    Route::put('/library/return/{id}', [\App\Http\Controllers\LibraryController::class, 'returnBook'])->name('admin.library.return');

    // Fees Routes
    Route::get('/fees/collect', [AdminController::class, 'collectFees'])->name('admin.fees.collect');
    Route::get('/fees/structure', [AdminController::class, 'feeStructure'])->name('admin.fees.structure');
    Route::get('/fees/report', [AdminController::class, 'feeReport'])->name('admin.fees.report');

    // Settings Routes
    Route::get('/settings/general', [AdminController::class, 'generalSettings'])->name('admin.settings.general');
    Route::get('/settings/school', [AdminController::class, 'schoolInfo'])->name('admin.settings.school');
    Route::post('/settings/school', [AdminController::class, 'updateSchoolInfo'])->name('admin.settings.school.update');
    Route::get('/settings/system-guide', function() {
        return view('admin.settings.system-guide');
    })->name('admin.settings.system.guide');
    Route::get('/settings/users', [AdminController::class, 'userManagement'])->name('admin.settings.users');
    Route::get('/settings/users/data', [AdminController::class, 'getUsersData'])->name('admin.settings.users.data');
    Route::get('/settings/users/login-history', [AdminController::class, 'getLoginHistory'])->name('admin.settings.users.login.history');
    Route::post('/settings/users/reset-password', [AdminController::class, 'resetUserPassword'])->name('admin.settings.users.reset.password');
    Route::get('/settings/users/export-history', [AdminController::class, 'exportLoginHistory'])->name('admin.settings.users.export.history');
    
    // Admin Profile Routes
    Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::post('/profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::post('/profile/change-password', [AdminController::class, 'changePassword'])->name('admin.profile.change.password');
    
    // Roles & Permissions Routes
    Route::get('/settings/roles', [RolePermissionController::class, 'rolesIndex'])->name('admin.settings.roles');
    Route::post('/settings/roles', [RolePermissionController::class, 'storeRole'])->name('admin.settings.roles.store');
    Route::get('/settings/roles/{id}/edit', [RolePermissionController::class, 'editRole'])->name('admin.settings.roles.edit');
    Route::put('/settings/roles/{id}', [RolePermissionController::class, 'updateRole'])->name('admin.settings.roles.update');
    Route::delete('/settings/roles/{id}', [RolePermissionController::class, 'deleteRole'])->name('admin.settings.roles.delete');
    
    Route::get('/settings/permissions', [RolePermissionController::class, 'permissionsIndex'])->name('admin.settings.permissions');
    Route::post('/settings/permissions', [RolePermissionController::class, 'storePermission'])->name('admin.settings.permissions.store');
    Route::get('/settings/permissions/{id}/edit', [RolePermissionController::class, 'editPermission'])->name('admin.settings.permissions.edit');
    Route::put('/settings/permissions/{id}', [RolePermissionController::class, 'updatePermission'])->name('admin.settings.permissions.update');
    Route::delete('/settings/permissions/{id}', [RolePermissionController::class, 'deletePermission'])->name('admin.settings.permissions.delete');
    
    Route::get('/settings/assign-permissions', [RolePermissionController::class, 'assignPermissions'])->name('admin.settings.assign.permissions');
    Route::post('/settings/roles/{id}/permissions', [RolePermissionController::class, 'updateRolePermissions'])->name('admin.settings.roles.permissions.update');
    
    Route::get('/settings/assign-roles', [RolePermissionController::class, 'assignRoles'])->name('admin.settings.assign.roles');
    Route::post('/settings/admins/{id}/roles', [RolePermissionController::class, 'assignRoleToAdmin'])->name('admin.settings.admins.roles');
    Route::post('/settings/teachers/{id}/roles', [RolePermissionController::class, 'assignRoleToTeacher'])->name('admin.settings.teachers.roles');
    Route::post('/settings/staff/{id}/roles', [RolePermissionController::class, 'assignRoleToStaff'])->name('admin.settings.staff.roles');

    // ID Card Routes
    Route::get('/id-cards', [AdminController::class, 'idCards'])->name('admin.id.cards');
    Route::get('/id-cards/templates', [AdminController::class, 'idCardTemplates'])->name('admin.id.cards.templates');
    Route::get('/id-cards/templates/data', [AdminController::class, 'getTemplatesData'])->name('admin.id.cards.templates.data');
    Route::post('/id-cards/templates', [AdminController::class, 'storeTemplate'])->name('admin.id.cards.templates.store');
    Route::put('/id-cards/templates/{id}', [AdminController::class, 'updateTemplate'])->name('admin.id.cards.templates.update');
    Route::delete('/id-cards/templates/{id}', [AdminController::class, 'deleteTemplate'])->name('admin.id.cards.templates.delete');
    Route::post('/id-cards/generate', [AdminController::class, 'generateIdCard'])->name('admin.id.cards.generate');

    // Calendar Routes
    Route::get('/calendar', [AdminController::class, 'calendar'])->name('admin.calendar');
    Route::get('/calendar/events', [AdminController::class, 'getCalendarEvents'])->name('admin.calendar.events');
    
    // Certificate Routes
    Route::get('/certificates/students-by-class', [\App\Http\Controllers\CertificateController::class, 'getStudentsByClass'])->name('admin.certificates.students-by-class');
    Route::get('/certificates/bulk/create', [\App\Http\Controllers\CertificateController::class, 'bulkCreate'])->name('admin.certificates.bulk.create');
    Route::post('/certificates/bulk/store', [\App\Http\Controllers\CertificateController::class, 'bulkStore'])->name('admin.certificates.bulk.store');
    Route::get('/certificates', [\App\Http\Controllers\CertificateController::class, 'index'])->name('admin.certificates.index');
    Route::get('/certificates/create', [\App\Http\Controllers\CertificateController::class, 'create'])->name('admin.certificates.create');
    Route::post('/certificates/store', [\App\Http\Controllers\CertificateController::class, 'store'])->name('admin.certificates.store');
    Route::get('/certificates/{id}/preview', [\App\Http\Controllers\CertificateController::class, 'preview'])->name('admin.certificates.preview');
    Route::get('/certificates/{id}/download', [\App\Http\Controllers\CertificateController::class, 'downloadPDF'])->name('admin.certificates.download');
    Route::put('/certificates/{id}/cancel', [\App\Http\Controllers\CertificateController::class, 'cancel'])->name('admin.certificates.cancel');
    Route::get('/certificates/{id}', [\App\Http\Controllers\CertificateController::class, 'show'])->name('admin.certificates.show');
    
    // Fee Management Routes
    Route::get('/fees/dashboard', [\App\Http\Controllers\FeeManagementController::class, 'dashboard'])->name('admin.fees.dashboard');
    Route::get('/fees/collect', [\App\Http\Controllers\FeeManagementController::class, 'collectFee'])->name('admin.fees.collect');
    Route::get('/fees/students-by-class', [\App\Http\Controllers\FeeManagementController::class, 'getStudentsByClass'])->name('admin.fees.students-by-class');
    Route::get('/fees/student-fees', [\App\Http\Controllers\FeeManagementController::class, 'getStudentFees'])->name('admin.fees.student-fees');
    Route::post('/fees/process-payment', [\App\Http\Controllers\FeeManagementController::class, 'processPayment'])->name('admin.fees.process-payment');
    Route::get('/fees/receipt/{id}/download', [\App\Http\Controllers\FeeManagementController::class, 'downloadReceipt'])->name('admin.fees.receipt.download');
    Route::get('/fees/receipt/{id}/print', [\App\Http\Controllers\FeeManagementController::class, 'printReceipt'])->name('admin.fees.receipt.print');
    Route::get('/fees/structure', [\App\Http\Controllers\FeeManagementController::class, 'feeStructure'])->name('admin.fees.structure');
    Route::post('/fees/structure/store', [\App\Http\Controllers\FeeManagementController::class, 'storeFeeStructure'])->name('admin.fees.structure.store');
    Route::put('/fees/structure/{id}', [\App\Http\Controllers\FeeManagementController::class, 'updateFeeStructure'])->name('admin.fees.structure.update');
    Route::get('/fees/reports', [\App\Http\Controllers\FeeManagementController::class, 'reports'])->name('admin.fees.reports');
    Route::get('/fees/assign', [\App\Http\Controllers\FeeManagementController::class, 'assignFees'])->name('admin.fees.assign');
    Route::post('/fees/assign/store', [\App\Http\Controllers\FeeManagementController::class, 'storeAssignFees'])->name('admin.fees.assign.store');
    Route::get('/fees/payment-history', [\App\Http\Controllers\FeeManagementController::class, 'paymentHistory'])->name('admin.fees.payment-history');
    Route::get('/fees/pending', [\App\Http\Controllers\FeeManagementController::class, 'pendingFees'])->name('admin.fees.pending');
    
    // Examination Management Routes
    Route::get('/examination/dashboard', [\App\Http\Controllers\ExaminationController::class, 'dashboard'])->name('admin.examination.dashboard');
    Route::get('/examination/exams', [\App\Http\Controllers\ExaminationController::class, 'exams'])->name('admin.examination.exams');
    Route::post('/examination/exam/store', [\App\Http\Controllers\ExaminationController::class, 'storeExam'])->name('admin.examination.exam.store');
    Route::put('/examination/exam/{id}/update', [\App\Http\Controllers\ExaminationController::class, 'updateExam'])->name('admin.examination.exam.update');
    Route::get('/examination/exam/{id}/subjects', [\App\Http\Controllers\ExaminationController::class, 'examSubjects'])->name('admin.examination.exam.subjects');
    Route::get('/examination/exam/{id}/subjects-list', [\App\Http\Controllers\ExaminationController::class, 'getExamSubjectsList'])->name('admin.examination.exam.subjects.list');
    Route::post('/examination/exam/subject/store', [\App\Http\Controllers\ExaminationController::class, 'storeExamSubject'])->name('admin.examination.exam.subject.store');
    Route::delete('/examination/exam/subject/{id}', [\App\Http\Controllers\ExaminationController::class, 'deleteExamSubject'])->name('admin.examination.exam.subject.delete');
    Route::get('/examination/marks-entry', [\App\Http\Controllers\ExaminationController::class, 'marksEntry'])->name('admin.examination.marks.entry');
    Route::post('/examination/marks/students', [\App\Http\Controllers\ExaminationController::class, 'getStudentsForMarks'])->name('admin.examination.marks.students');
    Route::post('/examination/marks/save', [\App\Http\Controllers\ExaminationController::class, 'saveMarks'])->name('admin.examination.marks.save');
    Route::get('/examination/results', [\App\Http\Controllers\ExaminationController::class, 'results'])->name('admin.examination.results');
    Route::post('/examination/results/generate', [\App\Http\Controllers\ExaminationController::class, 'generateResults'])->name('admin.examination.results.generate');
    Route::post('/examination/results/publish', [\App\Http\Controllers\ExaminationController::class, 'publishResults'])->name('admin.examination.results.publish');
    Route::get('/examination/results/{examId}', [\App\Http\Controllers\ExaminationController::class, 'viewResults'])->name('admin.examination.results.view');
    Route::get('/examination/report-card/{examId}/{studentId}', [\App\Http\Controllers\ExaminationController::class, 'reportCard'])->name('admin.examination.report.card');
    Route::get('/examination/report-card/{examId}/{studentId}/download', [\App\Http\Controllers\ExaminationController::class, 'downloadReportCard'])->name('admin.examination.report.card.download');
    Route::get('/examination/grade-system', [\App\Http\Controllers\ExaminationController::class, 'gradeSystem'])->name('admin.examination.grade.system');
    Route::post('/examination/grade-system/store', [\App\Http\Controllers\ExaminationController::class, 'storeGrade'])->name('admin.examination.grade.store');

    // Student Attendance Management Routes
    Route::get('/attendance/student/dashboard', [\App\Http\Controllers\StudentAttendanceController::class, 'dashboard'])->name('admin.attendance.student.dashboard');
    Route::get('/attendance/student/mark', [\App\Http\Controllers\StudentAttendanceController::class, 'markAttendance'])->name('admin.attendance.student.mark');
    Route::post('/attendance/student/get-students', [\App\Http\Controllers\StudentAttendanceController::class, 'getStudents'])->name('admin.attendance.student.get.students');
    Route::post('/attendance/student/save', [\App\Http\Controllers\StudentAttendanceController::class, 'saveAttendance'])->name('admin.attendance.student.save');
    Route::get('/attendance/student/view', [\App\Http\Controllers\StudentAttendanceController::class, 'viewAttendance'])->name('admin.attendance.student.view');
    Route::post('/attendance/student/records', [\App\Http\Controllers\StudentAttendanceController::class, 'getAttendanceRecords'])->name('admin.attendance.student.records');
    Route::get('/attendance/student/reports', [\App\Http\Controllers\StudentAttendanceController::class, 'reports'])->name('admin.attendance.student.reports');
    Route::post('/attendance/student/generate-report', [\App\Http\Controllers\StudentAttendanceController::class, 'generateReport'])->name('admin.attendance.student.generate.report');
    Route::get('/attendance/student/sections/{classId}', [\App\Http\Controllers\StudentAttendanceController::class, 'getSections'])->name('admin.attendance.student.sections');
});
