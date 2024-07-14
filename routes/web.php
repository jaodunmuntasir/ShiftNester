<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\ShiftPreferenceController;
use App\Http\Controllers\DashboardController;

use App\Http\Middleware\EmployeeMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

    Route::resource('employees', EmployeeController::class);
    Route::resource('skills', SkillController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('designations', DesignationController::class);
});

// Employee routes
Route::middleware(['auth', 'employee'])->group(function () {
    Route::get('/shift-preferences', [ShiftPreferenceController::class, 'index'])->name('shift_preferences.index');
    Route::post('/shift-preferences', [ShiftPreferenceController::class, 'store'])->name('shift_preferences.store');
});

// Route for fetching designations by department (used in forms)
Route::get('/designations/by-department/{department}', [DesignationController::class, 'getByDepartment']);

require __DIR__.'/auth.php';
