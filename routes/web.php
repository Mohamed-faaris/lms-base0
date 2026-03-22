<?php

use App\Livewire\Admin\Courses\Create as CoursesCreate;
use App\Livewire\Admin\Courses\Edit as CoursesEdit;
use App\Livewire\Admin\Courses\Index as CoursesIndex;
use App\Livewire\Admin\Courses\Show as CoursesShow;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Faculty\Certificates;
use App\Livewire\Faculty\CoursePlayer;
use App\Livewire\Faculty\Courses;
use App\Livewire\Faculty\Dashboard as FacultyDashboard;
use App\Livewire\Faculty\Notifications;
use App\Livewire\Faculty\Profile;
use App\Livewire\Faculty\Streaks;
use App\Livewire\Faculty\Suggestions;
use App\Livewire\Public\Courses as PublicCourses;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('courses', PublicCourses::class)->name('courses');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('faculty.dashboard');
    })->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', AdminDashboard::class)->name('dashboard');

        Route::prefix('courses')->name('courses.')->group(function () {
            Route::get('/', CoursesIndex::class)->name('index');
            Route::get('/create', CoursesCreate::class)->name('create');
            Route::get('/{course}', CoursesShow::class)->name('show');
            Route::get('/{course}/edit', CoursesEdit::class)->name('edit');
        });
    });

    Route::prefix('faculty')->name('faculty.')->group(function () {
        Route::get('dashboard', FacultyDashboard::class)->name('dashboard');
        Route::get('courses', Courses::class)->name('courses');
        Route::get('course-player/{course?}', CoursePlayer::class)->name('course-player');
        Route::get('streaks', Streaks::class)->name('streaks');
        Route::get('certificates', Certificates::class)->name('certificates');
        Route::get('notifications', Notifications::class)->name('notifications');
        Route::get('profile', Profile::class)->name('profile');
        Route::get('suggestions', Suggestions::class)->name('suggestions');
    });
});

require __DIR__.'/settings.php';
