<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CollegeController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\FacultyController;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

// Admin Routes
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Colleges
    Route::get('colleges', [CollegeController::class, 'index'])->name('admin.colleges.index');
    Route::get('colleges/create', [CollegeController::class, 'create'])->name('admin.colleges.create');
    Route::post('colleges', [CollegeController::class, 'store'])->name('admin.colleges.store');
    Route::get('colleges/{college}/edit', [CollegeController::class, 'edit'])->name('admin.colleges.edit');
    Route::put('colleges/{college}', [CollegeController::class, 'update'])->name('admin.colleges.update');
    Route::delete('colleges/{college}', [CollegeController::class, 'destroy'])->name('admin.colleges.destroy');

    // Departments
    Route::get('departments', [DepartmentController::class, 'index'])->name('admin.departments.index');
    Route::get('departments/create', [DepartmentController::class, 'create'])->name('admin.departments.create');
    Route::post('departments', [DepartmentController::class, 'store'])->name('admin.departments.store');
    Route::get('departments/{department}/edit', [DepartmentController::class, 'edit'])->name('admin.departments.edit');
    Route::put('departments/{department}', [DepartmentController::class, 'update'])->name('admin.departments.update');
    Route::delete('departments/{department}', [DepartmentController::class, 'destroy'])->name('admin.departments.destroy');

    // Faculties
    Route::get('faculties', [FacultyController::class, 'index'])->name('admin.faculties.index');
    Route::get('faculties/create', [FacultyController::class, 'create'])->name('admin.faculties.create');
    Route::post('faculties', [FacultyController::class, 'store'])->name('admin.faculties.store');
    Route::get('faculties/{faculty}/edit', [FacultyController::class, 'edit'])->name('admin.faculties.edit');
    Route::put('faculties/{faculty}', [FacultyController::class, 'update'])->name('admin.faculties.update');
    Route::delete('faculties/{faculty}', [FacultyController::class, 'destroy'])->name('admin.faculties.destroy');

    // Courses (parent)
    Route::view('courses', 'admin.courses.index')->name('admin.courses.index');
    Route::view('modules', 'admin.modules.index')->name('admin.modules.index');
    Route::view('videos', 'admin.videos.index')->name('admin.videos.index');
    Route::view('quizzes', 'admin.quizzes.index')->name('admin.quizzes.index');
    Route::view('fun-activities', 'admin.fun.index')->name('admin.fun.index');

    // Reports
    Route::view('reports', 'admin.reports.index')->name('admin.reports.index');
});

require __DIR__.'/auth.php';
