<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\ShiftPreferenceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShiftClaimController;

use App\Http\Middleware\EmployeeMiddleware;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
    Route::get('/shifts/create', [ShiftController::class, 'create'])->name('shifts.create');
    Route::post('/shifts/generate', [ShiftController::class, 'generateShifts'])->name('shifts.generate');
    Route::post('/shifts', [ShiftController::class, 'storeShifts'])->name('shifts.store');
    Route::delete('/shifts/{shift}', [ShiftController::class, 'destroy'])->name('shifts.destroy');

    Route::resource('employees', EmployeeController::class);
    Route::resource('skills', SkillController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('designations', DesignationController::class);
});

// Employee routes
Route::middleware(['auth', 'employee'])->group(function () {
    Route::get('/shift-preferences', [ShiftPreferenceController::class, 'index'])->name('shift_preferences.index');
    Route::post('/shift-preferences', [ShiftPreferenceController::class, 'store'])->name('shift_preferences.store');
    Route::get('/shifts/claim', [ShiftClaimController::class, 'index'])->name('shifts.claim.index');
    Route::post('/shifts/{publishedShift}/claim', [ShiftClaimController::class, 'claim'])->name('shifts.claim');
});

// Route for fetching designations by department (used in forms)
Route::get('/designations/by-department/{department}', [DesignationController::class, 'getByDepartment']);
Route::get('/admin/shifts/{date}', [AdminController::class, 'getShiftsForDate'])->name('admin.shifts.for.date');

Route::middleware(['auth'])->group(function () {
    Route::get('/shifts/published', [ShiftController::class, 'viewPublishedShifts'])->name('shifts.published');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/shift-preferences', [AdminController::class, 'viewShiftPreferences'])->name('admin.view_shift_preferences');
    Route::get('/admin/generate-roster', [AdminController::class, 'generateRoster'])->name('admin.generate_roster');
    Route::get('/admin/view-generated-roster', [AdminController::class, 'viewGeneratedRoster'])->name('admin.view_generated_roster');
    Route::match(['get', 'post'], '/admin/publish-shifts', [AdminController::class, 'publishShifts'])->name('admin.publish_shifts');
    Route::get('/shifts/calendar', [ShiftController::class, 'calendar'])->name('shifts.calendar');
});

require __DIR__.'/auth.php';
