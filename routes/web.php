<?php

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

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Student Routes
Route::middleware(['auth:student'])->prefix('student')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
});

// Teacher Routes
Route::middleware(['auth:teacher'])->prefix('teacher')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
});

// Admin Routes
Route::middleware(['auth:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
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

    // Student Routes
    Route::get('/students', [AdminController::class, 'students'])->name('admin.students');
    Route::get('/students/all', [AdminController::class, 'allStudents'])->name('admin.students.all');
    Route::get('/students/admission', [AdminController::class, 'studentAdmission'])->name('admin.students.admission');
    Route::get('/students/admission/generate-number', [AdminController::class, 'generateAdmissionNumber'])->name('admin.students.admission.generate-number');
    Route::post('/students/admission', [AdminController::class, 'storeAdmission'])->name('admin.students.admission.store');
    Route::get('/students/admissions', [AdminController::class, 'allAdmissions'])->name('admin.students.admissions');
    Route::get('/students/admissions/data', [AdminController::class, 'getAdmissionsData'])->name('admin.students.admissions.data');
    Route::get('/students/admissions/{id}/edit', [AdminController::class, 'editAdmission'])->name('admin.students.admissions.edit');
    Route::put('/students/admissions/{id}', [AdminController::class, 'updateAdmission'])->name('admin.students.admissions.update');
    Route::delete('/students/admissions/{id}', [AdminController::class, 'deleteAdmission'])->name('admin.students.admissions.delete');
    Route::post('/students/admissions/{id}/restore', [AdminController::class, 'restoreAdmission'])->name('admin.students.admissions.restore');
    Route::get('/students/admissions/trash', [AdminController::class, 'trashedAdmissions'])->name('admin.students.admissions.trash');
    Route::get('/students/admissions/trash/data', [AdminController::class, 'getTrashedAdmissionsData'])->name('admin.students.admissions.trash.data');
    Route::get('/students/add', [AdminController::class, 'addStudent'])->name('admin.students.add');
    Route::get('/students/strength', [AdminController::class, 'studentStrength'])->name('admin.students.strength');
    Route::get('/students/promotion', [AdminController::class, 'studentPromotion'])->name('admin.students.promotion');
    Route::get('/students/promotion/search', [AdminController::class, 'searchStudentsForPromotion'])->name('admin.students.promotion.search');
    Route::post('/students/promotion/promote', [AdminController::class, 'promoteStudents'])->name('admin.students.promotion.promote');
    Route::get('/students/details', [AdminController::class, 'studentDetails'])->name('admin.students.details');

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
    Route::get('/attendance/staff', [AdminController::class, 'staffAttendanceView'])->name('admin.attendance.staff');

    // Academic Routes
    Route::get('/timetable', [AdminController::class, 'timetable'])->name('admin.timetable');

    // Examination Routes
    Route::get('/exams', [AdminController::class, 'exams'])->name('admin.exams');
    Route::get('/exams/schedule', [AdminController::class, 'examSchedule'])->name('admin.exams.schedule');
    Route::get('/exams/marks', [AdminController::class, 'examMarks'])->name('admin.exams.marks');
    Route::get('/exams/results', [AdminController::class, 'examResults'])->name('admin.exams.results');

    // Library Route
    Route::get('/library', [AdminController::class, 'library'])->name('admin.library');

    // Fees Routes
    Route::get('/fees/collect', [AdminController::class, 'collectFees'])->name('admin.fees.collect');
    Route::get('/fees/structure', [AdminController::class, 'feeStructure'])->name('admin.fees.structure');
    Route::get('/fees/report', [AdminController::class, 'feeReport'])->name('admin.fees.report');

    // Settings Routes
    Route::get('/settings/general', [AdminController::class, 'generalSettings'])->name('admin.settings.general');
    Route::get('/settings/school', [AdminController::class, 'schoolInfo'])->name('admin.settings.school');
    Route::get('/settings/users', [AdminController::class, 'userManagement'])->name('admin.settings.users');
    
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
});
